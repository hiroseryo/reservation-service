<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>予約リマインダー</title>
</head>

<body>
    <p>{{ $userName }}様</p>
    <p>本日は、{{ $shopName }}のご予約がございます。</p>
    <p>時間：{{ $startAt }}</p>
    <p>人数：{{ $numOfUsers }}人</p>
    <p>お気をつけてお越しください。</p>
</body>

</html>