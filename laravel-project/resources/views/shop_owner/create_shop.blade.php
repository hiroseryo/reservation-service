@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/shop_create.css') }}">
@endsection

@section('content')
<main class="shop-create-page">
    <h2 class="page-title">店舗情報の登録</h2>

    @if ($errors->any())
    <div class="error-messages">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if (session('success'))
    <div class="success-message">
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('shop_owner.storeShop') }}" method="POST" enctype="multipart/form-data" class="shop-create-form">
        @csrf
        <div class="form-group">
            <label for="name">店舗名</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required>
        </div>
        <div class="form-group">
            <label for="area_id">エリア</label>
            <select name="area_id" id="area_id" required>
                @foreach ($areas as $area)
                <option value="{{ $area->id }}">{{ $area->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="genre_id">ジャンル</label>
            <select name="genre_id" id="genre_id" required>
                @foreach ($genres as $genre)
                <option value="{{ $genre->id }}">{{ $genre->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="description">概要</label>
            <textarea name="description" id="description" required>{{ old('description') }}</textarea>
        </div>
        <div class="form-group">
            <label for="image">画像</label>
            <input type="file" name="image" id="image">
        </div>
        <button type="submit" class="btn-submit">登録</button>
    </form>
</main>
@endsection