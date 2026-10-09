<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use App\Models\ClientDocument;
use Livewire\Component;
use Livewire\WithPagination;

class ListClientDocuments extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'portal';

    public bool $denied = false;

    public string $search = '';

    /** uploaded | na | missing */
    public string $filter = 'uploaded';

    public string $doc_key = '';

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user || (! $user->canView('clients', 'documents') && ! $user->canManage('clients', 'documents'))) {
            $this->denied = true;
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage('docsPage');
    }

    public function updatedFilter(): void
    {
        $this->resetPage('docsPage');
    }

    public function updatedDocKey(): void
    {
        $this->resetPage('docsPage');
    }

    public function documentRows(): array
    {
        return [
            ['key' => 'passport_copy', 'label' => 'Passport copy'],
            ['key' => 'police_character', 'label' => 'Police character certificate'],
            ['key' => 'matric', 'label' => 'Matric certificate'],
            ['key' => 'diploma_degree', 'label' => 'Diploma / Degree'],
            ['key' => 'professional_skill', 'label' => 'Professional skill / experience certificate'],
            ['key' => 'experience_letters', 'label' => 'Experience letters'],
            ['key' => 'frc', 'label' => 'FRC'],
            ['key' => 'birth_certificate', 'label' => 'Birth certificate'],
            ['key' => 'medical', 'label' => 'Medical'],
        ];
    }

    public function render()
    {
        if ($this->denied) {
            return view('livewire.clients.list-client-documents', [
                'rows' => collect(),
                'paginator' => null,
                'docTypes' => $this->documentRows(),
            ]);
        }

        if ($this->filter === 'missing') {
            return $this->renderMissing();
        }

        $query = ClientDocument::query()
            ->with(['client:id,full_name,cnic'])
            ->whereHas('client')
            ->orderByDesc('id');

        if ($this->filter === 'uploaded') {
            $query->whereNotNull('file_path')->where('file_path', '!=', '');
        } else {
            $query->where('is_not_required', true);
        }

        if ($this->doc_key !== '') {
            $query->where('doc_key', $this->doc_key);
        }

        if (trim($this->search) !== '') {
            $term = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($term) {
                $q->where('label', 'like', $term)
                    ->orWhere('original_name', 'like', $term)
                    ->orWhereHas('client', function ($clientQuery) use ($term) {
                        $clientQuery->where('full_name', 'like', $term)
                            ->orWhere('cnic', 'like', $term)
                            ->orWhere('passport_number', 'like', $term);
                    });
            });
        }

        $paginator = $query->paginate(12, ['*'], 'docsPage');

        $rows = $paginator->getCollection()->map(function (ClientDocument $doc) {
            return [
                'client_id' => $doc->client_id,
                'client_name' => $doc->client?->full_name ?: '—',
                'cnic' => $doc->client?->cnic ?: '—',
                'doc_key' => $doc->doc_key,
                'label' => $doc->label,
                'status' => $this->filter === 'uploaded' ? 'uploaded' : 'na',
                'file_name' => $doc->original_name,
                'can_view' => filled($doc->file_path),
            ];
        });

        return view('livewire.clients.list-client-documents', [
            'rows' => $rows,
            'paginator' => $paginator,
            'docTypes' => $this->documentRows(),
        ]);
    }

    private function renderMissing()
    {
        $allowedKeys = collect($this->documentRows())->pluck('key')->all();
        $labels = collect($this->documentRows())->pluck('label', 'key');

        if ($this->doc_key !== '' && ! in_array($this->doc_key, $allowedKeys, true)) {
            $this->doc_key = '';
        }

        $keysToCheck = $this->doc_key !== ''
            ? [$this->doc_key]
            : $allowedKeys;

        $clientsQuery = Client::query()
            ->with('documents')
            ->orderByDesc('id');

        if (trim($this->search) !== '') {
            $term = '%'.trim($this->search).'%';
            $clientsQuery->where(function ($q) use ($term) {
                $q->where('full_name', 'like', $term)
                    ->orWhere('cnic', 'like', $term)
                    ->orWhere('passport_number', 'like', $term);
            });
        }

        // Clients who still need at least one of the selected doc types
        $clientsQuery->where(function ($outer) use ($keysToCheck) {
            foreach ($keysToCheck as $key) {
                $outer->orWhere(function ($q) use ($key) {
                    $q->whereDoesntHave('documents', function ($doc) use ($key) {
                        $doc->where('doc_key', $key)
                            ->where(function ($status) {
                                $status->where(function ($file) {
                                    $file->whereNotNull('file_path')->where('file_path', '!=', '');
                                })->orWhere('is_not_required', true);
                            });
                    });
                });
            }
        });

        $paginator = $clientsQuery->paginate(8, ['*'], 'docsPage');
        $rows = collect();

        foreach ($paginator as $client) {
            $byKey = $client->documents->keyBy('doc_key');

            foreach ($keysToCheck as $key) {
                $existing = $byKey->get($key);
                $covered = $existing && (filled($existing->file_path) || $existing->is_not_required);

                if ($covered) {
                    continue;
                }

                $rows->push([
                    'client_id' => $client->id,
                    'client_name' => $client->full_name,
                    'cnic' => $client->cnic,
                    'doc_key' => $key,
                    'label' => $labels[$key] ?? $key,
                    'status' => 'missing',
                    'file_name' => null,
                    'can_view' => false,
                ]);
            }
        }

        return view('livewire.clients.list-client-documents', [
            'rows' => $rows,
            'paginator' => $paginator,
            'docTypes' => $this->documentRows(),
        ]);
    }
}
