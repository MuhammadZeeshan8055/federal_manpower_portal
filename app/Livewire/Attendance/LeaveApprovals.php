<?php

namespace App\Livewire\Attendance;

use App\Models\LeaveRequest;
use App\Models\UserNotification;
use App\Support\StaffLeave;
use Livewire\Component;
use Livewire\WithPagination;

class LeaveApprovals extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'portal';

    public bool $denied = false;

    public string $statusFilter = 'pending';

    public string $search = '';

    public string $successMessage = '';

    public int $toastVersion = 0;

    public string $toastType = 'success';

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user || ! $user->canManage('attendance', 'leave-approvals')) {
            $this->denied = true;
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage('leaveApprovalsPage');
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage('leaveApprovalsPage');
    }

    public function approve(int $id, bool $isPaid = true): void
    {
        if ($this->denied) {
            return;
        }

        $leave = LeaveRequest::query()->with('user')->find($id);

        if (! $leave || $leave->status !== 'pending') {
            return;
        }

        // Another approved leave already covers these dates
        $clash = StaffLeave::overlappingLeave(
            $leave->user,
            $leave->from_date->toDateString(),
            $leave->to_date->toDateString(),
            $leave->id
        );

        if ($clash && $clash->status === 'approved') {
            $this->successMessage = 'Leave already taken for this date — cannot approve again.';
            $this->toastType = 'danger';
            $this->toastVersion++;

            return;
        }

        $leave->status = 'approved';
        $leave->is_paid = $isPaid;
        $leave->approved_by = auth()->id();
        $leave->save();

        $this->notifyStaff($leave, 'approved');

        $this->successMessage = 'Leave approved.';
        $this->toastType = 'success';
        $this->toastVersion++;
        $this->dispatch('refresh-module-stats');
        // Other open tabs / staff bell picks this up via poll; same browser refreshes now
        $this->js('window.dispatchEvent(new CustomEvent("portal-notify-refresh"))');
    }

    public function reject(int $id): void
    {
        if ($this->denied) {
            return;
        }

        $leave = LeaveRequest::query()->with('user')->find($id);

        if (! $leave || $leave->status !== 'pending') {
            return;
        }

        $leave->status = 'rejected';
        $leave->approved_by = auth()->id();
        $leave->save();

        $this->notifyStaff($leave, 'rejected');

        $this->successMessage = 'Leave rejected.';
        $this->toastType = 'danger';
        $this->toastVersion++;
        $this->dispatch('refresh-module-stats');
        $this->js('window.dispatchEvent(new CustomEvent("portal-notify-refresh"))');
    }

    public function render()
    {
        $query = LeaveRequest::query()
            ->with(['user', 'approver'])
            ->orderByDesc('id');

        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }

        if (trim($this->search) !== '') {
            $term = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($term) {
                $q->where('reason', 'like', $term)
                    ->orWhereHas('user', function ($userQuery) use ($term) {
                        $userQuery->where('name', 'like', $term)
                            ->orWhere('email', 'like', $term);
                    });
            });
        }

        return view('livewire.attendance.leave-approvals', [
            'leaves' => $this->denied ? null : $query->paginate(12, ['*'], 'leaveApprovalsPage'),
            'statusOptions' => LeaveRequest::statusOptions(),
        ]);
    }

    private function notifyStaff(LeaveRequest $leave, string $result): void
    {
        $title = $result === 'approved' ? 'Leave approved' : 'Leave rejected';
        $body = $leave->from_date->format('d M Y').' to '.$leave->to_date->format('d M Y')
            .' — '.$leave->leaveTypeLabel();

        if ($result === 'approved') {
            $body .= $leave->is_paid ? ' (paid)' : ' (unpaid)';
        }

        UserNotification::query()->create([
            'user_id' => $leave->user_id,
            'type' => 'leave_'.$result,
            'title' => $title,
            'body' => $body,
            'module_key' => 'attendance',
            'feature_key' => (string) $leave->id,
            'read_at' => null,
        ]);
    }
}
