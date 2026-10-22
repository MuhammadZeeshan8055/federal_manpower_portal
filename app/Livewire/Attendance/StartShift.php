<?php

namespace App\Livewire\Attendance;

use App\Models\AttendanceSetting;
use App\Support\AttendancePunch;
use App\Support\StaffLeave;
use Livewire\Component;

class StartShift extends Component
{
    public string $errorMessage = '';

    public string $infoMessage = '';

    public string $clientIp = '';

    public string $officeStartLabel = '';

    public string $officeEndLabel = '';

    public bool $onLeave = false;

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

        // Approved leave → enter portal, no check-in
        if (StaffLeave::hasApprovedLeaveOn($user)) {
            $this->onLeave = true;
            $this->clientIp = request()->ip() ?? '';

            $settings = AttendanceSetting::current();
            $this->officeStartLabel = substr((string) $settings->office_start, 0, 5);
            $this->officeEndLabel = substr((string) $settings->office_end, 0, 5);

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

    public function enterPortal(): void
    {
        $user = auth()->user();

        if (! $user || ! StaffLeave::hasApprovedLeaveOn($user)) {
            return;
        }

        $this->redirect(route('dashboard'), navigate: false);
    }

    public function checkIn(): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        $this->errorMessage = '';
        $this->infoMessage = '';

        if (StaffLeave::hasApprovedLeaveOn($user)) {
            $this->onLeave = true;
            $this->errorMessage = 'You are on leave today. Check-in is not available.';

            return;
        }

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
            ->layout('layouts.attendance-gate', ['title' => $this->onLeave ? 'On leave' : 'Check in']);
    }
}
