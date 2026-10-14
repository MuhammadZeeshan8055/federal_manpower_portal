@extends('layouts.client-portal')

@section('title', 'Sign in')

@section('content')
    <header class="cp-brand">
        <img src="{{ asset('images/federal-group-logo.png') }}" alt="Federal Group of Companies">
        <p>Applicant sign in</p>
    </header>

    <main class="cp-card">
        <h1 class="cp-title">Welcome back</h1>
        <p class="cp-intro">Sign in to continue your manpower application.</p>

        <form method="POST" action="{{ route('client-portal.login') }}">
            @csrf

            <div class="cp-field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                @error('email') <p class="cp-error">{{ $message }}</p> @enderror
            </div>

            <div class="cp-field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
                @error('password') <p class="cp-error">{{ $message }}</p> @enderror
            </div>

            <label class="cp-check">
                <input type="checkbox" name="remember">
                Remember me
            </label>

            <button class="cp-btn" type="submit">Sign in</button>
        </form>

        <p class="cp-switch">
            New applicant?
            <a href="{{ route('client-portal.register') }}">Create an account</a>
        </p>
    </main>
@endsection
