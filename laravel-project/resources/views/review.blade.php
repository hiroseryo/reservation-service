@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/reviews.css') }}">
@endsection

@section('content')

@if (session('success'))
<div class="success-message">
    {{ session('success') }}
</div>
@endif

@if (session('error'))
<div class="error-message">
    {{ session('error') }}
</div>
@endif

<form action="{{ route('reviews.store', $shop->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="container">
        <div class="left-section">
            <h2>今回のご利用はいかがでしたか？</h2>
            <div class="restaurant-card">
                <div class="img-pack">
                    <a href="{{ asset($shop->image_url) }}" download="{{ $shop->name }}.jpg">
                        <img src="{{ $shop->image_url }}" alt="{{ $shop->name }}" class="restaurant-image">
                    </a>
                </div>
                <div class="restaurant-info">
                    <div class="restaurant-name">{{ $shop->name }}</div>
                    <div class="restaurant-tags">
                        <span class="tag">#{{ $shop->area->name }}</span>
                        <span class="tag">#{{ $shop->genre->name}}</span>
                    </div>
                    <div class="card-actions">
                        <a class="details-button" href="{{ route('shops.detail', ['shop' => $shop->id]) }}">詳しくみる</a>
                        <button class="like-btn" data-shop-id="{{ $shop->id }}" disabled>
                            @if(Auth::check() && Auth::user()->likedShops->contains($shop->id))
                            <span class="liked">♥</span>
                            @else
                            <span class="unliked">♥</span>
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @php
        $oldRating = old('rating', isset($review) ? $review->rating : '');
        $oldComment = old('comment', isset($review) ? $review->comment : '');
        @endphp

        <div class="right-section">
            <div class="review-section">
                <h2>体験を評価してください</h2>
                @error('rating')
                <div class="error-message">{{ $message }}</div>
                @enderror
                <div class="star-rating">
                    <span data-rating="5" @if($oldRating>= 5) class="selected" @endif>★</span>
                    <span data-rating="4" @if($oldRating>= 4) class="selected" @endif>★</span>
                    <span data-rating="3" @if($oldRating>= 3) class="selected" @endif>★</span>
                    <span data-rating="2" @if($oldRating>= 2) class="selected" @endif>★</span>
                    <span data-rating="1" @if($oldRating>= 1) class="selected" @endif>★</span>
                </div>
                <input type="hidden" name="rating" id="rating-value" value="{{ $oldRating }}" required>

                <div class="review-input">
                    <h3>口コミを投稿</h3>
                    @error('comment')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                    <label for="comment"></label>
                    <textarea name="comment" id="comment" placeholder="カジュアルな夜のお出かけにおすすめのスポット">{{ $oldComment }}</textarea>
                    <div class="char-count">0/400 (最高文字数)</div>
                </div>

                <h3>画像の追加</h3>
                <div class="image-upload" id="image-upload">
                    <label for="image" style="cursor: pointer;">クリックして写真を追加<br>
                        またはドラッグアンドドロップ</label>
                    </label>
                    <input type="file" name="image" id="image" style="display: none;" accept="image/jpeg,image/png">
                    <input type="hidden" name="old_img" value="{{ $review->img_url ?? '' }}">
                    @error('image')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                    @if(isset($review) && $review->img_url)
                    <div class="current-image">
                        <img src="{{ $review->img_url }}" class="preview-image">
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="submit-btn">
        <button type="submit">口コミを投稿</button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
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
    });

    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('upload-form');
        const imageInput = document.getElementById('image');
        const imageUpload = document.getElementById('image-upload');
        const maxFileSize = 1 * 1000000;
        const allowedTypes = ['image/png', 'image/jpeg'];

        /**
         *
         * @param {File} file
         */
        const handleImagePreview = (file) => {
            if (!file) return;
            if (file.size > maxFileSize) {
                alert('ファイルサイズは1MB以下にしてください。');
                imageInput.value = "";
                return;
            }
            if (!allowedTypes.includes(file.type)) {
                alert('pngまたはjpeg形式の画像ファイルのみ選択してください。');
                imageInput.value = "";
                return;
            }

            const reader = new FileReader();
            reader.onload = (event) => {
                let previewImage = document.querySelector('.preview-image');
                if (!previewImage) {
                    previewImage = document.createElement('img');
                    previewImage.classList.add('preview-image');

                    const currentImageDiv = document.createElement('div');
                    currentImageDiv.classList.add('current-image');
                    currentImageDiv.appendChild(previewImage);

                    imageInput.parentElement.appendChild(currentImageDiv);
                }
                previewImage.src = event.target.result;
            };
            reader.readAsDataURL(file);
        };

        imageInput.addEventListener('change', (e) => {
            const files = e.target.files;
            for (const file of files) {
                if (file.size > maxFileSize) {
                    alert('ファイルサイズは1MB以下にしてください。');
                    imageInput.value = "";
                    return;
                }
                if (!allowedTypes.includes(file.type)) {
                    alert('pngまたはjpeg形式の画像ファイルのみ選択してください。');
                    imageInput.value = "";
                    return;
                }
            }
            handleImagePreview(files[0]);
        });

        imageUpload.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.stopPropagation();
            imageUpload.classList.add('dragover');
        });

        imageUpload.addEventListener('dragleave', (e) => {
            e.preventDefault();
            e.stopPropagation();
            imageUpload.classList.remove('dragover');
        });

        imageUpload.addEventListener('drop', (e) => {
            e.preventDefault();
            e.stopPropagation();
            imageUpload.classList.remove('dragover');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const dataTransfer = new DataTransfer();
                for (const file of files) {
                    if (file.size > maxFileSize) {
                        alert('ファイルサイズは1MB以下にしてください。');
                        imageInput.value = "";
                        return;
                    }
                    if (!allowedTypes.includes(file.type)) {
                        alert('pngまたはjpeg形式の画像ファイルのみ選択してください。');
                        imageInput.value = "";
                        return;
                    }
                    dataTransfer.items.add(file);
                }
                imageInput.files = dataTransfer.files;
                imageInput.dispatchEvent(new Event('change'));
            }
        });

        form.addEventListener('submit', (e) => {
            const files = imageInput.files;
            for (const file of files) {
                if (file.size > maxFileSize) {
                    alert('ファイルサイズは1MB以下にしてください。');
                    e.preventDefault();
                    return;
                }
                if (!allowedTypes.includes(file.type)) {
                    alert('pngまたはjpeg形式の画像ファイルのみ選択してください。');
                    e.preventDefault();
                    return;
                }
            }
        });
    });
</script>
@endsection