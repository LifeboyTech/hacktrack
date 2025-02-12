<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('dashboard', ['date' => $prev_month]) }}" 
                   class="px-3 py-1 text-sm bg-gray-700 hover:bg-gray-600 rounded-md">
                    &larr; Previous
                </a>
                
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $current_month_name }}
                </h2>
                
                <a href="{{ route('dashboard', ['date' => $next_month]) }}" 
                   class="px-3 py-1 text-sm bg-gray-700 hover:bg-gray-600 rounded-md">
                    Next &rarr;
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                        <livewire:weight-chart :chartData="$chartData" />
                    </div>
                    <table class="table-auto w-full">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left">Day</th>
                                <th class="px-4 py-2 text-left">Weight</th>
                                <th class="px-4 py-2 text-left">Trend</th>
                                <th class="px-4 py-2 text-left">Variation</th>
                                <th class="px-4 py-2 text-left">Exercise Rung</th>
                                <th class="px-4 py-2 text-left">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($days as $day)
                                <livewire:day-entry :day="$day" :key="$day['date']" />
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    let chart = null; // Add this line to store chart instance globally

    document.addEventListener('DOMContentLoaded', function() {
        initChart();
        
        // Listen for Livewire events
        Livewire.on('chartDataUpdated', (data) => {
            if (chart) {
                // Update existing chart
                chart.data.labels = data.chartData.labels;
                chart.data.datasets[0].data = data.chartData.weights.map((y, index) => ({x: index + 1, y: y}));
                chart.data.datasets[1].data = data.chartData.trends.map((y, index) => ({x: index + 1, y: y}));
                chart.update('none');
            } else {
                // Create new chart if none exists
                initChart(data.chartData);
            }
        });
    });

    function initChart(chartData) {
        const ctx = document.getElementById('weightChart').getContext('2d');
        
        // Use provided data or fall back to initial data
        const weights = chartData ? chartData.weights : @json($chartData['weights']);
        const trends = chartData ? chartData.trends : @json($chartData['trends']);
        const labels = chartData ? chartData.labels : @json($chartData['labels']);

        // Define colors
        const belowTrendColor = 'rgb(75, 192, 192)';  // Green/blue for below trend
        const aboveTrendColor = 'rgb(255, 99, 132)';  // Red for above trend
        const trendColor = 'rgb(255, 99, 132)';       // Red for trend line

        // Create pairs of points for vertical lines and determine point colors
        const verticalLines = [];
        const pointColors = [];
        for (let i = 0; i < labels.length; i++) {
            if (weights[i] !== null && trends[i] !== null) {
                const isAboveTrend = weights[i] > trends[i];
                verticalLines.push({
                    x: i + 1,
                    y1: weights[i],
                    y2: trends[i],
                    isAboveTrend: isAboveTrend,
                    color: isAboveTrend ? aboveTrendColor : belowTrendColor
                });
                pointColors[i] = isAboveTrend ? aboveTrendColor : belowTrendColor;
            }
        }

        // If there's an existing chart, destroy it
        if (chart) {
            chart.destroy();
        }

        // Register the plugin
        const verticalLinesPlugin = {
            id: 'verticalLines',
            afterDraw: (chart) => {
                const ctx = chart.ctx;
                const scales = chart.scales;
                const chartArea = chart.chartArea;
                
                ctx.save();
                verticalLines.forEach((line) => {
                    const xPos = scales.x.getPixelForValue(line.x);
                    const yPos1 = scales.y.getPixelForValue(line.y1);
                    const yPos2 = scales.y.getPixelForValue(line.y2);
                    
                    if (xPos >= chartArea.left && xPos <= chartArea.right) {
                        ctx.beginPath();
                        ctx.strokeStyle = line.color;
                        ctx.lineWidth = 1;
                        ctx.setLineDash([]);
                        ctx.moveTo(xPos, yPos1);
                        ctx.lineTo(xPos, yPos2);
                        ctx.stroke();
                    }
                });
                ctx.restore();
            }
        };

        Chart.register(verticalLinesPlugin);

        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Weight',
                        data: weights.map((y, index) => ({x: index + 1, y: y})),
                        borderColor: pointColors,
                        backgroundColor: pointColors,
                        pointStyle: 'circle',
                        showLine: false,
                        pointRadius: 4,
                        tension: 0.1,
                        spanGaps: true
                    },
                    {
                        label: 'Trend',
                        data: trends.map((y, index) => ({x: index + 1, y: y})),
                        borderColor: trendColor,
                        backgroundColor: pointColors,
                        pointStyle: 'circle',
                        pointRadius: 3,
                        tension: 0.1,
                        spanGaps: true
                    }
                ]
            },
            options: {
                animation: false,
                responsive: true,
                scales: {
                    x: {
                        type: 'linear',
                        min: 1,
                        max: labels.length,
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                return Math.floor(value);
                            }
                        }
                    },
                    y: {
                        beginAtZero: false,
                        offset: true,
                        ticks: {
                            padding: 10
                        }
                    }
                },
                layout: {
                    padding: {
                        left: 10,
                        right: 10
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    }
                }
            }
        });

        return chart;
    }
    </script>
</x-app-layout>
