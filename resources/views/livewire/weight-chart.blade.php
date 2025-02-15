<div>
    <canvas id="weightChart" height="300"></canvas>

    <script>
        // Create a namespace for our chart
        if (!window.WeightChartApp) {
            window.WeightChartApp = {
                charts: {},
                colors: {
                    belowTrend: 'rgb(75, 192, 192)',
                    aboveTrend: 'rgb(255, 99, 132)',
                    trend: 'rgb(255, 99, 132)'
                },
                verticalLines: [],
                pointColors: []
            };

            // Register the plugin once, in our namespace
            const verticalLinesPlugin = {
                id: 'verticalLines',
                afterDraw: (chart) => {
                    const ctx = chart.ctx;
                    const scales = chart.scales;
                    const chartArea = chart.chartArea;
                    
                    ctx.save();
                    window.WeightChartApp.verticalLines.forEach((line) => {
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

            // Ensure plugin is registered only once
            if (!Chart.registry.plugins.get('verticalLines')) {
                Chart.register(verticalLinesPlugin);
            }
        }

        function initChart(chartData) {
            const chartId = 'chart_' + Date.now();
            const canvas = document.getElementById('weightChart');
            if (!canvas) {
                return;
            }

            // Destroy existing chart for this canvas
            const existingChart = Object.values(window.WeightChartApp.charts).find(chart => 
                chart.canvas.id === 'weightChart'
            );
            if (existingChart) {
                existingChart.destroy();
                delete window.WeightChartApp.charts[existingChart.id];
            }

            const ctx = canvas.getContext('2d');

            // Create pairs of points for vertical lines and determine point colors
            window.WeightChartApp.verticalLines = [];
            window.WeightChartApp.pointColors = [];
            for (let i = 0; i < chartData.labels.length; i++) {
                if (chartData.weights[i] !== null && chartData.trends[i] !== null) {
                    const dayNumber = parseInt(chartData.labels[i]);
                    const isAboveTrend = chartData.weights[i] > chartData.trends[i];
                    window.WeightChartApp.verticalLines.push({
                        x: dayNumber,
                        y1: chartData.weights[i],
                        y2: chartData.trends[i],
                        isAboveTrend: isAboveTrend,
                        color: isAboveTrend ? window.WeightChartApp.colors.aboveTrend : window.WeightChartApp.colors.belowTrend
                    });
                    window.WeightChartApp.pointColors[i] = isAboveTrend ? 
                        window.WeightChartApp.colors.aboveTrend : 
                        window.WeightChartApp.colors.belowTrend;
                }
            }

            // Create new chart instance
            const chartConfig = {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [
                        {
                            label: 'Weight',
                            data: chartData.weights.map((y, index) => ({
                                x: parseInt(chartData.labels[index]),
                                y: y
                            })),
                            borderColor: window.WeightChartApp.pointColors,
                            backgroundColor: window.WeightChartApp.pointColors,
                            pointStyle: 'circle',
                            showLine: false,
                            pointRadius: 4,
                            tension: 0.1,
                            spanGaps: true
                        },
                        {
                            label: 'Trend',
                            data: chartData.trends.map((y, index) => ({
                                x: parseInt(chartData.labels[index]),
                                y: y
                            })),
                            borderColor: window.WeightChartApp.colors.trend,
                            backgroundColor: window.WeightChartApp.pointColors,
                            pointStyle: 'circle',
                            pointRadius: 3,
                            tension: 0.1,
                            spanGaps: true
                        }
                    ],
                    verticalLines: window.WeightChartApp.verticalLines
                },
                options: {
                    animation: false,
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            type: 'linear',
                            offset: true,
                            min: 1,
                            max: chartData.labels.length,
                            ticks: {
                                stepSize: 1,
                                callback: function(value) {
                                    return Math.floor(value);
                                },
                                color: '#9ca3af'
                            },
                            grid: {
                                offset: true,
                                color: '#374151'
                            },
                            border: {
                                color: '#4b5563'
                            }
                        },
                        y: {
                            beginAtZero: false,
                            offset: true,
                            ticks: {
                                padding: 10,
                                color: '#9ca3af'
                            },
                            grid: {
                                offset: true,
                                color: '#374151'
                            },
                            border: {
                                color: '#4b5563'
                            }
                        }
                    },
                    layout: {
                        padding: {
                            left: 0,
                            right: 0,
                            top: 10,
                            bottom: 10
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
            };

            window.WeightChartApp.charts[chartId] = new Chart(ctx, chartConfig);
        }

        // Clean up any existing chart when the component is disconnected
        document.addEventListener('livewire:disconnected', () => {
            Object.values(window.WeightChartApp.charts).forEach(chart => {
                if (chart) {
                    chart.destroy();
                }
            });
            window.WeightChartApp.charts = {};
        });

        // Initialize chart
        initChart(@json($chartData));

        // Listen for Livewire events
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('chartDataUpdated', (data) => {
                initChart(data.chartData);
            });
        });
    </script>
</div> 