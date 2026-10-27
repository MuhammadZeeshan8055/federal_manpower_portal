<?php

namespace App\Livewire\Clients;

use App\Models\Batch;
use App\Support\PortalRefresh;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class ManageBatches extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'portal';

    public bool $denied = false;

    public bool $canManage = false;

    public ?int $editingId = null;

    public string $number = '';

    public bool $is_active = true;

    public string $search = '';

    public string $successMessage = '';

    public int $toastVersion = 0;

    public string $toastType = 'success';

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user
            || (! $user->canView('clients', 'batches')
                && ! $user->canManage('clients', 'batches')
                && ! $user->canView('clients', 'list')
                && ! $user->canManage('clients', 'list'))) {
            $this->denied = true;

            return;
        }

        $this->canManage = $user->canManage('clients', 'batches')
            || $user->canManage('clients', 'list');
    }

    public function updatedSearch(): void
    {
        $this->resetPage('batchesPage');
    }

    public function editBatch(int $id): void
    {
        if ($this->denied || ! $this->canManage) {
            return;
        }

        $batch = Batch::query()->find($id);

        if (! $batch) {
            return;
        }

        $this->editingId = $batch->id;
        $this->number = (string) $batch->number;
        $this->is_active = (bool) $batch->is_active;
        $this->successMessage = '';
        $this->resetValidation();
    }

    public function cancelEdit(): void
    {
        $this->successMessage = '';
        $this->resetForm();
    }

    public function deleteBatch(int $id): void
    {
        if ($this->denied || ! $this->canManage) {
            return;
        }

        Batch::query()->whereKey($id)->delete();

        if ($this->editingId === $id) {
            $this->resetForm();
        }

        $this->successMessage = 'Batch deleted.';
        $this->js(PortalRefresh::jsNotifyListsChanged());
        $this->toastType = 'danger';
        $this->toastVersion++;
        $this->resetPage('batchesPage');
    }

    public function saveBatch(): void
    {
        if ($this->denied || ! $this->canManage) {
            return;
        }

        $this->number = trim($this->number);

        $this->validate([
            'number' => ['required', 'string', 'max:40', Rule::unique('batches', 'number')->ignore($this->editingId)],
            'is_active' => ['boolean'],
        ], [], [
            'number' => 'batch number',
        ]);

        $data = [
            'number' => $this->number,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            Batch::query()->whereKey($this->editingId)->update($data);
            $this->successMessage = 'Batch updated.';
        } else {
            Batch::query()->create($data);
            $this->successMessage = 'Batch added.';
        }

        $this->js(PortalRefresh::jsNotifyListsChanged());
        $this->toastType = 'success';
        $this->toastVersion++;
        $this->resetForm();
        $this->resetPage('batchesPage');
    }

    public function render()
    {
        $query = Batch::query()
            ->withCount('clients')
            ->orderBy('number');

        if (trim($this->search) !== '') {
            $query->where('number', 'like', '%'.trim($this->search).'%');
        }

        return view('livewire.clients.manage-batches', [
            'batches' => $this->denied
                ? null
                : $query->paginate(12, ['*'], 'batchesPage'),
        ]);
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->reset('number');
        $this->is_active = true;
        $this->resetValidation();
        $this->dispatch('master-form-reset');
    }
}
