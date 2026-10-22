<div
    class="master-page"
    @if (! $denied)
        wire:poll.10s
    @endif
    @portal-feature-shown.window="
        if ($event.detail && $event.detail.module === 'attendance' && $event.detail.feature === 'leave-approvals') {
            $wire.$refresh();
        }
    "
>
    @if ($denied)
        <div class="panel empty-card">
            <h3>No access</h3>
            <p>You do not have permission to approve leave.</p>
        </div>
    @else
        @if ($successMessage !== '')
            <div
                class="portal-toast portal-toast--{{ $toastType }}"
                wire:key="leave-approval-toast-{{ $toastVersion }}"
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

        <section class="panel master-list-panel">
            <div class="panel__header">
                <div>
                    <p class="panel__eyebrow">HR</p>
                    <h3>Leave approvals</h3>
                </div>

                <div class="visitors-toolbar">
                    <select class="visitors-toolbar__select" wire:model.live="statusFilter" aria-label="Status filter">
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="">All</option>
                    </select>

                    <label class="master-search">
                        <span class="master-search__icon" aria-hidden="true">
                            @include('partials.icon', ['name' => 'search'])
                        </span>
                        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search staff / reason..." aria-label="Search leave">
                    </label>
                </div>
            </div>

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Staff</th>
                            <th>Dates</th>
                            <th>Type</th>
                            <th>Days</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th class="master-actions-heading">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($leaves as $leave)
                            <tr wire:key="leave-approval-{{ $leave->id }}">
                                <td>{{ $leave->user?->name ?? '—' }}</td>
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
                                <td>{{ $leave->reason ?: '—' }}</td>
                                <td>
                                    @if ($leave->status === 'approved')
                                        <span class="status status--ready">
                                            {{ $leave->statusLabel() }}
                                            {{ $leave->is_paid ? '(paid)' : '(unpaid)' }}
                                        </span>
                                    @elseif ($leave->status === 'rejected')
                                        <span class="status status--screening">{{ $leave->statusLabel() }}</span>
                                    @elseif ($leave->status === 'cancelled')
                                        <span class="muted">{{ $leave->statusLabel() }}</span>
                                    @else
                                        <span class="status status--interview">{{ $leave->statusLabel() }}</span>
                                    @endif
                                </td>
                                <td class="master-row-actions">
                                    @if ($leave->status === 'pending')
                                        <button type="button" class="row-action row-action--edit" wire:click="approve({{ $leave->id }}, true)" title="Approve as paid leave">
                                            @include('partials.icon', ['name' => 'check'])
                                            <span>Approve paid</span>
                                        </button>
                                        <button type="button" class="row-action row-action--edit" wire:click="approve({{ $leave->id }}, false)" title="Approve as unpaid (salary deduction later)">
                                            <span>Approve unpaid</span>
                                        </button>
                                        <button type="button" class="row-action row-action--delete" wire:click="reject({{ $leave->id }})" wire:confirm="Reject this leave request?">
                                            <span>Reject</span>
                                        </button>
                                    @else
                                        <span class="muted">{{ $leave->approver?->name ? 'By '.$leave->approver->name : '—' }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="muted">No leave requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($leaves)
                {{ $leaves->links() }}
            @endif
        </section>
    @endif
</div>
