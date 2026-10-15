<section class="workday-card workday-card--small">
    <p class="workday-card__eyebrow">Current workday</p>
    <h3 class="workday-card__date">{{ $todayDateLabel }}</h3>

    <div class="workday-card__rows">
        <div class="workday-card__row">
            <span>Check-in</span>
            <strong>{{ $checkInLabel }}</strong>
        </div>
        <div class="workday-card__row">
            <span>Check-out</span>
            <strong>{{ $checkOutLabel }}</strong>
        </div>
        <div class="workday-card__row">
            <span>Worked</span>
            <strong>{{ $workedLabel }}</strong>
        </div>
    </div>

    @if ($message !== '')
        <p class="workday-card__msg workday-card__msg--{{ $messageType }}">{{ $message }}</p>
    @endif

    @if ($isExempt)
        <div class="workday-card__badge">Exempt (WFH)</div>
    @elseif (! $canPunch)
        <div class="workday-card__badge">No check-in needed</div>
    @elseif ($shiftOpen)
        <div
            x-data="{ confirmOpen: false }"
            @keydown.escape.window="if (confirmOpen) confirmOpen = false"
        >
            <button type="button" class="workday-card__btn" @click="confirmOpen = true">
                End Shift
            </button>

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
                    <section class="confirm-modal" role="alertdialog" aria-modal="true">
                        <button type="button" class="confirm-modal__close" @click="confirmOpen = false" aria-label="Close dialog">&times;</button>
                        <p class="confirm-modal__eyebrow">END SHIFT</p>
                        <h3>End your shift?</h3>
                        <p>You checked in at <strong>{{ $checkInLabel }}</strong>. This will record check-out for today.</p>
                        <div class="confirm-modal__actions">
                            <button type="button" class="confirm-button confirm-button--cancel" @click="confirmOpen = false">Cancel</button>
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
    @elseif ($shiftDone)
        <div class="workday-card__badge workday-card__badge--done">Shift complete</div>
    @else
        <a href="{{ route('attendance.start') }}" class="workday-card__btn">Start Shift</a>
    @endif
</section>
