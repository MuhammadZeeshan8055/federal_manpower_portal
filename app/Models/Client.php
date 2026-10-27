<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'user_id',
        'country_id',
        'converted_from_country_id',
        'trade_id',
        'company_id',
        'batch_id',
        'job_post_id',
        'care_off_id',
        'process_status_id',
        'created_by',
        'full_name',
        'height',
        'weight',
        'father_name',
        'mother_name',
        'gender',
        'date_of_birth',
        'marital_status',
        'religion',
        'cnic',
        'place_of_birth',
        'phone',
        'email',
        'address',
        'police_station',
        'district',
        'medical_fitness',
        'photo_path',
        'passport_number',
        'passport_issue_date',
        'passport_expiry',
        'passport_available',
        'degree',
        'degree_year',
        'certification',
        'certification_year',
        'board_university',
        'languages',
        'total_experience',
        'experience_rows',
        'source',
        'overall_status',
        'public_token',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'passport_issue_date' => 'date',
            'passport_expiry' => 'date',
            'experience_rows' => 'array',
        ];
    }

    public static function emptyExperienceRows(): array
    {
        $rows = [];

        for ($i = 0; $i < 5; $i++) {
            $rows[] = [
                'from_to' => '',
                'company' => '',
                'project' => '',
                'country' => '',
                'job_title' => '',
            ];
        }

        return $rows;
    }

    public static function cleanExperienceRows(array $rows): ?array
    {
        $clean = [];

        foreach ($rows as $row) {
            $fromTo = trim((string) ($row['from_to'] ?? ''));
            $company = trim((string) ($row['company'] ?? ''));
            $project = trim((string) ($row['project'] ?? ''));
            $country = trim((string) ($row['country'] ?? ''));
            $jobTitle = trim((string) ($row['job_title'] ?? ''));

            if ($fromTo === '' && $company === '' && $project === '' && $country === '' && $jobTitle === '') {
                continue;
            }

            $clean[] = [
                'from_to' => $fromTo,
                'company' => $company,
                'project' => $project,
                'country' => $country,
                'job_title' => $jobTitle,
            ];

            if (count($clean) >= 5) {
                break;
            }
        }

        return $clean === [] ? null : $clean;
    }

    public function experienceRowsForForm(): array
    {
        $rows = self::emptyExperienceRows();
        $saved = $this->experience_rows ?? [];

        foreach ($saved as $i => $row) {
            if ($i >= 5) {
                break;
            }

            $rows[$i] = [
                'from_to' => (string) ($row['from_to'] ?? ''),
                'company' => (string) ($row['company'] ?? ''),
                'project' => (string) ($row['project'] ?? ''),
                'country' => (string) ($row['country'] ?? ''),
                'job_title' => (string) ($row['job_title'] ?? ''),
            ];
        }

        return $rows;
    }

    public function getAgeAttribute(): ?int
    {
        if (! $this->date_of_birth) {
            return null;
        }

        try {
            return Carbon::parse($this->date_of_birth)->age;
        } catch (\Throwable) {
            return null;
        }
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function convertedFromCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'converted_from_country_id');
    }

    public function trade(): BelongsTo
    {
        return $this->belongsTo(Trade::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function jobPost(): BelongsTo
    {
        return $this->belongsTo(JobPost::class);
    }

    public function careOff(): BelongsTo
    {
        return $this->belongsTo(CareOff::class);
    }

    public function processStatus(): BelongsTo
    {
        return $this->belongsTo(ProcessStatus::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ClientDocument::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->orderByDesc('paid_on')->orderByDesc('id');
    }
}
