document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modal');
    const openBtn = document.getElementById('modal-open-btn');
    const closeBtn = document.getElementById('modal-close-btn');

    openBtn.onclick = function() {
        modal.style.display = 'block';
    }

    closeBtn.onclick = function() {
        modal.style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('date');
    const timeInput = document.getElementById('time');
    const numSelect = document.getElementById('num_of_users');

    const selectedDate = document.getElementById('selected-date');
    const selectedTime = document.getElementById('selected-time');
    const selectedNum = document.getElementById('selected-num');

    dateInput.addEventListener('change', function() {
        selectedDate.textContent = this.value;
    });

    timeInput.addEventListener('change', function() {
        selectedTime.textContent = this.value;
    });

    numSelect.addEventListener('change', function() {
        selectedNum.textContent = this.options[this.selectedIndex].text;
    });
});