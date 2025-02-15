// Add a cleanup function
function destroyChart() {
    if (window.weightChart) {
        window.weightChart.destroy();
        window.weightChart = null;
    }
}

// Modify your chart initialization
function initializeChart(chartData) {
    // First destroy any existing chart
    destroyChart();
    
    const ctx = document.getElementById('weightChart').getContext('2d');
    window.weightChart = new Chart(ctx, {
        // your existing chart configuration
        data: chartData,
        // ... rest of your chart options
    });
}

// Listen for Livewire navigation events
document.addEventListener('livewire:navigated', () => {
    if (document.getElementById('weightChart')) {
        initializeChart(chartData); // Make sure chartData is available in scope
    }
});

// Cleanup when navigating away
document.addEventListener('livewire:navigating', () => {
    destroyChart();
});