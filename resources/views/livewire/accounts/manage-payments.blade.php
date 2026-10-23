<div
    class="master-page"
    x-data="{ formOpen: @js((bool) $editingId) }"
    @master-form-reset.window="formOpen = false"
>
    @if ($denied)
        <div class="panel empty-card">
            <h3>No access</h3>
            <p>You do not have permission to manage payments.</p>
        </div>
    @else
        @if ($canManage)
            <section class="panel master-form-panel master-form-panel--collapsible" :class="{ 'master-form-panel--open': formOpen }">
                @include('partials.master-form-toggle', [
                    'targetId' => 'payment-form-fields',
                    'eyebrow' => $editingId ? 'EDIT' : 'ADD',
                    'title' => $editingId ? 'Edit payment' : 'New payment',
                    'actionLabel' => 'Add payment',
                ])

                @if ($successMessage !== '')
                    <div
                        class="portal-toast portal-toast--{{ $toastType }}"
                        wire:key="payment-toast-{{ $toastVersion }}"
                        x-data="{ show: true }"
                        x-init="setTimeout(() => show = false, 5000)"
                        x-show="show"
                        role="status"
                    >
                        <span class="portal-toast__copy"><strong>{{ $successMessage }}</strong></span>
                    </div>
                @endif

                <div id="payment-form-fields" class="master-form-collapse" :class="{ 'master-form-collapse--open': formOpen }" :aria-hidden="(!formOpen).toString()" :inert="!formOpen">
                    <div class="master-form-collapse__inner"><div class="master-form-content">
                        <form class="master-form" wire:submit="savePayment">
                            <label>
                                Client
                                <select wire:model="client_id">
                                    <option value="">Select client</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->full_name }}</option>
                                    @endforeach
                                </select>
                                @error('client_id') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            <label>
                                Payment method
                                <select wire:model.live="payment_method_id">
                                    <option value="">Select method</option>
                                    @foreach ($methods as $method)
                                        <option value="{{ $method->id }}">{{ $method->name }}</option>
                                    @endforeach
                                </select>
                                @error('payment_method_id') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            <label>
                                Receiving account
                                <select wire:model="payment_account_id" @disabled($payment_method_id === '')>
                                    <option value="">
                                        {{ $payment_method_id === '' ? 'Select method first' : 'Select account' }}
                                    </option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->label() }}</option>
                                    @endforeach
                                </select>
                                @error('payment_account_id') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            <label>
                                Amount
                                <input type="number" wire:model="amount" min="0.01" step="0.01" placeholder="0.00">
                                @error('amount') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            <label>
                                Paid on
                                <input type="date" wire:model="paid_on">
                                @error('paid_on') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            <label>
                                Notes
                                <input type="text" wire:model="notes" maxlength="500" placeholder="Optional">
                                @error('notes') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            <label
                                class="master-file-field"
                                x-data="{ fileName: '' }"
                                @payment-form-reset.window="fileName = ''"
                            >
                                Screenshot (optional)
                                <span class="file-picker">
                                    <input
                                        type="file"
                                        class="file-picker__input"
                                        wire:model="screenshot"
                                        accept="image/jpeg,image/png,.jpg,.jpeg,.png"
                                        aria-label="Choose screenshot"
                                        @change="fileName = $event.target.files[0]?.name || ''"
                                    >
                                    <span class="file-picker__icon">
                                        @include('partials.icon', ['name' => 'upload'])
                                    </span>
                                    <span class="file-picker__text" x-text="fileName || 'Choose JPG or PNG'"></span>
                                </span>
                                @error('screenshot') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            <div class="master-form__actions">
                                <button type="submit" class="button button--soft" wire:loading.attr="disabled">
                                    {{ $editingId ? 'Save' : 'Add payment' }}
                                </button>
                                @if ($editingId)
                                    <button type="button" class="button button--ghost" wire:click="cancelEdit" @click="formOpen = false">
                                        Cancel
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div></div>
                </div>
            </section>
        @elseif ($successMessage !== '')
            <div
                class="portal-toast portal-toast--{{ $toastType }}"
                wire:key="payment-toast-{{ $toastVersion }}"
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
                    <p class="panel__eyebrow">ACCOUNTS</p>
                    <h3>Payments</h3>
                </div>
                <label class="master-search">
                    <span class="master-search__icon" aria-hidden="true">@include('partials.icon', ['name' => 'search'])</span>
                    <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search client or method..." aria-label="Search">
                </label>
            </div>

            <div class="table-wrap" wire:key="payments-list-{{ $toastVersion }}">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Method</th>
                            <th>Received in</th>
                            <th>Amount</th>
                            <th>Screenshot</th>
                            <th class="master-actions-heading">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                            @forelse ($payments as $payment)
                                <tr wire:key="payment-{{ $payment->id }}">
                                    <td>{{ $payment->paid_on->format('d M Y') }}</td>
                                    <td>{{ $payment->client?->full_name ?? '—' }}</td>
                                    <td>{{ $payment->method?->name ?? '—' }}</td>
                                    <td>{{ $payment->account?->label() ?? '—' }}</td>
                                    <td class="loan-amt--return">{{ number_format((float) $payment->amount, 2) }}</td>
                                    <td>
                                        @if ($payment->hasScreenshot())
                                            <a class="row-action row-action--edit" href="{{ route('accounts.payments.screenshot', $payment) }}" target="_blank" rel="noopener">
                                                <span>View</span>
                                            </a>
                                        @else
                                            <span class="muted">—</span>
                                        @endif
                                    </td>
                                    <td class="master-row-actions">
                                        <a class="row-action row-action--edit" href="{{ route('accounts.payments.receipt', $payment) }}" target="_blank" rel="noopener">
                                            <span>Receipt</span>
                                        </a>
                                        @if ($canManage)
                                            <button type="button" class="row-action row-action--edit" wire:click="editPayment({{ $payment->id }})" @click="formOpen = true">
                                                <span>Edit</span>
                                            </button>
                                            <button type="button" class="row-action row-action--delete" wire:click="deletePayment({{ $payment->id }})" wire:confirm="Delete this payment?">
                                                @include('partials.icon', ['name' => 'trash'])
                                                <span>Delete</span>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="muted">No payments yet.</td>
                                </tr>
                            @endforelse
                    </tbody>
                </table>
            </div>

            @if ($payments)
                {{ $payments->links() }}
            @endif
        </section>
    @endif
</div>
