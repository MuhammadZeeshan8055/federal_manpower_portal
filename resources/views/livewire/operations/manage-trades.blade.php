<div
    class="master-page"
    x-data="{
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
>
    @if ($denied)
        <div class="panel empty-card">
            <h3>No access</h3>
            <p>You do not have permission to manage trades.</p>
        </div>
    @else
        <section class="panel master-form-panel">
            <div class="panel__header">
                <div>
                    <p class="panel__eyebrow">{{ $editingId ? 'EDIT' : 'ADD' }}</p>
                    <h3>{{ $editingId ? 'Edit trade' : 'New trade' }}</h3>
                </div>
            </div>

            @if ($successMessage !== '')
                <div
                    class="portal-toast portal-toast--{{ $toastType }}"
                    wire:key="trade-toast-{{ $toastVersion }}"
                    x-data="{ show: true }"
                    x-init="setTimeout(() => show = false, 5000)"
                    x-show="show"
                    x-transition:enter="toast-enter"
                    x-transition:enter-start="toast-enter-start"
                    x-transition:enter-end="toast-enter-end"
                    x-transition:leave="toast-leave"
                    x-transition:leave-start="toast-leave-start"
                    x-transition:leave-end="toast-leave-end"
                    role="status"
                    aria-live="polite"
                    aria-atomic="true"
                >
                    <span class="portal-toast__icon">
                        @include('partials.icon', ['name' => $toastType === 'danger' ? 'trash' : 'check'])
                    </span>
                    <span class="portal-toast__copy">
                        <small>{{ $toastType === 'danger' ? 'Deleted' : 'Success' }}</small>
                        <strong>{{ $successMessage }}</strong>
                    </span>
                    <button type="button" class="portal-toast__close" @click="show = false" aria-label="Dismiss notification">
                        &times;
                    </button>
                    <span class="portal-toast__progress" aria-hidden="true"></span>
                </div>
            @endif

            <form class="master-form" wire:submit="saveTrade">
                <label>
                    Trade name
                    <input type="text" wire:model="name" placeholder="e.g. Electrician" maxlength="160">
                    @error('name') <span class="master-error">{{ $message }}</span> @enderror
                </label>

                <label class="master-check">
                    <input type="checkbox" wire:model="is_active">
                    <span>Active</span>
                </label>

                <div class="master-form__actions">
                    <button type="submit" class="button button--soft">
                        {{ $editingId ? 'Save' : 'Add trade' }}
                    </button>

                    @if ($editingId)
                        <button type="button" class="button button--ghost" wire:click="cancelEdit">
                            Cancel
                        </button>
                    @endif
                </div>
            </form>
        </section>

        <section class="panel master-list-panel">
            <div class="panel__header">
                <div>
                    <p class="panel__eyebrow">TRADES</p>
                    <h3>Job trades</h3>
                </div>
            </div>

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th class="master-actions-heading">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($trades as $trade)
                            <tr wire:key="trade-{{ $trade->id }}">
                                <td>{{ $trade->name }}</td>
                                <td>
                                    @if ($trade->is_active)
                                        <span class="status status--ready">Active</span>
                                    @else
                                        <span class="status">Inactive</span>
                                    @endif
                                </td>
                                <td class="master-row-actions">
                                    <button
                                        type="button"
                                        class="row-action row-action--edit"
                                        wire:click="editTrade({{ $trade->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="editTrade({{ $trade->id }})"
                                    >
                                        @include('partials.icon', ['name' => 'pencil'])
                                        <span>Edit</span>
                                    </button>
                                    <button
                                        type="button"
                                        class="row-action row-action--delete"
                                        @click="openDelete({{ $trade->id }}, @js($trade->name), $el)"
                                    >
                                        @include('partials.icon', ['name' => 'trash'])
                                        <span>Delete</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="muted">No trades yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <template x-teleport="body">
            <div
                class="confirm-backdrop"
                x-show="deleteOpen"
                x-cloak
                x-transition:enter="confirm-fade-in"
                x-transition:enter-start="confirm-fade-start"
                x-transition:enter-end="confirm-fade-end"
                x-transition:leave="confirm-fade-out"
                x-transition:leave-start="confirm-fade-end"
                x-transition:leave-end="confirm-fade-start"
                @click.self="closeDelete()"
                role="presentation"
            >
                <section
                    class="confirm-modal"
                    role="alertdialog"
                    aria-modal="true"
                    aria-labelledby="delete-trade-title"
                    aria-describedby="delete-trade-description"
                >
                    <button type="button" class="confirm-modal__close" @click="closeDelete()" aria-label="Close dialog">
                        &times;
                    </button>

                    <span class="confirm-modal__icon">
                        @include('partials.icon', ['name' => 'trash'])
                    </span>

                    <p class="confirm-modal__eyebrow">DELETE TRADE</p>
                    <h3 id="delete-trade-title">Are you sure?</h3>
                    <p id="delete-trade-description">
                        You are about to permanently delete
                        <strong x-text="deleteName"></strong>.
                        This action cannot be undone.
                    </p>

                    <div class="confirm-modal__actions">
                        <button type="button" class="confirm-button confirm-button--cancel" x-ref="deleteCancel" @click="closeDelete()">
                            Cancel
                        </button>
                        <button
                            type="button"
                            class="confirm-button confirm-button--delete"
                            @click="const id = deleteId; closeDelete(); $wire.deleteTrade(id)"
                            wire:loading.attr="disabled"
                            wire:target="deleteTrade"
                        >
                            @include('partials.icon', ['name' => 'trash'])
                            <span>Delete trade</span>
                        </button>
                    </div>
                </section>
            </div>
        </template>
    @endif
</div>
