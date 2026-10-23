@extends('layouts.client-portal')

@section('title', 'Register')
@section('shell-class', 'cp-shell--auth')

@section('content')
    <main class="cp-auth cp-auth--register">
        <section class="cp-auth__visual" style="background-image:url('{{ asset('images/website/hero-business-traveler.png') }}')">
            <a class="cp-auth__home" href="{{ route('website.home') }}"><span>&larr;</span> Back to website</a>
            <div class="cp-auth__visual-copy"><span class="cp-auth__eyebrow">Start your application</span><h1>Build a profile that opens doors.</h1><p>Register once, share your experience and be prepared for suitable overseas career opportunities.</p><ul class="cp-auth__benefits"><li>One complete profile</li><li>Clear application steps</li><li>Ongoing candidate support</li></ul></div>
        </section>
        <section class="cp-auth__panel"><div class="cp-auth__form-wrap">
            <div class="cp-auth__brand"><img src="{{ asset('images/federal-group-logo.png') }}" alt="Federal Group of Companies"><span>Applicant portal</span></div>
            <div class="cp-auth__form-head"><span class="cp-auth__eyebrow">Applicant registration</span><h2>Create your profile</h2><p>Set up your secure account to complete your bio data and track your application.</p></div>
            <form method="POST" action="{{ route('client-portal.register') }}">@csrf
                <div class="cp-field"><label for="name">Full name</label><input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Enter your full name" required autofocus autocomplete="name">@error('name') <p class="cp-error">{{ $message }}</p> @enderror</div>
                <div class="cp-field"><label for="email">Email address</label><input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required autocomplete="username">@error('email') <p class="cp-error">{{ $message }}</p> @enderror</div>
                <div class="cp-field"><label for="password">Password</label><input id="password" type="password" name="password" placeholder="Create a secure password" required autocomplete="new-password">@error('password') <p class="cp-error">{{ $message }}</p> @enderror</div>
                <div class="cp-field"><label for="password_confirmation">Confirm password</label><input id="password_confirmation" type="password" name="password_confirmation" placeholder="Repeat your password" required autocomplete="new-password"></div>
                <button class="cp-btn" type="submit">Create applicant account</button>
            </form>
            <p class="cp-switch">Already registered? <a href="{{ route('client-portal.login') }}">Sign in to your account</a></p>
            <div class="cp-auth__employer"><span><strong>Are you an employer?</strong>Join our recruitment network and submit job requirements.</span><a href="{{ route('employer.register') }}">Register as employer &rarr;</a></div>
            <p class="cp-auth__secure">By registering, you can securely save and manage your application information.</p>
        </div></section>
    </main>
@endsection
