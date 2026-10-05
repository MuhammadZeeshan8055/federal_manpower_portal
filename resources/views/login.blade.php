<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in — Federal Manpower Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
</head>
<body class="login-body">
    <main class="login-shell">
        <section class="login-showcase">
            <div class="login-showcase__orbs"><span></span><span></span><span></span></div>
            <a class="login-brand" href="{{ route('dashboard') }}"><img src="{{ asset('images/federal-mark.svg') }}" alt=""><div><strong>Federal</strong><small>Group of Companies</small></div></a>
            <div class="login-showcase__content"><p class="eyebrow"><span></span> FEDERAL MANPOWER PORTAL</p><h1>People. Possibility.<br><em>Progress.</em></h1><p>A modern workspace built to simplify recruitment, deployment, and workforce operations.</p><div class="login-metrics"><div><strong>1,284+</strong><small>Active candidates</small></div><div><strong>856</strong><small>Deployed workforce</small></div><div><strong>32</strong><small>Trusted clients</small></div></div></div>
            <p class="login-showcase__footer">Federal Group of Companies <span>•</span> Empowering global workforce</p>
        </section>
        <section class="login-form-side">
            <form class="login-card" action="{{ route('dashboard') }}" method="GET">
                <div class="login-card__mobile-brand"><img src="{{ asset('images/federal-mark.svg') }}" alt=""><strong>Federal</strong></div>
                <p class="login-card__eyebrow">WELCOME BACK</p><h2>Sign in to your account</h2><p class="login-card__intro">Enter your details to access the manpower portal.</p>
                <label>Email address<input type="email" value="admin@federal.com" placeholder="name@company.com" required></label>
                <label>Password<div class="password-wrap"><input type="password" value="password" required><span>◉</span></div></label>
                <div class="form-row"><label class="checkbox"><input type="checkbox" checked><span></span> Remember me</label><a href="#">Forgot password?</a></div>
                <button class="login-button" type="submit">Sign in to portal @include('partials.icon', ['name' => 'arrow'])</button>
                <div class="secure-note"><span>@include('partials.icon', ['name' => 'check'])</span>Your connection is secured and encrypted.</div>
            </form>
            <p class="login-help">Need help? <a href="#">Contact system administrator</a></p>
        </section>
    </main>
</body>
</html>
