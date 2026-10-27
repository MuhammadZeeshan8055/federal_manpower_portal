<?php

namespace App\Models;

use App\Support\JobPosters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobPost extends Model
{
    protected $fillable = [
        'employer_profile_id',
        'title',
        'category',
        'country',
        'city',
        'employment_type',
        'experience',
        'salary_text',
        'vacancies',
        'description',
        'poster_path',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'vacancies' => 'integer',
            'approved_at' => 'datetime',
        ];
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(EmployerProfile::class, 'employer_profile_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function posterUrl(): ?string
    {
        return JobPosters::url($this->poster_path);
    }

    public function hasPoster(): bool
    {
        return $this->posterUrl() !== null;
    }
}
