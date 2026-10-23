@extends('layouts.employer-portal')

@section('title', 'Employer Dashboard')

@section('content')
<section class="page-hero page-hero--employers">
    <div class="site-wrap page-hero__inner">
        <span class="section-kicker">Employer portal</span>
        <h1>Welcome{{ $profile?->contact_person ? ', '.$profile->contact_person : '' }}.</h1>
        <p>{{ $profile?->company_name ?: 'Your company dashboard' }}</p>
    </div>
</section>

<section class="page-section page-section--soft">
    <div class="site-wrap ep-home">
        @if (! $profile)
            <div class="contact-form ep-panel">
                <h2>Profile missing</h2>
                <p class="ep-panel__lead">Your employer profile could not be found. Please contact the office for help.</p>
                <a href="{{ route('website.contact') }}" class="btn btn-accent">Contact us <span>&rarr;</span></a>
            </div>
        @elseif ($profile->isPending())
            <div class="ep-home__grid">
                <div class="contact-details">
                    <span class="section-kicker">Status</span>
                    <h2>Registration under review</h2>
                    <p>An admin will approve your company before you can post jobs. You will use this portal once approved.</p>
                    <div class="contact-card">
                        <strong>Company</strong>
                        <span>{{ $profile->company_name }}</span>
                    </div>
                    <div class="contact-card">
                        <strong>Contact</strong>
                        <span>{{ collect([auth()->user()->email, $profile->phone])->filter()->implode(' · ') }}</span>
                    </div>
                </div>
                <div class="contact-form ep-panel">
                    <span class="ep-status ep-status--pending">Pending approval</span>
                    <h2>What happens next</h2>
                    <p class="ep-panel__lead">Your registration is waiting for admin review. Job posting unlocks after approval.</p>
                    <ul class="ep-steps">
                        <li>Office reviews your company details</li>
                        <li>You receive access to post vacancies</li>
                        <li>Each job goes live after a separate admin check</li>
                    </ul>
                </div>
            </div>
        @elseif ($profile->isRejected())
            <div class="contact-form ep-panel">
                <span class="ep-status ep-status--rejected">Rejected</span>
                <h2>Registration not approved</h2>
                <p class="ep-panel__lead">
                    Your company registration was not approved.
                    @if ($profile->rejection_reason)
                        Reason: {{ $profile->rejection_reason }}
                    @endif
                </p>
                <a href="{{ route('website.contact') }}" class="btn btn-accent">Contact the office <span>&rarr;</span></a>
            </div>
        @else
            <div class="ep-home__grid">
                <div class="contact-details">
                    <span class="section-kicker">Approved</span>
                    <h2>{{ $profile->company_name }}</h2>
                    <p>Your company is approved. Post jobs from My jobs — each vacancy stays pending until an admin activates it on the public site.</p>
                    <div class="contact-card">
                        <strong>Contact person</strong>
                        <span>{{ $profile->contact_person ?: '—' }}</span>
                    </div>
                    <div class="contact-card">
                        <strong>Location</strong>
                        <span>{{ collect([$profile->city, $profile->country])->filter()->implode(', ') ?: '—' }}</span>
                    </div>
                </div>
                <div class="contact-form ep-panel">
                    <span class="ep-status ep-status--approved">Approved</span>
                    <h2>Ready to hire</h2>
                    <p class="ep-panel__lead">Create a vacancy, submit it for approval, and track status from My jobs.</p>
                    <a href="{{ route('employer.jobs') }}" class="btn btn-accent">Manage jobs <span>&rarr;</span></a>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
