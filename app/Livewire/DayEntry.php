<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Day;
use Illuminate\Support\Facades\Auth;
use App\Services\WeightTrendService;
use Illuminate\Support\Facades\Log;
use App\Livewire\WeightChart;

class DayEntry extends Component
{
    public $date;
    public $weight;
    public $exercise_rung;
    public $notes;
    public $trend;
    public $variation;
    public $day;
    public $displayWeight;
    public $is_editable;

    public function mount($day)
    {
        $this->day = $day;
        $this->date = $day['date'];
        $this->weight = $day['weight'] ? (float)str_replace(',', '', $day['weight']) : null;
        $this->displayWeight = $day['weight']; // Keep formatted version for display
        $this->exercise_rung = $day['exercise_rung'];
        $this->notes = $day['notes'];
        $this->trend = $day['trend'];
        $this->variation = $day['variation'];
        $this->is_editable = $day['is_editable'];
    }

    public function updated($field, $value)
    {
        try {
            $value = $this->{$field};
            
            if ($field === 'weight' && !is_null($value)) {
                $value = (float)$value;
            }

            if ($field === 'exercise_rung' || $field === 'weight') {
                $value = $value == '' ? null : $value;
            }

            Day::updateOrCreate(
                [
                    'date' => $this->date,
                    'user_id' => Auth::id()
                ],
                [$field => $value]
            );

            if ($field === 'weight') {
                if (!is_null($value)) {
                    $this->displayWeight = floor($value) == $value 
                        ? number_format($value, 0) 
                        : number_format($value, 1);
                }

                // Recalculate trend and variation
                $weightTrendService = new WeightTrendService();
                $calculations = $weightTrendService->recalculateForDate($this->date);
                
                $this->trend = $calculations['trend'];
                $this->variation = $calculations['variation'];
            }

            // Get fresh chart data
            Log::info('About to dispatch refreshChart from DayEntry', [
                'target_component' => 'weight-chart',
                'component_id' => $this->getId()
            ]);
            $this->dispatch('refreshChart');
            // $this->dispatch('refreshChart')->to('weight-chart');  // Comment this out temporarily
            Log::info('Successfully dispatched refreshChart from DayEntry');

        } catch (\Exception $e) {
            logger()->error('Error updating day', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            Log::error('Failed to dispatch refreshChart', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function render()
    {
        $formattedVariation = null;
        if (!is_null($this->variation)) {
            $icon = $this->variation < 0 ? '↓' : '↑';
            $color = $this->variation < 0 ? 'text-green-600' : 'text-red-600';
            $formattedVariation = [
                'value' => abs($this->variation),
                'icon' => $icon,
                'color' => $color
            ];
        }

        return view('livewire.day-entry', [
            'formattedVariation' => $formattedVariation
        ]);
    }
}
