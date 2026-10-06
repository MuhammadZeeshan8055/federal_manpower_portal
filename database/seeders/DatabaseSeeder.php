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
            ['name' => 'Saudi Arabia', 'code' => 'SA', 'flag_image' => 'images/flags/sa.svg'],
            ['name' => 'United Arab Emirates', 'code' => 'AE', 'flag_image' => 'images/flags/ae.svg'],
            ['name' => 'Qatar', 'code' => 'QA', 'flag_image' => 'images/flags/qa.svg'],
            ['name' => 'Oman', 'code' => 'OM', 'flag_image' => 'images/flags/om.svg'],
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
