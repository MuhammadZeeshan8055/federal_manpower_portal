<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\ProcessStatus;
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
            'operations' => [
                'care_offs' => 'care_offs',
                'companies' => ['table' => 'companies', 'type' => 'employer'],
                'universities' => ['table' => 'companies', 'type' => 'university'],
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

            if ($moduleKey === 'clients') {
                $stats[$moduleKey] = $this->clientModuleStats();

                continue;
            }

            foreach ($module['children'] ?? [] as $feature) {
                $featureKey = $feature['key'];
                $source = $tables[$moduleKey][$featureKey] ?? null;
                $countKey = is_array($source)
                    ? ($source['table'].':'.($source['type'] ?? ''))
                    : $source;

                if ($source !== null && ! array_key_exists($countKey, $counts)) {
                    $table = is_array($source) ? $source['table'] : $source;

                    if (! Schema::hasTable($table)) {
                        $counts[$countKey] = 0;
                    } elseif (is_array($source) && isset($source['type'])) {
                        $counts[$countKey] = DB::table($table)
                            ->where('type', $source['type'])
                            ->count();
                    } else {
                        $counts[$countKey] = DB::table($table)->count();
                    }
                }

                $stats[$moduleKey][] = [
                    'key' => $featureKey,
                    'label' => $feature['label'],
                    'value' => $countKey !== null ? $counts[$countKey] : 0,
                    'process_status_id' => null,
                    'clickable' => false,
                ];
            }
        }

        return $stats;
    }

    private function clientModuleStats(): array
    {
        $cards = [];

        $totalClients = Schema::hasTable('clients')
            ? (int) DB::table('clients')->count()
            : 0;

        $cards[] = [
            'key' => 'list',
            'label' => 'All Clients',
            'value' => $totalClients,
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

        $statusCounts = DB::table('clients')
            ->select('process_status_id', DB::raw('count(*) as total'))
            ->whereNotNull('process_status_id')
            ->groupBy('process_status_id')
            ->pluck('total', 'process_status_id');

        foreach ($statuses as $status) {
            $cards[] = [
                'key' => 'status_'.$status->id,
                'label' => $status->name,
                'value' => (int) ($statusCounts[$status->id] ?? 0),
                'process_status_id' => $status->id,
                'clickable' => true,
            ];
        }

        return $cards;
    }
}
