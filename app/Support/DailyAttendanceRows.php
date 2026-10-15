<?php

namespace App\Support;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSetting;
use App\Models\Holiday;
use App\Models\User;
use Carbon\Carbon;

class DailyAttendanceRows
{
    // Build one month of rows for a user (simple list, easy to change)
    public static function forUser(User $user, int $year, int $month): array
    {
        $settings = AttendanceSetting::current();
        $requiredMinutes = ((int) $settings->required_hours) * 60;

        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end = $start->copy()->endOfMonth();
        $todayCarbon = now()->startOfDay();
        $today = $todayCarbon->toDateString();

        // Current (or future) month: only show up to today — no empty future rows
        if ($end->gt($todayCarbon)) {
            $end = $todayCarbon->copy();
        }

        // Whole month is still in the future
        if ($start->gt($end)) {
            return [];
        }

        // Holidays in this month
        $holidayMap = [];
        $holidays = Holiday::query()
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get();

        foreach ($holidays as $holiday) {
            $holidayMap[$holiday->date->toDateString()] = $holiday->title;
        }

        // Attendance rows this month
        $recordMap = [];
        $records = AttendanceRecord::query()
            ->where('user_id', $user->id)
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->get();

        foreach ($records as $record) {
            $recordMap[$record->work_date->toDateString()] = $record;
        }

        $rows = [];
        $day = $start->copy();

        while ($day->lte($end)) {
            $dateKey = $day->toDateString();
            $isSunday = $day->isSunday();
            $isFuture = $dateKey > $today;
            $isToday = $dateKey === $today;
            $holidayTitle = $holidayMap[$dateKey] ?? null;
            $record = $recordMap[$dateKey] ?? null;

            $row = self::buildOneDay(
                $day,
                $user,
                $record,
                $holidayTitle,
                $isSunday,
                $isFuture,
                $isToday,
                $requiredMinutes
            );

            $rows[] = $row;
            $day->addDay();
        }

        return $rows;
    }

    private static function buildOneDay(
        Carbon $day,
        User $user,
        ?AttendanceRecord $record,
        ?string $holidayTitle,
        bool $isSunday,
        bool $isFuture,
        bool $isToday,
        int $requiredMinutes
    ): array {
        $checkIn = '—';
        $checkOut = '—';
        $worked = '—';
        $isLate = false;
        $isEarly = false;
        $status = '—';
        $statusClass = 'muted';

        // Order: Sunday / Holiday → real punch wins → then WFH / future / absent
        if ($isSunday) {
            $status = 'Sunday';
            $statusClass = 'off';
        } elseif ($holidayTitle) {
            $status = 'Holiday';
            $statusClass = 'holiday';
        } elseif ($record && $record->check_in_at) {
            $checkIn = $record->check_in_at->format('h:i A');
            $isLate = (bool) $record->is_late;

            if ($record->check_out_at) {
                $checkOut = $record->check_out_at->format('h:i A');
                $isEarly = (bool) $record->is_early;
                $worked = self::minutesLabel((int) $record->worked_minutes);

                if ((int) $record->worked_minutes >= $requiredMinutes) {
                    $status = 'Hours met';
                    $statusClass = 'ok';
                } else {
                    $status = 'Short';
                    $statusClass = 'short';
                }
            } elseif ($isToday) {
                $worked = self::minutesLabel(
                    (int) $record->check_in_at->diffInMinutes(now())
                );
                $status = 'Running';
                $statusClass = 'running';
            } else {
                $status = 'No check-out';
                $statusClass = 'short';
            }
        } elseif ($user->bypassesAttendance()) {
            $status = 'Exempt (WFH)';
            $statusClass = 'exempt';
        } elseif ($isFuture) {
            $status = '—';
            $statusClass = 'muted';
        } else {
            $status = 'Absent';
            $statusClass = 'absent';
        }

        return [
            'date_key' => $day->toDateString(),
            'date_label' => $day->format('D, j M'),
            'is_today' => $isToday,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'worked' => $worked,
            'is_late' => $isLate,
            'is_early' => $isEarly,
            'status' => $status,
            'status_class' => $statusClass,
        ];
    }

    public static function minutesLabel(int $minutes): string
    {
        if ($minutes < 0) {
            $minutes = 0;
        }

        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        return $hours.'h '.$mins.'m';
    }
}
