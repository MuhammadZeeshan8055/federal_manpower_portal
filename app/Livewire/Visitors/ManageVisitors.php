<?php

namespace App\Livewire\Visitors;

use App\Models\User;
use App\Models\UserNotification;
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

    public string $outcome = 'immature';

    public string $remarks = '';

    public string $remind_on = '';

    public string $search = '';

    public string $outcomeFilter = '';

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

    public function updatedOutcomeFilter(): void
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
        $this->outcome = $visitor->outcome;
        $this->remarks = $visitor->remarks ?? '';
        $this->remind_on = $visitor->remind_on?->toDateString() ?? '';
        $this->successMessage = '';
        $this->resetValidation();
    }

    public function cancelEdit(): void
    {
        $this->successMessage = '';
        $this->resetForm();
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
            'outcome' => ['required', 'in:mature,immature'],
            'remarks' => ['nullable', 'string', 'max:2000'],
            'remind_on' => ['nullable', 'date'],
        ], [], [
            'contact_no' => 'contact no',
            'meet_with' => 'meet with',
            'remind_on' => 'reminder date',
        ]);

        $data = [
            'name' => trim($this->name),
            'contact_no' => trim($this->contact_no),
            'purpose' => trim($this->purpose),
            'meet_with' => trim($this->meet_with),
            'outcome' => $this->outcome,
            'remarks' => trim($this->remarks) !== '' ? trim($this->remarks) : null,
            'remind_on' => $this->remind_on !== '' ? $this->remind_on : null,
        ];

        // New reminder date → follow-up starts as pending again
        if ($data['remind_on']) {
            $data['follow_up_status'] = 'pending';
        }

        if ($this->editingId) {
            $visitor = Visitor::query()->find($this->editingId);

            if (! $visitor) {
                return;
            }

            // Keep follow-up status if reminder date did not change
            if ($visitor->remind_on?->toDateString() === $data['remind_on']) {
                unset($data['follow_up_status']);
            }

            $visitor->update($data);
            $this->successMessage = 'Visitor updated.';
        } else {
            $data['visited_at'] = now();
            $data['created_by'] = auth()->id();
            $visitor = Visitor::query()->create($data);
            $this->filterDate = now()->toDateString();
            $this->successMessage = 'Visitor logged.';
        }

        $this->saveReminderNotification($visitor);

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

        if ($this->outcomeFilter !== '') {
            $query->where('outcome', $this->outcomeFilter);
        }

        if (trim($this->search) !== '') {
            $term = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('contact_no', 'like', $term)
                    ->orWhere('purpose', 'like', $term)
                    ->orWhere('meet_with', 'like', $term)
                    ->orWhere('remarks', 'like', $term);
            });
        }

        return view('livewire.visitors.manage-visitors', [
            'visitors' => $this->denied ? null : $query->paginate(12, ['*'], 'visitorsPage'),
            'outcomeOptions' => Visitor::outcomeOptions(),
            'staffNames' => User::query()
                ->where('role', '!=', 'client')
                ->orderBy('name')
                ->pluck('name')
                ->all(),
            'isToday' => $this->filterDate === now()->toDateString(),
        ]);
    }

    private function saveReminderNotification(Visitor $visitor): void
    {
        if (! $visitor->remind_on) {
            return;
        }

        $remindDate = $visitor->remind_on->toDateString();
        $title = 'Visitor reminder: '.$visitor->name;
        $body = 'Follow up on '.$remindDate
            .' — '.$visitor->purpose
            .' (meet with '.$visitor->meet_with.')';

        $userIds = [];

        if ($visitor->created_by) {
            $userIds[] = (int) $visitor->created_by;
        }

        $meetUser = User::query()
            ->where('role', '!=', 'client')
            ->where('name', $visitor->meet_with)
            ->first();

        if ($meetUser) {
            $userIds[] = (int) $meetUser->id;
        }

        $userIds = array_unique($userIds);

        foreach ($userIds as $userId) {
            UserNotification::query()->updateOrCreate(
                [
                    'user_id' => $userId,
                    'type' => 'visitor_reminder',
                    'module_key' => 'visitors',
                    'feature_key' => (string) $visitor->id,
                ],
                [
                    'title' => $title,
                    'body' => $body,
                    'read_at' => null,
                ]
            );
        }
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->reset('name', 'contact_no', 'purpose', 'meet_with', 'remarks', 'remind_on');
        $this->outcome = 'immature';
        $this->resetValidation();
        $this->dispatch('master-form-reset');
    }
}
