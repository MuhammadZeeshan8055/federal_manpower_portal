<div class="profile-card__head">
    <p class="panel__eyebrow">SECURITY</p>
    <h3>Update password</h3>
    <p class="muted">Use a long password to keep your account safe.</p>
</div>

<form method="post" action="{{ route('password.update') }}" class="profile-form">
    @csrf
    @method('put')

    <label>
        Current password
        <input
            id="update_password_current_password"
            type="password"
            name="current_password"
            autocomplete="current-password"
        >
        @error('current_password', 'updatePassword')
            <span class="master-error">{{ $message }}</span>
        @enderror
    </label>

    <label>
        New password
        <input
            id="update_password_password"
            type="password"
            name="password"
            autocomplete="new-password"
        >
        @error('password', 'updatePassword')
            <span class="master-error">{{ $message }}</span>
        @enderror
    </label>

    <label>
        Confirm password
        <input
            id="update_password_password_confirmation"
            type="password"
            name="password_confirmation"
            autocomplete="new-password"
        >
        @error('password_confirmation', 'updatePassword')
            <span class="master-error">{{ $message }}</span>
        @enderror
    </label>

    <div class="profile-form__actions">
        <button type="submit" class="button button--soft">Save</button>

        @if (session('status') === 'password-updated')
            <span
                class="profile-form__ok"
                x-data="{ show: true }"
                x-show="show"
                x-init="setTimeout(() => show = false, 2500)"
            >Saved.</span>
        @endif
    </div>
</form>
