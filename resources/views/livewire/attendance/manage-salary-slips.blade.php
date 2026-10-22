<div
    class="master-page"
    x-data="{ formOpen: @js((bool) $editingId) }"
    @master-form-reset.window="formOpen = false"
>
    @if ($denied)
        <div class="panel empty-card">
            <h3>No access</h3>
            <p>You do not have permission to view salary slips.</p>
        </div>
    @else
        @if ($canManage)
            <section class="panel master-form-panel master-form-panel--collapsible" :class="{ 'master-form-panel--open': formOpen }">
                @include('partials.master-form-toggle', [
                    'targetId' => 'salary-slip-form-fields',
                    'eyebrow' => $editingId ? 'EDIT' : 'ADD',
                    'title' => $editingId ? 'Edit salary slip' : 'New salary slip',
                    'actionLabel' => 'Add slip',
                ])

                @if ($successMessage !== '')
                    <div
                        class="portal-toast portal-toast--{{ $toastType }}"
                        wire:key="salary-toast-{{ $toastVersion }}"
                        x-data="{ show: true }"
                        x-init="setTimeout(() => show = false, 5000)"
                        x-show="show"
                        role="status"
                    >
                        <span class="portal-toast__copy"><strong>{{ $successMessage }}</strong></span>
                    </div>
                @endif

                <div id="salary-slip-form-fields" class="master-form-collapse" :class="{ 'master-form-collapse--open': formOpen }" :aria-hidden="(!formOpen).toString()" :inert="!formOpen">
                    <div class="master-form-collapse__inner"><div class="master-form-content">
                        <form class="master-form" wire:submit="saveSlip">
                            <label>
                                Staff
                                <select wire:model.live="user_id">
                                    <option value="">Select staff</option>
                                    @foreach ($staffUsers as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                    @endforeach
                                </select>
                                @error('user_id') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            <label>
                                Month
                                <select wire:model.live="month">
                                    @foreach ($months as $num => $label)
                                        <option value="{{ $num }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('month') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            <label>
                                Year
                                <input type="number" wire:model.live="year" min="2020" max="2100" step="1">
                                @error('year') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            <label>
                                Gross amount
                                <input type="number" wire:model.live="gross_amount" min="0" step="0.01" placeholder="0.00">
                                <small class="muted">Auto-filled from staff monthly salary (Settings → Manage Users)</small>
                                @error('gross_amount') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            <label>
                                Unpaid leave days
                                <input type="number" wire:model.live="unpaid_leave_days" min="0" step="0.5">
                                <small class="muted">Auto-filled from approved unpaid leave (you can edit)</small>
                                @error('unpaid_leave_days') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            <label>
                                Deduction per unpaid day
                                <input type="number" wire:model.live="per_day_deduction" min="0" step="0.01" placeholder="0.00">
                                @error('per_day_deduction') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            <label>
                                Other deductions
                                <input type="number" wire:model.live="other_deductions" min="0" step="0.01" placeholder="0.00">
                                @error('other_deductions') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            <label>
                                Notes
                                <textarea class="visitor-textarea" wire:model="notes" rows="2" maxlength="500" placeholder="Optional"></textarea>
                                @error('notes') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            <p class="muted">
                                Unpaid leave deduction: <strong>{{ number_format($unpaidDeductionPreview, 2) }}</strong>
                                · Net pay: <strong>{{ number_format($netPreview, 2) }}</strong>
                            </p>

                            <div class="master-form__actions">
                                <button type="submit" class="button button--soft">
                                    {{ $editingId ? 'Save' : 'Create slip' }}
                                </button>
                                @if ($editingId)
                                    <button type="button" class="button button--ghost" wire:click="cancelEdit" @click="formOpen = false">Cancel</button>
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
                    <p class="panel__eyebrow">HR</p>
                    <h3>Salary slips</h3>
                </div>
                <div class="visitors-toolbar">
                    <select class="visitors-toolbar__select" wire:model.live="filterMonth" aria-label="Month">
                        <option value="">All months</option>
                        @foreach ($months as $num => $label)
                            <option value="{{ $num }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <input class="visitors-toolbar__select" type="number" wire:model.live="filterYear" placeholder="Year" min="2020" max="2100">
                    <label class="master-search">
                        <span class="master-search__icon" aria-hidden="true">@include('partials.icon', ['name' => 'search'])</span>
                        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search staff..." aria-label="Search">
                    </label>
                </div>
            </div>

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Staff</th>
                            <th>Period</th>
                            <th>Gross</th>
                            <th>Unpaid leave</th>
                            <th>Other ded.</th>
                            <th>Net</th>
                            <th class="master-actions-heading">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($slips as $slip)
                            <tr wire:key="slip-{{ $slip->id }}">
                                <td>{{ $slip->user?->name ?? '—' }}</td>
                                <td>{{ $slip->monthLabel() }} {{ $slip->year }}</td>
                                <td>{{ number_format((float) $slip->gross_amount, 2) }}</td>
                                <td>
                                    {{ number_format((float) $slip->unpaid_leave_days, 1) }} days
                                    → {{ number_format((float) $slip->unpaid_leave_deduction, 2) }}
                                </td>
                                <td>{{ number_format((float) $slip->other_deductions, 2) }}</td>
                                <td><strong>{{ number_format((float) $slip->net_amount, 2) }}</strong></td>
                                <td class="master-row-actions">
                                    @if ($canManage)
                                        <button type="button" class="row-action row-action--edit" wire:click="editSlip({{ $slip->id }})" @click="formOpen = true">
                                            @include('partials.icon', ['name' => 'pencil'])
                                            <span>Edit</span>
                                        </button>
                                        <button type="button" class="row-action row-action--delete" wire:click="deleteSlip({{ $slip->id }})" wire:confirm="Delete this salary slip?">
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
                                <td colspan="7" class="muted">No salary slips yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($slips)
                {{ $slips->links() }}
            @endif
        </section>
    @endif
</div>
