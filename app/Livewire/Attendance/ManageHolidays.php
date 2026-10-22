<?php

namespace App\Livewire\Attendance;

use App\Models\Holiday;
use Livewire\Component;
use Livewire\WithPagination;

class ManageHolidays extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'portal';

    public bool $denied = false;

    public ?int $editingId = null;

    public string $date = '';

    public string $title = '';

    public string $search = '';

    public string $successMessage = '';

    public int $toastVersion = 0;

    public string $toastType = 'success';

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user || ! $user->canManage('attendance', 'holidays')) {
            $this->denied = true;
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage('holidaysPage');
    }

    public function editHoliday(int $id): void
    {
        if ($this->denied) {
            return;
        }

        $holiday = Holiday::query()->find($id);

        if (! $holiday) {
            return;
        }

        $this->editingId = $holiday->id;
        $this->date = $holiday->date->toDateString();
        $this->title = $holiday->title;
        $this->successMessage = '';
        $this->resetValidation();
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
    }

    public function deleteHoliday(int $id): void
    {
        if ($this->denied) {
            return;
        }

        Holiday::query()->whereKey($id)->delete();

        if ($this->editingId === $id) {
            $this->resetForm();
        }

        $this->successMessage = 'Holiday deleted.';
        $this->toastType = 'danger';
        $this->toastVersion++;
        $this->dispatch('refresh-module-stats');
        $this->resetPage('holidaysPage');
    }

    public function saveHoliday(): void
    {
        if ($this->denied) {
            return;
        }

        $this->validate([
            'date' => ['required', 'date'],
            'title' => ['required', 'string', 'max:120'],
        ]);

        $data = [
            'date' => $this->date,
            'title' => trim($this->title),
        ];

        // Same date cannot be used twice
        $exists = Holiday::query()
            ->whereDate('date', $this->date)
            ->when($this->editingId, fn ($q) => $q->where('id', '!=', $this->editingId))
            ->exists();

        if ($exists) {
            $this->addError('date', 'A holiday already exists on this date.');

            return;
        }

        if ($this->editingId) {
            Holiday::query()->whereKey($this->editingId)->update($data);
            $this->successMessage = 'Holiday updated.';
        } else {
            Holiday::query()->create($data);
            $this->successMessage = 'Holiday added.';
        }

        $this->toastType = 'success';
        $this->toastVersion++;
        $this->dispatch('refresh-module-stats');
        $this->resetForm();
        $this->resetPage('holidaysPage');
    }

    public function render()
    {
        $query = Holiday::query()->orderByDesc('date');

        if (trim($this->search) !== '') {
            $term = '%'.trim($this->search).'%';
            $query->where('title', 'like', $term);
        }

        return view('livewire.attendance.manage-holidays', [
            'holidays' => $this->denied ? null : $query->paginate(12, ['*'], 'holidaysPage'),
        ]);
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->reset('date', 'title');
        $this->resetValidation();
        $this->dispatch('master-form-reset');
    }
}
