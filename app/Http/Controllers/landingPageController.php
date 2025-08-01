<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class landingPageController extends Controller
{
    public function show()
    {
        return view('landingpage');
    }
}
