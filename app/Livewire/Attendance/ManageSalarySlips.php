<?php

namespace App\Livewire\Attendance;

use App\Models\SalarySlip;
use App\Models\User;
use App\Support\UnpaidLeaveDays;
use Livewire\Component;
use Livewire\WithPagination;

class ManageSalarySlips extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'portal';

    public bool $denied = false;

    public bool $canManage = false;

    public ?int $editingId = null;

    public string $user_id = '';

    public string $month = '';

    public string $year = '';

    public string $gross_amount = '';

    public string $unpaid_leave_days = '0';

    public string $per_day_deduction = '0';

    public string $other_deductions = '0';

    public string $notes = '';

    public string $search = '';

    public string $filterMonth = '';

    public string $filterYear = '';

    public string $successMessage = '';

    public int $toastVersion = 0;

    public string $toastType = 'success';

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user || (! $user->canView('attendance', 'salary-slips') && ! $user->canManage('attendance', 'salary-slips'))) {
            $this->denied = true;

            return;
        }

        $this->canManage = $user->canManage('attendance', 'salary-slips');
        $this->month = (string) now()->month;
        $this->year = (string) now()->year;
        $this->filterMonth = (string) now()->month;
        $this->filterYear = (string) now()->year;
    }

    public function updatedSearch(): void
    {
        $this->resetPage('salarySlipsPage');
    }

    public function updatedFilterMonth(): void
    {
        $this->resetPage('salarySlipsPage');
    }

    public function updatedFilterYear(): void
    {
        $this->resetPage('salarySlipsPage');
    }

    public function updatedUserId(): void
    {
        $this->loadUnpaidDays();
        $this->loadMonthlySalary();
    }

    public function updatedMonth(): void
    {
        $this->loadUnpaidDays();
    }

    public function updatedYear(): void
    {
        $this->loadUnpaidDays();
    }

    public function editSlip(int $id): void
    {
        if ($this->denied || ! $this->canManage) {
            return;
        }

        $slip = SalarySlip::query()->find($id);

        if (! $slip) {
            return;
        }

        $this->editingId = $slip->id;
        $this->user_id = (string) $slip->user_id;
        $this->month = (string) $slip->month;
        $this->year = (string) $slip->year;
        $this->gross_amount = (string) $slip->gross_amount;
        $this->unpaid_leave_days = (string) $slip->unpaid_leave_days;
        $this->per_day_deduction = (string) $slip->per_day_deduction;
        $this->other_deductions = (string) $slip->other_deductions;
        $this->notes = $slip->notes ?? '';
        $this->successMessage = '';
        $this->resetValidation();
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
    }

    public function deleteSlip(int $id): void
    {
        if ($this->denied || ! $this->canManage) {
            return;
        }

        SalarySlip::query()->whereKey($id)->delete();

        if ($this->editingId === $id) {
            $this->resetForm();
        }

        $this->successMessage = 'Salary slip deleted.';
        $this->toastType = 'danger';
        $this->toastVersion++;
        $this->dispatch('refresh-module-stats');
        $this->resetPage('salarySlipsPage');
    }

    public function saveSlip(): void
    {
        if ($this->denied || ! $this->canManage) {
            return;
        }

        $this->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'gross_amount' => ['required', 'numeric', 'min:0'],
            'unpaid_leave_days' => ['required', 'numeric', 'min:0'],
            'per_day_deduction' => ['required', 'numeric', 'min:0'],
            'other_deductions' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [], [
            'user_id' => 'staff',
            'gross_amount' => 'gross amount',
            'unpaid_leave_days' => 'unpaid leave days',
            'per_day_deduction' => 'per day deduction',
            'other_deductions' => 'other deductions',
        ]);

        $gross = (float) $this->gross_amount;
        $unpaidDays = (float) $this->unpaid_leave_days;
        $perDay = (float) $this->per_day_deduction;
        $other = (float) $this->other_deductions;
        $unpaidDeduction = round($unpaidDays * $perDay, 2);
        $net = round($gross - $unpaidDeduction - $other, 2);

        if ($net < 0) {
            $this->addError('gross_amount', 'Net amount cannot be negative. Check deductions.');

            return;
        }

        $exists = SalarySlip::query()
            ->where('user_id', $this->user_id)
            ->where('month', (int) $this->month)
            ->where('year', (int) $this->year)
            ->when($this->editingId, fn ($q) => $q->where('id', '!=', $this->editingId))
            ->exists();

        if ($exists) {
            $this->addError('month', 'A salary slip already exists for this staff and month.');

            return;
        }

        $data = [
            'user_id' => (int) $this->user_id,
            'month' => (int) $this->month,
            'year' => (int) $this->year,
            'gross_amount' => $gross,
            'unpaid_leave_days' => $unpaidDays,
            'per_day_deduction' => $perDay,
            'unpaid_leave_deduction' => $unpaidDeduction,
            'other_deductions' => $other,
            'net_amount' => $net,
            'notes' => trim($this->notes) !== '' ? trim($this->notes) : null,
            'generated_by' => auth()->id(),
        ];

        if ($this->editingId) {
            SalarySlip::query()->whereKey($this->editingId)->update($data);
            $this->successMessage = 'Salary slip updated.';
        } else {
            SalarySlip::query()->create($data);
            $this->successMessage = 'Salary slip created.';
        }

        $this->toastType = 'success';
        $this->toastVersion++;
        $this->dispatch('refresh-module-stats');
        $this->resetForm();
        $this->resetPage('salarySlipsPage');
    }

    public function render()
    {
        $query = SalarySlip::query()
            ->with(['user', 'generator'])
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderByDesc('id');

        // Staff with view-only would only see own — manage users see all
        if (! $this->canManage && auth()->user()) {
            $query->where('user_id', auth()->id());
        }

        if ($this->filterMonth !== '') {
            $query->where('month', (int) $this->filterMonth);
        }

        if ($this->filterYear !== '') {
            $query->where('year', (int) $this->filterYear);
        }

        if (trim($this->search) !== '') {
            $term = '%'.trim($this->search).'%';
            $query->whereHas('user', function ($q) use ($term) {
                $q->where('name', 'like', $term)->orWhere('email', 'like', $term);
            });
        }

        $unpaidDeductionPreview = round(
            (float) $this->unpaid_leave_days * (float) $this->per_day_deduction,
            2
        );
        $netPreview = round(
            (float) $this->gross_amount - $unpaidDeductionPreview - (float) $this->other_deductions,
            2
        );

        return view('livewire.attendance.manage-salary-slips', [
            'slips' => $this->denied ? null : $query->paginate(12, ['*'], 'salarySlipsPage'),
            'staffUsers' => User::query()
                ->officeUsers()
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
            'unpaidDeductionPreview' => $unpaidDeductionPreview,
            'netPreview' => $netPreview,
            'months' => $this->monthOptions(),
        ]);
    }

    private function loadUnpaidDays(): void
    {
        if ($this->user_id === '' || $this->month === '' || $this->year === '') {
            return;
        }

        $staff = User::query()->find((int) $this->user_id);

        if (! $staff) {
            return;
        }

        $days = UnpaidLeaveDays::forUserMonth($staff, (int) $this->month, (int) $this->year);
        $this->unpaid_leave_days = (string) $days;
    }

    private function loadMonthlySalary(): void
    {
        if ($this->user_id === '' || $this->editingId) {
            return;
        }

        $staff = User::query()->find((int) $this->user_id);

        if (! $staff || $staff->monthly_salary === null) {
            return;
        }

        $this->gross_amount = (string) $staff->monthly_salary;
    }

    private function monthOptions(): array
    {
        return [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->user_id = '';
        $this->gross_amount = '';
        $this->unpaid_leave_days = '0';
        $this->per_day_deduction = '0';
        $this->other_deductions = '0';
        $this->notes = '';
        $this->month = (string) now()->month;
        $this->year = (string) now()->year;
        $this->resetValidation();
        $this->dispatch('master-form-reset');
    }
}
