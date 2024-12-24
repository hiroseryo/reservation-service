@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/shop_reservations.css') }}">
@endsection

@section('content')
<main class="shop-reservations-page">
    <h2 class="page-title">店舗予約情報</h2>

    @if ($errors->any())
    <div class="error-messages">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if ($reservations->isEmpty())
    <p class="no-reservations-message">現在、予約はありません。</p>
    @else
    <div class="reservations-table-wrapper">
        <table class="reservations-table">
            <thead>
                <tr>
                    <th>利用者名</th>
                    <th>日付</th>
                    <th>時間</th>
                    <th>人数</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reservations as $reservation)
                <tr>
                    <td>{{ $reservation->user->name }}</td>
                    <td>{{ $reservation->start_at->format('Y-m-d') }}</td>
                    <td>{{ $reservation->start_at->format('H:i') }}</td>
                    <td>{{ $reservation->num_of_users }}人</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</main>
@endsection