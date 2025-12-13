<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $apiToken = Setting::get('api_token');
        return view('settings.index', compact('apiToken'));
    }

    /**
     * Regenerate the API token.
     */
    public function regenerateToken(Request $request)
    {
        $newToken = Str::random(64);
        Setting::set('api_token', $newToken);

        return redirect()->route('settings.index')->with('success', 'API token regenerated successfully. Make sure to update your integrations with the new token.');
    }
}
