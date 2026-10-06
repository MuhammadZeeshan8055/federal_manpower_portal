<?php

namespace App\Livewire\Operations;

use App\Models\Country;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class ManageCountries extends Component
{
    use WithFileUploads;

    public bool $denied = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $code = '';

    public bool $is_active = true;

    public $flag;

    public string $successMessage = '';

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user || ! $user->canManage('operations', 'countries')) {
            $this->denied = true;
        }
    }

    public function editCountry(int $id): void
    {
        if ($this->denied) {
            return;
        }

        $country = Country::query()->find($id);

        if (! $country) {
            return;
        }

        $this->editingId = $country->id;
        $this->name = $country->name;
        $this->code = $country->code;
        $this->is_active = (bool) $country->is_active;
        $this->flag = null;
        $this->successMessage = '';
        $this->resetValidation();
        $this->dispatch('country-form-reset');
    }

    public function cancelEdit(): void
    {
        $this->successMessage = '';
        $this->resetForm();
    }

    public function deleteCountry(int $id): void
    {
        if ($this->denied) {
            return;
        }

        Country::query()->whereKey($id)->delete();

        if ($this->editingId === $id) {
            $this->resetForm();
        }

        if ((int) session('workspace_country_id') === $id) {
            session()->forget('workspace_country_id');
        }

        $this->successMessage = 'Country deleted.';
    }

    public function saveCountry(): void
    {
        if ($this->denied) {
            return;
        }

        $this->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('countries', 'name')->ignore($this->editingId)],
            'code' => ['required', 'string', 'max:20', Rule::unique('countries', 'code')->ignore($this->editingId)],
            'is_active' => ['boolean'],
            'flag' => ['nullable', 'image', 'max:2048'],
        ]);

        $data = [
            'name' => trim($this->name),
            'code' => strtoupper(trim($this->code)),
            'is_active' => $this->is_active,
        ];

        $flagPath = $this->storeFlag();

        if ($flagPath) {
            $data['flag_image'] = $flagPath;
        }

        if ($this->editingId) {
            Country::query()->whereKey($this->editingId)->update($data);
            $this->successMessage = 'Country updated.';
        } else {
            $data['flag_image'] = $flagPath;
            Country::query()->create($data);
            $this->successMessage = 'Country added.';
        }

        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.operations.manage-countries', [
            'countries' => Country::query()->orderBy('name')->get(),
        ]);
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->reset('name', 'code', 'flag');
        $this->is_active = true;
        $this->resetValidation();
        $this->dispatch('country-form-reset');
    }

    private function storeFlag(): ?string
    {
        if (! $this->flag) {
            return null;
        }

        $slug = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $this->code));
        $name = $slug.'.'.$this->flag->getClientOriginalExtension();
        $this->flag->storeAs('images/flags', $name, 'public_root');

        return 'images/flags/'.$name;
    }
}
