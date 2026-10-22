<?php

namespace App\Support;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSetting;
use App\Models\User;
use Carbon\Carbon;

class AttendancePunch
{
    // No office IPs saved → allow. If any IP is set → must match.
    public static function ipIsAllowed(string $clientIp): bool
    {
        $settings = AttendanceSetting::current();

        $allowed = [];

        if ($settings->office_ip_1) {
            $allowed[] = trim($settings->office_ip_1);
        }

        if ($settings->office_ip_2) {
            $allowed[] = trim($settings->office_ip_2);
        }

        if ($settings->office_ip_3) {
            $allowed[] = trim($settings->office_ip_3);
        }

        if (count($allowed) === 0) {
            return true;
        }

        foreach ($allowed as $ip) {
            if ($ip === $clientIp) {
                return true;
            }
        }

        return false;
    }

    public static function todayRecord(User $user): ?AttendanceRecord
    {
        return AttendanceRecord::query()
            ->where('user_id', $user->id)
            ->whereDate('work_date', now()->toDateString())
            ->first();
    }

    // Already checked in today (even if ended)
    public static function hasStartedToday(User $user): bool
    {
        $row = self::todayRecord($user);

        return $row && $row->check_in_at;
    }

    // Checked in, not yet checked out
    public static function shiftIsOpen(User $user): bool
    {
        $row = self::todayRecord($user);

        return $row && $row->check_in_at && ! $row->check_out_at;
    }

    // Start shift. Returns ['ok' => bool, 'message' => string]
    public static function start(User $user, string $clientIp): array
    {
        if ($user->bypassesAttendance()) {
            return [
                'ok' => false,
                'message' => 'Your account is marked WFH exempt. No check-in needed.',
            ];
        }

        if (StaffLeave::hasApprovedLeaveOn($user)) {
            return [
                'ok' => false,
                'message' => 'You are on leave today. Check-in is not available.',
            ];
        }

        if (self::hasStartedToday($user)) {
            return [
                'ok' => false,
                'message' => 'You already checked in today.',
            ];
        }

        if (! self::ipIsAllowed($clientIp)) {
            return [
                'ok' => false,
                'message' => 'Your IP ('.$clientIp.') is not allowed. Use office Wi‑Fi or ask admin to add your IP.',
            ];
        }

        $settings = AttendanceSetting::current();
        $now = now();
        $isLate = self::isLateNow($settings, $now);

        AttendanceRecord::query()->create([
            'user_id' => $user->id,
            'work_date' => $now->toDateString(),
            'check_in_at' => $now,
            'check_out_at' => null,
            'worked_minutes' => 0,
            'status_color' => $isLate ? 'amber' : 'green',
            'is_late' => $isLate,
            'is_early' => false,
        ]);

        return [
            'ok' => true,
            'message' => $isLate ? 'Checked in (late).' : 'Checked in. Welcome.',
        ];
    }

    // End shift. Returns ['ok' => bool, 'message' => string]
    public static function end(User $user, string $clientIp): array
    {
        $row = self::todayRecord($user);

        if (! $row || ! $row->check_in_at) {
            return [
                'ok' => false,
                'message' => 'No open shift to end.',
            ];
        }

        if ($row->check_out_at) {
            return [
                'ok' => false,
                'message' => 'You already ended your shift today.',
            ];
        }

        if (! self::ipIsAllowed($clientIp)) {
            return [
                'ok' => false,
                'message' => 'Your IP ('.$clientIp.') is not allowed. Use office Wi‑Fi or ask admin to add your IP.',
            ];
        }

        $settings = AttendanceSetting::current();
        $now = now();
        $isEarly = self::isEarlyNow($settings, $now);

        $checkIn = Carbon::parse($row->check_in_at);
        $worked = (int) $checkIn->diffInMinutes($now);

        $row->check_out_at = $now;
        $row->worked_minutes = $worked;
        $row->is_early = $isEarly;
        $row->status_color = $isEarly ? 'amber' : 'green';
        $row->save();

        return [
            'ok' => true,
            'message' => $isEarly ? 'Shift ended (early leave).' : 'Shift ended. Good work.',
        ];
    }

    private static function isLateNow(AttendanceSetting $settings, Carbon $now): bool
    {
        $start = Carbon::parse($now->toDateString().' '.$settings->office_start);
        $lateAfter = $start->copy()->addMinutes((int) $settings->grace_minutes);

        return $now->gt($lateAfter);
    }

    private static function isEarlyNow(AttendanceSetting $settings, Carbon $now): bool
    {
        $end = Carbon::parse($now->toDateString().' '.$settings->office_end);
        $earlyBefore = $end->copy()->subMinutes((int) $settings->grace_minutes);

        return $now->lt($earlyBefore);
    }
}
