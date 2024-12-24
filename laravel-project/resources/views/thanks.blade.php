@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/thanks.css') }}">
@endsection

@section('content')
<main>
    <div class="thanks-container">
        <p class="message">会員登録ありがとうございます</p>
        <a href="{{ url('/') }}" class="button">HOME</a>
    </div>
</main>
@endsection