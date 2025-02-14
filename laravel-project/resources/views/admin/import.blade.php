@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/import.css') }}">
@endsection

@section('content')
<div class="container">
    <h1>CSVインポート</h1>

    @if(session('success'))
    <div class="alert-success">{!! session('success') !!}</div>
    @elseif(session('error'))
    <div class="alert-danger">{!! session('error') !!}</div>
    @endif

    <form action="{{ route('shops.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="csv_file">CSVファイル</label>
            <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv.txt">
            @error('csv_file')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit" class="btn-primary">インポート開始</button>
    </form>
</div>

<script>
    document.getElementById('csv_file').addEventListener('change', function() {
        const file = this.files[0];

        if (file && file.size > 1 * 1000000) {
            alert('ファイルサイズが1MBを超えています。1MB以下のファイルを選択してください。');
            this.value = "";
        }
    });

    document.addEventListener("DOMContentLoaded", function() {
        var alertSuccess = document.querySelector('.alert-success');
        if (alertSuccess) {
            var html = alertSuccess.innerHTML;
            var marker = "。";
            var pos = html.indexOf(marker);
            if (pos !== -1) {
                var before = html.substring(0, pos + marker.length);
                var after = html.substring(pos + marker.length);
                alertSuccess.innerHTML = before + '<span class="error-text">' + after + '</span>';
            }
        }
    });
</script>

</script>
@endsection