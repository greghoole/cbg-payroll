<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelpController extends Controller
{
    /**
     * Display the API documentation page.
     */
    public function api()
    {
        return view('help.api');
    }
}
