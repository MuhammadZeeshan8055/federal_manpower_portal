<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Support\PortalModules;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $modules = PortalModules::forUser();

        return view('dashboard', [
            'modules' => $modules,
            'modulesMap' => PortalModules::mapForUser(),
            'moduleStats' => $this->moduleStats($modules),
            'countries' => Country::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'code', 'flag_image']),
            'workspaceCountryId' => session('workspace_country_id'),
        ]);
    }

    private function moduleStats(array $modules): array
    {
        $tables = [
            'clients' => [
                'list' => 'clients',
                'documents' => 'client_documents',
                'import' => 'client_imports',
            ],
            'care_offs' => ['list' => 'care_offs'],
            'operations' => [
                'companies' => 'companies',
                'countries' => 'countries',
                'trades' => 'trades',
                'process_statuses' => 'process_statuses',
            ],
            'visitors' => ['list' => 'visitors'],
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
            'settings' => ['users' => 'users'],
        ];

        $stats = [];
        $counts = [];

        foreach ($modules as $module) {
            $moduleKey = $module['key'];

            foreach ($module['children'] ?? [] as $feature) {
                $featureKey = $feature['key'];
                $table = $tables[$moduleKey][$featureKey] ?? null;

                if ($table !== null && ! array_key_exists($table, $counts)) {
                    $counts[$table] = Schema::hasTable($table)
                        ? DB::table($table)->count()
                        : 0;
                }

                $stats[$moduleKey][] = [
                    'key' => $featureKey,
                    'label' => $feature['label'],
                    'value' => $table !== null ? $counts[$table] : 0,
                ];
            }
        }

        return $stats;
    }
}
