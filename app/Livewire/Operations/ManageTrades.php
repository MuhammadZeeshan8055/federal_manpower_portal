<?php

namespace App\Livewire\Operations;

use App\Models\Trade;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ManageTrades extends Component
{
    public bool $denied = false;

    public ?int $editingId = null;

    public string $name = '';

    public bool $is_active = true;

    public string $successMessage = '';

    public int $toastVersion = 0;

    public string $toastType = 'success';

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user || ! $user->canManage('operations', 'trades')) {
            $this->denied = true;
        }
    }

    public function editTrade(int $id): void
    {
        if ($this->denied) {
            return;
        }

        $trade = Trade::query()->find($id);

        if (! $trade) {
            return;
        }

        $this->editingId = $trade->id;
        $this->name = $trade->name;
        $this->is_active = (bool) $trade->is_active;
        $this->successMessage = '';
        $this->resetValidation();
    }

    public function cancelEdit(): void
    {
        $this->successMessage = '';
        $this->resetForm();
    }

    public function deleteTrade(int $id): void
    {
        if ($this->denied) {
            return;
        }

        Trade::query()->whereKey($id)->delete();

        if ($this->editingId === $id) {
            $this->resetForm();
        }

        $this->successMessage = 'Trade deleted.';
        $this->toastType = 'danger';
        $this->toastVersion++;
    }

    public function saveTrade(): void
    {
        if ($this->denied) {
            return;
        }

        $this->validate([
            'name' => ['required', 'string', 'max:160', Rule::unique('trades', 'name')->ignore($this->editingId)],
            'is_active' => ['boolean'],
        ]);

        $data = [
            'name' => trim($this->name),
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            Trade::query()->whereKey($this->editingId)->update($data);
            $this->successMessage = 'Trade updated.';
        } else {
            Trade::query()->create($data);
            $this->successMessage = 'Trade added.';
        }

        $this->toastType = 'success';
        $this->toastVersion++;
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.operations.manage-trades', [
            'trades' => Trade::query()->orderBy('name')->get(),
        ]);
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->reset('name');
        $this->is_active = true;
        $this->resetValidation();
        $this->dispatch('master-form-reset');
    }
}
