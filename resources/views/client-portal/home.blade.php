@extends('layouts.client-portal-app')

@section('title', 'Applicant Dashboard')

@section('content')
<section class="page-hero page-hero--candidates">
    <div class="site-wrap page-hero__inner">
        <span class="section-kicker">Applicant portal</span>
        <h1>Welcome{{ $user->name ? ', '.$user->name : '' }}.</h1>
        <p>
            @if ($client)
                Your bio data is on file. Track progress below, or update your details anytime.
            @else
                Next step: fill your bio data form. Office staff will update your process status after review.
            @endif
        </p>
    </div>
</section>

<section class="page-section page-section--soft">
    <div class="site-wrap ep-home">
        <div class="ep-home__grid">
            <div class="contact-details">
                <span class="section-kicker">Your application</span>
                <h2>{{ $client ? 'Profile on file' : 'Get started' }}</h2>
                <p>Process status is updated by the office. Documents stay private and secure.</p>

                @if ($client)
                    <div class="contact-card">
                        <strong>Process status</strong>
                        <span>{{ $client->processStatus?->name ?: 'Pending review' }}</span>
                    </div>
                    <div class="contact-card">
                        <strong>Job title</strong>
                        <span>{{ $client->trade?->name ?: '—' }}</span>
                    </div>
                    <div class="contact-card">
                        <strong>Country</strong>
                        <span>{{ $client->country?->name ?: '—' }}</span>
                    </div>
                @else
                    <div class="contact-card">
                        <strong>Step 1</strong>
                        <span>Complete your bio data and documents</span>
                    </div>
                    <div class="contact-card">
                        <strong>Step 2</strong>
                        <span>Wait for office review and process updates</span>
                    </div>
                @endif
            </div>

            <div class="contact-form ep-panel">
                <h2>{{ $client ? 'Manage application' : 'Ready to apply' }}</h2>
                <p class="ep-panel__lead">
                    @if ($client)
                        Edit your details anytime. Public status page is shareable without private documents.
                    @else
                        Fill one complete application so our team can match you to suitable roles.
                    @endif
                </p>
                <div class="ep-actions">
                    @if ($client)
                        <a href="{{ route('client-portal.application') }}" class="btn btn-accent">Edit application <span>&rarr;</span></a>
                        @if ($statusUrl)
                            <a href="{{ $statusUrl }}" class="btn btn-ghost" target="_blank" rel="noopener">Public status page</a>
                        @endif
                    @else
                        <a href="{{ route('client-portal.application') }}" class="btn btn-accent">Fill application form <span>&rarr;</span></a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
