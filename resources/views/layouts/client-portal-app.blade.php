<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@hasSection('title')@yield('title')@else{{ $title ?? 'Applicant Portal' }}@endif &mdash; Federal Manpower</title>
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/website.css') }}?v={{ filemtime(public_path('css/website.css')) }}">
    @livewireStyles
</head>
<body class="site-body employer-portal-body">
    <div class="site-topbar">
        <div class="site-wrap site-topbar__inner">
            <div class="site-topbar__contact">
                <span>Applicant portal</span>
            </div>
            <div class="site-topbar__social">
                <a href="{{ route('website.home') }}">Back to website</a>
            </div>
        </div>
    </div>

    <header class="site-header">
        <div class="site-wrap site-header__inner">
            <a href="{{ route('client-portal.home') }}" class="site-logo site-logo--group" aria-label="Applicant home">
                <img src="{{ asset('images/federal-group-logo.png') }}" alt="Federal Group of Companies" width="846" height="295">
            </a>
            @auth
                @if (auth()->user()->isClient())
                    <nav class="ep-nav" aria-label="Applicant navigation">
                        <a href="{{ route('client-portal.home') }}" class="{{ request()->routeIs('client-portal.home') ? 'is-active' : '' }}">Dashboard</a>
                        <a href="{{ route('client-portal.application') }}" class="{{ request()->routeIs('client-portal.application') ? 'is-active' : '' }}">Application</a>
                        <form method="POST" action="{{ route('client-portal.logout') }}">
                            @csrf
                            <button class="btn btn-ghost" type="submit">Log out</button>
                        </form>
                    </nav>
                @endif
            @endauth
        </div>
    </header>

    <main>
        @yield('content')
        {{ $slot ?? '' }}
    </main>

    <footer class="ep-footer">
        <div class="site-wrap">
            <span>&copy; {{ date('Y') }} Federal Manpower</span>
            <a href="{{ route('website.contact') }}">Need help? Contact us</a>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
