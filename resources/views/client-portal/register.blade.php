@extends('layouts.client-portal')

@section('title', 'Register')

@section('content')
    <header class="cp-brand">
        <img src="{{ asset('images/federal-group-logo.png') }}" alt="Federal Group of Companies">
        <p>Applicant register</p>
    </header>

    <main class="cp-card">
        <h1 class="cp-title">Create your account</h1>
        <p class="cp-intro">Register to fill your bio data and track your application status.</p>

        <form method="POST" action="{{ route('client-portal.register') }}">
            @csrf

            <div class="cp-field">
                <label for="name">Full name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                @error('name') <p class="cp-error">{{ $message }}</p> @enderror
            </div>

            <div class="cp-field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                @error('email') <p class="cp-error">{{ $message }}</p> @enderror
            </div>

            <div class="cp-field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password">
                @error('password') <p class="cp-error">{{ $message }}</p> @enderror
            </div>

            <div class="cp-field">
                <label for="password_confirmation">Confirm password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
            </div>

            <button class="cp-btn" type="submit">Create account</button>
        </form>

        <p class="cp-switch">
            Already registered?
            <a href="{{ route('client-portal.login') }}">Sign in</a>
        </p>
    </main>
@endsection
