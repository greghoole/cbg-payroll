<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::with('coaches')->paginate(20);
        return view('clients.index', compact('clients'));
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
        $client->load('coaches', 'charges', 'refunds');
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        $client->load('coaches');
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
}
