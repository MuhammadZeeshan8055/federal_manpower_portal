<div class="master-page">
    @if ($denied)
        <div class="panel empty-card">
            <h3>No access</h3>
            <p>You do not have permission to view your loans.</p>
        </div>
    @else
        <section class="panel master-list-panel">
            <div class="panel__header">
                <div>
                    <p class="panel__eyebrow">MY LOANS</p>
                    <h3>Loan balance</h3>
                </div>
            </div>

            <div class="loan-details-summary">
                <div>
                    <small>Total taken</small>
                    <strong class="loan-amt--taken">{{ number_format($totalTaken, 2) }}</strong>
                </div>
                <div>
                    <small>Total returned</small>
                    <strong class="loan-amt--return">{{ number_format($totalReturned, 2) }}</strong>
                </div>
                <div>
                    <small>Remaining</small>
                    <strong>{{ number_format($remaining, 2) }}</strong>
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
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($loans as $loan)
                                    <tr wire:key="my-loan-{{ $loan->id }}">
                                        <td>{{ $loan->issued_on->format('d M Y') }}</td>
                                        <td class="loan-amt--taken">{{ number_format((float) $loan->principal, 2) }}</td>
                                        <td>
                                            @if ($loan->status === 'closed')
                                                <span class="status status--ready">Closed</span>
                                            @else
                                                <span class="status status--interview">Open</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="muted">No loans taken yet.</td>
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
                                    <tr wire:key="my-return-{{ $payment->id }}">
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
        </section>
    @endif
</div>
