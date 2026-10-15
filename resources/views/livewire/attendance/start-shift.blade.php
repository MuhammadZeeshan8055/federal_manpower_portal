<main class="login-shell">
    <section class="login-showcase">
        <div class="login-showcase__orbs"><span></span><span></span><span></span></div>
        <a class="login-brand" href="{{ route('dashboard') }}">
            <img src="{{ asset('images/federal-mark.png') }}" alt="Federal Group symbol">
            <div>
                <strong>Federal</strong>
                <small>Group of Companies</small>
            </div>
        </a>
        <div class="login-showcase__content">
            <p class="eyebrow"><span></span> ATTENDANCE</p>
            <h1>Start your<br><em>shift.</em></h1>
            <p>Check in from the office network to open today’s attendance and enter the portal.</p>
        </div>
        <p class="login-showcase__footer">Federal Group of Companies <span>•</span> Empowering global workforce</p>
    </section>

    <section class="login-form-side">
        <div class="login-card attendance-gate-card">
            <div class="login-card__mobile-brand">
                <img src="{{ asset('images/federal-mark.png') }}" alt="Federal Group symbol">
                <strong>Federal</strong>
            </div>

            <p class="login-card__eyebrow">CHECK IN</p>
            <h2>Start shift</h2>
            <p class="login-card__intro">
                Signed in as <strong>{{ auth()->user()->name }}</strong>.
                Office hours: <strong>{{ $officeStartLabel }} – {{ $officeEndLabel }}</strong>.
                Your current IP: <strong>{{ $clientIp !== '' ? $clientIp : 'unknown' }}</strong>
            </p>

            @if ($errorMessage !== '')
                <p class="login-status attendance-gate-error" role="alert">{{ $errorMessage }}</p>
            @endif

            @if ($infoMessage !== '')
                <p class="login-status">{{ $infoMessage }}</p>
            @endif

            <button
                type="button"
                class="button button--soft attendance-gate-btn"
                wire:click="checkIn"
                wire:loading.attr="disabled"
            >
                Check In &amp; Enter Portal
            </button>

            <form method="POST" action="{{ route('logout') }}" class="attendance-gate-logout">
                @csrf
                <button type="submit" class="button button--ghost">Sign out</button>
            </form>
        </div>
    </section>
</main>
