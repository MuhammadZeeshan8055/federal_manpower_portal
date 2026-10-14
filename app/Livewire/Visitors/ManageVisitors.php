<?php

namespace App\Livewire\Visitors;

use App\Models\User;
use App\Models\Visitor;
use Livewire\Component;
use Livewire\WithPagination;

class ManageVisitors extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'portal';

    public bool $denied = false;

    public bool $canManage = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $contact_no = '';

    public string $purpose = '';

    public string $meet_with = '';

    public string $status = 'waiting';

    public string $search = '';

    public string $statusFilter = '';

    // Y-m-d — always filter by one day (default today)
    public string $filterDate = '';

    public string $successMessage = '';

    public int $toastVersion = 0;

    public string $toastType = 'success';

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user || (! $user->canView('visitors', 'list') && ! $user->canManage('visitors', 'list'))) {
            $this->denied = true;

            return;
        }

        $this->canManage = $user->canManage('visitors', 'list');
        $this->filterDate = now()->toDateString();
    }

    public function updatedSearch(): void
    {
        $this->resetPage('visitorsPage');
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage('visitorsPage');
    }

    public function updatedFilterDate(): void
    {
        if ($this->filterDate === '') {
            $this->filterDate = now()->toDateString();
        }

        $this->resetPage('visitorsPage');
    }

    public function showToday(): void
    {
        $this->filterDate = now()->toDateString();
        $this->resetPage('visitorsPage');
    }

    public function editVisitor(int $id): void
    {
        if ($this->denied || ! $this->canManage) {
            return;
        }

        $visitor = Visitor::query()->find($id);

        if (! $visitor) {
            return;
        }

        $this->editingId = $visitor->id;
        $this->name = $visitor->name;
        $this->contact_no = $visitor->contact_no;
        $this->purpose = $visitor->purpose;
        $this->meet_with = $visitor->meet_with;
        $this->status = $visitor->status;
        $this->successMessage = '';
        $this->resetValidation();
    }

    public function cancelEdit(): void
    {
        $this->successMessage = '';
        $this->resetForm();
    }

    public function markStatus(int $id, string $status): void
    {
        if ($this->denied || ! $this->canManage) {
            return;
        }

        if (! array_key_exists($status, Visitor::statusOptions())) {
            return;
        }

        Visitor::query()->whereKey($id)->update(['status' => $status]);

        if ($this->editingId === $id) {
            $this->status = $status;
        }

        $this->successMessage = 'Status updated.';
        $this->toastType = 'success';
        $this->toastVersion++;
        $this->dispatch('refresh-module-stats');
    }

    public function deleteVisitor(int $id): void
    {
        if ($this->denied || ! $this->canManage) {
            return;
        }

        Visitor::query()->whereKey($id)->delete();

        if ($this->editingId === $id) {
            $this->resetForm();
        }

        $this->successMessage = 'Visitor deleted.';
        $this->dispatch('refresh-module-stats');
        $this->toastType = 'danger';
        $this->toastVersion++;
        $this->resetPage('visitorsPage');
    }

    public function saveVisitor(): void
    {
        if ($this->denied || ! $this->canManage) {
            return;
        }

        $this->validate([
            'name' => ['required', 'string', 'max:160'],
            'contact_no' => ['required', 'string', 'max:40'],
            'purpose' => ['required', 'string', 'max:255'],
            'meet_with' => ['required', 'string', 'max:160'],
            'status' => ['required', 'in:'.implode(',', array_keys(Visitor::statusOptions()))],
        ], [], [
            'contact_no' => 'contact no',
            'meet_with' => 'meet with',
        ]);

        $data = [
            'name' => trim($this->name),
            'contact_no' => trim($this->contact_no),
            'purpose' => trim($this->purpose),
            'meet_with' => trim($this->meet_with),
            'status' => $this->status,
        ];

        if ($this->editingId) {
            // Check-in time stays as registered — never overwrite on edit
            Visitor::query()->whereKey($this->editingId)->update($data);
            $this->successMessage = 'Visitor updated.';
        } else {
            $data['visited_at'] = now();
            $data['created_by'] = auth()->id();
            Visitor::query()->create($data);
            $this->filterDate = now()->toDateString();
            $this->successMessage = 'Visitor checked in.';
        }

        $this->dispatch('refresh-module-stats');
        $this->toastType = 'success';
        $this->toastVersion++;
        $this->resetForm();
        $this->resetPage('visitorsPage');
    }

    public function render()
    {
        if ($this->filterDate === '') {
            $this->filterDate = now()->toDateString();
        }

        $query = Visitor::query()
            ->whereDate('visited_at', $this->filterDate)
            ->orderByDesc('visited_at')
            ->orderByDesc('id');

        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }

        if (trim($this->search) !== '') {
            $term = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('contact_no', 'like', $term)
                    ->orWhere('purpose', 'like', $term)
                    ->orWhere('meet_with', 'like', $term);
            });
        }

        return view('livewire.visitors.manage-visitors', [
            'visitors' => $this->denied ? null : $query->paginate(12, ['*'], 'visitorsPage'),
            'statusOptions' => Visitor::statusOptions(),
            'staffNames' => User::query()
                ->where('role', '!=', 'client')
                ->orderBy('name')
                ->pluck('name')
                ->all(),
            'isToday' => $this->filterDate === now()->toDateString(),
        ]);
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->reset('name', 'contact_no', 'purpose', 'meet_with');
        $this->status = 'waiting';
        $this->resetValidation();
        $this->dispatch('master-form-reset');
    }
}
