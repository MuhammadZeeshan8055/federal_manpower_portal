@extends('layouts.website')

@section('title', 'Employer Login')

@section('content')
<section class="page-hero page-hero--employers">
    <div class="site-wrap page-hero__inner">
        <span class="section-kicker">For employers</span>
        <h1>Employer login.</h1>
        <p>Sign in to manage your company profile and job posts.</p>
    </div>
</section>

<section class="page-section">
    <div class="site-wrap contact-layout">
        <div class="contact-details">
            <span class="section-kicker">Hiring with us</span>
            <h2>Access your employer desk</h2>
            <p>Post vacancies after your company is approved. Every new job stays pending until an admin activates it on the website.</p>
            <div class="contact-card">
                <strong>New company?</strong>
                <span>Register first, then wait for admin approval.</span>
            </div>
            <p style="margin-top: 18px;">
                <a href="{{ route('employer.register') }}" class="text-link">Register as employer <span>&rarr;</span></a>
            </p>
        </div>

        <form class="contact-form" method="POST" action="{{ route('employer.login') }}">
            @csrf
            <h2>Sign in</h2>

            <label>
                Email address
                <input type="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email') <span class="ep-field-error">{{ $message }}</span> @enderror
            </label>

            <label>
                Password
                <input type="password" name="password" required>
                @error('password') <span class="ep-field-error">{{ $message }}</span> @enderror
            </label>

            <label class="employer-remember">
                <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                <span>Remember me</span>
            </label>

            <button class="btn btn-accent" type="submit">Log in <span>&rarr;</span></button>
        </form>
    </div>
</section>
@endsection
