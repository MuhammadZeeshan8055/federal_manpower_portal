<?php

namespace App\Http\Controllers;

use App\Models\Client;

class ClientStatusController extends Controller
{
    public function show(string $token)
    {
        $client = Client::query()
            ->with(['country:id,name', 'trade:id,name', 'processStatus:id,name'])
            ->where('public_token', $token)
            ->first();

        if (! $client) {
            abort(404);
        }

        return view('public.client-status', [
            'client' => $client,
        ]);
    }
}
