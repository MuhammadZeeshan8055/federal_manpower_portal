@php
    /** @var \Illuminate\Support\Collection|\App\Models\Payment[] $payments */
    $payments = $payments ?? collect();
@endphp

<div class="table-wrap">
    <table class="data-table bio-doc-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Method</th>
                <th>Received in</th>
                <th>Amount</th>
                <th>Notes</th>
                <th>Screenshot</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payments as $payment)
                <tr wire:key="client-payment-{{ $payment->id }}">
                    <td>{{ $payment->paid_on?->format('d M Y') ?: '—' }}</td>
                    <td>{{ $payment->method?->name ?: '—' }}</td>
                    <td>{{ $payment->account?->label() ?: '—' }}</td>
                    <td class="loan-amt--return">{{ number_format((float) $payment->amount, 2) }}</td>
                    <td>{{ $payment->notes ?: '—' }}</td>
                    <td>
                        @if ($payment->hasScreenshot())
                            <a
                                class="bio-payment-shot"
                                href="{{ route('accounts.payments.screenshot', $payment) }}"
                                target="_blank"
                                rel="noopener"
                                title="Open screenshot"
                            >
                                <img
                                    src="{{ route('accounts.payments.screenshot', $payment) }}"
                                    alt="Payment screenshot"
                                    loading="lazy"
                                >
                            </a>
                        @else
                            <span class="muted">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="muted">No payments recorded.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
