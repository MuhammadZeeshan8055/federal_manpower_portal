<div
    class="end-shift-wrap"
    x-data="{ confirmOpen: false }"
    @keydown.escape.window="if (confirmOpen) confirmOpen = false"
>
    @if ($onLeave)
        <span class="end-shift-on-leave" title="Approved leave today">On leave</span>
    @endif

    @if ($showTimes)
        <div class="end-shift-times" title="Today's shift times">
            <span class="end-shift-times__slot end-shift-times__slot--in">
                <small>In</small>
                <strong>{{ $checkInLabel }}</strong>
            </span>
            <span class="end-shift-times__sep" aria-hidden="true"></span>
            <span class="end-shift-times__slot end-shift-times__slot--out">
                <small>Out</small>
                <strong>{{ $checkOutLabel }}</strong>
            </span>
        </div>
    @endif

    @if ($showButton)
        <button
            type="button"
            class="button end-shift-btn"
            @click="confirmOpen = true"
            title="End shift for today"
        >
            End Shift
        </button>
    @endif

    @if ($message !== '')
        <span
            class="end-shift-msg end-shift-msg--{{ $messageType }}"
            wire:key="end-shift-msg-{{ md5($message) }}"
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 5000)"
            x-show="show"
            role="status"
        >
            {{ $message }}
        </span>
    @endif

    <template x-teleport="body">
        <div
            class="confirm-backdrop"
            x-show="confirmOpen"
            x-cloak
            x-transition:enter="confirm-fade-in"
            x-transition:enter-start="confirm-fade-start"
            x-transition:enter-end="confirm-fade-end"
            x-transition:leave="confirm-fade-out"
            x-transition:leave-start="confirm-fade-end"
            x-transition:leave-end="confirm-fade-start"
            @click.self="confirmOpen = false"
            role="presentation"
        >
            <section
                class="confirm-modal"
                role="alertdialog"
                aria-modal="true"
                aria-labelledby="end-shift-title"
                aria-describedby="end-shift-description"
            >
                <button type="button" class="confirm-modal__close" @click="confirmOpen = false" aria-label="Close dialog">
                    &times;
                </button>

                <p class="confirm-modal__eyebrow">END SHIFT</p>
                <h3 id="end-shift-title">End your shift?</h3>
                <p id="end-shift-description">
                    You checked in at <strong>{{ $checkInLabel }}</strong>.
                    This will record your check-out time for today.
                </p>

                <div class="confirm-modal__actions">
                    <button type="button" class="confirm-button confirm-button--cancel" @click="confirmOpen = false">
                        Cancel
                    </button>
                    <button
                        type="button"
                        class="button button--soft"
                        @click="confirmOpen = false; $wire.endShift()"
                        wire:loading.attr="disabled"
                        wire:target="endShift"
                    >
                        Yes, end shift
                    </button>
                </div>
            </section>
        </div>
    </template>
</div>
