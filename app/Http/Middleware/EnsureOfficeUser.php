<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOfficeUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isOfficeUser()) {
            if ($user?->isClient()) {
                return redirect()->route('client-portal.home');
            }

            if ($user?->isEmployer()) {
                return redirect()->route('employer.home');
            }

            return redirect()->route('login');
        }

        return $next($request);
    }
}
