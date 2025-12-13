<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use App\Models\Refund;
use App\Models\Client;
use App\Models\Coach;
use App\Models\OneOffCashIn;
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

        // Calculate coach commissions
        $coaches = Coach::with('clients')->get()->map(function ($coach) {
            $commissionFromCharges = 0;
            foreach ($coach->clients as $client) {
                $commissionRate = $client->pivot->commission_rate ?? 0;
                $clientCharges = $client->charges()->sum('net');
                $commissionFromCharges += ($clientCharges * $commissionRate) / 100;
            }
            
            $oneOffAmount = $coach->oneOffCashIns()->sum('amount');
            
            return [
                'coach' => $coach,
                'commission' => $commissionFromCharges,
                'one_off' => $oneOffAmount,
                'total' => $commissionFromCharges + $oneOffAmount,
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
