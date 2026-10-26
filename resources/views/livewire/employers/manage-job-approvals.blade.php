<div class="master-page" wire:poll.10s>
    @if ($denied)
        <div class="panel empty-card">
            <h3>No access</h3>
            <p>You do not have permission to approve job posts.</p>
        </div>
    @else
        @if ($successMessage !== '')
            <div
                class="portal-toast portal-toast--{{ $toastType }}"
                wire:key="job-approval-toast-{{ $toastVersion }}"
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
                    <h3>Job approvals</h3>
                </div>
                <div class="visitors-toolbar">
                    <select class="visitors-toolbar__select" wire:model.live="filter" aria-label="Status">
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="closed">Closed</option>
                        <option value="all">All</option>
                    </select>
                    <label class="master-search">
                        <span class="master-search__icon" aria-hidden="true">@include('partials.icon', ['name' => 'search'])</span>
                        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search jobs..." aria-label="Search">
                    </label>
                </div>
            </div>

            <div class="table-wrap">
                <table class="data-table employers-approvals-table">
                    <thead>
                        <tr>
                            <th>Job</th>
                            <th>Company</th>
                            <th>Country</th>
                            <th>Status</th>
                            <th class="master-actions-heading">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jobs as $job)
                            <tr wire:key="job-approval-{{ $job->id }}">
                                <td class="employers-approvals-table__text">
                                    <strong>{{ $job->title }}</strong>
                                    <div class="muted">{{ $job->category ?: '—' }} · {{ $job->employment_type ?: '—' }}</div>
                                </td>
                                <td class="employers-approvals-table__text">{{ $job->employer?->company_name ?? '—' }}</td>
                                <td>{{ $job->country ?: '—' }}</td>
                                <td>
                                    @if ($job->isPending())
                                        <span class="status status--interview">Pending</span>
                                    @elseif ($job->isApproved())
                                        <span class="status status--ready">Approved</span>
                                    @elseif ($job->isClosed())
                                        <span class="status status--interview">Closed</span>
                                    @else
                                        <span class="status status--interview">Rejected</span>
                                    @endif
                                </td>
                                <td class="master-row-actions">
                                    @if ($canManage && $job->isPending())
                                        <button type="button" class="row-action row-action--edit" wire:click="approve({{ $job->id }})">
                                            <span>Approve</span>
                                        </button>
                                        <button type="button" class="row-action row-action--delete" wire:click="startReject({{ $job->id }})">
                                            <span>Reject</span>
                                        </button>
                                    @elseif ($job->isRejected() && $job->rejection_reason)
                                        <span class="muted employers-approvals-table__text">{{ $job->rejection_reason }}</span>
                                    @endif
                                    @if ($canManage)
                                        <button
                                            type="button"
                                            class="row-action row-action--delete"
                                            wire:click="delete({{ $job->id }})"
                                            wire:confirm="Delete this job permanently? It will be removed from the website."
                                        >
                                            <span>Delete</span>
                                        </button>
                                    @elseif (! $job->isPending() && ! ($job->isRejected() && $job->rejection_reason))
                                        <span class="muted">—</span>
                                    @endif
                                </td>
                            </tr>
                            @if ($rejectingId === $job->id)
                                <tr wire:key="reject-job-{{ $job->id }}">
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
                                <td colspan="5" class="muted">No jobs in this filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($jobs)
                {{ $jobs->links() }}
            @endif
        </section>
    @endif
</div>
