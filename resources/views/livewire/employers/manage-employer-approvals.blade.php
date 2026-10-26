<div class="master-page" wire:poll.10s>
    @if ($denied)
        <div class="panel empty-card">
            <h3>No access</h3>
            <p>You do not have permission to approve employers.</p>
        </div>
    @else
        @if ($successMessage !== '')
            <div
                class="portal-toast portal-toast--{{ $toastType }}"
                wire:key="employer-approval-toast-{{ $toastVersion }}"
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 5000)"
                x-show="show"
                role="status"
            >
                <span class="portal-toast__copy"><strong>{{ $successMessage }}</strong></span>
            </div>
        @endif

        <section class="panel master-list-panel">
            <div class="panel__header">
                <div>
                    <p class="panel__eyebrow">EMPLOYERS</p>
                    <h3>Employer approvals</h3>
                </div>
                <div class="visitors-toolbar">
                    <select class="visitors-toolbar__select" wire:model.live="filter" aria-label="Status">
                        <option value="all">All</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                    <label class="master-search">
                        <span class="master-search__icon" aria-hidden="true">@include('partials.icon', ['name' => 'search'])</span>
                        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search company..." aria-label="Search">
                    </label>
                </div>
            </div>

            <div class="table-wrap">
                <table class="data-table employers-approvals-table">
                    <thead>
                        <tr>
                            <th>Company</th>
                            <th>Contact</th>
                            <th>Country</th>
                            <th>Status</th>
                            <th class="master-actions-heading">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($profiles as $profile)
                            <tr wire:key="employer-profile-{{ $profile->id }}">
                                <td class="employers-approvals-table__text">
                                    <strong>{{ $profile->company_name }}</strong>
                                    <div class="muted">{{ $profile->user?->email }}</div>
                                </td>
                                <td class="employers-approvals-table__text">
                                    {{ $profile->contact_person }}
                                    @if ($profile->phone)
                                        <div class="muted">{{ $profile->phone }}</div>
                                    @endif
                                </td>
                                <td>{{ $profile->country ?: '—' }}</td>
                                <td>
                                    @if ($profile->isPending())
                                        <span class="status status--interview">Pending</span>
                                    @elseif ($profile->isApproved())
                                        <span class="status status--ready">Approved</span>
                                    @else
                                        <span class="status status--interview">Rejected</span>
                                    @endif
                                </td>
                                <td class="master-row-actions">
                                    @if ($canManage && $profile->isPending())
                                        <button type="button" class="row-action row-action--edit" wire:click="approve({{ $profile->id }})">
                                            <span>Approve</span>
                                        </button>
                                        <button type="button" class="row-action row-action--delete" wire:click="startReject({{ $profile->id }})">
                                            <span>Reject</span>
                                        </button>
                                    @elseif ($profile->isRejected() && $profile->rejection_reason)
                                        <span class="muted employers-approvals-table__text">{{ $profile->rejection_reason }}</span>
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>
                            </tr>
                            @if ($rejectingId === $profile->id)
                                <tr wire:key="reject-employer-{{ $profile->id }}">
                                    <td colspan="5">
                                        <form class="master-form" wire:submit="reject" style="display:flex; gap:10px; align-items:end; flex-wrap:wrap;">
                                            <label style="flex:1; min-width:220px;">
                                                Rejection reason (optional)
                                                <input type="text" wire:model="rejectReason" maxlength="255">
                                            </label>
                                            <button type="submit" class="button button--soft">Confirm reject</button>
                                            <button type="button" class="button button--ghost" wire:click="cancelReject">Cancel</button>
                                        </form>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="5" class="muted">No employers in this filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($profiles)
                {{ $profiles->links() }}
            @endif
        </section>
    @endif
</div>
