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
        'status',
        'visited_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'visited_at' => 'datetime',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            'waiting' => 'Waiting',
            'in_meeting' => 'In meeting',
            'meeting_done' => 'Meeting done',
            'visit_complete' => 'Visit complete',
        ];
    }

    public function statusLabel(): string
    {
        return self::statusOptions()[$this->status] ?? $this->status;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
