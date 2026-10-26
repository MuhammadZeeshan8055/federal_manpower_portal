<?php

namespace App\Livewire\Attendance;

use App\Models\User;
use App\Support\DailyAttendanceRows;
use Livewire\Component;

class StaffDailyAttendance extends Component
{
    public bool $denied = false;

    public ?int $selectedUserId = null;

    public int $year = 0;

    public int $month = 0;

    public function mount(): void
    {
        $this->year = (int) now()->year;
        $this->month = (int) now()->month;

        $user = auth()->user();

        if (! $user || ! $user->canView('attendance', 'staff-daily-attendance')) {
            $this->denied = true;

            return;
        }

        // Only Admin / HR should use this screen
        if (! $user->isAdmin() && ! $user->isHr()) {
            $this->denied = true;

            return;
        }

        $first = $this->officeUsersQuery()->first();

        if ($first) {
            $this->selectedUserId = (int) $first->id;
        }
    }

    public function updatedSelectedUserId(): void
    {
        if ($this->selectedUserId) {
            $this->selectedUserId = (int) $this->selectedUserId;
        }
    }

    public function prevMonth(): void
    {
        $date = now()->setDate($this->year, $this->month, 1)->subMonth();
        $this->year = (int) $date->year;
        $this->month = (int) $date->month;
    }

    public function nextMonth(): void
    {
        $date = now()->setDate($this->year, $this->month, 1)->addMonth();
        $this->year = (int) $date->year;
        $this->month = (int) $date->month;
    }

    public function goThisMonth(): void
    {
        $this->year = (int) now()->year;
        $this->month = (int) now()->month;
    }

    public function render()
    {
        $staffUsers = [];
        $rows = [];
        $selectedName = '';
        $selectedRole = '';

        if (! $this->denied) {
            $staffUsers = $this->officeUsersQuery()->get();

            if ($this->selectedUserId) {
                $selected = null;

                foreach ($staffUsers as $staff) {
                    if ((int) $staff->id === (int) $this->selectedUserId) {
                        $selected = $staff;
                        break;
                    }
                }

                if ($selected) {
                    $selectedName = $selected->name;
                    $selectedRole = $selected->roleLabel();
                    $rows = DailyAttendanceRows::forUser($selected, $this->year, $this->month);
                }
            }
        }

        $monthLabel = now()->setDate($this->year, $this->month, 1)->format('F Y');

        return view('livewire.attendance.staff-daily-attendance', [
            'staffUsers' => $staffUsers,
            'rows' => $rows,
            'monthLabel' => $monthLabel,
            'selectedName' => $selectedName,
            'selectedRole' => $selectedRole,
        ]);
    }

    private function officeUsersQuery()
    {
        return User::query()
            ->whereNotIn('role', ['client', 'employer'])
            ->orderBy('name');
    }
}
