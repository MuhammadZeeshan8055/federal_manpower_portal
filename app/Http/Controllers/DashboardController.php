<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Support\PortalModules;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard', [
            'modules' => PortalModules::forUser(),
            'modulesMap' => PortalModules::mapForUser(),
            'countries' => Country::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'code', 'flag_image']),
            'workspaceCountryId' => session('workspace_country_id'),
        ]);
    }
}
