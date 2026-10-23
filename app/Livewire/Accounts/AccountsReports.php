<?php

namespace App\Livewire\Accounts;

use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Support\WorkspaceCountry;
use Livewire\Component;

class AccountsReports extends Component
{
    public bool $denied = false;

    public string $date_from = '';

    public string $date_to = '';

    public string $payment_method_id = '';

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user || (! $user->canView('accounts', 'reports') && ! $user->canManage('accounts', 'reports'))) {
            $this->denied = true;

            return;
        }

        $this->date_from = now()->startOfMonth()->toDateString();
        $this->date_to = now()->toDateString();
    }

    public function render()
    {
        $totalAmount = 0.0;
        $paymentCount = 0;
        $rows = [];
        $methods = collect();

        if (! $this->denied) {
            $methods = PaymentMethod::query()->orderBy('name')->get(['id', 'name']);

            $query = Payment::query()->with(['method', 'account']);

            $countryId = WorkspaceCountry::id();

            if ($countryId) {
                $query->whereHas('client', function ($q) use ($countryId) {
                    $q->where('country_id', $countryId);
                });
            }

            if ($this->date_from !== '') {
                $query->whereDate('paid_on', '>=', $this->date_from);
            }

            if ($this->date_to !== '') {
                $query->whereDate('paid_on', '<=', $this->date_to);
            }

            if ($this->payment_method_id !== '') {
                $query->where('payment_method_id', (int) $this->payment_method_id);
            }

            $payments = $query->get();
            $paymentCount = $payments->count();

            foreach ($payments as $payment) {
                $amount = (float) $payment->amount;
                $totalAmount += $amount;

                $methodName = $payment->method?->name ?? 'Unknown';
                $accountName = $payment->account?->label() ?? '—';
                $key = $methodName.'|'.$accountName;

                if (! isset($rows[$key])) {
                    $rows[$key] = [
                        'method' => $methodName,
                        'account' => $accountName,
                        'count' => 0,
                        'total' => 0.0,
                    ];
                }

                $rows[$key]['count']++;
                $rows[$key]['total'] += $amount;
            }

            $rows = array_values($rows);

            usort($rows, function ($a, $b) {
                if ($a['method'] === $b['method']) {
                    return $b['total'] <=> $a['total'];
                }

                return strcmp($a['method'], $b['method']);
            });
        }

        return view('livewire.accounts.accounts-reports', [
            'totalAmount' => round($totalAmount, 2),
            'paymentCount' => $paymentCount,
            'rows' => $rows,
            'methods' => $methods,
        ]);
    }
}
