<?php

namespace App\Livewire\Visitors;

use App\Models\Visitor;
use Livewire\Component;
use Livewire\WithPagination;

class ManageReminders extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'portal';

    public bool $denied = false;

    public bool $canManage = false;

    public string $search = '';

    public string $statusFilter = 'pending';

    public ?int $focusId = null;

    public ?int $editingId = null;

    public string $follow_up_status = 'pending';

    public string $follow_up_remarks = '';

    public string $successMessage = '';

    public int $toastVersion = 0;

    public string $toastType = 'success';

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user || (! $user->canView('visitors', 'list') && ! $user->canView('visitors', 'reminders'))) {
            $this->denied = true;

            return;
        }

        $this->canManage = $user->canManage('visitors', 'list') || $user->canManage('visitors', 'reminders');
    }

    public function updatedSearch(): void
    {
        $this->resetPage('remindersPage');
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage('remindersPage');
    }

    public function editFollowUp(int $id): void
    {
        if ($this->denied || ! $this->canManage) {
            return;
        }

        $visitor = Visitor::query()->find($id);

        if (! $visitor || ! $visitor->remind_on) {
            return;
        }

        $this->statusFilter = '';
        $this->editingId = $visitor->id;
        $this->focusId = $visitor->id;
        $this->follow_up_status = $visitor->follow_up_status ?: 'pending';
        $this->follow_up_remarks = $visitor->follow_up_remarks ?? '';
        $this->successMessage = '';
        $this->resetValidation();
        $this->resetPage('remindersPage');
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->follow_up_status = 'pending';
        $this->follow_up_remarks = '';
        $this->resetValidation();
    }

    public function saveFollowUp(): void
    {
        if ($this->denied || ! $this->canManage || ! $this->editingId) {
            return;
        }

        $this->validate([
            'follow_up_status' => ['required', 'in:pending,done'],
            'follow_up_remarks' => ['nullable', 'string', 'max:2000'],
        ], [], [
            'follow_up_status' => 'follow up status',
            'follow_up_remarks' => 'follow up remarks',
        ]);

        $visitor = Visitor::query()->find($this->editingId);

        if (! $visitor) {
            return;
        }

        $visitor->update([
            'follow_up_status' => $this->follow_up_status,
            'follow_up_remarks' => trim($this->follow_up_remarks) !== '' ? trim($this->follow_up_remarks) : null,
        ]);

        $this->successMessage = $this->follow_up_status === 'done'
            ? 'Follow up marked as done.'
            : 'Follow up updated.';
        $this->toastType = 'success';
        $this->toastVersion++;
        $this->cancelEdit();
        $this->dispatch('refresh-module-stats');
    }

    public function markDone(int $id): void
    {
        if ($this->denied || ! $this->canManage) {
            return;
        }

        $visitor = Visitor::query()->find($id);

        if (! $visitor || ! $visitor->remind_on) {
            return;
        }

        $visitor->update([
            'follow_up_status' => 'done',
        ]);

        $this->successMessage = 'Follow up marked as done.';
        $this->toastType = 'success';
        $this->toastVersion++;
        $this->dispatch('refresh-module-stats');
    }

    public function render()
    {
        $query = Visitor::query()
            ->whereNotNull('remind_on')
            ->orderBy('remind_on')
            ->orderByDesc('id');

        if ($this->statusFilter === 'pending') {
            $query->where('follow_up_status', 'pending');
        } elseif ($this->statusFilter === 'done') {
            $query->where('follow_up_status', 'done');
        }

        if (trim($this->search) !== '') {
            $term = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('contact_no', 'like', $term)
                    ->orWhere('purpose', 'like', $term)
                    ->orWhere('meet_with', 'like', $term)
                    ->orWhere('remarks', 'like', $term)
                    ->orWhere('follow_up_remarks', 'like', $term);
            });
        }

        return view('livewire.visitors.manage-reminders', [
            'reminders' => $this->denied ? null : $query->paginate(12, ['*'], 'remindersPage'),
            'statusOptions' => Visitor::followUpStatusOptions(),
        ]);
    }
}
