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
            <p>You do not have permission to manage countries.</p>
        </div>
    @else
        <section class="panel master-form-panel">
            <div class="panel__header">
                <div>
                    <p class="panel__eyebrow">{{ $editingId ? 'EDIT' : 'ADD' }}</p>
                    <h3>{{ $editingId ? 'Edit country' : 'New country' }}</h3>
                </div>
            </div>

            @if ($successMessage !== '')
                <p class="master-message">{{ $successMessage }}</p>
            @endif

            <form class="master-form" wire:submit="saveCountry">
                <label>
                    Country name
                    <input type="text" wire:model="name" placeholder="e.g. Serbia" maxlength="120">
                    @error('name') <span class="master-error">{{ $message }}</span> @enderror
                </label>

                <label>
                    Code
                    <input type="text" wire:model="code" placeholder="e.g. RS" maxlength="20">
                    @error('code') <span class="master-error">{{ $message }}</span> @enderror
                </label>

                <label
                    class="master-file-field"
                    x-data="{ fileName: '' }"
                    @country-form-reset.window="fileName = ''"
                >
                    Flag (optional)
                    <span class="file-picker">
                        <input
                            type="file"
                            class="file-picker__input"
                            wire:model="flag"
                            accept="image/*"
                            aria-label="Choose a flag image"
                            @change="fileName = $event.target.files[0]?.name || ''"
                        >
                        <span class="file-picker__icon">
                            @include('partials.icon', ['name' => 'upload'])
                        </span>
                        <span class="file-picker__copy">
                            <strong x-text="fileName || 'Choose a flag image'"></strong>
                            <small x-text="fileName ? 'Ready to upload' : 'PNG, JPG, WEBP or GIF up to 2 MB'"></small>
                        </span>
                        <span class="file-picker__button">Browse</span>
                    </span>
                    @error('flag') <span class="master-error">{{ $message }}</span> @enderror
                    <small class="file-picker__status" wire:loading wire:target="flag">Uploading image...</small>
                </label>

                <label class="master-check">
                    <input type="checkbox" wire:model="is_active">
                    <span>Active</span>
                </label>

                <div class="master-form__actions">
                    <button type="submit" class="button button--soft">
                        {{ $editingId ? 'Save' : 'Add country' }}
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
                    <p class="panel__eyebrow">COUNTRIES</p>
                    <h3>Saved list</h3>
                </div>
            </div>

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Flag</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Status</th>
                            <th class="master-actions-heading">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($countries as $country)
                            <tr wire:key="country-{{ $country->id }}">
                                <td>
                                    <img class="master-flag" src="{{ $country->flagUrl() }}" alt="">
                                </td>
                                <td>{{ $country->name }}</td>
                                <td>{{ $country->code }}</td>
                                <td>
                                    @if ($country->is_active)
                                        <span class="status status--ready">Active</span>
                                    @else
                                        <span class="status">Inactive</span>
                                    @endif
                                </td>
                                <td class="master-row-actions">
                                    <button
                                        type="button"
                                        class="row-action row-action--edit"
                                        wire:click="editCountry({{ $country->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="editCountry({{ $country->id }})"
                                    >
                                        @include('partials.icon', ['name' => 'pencil'])
                                        <span>Edit</span>
                                    </button>
                                    <button
                                        type="button"
                                        class="row-action row-action--delete"
                                        @click="openDelete({{ $country->id }}, @js($country->name), $el)"
                                    >
                                        @include('partials.icon', ['name' => 'trash'])
                                        <span>Delete</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="muted">No countries yet.</td>
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
                    aria-labelledby="delete-country-title"
                    aria-describedby="delete-country-description"
                >
                    <button type="button" class="confirm-modal__close" @click="closeDelete()" aria-label="Close dialog">
                        &times;
                    </button>

                    <span class="confirm-modal__icon">
                        @include('partials.icon', ['name' => 'trash'])
                    </span>

                    <p class="confirm-modal__eyebrow">DELETE COUNTRY</p>
                    <h3 id="delete-country-title">Are you sure?</h3>
                    <p id="delete-country-description">
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
                            @click="const id = deleteId; closeDelete(); $wire.deleteCountry(id)"
                            wire:loading.attr="disabled"
                            wire:target="deleteCountry"
                        >
                            @include('partials.icon', ['name' => 'trash'])
                            <span>Delete country</span>
                        </button>
                    </div>
                </section>
            </div>
        </template>
    @endif
</div>
