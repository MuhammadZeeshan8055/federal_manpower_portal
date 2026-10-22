<?php

namespace App\Support;

use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;

class UnpaidLeaveDays
{
    // How many unpaid leave days fall inside this month (half day = 0.5)
    public static function forUserMonth(User $user, int $month, int $year): float
    {
        $monthStart = Carbon::create($year, $month, 1)->startOfDay();
        $monthEnd = $monthStart->copy()->endOfMonth()->startOfDay();

        $leaves = LeaveRequest::query()
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('is_paid', false)
            ->whereDate('from_date', '<=', $monthEnd->toDateString())
            ->whereDate('to_date', '>=', $monthStart->toDateString())
            ->get();

        $total = 0.0;

        foreach ($leaves as $leave) {
            if ($leave->leave_type === 'half') {
                $day = $leave->from_date->copy()->startOfDay();

                if ($day->gte($monthStart) && $day->lte($monthEnd)) {
                    $total += 0.5;
                }

                continue;
            }

            $start = $leave->from_date->copy()->startOfDay();
            $end = $leave->to_date->copy()->startOfDay();

            if ($start->lt($monthStart)) {
                $start = $monthStart->copy();
            }

            if ($end->gt($monthEnd)) {
                $end = $monthEnd->copy();
            }

            if ($start->lte($end)) {
                $total += $start->diffInDays($end) + 1;
            }
        }

        return round($total, 2);
    }
}
