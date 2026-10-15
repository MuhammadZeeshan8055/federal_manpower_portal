<?php

namespace App\Livewire\Attendance;

use App\Support\DailyAttendanceRows;
use Livewire\Component;

class MyDailyAttendance extends Component
{
    public bool $denied = false;

    public int $year = 0;

    public int $month = 0;

    public function mount(): void
    {
        $this->year = (int) now()->year;
        $this->month = (int) now()->month;

        $user = auth()->user();

        if (! $user || ! $user->canView('attendance', 'my-daily-attendance')) {
            $this->denied = true;

            return;
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
        $rows = [];

        if (! $this->denied && auth()->user()) {
            $rows = DailyAttendanceRows::forUser(auth()->user(), $this->year, $this->month);
        }

        $monthLabel = now()->setDate($this->year, $this->month, 1)->format('F Y');

        return view('livewire.attendance.my-daily-attendance', [
            'rows' => $rows,
            'monthLabel' => $monthLabel,
        ]);
    }
}
