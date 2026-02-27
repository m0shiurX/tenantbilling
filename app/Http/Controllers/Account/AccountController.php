<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    /**
     * Display the My Account dashboard.
     */
    public function index(): Response
    {
        return Inertia::render('account/Index');
    }
}
