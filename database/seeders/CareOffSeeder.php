<?php

namespace Database\Seeders;

use App\Models\CareOff;
use Illuminate\Database\Seeder;

class CareOffSeeder extends Seeder
{
    public function run(): void
    {
        $careOffs = [
            [
                'name' => 'Direct Walk-in',
                'phone' => null,
                'email' => null,
            ],
            [
                'name' => 'Ali Referral Agency',
                'phone' => '0300-1112233',
                'email' => 'ali.referral@example.com',
            ],
            [
                'name' => 'City Manpower Link',
                'phone' => '0321-4455667',
                'email' => 'city.link@example.com',
            ],
            [
                'name' => 'Green Path Consultants',
                'phone' => '0333-7788990',
                'email' => 'green.path@example.com',
            ],
            [
                'name' => 'Online Portal Lead',
                'phone' => null,
                'email' => 'leads@federal.local',
            ],
        ];

        foreach ($careOffs as $row) {
            CareOff::query()->updateOrCreate(
                ['name' => $row['name']],
                [
                    'phone' => $row['phone'],
                    'email' => $row['email'],
                    'is_active' => true,
                ]
            );
        }
    }
}
