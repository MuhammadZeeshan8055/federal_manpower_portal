<div
    class="master-page"
    x-data="{
        deleteOpen: false,
        deleteId: null,
        deleteName: '',
        openDelete(id, name) {
            this.deleteId = id;
            this.deleteName = name;
            this.deleteOpen = true;
        },
        closeDelete() {
            this.deleteOpen = false;
        }
    }"
    @keydown.escape.window="if (deleteOpen) closeDelete()"
>
    @if ($denied)
        <div class="panel empty-card">
            <h3>No access</h3>
            <p>Only admins can manage users.</p>
        </div>
    @else
        @if ($successMessage !== '')
            <div
                class="portal-toast portal-toast--{{ $toastType }}"
                wire:key="users-toast-{{ $toastVersion }}"
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
            >
                <span class="portal-toast__icon">
                    @include('partials.icon', ['name' => $toastType === 'danger' ? 'trash' : 'check'])
                </span>
                <span class="portal-toast__copy">
                    <small>{{ $toastType === 'danger' ? 'Notice' : 'Success' }}</small>
                    <strong>{{ $successMessage }}</strong>
                </span>
                <button type="button" class="portal-toast__close" @click="show = false" aria-label="Dismiss">&times;</button>
                <span class="portal-toast__progress" aria-hidden="true"></span>
            </div>
        @endif

        <section class="panel master-list-panel">
            <div class="panel__header attendance-month-head">
                <div>
                    <p class="panel__eyebrow">SETTINGS</p>
                    <h3>All users</h3>
                </div>
                <button type="button" class="button button--soft" wire:click="openCreate">
                    Add user
                </button>
            </div>

            <div class="users-toolbar">
                <input
                    type="search"
                    class="users-toolbar__search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search name, email, role…"
                >
            </div>

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>WFH</th>
                            <th class="master-actions-heading">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr wire:key="user-{{ $user->id }}">
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->roleLabel() }}</td>
                                <td>
                                    @if ($user->bypass_attendance)
                                        <span class="att-status att-status--exempt">Exempt</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="master-row-actions">
                                    <button
                                        type="button"
                                        class="row-action row-action--edit"
                                        wire:click="editUser({{ $user->id }})"
                                    >
                                        @include('partials.icon', ['name' => 'pencil'])
                                        <span>Edit</span>
                                    </button>

                                    @if ($user->id !== auth()->id())
                                        <button
                                            type="button"
                                            class="row-action row-action--delete"
                                            @click="openDelete({{ $user->id }}, @js($user->name))"
                                        >
                                            @include('partials.icon', ['name' => 'trash'])
                                            <span>Delete</span>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">No office users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (method_exists($users, 'links'))
                <div class="users-pager">{{ $users->links() }}</div>
            @endif
        </section>

        @if ($formOpen)
            <div class="user-modal-backdrop" wire:key="user-form-modal">
                <div class="user-modal" role="dialog" aria-modal="true">
                    <div class="user-modal__head">
                        <h3>{{ $editingId ? 'Edit User' : 'Add User' }}</h3>
                        <button type="button" class="user-modal__close" wire:click="cancelForm" aria-label="Close">&times;</button>
                    </div>

                    <form class="user-modal__body" wire:submit="saveUser">
                        <div class="user-modal__grid">
                            <label>
                                Name
                                <input type="text" wire:model="name" maxlength="160">
                                @error('name') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            <label>
                                Email
                                <input type="email" wire:model="email" maxlength="160">
                                @error('email') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            <label>
                                {{ $editingId ? 'New password (optional)' : 'Password' }}
                                <input
                                    type="password"
                                    wire:model="password"
                                    autocomplete="new-password"
                                    placeholder="{{ $editingId ? 'Leave blank to keep current' : '' }}"
                                >
                                @error('password') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            <label>
                                Role
                                <select wire:model.live="role">
                                    @foreach ($roleOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('role') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            <label>
                                Monthly salary
                                <input type="number" wire:model="monthly_salary" min="0" step="0.01" placeholder="e.g. 50000">
                                <small class="muted">Used as gross amount when creating salary slips</small>
                                @error('monthly_salary') <span class="master-error">{{ $message }}</span> @enderror
                            </label>
                        </div>

                        @if ($showPermissionGrid)
                            <label class="user-perms__all user-modal__wfh">
                                <input type="checkbox" wire:model.live="bypass_attendance">
                                <span>
                                    <strong>Attendance exempt (WFH)</strong>
                                </span>
                            </label>

                            <div class="user-perms">
                                <p class="user-perms__eyebrow">Feature access</p>

                                <label class="user-perms__all">
                                    <input type="checkbox" wire:model.live="allowAll">
                                    <span>
                                        <strong>Allow all access</strong>
                                        <small>Manage every module and feature.</small>
                                    </span>
                                </label>

                                <div class="user-perms__grid">
                                    @foreach ($grantModules as $module)
                                        @php $key = $module['key']; @endphp
                                        <div class="user-perms__card" wire:key="perm-{{ $key }}">
                                            <label class="user-perms__check">
                                                <input type="checkbox" wire:model.live="moduleAccess.{{ $key }}.on">
                                                <span>{{ $module['title'] }}</span>
                                            </label>
                                            <select
                                                wire:model="moduleAccess.{{ $key }}.access"
                                                {{ ! empty($moduleAccess[$key]['on']) ? '' : 'disabled' }}
                                            >
                                                <option value="manage">Manage</option>
                                                <option value="view">View</option>
                                            </select>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="user-perms__admin-note">
                                Admins have full access to all modules. Feature checkboxes are not needed.
                            </div>
                        @endif

                        <div class="user-modal__footer">
                            <button type="button" class="button button--ghost" wire:click="cancelForm">Cancel</button>
                            <button type="submit" class="button user-modal__save" wire:loading.attr="disabled">
                                {{ $editingId ? 'Update User' : 'Create User' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <template x-teleport="body">
            <div
                class="confirm-backdrop"
                x-show="deleteOpen"
                x-cloak
                @click.self="closeDelete()"
                role="presentation"
            >
                <section class="confirm-modal" role="alertdialog" aria-modal="true">
                    <button type="button" class="confirm-modal__close" @click="closeDelete()" aria-label="Close">&times;</button>
                    <p class="confirm-modal__eyebrow">DELETE USER</p>
                    <h3>Are you sure?</h3>
                    <p>You are about to delete <strong x-text="deleteName"></strong>.</p>
                    <div class="confirm-modal__actions">
                        <button type="button" class="confirm-button confirm-button--cancel" @click="closeDelete()">Cancel</button>
                        <button
                            type="button"
                            class="confirm-button confirm-button--delete"
                            @click="const id = deleteId; closeDelete(); $wire.deleteUser(id)"
                        >
                            Delete
                        </button>
                    </div>
                </section>
            </div>
        </template>
    @endif
</div>
