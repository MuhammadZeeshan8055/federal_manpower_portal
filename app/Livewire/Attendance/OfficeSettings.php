<?php

namespace App\Livewire\Attendance;

use App\Models\AttendanceSetting;
use App\Models\User;
use Livewire\Component;

class OfficeSettings extends Component
{
    public bool $denied = false;

    public string $office_start = '10:00';

    public string $office_end = '18:00';

    public int $required_hours = 8;

    public string $lunch_start = '13:00';

    public string $lunch_end = '14:00';

    public int $break_minutes = 60;

    public int $grace_minutes = 15;

    public string $office_ip_1 = '';

    public string $office_ip_2 = '';

    public string $office_ip_3 = '';

    // User ids marked WFH / attendance exempt
    public array $exemptUserIds = [];

    public string $successMessage = '';

    public int $toastVersion = 0;

    public string $toastType = 'success';

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user || ! $user->canManage('attendance', 'office-settings')) {
            $this->denied = true;

            return;
        }

        $this->loadFromDb();
    }

    public function saveSettings(): void
    {
        if ($this->denied) {
            return;
        }

        $this->validate([
            'office_start' => ['required', 'date_format:H:i'],
            'office_end' => ['required', 'date_format:H:i'],
            'required_hours' => ['required', 'integer', 'min:1', 'max:24'],
            'lunch_start' => ['nullable', 'date_format:H:i'],
            'lunch_end' => ['nullable', 'date_format:H:i'],
            'break_minutes' => ['required', 'integer', 'min:0', 'max:240'],
            'grace_minutes' => ['required', 'integer', 'min:0', 'max:120'],
            'office_ip_1' => ['nullable', 'string', 'max:45'],
            'office_ip_2' => ['nullable', 'string', 'max:45'],
            'office_ip_3' => ['nullable', 'string', 'max:45'],
            'exemptUserIds' => ['array'],
            'exemptUserIds.*' => ['integer'],
        ]);

        $row = AttendanceSetting::current();

        $row->office_start = $this->timeForDb($this->office_start);
        $row->office_end = $this->timeForDb($this->office_end);
        $row->required_hours = $this->required_hours;
        $row->lunch_start = $this->timeForDb($this->lunch_start);
        $row->lunch_end = $this->timeForDb($this->lunch_end);
        $row->break_minutes = $this->break_minutes;
        $row->grace_minutes = $this->grace_minutes;
        $row->office_ip_1 = $this->cleanIp($this->office_ip_1);
        $row->office_ip_2 = $this->cleanIp($this->office_ip_2);
        $row->office_ip_3 = $this->cleanIp($this->office_ip_3);
        $row->save();

        $this->saveExemptUsers();

        $this->toastType = 'success';
        $this->successMessage = 'Office settings saved.';
        $this->toastVersion++;
    }

    public function render()
    {
        $staffUsers = [];

        if (! $this->denied) {
            $staffUsers = User::query()
                ->officeUsers()
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'role']);
        }

        return view('livewire.attendance.office-settings', [
            'staffUsers' => $staffUsers,
        ]);
    }

    private function loadFromDb(): void
    {
        $row = AttendanceSetting::current();

        $this->office_start = $this->timeForInput($row->office_start);
        $this->office_end = $this->timeForInput($row->office_end);
        $this->required_hours = (int) $row->required_hours;
        $this->lunch_start = $this->timeForInput($row->lunch_start);
        $this->lunch_end = $this->timeForInput($row->lunch_end);
        $this->break_minutes = (int) $row->break_minutes;
        $this->grace_minutes = (int) $row->grace_minutes;
        $this->office_ip_1 = (string) ($row->office_ip_1 ?? '');
        $this->office_ip_2 = (string) ($row->office_ip_2 ?? '');
        $this->office_ip_3 = (string) ($row->office_ip_3 ?? '');

        $this->exemptUserIds = [];

        $exemptRows = User::query()
            ->officeUsers()
            ->where('bypass_attendance', true)
            ->get(['id']);

        foreach ($exemptRows as $exempt) {
            $this->exemptUserIds[] = (int) $exempt->id;
        }
    }

    private function saveExemptUsers(): void
    {
        // Clear all office users first, then mark checked ones
        User::query()
            ->officeUsers()
            ->update(['bypass_attendance' => false]);

        if (count($this->exemptUserIds) === 0) {
            return;
        }

        $ids = [];

        foreach ($this->exemptUserIds as $id) {
            $ids[] = (int) $id;
        }

        User::query()
            ->officeUsers()
            ->whereIn('id', $ids)
            ->update(['bypass_attendance' => true]);
    }

    private function timeForInput($value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        return substr((string) $value, 0, 5);
    }

    private function timeForDb($value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (strlen($value) === 5) {
            return $value.':00';
        }

        return $value;
    }

    private function cleanIp(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        return $value;
    }
}
