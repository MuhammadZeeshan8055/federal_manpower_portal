<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Support\PortalModules;

class DashboardController extends Controller
{
    public function index($module = null, $feature = null)
    {
        $modulesMap = PortalModules::mapForUser();

        // If module is wrong, go to home
        if ($module && ! isset($modulesMap[$module])) {
            return redirect('/dashboard');
        }

        // If feature is wrong, go to that module page
        if ($module && $feature) {
            $featureOk = false;

            foreach ($modulesMap[$module]['children'] ?? [] as $child) {
                if ($child['key'] === $feature) {
                    $featureOk = true;
                    break;
                }
            }

            if (! $featureOk) {
                return redirect('/dashboard/'.$module);
            }
        }

        return view('dashboard', [
            'modules' => PortalModules::forUser(),
            'modulesMap' => $modulesMap,
            'startModule' => $module ? $module : '',
            'startFeature' => $feature ? $feature : '',
            'countries' => Country::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'code', 'flag_image']),
            'workspaceCountryId' => session('workspace_country_id'),
        ]);
    }
}
