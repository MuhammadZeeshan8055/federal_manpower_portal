<?php

namespace App\Livewire\Attendance;

use App\Support\AttendancePunch;
use Livewire\Component;

class EndShiftButton extends Component
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

        $ip = request()->ip() ?? '';
        $result = AttendancePunch::end($user, $ip);

        if (! $result['ok']) {
            $this->messageType = 'error';
            $this->message = $result['message'];

            return;
        }

        $this->messageType = 'ok';
        $this->message = $result['message'];
    }

    public function render()
    {
        $user = auth()->user();
        $showButton = false;
        $showTimes = false;
        $checkInLabel = '—';
        $checkOutLabel = '—';

        if ($user && ! $user->isAdmin() && ! $user->bypassesAttendance()) {
            $row = AttendancePunch::todayRecord($user);

            if ($row && $row->check_in_at) {
                $showTimes = true;
                $checkInLabel = $row->check_in_at->format('h:i A');

                if ($row->check_out_at) {
                    $checkOutLabel = $row->check_out_at->format('h:i A');
                }
            }

            $showButton = AttendancePunch::shiftIsOpen($user);
        }

        return view('livewire.attendance.end-shift-button', [
            'showButton' => $showButton,
            'showTimes' => $showTimes,
            'checkInLabel' => $checkInLabel,
            'checkOutLabel' => $checkOutLabel,
        ]);
    }
}
