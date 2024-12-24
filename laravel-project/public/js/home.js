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

    const likeButtons = document.querySelectorAll('.like-btn');

    likeButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const shopId = this.getAttribute('data-shop-id');
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const isLoggedIn = document.body.getAttribute('data-user');

            if (!isLoggedIn) {
                alert('ログインしてからいいねして下さい。');
                return;
            }

            const isLiked = this.querySelector('.liked');

            if (isLiked) {
                fetch('/unlike', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                    body: JSON.stringify({ shop_id: shopId }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.result) {
                        this.innerHTML = '<span class="unliked">♥</span>';
                    }
                });
            } else {
                fetch('/like', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                    body: JSON.stringify({ shop_id: shopId }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.result) {
                        this.innerHTML = '<span class="liked">♥</span>';
                    }
                });
            }
        });
    });
});

