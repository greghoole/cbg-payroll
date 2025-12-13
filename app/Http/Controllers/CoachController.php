<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use App\Models\Client;
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

    public function show(Coach $coach)
    {
        $coach->load('clients', 'oneOffCashIns');
        
        // Calculate commission for this coach
        $commissionFromCharges = 0;
        foreach ($coach->clients as $client) {
            $commissionRate = $client->pivot->commission_rate ?? 0;
            $clientCharges = $client->charges()->sum('net');
            $commissionFromCharges += ($clientCharges * $commissionRate) / 100;
        }
        
        $oneOffAmount = $coach->oneOffCashIns()->sum('amount');
        
        return view('coaches.show', compact('coach', 'commissionFromCharges', 'oneOffAmount'));
    }

    public function edit(Coach $coach)
    {
        $clients = Client::all();
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

        // Update client relationships with commission rates
        if ($request->has('clients')) {
            $syncData = [];
            foreach ($request->input('clients', []) as $clientId => $data) {
                if (isset($data['assigned']) && isset($data['commission_rate'])) {
                    $syncData[$clientId] = ['commission_rate' => $data['commission_rate'] ?? 0];
                }
            }
            $coach->clients()->sync($syncData);
        }

        return redirect()->route('coaches.show', $coach)->with('success', 'Coach updated successfully.');
    }

    public function destroy(Coach $coach)
    {
        $coach->delete();
        return redirect()->route('coaches.index')->with('success', 'Coach deleted successfully.');
    }
}
