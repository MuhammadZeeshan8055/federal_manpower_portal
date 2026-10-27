<?php

namespace App\Livewire\Accounts;

use App\Models\Client;
use App\Models\ProtectorPayment;
use App\Support\WorkspaceCountry;
use Livewire\Component;
use Livewire\WithPagination;

class ManageProtectorPayments extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'portal';

    public bool $denied = false;

    public bool $canManage = false;

    public ?int $editingId = null;

    public string $client_id = '';

    public string $amount = '';

    public string $paid_on = '';

    public string $description = '';

    public string $search = '';

    public string $successMessage = '';

    public int $toastVersion = 0;

    public string $toastType = 'success';

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user || (! $user->canView('accounts', 'protector_payments') && ! $user->canManage('accounts', 'protector_payments'))) {
            $this->denied = true;

            return;
        }

        $this->canManage = $user->canManage('accounts', 'protector_payments');
        $this->paid_on = now()->toDateString();
    }

    public function updatedSearch(): void
    {
        $this->resetPage('protectorPaymentsPage');
    }

    public function editPayment(int $id): void
    {
        if ($this->denied || ! $this->canManage) {
            return;
        }

        $payment = ProtectorPayment::query()->find($id);

        if (! $payment) {
            return;
        }

        $this->editingId = $payment->id;
        $this->client_id = (string) $payment->client_id;
        $this->amount = (string) $payment->amount;
        $this->paid_on = $payment->paid_on->toDateString();
        $this->description = (string) ($payment->description ?? '');
        $this->successMessage = '';
        $this->resetValidation();
    }

    public function cancelEdit(): void
    {
        $this->successMessage = '';
        $this->resetForm();
    }

    public function deletePayment(int $id): void
    {
        if ($this->denied || ! $this->canManage) {
            return;
        }

        ProtectorPayment::query()->whereKey($id)->delete();

        if ($this->editingId === $id) {
            $this->resetForm();
        }

        $this->successMessage = 'Protector payment deleted.';
        $this->toastType = 'danger';
        $this->toastVersion++;
        $this->resetPage('protectorPaymentsPage');
    }

    public function savePayment(): void
    {
        if ($this->denied || ! $this->canManage) {
            return;
        }

        $this->validate([
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'paid_on' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:500'],
        ], [], [
            'client_id' => 'client',
            'paid_on' => 'date',
        ]);

        $data = [
            'client_id' => (int) $this->client_id,
            'amount' => $this->amount,
            'paid_on' => $this->paid_on,
            'description' => trim($this->description) !== '' ? trim($this->description) : null,
        ];

        if ($this->editingId) {
            ProtectorPayment::query()->whereKey($this->editingId)->update($data);
            $this->successMessage = 'Protector payment updated.';
        } else {
            $data['recorded_by'] = auth()->id();
            ProtectorPayment::query()->create($data);
            $this->successMessage = 'Protector payment added.';
        }

        $this->toastType = 'success';
        $this->toastVersion++;
        $this->resetForm();
        $this->resetPage('protectorPaymentsPage');
    }

    public function render()
    {
        $clientsQuery = Client::query()->orderBy('full_name');
        WorkspaceCountry::filterClients($clientsQuery);

        $paymentsQuery = ProtectorPayment::query()
            ->with(['client:id,full_name'])
            ->whereHas('client', function ($query) {
                WorkspaceCountry::filterClients($query);
            })
            ->orderByDesc('paid_on')
            ->orderByDesc('id');

        if (trim($this->search) !== '') {
            $term = '%'.trim($this->search).'%';
            $paymentsQuery->where(function ($q) use ($term) {
                $q->where('description', 'like', $term)
                    ->orWhereHas('client', function ($clientQuery) use ($term) {
                        $clientQuery->where('full_name', 'like', $term)
                            ->orWhere('cnic', 'like', $term);
                    });
            });
        }

        return view('livewire.accounts.manage-protector-payments', [
            'clients' => $this->denied ? collect() : $clientsQuery->get(['id', 'full_name']),
            'payments' => $this->denied
                ? null
                : $paymentsQuery->paginate(12, ['*'], 'protectorPaymentsPage'),
        ]);
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->client_id = '';
        $this->amount = '';
        $this->paid_on = now()->toDateString();
        $this->description = '';
        $this->resetValidation();
        $this->dispatch('master-form-reset');
    }
}
