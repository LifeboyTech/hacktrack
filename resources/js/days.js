document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.day-input');
    
    inputs.forEach(input => {
        input.addEventListener('change', updateDay);
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                updateDay.call(this);
            }
        });
    });

    function updateDay() {
        const date = this.dataset.date;
        const field = this.dataset.field;
        const value = this.value;

        fetch('/dashboard/update-day', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ date, field, value })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Failed to update');
            }
        })
        .catch(error => console.error('Error:', error));
    }
}); 