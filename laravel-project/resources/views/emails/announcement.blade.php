<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
</head>

<body>
    <h1>{{ $title }}</h1>
    <p>{!! nl2br(e($messageBody)) !!}</p>
</body>

</html>