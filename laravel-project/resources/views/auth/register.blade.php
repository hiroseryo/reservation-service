@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<main>
    <div class="register-container">
        <div class="register-header">Registration</div>
        <div class="register-body">
            <form method="post" action="{{ route('register') }}">
                @csrf
                <div class="input-group">
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                    <input type="text" placeholder="Username" name="name" value="{{ old('name') }}" autofocus>
                </div>
                @error('name')
                <div style="color: red">
                    {{ $message }}
                </div>
                @enderror

                <div class="input-group">
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                        <polyline points="22,6 12,13 2,6" />
                    </svg>
                    <input name="email" type="email" value="{{ old('email') }}" placeholder="Email">
                </div>
                @error('email')
                <div style="color: red">
                    {{ $message }}
                </div>
                @enderror

                <div class="input-group">
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg>
                    <input name="password" type="password" placeholder="Password">
                </div>
                @error('password')
                <div style="color: red">
                    {{ $message }}
                </div>
                @enderror

                <div class="input-group">
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg>
                    <input name="password_confirmation" type="password" placeholder="確認用 password">
                </div>

                <button type="submit" class="submit-button">登録</button>
            </form>
        </div>
    </div>
</main>
@endsection