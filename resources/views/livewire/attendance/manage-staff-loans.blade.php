<div
    class="master-page"
    x-data="{
        addOpen: false,
        returnOpen: false
    }"
    @loan-form-reset.window="addOpen = false"
    @return-form-reset.window="returnOpen = false"
>
    @if ($denied)
        <div class="panel empty-card">
            <h3>No access</h3>
            <p>You do not have permission to manage staff loans.</p>
        </div>
    @else
        @if ($successMessage !== '')
            <div
                class="portal-toast portal-toast--{{ $toastType }}"
                wire:key="loan-toast-{{ $toastVersion }}"
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 5000)"
                x-show="show"
                role="status"
            >
                <span class="portal-toast__copy"><strong>{{ $successMessage }}</strong></span>
            </div>
        @endif

        @if ($viewMode === 'summary')
            <div class="loan-page-summary" aria-label="Loan totals">
                <div>
                    <small>Total taken</small>
                    <strong class="loan-amt--taken">{{ number_format($collectiveTotalTaken, 2) }}</strong>
                </div>
                <div>
                    <small>Total returned</small>
                    <strong class="loan-amt--return">{{ number_format($collectiveTotalReturned, 2) }}</strong>
                </div>
                <div>
                    <small>Remaining</small>
                    <strong>{{ number_format($collectiveRemaining, 2) }}</strong>
                </div>
            </div>
        @endif

        <div class="loan-forms-row">
            <section class="panel master-form-panel master-form-panel--collapsible loan-forms-row__col" :class="{ 'master-form-panel--open': addOpen }">
                @include('partials.master-form-toggle', [
                    'targetId' => 'loan-add-form-fields',
                    'eyebrow' => 'ADD',
                    'title' => 'Add loan',
                    'actionLabel' => 'Add loan',
                    'openVar' => 'addOpen',
                ])

                <div id="loan-add-form-fields" class="master-form-collapse" :class="{ 'master-form-collapse--open': addOpen }" :aria-hidden="(!addOpen).toString()" :inert="!addOpen">
                    <div class="master-form-collapse__inner"><div class="master-form-content">
                        <form class="master-form" wire:submit="saveLoan">
                            <label>
                                Staff
                                <select wire:model="user_id">
                                    <option value="">Select staff</option>
                                    @foreach ($staffUsers as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                    @endforeach
                                </select>
                                @error('user_id') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            <label>
                                Loan amount
                                <input type="number" wire:model="principal" min="1" step="0.01" placeholder="0.00">
                                @error('principal') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            <label>
                                Issued on
                                <input type="date" wire:model="issued_on">
                                @error('issued_on') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            <label>
                                Notes
                                <textarea class="visitor-textarea" wire:model="notes" rows="2" maxlength="500" placeholder="Optional"></textarea>
                                @error('notes') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            <div class="master-form__actions">
                                <button type="submit" class="button button--soft">Add loan</button>
                            </div>
                        </form>
                    </div></div>
                </div>
            </section>

            <section class="panel master-form-panel master-form-panel--collapsible loan-forms-row__col" :class="{ 'master-form-panel--open': returnOpen }">
                @include('partials.master-form-toggle', [
                    'targetId' => 'loan-return-form-fields',
                    'eyebrow' => 'RETURN',
                    'title' => 'Return loan',
                    'actionLabel' => 'Return loan',
                    'openVar' => 'returnOpen',
                ])

                <div id="loan-return-form-fields" class="master-form-collapse" :class="{ 'master-form-collapse--open': returnOpen }" :aria-hidden="(!returnOpen).toString()" :inert="!returnOpen">
                    <div class="master-form-collapse__inner"><div class="master-form-content">
                        <form class="master-form" wire:submit="saveReturn">
                            <label>
                                Staff
                                <select wire:model.live="return_user_id">
                                    <option value="">Select staff</option>
                                    @foreach ($staffWithPending as $row)
                                        <option value="{{ $row['user_id'] }}">
                                            {{ $row['name'] }} — pending {{ number_format($row['remaining'], 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('return_user_id') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            @if ($return_user_id !== '')
                                <p class="muted">Pending balance: <strong>{{ number_format($returnPending, 2) }}</strong></p>
                            @endif

                            <label>
                                Amount returned
                                <input type="number" wire:model="return_amount" min="0.01" step="0.01" placeholder="0.00">
                                @error('return_amount') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            <label>
                                Paid on
                                <input type="date" wire:model="return_paid_on">
                                @error('return_paid_on') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            <label>
                                Notes
                                <input type="text" wire:model="return_notes" maxlength="255" placeholder="Optional">
                                @error('return_notes') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            <div class="master-form__actions">
                                <button type="submit" class="button button--soft">Save return</button>
                            </div>
                        </form>
                    </div></div>
                </div>
            </section>
        </div>

        {{-- List --}}
        <section class="panel master-list-panel">
            <div class="panel__header">
                <div>
                    <p class="panel__eyebrow">HR</p>
                    <h3>
                        @if ($viewMode === 'details')
                            Loan details — {{ $detailsStaffName ?: 'Staff' }}
                        @else
                            Staff loan balances
                        @endif
                    </h3>
                </div>

                <div class="visitors-toolbar">
                    @if ($viewMode === 'details')
                        <button type="button" class="button button--ghost" wire:click="showSummary">
                            Back to balances
                        </button>
                    @else
                        <select class="visitors-toolbar__select" wire:model.live="filterStaffId" aria-label="Filter by staff">
                            <option value="">All staff</option>
                            @foreach ($filterStaffOptions as $staff)
                                <option value="{{ $staff['id'] }}">{{ $staff['name'] }}</option>
                            @endforeach
                        </select>

                        <select class="visitors-toolbar__select" wire:model.live="filterBalance" aria-label="Filter by balance">
                            <option value="">All balances</option>
                            <option value="pending">Pending remaining</option>
                            <option value="cleared">Cleared</option>
                        </select>

                        @if ($filterStaffId !== '' || $filterBalance !== '' || trim($search) !== '')
                            <button type="button" class="text-button" wire:click="clearFilters">Clear filters</button>
                        @endif
                    @endif

                    <label class="master-search">
                        <span class="master-search__icon" aria-hidden="true">@include('partials.icon', ['name' => 'search'])</span>
                        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search staff..." aria-label="Search">
                    </label>
                </div>
            </div>

            @if ($viewMode === 'summary')
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Staff</th>
                                <th>Times taken</th>
                                <th>Total given</th>
                                <th>Total returned</th>
                                <th>Remaining</th>
                                <th class="master-actions-heading">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($summaryRows as $row)
                                <tr wire:key="loan-summary-{{ $row['user_id'] }}">
                                    <td>{{ $row['name'] }}</td>
                                    <td>{{ $row['times_taken'] }}</td>
                                    <td class="loan-amt--taken">{{ number_format($row['total_taken'], 2) }}</td>
                                    <td class="loan-amt--return">{{ number_format($row['total_paid'], 2) }}</td>
                                    <td>
                                        @if ($row['remaining'] > 0)
                                            <strong>{{ number_format($row['remaining'], 2) }}</strong>
                                        @else
                                            <span class="status status--ready">0.00</span>
                                        @endif
                                    </td>
                                    <td class="master-row-actions">
                                        <button type="button" class="row-action row-action--edit" wire:click="viewDetails({{ $row['user_id'] }})">
                                            <span>View details</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="muted">No staff loans yet. Use Add loan above.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div class="loan-details-summary">
                    <div>
                        <small>Total taken</small>
                        <strong class="loan-amt--taken">{{ number_format($detailsTotalTaken, 2) }}</strong>
                    </div>
                    <div>
                        <small>Total returned</small>
                        <strong class="loan-amt--return">{{ number_format($detailsTotalReturned, 2) }}</strong>
                    </div>
                    <div>
                        <small>Remaining</small>
                        <strong>{{ number_format($detailsRemaining, 2) }}</strong>
                    </div>
                </div>

                <div class="loan-details-cols">
                    <div class="loan-details-cols__col">
                        <h4 class="loan-details-cols__title">Loan taken</h4>
                        <div class="table-wrap">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th class="master-actions-heading">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($loans as $loan)
                                        <tr wire:key="loan-detail-{{ $loan->id }}">
                                            <td>{{ $loan->issued_on->format('d M Y') }}</td>
                                            <td class="loan-amt--taken">{{ number_format((float) $loan->principal, 2) }}</td>
                                            <td>
                                                @if ($loan->status === 'closed')
                                                    <span class="status status--ready">Closed</span>
                                                @else
                                                    <span class="status status--interview">Open</span>
                                                @endif
                                            </td>
                                            <td class="master-row-actions">
                                                <button type="button" class="row-action row-action--delete" wire:click="deleteLoan({{ $loan->id }})" wire:confirm="Delete this loan and its payments?">
                                                    @include('partials.icon', ['name' => 'trash'])
                                                    <span>Delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="muted">No loans taken.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($loans)
                            {{ $loans->links() }}
                        @endif
                    </div>

                    <div class="loan-details-cols__col">
                        <h4 class="loan-details-cols__title">Loan return</h4>
                        <div class="table-wrap">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($returns as $payment)
                                        <tr wire:key="loan-return-{{ $payment->id }}">
                                            <td>{{ $payment->paid_on->format('d M Y') }}</td>
                                            <td class="loan-amt--return">{{ number_format((float) $payment->amount, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="muted">No returns yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </section>
    @endif
</div>
