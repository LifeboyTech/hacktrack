<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use App\Services\ChartDataService;

class WeightChart extends Component
{
    public $chartData;
    protected $chartDataService;

    protected $listeners = ['refreshChart' => 'refreshChart'];

    public function boot(ChartDataService $chartDataService)
    {
        $this->chartDataService = $chartDataService;
    }

    public function mount($chartData)
    {
        Log::info('WeightChart mounted', [
            'component_name' => $this->getName(),
            'component_id' => $this->getId(),
            'listeners' => $this->getListeners()
        ]);
        $this->chartData = $chartData;
    }

    public function render()
    {
        return view('livewire.weight-chart');
    }

    #[On('refreshChart')]
    public function refreshChart()
    {
        Log::info('Refreshing chart in WeightChart component');
        $data = $this->chartDataService->getData();
        $this->chartData = $data['chartData'];
        $this->dispatch('chartDataUpdated', chartData: $this->chartData);
    }
} 