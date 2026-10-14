<?php

namespace App\Support;

use App\Models\ProcessStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ModuleStats
{
    public static function cardsFor(string $moduleKey): array
    {
        if ($moduleKey === '') {
            return [];
        }

        if ($moduleKey === 'clients') {
            return self::clientCards();
        }

        return self::simpleFeatureCards($moduleKey);
    }

    private static function clientCards(): array
    {
        $cards = [];

        $cards[] = [
            'key' => 'list',
            'label' => 'All Clients',
            'value' => self::countRows('clients'),
            'tone' => '#3397c7',
            'process_status_id' => null,
            'clickable' => true,
        ];

        if (! Schema::hasTable('process_statuses') || ! Schema::hasTable('clients')) {
            return $cards;
        }

        $statuses = ProcessStatus::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        foreach ($statuses as $status) {
            $count = (int) DB::table('clients')
                ->where('process_status_id', $status->id)
                ->count();

            $cards[] = [
                'key' => 'status_'.$status->id,
                'label' => $status->name,
                'value' => $count,
                'tone' => self::colorForStatus($status->name),
                'process_status_id' => $status->id,
                'clickable' => true,
            ];
        }

        return $cards;
    }

    private static function simpleFeatureCards(string $moduleKey): array
    {
        $modules = PortalModules::forUser();
        $module = null;

        foreach ($modules as $item) {
            if ($item['key'] === $moduleKey) {
                $module = $item;
                break;
            }
        }

        if (! $module) {
            return [];
        }

        // Which DB table each feature uses
        $tables = [
            'operations' => [
                'care_offs' => 'care_offs',
                'companies' => 'companies:employer',
                'universities' => 'companies:university',
                'countries' => 'countries',
                'trades' => 'trades',
                'process_statuses' => 'process_statuses',
            ],
            'visitors' => [
                'list' => 'visitors',
            ],
            'accounts' => [
                'payments' => 'payments',
                'payment_methods' => 'payment_methods',
                'reports' => 'account_reports',
            ],
            'attendance' => [
                'my-daily-attendance' => 'attendance_records',
                'apply-leave' => 'leave_requests',
                'staff-daily-attendance' => 'attendance_records',
                'leave-approvals' => 'leave_requests',
                'salary-slips' => 'salary_slips',
                'office-settings' => 'office_settings',
                'holidays' => 'holidays',
            ],
            'settings' => [
                'users' => 'users',
            ],
        ];

        $cards = [];
        $moduleTables = $tables[$moduleKey] ?? [];

        foreach ($module['children'] ?? [] as $feature) {
            $featureKey = $feature['key'];
            $source = $moduleTables[$featureKey] ?? null;

            $cards[] = [
                'key' => $featureKey,
                'label' => $feature['label'],
                'value' => self::countFromSource($source),
                'tone' => null,
                'process_status_id' => null,
                'clickable' => false,
            ];
        }

        return $cards;
    }

    private static function countFromSource(?string $source): int
    {
        if ($source === null || $source === '') {
            return 0;
        }

        // Special case: companies:employer or companies:university
        if (str_contains($source, ':')) {
            [$table, $type] = explode(':', $source, 2);

            if (! Schema::hasTable($table)) {
                return 0;
            }

            return (int) DB::table($table)->where('type', $type)->count();
        }

        return self::countRows($source);
    }

    private static function countRows(string $table): int
    {
        if (! Schema::hasTable($table)) {
            return 0;
        }

        return (int) DB::table($table)->count();
    }

    private static function colorForStatus(string $name): string
    {
        $name = strtolower($name);

        if (str_contains($name, 'refus') || str_contains($name, 'reject') || str_contains($name, 'denied') || str_contains($name, 'fail') || str_contains($name, 'cancel')) {
            return '#e05d68';
        }

        if (str_contains($name, 'issued') || str_contains($name, 'approved') || str_contains($name, 'complete') || str_contains($name, 'success')) {
            return '#43b96e';
        }

        if (str_contains($name, 'ready') || str_contains($name, 'pending') || str_contains($name, 'waiting') || str_contains($name, 'review')) {
            return '#dca139';
        }

        if (str_contains($name, 'applied') || str_contains($name, 'submitted') || str_contains($name, 'process')) {
            return '#7b79cf';
        }

        return '#3397c7';
    }
}
