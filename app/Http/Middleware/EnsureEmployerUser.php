<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployerUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isEmployer()) {
            if ($user?->isClient()) {
                return redirect()->route('client-portal.home');
            }

            if ($user?->isOfficeUser()) {
                return redirect()->route('dashboard');
            }

            return redirect()->route('employer.login');
        }

        return $next($request);
    }
}
