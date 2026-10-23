<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $profile = auth()->user()->employerProfile;

        return view('employer.home', [
            'profile' => $profile,
        ]);
    }
}
