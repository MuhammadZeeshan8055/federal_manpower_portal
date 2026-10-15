<div class="master-page">
    @if ($denied)
        <div class="panel empty-card">
            <h3>No access</h3>
            <p>You do not have permission to manage office settings.</p>
        </div>
    @else
        <section class="panel master-form-panel">
            <div class="panel__header">
                <div>
                    <p class="panel__eyebrow">HR</p>
                    <h3>Office settings</h3>
                </div>
            </div>

            @if ($successMessage !== '')
                <div
                    class="portal-toast portal-toast--{{ $toastType }}"
                    wire:key="office-settings-toast-{{ $toastVersion }}"
                    x-data="{ show: true }"
                    x-init="setTimeout(() => show = false, 5000)"
                    x-show="show"
                    x-transition:enter="toast-enter"
                    x-transition:enter-start="toast-enter-start"
                    x-transition:enter-end="toast-enter-end"
                    x-transition:leave="toast-leave"
                    x-transition:leave-start="toast-leave-start"
                    x-transition:leave-end="toast-leave-end"
                    role="status"
                    aria-live="polite"
                    aria-atomic="true"
                >
                    <span class="portal-toast__icon">
                        @include('partials.icon', ['name' => 'check'])
                    </span>
                    <span class="portal-toast__copy">
                        <small>Success</small>
                        <strong>{{ $successMessage }}</strong>
                    </span>
                    <button type="button" class="portal-toast__close" @click="show = false" aria-label="Dismiss notification">
                        &times;
                    </button>
                    <span class="portal-toast__progress" aria-hidden="true"></span>
                </div>
            @endif

            <div class="master-form-content">
                <form class="master-form" wire:submit="saveSettings">
                    <p class="muted">Office hours</p>

                    <label>
                        Office start
                        <input type="time" wire:model="office_start">
                        @error('office_start') <span class="master-error">{{ $message }}</span> @enderror
                    </label>

                    <label>
                        Office end
                        <input type="time" wire:model="office_end">
                        @error('office_end') <span class="master-error">{{ $message }}</span> @enderror
                    </label>

                    <label>
                        Required hours
                        <input type="number" wire:model="required_hours" min="1" max="24" step="1">
                        @error('required_hours') <span class="master-error">{{ $message }}</span> @enderror
                    </label>

                    <label>
                        Grace minutes (late / early)
                        <input type="number" wire:model="grace_minutes" min="0" max="120" step="1">
                        @error('grace_minutes') <span class="master-error">{{ $message }}</span> @enderror
                    </label>

                    <p class="muted">Lunch / break</p>

                    <label>
                        Lunch start
                        <input type="time" wire:model="lunch_start">
                        @error('lunch_start') <span class="master-error">{{ $message }}</span> @enderror
                    </label>

                    <label>
                        Lunch end
                        <input type="time" wire:model="lunch_end">
                        @error('lunch_end') <span class="master-error">{{ $message }}</span> @enderror
                    </label>

                    <label>
                        Break minutes
                        <input type="number" wire:model="break_minutes" min="0" max="240" step="1">
                        @error('break_minutes') <span class="master-error">{{ $message }}</span> @enderror
                    </label>

                    <p class="muted">Office IPs (leave empty to allow any IP while setting up)</p>

                    <label>
                        Office IP 1
                        <input type="text" wire:model="office_ip_1" placeholder="e.g. 192.168.1.10" maxlength="45">
                        @error('office_ip_1') <span class="master-error">{{ $message }}</span> @enderror
                    </label>

                    <label>
                        Office IP 2
                        <input type="text" wire:model="office_ip_2" maxlength="45">
                        @error('office_ip_2') <span class="master-error">{{ $message }}</span> @enderror
                    </label>

                    <label>
                        Office IP 3
                        <input type="text" wire:model="office_ip_3" maxlength="45">
                        @error('office_ip_3') <span class="master-error">{{ $message }}</span> @enderror
                    </label>

                    <p class="muted">Monthly leave allowance (used later for Apply Leave)</p>

                    <label>
                        Full days per month
                        <input type="number" wire:model="monthly_full_days" min="0" max="31" step="1">
                        @error('monthly_full_days') <span class="master-error">{{ $message }}</span> @enderror
                    </label>

                    <label>
                        Half days per month
                        <input type="number" wire:model="monthly_half_days" min="0" max="31" step="1">
                        @error('monthly_half_days') <span class="master-error">{{ $message }}</span> @enderror
                    </label>

                    <p class="muted">Attendance exempt (WFH) — these users skip the check-in gate</p>

                    <div class="office-exempt-list">
                        @forelse ($staffUsers as $staff)
                            <label class="master-check" wire:key="exempt-{{ $staff->id }}">
                                <input type="checkbox" value="{{ $staff->id }}" wire:model="exemptUserIds">
                                <span>{{ $staff->name }} <small class="muted">({{ $staff->roleLabel() }})</small></span>
                            </label>
                        @empty
                            <p class="muted">No office users found.</p>
                        @endforelse
                    </div>
                    @error('exemptUserIds') <span class="master-error">{{ $message }}</span> @enderror
                    @error('exemptUserIds.*') <span class="master-error">{{ $message }}</span> @enderror

                    <div class="master-form__actions">
                        <button type="submit" class="button button--soft" wire:loading.attr="disabled">
                            Save settings
                        </button>
                    </div>
                </form>
            </div>
        </section>
    @endif
</div>
