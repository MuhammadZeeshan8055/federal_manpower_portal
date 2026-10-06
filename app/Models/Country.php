<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'name',
        'code',
        'flag_image',
        'is_active',
    ];

    public function flagUrl(): string
    {
        if ($this->flag_image) {
            return asset($this->flag_image);
        }

        return asset('images/flags/world.svg');
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
