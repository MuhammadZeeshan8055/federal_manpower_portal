@extends('layouts.portal')

@section('title', 'My profile')

@section('content')
<div class="profile-page">
    <section class="feature-page-head">
        <a href="{{ route('dashboard') }}" class="text-button back-button">
            <span aria-hidden="true">&larr;</span> Back to workspace
        </a>
        <p class="eyebrow"><span></span> ACCOUNT</p>
        <h2>My profile</h2>
        <p class="muted">Update your name, email, and password.</p>
    </section>

    <div class="profile-page__stack">
        <section class="panel profile-card">
            @include('profile.partials.update-profile-information-form')
        </section>

        <section class="panel profile-card">
            @include('profile.partials.update-password-form')
        </section>

        <section class="panel profile-card profile-card--danger">
            @include('profile.partials.delete-user-form')
        </section>
    </div>
</div>
@endsection
