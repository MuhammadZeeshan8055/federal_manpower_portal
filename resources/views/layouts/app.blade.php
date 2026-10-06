<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#145f90">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('images/favicon.png') }}">
    <title>{{ config('app.name', 'Federal Manpower Portal') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
    @livewireStyles
</head>
<body class="portal-body">
    <main class="portal-content" style="max-width: 960px; margin: 0 auto; padding: 32px 20px;">
        {{ $slot }}
    </main>
    @livewireScripts
</body>
</html>
