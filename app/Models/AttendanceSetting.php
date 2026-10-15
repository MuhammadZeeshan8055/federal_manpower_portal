<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    protected $fillable = [
        'office_start',
        'office_end',
        'required_hours',
        'lunch_start',
        'lunch_end',
        'break_minutes',
        'office_ip_1',
        'office_ip_2',
        'office_ip_3',
        'monthly_full_days',
        'monthly_half_days',
        'grace_minutes',
    ];

    // Get the one settings row (create defaults if empty)
    public static function current(): self
    {
        $row = self::query()->first();

        if ($row) {
            return $row;
        }

        return self::query()->create([
            'office_start' => '10:00:00',
            'office_end' => '18:00:00',
            'required_hours' => 8,
            'lunch_start' => '13:00:00',
            'lunch_end' => '14:00:00',
            'break_minutes' => 60,
            'office_ip_1' => null,
            'office_ip_2' => null,
            'office_ip_3' => null,
            'monthly_full_days' => 1,
            'monthly_half_days' => 2,
            'grace_minutes' => 15,
        ]);
    }
}
