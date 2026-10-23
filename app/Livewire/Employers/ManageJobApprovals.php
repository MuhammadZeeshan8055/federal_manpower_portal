<?php

namespace App\Livewire\Employers;

use App\Models\JobPost;
use Livewire\Component;
use Livewire\WithPagination;

class ManageJobApprovals extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'portal';

    public bool $denied = false;

    public string $filter = 'pending';

    public string $search = '';

    public string $rejectReason = '';

    public ?int $rejectingId = null;

    public string $successMessage = '';

    public int $toastVersion = 0;

    public string $toastType = 'success';

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user || (! $user->canView('employers', 'job-approvals') && ! $user->canManage('employers', 'job-approvals'))) {
            $this->denied = true;
        }
    }

    public function updatedFilter(): void
    {
        $this->resetPage('jobApprovalsPage');
    }

    public function updatedSearch(): void
    {
        $this->resetPage('jobApprovalsPage');
    }

    public function approve(int $id): void
    {
        if ($this->denied || ! auth()->user()->canManage('employers', 'job-approvals')) {
            return;
        }

        $job = JobPost::query()->with('employer')->find($id);

        if (! $job) {
            return;
        }

        if (! $job->employer || ! $job->employer->isApproved()) {
            $this->flashToast('Approve the employer company first.', 'danger');

            return;
        }

        $job->update([
            'status' => 'approved',
            'rejection_reason' => null,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $this->flashToast('Job approved and now live on the website.', 'success');
        $this->dispatch('refresh-module-stats');
    }

    public function startReject(int $id): void
    {
        $this->rejectingId = $id;
        $this->rejectReason = '';
    }

    public function cancelReject(): void
    {
        $this->rejectingId = null;
        $this->rejectReason = '';
    }

    public function reject(): void
    {
        if ($this->denied || ! auth()->user()->canManage('employers', 'job-approvals')) {
            return;
        }

        $this->validate([
            'rejectReason' => ['nullable', 'string', 'max:255'],
            'rejectingId' => ['required', 'integer'],
        ]);

        $job = JobPost::query()->find((int) $this->rejectingId);

        if (! $job) {
            return;
        }

        $reason = trim($this->rejectReason);

        $job->update([
            'status' => 'rejected',
            'rejection_reason' => $reason !== '' ? $reason : null,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $this->rejectingId = null;
        $this->rejectReason = '';
        $this->flashToast('Job rejected.', 'danger');
        $this->dispatch('refresh-module-stats');
    }

    public function render()
    {
        $jobs = null;

        if (! $this->denied) {
            $query = JobPost::query()
                ->with('employer')
                ->orderByDesc('id');

            if ($this->filter !== 'all') {
                $query->where('status', $this->filter);
            }

            if (trim($this->search) !== '') {
                $term = '%'.trim($this->search).'%';
                $query->where(function ($q) use ($term) {
                    $q->where('title', 'like', $term)
                        ->orWhere('country', 'like', $term)
                        ->orWhereHas('employer', function ($employerQuery) use ($term) {
                            $employerQuery->where('company_name', 'like', $term);
                        });
                });
            }

            $jobs = $query->paginate(12, ['*'], 'jobApprovalsPage');
        }

        return view('livewire.employers.manage-job-approvals', [
            'jobs' => $jobs,
            'canManage' => auth()->user()?->canManage('employers', 'job-approvals') ?? false,
        ]);
    }

    private function flashToast(string $message, string $type): void
    {
        $this->successMessage = $message;
        $this->toastType = $type;
        $this->toastVersion++;
    }
}
