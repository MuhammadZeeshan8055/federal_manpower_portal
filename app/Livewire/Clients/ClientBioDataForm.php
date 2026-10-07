<?php

namespace App\Livewire\Clients;

use App\Models\CareOff;
use App\Models\Company;
use App\Models\Country;
use App\Models\ProcessStatus;
use App\Models\Trade;
use Carbon\Carbon;
use Livewire\Component;

class ClientBioDataForm extends Component
{
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

    public bool $sectionsComplete = false;

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

    public function continueDocuments(): void
    {
        $this->sectionsComplete = true;
    }

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user || (! $user->canView('clients', 'create') && ! $user->canManage('clients', 'create'))) {
            $this->denied = true;
        }

        foreach ($this->documentRows() as $row) {
            $this->doc_not_required[$row['key']] = false;
        }
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
        ]);
    }
}
