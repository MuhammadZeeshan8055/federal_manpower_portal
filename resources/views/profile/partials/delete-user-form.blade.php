<div
    class="profile-card__head"
    x-data="{ open: {{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }} }"
>
    <p class="panel__eyebrow">DANGER ZONE</p>
    <h3>Delete account</h3>
    <p class="muted">
        Once your account is deleted, its data is permanently removed.
        Download anything you need first.
    </p>

    <div class="profile-form__actions">
        <button type="button" class="button profile-delete-btn" @click="open = true">
            Delete account
        </button>
    </div>

    <template x-teleport="body">
        <div
            class="confirm-backdrop"
            x-show="open"
            x-cloak
            x-transition:enter="confirm-fade-in"
            x-transition:enter-start="confirm-fade-start"
            x-transition:enter-end="confirm-fade-end"
            x-transition:leave="confirm-fade-out"
            x-transition:leave-start="confirm-fade-end"
            x-transition:leave-end="confirm-fade-start"
            @click.self="open = false"
            @keydown.escape.window="if (open) open = false"
            role="presentation"
        >
            <section class="confirm-modal" role="alertdialog" aria-modal="true">
                <button type="button" class="confirm-modal__close" @click="open = false" aria-label="Close">&times;</button>
                <p class="confirm-modal__eyebrow">DELETE ACCOUNT</p>
                <h3>Are you sure?</h3>
                <p>Enter your password to permanently delete this account.</p>

                <form method="post" action="{{ route('profile.destroy') }}" class="profile-delete-form">
                    @csrf
                    @method('delete')

                    <label>
                        Password
                        <input type="password" name="password" placeholder="Password" required>
                        @error('password', 'userDeletion')
                            <span class="master-error">{{ $message }}</span>
                        @enderror
                    </label>

                    <div class="confirm-modal__actions">
                        <button type="button" class="confirm-button confirm-button--cancel" @click="open = false">
                            Cancel
                        </button>
                        <button type="submit" class="confirm-button confirm-button--delete">
                            Delete account
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </template>
</div>
