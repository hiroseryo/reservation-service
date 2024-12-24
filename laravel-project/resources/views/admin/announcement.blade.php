@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin-announcement.css') }}">
@endsection

@section('content')
<main class="admin-announcement-page">
    <h2 class="page-title">利用者へのお知らせメール送信</h2>

    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
    <div class="alert alert-error">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.sendAnnouncement') }}" method="POST" class="announcement-form">
        @csrf
        <div class="form-group">
            <label for="title">タイトル</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" required>
        </div>
        <div class="form-group">
            <label for="message">本文</label>
            <textarea name="message" id="message" rows="10" required>{{ old('message') }}</textarea>
        </div>
        <button type="submit" class="btn-submit">送信</button>
    </form>
</main>
@endsection