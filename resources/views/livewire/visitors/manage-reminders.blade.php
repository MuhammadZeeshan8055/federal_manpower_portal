<div
    class="master-page"
    x-data
    @open-visitor-reminder.window="
        if ($event.detail && $event.detail.visitorId) {
            $wire.editFollowUp(Number($event.detail.visitorId));
        }
    "
>
    @if ($denied)
        <div class="panel empty-card">
            <h3>No access</h3>
            <p>You do not have permission to view reminders.</p>
        </div>
    @else
        @if ($successMessage !== '')
            <div
                class="portal-toast portal-toast--{{ $toastType }}"
                wire:key="reminder-toast-{{ $toastVersion }}"
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

        @if ($canManage && $editingId)
            <section class="panel master-form-panel">
                <div class="panel__header">
                    <div>
                        <p class="panel__eyebrow">FOLLOW UP</p>
                        <h3>Update reminder</h3>
                    </div>
                </div>

                <form class="master-form" wire:submit="saveFollowUp">
                    <label>
                        Follow up status
                        <select wire:model="follow_up_status">
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('follow_up_status') <span class="master-error">{{ $message }}</span> @enderror
                    </label>

                    <label>
                        Follow up remarks
                        <textarea class="visitor-textarea" wire:model="follow_up_remarks" rows="3" placeholder="What was done / next step" maxlength="2000"></textarea>
                        @error('follow_up_remarks') <span class="master-error">{{ $message }}</span> @enderror
                    </label>

                    <div class="master-form__actions">
                        <button type="submit" class="button button--soft">Save follow up</button>
                        <button type="button" class="button button--ghost" wire:click="cancelEdit">Cancel</button>
                    </div>
                </form>
            </section>
        @endif

        <section class="panel master-list-panel">
            <div class="panel__header">
                <div>
                    <p class="panel__eyebrow">DAILY VISITORS</p>
                    <h3>Reminders</h3>
                </div>

                <div class="visitors-toolbar">
                    <select class="visitors-toolbar__select" wire:model.live="statusFilter" aria-label="Follow up filter">
                        <option value="pending">Pending</option>
                        <option value="done">Follow up done</option>
                        <option value="">All</option>
                    </select>

                    <label class="master-search">
                        <span class="master-search__icon" aria-hidden="true">
                            @include('partials.icon', ['name' => 'search'])
                        </span>
                        <input
                            type="search"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search reminders..."
                            aria-label="Search reminders"
                        >
                    </label>
                </div>
            </div>

            <div class="table-wrap visitors-table-wrap">
                <table class="data-table visitors-table visitors-table--reminders">
                    <thead>
                        <tr>
                            <th>Remind on</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Purpose</th>
                            <th>Meet with</th>
                            <th>Outcome</th>
                            <th>Meeting remarks</th>
                            <th>Follow up</th>
                            <th class="master-actions-heading">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reminders as $visitor)
                            <tr
                                wire:key="reminder-{{ $visitor->id }}"
                                class="{{ (int) $focusId === (int) $visitor->id ? 'visitors-row--focus' : '' }}"
                            >
                                <td>
                                    <span class="visitors-remind {{ $visitor->remind_on->isToday() || $visitor->remind_on->isPast() ? 'visitors-remind--due' : '' }}">
                                        {{ $visitor->remind_on->format('d M Y') }}
                                    </span>
                                </td>
                                <td><span class="visitors-table__clamp" title="{{ $visitor->name }}">{{ $visitor->name }}</span></td>
                                <td><span class="visitors-table__clamp" title="{{ $visitor->contact_no }}">{{ $visitor->contact_no }}</span></td>
                                <td><span class="visitors-table__clamp" title="{{ $visitor->purpose }}">{{ $visitor->purpose }}</span></td>
                                <td><span class="visitors-table__clamp" title="{{ $visitor->meet_with }}">{{ $visitor->meet_with }}</span></td>
                                <td>
                                    @if ($visitor->outcome === 'mature')
                                        <span class="status status--ready">Mature</span>
                                    @else
                                        <span class="status status--screening">Immature</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($visitor->remarks)
                                        <span class="visitors-table__clamp" title="{{ $visitor->remarks }}">{{ $visitor->remarks }}</span>
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($visitor->follow_up_status === 'done')
                                        <span class="status status--ready">Follow up done</span>
                                    @else
                                        <span class="status status--screening">Pending</span>
                                    @endif
                                    @if ($visitor->follow_up_remarks)
                                        <span class="visitors-table__clamp muted" title="{{ $visitor->follow_up_remarks }}">{{ $visitor->follow_up_remarks }}</span>
                                    @endif
                                </td>
                                <td class="master-row-actions">
                                    @if ($canManage)
                                        <button
                                            type="button"
                                            class="row-action row-action--edit"
                                            wire:click="editFollowUp({{ $visitor->id }})"
                                            title="Update follow up"
                                        >
                                            @include('partials.icon', ['name' => 'pencil'])
                                            <span>Update</span>
                                        </button>
                                        @if ($visitor->follow_up_status !== 'done')
                                            <button
                                                type="button"
                                                class="row-action row-action--edit"
                                                wire:click="markDone({{ $visitor->id }})"
                                                title="Mark follow up done"
                                            >
                                                @include('partials.icon', ['name' => 'check'])
                                                <span>Done</span>
                                            </button>
                                        @endif
                                    @else
                                        <span class="muted">View only</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="muted">No reminders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($reminders)
                {{ $reminders->links() }}
            @endif
        </section>
    @endif
</div>
