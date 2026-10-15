<?php

namespace App\Livewire\Attendance;

use App\Models\AttendanceSetting;
use App\Support\AttendancePunch;
use Livewire\Component;

class StartShift extends Component
{
    public string $errorMessage = '';

    public string $infoMessage = '';

    public string $clientIp = '';

    public string $officeStartLabel = '';

    public string $officeEndLabel = '';

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user) {
            $this->redirect(route('login'), navigate: false);

            return;
        }

        // These users do not use the gate
        if ($user->isAdmin() || $user->bypassesAttendance()) {
            $this->redirect(route('dashboard'), navigate: false);

            return;
        }

        if (AttendancePunch::hasStartedToday($user)) {
            $this->redirect(route('dashboard'), navigate: false);

            return;
        }

        $this->clientIp = request()->ip() ?? '';

        $settings = AttendanceSetting::current();
        $this->officeStartLabel = substr((string) $settings->office_start, 0, 5);
        $this->officeEndLabel = substr((string) $settings->office_end, 0, 5);
    }

    public function checkIn(): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        $this->errorMessage = '';
        $this->infoMessage = '';

        $ip = request()->ip() ?? '';
        $result = AttendancePunch::start($user, $ip);

        if (! $result['ok']) {
            $this->errorMessage = $result['message'];

            return;
        }

        $this->redirect(route('dashboard'), navigate: false);
    }

    public function render()
    {
        return view('livewire.attendance.start-shift')
            ->layout('layouts.attendance-gate', ['title' => 'Check in']);
    }
}
