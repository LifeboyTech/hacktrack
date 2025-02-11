<?php

namespace App\Http\Controllers;

use App\Models\Day;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\DayRecord;

class DashboardController extends Controller
{
    private function calculateTrend($weights, $trendCarryForward = 0)
    {
        $trends = [];
        $trend = $trendCarryForward;

        // If no trend carried forward, use first available weight
        if ($trend == 0) {
            foreach ($weights as $weight) {
                if (!empty($weight)) {
                    $trend = $weight;
                    break;
                }
            }
        }

        // Calculate trend using exponential moving average
        if ($trend > 0) {
            foreach ($weights as $date => $weight) {
                if (!empty($weight)) {
                    // Using same formula as original: trend + ((weight - trend) / 10)
                    $trend = $trend + (($weight - $trend) / 10);
                }
                $trends[$date] = $trend > 0 ? round($trend, 1) : null;
            }
        }

        return $trends;
    }

    private function calculateVariation($weight, $trend)
    {
        if (empty($weight) || empty($trend)) {
            return null;
        }
        
        return round($weight - $trend, 1);
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
        $trends = $this->calculateTrend($weights, $trendCarryForward);

        // Second pass: build days array with all data
        for ($i = 1; $i <= $n_days_in_month; $i++) {
            $date = $year_month . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $dayRecord = Day::where('date', $date)
                           ->where('user_id', auth()->id())
                           ->first();

            $trend = $trends[$date] ?? null;
            $variation = $this->calculateVariation($dayRecord?->weight, $trend);

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
            ];
        }

        return view('dashboard', compact('days', 'current_month_name', 'prev_month', 'next_month'));
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