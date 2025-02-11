<?php

namespace App\Http\Controllers;

use App\Models\Day;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\DayRecord;
use App\Services\WeightTrendService;

class DashboardController extends Controller
{
    protected $weightTrendService;

    public function __construct(WeightTrendService $weightTrendService)
    {
        $this->weightTrendService = $weightTrendService;
    }

    private function calculateTrend($weights, $trendCarryForward = 0)
    {
        $trends = [];
        $trend = $trendCarryForward;

        // If no trend carried forward, use first available valid weight
        if ($trend == 0) {
            foreach ($weights as $weight) {
                if (!empty($weight) && $weight > 0) {
                    $trend = $weight;
                    break;
                }
            }
        }

        // Calculate trend using exponential moving average
        if ($trend > 0) {
            foreach ($weights as $date => $weight) {
                if (!empty($weight) && $weight > 0) {
                    // Using same formula as original: trend + ((weight - trend) / 10)
                    $trend = $trend + (($weight - $trend) / 10);
                }
                $trends[$date] = $trend > 0 ? round($trend, 1) : null;
            }
        }

        return $trends;
    }

    private function calculateVariation($weight, $trend, $previousVariation = null)
    {
        // If there's a valid weight, calculate actual variation
        if (!empty($weight) && $weight > 0 && !empty($trend)) {
            return round($weight - $trend, 1);
        }
        
        // If there's no weight but we have a previous variation, carry it forward
        if (!empty($previousVariation)) {
            return $previousVariation;
        }
        
        // If there's no weight and no previous variation but there is a trend,
        // assume we're on trend (variation = 0)
        if (!empty($trend)) {
            return 0;
        }
        
        return null;
    }

    private function getLastTrendFromPreviousMonth($date)
    {
        // Get the last day of previous month that has a trend
        $lastRecord = Day::where('user_id', Auth::id())
            ->where('date', '<', $date->startOfMonth()->format('Y-m-d'))
            ->whereNotNull('weight')
            ->orderBy('date', 'desc')
            ->first();

        if ($lastRecord) {
            // Calculate the trend for the previous month up to this record
            $prevMonthDate = Carbon::parse($lastRecord->date)->startOfMonth();
            $prevMonthRecords = Day::where('user_id', Auth::id())
                ->whereYear('date', $prevMonthDate->year)
                ->whereMonth('date', $prevMonthDate->month)
                ->orderBy('date')
                ->get()
                ->pluck('weight', 'date')
                ->toArray();

            $prevTrends = $this->calculateTrend($prevMonthRecords);
            return end($prevTrends) ?: 0;
        }

        return 0;
    }

    public function index(Request $request)
    {
        // Get selected month or default to current month
        $selected_date = $request->get('date') 
            ? Carbon::createFromFormat('Y-m', $request->get('date'))
            : now();
            
        $current_month = $selected_date->format('m');
        $current_year = $selected_date->format('Y');
        $year_month = $current_year . '-' . $current_month;
        $n_days_in_month = $selected_date->daysInMonth;
        $current_month_name = $selected_date->format('F Y');

        // Calculate previous and next month links
        $prev_month = $selected_date->copy()->subMonth()->format('Y-m');
        $next_month = $selected_date->copy()->addMonth()->format('Y-m');

        $days = [];
        $weights = []; // Will store weights for trend calculation
        
        // First pass: collect all weights
        for ($i = 1; $i <= $n_days_in_month; $i++) {
            $date = $year_month . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $dayRecord = Day::where('date', $date)
                           ->where('user_id', auth()->id())
                           ->first();

            $weights[$date] = $dayRecord?->weight;
        }

        // Get the trend carry-forward from previous month
        $trendCarryForward = $this->getLastTrendFromPreviousMonth($selected_date);

        // Calculate trends for all days using the carry-forward value
        $trends = $this->weightTrendService->calculateTrend($weights, $trendCarryForward);

        // Second pass: build days array with all data
        $previousVariation = null;
        for ($i = 1; $i <= $n_days_in_month; $i++) {
            $date = $year_month . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $dayRecord = Day::where('date', $date)
                           ->where('user_id', auth()->id())
                           ->first();

            // Check if the date is in the future
            $isFutureDate = Carbon::parse($date)->isAfter(now());

            $trend = $isFutureDate ? null : ($trends[$date] ?? null);
            $variation = $isFutureDate ? null : $this->weightTrendService->calculateVariation(
                $dayRecord?->weight, 
                $trend, 
                $previousVariation
            );
            
            // Store this variation for the next iteration
            if ($dayRecord?->weight > 0) {
                $previousVariation = $variation;
            }

            $weight = null;
            if ($dayRecord?->weight !== null) {
                $weight = floor($dayRecord->weight) == $dayRecord->weight 
                    ? number_format($dayRecord->weight, 0) 
                    : number_format($dayRecord->weight, 1);
            }

            $days[] = [
                'day' => $i,
                'name' => date('D, jS', strtotime($date)),
                'date' => $date,
                'weight' => $weight,
                'trend' => $trend,
                'variation' => $variation,
                'exercise_rung' => $dayRecord?->exercise_rung,
                'notes' => $dayRecord?->notes,
                'is_editable' => !$isFutureDate,
            ];
        }

        // Prepare vertical lines data
        $verticalLines = [];
        foreach ($days as $day) {
            if ($day['weight'] && $day['trend']) {
                // Create two points for each vertical line
                $verticalLines[] = [
                    'x' => date('j', strtotime($day['date'])),
                    'y' => floatval($day['weight'])
                ];
                $verticalLines[] = [
                    'x' => date('j', strtotime($day['date'])),
                    'y' => floatval($day['trend'])
                ];
                $verticalLines[] = null; // Add null to create a break between lines
            }
        }

        $chartData = [
            'labels' => array_map(function($day) {
                return date('j', strtotime($day['date']));
            }, $days),
            'weights' => array_map(function($day) {
                return $day['weight'] ? floatval($day['weight']) : null;
            }, $days),
            'trends' => array_map(function($day) {
                return $day['trend'] ? floatval($day['trend']) : null;
            }, $days)
        ];

        // Let's log the data to make sure it's not empty
        \Log::info('Chart Data:', $chartData);

        return view('dashboard', compact('days', 'current_month_name', 'prev_month', 'next_month', 'chartData'));
    }

    public function updateDay(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'field' => 'required|in:weight,exercise_rung,notes',
            'value' => 'nullable',
        ]);

        $day = Day::firstOrNew([
            'date' => $validated['date'],
            'user_id' => auth()->id()
        ]);
        $day->{$validated['field']} = $validated['value'];
        $day->save();

        return response()->json(['success' => true]);
    }
} 