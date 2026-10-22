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
        'follow_up_status',
        'follow_up_remarks',
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

    public static function followUpStatusOptions(): array
    {
        return [
            'pending' => 'Pending',
            'done' => 'Follow up done',
        ];
    }

    public function outcomeLabel(): string
    {
        return self::outcomeOptions()[$this->outcome] ?? $this->outcome;
    }

    public function followUpStatusLabel(): string
    {
        return self::followUpStatusOptions()[$this->follow_up_status] ?? $this->follow_up_status;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
