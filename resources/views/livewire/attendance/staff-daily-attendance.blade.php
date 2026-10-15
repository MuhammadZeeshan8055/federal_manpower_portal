<div class="master-page">
    @if ($denied)
        <div class="panel empty-card">
            <h3>No access</h3>
            <p>Only Admin or HR can view staff attendance.</p>
        </div>
    @else
        <section class="panel master-form-panel staff-att-picker">
            <div class="panel__header">
                <div>
                    <p class="panel__eyebrow">HR</p>
                    <h3>Staff attendance</h3>
                </div>
            </div>

            <div class="master-form-content">
                <div class="master-form">
                    <label>
                        Staff member
                        <select wire:model.live="selectedUserId">
                            <option value="">Select staff…</option>
                            @foreach ($staffUsers as $staff)
                                <option value="{{ $staff->id }}">
                                    {{ $staff->name }} ({{ $staff->roleLabel() }})
                                </option>
                            @endforeach
                        </select>
                    </label>
                </div>
            </div>
        </section>

        <section class="panel master-list-panel">
            <div class="panel__header attendance-month-head">
                <div>
                    <p class="panel__eyebrow">MONTH VIEW</p>
                    <h3>
                        {{ $monthLabel }}
                        @if ($selectedName !== '')
                            <span class="staff-att-selected">— {{ $selectedName }} ({{ $selectedRole }})</span>
                        @endif
                    </h3>
                </div>
                <div class="attendance-month-nav">
                    <button type="button" class="button button--ghost" wire:click="prevMonth" wire:loading.attr="disabled">← Prev</button>
                    <button type="button" class="button button--ghost" wire:click="goThisMonth" wire:loading.attr="disabled">This month</button>
                    <button type="button" class="button button--ghost" wire:click="nextMonth" wire:loading.attr="disabled">Next →</button>
                </div>
            </div>

            @if (! $selectedUserId)
                <div class="panel empty-card staff-att-empty">
                    <h3>Pick a staff member</h3>
                    <p>Choose someone above to see their daily attendance.</p>
                </div>
            @else
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Worked</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rows as $row)
                                <tr
                                    wire:key="staff-att-{{ $selectedUserId }}-{{ $row['date_key'] }}"
                                    class="{{ $row['is_today'] ? 'attendance-row--today' : '' }}"
                                >
                                    <td>
                                        {{ $row['date_label'] }}
                                        @if ($row['is_today'])
                                            <span class="status status--ready">Today</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $row['check_in'] }}
                                        @if ($row['is_late'])
                                            <span class="att-badge att-badge--late">Late</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $row['check_out'] }}
                                        @if ($row['is_early'])
                                            <span class="att-badge att-badge--early">Early</span>
                                        @endif
                                    </td>
                                    <td>{{ $row['worked'] }}</td>
                                    <td>
                                        <span class="att-status att-status--{{ $row['status_class'] }}">
                                            {{ $row['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">No days in this month.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    @endif
</div>
