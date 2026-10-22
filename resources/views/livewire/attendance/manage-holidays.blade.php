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
            <p>You do not have permission to manage holidays.</p>
        </div>
    @else
        <section class="panel master-form-panel master-form-panel--collapsible" :class="{ 'master-form-panel--open': formOpen }">
            @include('partials.master-form-toggle', [
                'targetId' => 'holiday-form-fields',
                'eyebrow' => $editingId ? 'EDIT' : 'ADD',
                'title' => $editingId ? 'Edit holiday' : 'New holiday',
                'actionLabel' => 'Add holiday',
            ])

            @if ($successMessage !== '')
                <div
                    class="portal-toast portal-toast--{{ $toastType }}"
                    wire:key="holiday-toast-{{ $toastVersion }}"
                    x-data="{ show: true }"
                    x-init="setTimeout(() => show = false, 5000)"
                    x-show="show"
                    role="status"
                >
                    <span class="portal-toast__copy">
                        <strong>{{ $successMessage }}</strong>
                    </span>
                </div>
            @endif

            <div id="holiday-form-fields" class="master-form-collapse" :class="{ 'master-form-collapse--open': formOpen }" :aria-hidden="(!formOpen).toString()" :inert="!formOpen">
                <div class="master-form-collapse__inner"><div class="master-form-content">
                    <form class="master-form" wire:submit="saveHoliday">
                        <label>
                            Date
                            <input type="date" wire:model="date">
                            @error('date') <span class="master-error">{{ $message }}</span> @enderror
                        </label>

                        <label>
                            Title
                            <input type="text" wire:model="title" placeholder="e.g. Eid holiday" maxlength="120">
                            @error('title') <span class="master-error">{{ $message }}</span> @enderror
                        </label>

                        <div class="master-form__actions">
                            <button type="submit" class="button button--soft">
                                {{ $editingId ? 'Save' : 'Add holiday' }}
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

        <section class="panel master-list-panel">
            <div class="panel__header">
                <div>
                    <p class="panel__eyebrow">HR</p>
                    <h3>Holidays</h3>
                </div>
                <label class="master-search">
                    <span class="master-search__icon" aria-hidden="true">
                        @include('partials.icon', ['name' => 'search'])
                    </span>
                    <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search title..." aria-label="Search holidays">
                </label>
            </div>

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Title</th>
                            <th class="master-actions-heading">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($holidays as $holiday)
                            <tr wire:key="holiday-{{ $holiday->id }}">
                                <td>{{ $holiday->date->format('d M Y') }}</td>
                                <td>{{ $holiday->title }}</td>
                                <td class="master-row-actions">
                                    <button type="button" class="row-action row-action--edit" wire:click="editHoliday({{ $holiday->id }})" @click="formOpen = true">
                                        @include('partials.icon', ['name' => 'pencil'])
                                        <span>Edit</span>
                                    </button>
                                    <button type="button" class="row-action row-action--delete" @click="openDelete({{ $holiday->id }}, @js($holiday->title), $el)">
                                        @include('partials.icon', ['name' => 'trash'])
                                        <span>Delete</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="muted">No holidays added yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($holidays)
                {{ $holidays->links() }}
            @endif
        </section>

        <template x-teleport="body">
            <div class="confirm-backdrop" x-show="deleteOpen" x-cloak @click.self="closeDelete()" role="presentation">
                <section class="confirm-modal" role="alertdialog" aria-modal="true">
                    <p class="confirm-modal__eyebrow">DELETE HOLIDAY</p>
                    <h3>Are you sure?</h3>
                    <p>Delete <strong x-text="deleteName"></strong>?</p>
                    <div class="confirm-modal__actions">
                        <button type="button" class="confirm-button confirm-button--cancel" x-ref="deleteCancel" @click="closeDelete()">Cancel</button>
                        <button type="button" class="confirm-button confirm-button--delete" @click="const id = deleteId; closeDelete(); $wire.deleteHoliday(id)">
                            Delete
                        </button>
                    </div>
                </section>
            </div>
        </template>
    @endif
</div>
