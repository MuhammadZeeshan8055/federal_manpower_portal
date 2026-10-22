<?php

namespace App\Support;

use App\Models\ProcessStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ModuleStats
{
    public static function cardsFor($moduleKey)
    {
        if ($moduleKey === '') {
            return [];
        }

        if ($moduleKey === 'clients') {
            return self::clientCards();
        }

        return self::otherModuleCards($moduleKey);
    }

    private static function clientCards()
    {
        $cards = [];
        $countryId = WorkspaceCountry::id();

        // Card 1: all clients (for selected country, or all)
        $allQuery = DB::table('clients');
        if ($countryId) {
            $allQuery->where('country_id', $countryId);
        }

        $cards[] = [
            'key' => 'list',
            'label' => 'All Clients',
            'value' => Schema::hasTable('clients') ? (int) $allQuery->count() : 0,
            'tone' => '#3397c7',
            'process_status_id' => null,
            'clickable' => true,
        ];

        if (! Schema::hasTable('process_statuses') || ! Schema::hasTable('clients')) {
            return $cards;
        }

        // One card per process status
        $statuses = ProcessStatus::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        foreach ($statuses as $status) {
            $statusQuery = DB::table('clients')
                ->where('process_status_id', $status->id);

            if ($countryId) {
                $statusQuery->where('country_id', $countryId);
            }

            $cards[] = [
                'key' => 'status_'.$status->id,
                'label' => $status->name,
                'value' => (int) $statusQuery->count(),
                'tone' => self::statusColor($status->name),
                'process_status_id' => $status->id,
                'clickable' => true,
            ];
        }

        return $cards;
    }

    private static function otherModuleCards($moduleKey)
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

        // feature key => database table
        $tables = [
            'care_offs' => 'care_offs',
            'companies' => 'companies',
            'universities' => 'companies',
            'countries' => 'countries',
            'trades' => 'trades',
            'process_statuses' => 'process_statuses',
            'list' => 'visitors',
            'reminders' => 'visitors',
            'payments' => 'payments',
            'payment_methods' => 'payment_methods',
            'reports' => 'account_reports',
            'users' => 'users',
            'my-daily-attendance' => 'attendance_records',
            'my-loans' => 'staff_loans',
            'apply-leave' => 'leave_requests',
            'staff-daily-attendance' => 'attendance_records',
            'leave-approvals' => 'leave_requests',
            'salary-slips' => 'salary_slips',
            'staff-loans' => 'staff_loans',
            'office-settings' => 'attendance_settings',
            'holidays' => 'holidays',
        ];

        $cards = [];

        foreach ($module['children'] ?? [] as $feature) {
            $featureKey = $feature['key'];
            $table = $tables[$featureKey] ?? null;
            $count = 0;

            if ($table) {
                if ($featureKey === 'companies') {
                    $count = self::companyCount('employer');
                } elseif ($featureKey === 'universities') {
                    $count = self::companyCount('university');
                } elseif ($featureKey === 'reminders') {
                    $count = self::pendingReminderCount();
                } elseif ($featureKey === 'leave-approvals') {
                    $count = self::pendingLeaveCount();
                } elseif ($featureKey === 'holidays') {
                    $count = self::tableCount('holidays');
                } elseif ($featureKey === 'staff-loans') {
                    $count = self::openLoanCount();
                } elseif ($featureKey === 'my-loans') {
                    $count = self::myOpenLoanCount();
                } else {
                    $count = self::tableCount($table);
                }
            }

            $cards[] = [
                'key' => $featureKey,
                'label' => $feature['label'],
                'value' => $count,
                'tone' => null,
                'process_status_id' => null,
                'clickable' => false,
            ];
        }

        return $cards;
    }

    private static function tableCount($table)
    {
        if (! Schema::hasTable($table)) {
            return 0;
        }

        return (int) DB::table($table)->count();
    }

    private static function pendingReminderCount()
    {
        if (! Schema::hasTable('visitors')) {
            return 0;
        }

        return (int) DB::table('visitors')
            ->whereNotNull('remind_on')
            ->where('follow_up_status', 'pending')
            ->count();
    }

    private static function pendingLeaveCount()
    {
        if (! Schema::hasTable('leave_requests')) {
            return 0;
        }

        return (int) DB::table('leave_requests')
            ->where('status', 'pending')
            ->count();
    }

    private static function openLoanCount()
    {
        if (! Schema::hasTable('staff_loans')) {
            return 0;
        }

        return (int) DB::table('staff_loans')
            ->where('status', 'open')
            ->count();
    }

    private static function myOpenLoanCount()
    {
        if (! Schema::hasTable('staff_loans') || ! auth()->id()) {
            return 0;
        }

        return (int) DB::table('staff_loans')
            ->where('user_id', auth()->id())
            ->where('status', 'open')
            ->count();
    }

    private static function companyCount($type)
    {
        if (! Schema::hasTable('companies')) {
            return 0;
        }

        $query = DB::table('companies')->where('type', $type);
        $countryId = WorkspaceCountry::id();

        if ($countryId) {
            $query->where('country_id', $countryId);
        }

        return (int) $query->count();
    }

    private static function statusColor($name)
    {
        $name = strtolower($name);

        if (str_contains($name, 'refus') || str_contains($name, 'reject') || str_contains($name, 'cancel')) {
            return '#e05d68';
        }

        if (str_contains($name, 'issued') || str_contains($name, 'approved') || str_contains($name, 'complete')) {
            return '#43b96e';
        }

        if (str_contains($name, 'ready') || str_contains($name, 'pending') || str_contains($name, 'waiting')) {
            return '#dca139';
        }

        if (str_contains($name, 'applied') || str_contains($name, 'process')) {
            return '#7b79cf';
        }

        return '#3397c7';
    }
}
