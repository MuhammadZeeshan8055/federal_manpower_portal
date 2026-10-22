<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visitor extends Model
{
    protected $fillable = [
        'name',
        'contact_no',
        'purpose',
        'meet_with',
        'outcome',
        'remarks',
        'remind_on',
        'visited_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'visited_at' => 'datetime',
            'remind_on' => 'date',
        ];
    }

    public static function outcomeOptions(): array
    {
        return [
            'mature' => 'Mature',
            'immature' => 'Immature',
        ];
    }

    public function outcomeLabel(): string
    {
        return self::outcomeOptions()[$this->outcome] ?? $this->outcome;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
