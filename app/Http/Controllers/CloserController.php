<?php

namespace App\Http\Controllers;

use App\Models\Closer;
use App\Models\Client;
use Illuminate\Http\Request;

class CloserController extends Controller
{
    public function index()
    {
        $closers = Closer::with('clients')->paginate(20);
        return view('closers.index', compact('closers'));
    }

    public function create()
    {
        return view('closers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:closers,email',
        ]);

        Closer::create($validated);

        return redirect()->route('closers.index')->with('success', 'Closer created successfully.');
    }

    public function show(Closer $closer)
    {
        $closer->load('clients');
        
        // Calculate commission for this closer
        $commissionFromCharges = 0;
        foreach ($closer->clients as $client) {
            $commissionRate = $client->pivot->commission_rate ?? 0;
            $clientCharges = $client->charges()->sum('net');
            $commissionFromCharges += ($clientCharges * $commissionRate) / 100;
        }
        
        return view('closers.show', compact('closer', 'commissionFromCharges'));
    }

    public function edit(Closer $closer)
    {
        $clients = Client::all();
        $closer->load('clients');
        return view('closers.edit', compact('closer', 'clients'));
    }

    public function update(Request $request, Closer $closer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:closers,email,' . $closer->id,
            'clients' => 'array',
            'clients.*.commission_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $closer->update([
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
            $closer->clients()->sync($syncData);
        }

        return redirect()->route('closers.show', $closer)->with('success', 'Closer updated successfully.');
    }

    public function destroy(Closer $closer)
    {
        $closer->delete();
        return redirect()->route('closers.index')->with('success', 'Closer deleted successfully.');
    }
}
