<?php

namespace App\Livewire\Operations;

use App\Models\ProcessStatus;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ManageProcessStatuses extends Component
{
    public bool $denied = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $sort_order = '0';

    public bool $is_active = true;

    public string $successMessage = '';

    public int $toastVersion = 0;

    public string $toastType = 'success';

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user || ! $user->canManage('operations', 'process_statuses')) {
            $this->denied = true;
        }
    }

    public function editProcessStatus(int $id): void
    {
        if ($this->denied) {
            return;
        }

        $status = ProcessStatus::query()->find($id);

        if (! $status) {
            return;
        }

        $this->editingId = $status->id;
        $this->name = $status->name;
        $this->sort_order = (string) $status->sort_order;
        $this->is_active = (bool) $status->is_active;
        $this->successMessage = '';
        $this->resetValidation();
    }

    public function cancelEdit(): void
    {
        $this->successMessage = '';
        $this->resetForm();
    }

    public function deleteProcessStatus(int $id): void
    {
        if ($this->denied) {
            return;
        }

        ProcessStatus::query()->whereKey($id)->delete();

        if ($this->editingId === $id) {
            $this->resetForm();
        }

        $this->successMessage = 'Process status deleted.';
        $this->toastType = 'danger';
        $this->toastVersion++;
    }

    public function saveProcessStatus(): void
    {
        if ($this->denied) {
            return;
        }

        $this->validate([
            'name' => ['required', 'string', 'max:160', Rule::unique('process_statuses', 'name')->ignore($this->editingId)],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['boolean'],
        ]);

        $data = [
            'name' => trim($this->name),
            'sort_order' => (int) $this->sort_order,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            ProcessStatus::query()->whereKey($this->editingId)->update($data);
            $this->successMessage = 'Process status updated.';
        } else {
            ProcessStatus::query()->create($data);
            $this->successMessage = 'Process status added.';
        }

        $this->toastType = 'success';
        $this->toastVersion++;
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.operations.manage-process-statuses', [
            'processStatuses' => ProcessStatus::query()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
        ]);
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->reset('name');
        $this->sort_order = '0';
        $this->is_active = true;
        $this->resetValidation();
        $this->dispatch('master-form-reset');
    }
}
