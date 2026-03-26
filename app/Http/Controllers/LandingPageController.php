<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class LandingPageController extends Controller
{
    /**
     * Show the landing page.
     */
    public function index(): View
    {
        return view('landing');
    }
}
