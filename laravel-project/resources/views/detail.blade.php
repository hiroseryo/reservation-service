<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rese</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/detail.css') }}">
</head>

<body>
    <header>
        <button class="menu-button" id="modal-open-btn">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <h1>Rese</h1>
    </header>

    <div class="container">
        <main>
            <div class="title">
                <a href="/" class="back-button">
                    ＜ </a>
                <h2>{{ $shop->name }}</h2>
            </div>
            <a href="{{ asset($shop->image_url) }}" download="{{ $shop->name }}.jpg" class="restaurant-image-link">
                <img src="{{ asset($shop->image_url) }}" alt="{{ $shop->name }}" class="restaurant-image">
            </a>
            <div class="restaurant-title">
                <p>#{{ $shop->area->name }}</p>
                <p>#{{ $shop->genre->name }}</p>
            </div>
            <p class="restaurant-description">
                {{ $shop->description }}
            </p>
        </main>

        @role('user')
        <aside class="reservation-card">
            <h3 class="reservation-title">予約</h3>
            <form action="{{ route('reserve.store') }}" method="post">
                @csrf
                <input type="hidden" name="shop_id" value="{{ old('shop_id', $shop->id ?? '') }}">
                <div class="form-group">
                    <input type="date" id="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <input type="time" id="time" name="time" class="form-control" value="{{ old('time', '12:00') }}" required>
                </div>
                <div class="form-group">
                    <select class="form-control" id="num_of_users" name="num_of_users" required>
                        @for($i = 1; $i <= 9; $i++)
                            <option value="{{ $i }}" {{ old('num_of_users') == $i ? 'selected' : '' }}>{{ $i }}人</option>
                            @endfor
                            <option value="10" {{ old('num_of_users') == 10 ? 'selected' : '' }}>10人以上</option>
                    </select>
                </div>

                @if ($errors->any())
                <div class="error-messages">
                    @foreach ($errors->all() as $error)
                    <div style="color: #e5417d;">{{ $error }}</div>
                    @endforeach
                </div>
                @endif

                <div class="reservation-details">
                    <div class="detail-row">
                        <span>Shop</span>
                        <span>{{ $shop->name }}</span>
                    </div>
                    <div class="detail-row">
                        <span>Date</span>
                        <span id="selected-date">{{ date('Y-m-d') }}</span>
                    </div>
                    <div class="detail-row">
                        <span>Time</span>
                        <span id="selected-time">12:00</span>
                    </div>
                    <div class="detail-row">
                        <span>Number</span>
                        <span id="selected-num">{{ old('num_of_users', '1') }}人</span>
                    </div>
                </div>
                <button type="submit" class="submit-button">予約する</button>
            </form>
            @endrole

            @guest
            <div class="guest-container">
                <div class="login-message">
                    <p>予約をするにはユーザーとしてログインしてください。</p>
                    <a href="{{ route('login') }}">Login</a>
                </div>
            </div>
            @endguest
        </aside>
    </div>

    <div class="modal" id="modal">
        <div class="modal-content">
            <span id="modal-close-btn" class="close">&times;</span>
            <div class="modal-links">
                @guest
                <a href="{{ route('register') }}">Registration</a>
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ url('/') }}">Home</a>
                @endguest

                @role('admin')
                <a href="{{ route('admin.owners.create') }}">店舗代表者作成</a>
                <a href="{{ route('admin.announcement') }}">お知らせメール送信</a>
                <a href="{{ url('/') }}">Home</a>
                @endrole

                @role('shop_owner')
                <a href="{{ route('shop_owner.createShop') }}">店舗作成</a>
                <a href="{{ route('shop_owner.editShop') }}">店舗更新</a>
                <a href="{{ route('shop_owner.reservations') }}">予約情報</a>
                <a href="{{ url('/') }}">Home</a>
                @endrole

                @role('user')
                <a href="{{ url('/') }}">Home</a>
                <a href="{{ url('/mypage') }}">MyPage</a>
                @endrole

                @auth
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                @endauth
            </div>
        </div>
    </div>
    <script src="{{ asset('js/detail.js') }}"></script>
</body>

</html>