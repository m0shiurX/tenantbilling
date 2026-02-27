<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    /**
     * Display the settings dashboard.
     */
    public function index(): Response
    {
        return Inertia::render('settings/Index');
    }
}
