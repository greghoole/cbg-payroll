<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class HelpController extends Controller
{
    /**
     * Display the API documentation page.
     */
    public function api()
    {
        // Get API token from database or fallback to env
        $apiToken = Setting::get('api_token') ?? env('API_TOKEN', 'No token configured');
        
        return view('help.api', compact('apiToken'));
    }
}
