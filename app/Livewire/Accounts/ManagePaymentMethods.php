<?php

namespace App\Livewire\Accounts;

use App\Models\PaymentAccount;
use App\Models\PaymentMethod;
use Livewire\Component;

class ManagePaymentMethods extends Component
{
    public bool $denied = false;

    public bool $canManage = false;

    // Method form
    public ?int $editingMethodId = null;

    public string $method_name = '';

    public bool $method_active = true;

    // Account form
    public ?int $editingAccountId = null;

    public string $account_method_id = '';

    public string $account_name = '';

    public string $account_details = '';

    public bool $account_active = true;

    public string $search = '';

    public string $successMessage = '';

    public int $toastVersion = 0;

    public string $toastType = 'success';

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user || (! $user->canView('accounts', 'payment_methods') && ! $user->canManage('accounts', 'payment_methods'))) {
            $this->denied = true;

            return;
        }

        $this->canManage = $user->canManage('accounts', 'payment_methods');
    }

    public function editMethod(int $id): void
    {
        if ($this->denied || ! $this->canManage) {
            return;
        }

        $method = PaymentMethod::query()->find($id);

        if (! $method) {
            return;
        }

        $this->editingMethodId = $method->id;
        $this->method_name = $method->name;
        $this->method_active = (bool) $method->is_active;
        $this->successMessage = '';
        $this->resetValidation();
    }

    public function cancelMethodEdit(): void
    {
        $this->resetMethodForm();
    }

    public function deleteMethod(int $id): void
    {
        if ($this->denied || ! $this->canManage) {
            return;
        }

        $method = PaymentMethod::query()->find($id);

        if (! $method) {
            return;
        }

        if ($method->payments()->exists()) {
            $this->flashToast('Cannot delete — payments use this method.', 'danger');

            return;
        }

        $method->accounts()->delete();
        $method->delete();

        if ($this->editingMethodId === $id) {
            $this->resetMethodForm();
        }

        if ((int) $this->account_method_id === $id) {
            $this->account_method_id = '';
        }

        $this->flashToast('Payment method deleted.', 'danger');
        $this->dispatch('refresh-module-stats');
    }

    public function saveMethod(): void
    {
        if ($this->denied || ! $this->canManage) {
            return;
        }

        $this->validate([
            'method_name' => ['required', 'string', 'max:120'],
            'method_active' => ['boolean'],
        ], [], [
            'method_name' => 'method name',
        ]);

        $name = trim($this->method_name);

        $sameName = PaymentMethod::query()
            ->whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->when($this->editingMethodId, function ($q) {
                $q->where('id', '!=', $this->editingMethodId);
            })
            ->exists();

        if ($sameName) {
            $this->addError('method_name', 'This method name already exists.');

            return;
        }

        $data = [
            'name' => $name,
            'is_active' => $this->method_active,
        ];

        if ($this->editingMethodId) {
            PaymentMethod::query()->whereKey($this->editingMethodId)->update($data);
            $this->flashToast('Payment method updated.', 'success');
        } else {
            PaymentMethod::query()->create($data);
            $this->flashToast('Payment method added.', 'success');
        }

        $this->dispatch('refresh-module-stats');
        $this->resetMethodForm();
    }

    public function editAccount(int $id): void
    {
        if ($this->denied || ! $this->canManage) {
            return;
        }

        $account = PaymentAccount::query()->find($id);

        if (! $account) {
            return;
        }

        $this->editingAccountId = $account->id;
        $this->account_method_id = (string) $account->payment_method_id;
        $this->account_name = $account->name;
        $this->account_details = (string) ($account->details ?? '');
        $this->account_active = (bool) $account->is_active;
        $this->successMessage = '';
        $this->resetValidation();
    }

    public function cancelAccountEdit(): void
    {
        $this->resetAccountForm();
    }

    public function deleteAccount(int $id): void
    {
        if ($this->denied || ! $this->canManage) {
            return;
        }

        $account = PaymentAccount::query()->find($id);

        if (! $account) {
            return;
        }

        if ($account->payments()->exists()) {
            $this->flashToast('Cannot delete — payments use this account.', 'danger');

            return;
        }

        $account->delete();

        if ($this->editingAccountId === $id) {
            $this->resetAccountForm();
        }

        $this->flashToast('Receiving account deleted.', 'danger');
        $this->dispatch('refresh-module-stats');
    }

    public function saveAccount(): void
    {
        if ($this->denied || ! $this->canManage) {
            return;
        }

        $this->validate([
            'account_method_id' => ['required', 'integer', 'exists:payment_methods,id'],
            'account_name' => ['required', 'string', 'max:160'],
            'account_details' => ['nullable', 'string', 'max:255'],
            'account_active' => ['boolean'],
        ], [], [
            'account_method_id' => 'method',
            'account_name' => 'account name',
            'account_details' => 'details',
        ]);

        $name = trim($this->account_name);
        $details = trim($this->account_details);
        $methodId = (int) $this->account_method_id;

        $sameName = PaymentAccount::query()
            ->where('payment_method_id', $methodId)
            ->whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->when($this->editingAccountId, function ($q) {
                $q->where('id', '!=', $this->editingAccountId);
            })
            ->exists();

        if ($sameName) {
            $this->addError('account_name', 'This account already exists under that method.');

            return;
        }

        $data = [
            'payment_method_id' => $methodId,
            'name' => $name,
            'details' => $details !== '' ? $details : null,
            'is_active' => $this->account_active,
        ];

        if ($this->editingAccountId) {
            PaymentAccount::query()->whereKey($this->editingAccountId)->update($data);
            $this->flashToast('Receiving account updated.', 'success');
        } else {
            PaymentAccount::query()->create($data);
            $this->flashToast('Receiving account added.', 'success');
        }

        $this->dispatch('refresh-module-stats');
        $this->resetAccountForm();
    }

    public function render()
    {
        $methods = collect();

        if (! $this->denied) {
            $query = PaymentMethod::query()
                ->with(['accounts' => function ($q) {
                    $q->orderBy('name');
                }])
                ->orderBy('name');

            if (trim($this->search) !== '') {
                $term = '%'.trim($this->search).'%';
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', $term)
                        ->orWhereHas('accounts', function ($accountQuery) use ($term) {
                            $accountQuery->where('name', 'like', $term)
                                ->orWhere('details', 'like', $term);
                        });
                });
            }

            $methods = $query->get();
        }

        $methodOptions = PaymentMethod::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('livewire.accounts.manage-payment-methods', [
            'methods' => $methods,
            'methodOptions' => $methodOptions,
        ]);
    }

    private function flashToast(string $message, string $type): void
    {
        $this->successMessage = $message;
        $this->toastType = $type;
        $this->toastVersion++;
    }

    private function resetMethodForm(): void
    {
        $this->editingMethodId = null;
        $this->method_name = '';
        $this->method_active = true;
        $this->resetValidation();
        $this->dispatch('method-form-reset');
    }

    private function resetAccountForm(): void
    {
        $this->editingAccountId = null;
        $this->account_method_id = '';
        $this->account_name = '';
        $this->account_details = '';
        $this->account_active = true;
        $this->resetValidation();
        $this->dispatch('account-form-reset');
    }
}
