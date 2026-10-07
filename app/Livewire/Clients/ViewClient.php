<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;

class ViewClient extends Component
{
    public int $clientId;

    public bool $denied = false;

    public bool $notFound = false;

    public ?Client $client = null;

    public function mount(int $clientId): void
    {
        $user = auth()->user();

        if (! $user || (! $user->canView('clients', 'list') && ! $user->canManage('clients', 'list'))) {
            $this->denied = true;

            return;
        }

        $this->clientId = $clientId;

        $this->client = Client::query()
            ->with([
                'country:id,name',
                'trade:id,name',
                'company:id,name',
                'careOff:id,name',
                'processStatus:id,name',
                'documents',
            ])
            ->find($clientId);

        if (! $this->client) {
            $this->notFound = true;
        }
    }

    public function backToList(): void
    {
        $this->dispatch('client-edit-closed');
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
        $docsByKey = [];

        if ($this->client) {
            foreach ($this->client->documents as $doc) {
                $docsByKey[$doc->doc_key] = $doc;
            }
        }

        return view('livewire.clients.view-client', [
            'docsByKey' => $docsByKey,
            'documentRows' => $this->documentRows(),
        ]);
    }
}
