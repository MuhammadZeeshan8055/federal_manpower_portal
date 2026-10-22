<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StaffLoan extends Model
{
    protected $fillable = [
        'user_id',
        'principal',
        'issued_on',
        'notes',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'principal' => 'decimal:2',
            'issued_on' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(StaffLoanPayment::class);
    }

    public function totalPaid(): float
    {
        if ($this->relationLoaded('payments')) {
            return (float) $this->payments->sum('amount');
        }

        return (float) $this->payments()->sum('amount');
    }

    public function remaining(): float
    {
        $left = (float) $this->principal - $this->totalPaid();

        if ($left < 0) {
            return 0;
        }

        return $left;
    }

    public function refreshStatus(): void
    {
        if ($this->remaining() <= 0) {
            $this->status = 'closed';
        } else {
            $this->status = 'open';
        }

        $this->save();
    }
}
