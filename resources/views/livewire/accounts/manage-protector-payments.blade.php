<div
    class="master-page"
    x-data="{
        formOpen: @js((bool) $editingId),
        deleteOpen: false,
        deleteId: null,
        deleteName: '',
        deleteTrigger: null,
        openDelete(id, name, trigger) {
            this.deleteId = id;
            this.deleteName = name;
            this.deleteTrigger = trigger;
            this.deleteOpen = true;
            this.$nextTick(() => this.$refs.deleteCancel.focus());
        },
        closeDelete() {
            this.deleteOpen = false;
            this.$nextTick(() => this.deleteTrigger?.focus());
        }
    }"
    @keydown.escape.window="if (deleteOpen) closeDelete()"
    @master-form-reset.window="formOpen = false"
>
    @if ($denied)
        <div class="panel empty-card">
            <h3>No access</h3>
            <p>You do not have permission to view protector payments.</p>
        </div>
    @else
        @if ($canManage)
            <section class="panel master-form-panel master-form-panel--collapsible" :class="{ 'master-form-panel--open': formOpen }">
                @include('partials.master-form-toggle', [
                    'targetId' => 'protector-payment-form-fields',
                    'eyebrow' => $editingId ? 'EDIT' : 'ADD',
                    'title' => $editingId ? 'Edit protector payment' : 'New protector payment',
                    'actionLabel' => 'Add payment',
                ])

                @if ($successMessage !== '')
                    <div
                        class="portal-toast portal-toast--{{ $toastType }}"
                        wire:key="protector-payment-toast-{{ $toastVersion }}"
                        x-data="{ show: true }"
                        x-init="setTimeout(() => show = false, 5000)"
                        x-show="show"
                        role="status"
                    >
                        <span class="portal-toast__copy"><strong>{{ $successMessage }}</strong></span>
                    </div>
                @endif

                <div id="protector-payment-form-fields" class="master-form-collapse" :class="{ 'master-form-collapse--open': formOpen }" :aria-hidden="(!formOpen).toString()" :inert="!formOpen">
                    <div class="master-form-collapse__inner"><div class="master-form-content">
                        <form class="master-form" wire:submit="savePayment">
                            <label>
                                Client
                                @include('partials.searchable-select', [
                                    'options' => $clients->map(fn ($client) => ['id' => $client->id, 'name' => $client->full_name]),
                                    'wireModel' => 'client_id',
                                    'selectedId' => $client_id,
                                    'placeholder' => 'Select client',
                                    'searchPlaceholder' => 'Search client...',
                                    'emptyText' => 'No client found',
                                ])
                                @error('client_id') <span class="master-error">{{ $message }}</span> @enderror
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
                                Description
                                <input type="text" wire:model="description" maxlength="500" placeholder="Optional">
                                @error('description') <span class="master-error">{{ $message }}</span> @enderror
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
        @endif

        <section class="panel master-list-panel">
            <div class="panel__header">
                <div>
                    <p class="panel__eyebrow">ACCOUNTS</p>
                    <h3>Protector payments</h3>
                </div>
                <label class="master-search">
                    <span class="master-search__icon" aria-hidden="true">@include('partials.icon', ['name' => 'search'])</span>
                    <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search client or description..." aria-label="Search">
                </label>
            </div>

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Amount</th>
                            <th>Description</th>
                            @if ($canManage)
                                <th class="master-actions-heading">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            <tr wire:key="protector-payment-{{ $payment->id }}">
                                <td>{{ $payment->paid_on->format('d M Y') }}</td>
                                <td>{{ $payment->client?->full_name ?? '—' }}</td>
                                <td class="loan-amt--return">{{ number_format((float) $payment->amount, 2) }}</td>
                                <td>{{ $payment->description ?: '—' }}</td>
                                @if ($canManage)
                                    <td class="master-row-actions">
                                        <button type="button" class="row-action row-action--edit" wire:click="editPayment({{ $payment->id }})" @click="formOpen = true">
                                            @include('partials.icon', ['name' => 'pencil'])
                                            <span>Edit</span>
                                        </button>
                                        <button
                                            type="button"
                                            class="row-action row-action--delete"
                                            @click="openDelete({{ $payment->id }}, @js(($payment->client?->full_name ?? 'payment').' · '.number_format((float) $payment->amount, 2)), $el)"
                                        >
                                            @include('partials.icon', ['name' => 'trash'])
                                            <span>Delete</span>
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $canManage ? 5 : 4 }}" class="muted">No protector payments yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($payments)
                {{ $payments->links() }}
            @endif
        </section>

        @if ($canManage)
            <template x-teleport="body">
                <div
                    class="confirm-backdrop"
                    x-show="deleteOpen"
                    x-cloak
                    @click.self="closeDelete()"
                    role="presentation"
                >
                    <section class="confirm-modal" role="alertdialog" aria-modal="true">
                        <button type="button" class="confirm-modal__close" @click="closeDelete()" aria-label="Close dialog">&times;</button>
                        <p class="confirm-modal__eyebrow">DELETE</p>
                        <h3>Delete this protector payment?</h3>
                        <p>
                            <strong x-text="deleteName"></strong>
                            will be removed.
                        </p>
                        <div class="confirm-modal__actions">
                            <button type="button" class="confirm-button confirm-button--cancel" x-ref="deleteCancel" @click="closeDelete()">Cancel</button>
                            <button
                                type="button"
                                class="confirm-button confirm-button--delete"
                                @click="const id = deleteId; closeDelete(); $wire.deletePayment(id)"
                            >
                                Delete
                            </button>
                        </div>
                    </section>
                </div>
            </template>
        @endif
    @endif
</div>
