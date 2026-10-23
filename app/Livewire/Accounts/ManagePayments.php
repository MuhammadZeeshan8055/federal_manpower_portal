<?php

namespace App\Livewire\Accounts;

use App\Models\Client;
use App\Models\Payment;
use App\Models\PaymentAccount;
use App\Models\PaymentMethod;
use App\Support\PaymentReceipts;
use App\Support\PortalRefresh;
use App\Support\WorkspaceCountry;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ManagePayments extends Component
{
    use WithFileUploads;
    use WithPagination;

    protected string $paginationTheme = 'portal';

    public bool $denied = false;

    public bool $canManage = false;

    public ?int $editingId = null;

    public string $client_id = '';

    public string $payment_method_id = '';

    public string $payment_account_id = '';

    public string $amount = '';

    public string $paid_on = '';

    public string $notes = '';

    public $screenshot;

    public string $search = '';

    public string $successMessage = '';

    public int $toastVersion = 0;

    public string $toastType = 'success';

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user || (! $user->canView('accounts', 'payments') && ! $user->canManage('accounts', 'payments'))) {
            $this->denied = true;

            return;
        }

        $this->canManage = $user->canManage('accounts', 'payments');
        $this->paid_on = now()->toDateString();
    }

    public function updatedSearch(): void
    {
        $this->resetPage('paymentsPage');
    }

    public function updatedPaymentMethodId(): void
    {
        $this->payment_account_id = '';
    }

    public function editPayment(int $id): void
    {
        if ($this->denied || ! $this->canManage) {
            return;
        }

        $payment = Payment::query()->find($id);

        if (! $payment) {
            return;
        }

        $this->editingId = $payment->id;
        $this->client_id = (string) $payment->client_id;
        $this->payment_method_id = (string) $payment->payment_method_id;
        $this->payment_account_id = (string) ($payment->payment_account_id ?? '');
        $this->amount = (string) $payment->amount;
        $this->paid_on = $payment->paid_on->toDateString();
        $this->notes = (string) ($payment->notes ?? '');
        $this->screenshot = null;
        $this->successMessage = '';
        $this->resetValidation();
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
    }

    public function deletePayment(int $id): void
    {
        if ($this->denied || ! $this->canManage) {
            return;
        }

        $payment = Payment::query()->find($id);

        if (! $payment) {
            return;
        }

        PaymentReceipts::deleteFolder($payment->id);
        $payment->delete();

        if ($this->editingId === $id) {
            $this->resetForm();
        }

        $this->successMessage = 'Payment deleted.';
        $this->toastType = 'danger';
        $this->toastVersion++;
        $this->dispatch('refresh-module-stats');
        $this->resetPage('paymentsPage');
        $this->js(PortalRefresh::jsNotifyListsChanged());
    }

    public function savePayment(): void
    {
        if ($this->denied || ! $this->canManage) {
            return;
        }

        $maxKb = PaymentReceipts::maxKilobytes();

        $this->validate([
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'payment_method_id' => ['required', 'integer', 'exists:payment_methods,id'],
            'payment_account_id' => ['required', 'integer', 'exists:payment_accounts,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'paid_on' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
            'screenshot' => ['nullable', 'image', 'max:'.$maxKb],
        ], [], [
            'client_id' => 'client',
            'payment_method_id' => 'payment method',
            'payment_account_id' => 'receiving account',
            'paid_on' => 'paid on',
        ]);

        $account = PaymentAccount::query()->find((int) $this->payment_account_id);

        if (! $account || (int) $account->payment_method_id !== (int) $this->payment_method_id) {
            $this->addError('payment_account_id', 'Pick an account that belongs to the selected method.');

            return;
        }

        $noteText = trim($this->notes);

        $data = [
            'client_id' => (int) $this->client_id,
            'payment_method_id' => (int) $this->payment_method_id,
            'payment_account_id' => (int) $this->payment_account_id,
            'amount' => round((float) $this->amount, 2),
            'paid_on' => $this->paid_on,
            'notes' => $noteText !== '' ? $noteText : null,
        ];

        $isNew = ! $this->editingId;

        if ($this->editingId) {
            $payment = Payment::query()->find($this->editingId);

            if (! $payment) {
                return;
            }

            $payment->update($data);
            $this->successMessage = 'Payment updated.';
        } else {
            $data['recorded_by'] = auth()->id();
            $payment = Payment::query()->create($data);
            $this->successMessage = 'Payment added.';
        }

        if ($this->screenshot) {
            PaymentReceipts::delete($payment->screenshot_path);
            $path = PaymentReceipts::store($payment->id, $this->screenshot);
            $payment->update(['screenshot_path' => $path]);
        }

        $receiptUrl = route('accounts.payments.receipt', $payment);

        $this->toastType = 'success';
        $this->toastVersion++;
        $this->reset('screenshot');
        $this->dispatch('refresh-module-stats');
        $this->resetForm();
        $this->resetPage('paymentsPage');
        $this->js(PortalRefresh::jsNotifyListsChanged());

        if ($isNew) {
            $this->js('window.open('.json_encode($receiptUrl).', "_blank")');
        }
    }

    public function render()
    {
        $payments = null;
        $clients = collect();
        $methods = collect();
        $accounts = collect();

        if (! $this->denied) {
            $query = Payment::query()
                ->with(['client', 'method', 'account'])
                ->orderByDesc('paid_on')
                ->orderByDesc('id');

            $countryId = WorkspaceCountry::id();

            if ($countryId) {
                $query->whereHas('client', function ($q) use ($countryId) {
                    $q->where('country_id', $countryId);
                });
            }

            if (trim($this->search) !== '') {
                $term = '%'.trim($this->search).'%';
                $query->where(function ($q) use ($term) {
                    $q->where('notes', 'like', $term)
                        ->orWhereHas('client', function ($clientQuery) use ($term) {
                            $clientQuery->where('full_name', 'like', $term)
                                ->orWhere('cnic', 'like', $term)
                                ->orWhere('passport_number', 'like', $term);
                        })
                        ->orWhereHas('method', function ($methodQuery) use ($term) {
                            $methodQuery->where('name', 'like', $term);
                        })
                        ->orWhereHas('account', function ($accountQuery) use ($term) {
                            $accountQuery->where('name', 'like', $term)
                                ->orWhere('details', 'like', $term);
                        });
                });
            }

            $payments = $query->paginate(12, ['*'], 'paymentsPage');

            $clientQuery = Client::query()->orderBy('full_name');
            WorkspaceCountry::filterClients($clientQuery);
            $clients = $clientQuery->get(['id', 'full_name']);

            $methods = PaymentMethod::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']);

            if ($this->payment_method_id !== '') {
                $accounts = PaymentAccount::query()
                    ->where('payment_method_id', (int) $this->payment_method_id)
                    ->where(function ($q) {
                        $q->where('is_active', true);

                        if ($this->payment_account_id !== '') {
                            $q->orWhere('id', (int) $this->payment_account_id);
                        }
                    })
                    ->orderBy('name')
                    ->get();
            }
        }

        return view('livewire.accounts.manage-payments', [
            'payments' => $payments,
            'clients' => $clients,
            'methods' => $methods,
            'accounts' => $accounts,
        ]);
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->client_id = '';
        $this->payment_method_id = '';
        $this->payment_account_id = '';
        $this->amount = '';
        $this->paid_on = now()->toDateString();
        $this->notes = '';
        $this->reset('screenshot');
        $this->resetValidation();
        $this->dispatch('master-form-reset');
        $this->dispatch('payment-form-reset');
    }
}
