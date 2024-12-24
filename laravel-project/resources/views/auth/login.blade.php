@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<main>
    <div class="login-container">
        <div class="login-header">
            Login
        </div>
        <div class="login-body">
            <form action="{{ route('login') }}" method="post">
                @csrf
                <div class="input-group">
                    <label for="email" class="sr-only">Email</label>
                    <input type="email" id="email" placeholder="Email" value="{{ old('email') }}" name="email" autofocus>
                    <span class="icon email-icon"></span>
                </div>
                @error('email')
                <div style="color: red">
                    {{ $message }}
                </div>
                @enderror
                <div class="input-group">
                    <label for="password" class="sr-only">Password</label>
                    <input type="password" id="password" placeholder="Password" name="password">
                    <span class="icon password-icon"></span>
                </div>
                @error('password')
                <div style="color: red">
                    {{ $message }}
                </div>
                @enderror
                <div class="button-container">
                    <button type="submit">ログイン</button>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection