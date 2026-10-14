<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class RegisteredUserController extends Controller
{
    public function create(): RedirectResponse
    {
        return redirect()->route('client-portal.register');
    }

    public function store(): RedirectResponse
    {
        return redirect()->route('client-portal.register');
    }
}
