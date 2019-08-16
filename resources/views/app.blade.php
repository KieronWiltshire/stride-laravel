<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>FeedMe</title>

    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body data-spy="scroll" data-target=".onpage-navigation" data-offset="60">
    <noscript>
        You need to enable JavaScript to run this app.
    </noscript>

    <div id="root"></div>

    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
