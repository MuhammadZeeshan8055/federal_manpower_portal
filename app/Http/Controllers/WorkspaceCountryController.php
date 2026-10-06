<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WorkspaceCountryController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'country_id' => ['nullable', 'integer', 'exists:countries,id'],
        ]);

        $countryId = $validated['country_id'] ?? null;

        if ($countryId) {
            $active = Country::query()
                ->whereKey($countryId)
                ->where('is_active', true)
                ->exists();

            if (! $active) {
                return back();
            }

            session(['workspace_country_id' => (int) $countryId]);
        } else {
            session()->forget('workspace_country_id');
        }

        return back();
    }
}
