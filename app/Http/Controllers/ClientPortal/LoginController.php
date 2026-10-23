<?php

namespace App\Http\Controllers\ClientPortal;

use App\Http\Controllers\Controller;
use App\Support\JobApplyPrefill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(Request $request): View
    {
        $jobId = (int) $request->query('job', 0);

        if ($jobId > 0 && JobApplyPrefill::approvedJob($jobId)) {
            session(['apply_job_id' => $jobId]);
        }

        return view('client-portal.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $request->session()->regenerate();

        if (! $request->user()->isClient()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Use the office login for staff accounts.',
            ]);
        }

        if ($request->session()->has('apply_job_id')) {
            return redirect()->route('client-portal.application');
        }

        return redirect()->intended(route('client-portal.home'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('client-portal.login');
    }
}
