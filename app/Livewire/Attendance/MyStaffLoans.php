<?php

namespace App\Livewire\Attendance;

use App\Models\StaffLoan;
use App\Models\StaffLoanPayment;
use Livewire\Component;
use Livewire\WithPagination;

class MyStaffLoans extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'portal';

    public bool $denied = false;

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user || ! $user->canView('attendance', 'my-loans')) {
            $this->denied = true;
        }
    }

    public function render()
    {
        $loans = null;
        $returns = collect();
        $totalTaken = 0.0;
        $totalReturned = 0.0;
        $remaining = 0.0;

        if (! $this->denied && auth()->user()) {
            $userId = (int) auth()->id();

            $loans = StaffLoan::query()
                ->where('user_id', $userId)
                ->orderByDesc('issued_on')
                ->orderByDesc('id')
                ->paginate(12, ['*'], 'myLoansPage');

            $allLoans = StaffLoan::query()
                ->with('payments')
                ->where('user_id', $userId)
                ->get();

            foreach ($allLoans as $loan) {
                $totalTaken += (float) $loan->principal;
                $totalReturned += $loan->totalPaid();
                $remaining += $loan->remaining();
            }

            $returns = StaffLoanPayment::query()
                ->whereIn('staff_loan_id', $allLoans->pluck('id'))
                ->orderByDesc('paid_on')
                ->orderByDesc('id')
                ->get();
        }

        return view('livewire.attendance.my-staff-loans', [
            'loans' => $loans,
            'returns' => $returns,
            'totalTaken' => round($totalTaken, 2),
            'totalReturned' => round($totalReturned, 2),
            'remaining' => round($remaining, 2),
        ]);
    }
}
