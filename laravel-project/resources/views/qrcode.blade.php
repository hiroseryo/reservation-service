@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/qr.css') }}">
@endsection

@section('content')
<main class="qr-code-page">
    <div class="qr-code-intro">
        <div class="qr-code-title">{{ $reservation->shop->name }}のQRコード</div>
        <p class="qr-code-instructions">下記のQRコードを来店時にお店側にお見せください。</p>
    </div>

    <div class="qr-code-section">
        <div class="qr-code">
            {!! $qrCodeSvg !!}
        </div>
    </div>

    <div class="qr-code-footer">
        <p><a href="{{ route('mypage') }}" class="back-to-mypage">マイページに戻る</a></p>
    </div>
</main>
@endsection