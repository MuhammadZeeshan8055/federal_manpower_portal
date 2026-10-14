<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Support\PortalModules;

class DashboardController extends Controller
{
    // Used on first visit / refresh / shared link
    // Alpine handles clicks after that (no reload)
    public function index($module = null, $feature = null)
    {
        $modulesMap = PortalModules::mapForUser();

        if ($module && empty($modulesMap[$module])) {
            return redirect('/dashboard');
        }

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
            'startModule' => $module ?: '',
            'startFeature' => $feature ?: '',
            'countries' => Country::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'code', 'flag_image']),
            'workspaceCountryId' => session('workspace_country_id'),
        ]);
    }
}
