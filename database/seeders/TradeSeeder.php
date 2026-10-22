<?php

namespace Database\Seeders;

use App\Models\Trade;
use Illuminate\Database\Seeder;

class TradeSeeder extends Seeder
{
    public function run(): void
    {
        $trades = [
            'General Labor',
            'Electrician',
            'Plumber',
            'Welder',
            'Carpenter',
            'Mason',
            'Painter',
            'Driver (Light)',
            'Driver (Heavy)',
            'Cook',
            'Waiter',
            'Cleaner',
            'Security Guard',
            'Nurse',
            'Technician',
            'AC Technician',
            'Steel Fixer',
            'Scaffolding',
            'Helper',
            'Office Staff',
        ];

        foreach ($trades as $name) {
            Trade::query()->updateOrCreate(
                ['name' => $name],
                ['is_active' => true]
            );
        }
    }
}
