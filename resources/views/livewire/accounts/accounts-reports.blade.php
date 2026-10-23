<div class="master-page">
    @if ($denied)
        <div class="panel empty-card">
            <h3>No access</h3>
            <p>You do not have permission to view accounts reports.</p>
        </div>
    @else
        <section class="panel master-list-panel">
            <div class="panel__header">
                <div>
                    <p class="panel__eyebrow">ACCOUNTS</p>
                    <h3>Reports</h3>
                </div>
            </div>

            <form class="master-form accounts-report-filters" wire:submit.prevent>
                <label>
                    From
                    <input type="date" wire:model.live="date_from">
                </label>
                <label>
                    To
                    <input type="date" wire:model.live="date_to">
                </label>
                <label>
                    Method
                    <select wire:model.live="payment_method_id">
                        <option value="">All methods</option>
                        @foreach ($methods as $method)
                            <option value="{{ $method->id }}">{{ $method->name }}</option>
                        @endforeach
                    </select>
                </label>
            </form>

            <div class="loan-details-summary">
                <div>
                    <small>Payments</small>
                    <strong>{{ $paymentCount }}</strong>
                </div>
                <div>
                    <small>Total received</small>
                    <strong class="loan-amt--return">{{ number_format($totalAmount, 2) }}</strong>
                </div>
            </div>

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Method</th>
                            <th>Receiving account</th>
                            <th>Count</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr wire:key="report-{{ $row['method'] }}-{{ $row['account'] }}">
                                <td>{{ $row['method'] }}</td>
                                <td>{{ $row['account'] }}</td>
                                <td>{{ $row['count'] }}</td>
                                <td class="loan-amt--return">{{ number_format($row['total'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="muted">No payments in this range.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif
</div>
