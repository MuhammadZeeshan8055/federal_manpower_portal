<?php

namespace App\Http\Controllers\ClientPortal;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\JobApplyPrefill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(Request $request): View
    {
        $this->rememberJobFromRequest($request);

        return view('client-portal.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'client',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        if ($request->session()->has('apply_job_id')) {
            return redirect()->route('client-portal.application');
        }

        return redirect()->route('client-portal.home');
    }

    private function rememberJobFromRequest(Request $request): void
    {
        $jobId = (int) $request->query('job', 0);

        if ($jobId > 0 && JobApplyPrefill::approvedJob($jobId)) {
            session(['apply_job_id' => $jobId]);
        }
    }
}
