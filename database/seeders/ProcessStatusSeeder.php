<?php

namespace Database\Seeders;

use App\Models\ProcessStatus;
use Illuminate\Database\Seeder;

class ProcessStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            'Applied',
            'Documents collected',
            'File under process',
            'Interview scheduled',
            'Selected',
            'Medical',
            'Visa applied',
            'Visa issued',
            'Ticket booked',
            'Deployed',
            'On hold',
            'Rejected',
        ];

        foreach ($statuses as $index => $name) {
            ProcessStatus::query()->updateOrCreate(
                ['name' => $name],
                [
                    'sort_order' => ($index + 1) * 10,
                    'is_active' => true,
                ]
            );
        }
    }
}
