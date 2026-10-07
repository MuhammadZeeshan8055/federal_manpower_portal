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
    @clients-filter-status.window="$wire.applyStatusFilter($event.detail.id || '', $event.detail.label || '')"
>
    @if ($denied)
        <div class="panel empty-card">
            <h3>No access</h3>
            <p>You do not have permission to view clients.</p>
        </div>
    @elseif ($viewingId)
        <livewire:clients.view-client :client-id="$viewingId" :key="'view-client-'.$viewingId" />
    @elseif ($editingId)
        <livewire:clients.edit-client :client-id="$editingId" :key="'edit-client-'.$editingId" />
    @else
        @if ($successMessage !== '')
            <div
                class="portal-toast portal-toast--{{ $toastType }}"
                wire:key="clients-list-toast-{{ $toastVersion }}"
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

        <section class="panel master-list-panel">
            <div class="panel__header">
                <div>
                    <p class="panel__eyebrow">CLIENTS</p>
                    <h3>
                        @if ($statusFilterLabel !== '')
                            {{ $statusFilterLabel }}
                        @else
                            All clients
                        @endif
                    </h3>
                    @if ($statusFilterLabel !== '')
                        <button type="button" class="text-button" wire:click="clearStatusFilter" style="margin-top:6px;">
                            Clear status filter
                        </button>
                    @endif
                </div>

                <label class="master-search">
                    <span class="master-search__icon" aria-hidden="true">
                        @include('partials.icon', ['name' => 'search'])
                    </span>
                    <input
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search name, CNIC, passport, phone..."
                        aria-label="Search clients"
                    >
                </label>
            </div>

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>CNIC</th>
                            <th>Passport</th>
                            <th>Country</th>
                            <th>Trade</th>
                            <th>Process</th>
                            <th>Docs</th>
                            <th>Status</th>
                            <th class="master-actions-heading">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($clients as $client)
                            <tr wire:key="client-{{ $client->id }}">
                                <td>{{ $client->full_name }}</td>
                                <td>{{ $client->cnic }}</td>
                                <td>{{ $client->passport_number }}</td>
                                <td>{{ $client->country?->name ?: '—' }}</td>
                                <td>{{ $client->trade?->name ?: '—' }}</td>
                                <td>{{ $client->processStatus?->name ?: '—' }}</td>
                                <td>{{ $client->documents_count }}</td>
                                <td>
                                    @if ($client->overall_status === 'active')
                                        <span class="status status--ready">Active</span>
                                    @else
                                        <span class="status">Inactive</span>
                                    @endif
                                </td>
                                <td class="master-row-actions">
                                    <button
                                        type="button"
                                        class="row-action row-action--edit"
                                        wire:click="viewClient({{ $client->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="viewClient({{ $client->id }})"
                                    >
                                        <span>View</span>
                                    </button>
                                    @if ($canManageClients)
                                        <button
                                            type="button"
                                            class="row-action row-action--edit"
                                            wire:click="editClient({{ $client->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="editClient({{ $client->id }})"
                                        >
                                            @include('partials.icon', ['name' => 'pencil'])
                                            <span>Edit</span>
                                        </button>
                                        <button
                                            type="button"
                                            class="row-action row-action--delete"
                                            @click="openDelete({{ $client->id }}, @js($client->full_name), $el)"
                                        >
                                            @include('partials.icon', ['name' => 'trash'])
                                            <span>Delete</span>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="muted">No clients yet. Add one from Add Client.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $clients->links() }}
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
                    aria-labelledby="delete-client-title"
                    aria-describedby="delete-client-description"
                >
                    <button type="button" class="confirm-modal__close" @click="closeDelete()" aria-label="Close dialog">
                        &times;
                    </button>

                    <span class="confirm-modal__icon">
                        @include('partials.icon', ['name' => 'trash'])
                    </span>

                    <p class="confirm-modal__eyebrow">DELETE CLIENT</p>
                    <h3 id="delete-client-title">Are you sure?</h3>
                    <p id="delete-client-description">
                        You are about to permanently delete
                        <strong x-text="deleteName"></strong>
                        and their private files. This cannot be undone.
                    </p>

                    <div class="confirm-modal__actions">
                        <button type="button" class="confirm-button confirm-button--cancel" x-ref="deleteCancel" @click="closeDelete()">
                            Cancel
                        </button>
                        <button
                            type="button"
                            class="confirm-button confirm-button--delete"
                            @click="const id = deleteId; closeDelete(); $wire.deleteClient(id)"
                            wire:loading.attr="disabled"
                            wire:target="deleteClient"
                        >
                            @include('partials.icon', ['name' => 'trash'])
                            <span>Delete client</span>
                        </button>
                    </div>
                </section>
            </div>
        </template>
    @endif
</div>
