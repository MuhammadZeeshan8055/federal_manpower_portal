<?php

namespace App\Livewire\Attendance;

use App\Models\LeaveRequest;
use App\Models\User;
use App\Models\UserNotification;
use App\Support\StaffLeave;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class ApplyLeave extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'portal';

    public bool $denied = false;

    public string $from_date = '';

    public string $to_date = '';

    public string $leave_type = 'full';

    public string $reason = '';

    public string $successMessage = '';

    public int $toastVersion = 0;

    public string $toastType = 'success';

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user || (! $user->canView('attendance', 'apply-leave') && ! $user->canManage('attendance', 'apply-leave'))) {
            $this->denied = true;

            return;
        }

        $this->from_date = now()->toDateString();
        $this->to_date = now()->toDateString();
    }

    public function updatedLeaveType(): void
    {
        // Half day is always one date only
        if ($this->leave_type === 'half') {
            $this->to_date = $this->from_date;
        }
    }

    public function updatedFromDate(): void
    {
        if ($this->leave_type === 'half') {
            $this->to_date = $this->from_date;
        }

        // Keep range valid when changing from-date
        if ($this->to_date !== '' && $this->to_date < $this->from_date) {
            $this->to_date = $this->from_date;
        }
    }

    public function saveLeave(): void
    {
        if ($this->denied) {
            return;
        }

        $user = auth()->user();

        if (! $user) {
            return;
        }

        // Half day = exactly 1 day (no from–to range)
        if ($this->leave_type === 'half') {
            $this->to_date = $this->from_date;
        }

        $rules = [
            'from_date' => ['required', 'date'],
            'leave_type' => ['required', 'in:full,half'],
            'reason' => ['nullable', 'string', 'max:255'],
        ];

        if ($this->leave_type === 'full') {
            $rules['to_date'] = ['required', 'date', 'after_or_equal:from_date'];
        }

        $this->validate($rules, [], [
            'from_date' => $this->leave_type === 'half' ? 'date' : 'from date',
            'to_date' => 'to date',
            'leave_type' => 'leave type',
        ]);

        $existing = StaffLeave::overlappingLeave($user, $this->from_date, $this->to_date);

        if ($existing) {
            $range = $existing->from_date->format('d M Y');

            if (! $existing->from_date->equalTo($existing->to_date)) {
                $range .= ' to '.$existing->to_date->format('d M Y');
            }

            $this->addError(
                'from_date',
                'Leave already taken for this date ('.$existing->statusLabel().' — '.$range.').'
            );

            return;
        }

        $days = $this->countLeaveDays($this->from_date, $this->to_date, $this->leave_type);

        $leave = LeaveRequest::query()->create([
            'user_id' => $user->id,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
            'leave_type' => $this->leave_type,
            'full_days' => $days['full_days'],
            'half_days' => $days['half_days'],
            'reason' => trim($this->reason) !== '' ? trim($this->reason) : null,
            'status' => 'pending',
            'is_paid' => true,
            'approved_by' => null,
        ]);

        $this->notifyApprovers($leave, $user->name);

        $this->successMessage = 'Leave request submitted.';
        $this->toastType = 'success';
        $this->toastVersion++;
        $this->reason = '';
        $this->leave_type = 'full';
        $this->from_date = now()->toDateString();
        $this->to_date = now()->toDateString();
        $this->resetValidation();
        $this->resetPage('myLeavePage');
        $this->dispatch('refresh-module-stats');
        $this->js('window.dispatchEvent(new CustomEvent("portal-notify-refresh"))');
    }

    public function cancelLeave(int $id): void
    {
        if ($this->denied) {
            return;
        }

        $user = auth()->user();

        if (! $user) {
            return;
        }

        $leave = LeaveRequest::query()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->whereKey($id)
            ->first();

        if (! $leave) {
            return;
        }

        $leave->status = 'cancelled';
        $leave->save();

        $this->successMessage = 'Leave request cancelled.';
        $this->toastType = 'danger';
        $this->toastVersion++;
        $this->dispatch('refresh-module-stats');
    }

    public function render()
    {
        $myLeaves = null;

        if (! $this->denied && auth()->user()) {
            $myLeaves = LeaveRequest::query()
                ->where('user_id', auth()->id())
                ->orderByDesc('id')
                ->paginate(10, ['*'], 'myLeavePage');
        }

        return view('livewire.attendance.apply-leave', [
            'myLeaves' => $myLeaves,
        ]);
    }

    // Full day = every calendar day from → to (inclusive). Half day = 1 half.
    private function countLeaveDays(string $from, string $to, string $type): array
    {
        if ($type === 'half') {
            return [
                'full_days' => 0,
                'half_days' => 1,
            ];
        }

        $start = Carbon::parse($from)->startOfDay();
        $end = Carbon::parse($to)->startOfDay();
        $fullDays = $start->diffInDays($end) + 1;

        return [
            'full_days' => $fullDays,
            'half_days' => 0,
        ];
    }

    private function notifyApprovers(LeaveRequest $leave, string $staffName): void
    {
        $approvers = User::query()
            ->whereIn('role', ['super_admin', 'admin', 'hr'])
            ->get();

        $title = 'Leave request: '.$staffName;
        $body = $leave->from_date->format('d M Y').' to '.$leave->to_date->format('d M Y')
            .' ('.$leave->leaveTypeLabel().')';

        foreach ($approvers as $approver) {
            UserNotification::query()->create([
                'user_id' => $approver->id,
                'type' => 'leave_request',
                'title' => $title,
                'body' => $body,
                'module_key' => 'attendance',
                'feature_key' => (string) $leave->id,
                'read_at' => null,
            ]);
        }
    }
}
