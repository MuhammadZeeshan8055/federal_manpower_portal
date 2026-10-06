<?php

namespace App\Livewire\Operations;

use App\Models\CareOff;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ManageCareOffs extends Component
{
    public bool $denied = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $phone = '';

    public string $email = '';

    public bool $is_active = true;

    public string $successMessage = '';

    public int $toastVersion = 0;

    public string $toastType = 'success';

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user || ! $user->canManage('operations', 'care_offs')) {
            $this->denied = true;
        }
    }

    public function editCareOff(int $id): void
    {
        if ($this->denied) {
            return;
        }

        $careOff = CareOff::query()->find($id);

        if (! $careOff) {
            return;
        }

        $this->editingId = $careOff->id;
        $this->name = $careOff->name;
        $this->phone = (string) ($careOff->phone ?? '');
        $this->email = (string) ($careOff->email ?? '');
        $this->is_active = (bool) $careOff->is_active;
        $this->successMessage = '';
        $this->resetValidation();
    }

    public function cancelEdit(): void
    {
        $this->successMessage = '';
        $this->resetForm();
    }

    public function deleteCareOff(int $id): void
    {
        if ($this->denied) {
            return;
        }

        CareOff::query()->whereKey($id)->delete();

        if ($this->editingId === $id) {
            $this->resetForm();
        }

        $this->successMessage = 'Care Off deleted.';
        $this->toastType = 'danger';
        $this->toastVersion++;
    }

    public function saveCareOff(): void
    {
        if ($this->denied) {
            return;
        }

        $this->validate([
            'name' => ['required', 'string', 'max:160', Rule::unique('care_offs', 'name')->ignore($this->editingId)],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:160'],
            'is_active' => ['boolean'],
        ]);

        $data = [
            'name' => trim($this->name),
            'phone' => trim($this->phone) !== '' ? trim($this->phone) : null,
            'email' => trim($this->email) !== '' ? trim($this->email) : null,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            CareOff::query()->whereKey($this->editingId)->update($data);
            $this->successMessage = 'Care Off updated.';
        } else {
            CareOff::query()->create($data);
            $this->successMessage = 'Care Off added.';
        }

        $this->toastType = 'success';
        $this->toastVersion++;
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.operations.manage-care-offs', [
            'careOffs' => CareOff::query()->orderBy('name')->get(),
        ]);
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->reset('name', 'phone', 'email');
        $this->is_active = true;
        $this->resetValidation();
        $this->dispatch('care-off-form-reset');
    }
}
