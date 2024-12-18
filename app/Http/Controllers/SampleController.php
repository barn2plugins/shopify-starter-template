<?php

namespace Barn2App\Http\Controllers;

use Inertia\Inertia;

class SampleController extends Controller
{
    public function index()
    {
        return Inertia::render('Sample');
    }
}
