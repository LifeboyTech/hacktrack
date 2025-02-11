<div>
    <canvas id="weightChart" height="100"></canvas>

    <script>
        // Create a unique identifier for this instance
        const chartId = 'chart_' + Date.now();
        
        // Store chart instances globally
        window.weightCharts = window.weightCharts || {};

        function initChart(chartData) {
            // Wait for DOM to be ready
            const canvas = document.getElementById('weightChart');
            if (!canvas) {
                return;
            }

            // Destroy existing chart for this canvas
            if (window.weightCharts[chartId]) {
                window.weightCharts[chartId].destroy();
                delete window.weightCharts[chartId];
            }

            const ctx = canvas.getContext('2d');

            // Define colors
            const belowTrendColor = 'rgb(75, 192, 192)';
            const aboveTrendColor = 'rgb(255, 99, 132)';
            const trendColor = 'rgb(255, 99, 132)';

            // Create pairs of points for vertical lines and determine point colors
            const verticalLines = [];
            const pointColors = [];
            for (let i = 0; i < chartData.labels.length; i++) {
                if (chartData.weights[i] !== null && chartData.trends[i] !== null) {
                    const isAboveTrend = chartData.weights[i] > chartData.trends[i];
                    verticalLines.push({
                        x: parseInt(chartData.labels[i]),
                        y1: chartData.weights[i],
                        y2: chartData.trends[i],
                        isAboveTrend: isAboveTrend,
                        color: isAboveTrend ? aboveTrendColor : belowTrendColor
                    });
                    pointColors[i] = isAboveTrend ? aboveTrendColor : belowTrendColor;
                }
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

            // Ensure plugin is registered only once
            if (!Chart.registry.plugins.get('verticalLines')) {
                Chart.register(verticalLinesPlugin);
            }

            // Create new chart instance
            window.weightCharts[chartId] = new Chart(ctx, {
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
                            data: chartData.trends.map((y, index) => ({
                                x: parseInt(chartData.labels[index]),
                                y: y
                            })),
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
                    // ... your existing options ...
                }
            });
        }

        // Clean up any existing chart when the component is disconnected
        document.addEventListener('livewire:disconnected', () => {
            if (window.weightCharts[chartId]) {
                window.weightCharts[chartId].destroy();
                delete window.weightCharts[chartId];
            }
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