<x-guest-layout>
    <main class="login-shell">
        <section class="login-form-side" style="width: 100%;">
            <form class="login-card" method="POST" action="{{ route('password.email') }}">
                @csrf
                <p class="login-card__eyebrow">PASSWORD</p>
                <h2>Forgot password</h2>
                <p class="login-card__intro">Enter your email and we will send a reset link.</p>

                @if (session('status'))
                    <p class="login-status">{{ session('status') }}</p>
                @endif

                <label>
                    Email address
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <span class="login-error">{{ $message }}</span>
                    @enderror
                </label>

                <button class="login-button" type="submit">Email reset link</button>
                <p class="login-help"><a href="{{ route('login') }}">Back to sign in</a></p>
            </form>
        </section>
    </main>
</x-guest-layout>
