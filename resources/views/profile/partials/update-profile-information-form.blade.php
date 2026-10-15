<div class="profile-card__head">
    <p class="panel__eyebrow">PROFILE</p>
    <h3>Profile information</h3>
    <p class="muted">Update your name and email address.</p>
</div>

<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}" class="profile-form">
    @csrf
    @method('patch')

    <label>
        Name
        <input
            id="name"
            type="text"
            name="name"
            value="{{ old('name', $user->name) }}"
            required
            autofocus
            autocomplete="name"
            maxlength="160"
        >
        @error('name') <span class="master-error">{{ $message }}</span> @enderror
    </label>

    <label>
        Email
        <input
            id="email"
            type="email"
            name="email"
            value="{{ old('email', $user->email) }}"
            required
            autocomplete="username"
            maxlength="160"
        >
        @error('email') <span class="master-error">{{ $message }}</span> @enderror

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <span class="profile-form__note">
                Your email address is unverified.
                <button form="send-verification" class="text-button">Resend verification email</button>
            </span>

            @if (session('status') === 'verification-link-sent')
                <span class="profile-form__ok">A new verification link has been sent.</span>
            @endif
        @endif
    </label>

    <div class="profile-form__actions">
        <button type="submit" class="button button--soft">Save</button>

        @if (session('status') === 'profile-updated')
            <span
                class="profile-form__ok"
                x-data="{ show: true }"
                x-show="show"
                x-init="setTimeout(() => show = false, 2500)"
            >Saved.</span>
        @endif
    </div>
</form>
