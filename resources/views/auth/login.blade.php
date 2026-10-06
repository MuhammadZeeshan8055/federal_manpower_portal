<x-guest-layout>
    <main class="login-shell">
        <section class="login-showcase">
            <div class="login-showcase__orbs"><span></span><span></span><span></span></div>
            <a class="login-brand" href="{{ route('login') }}">
                <img src="{{ asset('images/federal-mark.png') }}" alt="Federal Group symbol">
                <div>
                    <strong>Federal</strong>
                    <small>Group of Companies</small>
                </div>
            </a>
            <div class="login-showcase__content">
                <p class="eyebrow"><span></span> FEDERAL MANPOWER PORTAL</p>
                <h1>People. Possibility.<br><em>Progress.</em></h1>
                <p>A modern workspace built to simplify recruitment, deployment, and workforce operations.</p>
            </div>
            <p class="login-showcase__footer">Federal Group of Companies <span>•</span> Empowering global workforce</p>
        </section>

        <section class="login-form-side">
            <form class="login-card" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="login-card__mobile-brand">
                    <img src="{{ asset('images/federal-mark.png') }}" alt="Federal Group symbol">
                    <strong>Federal</strong>
                </div>
                <p class="login-card__eyebrow">WELCOME BACK</p>
                <h2>Sign in to your account</h2>
                <p class="login-card__intro">Enter your details to access the manpower portal.</p>

                @if (session('status'))
                    <p class="login-status">{{ session('status') }}</p>
                @endif

                <label>
                    Email address
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="name@company.com"
                        required
                        autofocus
                        autocomplete="username"
                    >
                    @error('email')
                        <span class="login-error">{{ $message }}</span>
                    @enderror
                </label>

                <label>
                    Password
                    <div class="password-wrap">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                        >
                    </div>
                    @error('password')
                        <span class="login-error">{{ $message }}</span>
                    @enderror
                </label>

                <div class="form-row">
                    <label class="checkbox">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span></span>
                        Remember me
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">Forgot password?</a>
                    @endif
                </div>

                <button class="login-button" type="submit">
                    Sign in to portal
                    @include('partials.icon', ['name' => 'arrow'])
                </button>
            </form>
            <p class="login-help">Need help? Contact system administrator</p>
        </section>
    </main>
</x-guest-layout>
