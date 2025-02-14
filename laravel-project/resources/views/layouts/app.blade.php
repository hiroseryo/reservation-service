<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rese</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <header>
        <div class="header-container">
            <button class="menu-button" id="modal-open-btn">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <h1>Rese</h1>
        </div>
    </header>

    @yield('content')

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
    <script src="{{ asset('js/script.js') }}"></script>
</body>

</html>