@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')

<div class="name">{{ Auth::user()->name }}さん</div>
@if (session('success'))
<div class="success-message">
    {{ session('success') }}
</div>
@endif

@if (session('validation_error'))
<div class="error-message">
    {{ session('validation_error') }}
</div>
@endif

@if (session('error'))
<div class="error-message">
    {{ session('error') }}
</div>
@endif

<div class="main-content">
    <section>
        <h2 class="section-title">予約状況</h2>
        @forelse ($reservations as $index => $reservation)
        <div class="reservation-card">
            <button class="edit-btn" data-reservation-id="{{ $reservation->id }}">変更</button>
            <!-- 予約番号を繰り返す -->
            <h4>予約{{ $index + 1 }}</h4>
            <form action="{{ route('reservation.cancel', ['id' => $reservation->id]) }}" method="post">
                @csrf
                <button class="reservation-close" aria-label="予約をキャンセル" type="submit">×</button>
            </form>
            <div class="reservation-details">
                <div>Shop</div>
                <div>{{ $reservation->shop->name }}</div>
                <div>Date</div>
                <div>{{ $reservation->start_at->format('Y-m-d') }}</div>
                <div>Time</div>
                <div>{{ $reservation->start_at->format('H:i') }}</div>
                <div>Number</div>
                <div>{{ $reservation->num_of_users }}人</div>
                <div>決済</div>
                <div>
                    @if ($reservation->payment_status === 'unpaid')
                    <a href="{{ route('payment.checkout', $reservation->id) }}" class="btn btn-primary">
                        今すぐ支払う
                    </a>
                    @elseif ($reservation->payment_status === 'paid')
                    <div>支払済み</div>
                    @elseif ($reservation->payment_status === 'canceled')
                    <a href="{{ route('payment.checkout', $reservation->id) }}" class="btn btn-primary">
                        再度支払う
                    </a>
                    @endif
                </div>
                <div>QR</div>
                <div>
                    <a href="{{ route('reservations.qrcode.show', $reservation->id) }}" class="btn btn-qr_code">QRコード表示</a>
                </div>
            </div>

            @php
            $hasReviewed = \App\Models\Review::where('user_id', Auth::id())->where('shop_id', $reservation->shop->id)->exists();
            @endphp

            @if ($reservation->start_at <= now() && !$hasReviewed)
                <button class="review-btn" onclick="openReviewModal({{ $reservation->id }}, {{ $reservation->shop->id }}, '{{ $reservation->shop->name }}')">評価する</button>
                @elseif ($hasReviewed)
                <p class="evaluated">評価済み</p>
                @endif
        </div>

        <div class="reservation-edit-form" id="edit-mode-{{ $reservation->id }}" style="display: none;">
            <form action="{{ route('reservation.update', ['id' => $reservation->id]) }}" method="post">
                @csrf

                <label for="time-{{ $reservation->id }}">日付</label>
                <input type="date" id="date-{{ $reservation->id }}" name="date" value="{{ $reservation->start_at->format('Y-m-d') }}" required>
                @error('date')
                <div class="error-message">{{ $message }}</div>
                @enderror

                <label for="time-{{ $reservation->id }}">時間</label>
                <input type="time" id="time-{{ $reservation->id }}" name="time" value="{{ $reservation->start_at->format('H:i') }}" required>
                @error('time')
                <div class="error-message">{{ $message }}</div>
                @enderror

                <label for="num_of_users-{{ $reservation->id }}">人数</label>
                <select name="num_of_users" id="num_of_users-{{ $reservation->id }}">
                    @for ($i = 1; $i <= 9; $i++)
                        <option value="{{ $i }}" {{ $reservation->num_of_users == $i ? 'selected' : '' }}>{{ $i }}人</option>
                        @endfor
                        <option value="10" {{ $reservation->num_of_users >= 10 ? 'selected' : '' }}>10人以上</option>
                </select>
                @error('num_of_users')
                <div class="error-message">{{ $message }}</div>
                @enderror

                <button type="submit" class="update-btn">更新</button>
                <button type="button" class="cancel-edit-btn" onclick="toggleEditMode('{{ $reservation->id }}')">キャンセル</button>
            </form>
        </div>
        @empty
        <p>現在予約はありません。</p>
        @endforelse
    </section>

    <section>
        <h2 class="section-title">お気に入り店舗</h2>
        @if ($likedShops->isEmpty())
        <p>お気に入り店舗はありません。</p>
        @else
        <div class="favorites-grid">
            @foreach ($likedShops as $shop)
            <div class="restaurant-card">
                <div class="img-pack">
                    <a href="{{ asset($shop->image_url) }}" download="{{ $shop->name }}.jpg">
                        <img src="{{ asset($shop->image_url) }}" alt="{{ $shop->name }}" class="restaurant-image">
                    </a>
                </div>
                <div class="restaurant-info">
                    <div class="restaurant-name">{{ $shop->name }}</div>
                    <div class="restaurant-tags">
                        <span class="tag">#{{ $shop->area->name }}</span>
                        <span class="tag">#{{ $shop->genre->name }}</span>
                    </div>
                    <div class="card-actions">
                        <a href="{{ route('shops.detail', ['shop' => $shop->id]) }}" class="details-button">詳しくみる</a>
                        <form action="{{ route('unlike.from_mypage', ['shop' => $shop->id]) }}" method="post">
                            @csrf
                            <button class="heart-button" type="submit">♥</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </section>
</div>

<div id="review-modal" class="modal">
    <div class="modal-content">
        <span id="review-modal-close-btn" class="close">&times;</span>
        <h3 id="review-modal-title"></h3>
        <form action="{{ route('reviews.store') }}" method="POST">
            @csrf
            <input type="hidden" name="reservation_id" id="modal-reservation-id">
            <input type="hidden" name="shop_id" id="modal-shop-id">

            <div class="star-rating">
                <span data-rating="5">★</span>
                <span data-rating="4">★</span>
                <span data-rating="3">★</span>
                <span data-rating="2">★</span>
                <span data-rating="1">★</span>
            </div>
            <input type="hidden" name="rating" id="rating-value" required>

            <label for="comment">コメント</label>
            <textarea name="comment" id="comment" rows="4" maxlength="500" placeholder="コメントを入力（任意）"></textarea>

            <div class="review-btn-container">
                <button type="submit" class="submit-review-btn">投稿</button>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/mypage.js') }}"></script>
@endsection