<?php

namespace App\Livewire\Operations;

use App\Models\Company;
use App\Models\Country;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ManageCompanies extends Component
{
    public bool $denied = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $country_id = '';

    public bool $is_active = true;

    public string $successMessage = '';

    public int $toastVersion = 0;

    public string $toastType = 'success';

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user || ! $user->canManage('operations', 'companies')) {
            $this->denied = true;
        }
    }

    public function editCompany(int $id): void
    {
        if ($this->denied) {
            return;
        }

        $company = Company::query()
            ->where('type', 'employer')
            ->find($id);

        if (! $company) {
            return;
        }

        $this->editingId = $company->id;
        $this->name = $company->name;
        $this->country_id = (string) $company->country_id;
        $this->is_active = (bool) $company->is_active;
        $this->successMessage = '';
        $this->resetValidation();
    }

    public function cancelEdit(): void
    {
        $this->successMessage = '';
        $this->resetForm();
    }

    public function deleteCompany(int $id): void
    {
        if ($this->denied) {
            return;
        }

        Company::query()
            ->where('type', 'employer')
            ->whereKey($id)
            ->delete();

        if ($this->editingId === $id) {
            $this->resetForm();
        }

        $this->successMessage = 'Company deleted.';
        $this->toastType = 'danger';
        $this->toastVersion++;
    }

    public function saveCompany(): void
    {
        if ($this->denied) {
            return;
        }

        $this->validate([
            'name' => [
                'required',
                'string',
                'max:160',
                Rule::unique('companies', 'name')
                    ->where(fn ($query) => $query->where('type', 'employer'))
                    ->ignore($this->editingId),
            ],
            'country_id' => ['required', 'exists:countries,id'],
            'is_active' => ['boolean'],
        ]);

        $data = [
            'name' => trim($this->name),
            'type' => 'employer',
            'country_id' => (int) $this->country_id,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            Company::query()
                ->where('type', 'employer')
                ->whereKey($this->editingId)
                ->update($data);
            $this->successMessage = 'Company updated.';
        } else {
            Company::query()->create($data);
            $this->successMessage = 'Company added.';
        }

        $this->toastType = 'success';
        $this->toastVersion++;
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.operations.manage-companies', [
            'companies' => Company::query()
                ->where('type', 'employer')
                ->with('country')
                ->orderBy('name')
                ->get(),
            'countryOptions' => Country::query()
                ->orderByDesc('is_active')
                ->orderBy('name')
                ->get(['id', 'name', 'code', 'flag_image', 'is_active']),
        ]);
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->reset('name', 'country_id');
        $this->is_active = true;
        $this->resetValidation();
        $this->dispatch('master-form-reset');
    }
}
