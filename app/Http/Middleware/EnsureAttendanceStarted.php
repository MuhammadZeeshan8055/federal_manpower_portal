<?php

namespace App\Http\Middleware;

use App\Support\AttendancePunch;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAttendanceStarted
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Admin and WFH exempt skip the gate
        if ($user->isAdmin() || $user->bypassesAttendance()) {
            return $next($request);
        }

        // Already checked in today → allow
        if (AttendancePunch::hasStartedToday($user)) {
            return $next($request);
        }

        return redirect()->route('attendance.start');
    }
}
