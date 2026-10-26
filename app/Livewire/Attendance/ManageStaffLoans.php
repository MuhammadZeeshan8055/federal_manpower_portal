<?php

namespace App\Livewire\Attendance;

use App\Models\StaffLoan;
use App\Models\StaffLoanPayment;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ManageStaffLoans extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'portal';

    public bool $denied = false;

    public string $user_id = '';

    public string $principal = '';

    public string $issued_on = '';

    public string $notes = '';

    public string $return_user_id = '';

    public string $return_amount = '';

    public string $return_paid_on = '';

    public string $return_notes = '';

    public string $search = '';

    public string $filterStaffId = '';

    // '' = all, pending = remaining > 0, cleared = remaining == 0
    public string $filterBalance = '';

    public string $viewMode = 'summary';

    public ?int $detailsUserId = null;

    public string $successMessage = '';

    public int $toastVersion = 0;

    public string $toastType = 'success';

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user || ! $user->canManage('attendance', 'staff-loans')) {
            $this->denied = true;

            return;
        }

        $this->issued_on = now()->toDateString();
        $this->return_paid_on = now()->toDateString();
    }

    public function updatedSearch(): void
    {
        $this->resetPage('loansPage');
    }

    public function updatedFilterStaffId(): void
    {
        $this->resetPage('loansPage');
    }

    public function updatedFilterBalance(): void
    {
        $this->resetPage('loansPage');
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->filterStaffId = '';
        $this->filterBalance = '';
        $this->resetPage('loansPage');
    }

    public function showSummary(): void
    {
        $this->viewMode = 'summary';
        $this->detailsUserId = null;
        $this->resetPage('loansPage');
    }

    public function viewDetails(int $userId): void
    {
        $this->viewMode = 'details';
        $this->detailsUserId = $userId;
        $this->resetPage('loansPage');
    }

    public function saveLoan(): void
    {
        if ($this->denied) {
            return;
        }

        $this->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'principal' => ['required', 'numeric', 'min:1'],
            'issued_on' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [], [
            'user_id' => 'staff',
            'issued_on' => 'issued on',
        ]);

        $noteText = trim($this->notes);

        StaffLoan::query()->create([
            'user_id' => (int) $this->user_id,
            'principal' => (float) $this->principal,
            'issued_on' => $this->issued_on,
            'notes' => $noteText !== '' ? $noteText : null,
            'status' => 'open',
            'created_by' => auth()->id(),
        ]);

        $this->user_id = '';
        $this->principal = '';
        $this->notes = '';
        $this->issued_on = now()->toDateString();
        $this->resetValidation();
        $this->flashToast('Loan added.', 'success');
        $this->dispatch('refresh-module-stats');
        $this->dispatch('loan-form-reset');
    }

    public function saveReturn(): void
    {
        if ($this->denied) {
            return;
        }

        $this->validate([
            'return_user_id' => ['required', 'integer', 'exists:users,id'],
            'return_amount' => ['required', 'numeric', 'min:0.01'],
            'return_paid_on' => ['required', 'date'],
            'return_notes' => ['nullable', 'string', 'max:255'],
        ], [], [
            'return_user_id' => 'staff',
            'return_amount' => 'amount returned',
            'return_paid_on' => 'paid on',
        ]);

        $staffId = (int) $this->return_user_id;
        $amountLeft = round((float) $this->return_amount, 2);
        $pending = $this->staffRemaining($staffId);

        if ($pending <= 0) {
            $this->addError('return_user_id', 'This staff has no pending loan balance.');

            return;
        }

        if ($amountLeft > $pending) {
            $this->addError('return_amount', 'Amount is more than pending balance ('.number_format($pending, 2).').');

            return;
        }

        $noteText = trim($this->return_notes);
        $noteValue = $noteText !== '' ? $noteText : null;

        // Oldest open loans first
        $openLoans = StaffLoan::query()
            ->where('user_id', $staffId)
            ->where('status', 'open')
            ->orderBy('issued_on')
            ->orderBy('id')
            ->get();

        foreach ($openLoans as $loan) {
            if ($amountLeft <= 0) {
                break;
            }

            $loanLeft = $loan->remaining();

            if ($loanLeft <= 0) {
                $loan->refreshStatus();

                continue;
            }

            $payNow = min($amountLeft, $loanLeft);

            StaffLoanPayment::query()->create([
                'staff_loan_id' => $loan->id,
                'amount' => $payNow,
                'paid_on' => $this->return_paid_on,
                'notes' => $noteValue,
                'recorded_by' => auth()->id(),
            ]);

            $loan->refreshStatus();
            $amountLeft = round($amountLeft - $payNow, 2);
        }

        $this->return_user_id = '';
        $this->return_amount = '';
        $this->return_notes = '';
        $this->return_paid_on = now()->toDateString();
        $this->resetValidation();
        $this->flashToast('Loan return recorded.', 'success');
        $this->dispatch('refresh-module-stats');
        $this->dispatch('return-form-reset');
    }

    public function deleteLoan(int $id): void
    {
        if ($this->denied) {
            return;
        }

        StaffLoan::query()->whereKey($id)->delete();

        $this->flashToast('Loan deleted.', 'danger');
        $this->dispatch('refresh-module-stats');
        $this->resetPage('loansPage');
    }

    public function render()
    {
        $summaryRows = [];
        $loans = null;
        $returns = collect();
        $detailsStaffName = null;
        $detailsTotalTaken = 0.0;
        $detailsTotalReturned = 0.0;
        $detailsRemaining = 0.0;
        $collectiveTotalTaken = 0.0;
        $collectiveTotalReturned = 0.0;
        $collectiveRemaining = 0.0;
        $returnPending = 0.0;

        if (! $this->denied) {
            if ($this->viewMode === 'summary') {
                $summaryRows = $this->staffBalanceRows();
                $totalsQuery = StaffLoan::query()->with('payments');

                if ($this->filterStaffId !== '') {
                    $totalsQuery->where('user_id', (int) $this->filterStaffId);
                }

                $allTotals = $this->moneyTotals($totalsQuery->get());
                $collectiveTotalTaken = $allTotals['taken'];
                $collectiveTotalReturned = $allTotals['returned'];
                $collectiveRemaining = $allTotals['remaining'];

                if ($this->filterBalance === 'pending' || $this->filterBalance === 'cleared') {
                    $taken = 0.0;
                    $returned = 0.0;
                    $remaining = 0.0;

                    foreach ($summaryRows as $row) {
                        $taken += $row['total_taken'];
                        $returned += $row['total_paid'];
                        $remaining += $row['remaining'];
                    }

                    $collectiveTotalTaken = round($taken, 2);
                    $collectiveTotalReturned = round($returned, 2);
                    $collectiveRemaining = round($remaining, 2);
                }
            }

            if ($this->viewMode === 'details' && $this->detailsUserId) {
                $staffId = $this->detailsUserId;
                $detailsStaffName = User::query()->whereKey($staffId)->value('name');

                $loans = StaffLoan::query()
                    ->where('user_id', $staffId)
                    ->orderByDesc('issued_on')
                    ->orderByDesc('id')
                    ->paginate(12, ['*'], 'loansPage');

                $staffLoans = StaffLoan::query()
                    ->with('payments')
                    ->where('user_id', $staffId)
                    ->get();

                $staffTotals = $this->moneyTotals($staffLoans);
                $detailsTotalTaken = $staffTotals['taken'];
                $detailsTotalReturned = $staffTotals['returned'];
                $detailsRemaining = $staffTotals['remaining'];

                $loanIds = $staffLoans->pluck('id');
                $returns = StaffLoanPayment::query()
                    ->whereIn('staff_loan_id', $loanIds)
                    ->orderByDesc('paid_on')
                    ->orderByDesc('id')
                    ->get();
            }

            if ($this->return_user_id !== '') {
                $returnPending = $this->staffRemaining((int) $this->return_user_id);
            }
        }

        return view('livewire.attendance.manage-staff-loans', [
            'summaryRows' => $summaryRows,
            'loans' => $loans,
            'returns' => $returns,
            'detailsStaffName' => $detailsStaffName,
            'detailsTotalTaken' => $detailsTotalTaken,
            'detailsTotalReturned' => $detailsTotalReturned,
            'detailsRemaining' => $detailsRemaining,
            'collectiveTotalTaken' => $collectiveTotalTaken,
            'collectiveTotalReturned' => $collectiveTotalReturned,
            'collectiveRemaining' => $collectiveRemaining,
            'returnPending' => $returnPending,
            'staffUsers' => $this->officeUsers(),
            'filterStaffOptions' => $this->filterStaffOptions(),
            'staffWithPending' => $this->staffWithPending(),
        ]);
    }

    private function officeUsers()
    {
        return User::query()
            ->whereNotIn('role', ['client', 'employer'])
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }

    private function filterStaffOptions(): array
    {
        $options = [];

        foreach ($this->staffBalanceRows(false, false) as $row) {
            $options[] = [
                'id' => $row['user_id'],
                'name' => $row['name'],
            ];
        }

        usort($options, fn ($a, $b) => strcmp($a['name'], $b['name']));

        return $options;
    }

    private function flashToast(string $message, string $type): void
    {
        $this->successMessage = $message;
        $this->toastType = $type;
        $this->toastVersion++;
    }

    private function moneyTotals($loans): array
    {
        $taken = 0.0;
        $returned = 0.0;
        $remaining = 0.0;

        foreach ($loans as $loan) {
            $taken += (float) $loan->principal;
            $returned += $loan->totalPaid();
            $remaining += $loan->remaining();
        }

        return [
            'taken' => round($taken, 2),
            'returned' => round($returned, 2),
            'remaining' => round($remaining, 2),
        ];
    }

    private function staffRemaining(int $userId): float
    {
        $openLoans = StaffLoan::query()
            ->where('user_id', $userId)
            ->where('status', 'open')
            ->with('payments')
            ->get();

        return $this->moneyTotals($openLoans)['remaining'];
    }

    private function staffWithPending(): array
    {
        $pending = [];

        foreach ($this->staffBalanceRows(false, false) as $row) {
            if ($row['remaining'] > 0) {
                $pending[] = $row;
            }
        }

        return $pending;
    }

    private function staffBalanceRows(bool $useSearch = true, bool $useFilters = true): array
    {
        $loans = StaffLoan::query()
            ->with(['user', 'payments'])
            ->orderBy('user_id')
            ->get();

        $byStaff = [];

        foreach ($loans as $loan) {
            $id = (int) $loan->user_id;

            if (! isset($byStaff[$id])) {
                $byStaff[$id] = [
                    'user_id' => $id,
                    'name' => $loan->user?->name ?? 'Unknown',
                    'times_taken' => 0,
                    'total_taken' => 0.0,
                    'total_paid' => 0.0,
                    'remaining' => 0.0,
                ];
            }

            $byStaff[$id]['times_taken']++;
            $byStaff[$id]['total_taken'] += (float) $loan->principal;
            $byStaff[$id]['total_paid'] += $loan->totalPaid();
            $byStaff[$id]['remaining'] += $loan->remaining();
        }

        $byStaff = array_values($byStaff);

        if ($useFilters && $this->filterStaffId !== '') {
            $staffId = (int) $this->filterStaffId;
            $byStaff = array_values(array_filter(
                $byStaff,
                fn ($row) => (int) $row['user_id'] === $staffId
            ));
        }

        if ($useFilters && $this->filterBalance === 'pending') {
            $byStaff = array_values(array_filter(
                $byStaff,
                fn ($row) => $row['remaining'] > 0
            ));
        }

        if ($useFilters && $this->filterBalance === 'cleared') {
            $byStaff = array_values(array_filter(
                $byStaff,
                fn ($row) => $row['remaining'] <= 0
            ));
        }

        if ($useSearch && trim($this->search) !== '') {
            $term = strtolower(trim($this->search));
            $byStaff = array_values(array_filter(
                $byStaff,
                fn ($row) => str_contains(strtolower($row['name']), $term)
            ));
        }

        usort($byStaff, function ($a, $b) {
            if ($a['remaining'] == $b['remaining']) {
                return strcmp($a['name'], $b['name']);
            }

            return $b['remaining'] <=> $a['remaining'];
        });

        return $byStaff;
    }
}
