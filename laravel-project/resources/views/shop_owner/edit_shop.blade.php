@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/shop_edit.css') }}">
@endsection

@section('content')
<main class="shop-edit-page">
    <h2 class="page-title">店舗情報の編集</h2>

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

    <form action="{{ route('shop_owner.updateShop') }}" method="POST" enctype="multipart/form-data" class="shop-edit-form">
        @csrf
        <div class="form-group">
            <label for="name">店舗名</label>
            <input type="text" name="name" id="name" value="{{ old('name', $shop->name) }}" required>
        </div>
        <div class="form-group">
            <label for="area_id">エリア</label>
            <select name="area_id" id="area_id" required>
                @foreach ($areas as $area)
                <option value="{{ $area->id }}" {{ $shop->area_id == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="genre_id">ジャンル</label>
            <select name="genre_id" id="genre_id" required>
                @foreach ($genres as $genre)
                <option value="{{ $genre->id }}" {{ $shop->genre_id == $genre->id ? 'selected' : '' }}>{{ $genre->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="description">概要</label>
            <textarea name="description" id="description" required>{{ old('description', $shop->description) }}</textarea>
        </div>
        <div class="form-group">
            <label for="image">画像（変更する場合は選択）</label>
            <input type="file" name="image" id="image">
            @if ($shop->image_url)
            <div class="current-image">
                <img src="{{ $shop->image_url }}" alt="{{ $shop->name }}" class="preview-image">
            </div>
            @endif
        </div>
        <button type="submit" class="btn-submit">更新</button>
    </form>
</main>
@endsection