@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/done.css') }}">
@endsection

@section('content')
<main>
    <div class="done-container">
        <p class="message">ご予約ありがとうございます</p>
        <a href="{{ url('/mypage') }}" class="button">MYページ</a>
    </div>
</main>
@endsection