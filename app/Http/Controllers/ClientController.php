<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::with('coach');

        // Search filters
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }
        if ($request->filled('coach_id')) {
            $query->where('coach_id', $request->coach_id);
        }

        $clients = $query->orderBy('name')->paginate(20)->withQueryString();
        
        $coaches = \App\Models\Coach::orderBy('name')->get();
        
        return view('clients.index', compact('clients', 'coaches'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:clients,email',
            'name' => 'required|string|max:255',
            'stripe_customer_id' => 'nullable|string',
            'country' => 'nullable|string|max:255',
        ]);

        Client::create($validated);

        return redirect()->route('clients.index')->with('success', 'Client created successfully.');
    }

    public function show(Client $client)
    {
        $client->load('coach', 'charges', 'refunds');
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        $client->load('coach');
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:clients,email,' . $client->id,
            'name' => 'required|string|max:255',
            'stripe_customer_id' => 'nullable|string',
            'country' => 'nullable|string|max:255',
        ]);

        $client->update($validated);

        return redirect()->route('clients.show', $client)->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
    }

    /**
     * Update the coach assignment for a client
     */
    public function updateCoach(Request $request, Client $client)
    {
        $validated = $request->validate([
            'coach_id' => 'nullable|exists:coaches,id',
        ]);

        $client->update([
            'coach_id' => $validated['coach_id'] ?? null,
        ]);

        $client->load('coach');

        return response()->json([
            'success' => true,
            'message' => 'Coach assignment updated successfully.',
            'coach' => $client->coach,
        ]);
    }
}
