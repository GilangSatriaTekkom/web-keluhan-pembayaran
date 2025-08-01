<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TicketingController extends Controller
{
    public function show()
    {
        return view('pages.keluhan');
    }
}
