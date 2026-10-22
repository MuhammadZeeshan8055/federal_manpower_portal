<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Country;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        // Employers (overseas companies)
        $employers = [
            ['name' => 'Gulf Build Co', 'code' => 'AE'],
            ['name' => 'Qatar Facility Services', 'code' => 'QA'],
            ['name' => 'Riyadh Industrial Group', 'code' => 'SA'],
            ['name' => 'Muscat Logistics LLC', 'code' => 'OM'],
            ['name' => 'Cyprus Hotel Partners', 'code' => 'CY'],
            ['name' => 'Romania Agro Works', 'code' => 'RO'],
            ['name' => 'Serbia Construction Ltd', 'code' => 'RS'],
            ['name' => 'Istanbul Tech Staffing', 'code' => 'TR'],
        ];

        foreach ($employers as $row) {
            $country = Country::query()->where('code', $row['code'])->first();

            if (! $country) {
                continue;
            }

            Company::query()->updateOrCreate(
                [
                    'name' => $row['name'],
                    'type' => 'employer',
                    'country_id' => $country->id,
                ],
                ['is_active' => true]
            );
        }

        // Universities
        $universities = [
            ['name' => 'University of Belgrade', 'code' => 'RS'],
            ['name' => 'Belgrade Study Center', 'code' => 'RS-ST'],
            ['name' => 'Bucharest Technical University', 'code' => 'RO'],
            ['name' => 'Nicosia International College', 'code' => 'CY'],
            ['name' => 'Istanbul Campus Pathway', 'code' => 'TR'],
            ['name' => 'Budapest Study Hub', 'code' => 'HU'],
        ];

        foreach ($universities as $row) {
            $country = Country::query()->where('code', $row['code'])->first();

            if (! $country) {
                continue;
            }

            Company::query()->updateOrCreate(
                [
                    'name' => $row['name'],
                    'type' => 'university',
                    'country_id' => $country->id,
                ],
                ['is_active' => true]
            );
        }
    }
}
