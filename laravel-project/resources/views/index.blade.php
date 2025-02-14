<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rese</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body data-user="{{ Auth::check() ? Auth::id() : '' }}">
    <header>
        <div class="header-container">
            <button class="menu-button" id="modal-open-btn">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <h1>Rese</h1>
        </div>
        @php
        $sortTextMapping = [
        'random' => 'ランダム',
        'low' => '評価が低い順',
        'high' => '評価が高い順',
        ];
        @endphp
        <div class="search-container">
            <form action="{{ route('shops.index') }}" method="get" class="search-form">
                <div class="select-wrapper">
                    <select name="sort" id="sort-select" onchange="this.form.submit()">
                        <option value="random" {{ $request->sort === 'random' ? 'selected' : '' }}>ランダム</option>
                        <option value="low" {{ $request->sort === 'low' ? 'selected' : '' }}>評価が低い順</option>
                        <option value="high" {{ $request->sort === 'high' ? 'selected' : '' }}>評価が高い順</option>
                    </select>
                </div>
                <div class="select-wrapper">
                    <select name="area_id" onchange="this.form.submit()">
                        <option value="All">All area</option>
                        @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ ($request->area_id == $area->id) ? 'selected' : '' }}>
                            {{ $area->name}}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="select-wrapper">
                    <select name="genre_id" onchange="this.form.submit()">
                        <option value="All">All genre</option>
                        @foreach($genres as $genre)
                        <option value="{{ $genre->id}}" {{ ($request->genre_id == $genre->id) ? 'selected' : ''}}>
                            {{ $genre->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="search-wrapper">
                    <button class="search-button" aria-label="検索" type="submit">
                        🔍
                    </button>
                    <input type="text" placeholder="Search ..." name="name" value="{{ $request->name }}">
                </div>
            </form>
        </div>
    </header>

    <main>
        <div class="search-result">
            <p>検索情報</p>
            <p>:</p>
            <p id="sort-result">"{{ $sortTextMapping[$request->sort ?? 'random'] }}"</p>
        </div>
        <div class="restaurant-grid">
            @foreach($shops as $shop)
            <div class="restaurant-card">
                <div class="img-pack">
                    <a href="{{ asset($shop->image_url) }}" download="{{ $shop->name }}.jpg">
                        <img src="{{ asset($shop->image_url) }}" alt="{{ $shop->name }}" class="restaurant-image">
                    </a>
                </div>
                <div class="restaurant-info">
                    <div class="restaurant-header">
                        <h3 class="restaurant-name">{{ $shop->name }}</h3>
                        <div class="stars">★</div>
                        @if (!is_null($shop->reviews_avg_rating))
                        <p class="rating">
                            {{ number_format($shop->reviews_avg_rating, 2) }}
                        </p>
                        @else
                        <p class="no-review">
                            投稿なし
                        </p>
                        @endif
                    </div>

                    <div class="restaurant-tags">
                        <span class="tag">#{{ $shop->area->name }}</span>
                        <span class="tag">#{{ $shop->genre->name}}</span>
                    </div>
                    <div class="card-actions">
                        <a class="details-button" href="{{ route('shops.detail', ['shop' => $shop->id]) }}">詳しくみる</a>
                        <button class="like-btn" data-shop-id="{{ $shop->id }}">
                            @if(Auth::check() && Auth::user()->likedShops->contains($shop->id))
                            <span class="liked">♥</span>
                            @else
                            <span class="unliked">♥</span>
                            @endif
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </main>

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
                <a href="{{ route('shops.import.form') }}">CSVインポート</a>
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

    <script src="{{ asset('js/home.js') }}"></script>
</body>

</html>