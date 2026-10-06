<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'Serbia', 'code' => 'RS', 'flag_image' => 'images/flags/rs.svg'],
            ['name' => 'Seribia-Study', 'code' => 'RS-ST', 'flag_image' => 'images/flags/rs.svg'],
            ['name' => 'Romania', 'code' => 'RO', 'flag_image' => 'images/flags/ro.svg'],
            ['name' => 'Cyprus', 'code' => 'CY', 'flag_image' => 'images/flags/cy.svg'],
            ['name' => 'Cyprus-Urgent', 'code' => 'CY-UR', 'flag_image' => 'images/flags/cy.svg'],
            ['name' => 'Turkey', 'code' => 'TR', 'flag_image' => 'images/flags/tr.svg'],
            ['name' => 'Ukraine', 'code' => 'UA', 'flag_image' => 'images/flags/ua.svg'],
            ['name' => 'Bosnia', 'code' => 'BA', 'flag_image' => 'images/flags/ba.svg'],
            ['name' => 'France', 'code' => 'FR', 'flag_image' => 'images/flags/fr.svg'],
            ['name' => 'Qatar', 'code' => 'QA', 'flag_image' => 'images/flags/qa.svg'],
            ['name' => 'UAE', 'code' => 'AE', 'flag_image' => 'images/flags/ae.svg'],
            ['name' => 'Italy', 'code' => 'IT', 'flag_image' => 'images/flags/it.svg'],
            ['name' => 'Slovenia', 'code' => 'SI', 'flag_image' => 'images/flags/si.svg'],
            ['name' => 'Belarus', 'code' => 'BY', 'flag_image' => 'images/flags/by.svg'],
            ['name' => 'Spain', 'code' => 'ES', 'flag_image' => 'images/flags/es.svg'],
            ['name' => 'KSA', 'code' => 'SA', 'flag_image' => 'images/flags/sa.svg'],
            ['name' => 'Oman', 'code' => 'OM', 'flag_image' => 'images/flags/om.svg'],
            ['name' => 'Moldova', 'code' => 'MD', 'flag_image' => 'images/flags/md.svg'],
            ['name' => 'Georgia', 'code' => 'GE', 'flag_image' => 'images/flags/ge.svg'],
            ['name' => 'Hungary', 'code' => 'HU', 'flag_image' => 'images/flags/hu.svg'],
            ['name' => 'Portugal', 'code' => 'PT', 'flag_image' => 'images/flags/pt.svg'],
            ['name' => 'Albania', 'code' => 'AL', 'flag_image' => 'images/flags/al.svg'],
            ['name' => 'Greece', 'code' => 'GR', 'flag_image' => 'images/flags/gr.svg'],
            ['name' => 'Austria', 'code' => 'AT', 'flag_image' => 'images/flags/at.svg'],
        ] as $country) {
            Country::query()->updateOrCreate(
                ['code' => $country['code']],
                [
                    'name' => $country['name'],
                    'flag_image' => $country['flag_image'],
                    'is_active' => true,
                ]
            );
        }

        User::query()->updateOrCreate(
            ['email' => 'admin@federal.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ]
        );

        $hr = User::query()->updateOrCreate(
            ['email' => 'hr@federal.com'],
            [
                'name' => 'HR User',
                'password' => Hash::make('password'),
                'role' => 'hr',
                'email_verified_at' => now(),
            ]
        );

        $accounts = User::query()->updateOrCreate(
            ['email' => 'accounts@federal.com'],
            [
                'name' => 'Accounts User',
                'password' => Hash::make('password'),
                'role' => 'accounts',
                'email_verified_at' => now(),
            ]
        );

        $staff = User::query()->updateOrCreate(
            ['email' => 'staff@federal.com'],
            [
                'name' => 'Staff User',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'email_verified_at' => now(),
            ]
        );

        $this->syncPermissions($hr, [
            ['attendance', 'my-daily-attendance', 'manage'],
            ['attendance', 'apply-leave', 'manage'],
            ['attendance', 'staff-daily-attendance', 'manage'],
            ['attendance', 'leave-approvals', 'manage'],
            ['attendance', 'salary-slips', 'manage'],
            ['attendance', 'holidays', 'manage'],
        ]);

        $this->syncPermissions($accounts, [
            ['accounts', 'payments', 'manage'],
            ['accounts', 'payment_methods', 'manage'],
            ['accounts', 'reports', 'view'],
        ]);

        $this->syncPermissions($staff, [
            ['clients', 'list', 'view'],
            ['clients', 'documents', 'view'],
            ['attendance', 'my-daily-attendance', 'manage'],
            ['attendance', 'apply-leave', 'manage'],
        ]);
    }

    private function syncPermissions(User $user, array $rows): void
    {
        UserPermission::query()->where('user_id', $user->id)->delete();

        foreach ($rows as [$module, $feature, $access]) {
            UserPermission::query()->create([
                'user_id' => $user->id,
                'module_key' => $module,
                'feature_key' => $feature,
                'access' => $access,
            ]);
        }
    }
}
