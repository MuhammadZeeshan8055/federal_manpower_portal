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
            <p>You do not have permission to view visitors.</p>
        </div>
    @else
        @if ($canManage)
            <section class="panel master-form-panel master-form-panel--collapsible" :class="{ 'master-form-panel--open': formOpen }">
                @include('partials.master-form-toggle', [
                    'targetId' => 'visitor-form-fields',
                    'eyebrow' => $editingId ? 'EDIT' : 'ADD',
                    'title' => $editingId ? 'Edit visitor' : 'Log visitor',
                    'actionLabel' => 'Log visitor',
                ])

                @if ($successMessage !== '')
                    <div
                        class="portal-toast portal-toast--{{ $toastType }}"
                        wire:key="visitor-toast-{{ $toastVersion }}"
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

                <div id="visitor-form-fields" class="master-form-collapse" :class="{ 'master-form-collapse--open': formOpen }" :aria-hidden="(!formOpen).toString()" :inert="!formOpen">
                    <div class="master-form-collapse__inner"><div class="master-form-content">
                <form class="master-form" wire:submit="saveVisitor">
                    <label>
                        Name
                        <textarea class="visitor-textarea" wire:model="name" rows="2" placeholder="Visitor full name" maxlength="160"></textarea>
                        @error('name') <span class="master-error">{{ $message }}</span> @enderror
                    </label>

                    <label>
                        Contact no
                        <input type="text" wire:model="contact_no" placeholder="Phone / mobile" maxlength="40">
                        @error('contact_no') <span class="master-error">{{ $message }}</span> @enderror
                    </label>

                    <label>
                        Purpose
                        <textarea class="visitor-textarea" wire:model="purpose" rows="3" placeholder="e.g. Job inquiry, Document submit" maxlength="255"></textarea>
                        @error('purpose') <span class="master-error">{{ $message }}</span> @enderror
                    </label>

                    <label>
                        Meet with
                        <input type="text" list="visitor-staff-names" wire:model="meet_with" placeholder="Staff name" maxlength="160">
                        <datalist id="visitor-staff-names">
                            @foreach ($staffNames as $staffName)
                                <option value="{{ $staffName }}"></option>
                            @endforeach
                        </datalist>
                        @error('meet_with') <span class="master-error">{{ $message }}</span> @enderror
                    </label>

                    <label>
                        Status
                        <select wire:model="status">
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status') <span class="master-error">{{ $message }}</span> @enderror
                    </label>

                    <div class="master-form__actions">
                        <button type="submit" class="button button--soft">
                            {{ $editingId ? 'Save' : 'Log visitor' }}
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
                wire:key="visitor-toast-{{ $toastVersion }}"
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

        <section class="panel master-list-panel">
            <div class="panel__header">
                <div>
                    <p class="panel__eyebrow">DAILY VISITORS</p>
                    <h3>
                        @if ($isToday)
                            Today's visitors
                        @else
                            Visitors — {{ \Illuminate\Support\Carbon::parse($filterDate)->format('d M Y') }}
                        @endif
                    </h3>
                </div>

                <div class="visitors-toolbar">
                    <input
                        class="visitors-toolbar__select"
                        type="date"
                        wire:model.live="filterDate"
                        aria-label="Filter by date"
                    >

                    @unless ($isToday)
                        <button type="button" class="text-button" wire:click="showToday">
                            Today
                        </button>
                    @endunless

                    <select class="visitors-toolbar__select" wire:model.live="statusFilter" aria-label="Status filter">
                        <option value="">All statuses</option>
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>

                    <label class="master-search">
                        <span class="master-search__icon" aria-hidden="true">
                            @include('partials.icon', ['name' => 'search'])
                        </span>
                        <input
                            type="search"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search name, contact, purpose..."
                            aria-label="Search visitors"
                        >
                    </label>
                </div>
            </div>

            <div class="table-wrap visitors-table-wrap">
                <table class="data-table visitors-table">
                    <thead>
                        <tr>
                            <th>Check-in</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Purpose</th>
                            <th>Meet with</th>
                            <th>Status</th>
                            <th class="master-actions-heading">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($visitors as $visitor)
                            <tr wire:key="visitor-{{ $visitor->id }}">
                                <td class="visitors-table__time">{{ $visitor->visited_at?->format('h:i A') ?: '—' }}</td>
                                <td><span class="visitors-table__clamp" title="{{ $visitor->name }}">{{ $visitor->name }}</span></td>
                                <td><span class="visitors-table__clamp" title="{{ $visitor->contact_no }}">{{ $visitor->contact_no }}</span></td>
                                <td><span class="visitors-table__clamp" title="{{ $visitor->purpose }}">{{ $visitor->purpose }}</span></td>
                                <td><span class="visitors-table__clamp" title="{{ $visitor->meet_with }}">{{ $visitor->meet_with }}</span></td>
                                <td>
                                    @if ($visitor->status === 'visit_complete' || $visitor->status === 'meeting_done')
                                        <span class="status status--ready">{{ $visitor->statusLabel() }}</span>
                                    @elseif ($visitor->status === 'in_meeting')
                                        <span class="status status--interview">{{ $visitor->statusLabel() }}</span>
                                    @else
                                        <span class="status status--screening">{{ $visitor->statusLabel() }}</span>
                                    @endif
                                </td>
                                <td class="master-row-actions">
                                    @if ($canManage)
                                        @if (in_array($visitor->status, ['waiting', 'in_meeting'], true))
                                            <select
                                                class="visitors-status-select"
                                                wire:change="markStatus({{ $visitor->id }}, $event.target.value)"
                                                aria-label="Update status"
                                            >
                                                <option value="">Status…</option>
                                                @if ($visitor->status === 'waiting')
                                                    <option value="in_meeting">In meeting</option>
                                                @endif
                                                <option value="meeting_done">Meeting done</option>
                                                <option value="visit_complete">Visit complete</option>
                                            </select>
                                        @endif
                                        <button
                                            type="button"
                                            class="row-action row-action--edit"
                                            wire:click="editVisitor({{ $visitor->id }})"
                                            @click="formOpen = true"
                                            title="Edit"
                                        >
                                            @include('partials.icon', ['name' => 'pencil'])
                                            <span>Edit</span>
                                        </button>
                                        <button
                                            type="button"
                                            class="row-action row-action--delete"
                                            @click="openDelete({{ $visitor->id }}, @js($visitor->name), $el)"
                                            title="Delete"
                                        >
                                            @include('partials.icon', ['name' => 'trash'])
                                            <span>Delete</span>
                                        </button>
                                    @else
                                        <span class="muted">View only</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="muted">No visitors logged yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($visitors)
                {{ $visitors->links() }}
            @endif
        </section>

        @if ($canManage)
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
                        aria-labelledby="delete-visitor-title"
                        aria-describedby="delete-visitor-description"
                    >
                        <button type="button" class="confirm-modal__close" @click="closeDelete()" aria-label="Close dialog">
                            &times;
                        </button>

                        <span class="confirm-modal__icon">
                            @include('partials.icon', ['name' => 'trash'])
                        </span>

                        <p class="confirm-modal__eyebrow">DELETE VISITOR</p>
                        <h3 id="delete-visitor-title">Are you sure?</h3>
                        <p id="delete-visitor-description">
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
                                @click="const id = deleteId; closeDelete(); $wire.deleteVisitor(id)"
                                wire:loading.attr="disabled"
                                wire:target="deleteVisitor"
                            >
                                @include('partials.icon', ['name' => 'trash'])
                                <span>Delete visitor</span>
                            </button>
                        </div>
                    </section>
                </div>
            </template>
        @endif
    @endif
</div>
