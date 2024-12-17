<?php

namespace Barn2App\Http\Controllers;

use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Welcome');
    }

    public function products()
    {
        return Inertia::render('Products');
    }

    public function sample()
    {
        return Inertia::render('Sample');
    }
}
