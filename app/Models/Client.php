<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'country_id',
        'trade_id',
        'company_id',
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
        'degree',
        'degree_year',
        'certification',
        'certification_year',
        'board_university',
        'languages',
        'total_experience',
        'source',
        'overall_status',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'passport_issue_date' => 'date',
            'passport_expiry' => 'date',
        ];
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

    public function trade(): BelongsTo
    {
        return $this->belongsTo(Trade::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
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

    public function documents(): HasMany
    {
        return $this->hasMany(ClientDocument::class);
    }
}
