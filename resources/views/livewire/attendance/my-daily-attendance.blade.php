<div class="master-page">
    @if ($denied)
        <div class="panel empty-card">
            <h3>No access</h3>
            <p>You do not have permission to view attendance.</p>
        </div>
    @else
        <section class="panel master-list-panel">
            <div class="panel__header attendance-month-head">
                <div>
                    <p class="panel__eyebrow">MY ATTENDANCE</p>
                    <h3>{{ $monthLabel }}</h3>
                </div>
                <div class="attendance-month-nav">
                    <button type="button" class="button button--ghost" wire:click="prevMonth" wire:loading.attr="disabled">← Prev</button>
                    <button type="button" class="button button--ghost" wire:click="goThisMonth" wire:loading.attr="disabled">This month</button>
                    <button type="button" class="button button--ghost" wire:click="nextMonth" wire:loading.attr="disabled">Next →</button>
                </div>
            </div>

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
                        @foreach ($rows as $row)
                            <tr
                                wire:key="att-{{ $row['date_key'] }}"
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif
</div>
