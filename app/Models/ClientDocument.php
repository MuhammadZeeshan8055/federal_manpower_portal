<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientDocument extends Model
{
    protected $fillable = [
        'client_id',
        'doc_key',
        'label',
        'file_path',
        'original_name',
        'is_not_required',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'is_not_required' => 'boolean',
            'uploaded_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function hasFile(): bool
    {
        return filled($this->file_path);
    }
}
