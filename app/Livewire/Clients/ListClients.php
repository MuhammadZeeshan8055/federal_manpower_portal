<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use App\Support\ClientPrivateFiles;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ListClients extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'portal';

    public bool $denied = false;

    public bool $canManageClients = false;

    public ?int $editingId = null;

    public ?int $viewingId = null;

    public string $search = '';

    public string $process_status_id = '';

    public string $statusFilterLabel = '';

    public string $successMessage = '';

    public int $toastVersion = 0;

    public string $toastType = 'success';

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user || (! $user->canView('clients', 'list') && ! $user->canManage('clients', 'list'))) {
            $this->denied = true;

            return;
        }

        $this->canManageClients = $user->canManage('clients', 'list');
    }

    public function updatedSearch(): void
    {
        $this->resetPage('clientsPage');
    }

    public function applyStatusFilter(?string $id = '', ?string $label = ''): void
    {
        $this->process_status_id = $id ? (string) $id : '';
        $this->statusFilterLabel = $label ? (string) $label : '';
        $this->resetPage('clientsPage');
    }

    public function clearStatusFilter(): void
    {
        $this->process_status_id = '';
        $this->statusFilterLabel = '';
        $this->resetPage('clientsPage');
    }

    public function viewClient(int $id): void
    {
        if ($this->denied) {
            return;
        }

        if (! Client::query()->whereKey($id)->exists()) {
            return;
        }

        $this->viewingId = $id;
        $this->editingId = null;
        $this->successMessage = '';
    }

    public function editClient(int $id): void
    {
        if ($this->denied || ! $this->canManageClients) {
            return;
        }

        if (! Client::query()->whereKey($id)->exists()) {
            return;
        }

        $this->editingId = $id;
        $this->viewingId = null;
        $this->successMessage = '';
    }

    #[On('client-edit-closed')]
    public function closePanel(): void
    {
        $this->editingId = null;
        $this->viewingId = null;
        $this->resetPage('clientsPage');
    }

    public function deleteClient(int $id): void
    {
        if ($this->denied || ! $this->canManageClients) {
            return;
        }

        if ($this->editingId === $id) {
            $this->editingId = null;
        }

        if ($this->viewingId === $id) {
            $this->viewingId = null;
        }

        $client = Client::query()->find($id);

        if (! $client) {
            return;
        }

        $folder = 'clients/'.$client->id;

        if (Storage::disk(ClientPrivateFiles::disk())->exists($folder)) {
            Storage::disk(ClientPrivateFiles::disk())->deleteDirectory($folder);
        }

        $client->delete();

        $this->successMessage = 'Client deleted.';
        $this->toastType = 'danger';
        $this->toastVersion++;
        $this->resetPage('clientsPage');
    }

    public function render()
    {
        if ($this->editingId || $this->viewingId) {
            return view('livewire.clients.list-clients', [
                'clients' => null,
            ]);
        }

        $query = Client::query()
            ->with(['country:id,name', 'trade:id,name', 'processStatus:id,name'])
            ->withCount('documents')
            ->orderByDesc('id');

        if ($this->process_status_id !== '') {
            $query->where('process_status_id', (int) $this->process_status_id);
        }

        if (trim($this->search) !== '') {
            $term = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($term) {
                $q->where('full_name', 'like', $term)
                    ->orWhere('cnic', 'like', $term)
                    ->orWhere('passport_number', 'like', $term)
                    ->orWhere('phone', 'like', $term);
            });
        }

        return view('livewire.clients.list-clients', [
            'clients' => $query->paginate(10, ['*'], 'clientsPage'),
        ]);
    }
}
