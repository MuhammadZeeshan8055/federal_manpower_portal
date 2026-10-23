<?php

namespace App\Http\Controllers\ClientPortal;

use App\Http\Controllers\Controller;
use App\Support\ClientPublicLink;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();
        $client = $user->clientProfile()
            ->with([
                'processStatus:id,name',
                'country:id,name',
                'trade:id,name',
                'jobPost.employer',
            ])
            ->first();

        return view('client-portal.home', [
            'user' => $user,
            'client' => $client,
            'statusUrl' => $client ? ClientPublicLink::statusUrl($client) : null,
        ]);
    }
}
