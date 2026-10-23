<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClientUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isClient()) {
            if ($user?->isEmployer()) {
                return redirect()->route('employer.home');
            }

            if ($user?->isOfficeUser()) {
                return redirect()->route('dashboard');
            }

            return redirect()->route('client-portal.login');
        }

        return $next($request);
    }
}
