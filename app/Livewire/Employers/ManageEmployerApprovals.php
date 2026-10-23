<?php

namespace App\Livewire\Employers;

use App\Models\EmployerProfile;
use Livewire\Component;
use Livewire\WithPagination;

class ManageEmployerApprovals extends Component
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

        if (! $user || (! $user->canView('employers', 'approvals') && ! $user->canManage('employers', 'approvals'))) {
            $this->denied = true;
        }
    }

    public function updatedFilter(): void
    {
        $this->resetPage('employerApprovalsPage');
    }

    public function updatedSearch(): void
    {
        $this->resetPage('employerApprovalsPage');
    }

    public function approve(int $id): void
    {
        if ($this->denied || ! auth()->user()->canManage('employers', 'approvals')) {
            return;
        }

        $profile = EmployerProfile::query()->find($id);

        if (! $profile) {
            return;
        }

        $profile->update([
            'status' => 'approved',
            'rejection_reason' => null,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $this->flashToast('Employer approved.', 'success');
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
        if ($this->denied || ! auth()->user()->canManage('employers', 'approvals')) {
            return;
        }

        $this->validate([
            'rejectReason' => ['nullable', 'string', 'max:255'],
            'rejectingId' => ['required', 'integer'],
        ]);

        $profile = EmployerProfile::query()->find((int) $this->rejectingId);

        if (! $profile) {
            return;
        }

        $reason = trim($this->rejectReason);

        $profile->update([
            'status' => 'rejected',
            'rejection_reason' => $reason !== '' ? $reason : null,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $this->rejectingId = null;
        $this->rejectReason = '';
        $this->flashToast('Employer rejected.', 'danger');
        $this->dispatch('refresh-module-stats');
    }

    public function render()
    {
        $profiles = null;

        if (! $this->denied) {
            $query = EmployerProfile::query()
                ->with('user')
                ->orderByDesc('id');

            if ($this->filter !== 'all') {
                $query->where('status', $this->filter);
            }

            if (trim($this->search) !== '') {
                $term = '%'.trim($this->search).'%';
                $query->where(function ($q) use ($term) {
                    $q->where('company_name', 'like', $term)
                        ->orWhere('contact_person', 'like', $term)
                        ->orWhereHas('user', function ($userQuery) use ($term) {
                            $userQuery->where('email', 'like', $term);
                        });
                });
            }

            $profiles = $query->paginate(12, ['*'], 'employerApprovalsPage');
        }

        return view('livewire.employers.manage-employer-approvals', [
            'profiles' => $profiles,
            'canManage' => auth()->user()?->canManage('employers', 'approvals') ?? false,
        ]);
    }

    private function flashToast(string $message, string $type): void
    {
        $this->successMessage = $message;
        $this->toastType = $type;
        $this->toastVersion++;
    }
}
