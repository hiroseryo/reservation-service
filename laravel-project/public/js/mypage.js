document.querySelectorAll('.edit-btn').forEach((button) => {
    button.addEventListener('click', (event) => {
        const reservationId = event.target.dataset.reservationId;
        const editMode = document.getElementById(`edit-mode-${reservationId}`);
        editMode.style.display = editMode.style.display === 'none' ? 'block' : 'none';
    });
});

function toggleEditMode(reservationId) {
    const editMode = document.getElementById(`edit-mode-${reservationId}`);
    editMode.style.display = editMode.style.display === 'none' ? 'block' : 'none';
}