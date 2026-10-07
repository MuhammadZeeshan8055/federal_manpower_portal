<?php

namespace App\Livewire\Clients;

use App\Models\CareOff;
use App\Models\Client;
use App\Models\ClientDocument;
use App\Models\Company;
use App\Models\Country;
use App\Models\ProcessStatus;
use App\Models\Trade;
use App\Support\ClientPrivateFiles;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditClient extends Component
{
    use WithFileUploads;

    public int $clientId;

    public bool $denied = false;

    public bool $notFound = false;

    public string $trade_id = '';

    public string $country_id = '';

    public string $full_name = '';

    public string $height = '';

    public string $weight = '';

    public string $father_name = '';

    public string $mother_name = '';

    public string $gender = '';

    public string $date_of_birth = '';

    public string $marital_status = '';

    public string $religion = '';

    public string $cnic = '';

    public string $phone = '';

    public string $email = '';

    public string $address = '';

    public string $police_station = '';

    public string $district = '';

    public string $place_of_birth = '';

    public string $passport_number = '';

    public string $passport_issue_date = '';

    public string $passport_expiry = '';

    public string $medical_fitness = '';

    public string $degree = '';

    public string $degree_year = '';

    public string $certification = '';

    public string $certification_year = '';

    public string $board_university = '';

    public string $languages = '';

    public string $total_experience = '';

    public string $source = 'direct';

    public string $care_off_id = '';

    public string $company_id = '';

    public string $process_status_id = '';

    public string $overall_status = 'active';

    public ?string $photo_path = null;

    /** @var mixed */
    public $photo = null;

    /** @var array<string, mixed> */
    public array $doc_files = [];

    /** @var array<string, bool> */
    public array $doc_not_required = [];

    /** @var array<string, array{id:?int,file_path:?string,original_name:?string,is_not_required:bool}> */
    public array $existingDocs = [];

    public string $successMessage = '';

    public int $toastVersion = 0;

    public string $toastType = 'success';

    public function mount(int $clientId): void
    {
        $user = auth()->user();

        if (! $user || ! $user->canManage('clients', 'list')) {
            $this->denied = true;

            return;
        }

        $this->clientId = $clientId;
        $this->loadClient();
    }

    public function backToList(): void
    {
        $this->dispatch('client-edit-closed');
    }

    public function saveClient(): void
    {
        if ($this->denied || $this->notFound) {
            return;
        }

        $maxKb = ClientPrivateFiles::maxKilobytes();
        $docMimes = implode(',', ClientPrivateFiles::allowedMimes());

        $this->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'father_name' => ['required', 'string', 'max:255'],
            'mother_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female'],
            'date_of_birth' => ['required', 'date'],
            'marital_status' => ['required', 'in:single,married,divorced,widowed'],
            'religion' => ['required', 'string', 'max:100'],
            'cnic' => ['required', 'string', 'max:30', Rule::unique('clients', 'cnic')->ignore($this->clientId)],
            'passport_number' => ['required', 'string', 'max:50'],
            'passport_issue_date' => ['required', 'date'],
            'passport_expiry' => ['required', 'date', 'after:passport_issue_date'],
            'source' => ['required', 'in:direct,referred'],
            'care_off_id' => [$this->source === 'referred' ? 'required' : 'nullable'],
            'overall_status' => ['required', 'in:active,inactive'],
            'email' => ['nullable', 'email', 'max:255'],
            'photo' => $this->photoRules(),
            'doc_files' => ['array'],
            'doc_files.*' => ['nullable', 'file', 'max:'.$maxKb, 'mimes:'.$docMimes],
        ], [], [
            'full_name' => 'name',
            'father_name' => "father's name",
            'mother_name' => "mother's name",
            'date_of_birth' => 'date of birth',
            'marital_status' => 'marital status',
            'cnic' => 'ID number',
            'passport_number' => 'passport number',
            'passport_issue_date' => 'date of issue',
            'passport_expiry' => 'date of expiry',
            'care_off_id' => 'care off',
            'doc_files.*' => 'document',
        ]);

        $allowedDocKeys = collect($this->documentRows())->pluck('key')->all();

        foreach (array_keys($this->doc_files) as $key) {
            if (! in_array($key, $allowedDocKeys, true)) {
                unset($this->doc_files[$key]);
            }
        }

        $careOffId = $this->source === 'referred' ? $this->idOrNull($this->care_off_id) : null;

        DB::transaction(function () use ($careOffId) {
            $client = Client::query()->whereKey($this->clientId)->firstOrFail();

            $client->update([
                'country_id' => $this->idOrNull($this->country_id),
                'trade_id' => $this->idOrNull($this->trade_id),
                'company_id' => $this->idOrNull($this->company_id),
                'care_off_id' => $careOffId,
                'process_status_id' => $this->idOrNull($this->process_status_id),
                'full_name' => $this->full_name,
                'height' => $this->nullIfEmpty($this->height),
                'weight' => $this->nullIfEmpty($this->weight),
                'father_name' => $this->father_name,
                'mother_name' => $this->mother_name,
                'gender' => $this->gender,
                'date_of_birth' => $this->date_of_birth,
                'marital_status' => $this->marital_status,
                'religion' => $this->religion,
                'cnic' => $this->cnic,
                'place_of_birth' => $this->nullIfEmpty($this->place_of_birth),
                'phone' => $this->nullIfEmpty($this->phone),
                'email' => $this->nullIfEmpty($this->email),
                'address' => $this->nullIfEmpty($this->address),
                'police_station' => $this->nullIfEmpty($this->police_station),
                'district' => $this->nullIfEmpty($this->district),
                'medical_fitness' => $this->nullIfEmpty($this->medical_fitness),
                'passport_number' => $this->passport_number,
                'passport_issue_date' => $this->passport_issue_date,
                'passport_expiry' => $this->passport_expiry,
                'degree' => $this->nullIfEmpty($this->degree),
                'degree_year' => $this->nullIfEmpty($this->degree_year),
                'certification' => $this->nullIfEmpty($this->certification),
                'certification_year' => $this->nullIfEmpty($this->certification_year),
                'board_university' => $this->nullIfEmpty($this->board_university),
                'languages' => $this->nullIfEmpty($this->languages),
                'total_experience' => $this->nullIfEmpty($this->total_experience),
                'source' => $this->source,
                'overall_status' => $this->overall_status,
            ]);

            if ($this->photo) {
                $this->deletePrivatePath($client->photo_path);
                $photoPath = ClientPrivateFiles::storeForClient($client->id, 'photo', $this->photo);
                $client->update(['photo_path' => $photoPath]);
                $this->photo_path = $photoPath;
                $this->photo = null;
            }

            foreach ($this->documentRows() as $row) {
                $key = $row['key'];
                $notRequired = (bool) ($this->doc_not_required[$key] ?? false);
                $file = (! $notRequired) ? ($this->doc_files[$key] ?? null) : null;
                $existing = ClientDocument::query()
                    ->where('client_id', $client->id)
                    ->where('doc_key', $key)
                    ->first();

                if ($file) {
                    if ($existing?->file_path) {
                        $this->deletePrivatePath($existing->file_path);
                    }

                    $filePath = ClientPrivateFiles::storeForClient($client->id, $key, $file);

                    ClientDocument::query()->updateOrCreate(
                        ['client_id' => $client->id, 'doc_key' => $key],
                        [
                            'label' => $row['label'],
                            'file_path' => $filePath,
                            'original_name' => $file->getClientOriginalName(),
                            'is_not_required' => false,
                            'uploaded_at' => now(),
                        ]
                    );

                    continue;
                }

                if ($notRequired) {
                    if ($existing?->file_path) {
                        $this->deletePrivatePath($existing->file_path);
                    }

                    ClientDocument::query()->updateOrCreate(
                        ['client_id' => $client->id, 'doc_key' => $key],
                        [
                            'label' => $row['label'],
                            'file_path' => null,
                            'original_name' => null,
                            'is_not_required' => true,
                            'uploaded_at' => null,
                        ]
                    );

                    continue;
                }

                if ($existing && $existing->is_not_required) {
                    $existing->delete();
                }
            }

            $this->doc_files = [];
        });

        $this->loadClient();
        $this->successMessage = 'Client updated.';
        $this->toastType = 'success';
        $this->toastVersion++;
    }

    public function getAgeProperty(): string
    {
        if ($this->date_of_birth === '') {
            return '';
        }

        try {
            return (string) Carbon::parse($this->date_of_birth)->age;
        } catch (\Throwable) {
            return '';
        }
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
        return view('livewire.clients.edit-client', [
            'countries' => Country::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'trades' => Trade::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'companies' => Company::query()->where('type', 'employer')->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'careOffs' => CareOff::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'processStatuses' => ProcessStatus::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(['id', 'name']),
            'documents' => $this->documentRows(),
            'age' => $this->age,
            'maxUploadMb' => max(1, (int) ceil(ClientPrivateFiles::maxKilobytes() / 1024)),
        ]);
    }

    private function loadClient(): void
    {
        $client = Client::query()->with('documents')->find($this->clientId);

        if (! $client) {
            $this->notFound = true;

            return;
        }

        $this->notFound = false;
        $this->trade_id = (string) ($client->trade_id ?? '');
        $this->country_id = (string) ($client->country_id ?? '');
        $this->full_name = $client->full_name;
        $this->height = (string) ($client->height ?? '');
        $this->weight = (string) ($client->weight ?? '');
        $this->father_name = $client->father_name;
        $this->mother_name = $client->mother_name;
        $this->gender = $client->gender;
        $this->date_of_birth = optional($client->date_of_birth)->format('Y-m-d') ?? '';
        $this->marital_status = $client->marital_status;
        $this->religion = $client->religion;
        $this->cnic = $client->cnic;
        $this->phone = (string) ($client->phone ?? '');
        $this->email = (string) ($client->email ?? '');
        $this->address = (string) ($client->address ?? '');
        $this->police_station = (string) ($client->police_station ?? '');
        $this->district = (string) ($client->district ?? '');
        $this->place_of_birth = (string) ($client->place_of_birth ?? '');
        $this->passport_number = $client->passport_number;
        $this->passport_issue_date = optional($client->passport_issue_date)->format('Y-m-d') ?? '';
        $this->passport_expiry = optional($client->passport_expiry)->format('Y-m-d') ?? '';
        $this->medical_fitness = (string) ($client->medical_fitness ?? '');
        $this->degree = (string) ($client->degree ?? '');
        $this->degree_year = (string) ($client->degree_year ?? '');
        $this->certification = (string) ($client->certification ?? '');
        $this->certification_year = (string) ($client->certification_year ?? '');
        $this->board_university = (string) ($client->board_university ?? '');
        $this->languages = (string) ($client->languages ?? '');
        $this->total_experience = (string) ($client->total_experience ?? '');
        $this->source = $client->source ?: 'direct';
        $this->care_off_id = (string) ($client->care_off_id ?? '');
        $this->company_id = (string) ($client->company_id ?? '');
        $this->process_status_id = (string) ($client->process_status_id ?? '');
        $this->overall_status = $client->overall_status ?: 'active';
        $this->photo_path = $client->photo_path;

        $this->existingDocs = [];
        $this->doc_not_required = [];

        foreach ($this->documentRows() as $row) {
            $this->doc_not_required[$row['key']] = false;
            $this->existingDocs[$row['key']] = [
                'id' => null,
                'file_path' => null,
                'original_name' => null,
                'is_not_required' => false,
            ];
        }

        foreach ($client->documents as $doc) {
            $this->existingDocs[$doc->doc_key] = [
                'id' => $doc->id,
                'file_path' => $doc->file_path,
                'original_name' => $doc->original_name,
                'is_not_required' => (bool) $doc->is_not_required,
            ];
            $this->doc_not_required[$doc->doc_key] = (bool) $doc->is_not_required;
        }
    }

    private function deletePrivatePath(?string $path): void
    {
        if (! ClientPrivateFiles::pathBelongsToClient($this->clientId, $path)) {
            return;
        }

        if (ClientPrivateFiles::exists($path)) {
            Storage::disk(ClientPrivateFiles::disk())->delete($path);
        }
    }

    private function photoRules(): array
    {
        $maxKb = ClientPrivateFiles::maxKilobytes();
        $mimes = implode(',', ClientPrivateFiles::photoMimes());

        return ['nullable', 'image', 'max:'.$maxKb, 'mimes:'.$mimes];
    }

    private function idOrNull(string $value): ?int
    {
        return $value !== '' ? (int) $value : null;
    }

    private function nullIfEmpty(string $value): ?string
    {
        return $value !== '' ? $value : null;
    }
}
