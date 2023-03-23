<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link type="text/css" rel="stylesheet" href="css/pdf.css">
</head>

<body>
    <main class="wrap">
        {!! $knowledge->contents !!}
    </main>
</body>

</html>
