<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use App\Models\Refund;
use App\Models\Client;
use App\Models\Coach;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalCharges = Charge::sum('net');
        $totalRefunds = Refund::sum('amount');
        $totalClients = Client::count();
        $totalCoaches = Coach::count();
        
        $recentCharges = Charge::with('client')->latest('date')->take(10)->get();
        $recentRefunds = Refund::with('client')->latest('date')->take(10)->get();

        // Calculate coach commissions based on charge payouts
        $coaches = Coach::with('clients.charges')->get()->map(function ($coach) {
            $commissionFromCharges = 0;
            foreach ($coach->clients as $client) {
                foreach ($client->charges as $charge) {
                    if ($charge->payout) {
                        $commissionFromCharges += $charge->payout;
                    }
                }
            }
            
            return [
                'coach' => $coach,
                'commission' => $commissionFromCharges,
                'total' => $commissionFromCharges,
            ];
        })->sortByDesc('total')->take(10);

        return view('dashboard', compact(
            'totalCharges',
            'totalRefunds',
            'totalClients',
            'totalCoaches',
            'recentCharges',
            'recentRefunds',
            'coaches'
        ));
    }
}
