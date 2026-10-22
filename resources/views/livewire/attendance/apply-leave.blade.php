<div
    class="master-page"
    @if (! $denied)
        wire:poll.10s
    @endif
    @portal-feature-shown.window="
        if ($event.detail && $event.detail.module === 'attendance' && $event.detail.feature === 'apply-leave') {
            $wire.$refresh();
        }
    "
>
    @if ($denied)
        <div class="panel empty-card">
            <h3>No access</h3>
            <p>You do not have permission to apply for leave.</p>
        </div>
    @else
        <section class="panel master-form-panel">
            <div class="panel__header">
                <div>
                    <p class="panel__eyebrow">HR</p>
                    <h3>Apply leave</h3>
                </div>
            </div>

            @if ($successMessage !== '')
                <div
                    class="portal-toast portal-toast--{{ $toastType }}"
                    wire:key="apply-leave-toast-{{ $toastVersion }}"
                    x-data="{ show: true }"
                    x-init="setTimeout(() => show = false, 5000)"
                    x-show="show"
                    role="status"
                >
                    <span class="portal-toast__copy">
                        <strong>{{ $successMessage }}</strong>
                    </span>
                </div>
            @endif

            <div class="master-form-content">
                <form class="master-form" wire:submit="saveLeave">
                    <label>
                        Leave type
                        <select wire:model.live="leave_type">
                            <option value="full">Full day (one or more days)</option>
                            <option value="half">Half day (1 day only)</option>
                        </select>
                        @error('leave_type') <span class="master-error">{{ $message }}</span> @enderror
                    </label>

                    @if ($leave_type === 'half')
                        <label>
                            Date
                            <input type="date" wire:model.live="from_date">
                            <small class="muted">Half day = one day only (today or any single date)</small>
                            @error('from_date') <span class="master-error">{{ $message }}</span> @enderror
                        </label>
                    @else
                        <label>
                            From date
                            <input type="date" wire:model.live="from_date">
                            @error('from_date') <span class="master-error">{{ $message }}</span> @enderror
                        </label>

                        <label>
                            To date
                            <input type="date" wire:model="to_date">
                            <small class="muted">Same day or later — use for multi-day leave</small>
                            @error('to_date') <span class="master-error">{{ $message }}</span> @enderror
                        </label>
                    @endif

                    <label>
                        Reason
                        <textarea class="visitor-textarea" wire:model="reason" rows="3" placeholder="Optional" maxlength="255"></textarea>
                        @error('reason') <span class="master-error">{{ $message }}</span> @enderror
                    </label>

                    <div class="master-form__actions">
                        <button type="submit" class="button button--soft">Submit leave</button>
                    </div>
                </form>
            </div>
        </section>

        <section class="panel master-list-panel">
            <div class="panel__header">
                <div>
                    <p class="panel__eyebrow">MY REQUESTS</p>
                    <h3>Leave history</h3>
                </div>
            </div>

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Dates</th>
                            <th>Type</th>
                            <th>Days</th>
                            <th>Status</th>
                            <th>Reason</th>
                            <th class="master-actions-heading">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($myLeaves as $leave)
                            <tr wire:key="my-leave-{{ $leave->id }}">
                                <td>
                                    {{ $leave->from_date->format('d M Y') }}
                                    @if (! $leave->from_date->equalTo($leave->to_date))
                                        – {{ $leave->to_date->format('d M Y') }}
                                    @endif
                                </td>
                                <td>{{ $leave->leaveTypeLabel() }}</td>
                                <td>
                                    @if ($leave->leave_type === 'half')
                                        {{ $leave->half_days }} half
                                    @else
                                        {{ $leave->full_days }} full
                                    @endif
                                </td>
                                <td>
                                    @if ($leave->status === 'approved')
                                        <span class="status status--ready">
                                            {{ $leave->statusLabel() }}
                                            ({{ $leave->is_paid ? 'paid' : 'unpaid' }})
                                        </span>
                                    @elseif ($leave->status === 'rejected')
                                        <span class="status status--screening">{{ $leave->statusLabel() }}</span>
                                    @elseif ($leave->status === 'cancelled')
                                        <span class="muted">{{ $leave->statusLabel() }}</span>
                                    @else
                                        <span class="status status--interview">{{ $leave->statusLabel() }}</span>
                                    @endif
                                </td>
                                <td>{{ $leave->reason ?: '—' }}</td>
                                <td class="master-row-actions">
                                    @if ($leave->status === 'pending')
                                        <button type="button" class="row-action row-action--delete" wire:click="cancelLeave({{ $leave->id }})" wire:confirm="Cancel this leave request?">
                                            <span>Cancel</span>
                                        </button>
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="muted">No leave requests yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($myLeaves)
                {{ $myLeaves->links() }}
            @endif
        </section>
    @endif
</div>
