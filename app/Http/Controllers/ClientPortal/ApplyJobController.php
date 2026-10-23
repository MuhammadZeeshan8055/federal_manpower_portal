<?php

namespace App\Http\Controllers\ClientPortal;

use App\Http\Controllers\Controller;
use App\Support\JobApplyPrefill;
use Illuminate\Http\RedirectResponse;

class ApplyJobController extends Controller
{
    public function __invoke(int $job): RedirectResponse
    {
        $post = JobApplyPrefill::approvedJob($job);

        if (! $post) {
            return redirect()
                ->route('website.jobs')
                ->with('error', 'That job is not available.');
        }

        session(['apply_job_id' => $post->id]);

        $user = auth()->user();

        if ($user && $user->isClient()) {
            return redirect()->route('client-portal.application');
        }

        return redirect()->route('client-portal.register');
    }
}
