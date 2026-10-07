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
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class ClientBioDataForm extends Component
{
    use WithFileUploads;

    public bool $denied = false;

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

    /** @var mixed */
    public $photo = null;

    /** @var array<string, mixed> */
    public array $doc_files = [];

    /** @var array<string, bool> */
    public array $doc_not_required = [];

    public bool $openPersonal = true;

    public bool $openPassport = false;

    public bool $openEducation = false;

    public bool $openCase = false;

    public bool $openDocuments = false;

    public bool $unlockedPassport = false;

    public bool $unlockedEducation = false;

    public bool $unlockedCase = false;

    public bool $unlockedDocuments = false;

    public string $successMessage = '';

    public int $toastVersion = 0;

    public string $toastType = 'success';

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user || (! $user->canView('clients', 'create') && ! $user->canManage('clients', 'create'))) {
            $this->denied = true;
        }

        $this->resetDocumentChecks();
    }

    public function toggleSection(string $section): void
    {
        match ($section) {
            'personal' => $this->openPersonal = ! $this->openPersonal,
            'passport' => $this->unlockedPassport
                ? $this->openPassport = ! $this->openPassport
                : null,
            'education' => $this->unlockedEducation
                ? $this->openEducation = ! $this->openEducation
                : null,
            'case' => $this->unlockedCase
                ? $this->openCase = ! $this->openCase
                : null,
            'documents' => $this->unlockedDocuments
                ? $this->openDocuments = ! $this->openDocuments
                : null,
            default => null,
        };
    }

    public function continuePersonal(): void
    {
        $this->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'father_name' => ['required', 'string', 'max:255'],
            'mother_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female'],
            'date_of_birth' => ['required', 'date'],
            'marital_status' => ['required', 'in:single,married,divorced,widowed'],
            'religion' => ['required', 'string', 'max:100'],
            'cnic' => ['required', 'string', 'max:30'],
            'photo' => $this->photoRules(),
        ], [], [
            'full_name' => 'name',
            'father_name' => "father's name",
            'mother_name' => "mother's name",
            'date_of_birth' => 'date of birth',
            'marital_status' => 'marital status',
            'cnic' => 'ID number',
        ]);

        $this->openPersonal = false;
        $this->unlockedPassport = true;
        $this->openPassport = true;
    }

    public function continuePassport(): void
    {
        $this->validate([
            'passport_number' => ['required', 'string', 'max:50'],
            'passport_issue_date' => ['required', 'date'],
            'passport_expiry' => ['required', 'date', 'after:passport_issue_date'],
        ], [], [
            'passport_number' => 'passport number',
            'passport_issue_date' => 'date of issue',
            'passport_expiry' => 'date of expiry',
        ]);

        $this->openPassport = false;
        $this->unlockedEducation = true;
        $this->openEducation = true;
    }

    public function continueEducation(): void
    {
        $this->openEducation = false;
        $this->unlockedCase = true;
        $this->openCase = true;
    }

    public function continueCase(): void
    {
        $rules = [
            'source' => ['required', 'in:direct,referred'],
        ];

        if ($this->source === 'referred') {
            $rules['care_off_id'] = ['required'];
        }

        $this->validate($rules, [], [
            'care_off_id' => 'care off',
        ]);

        $this->openCase = false;
        $this->unlockedDocuments = true;
        $this->openDocuments = true;
    }

    public function saveClient(): void
    {
        if ($this->denied) {
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
            'cnic' => ['required', 'string', 'max:30', Rule::unique('clients', 'cnic')],
            'passport_number' => ['required', 'string', 'max:50'],
            'passport_issue_date' => ['required', 'date'],
            'passport_expiry' => ['required', 'date', 'after:passport_issue_date'],
            'source' => ['required', 'in:direct,referred'],
            'care_off_id' => [$this->source === 'referred' ? 'required' : 'nullable'],
            'country_id' => ['nullable'],
            'trade_id' => ['nullable'],
            'company_id' => ['nullable'],
            'process_status_id' => ['nullable'],
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
            $client = Client::query()->create([
                'country_id' => $this->idOrNull($this->country_id),
                'trade_id' => $this->idOrNull($this->trade_id),
                'company_id' => $this->idOrNull($this->company_id),
                'care_off_id' => $careOffId,
                'process_status_id' => $this->idOrNull($this->process_status_id),
                'created_by' => auth()->id(),
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
                'photo_path' => null,
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
                'overall_status' => 'active',
            ]);

            if ($this->photo) {
                $photoPath = ClientPrivateFiles::storeForClient($client->id, 'photo', $this->photo);
                $client->update(['photo_path' => $photoPath]);
            }

            foreach ($this->documentRows() as $row) {
                $notRequired = (bool) ($this->doc_not_required[$row['key']] ?? false);
                $file = (! $notRequired) ? ($this->doc_files[$row['key']] ?? null) : null;

                if (! $notRequired && ! $file) {
                    continue;
                }

                $filePath = null;
                $originalName = null;
                $uploadedAt = null;

                if ($file) {
                    $filePath = ClientPrivateFiles::storeForClient($client->id, $row['key'], $file);
                    $originalName = $file->getClientOriginalName();
                    $uploadedAt = now();
                }

                ClientDocument::query()->create([
                    'client_id' => $client->id,
                    'doc_key' => $row['key'],
                    'label' => $row['label'],
                    'file_path' => $filePath,
                    'original_name' => $originalName,
                    'is_not_required' => $notRequired,
                    'uploaded_at' => $uploadedAt,
                ]);
            }
        });

        $this->successMessage = 'Client saved. Files stored privately.';
        $this->toastType = 'success';
        $this->toastVersion++;
        $this->resetForm();
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
        return view('livewire.clients.client-bio-data-form', [
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

    private function photoRules(): array
    {
        $maxKb = ClientPrivateFiles::maxKilobytes();
        $mimes = implode(',', ClientPrivateFiles::photoMimes());

        return ['nullable', 'image', 'max:'.$maxKb, 'mimes:'.$mimes];
    }

    private function resetForm(): void
    {
        $this->trade_id = '';
        $this->country_id = '';
        $this->full_name = '';
        $this->height = '';
        $this->weight = '';
        $this->father_name = '';
        $this->mother_name = '';
        $this->gender = '';
        $this->date_of_birth = '';
        $this->marital_status = '';
        $this->religion = '';
        $this->cnic = '';
        $this->phone = '';
        $this->email = '';
        $this->address = '';
        $this->police_station = '';
        $this->district = '';
        $this->place_of_birth = '';
        $this->passport_number = '';
        $this->passport_issue_date = '';
        $this->passport_expiry = '';
        $this->medical_fitness = '';
        $this->degree = '';
        $this->degree_year = '';
        $this->certification = '';
        $this->certification_year = '';
        $this->board_university = '';
        $this->languages = '';
        $this->total_experience = '';
        $this->source = 'direct';
        $this->care_off_id = '';
        $this->company_id = '';
        $this->process_status_id = '';
        $this->overall_status = 'active';
        $this->photo = null;
        $this->doc_files = [];
        $this->openPersonal = true;
        $this->openPassport = false;
        $this->openEducation = false;
        $this->openCase = false;
        $this->openDocuments = false;
        $this->unlockedPassport = false;
        $this->unlockedEducation = false;
        $this->unlockedCase = false;
        $this->unlockedDocuments = false;
        $this->resetDocumentChecks();
        $this->resetValidation();
    }

    private function resetDocumentChecks(): void
    {
        $this->doc_not_required = [];

        foreach ($this->documentRows() as $row) {
            $this->doc_not_required[$row['key']] = false;
        }
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
