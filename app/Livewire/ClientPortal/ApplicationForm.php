<?php

namespace App\Livewire\ClientPortal;

use App\Models\CareOff;
use App\Models\Client;
use App\Models\ClientDocument;
use App\Models\Company;
use App\Models\Country;
use App\Models\Trade;
use App\Support\ClientPrivateFiles;
use App\Support\ClientPublicLink;
use App\Support\JobApplyPrefill;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class ApplicationForm extends Component
{
    use WithFileUploads;

    public ?int $clientId = null;

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

    /** @var array<int, array{from_to:string,company:string,project:string,country:string,job_title:string}> */
    public array $experiences = [];

    public string $source = 'direct';

    public string $care_off_id = '';

    public string $company_id = '';

    public string $job_post_id = '';

    public ?string $appliedJobTitle = null;

    public ?string $appliedJobCompany = null;

    /** @var mixed */
    public $photo = null;

    public ?string $photo_path = null;

    /** @var array<string, mixed> */
    public array $doc_files = [];

    /** @var array<string, bool> */
    public array $doc_not_required = [];

    /** @var array<string, string|null> */
    public array $existing_docs = [];

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

    public function updated($name, $value): void
    {
        if ($name === 'photo' && $value instanceof TemporaryUploadedFile) {
            if (! ClientPrivateFiles::matchesAllowedContent($value, true)) {
                $this->photo = null;
                $this->addError('photo', 'Photo must be a real JPG or PNG file.');

                return;
            }

            $this->photo = ClientPrivateFiles::compressTemporaryIfNeeded($value, true);
            $this->resetValidation('photo');

            return;
        }

        if (str_starts_with($name, 'doc_files.') && $value instanceof TemporaryUploadedFile) {
            $key = substr($name, strlen('doc_files.'));

            if (! ClientPrivateFiles::matchesAllowedContent($value)) {
                unset($this->doc_files[$key]);
                $this->addError('doc_files.'.$key, 'File must be a real PDF, JPG, or PNG (not renamed).');

                return;
            }

            $this->doc_files[$key] = ClientPrivateFiles::compressTemporaryIfNeeded($value);
            $this->resetValidation('doc_files.'.$key);
        }
    }

    public function mount(): void
    {
        $user = auth()->user();
        $client = $user->clientProfile()->with(['documents', 'jobPost.employer'])->first();

        $this->experiences = Client::emptyExperienceRows();
        $this->resetDocumentChecks();

        $this->full_name = $user->name ?? '';
        $this->email = $user->email ?? '';

        if ($client) {
            $this->clientId = $client->id;
            $this->loadClient($client);

            $this->unlockedPassport = true;
            $this->unlockedEducation = true;
            $this->unlockedCase = true;
            $this->unlockedDocuments = true;
            $this->openPersonal = true;
            $this->openPassport = false;
            $this->openEducation = false;
            $this->openCase = false;
            $this->openDocuments = false;
        }

        $this->applyJobFromSession();
    }

    public function toggleSection(string $section): void
    {
        match ($section) {
            'personal' => $this->openPersonal = ! $this->openPersonal,
            'passport' => $this->unlockedPassport ? $this->openPassport = ! $this->openPassport : null,
            'education' => $this->unlockedEducation ? $this->openEducation = ! $this->openEducation : null,
            'case' => $this->unlockedCase ? $this->openCase = ! $this->openCase : null,
            'documents' => $this->unlockedDocuments ? $this->openDocuments = ! $this->openDocuments : null,
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
        ], [], $this->labels());

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
        ], [], $this->labels());

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
            'trade_id' => ['nullable'],
            'country_id' => ['nullable'],
            'company_id' => ['nullable'],
        ];

        if ($this->source === 'referred') {
            $rules['care_off_id'] = ['required'];
        }

        $this->validate($rules, [], $this->labels());

        $this->openCase = false;
        $this->unlockedDocuments = true;
        $this->openDocuments = true;
    }

    public function saveApplication(): void
    {
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
            'cnic' => [
                'required',
                'string',
                'max:30',
                Rule::unique('clients', 'cnic')->ignore($this->clientId),
            ],
            'passport_number' => ['required', 'string', 'max:50'],
            'passport_issue_date' => ['required', 'date'],
            'passport_expiry' => ['required', 'date', 'after:passport_issue_date'],
            'source' => ['required', 'in:direct,referred'],
            'care_off_id' => [$this->source === 'referred' ? 'required' : 'nullable'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'trade_id' => ['nullable', 'exists:trades,id'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'job_post_id' => ['nullable', 'exists:job_posts,id'],
            'email' => ['nullable', 'email', 'max:255'],
            'photo' => $this->photoRules(),
            'doc_files' => ['array'],
            'doc_files.*' => ['nullable', 'file', 'max:'.$maxKb, 'mimes:'.$docMimes, ClientPrivateFiles::contentRule()],
        ], [], $this->labels());

        $allowedDocKeys = collect($this->documentRows())->pluck('key')->all();

        foreach (array_keys($this->doc_files) as $key) {
            if (! in_array($key, $allowedDocKeys, true)) {
                unset($this->doc_files[$key]);
            }
        }

        $careOffId = $this->source === 'referred' ? $this->idOrNull($this->care_off_id) : null;
        $userId = auth()->id();

        DB::transaction(function () use ($careOffId, $userId) {
            $data = [
                'user_id' => $userId,
                'country_id' => $this->idOrNull($this->country_id),
                'trade_id' => $this->idOrNull($this->trade_id),
                'company_id' => $this->idOrNull($this->company_id),
                'job_post_id' => $this->idOrNull($this->job_post_id),
                'care_off_id' => $careOffId,
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
                'experience_rows' => Client::cleanExperienceRows($this->experiences),
                'source' => $this->source,
            ];

            if ($this->clientId) {
                $client = Client::query()
                    ->whereKey($this->clientId)
                    ->where('user_id', $userId)
                    ->firstOrFail();

                $client->update($data);
            } else {
                $data['created_by'] = null;
                $data['process_status_id'] = null;
                $data['overall_status'] = 'active';
                $data['photo_path'] = null;

                $client = Client::query()->create($data);
                $this->clientId = $client->id;
            }

            ClientPublicLink::ensureToken($client);

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

                    $this->existing_docs[$key] = $file->getClientOriginalName();
                    unset($this->doc_files[$key]);

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

                    $this->existing_docs[$key] = null;

                    continue;
                }
            }
        });

        session()->forget('apply_job_id');
        $this->successMessage = 'Application saved. You can track status from your portal home.';
        $this->toastVersion++;
        $this->doc_files = [];
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
        return view('livewire.client-portal.application-form', [
            'countries' => Country::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'trades' => Trade::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'companies' => Company::query()->where('type', 'employer')->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'careOffs' => CareOff::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'documents' => $this->documentRows(),
            'age' => $this->age,
            'maxUploadMb' => max(1, (int) ceil(ClientPrivateFiles::maxKilobytes() / 1024)),
        ])->layout('layouts.client-portal-app', [
            'title' => $this->clientId ? 'Edit application' : 'Fill application',
        ]);
    }

    private function applyJobFromSession(): void
    {
        $jobId = (int) session('apply_job_id', 0);

        if ($jobId <= 0) {
            return;
        }

        $job = JobApplyPrefill::approvedJob($jobId);

        if (! $job) {
            session()->forget('apply_job_id');

            return;
        }

        $this->job_post_id = (string) $job->id;
        $this->appliedJobTitle = $job->title;
        $this->appliedJobCompany = $job->employer?->company_name;

        $matched = JobApplyPrefill::matchIds($job);

        if ($this->trade_id === '' && $matched['trade_id']) {
            $this->trade_id = (string) $matched['trade_id'];
        }

        if ($this->country_id === '' && $matched['country_id']) {
            $this->country_id = (string) $matched['country_id'];
        }

        if ($this->company_id === '' && $matched['company_id']) {
            $this->company_id = (string) $matched['company_id'];
        }
    }

    private function loadClient(Client $client): void
    {
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
        $this->place_of_birth = (string) ($client->place_of_birth ?? '');
        $this->phone = (string) ($client->phone ?? '');
        $this->email = (string) ($client->email ?? '');
        $this->address = (string) ($client->address ?? '');
        $this->police_station = (string) ($client->police_station ?? '');
        $this->district = (string) ($client->district ?? '');
        $this->medical_fitness = (string) ($client->medical_fitness ?? '');
        $this->photo_path = $client->photo_path;
        $this->passport_number = $client->passport_number;
        $this->passport_issue_date = optional($client->passport_issue_date)->format('Y-m-d') ?? '';
        $this->passport_expiry = optional($client->passport_expiry)->format('Y-m-d') ?? '';
        $this->degree = (string) ($client->degree ?? '');
        $this->degree_year = (string) ($client->degree_year ?? '');
        $this->certification = (string) ($client->certification ?? '');
        $this->certification_year = (string) ($client->certification_year ?? '');
        $this->board_university = (string) ($client->board_university ?? '');
        $this->languages = (string) ($client->languages ?? '');
        $this->total_experience = (string) ($client->total_experience ?? '');
        $this->experiences = $client->experienceRowsForForm();
        $this->source = $client->source ?: 'direct';
        $this->care_off_id = (string) ($client->care_off_id ?? '');
        $this->company_id = (string) ($client->company_id ?? '');
        $this->job_post_id = (string) ($client->job_post_id ?? '');

        if ($client->jobPost) {
            $this->appliedJobTitle = $client->jobPost->title;
            $this->appliedJobCompany = $client->jobPost->employer?->company_name;
        }

        $this->existing_docs = [];
        $this->doc_not_required = [];

        foreach ($this->documentRows() as $row) {
            $doc = $client->documents->firstWhere('doc_key', $row['key']);
            $this->doc_not_required[$row['key']] = (bool) ($doc?->is_not_required);
            $this->existing_docs[$row['key']] = $doc && filled($doc->file_path)
                ? ($doc->original_name ?: 'Uploaded')
                : null;
        }
    }

    private function photoRules(): array
    {
        $maxKb = ClientPrivateFiles::maxKilobytes();
        $mimes = implode(',', ClientPrivateFiles::photoMimes());

        return ['nullable', 'image', 'max:'.$maxKb, 'mimes:'.$mimes, ClientPrivateFiles::contentRule(true)];
    }

    private function labels(): array
    {
        return [
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
        ];
    }

    private function resetDocumentChecks(): void
    {
        $this->doc_not_required = [];
        $this->existing_docs = [];

        foreach ($this->documentRows() as $row) {
            $this->doc_not_required[$row['key']] = false;
            $this->existing_docs[$row['key']] = null;
        }
    }

    private function deletePrivatePath(?string $path): void
    {
        if (! $path) {
            return;
        }

        if (! ClientPrivateFiles::pathBelongsToClient((int) $this->clientId, $path)) {
            return;
        }

        Storage::disk(ClientPrivateFiles::disk())->delete($path);
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
