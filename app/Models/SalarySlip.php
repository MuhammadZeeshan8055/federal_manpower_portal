<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalarySlip extends Model
{
    protected $fillable = [
        'user_id',
        'month',
        'year',
        'gross_amount',
        'unpaid_leave_days',
        'per_day_deduction',
        'unpaid_leave_deduction',
        'other_deductions',
        'net_amount',
        'notes',
        'generated_by',
    ];

    protected function casts(): array
    {
        return [
            'gross_amount' => 'decimal:2',
            'unpaid_leave_days' => 'decimal:2',
            'per_day_deduction' => 'decimal:2',
            'unpaid_leave_deduction' => 'decimal:2',
            'other_deductions' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'month' => 'integer',
            'year' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function monthLabel(): string
    {
        return date('F', mktime(0, 0, 0, $this->month, 1));
    }
}
