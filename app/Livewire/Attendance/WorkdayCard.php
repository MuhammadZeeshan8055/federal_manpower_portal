<?php

namespace App\Livewire\Attendance;

use App\Support\AttendancePunch;
use App\Support\DailyAttendanceRows;
use Livewire\Component;

class WorkdayCard extends Component
{
    public string $message = '';

    public string $messageType = '';

    public function endShift(): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        $this->message = '';
        $this->messageType = '';

        $result = AttendancePunch::end($user, request()->ip() ?? '');

        $this->messageType = $result['ok'] ? 'ok' : 'error';
        $this->message = $result['message'];
    }

    public function render()
    {
        $user = auth()->user();
        $todayDateLabel = now()->format('l, j F Y');
        $checkInLabel = '—';
        $checkOutLabel = '—';
        $workedLabel = '—';
        $shiftOpen = false;
        $shiftDone = false;
        $isExempt = false;
        $canPunch = false;

        if ($user) {
            $isExempt = $user->bypassesAttendance();
            $canPunch = ! $user->isAdmin() && ! $isExempt;

            $today = AttendancePunch::todayRecord($user);

            if ($today && $today->check_in_at) {
                $checkInLabel = $today->check_in_at->format('h:i A');

                if ($today->check_out_at) {
                    $checkOutLabel = $today->check_out_at->format('h:i A');
                    $workedLabel = DailyAttendanceRows::minutesLabel((int) $today->worked_minutes);
                    $shiftDone = true;
                } else {
                    $workedLabel = DailyAttendanceRows::minutesLabel(
                        (int) $today->check_in_at->diffInMinutes(now())
                    );
                    $shiftOpen = true;
                }
            }
        }

        return view('livewire.attendance.workday-card', [
            'todayDateLabel' => $todayDateLabel,
            'checkInLabel' => $checkInLabel,
            'checkOutLabel' => $checkOutLabel,
            'workedLabel' => $workedLabel,
            'shiftOpen' => $shiftOpen,
            'shiftDone' => $shiftDone,
            'isExempt' => $isExempt,
            'canPunch' => $canPunch,
        ]);
    }
}
