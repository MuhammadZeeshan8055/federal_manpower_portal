<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CareOff extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
