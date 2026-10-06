<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Company extends Model
{
    protected $fillable = [
        'name',
        'type',
        'country_id',
        'is_active',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'university' => 'University',
            default => 'Employer',
        };
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
