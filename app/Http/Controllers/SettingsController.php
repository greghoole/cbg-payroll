<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Charge;
use App\Models\Client;
use App\Models\Refund;
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

    /**
     * Reset all data by deleting all charges, refunds, and clients.
     */
    public function resetData(Request $request)
    {
        // Delete all refunds first (due to foreign key constraints)
        Refund::query()->delete();
        
        // Delete all charges
        Charge::query()->delete();
        
        // Delete all clients (this will also cascade delete pivot table relationships)
        Client::query()->delete();

        return redirect()->route('settings.index')->with('success', 'All charges, refunds, and clients have been deleted successfully.');
    }
}
