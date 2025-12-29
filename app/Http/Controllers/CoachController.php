<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use App\Models\Client;
use App\Models\Charge;
use Illuminate\Http\Request;

class CoachController extends Controller
{
    public function index()
    {
        $coaches = Coach::with('clients')->paginate(20);
        return view('coaches.index', compact('coaches'));
    }

    public function create()
    {
        return view('coaches.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:coaches,email',
        ]);

        Coach::create($validated);

        return redirect()->route('coaches.index')->with('success', 'Coach created successfully.');
    }

    public function show(Request $request, Coach $coach)
    {
        $coach->load('clients.charges');
        
        // Calculate commission for this coach based on charge commission_percentage
        // Include charges from clients AND charges with direct coach_id
        $commissionFromCharges = 0;
        $commissionsByMonth = [];
        $processedChargeIds = []; // Track processed charges to avoid duplicates
        
        // Process charges through clients
        foreach ($coach->clients as $client) {
            foreach ($client->charges as $charge) {
                if ($charge->commission_percentage && $charge->payout && !in_array($charge->id, $processedChargeIds)) {
                    $commissionFromCharges += $charge->payout;
                    $processedChargeIds[] = $charge->id;
                    
                    // Group by month (YYYY-MM format)
                    $monthKey = $charge->date->format('Y-m');
                    if (!isset($commissionsByMonth[$monthKey])) {
                        $commissionsByMonth[$monthKey] = [
                            'month' => $charge->date->format('F Y'),
                            'year' => $charge->date->format('Y'),
                            'month_number' => $charge->date->format('m'),
                            'total' => 0,
                            'charges' => [],
                        ];
                    }
                    $commissionsByMonth[$monthKey]['total'] += $charge->payout;
                    
                    // Add charge details to the month
                    $commissionsByMonth[$monthKey]['charges'][] = [
                        'date' => $charge->date->format('M d, Y'),
                        'client_name' => $client->name,
                        'client_email' => $client->email,
                        'amount' => $charge->amount_charged ?? $charge->net,
                        'commission_percentage' => $charge->commission_percentage,
                        'payout' => $charge->payout,
                    ];
                }
            }
        }
        
        // Process charges with direct coach_id
        $directCharges = Charge::where('coach_id', $coach->id)
            ->whereNotNull('commission_percentage')
            ->whereNotNull('payout')
            ->where('payout', '>', 0)
            ->with('client')
            ->get();
        
        foreach ($directCharges as $charge) {
            if (!in_array($charge->id, $processedChargeIds)) {
                $commissionFromCharges += $charge->payout;
                $processedChargeIds[] = $charge->id;
                
                // Group by month (YYYY-MM format)
                $monthKey = $charge->date->format('Y-m');
                if (!isset($commissionsByMonth[$monthKey])) {
                    $commissionsByMonth[$monthKey] = [
                        'month' => $charge->date->format('F Y'),
                        'year' => $charge->date->format('Y'),
                        'month_number' => $charge->date->format('m'),
                        'total' => 0,
                        'charges' => [],
                    ];
                }
                $commissionsByMonth[$monthKey]['total'] += $charge->payout;
                
                // Add charge details to the month
                $commissionsByMonth[$monthKey]['charges'][] = [
                    'date' => $charge->date->format('M d, Y'),
                    'client_name' => $charge->client ? $charge->client->name : 'No Client',
                    'client_email' => $charge->client ? $charge->client->email : '—',
                    'amount' => $charge->amount_charged ?? $charge->net,
                    'commission_percentage' => $charge->commission_percentage,
                    'payout' => $charge->payout,
                ];
            }
        }
        
        // Sort charges within each month by date (most recent first)
        foreach ($commissionsByMonth as &$monthData) {
            usort($monthData['charges'], function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
        }
        
        // Sort by most recent month first (descending)
        krsort($commissionsByMonth);
        
        // Sort clients alphabetically by name (no filtering - done client-side)
        $clients = $coach->clients->sortBy('name')->values();
        
        return view('coaches.show', compact('coach', 'commissionFromCharges', 'commissionsByMonth', 'clients'));
    }

    public function edit(Coach $coach)
    {
        $clients = Client::orderBy('name')->get();
        $coach->load('clients');
        return view('coaches.edit', compact('coach', 'clients'));
    }

    public function update(Request $request, Coach $coach)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:coaches,email,' . $coach->id,
            'clients' => 'array',
            'clients.*.commission_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $coach->update([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
        ]);

        // Update client relationships (1:1 relationship)
        if ($request->has('clients')) {
            // First, remove this coach from all clients
            Client::where('coach_id', $coach->id)->update(['coach_id' => null]);
            
            // Then assign this coach to selected clients
            foreach ($request->input('clients', []) as $clientId => $data) {
                if (isset($data['assigned']) && $data['assigned']) {
                    Client::where('id', $clientId)->update(['coach_id' => $coach->id]);
                }
            }
        }

        return redirect()->route('coaches.show', $coach)->with('success', 'Coach updated successfully.');
    }

    public function destroy(Coach $coach)
    {
        $coach->delete();
        return redirect()->route('coaches.index')->with('success', 'Coach deleted successfully.');
    }
}
