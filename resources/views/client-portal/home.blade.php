@extends('layouts.client-portal')

@section('title', 'My application')

@section('content')
    <div class="cp-topbar">
        <span>{{ $user->name }}</span>
        <form method="POST" action="{{ route('client-portal.logout') }}">
            @csrf
            <button class="cp-link-btn" type="submit">Sign out</button>
        </form>
    </div>

    <header class="cp-brand">
        <img src="{{ asset('images/federal-group-logo.png') }}" alt="Federal Group of Companies">
        <p>Client portal</p>
    </header>

    <main class="cp-card">
        <h1 class="cp-title">Your application</h1>
        <p class="cp-intro">
            @if ($client)
                Your bio data is on file. Track progress below, or update your details anytime.
            @else
                Next step: fill your bio data form. Office staff will update your process status after review.
            @endif
        </p>

        @if ($client)
            <div class="cp-item">
                <span>Process status</span>
                <span>{{ $client->processStatus?->name ?: 'Pending review' }}</span>
            </div>
            <div class="cp-item">
                <span>Job title</span>
                <span>{{ $client->trade?->name ?: '—' }}</span>
            </div>
            <div class="cp-item">
                <span>Country</span>
                <span>{{ $client->country?->name ?: '—' }}</span>
            </div>
        @endif

        <div class="cp-actions">
            @if ($client)
                <a class="cp-btn" href="{{ route('client-portal.application') }}">Edit application</a>
                @if ($statusUrl)
                    <a class="cp-btn cp-btn--ghost" href="{{ $statusUrl }}" target="_blank" rel="noopener">Open public status page</a>
                @endif
            @else
                <a class="cp-btn" href="{{ route('client-portal.application') }}">Fill application form</a>
            @endif
        </div>

        <p class="cp-note">
            Process status is updated by the office. Documents stay private and secure.
        </p>
    </main>
@endsection
