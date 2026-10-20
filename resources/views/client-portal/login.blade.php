@extends('layouts.client-portal')

@section('title', 'Sign in')
@section('shell-class', 'cp-shell--auth')

@section('content')
    <main class="cp-auth">
        <section class="cp-auth__visual" style="background-image:url('{{ asset('images/website/hero-passport-traveler.png') }}')">
            <a class="cp-auth__home" href="{{ route('website.home') }}"><span>&larr;</span> Back to website</a>
            <div class="cp-auth__visual-copy"><span class="cp-auth__eyebrow">Your overseas career</span><h1>Welcome back to your journey.</h1><p>Access your applicant profile, continue your application and stay ready for suitable overseas opportunities.</p><ul class="cp-auth__benefits"><li>Track your application</li><li>Update your profile</li><li>Stay opportunity-ready</li></ul></div>
        </section>
        <section class="cp-auth__panel"><div class="cp-auth__form-wrap">
            <div class="cp-auth__brand"><img src="{{ asset('images/federal-group-logo.png') }}" alt="Federal Group of Companies"><span>Applicant portal</span></div>
            <div class="cp-auth__form-head"><span class="cp-auth__eyebrow">Secure sign in</span><h2>Welcome back</h2><p>Enter your account details to continue your manpower application.</p></div>
            <form method="POST" action="{{ route('client-portal.login') }}">@csrf
                <div class="cp-field"><label for="email">Email address</label><input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus autocomplete="username">@error('email') <p class="cp-error">{{ $message }}</p> @enderror</div>
                <div class="cp-field"><label for="password">Password</label><input id="password" type="password" name="password" placeholder="Enter your password" required autocomplete="current-password">@error('password') <p class="cp-error">{{ $message }}</p> @enderror</div>
                <label class="cp-check"><input type="checkbox" name="remember">Remember me on this device</label>
                <button class="cp-btn" type="submit">Sign in to your account</button>
            </form>
            <p class="cp-switch">New applicant? <a href="{{ route('client-portal.register') }}">Create your profile</a></p>
            <div class="cp-auth__employer"><span><strong>Hiring international talent?</strong>Register your company and share your manpower needs.</span><a href="{{ route('website.employers') }}">Register as employer &rarr;</a></div>
            <p class="cp-auth__secure">Your account and application documents are kept private and secure.</p>
        </div></section>
    </main>
@endsection
