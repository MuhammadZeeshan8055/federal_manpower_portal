<?php

namespace App\Support;

use App\Models\LeaveRequest;
use App\Models\User;

class StaffLeave
{
    // Approved leave that covers this calendar date
    public static function hasApprovedLeaveOn(User $user, ?string $date = null): bool
    {
        $date = $date ?: now()->toDateString();

        return LeaveRequest::query()
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereDate('from_date', '<=', $date)
            ->whereDate('to_date', '>=', $date)
            ->exists();
    }

    // Pending or approved leave overlapping this date range (same day not allowed twice)
    public static function overlappingLeave(
        User $user,
        string $fromDate,
        string $toDate,
        ?int $ignoreId = null
    ): ?LeaveRequest {
        $query = LeaveRequest::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->whereDate('from_date', '<=', $toDate)
            ->whereDate('to_date', '>=', $fromDate);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->orderByDesc('id')->first();
    }
}
