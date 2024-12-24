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

function openReviewModal(reservationId, shopId, shopName) {
    var modal = document.getElementById('review-modal');
    var modalTitle = document.getElementById('review-modal-title');
    var reservationIdInput = document.getElementById('modal-reservation-id');
    var shopIdInput = document.getElementById('modal-shop-id');

    modalTitle.textContent = shopName + ' の評価';
    reservationIdInput.value = reservationId;
    shopIdInput.value = shopId;

    resetStars();

    modal.style.display = 'block';
}

var modalCloseBtn = document.getElementById('review-modal-close-btn');
modalCloseBtn.onclick = function() {
    var modal = document.getElementById('review-modal');
    modal.style.display = 'none';
}

var stars = document.querySelectorAll('.star-rating span');
var ratingValue = document.getElementById('rating-value');

stars.forEach(function(star) {
    star.addEventListener('click', function() {
        var rating = this.getAttribute('data-rating');
        ratingValue.value = rating;
        highlightStars(rating);
    });

    star.addEventListener('mouseover', function() {
        var rating = this.getAttribute('data-rating');
        highlightStars(rating);
    });

    star.addEventListener('mouseout', function() {
        var rating = ratingValue.value;
        highlightStars(rating);
    });
});

function highlightStars(rating) {
    stars.forEach(function(star) {
        if (star.getAttribute('data-rating') <= rating) {
            star.classList.add('selected');
        } else {
            star.classList.remove('selected');
        }
    });
}

function resetStars() {
    ratingValue.value = '';
    stars.forEach(function(star) {
        star.classList.remove('selected');
    });
}

// モーダル外をクリックしたときに閉じる
window.onclick = function(event) {
    var modal = document.getElementById('review-modal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}