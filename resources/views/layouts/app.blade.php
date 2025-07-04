<?php
    use Carbon\Carbon;
?>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Validador Json') }}</title>
    <link rel="stylesheet" href="css/styles.css" />
</head>
<body>
    <div>
        @yield('content')
    </div>
</body>
</html>