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
    @care-off-form-reset.window="formOpen = false"
>
    @if ($denied)
        <div class="panel empty-card">
            <h3>No access</h3>
            <p>You do not have permission to manage care offs.</p>
        </div>
    @else
        <section
            class="panel master-form-panel master-form-panel--collapsible"
            :class="{ 'master-form-panel--open': formOpen }"
        >
            <button
                type="button"
                class="panel__header master-form-toggle"
                @click="formOpen = !formOpen"
                :aria-expanded="formOpen.toString()"
                aria-controls="care-off-form-fields"
            >
                <div>
                    <p class="panel__eyebrow">{{ $editingId ? 'EDIT' : 'ADD' }}</p>
                    <h3>{{ $editingId ? 'Edit care off' : 'New care off' }}</h3>
                </div>
                <span class="master-form-toggle__action">
                    <span x-text="formOpen ? 'Close form' : 'Add care off'"></span>
                    <span class="master-form-toggle__icon" :class="{ 'master-form-toggle__icon--open': formOpen }">
                        @include('partials.icon', ['name' => 'plus'])
                    </span>
                </span>
            </button>

            @if ($successMessage !== '')
                <div
                    class="portal-toast portal-toast--{{ $toastType }}"
                    wire:key="care-off-toast-{{ $toastVersion }}"
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

            <div
                id="care-off-form-fields"
                class="master-form-collapse"
                :class="{ 'master-form-collapse--open': formOpen }"
                :aria-hidden="(!formOpen).toString()"
                :inert="!formOpen"
            >
                <div class="master-form-collapse__inner">
                    <div class="master-form-content">
            <form class="master-form" wire:submit="saveCareOff">
                <label>
                    Name
                    <input type="text" wire:model="name" placeholder="e.g. Ali Referrals" maxlength="160">
                    @error('name') <span class="master-error">{{ $message }}</span> @enderror
                </label>

                <label>
                    Phone
                    <input type="text" wire:model="phone" placeholder="e.g. 0300 1234567" maxlength="40">
                    @error('phone') <span class="master-error">{{ $message }}</span> @enderror
                </label>

                <label>
                    Email
                    <input type="email" wire:model="email" placeholder="optional@email.com" maxlength="160">
                    @error('email') <span class="master-error">{{ $message }}</span> @enderror
                </label>

                <label class="master-check">
                    <input type="checkbox" wire:model="is_active">
                    <span>Active</span>
                </label>

                <div class="master-form__actions">
                    <button type="submit" class="button button--soft">
                        {{ $editingId ? 'Save' : 'Add care off' }}
                    </button>

                    @if ($editingId)
                        <button type="button" class="button button--ghost" wire:click="cancelEdit" @click="formOpen = false">
                            Cancel
                        </button>
                    @endif
                </div>
            </form>
                    </div>
                </div>
            </div>
        </section>

        <section class="panel master-list-panel">
            <div class="panel__header">
                <div>
                    <p class="panel__eyebrow">CARE OFFS</p>
                    <h3>Referrers &amp; submitters</h3>
                </div>

                <label class="master-search">
                    <span class="master-search__icon" aria-hidden="true">
                        @include('partials.icon', ['name' => 'search'])
                    </span>
                    <input
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search care offs..."
                        aria-label="Search care offs"
                    >
                </label>
            </div>

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th class="master-actions-heading">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($careOffs as $careOff)
                            <tr wire:key="care-off-{{ $careOff->id }}">
                                <td>{{ $careOff->name }}</td>
                                <td>{{ $careOff->phone ?: '—' }}</td>
                                <td>{{ $careOff->email ?: '—' }}</td>
                                <td>
                                    @if ($careOff->is_active)
                                        <span class="status status--ready">Active</span>
                                    @else
                                        <span class="status">Inactive</span>
                                    @endif
                                </td>
                                <td class="master-row-actions">
                                    <button
                                        type="button"
                                        class="row-action row-action--edit"
                                        wire:click="editCareOff({{ $careOff->id }})"
                                        @click="formOpen = true"
                                        wire:loading.attr="disabled"
                                        wire:target="editCareOff({{ $careOff->id }})"
                                    >
                                        @include('partials.icon', ['name' => 'pencil'])
                                        <span>Edit</span>
                                    </button>
                                    <button
                                        type="button"
                                        class="row-action row-action--delete"
                                        @click="openDelete({{ $careOff->id }}, @js($careOff->name), $el)"
                                    >
                                        @include('partials.icon', ['name' => 'trash'])
                                        <span>Delete</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="muted">No care offs yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $careOffs->links() }}
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
                    aria-labelledby="delete-care-off-title"
                    aria-describedby="delete-care-off-description"
                >
                    <button type="button" class="confirm-modal__close" @click="closeDelete()" aria-label="Close dialog">
                        &times;
                    </button>

                    <span class="confirm-modal__icon">
                        @include('partials.icon', ['name' => 'trash'])
                    </span>

                    <p class="confirm-modal__eyebrow">DELETE CARE OFF</p>
                    <h3 id="delete-care-off-title">Are you sure?</h3>
                    <p id="delete-care-off-description">
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
                            @click="const id = deleteId; closeDelete(); $wire.deleteCareOff(id)"
                            wire:loading.attr="disabled"
                            wire:target="deleteCareOff"
                        >
                            @include('partials.icon', ['name' => 'trash'])
                            <span>Delete care off</span>
                        </button>
                    </div>
                </section>
            </div>
        </template>
    @endif
</div>
