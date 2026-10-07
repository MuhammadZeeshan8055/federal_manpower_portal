<?php

namespace App\Livewire\Operations;

use App\Models\Trade;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class ManageTrades extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'portal';

    public bool $denied = false;

    public ?int $editingId = null;

    public string $name = '';

    public bool $is_active = true;

    public string $search = '';

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

    public function updatedSearch(): void
    {
        $this->resetPage('tradesPage');
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
        $this->resetPage('tradesPage');
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
        $this->resetPage('tradesPage');
    }

    public function render()
    {
        $query = Trade::query()->orderBy('name');

        if (trim($this->search) !== '') {
            $query->where('name', 'like', '%'.trim($this->search).'%');
        }

        return view('livewire.operations.manage-trades', [
            'trades' => $query->paginate(10, ['*'], 'tradesPage'),
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
