<?php

namespace App\Livewire;

use Livewire\Component;

class WeightChart extends Component
{
    public $chartData;

    public function mount($chartData)
    {
        $this->chartData = $chartData;
    }

    public function render()
    {
        return view('livewire.weight-chart');
    }

    public function refreshChart($newChartData = null)
    {
        if ($newChartData) {
            $this->chartData = $newChartData;
        }
        $this->dispatch('chartDataUpdated', chartData: $this->chartData);
    }
} 