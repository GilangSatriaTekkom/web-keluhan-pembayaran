<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PackagePageController extends Controller
{
    public function show()
    {
        return view('LandingPages.package');
    }
}
