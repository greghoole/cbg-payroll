<?php

namespace App\Http\Controllers;

use App\Models\Refund;
use App\Models\Client;
use App\Models\Charge;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    public function index(Request $request)
    {
        $query = Refund::with(['client', 'charge']);

        // Search filters
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        if ($request->filled('reason')) {
            $query->where('reason', 'like', '%' . $request->reason . '%');
        }

        $refunds = $query->orderBy('date', 'desc')->paginate(50)->withQueryString();
        
        $clients = Client::orderBy('name')->get();
        
        return view('refunds.index', compact('refunds', 'clients'));
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get();
        $charges = Charge::with('client')->orderBy('date', 'desc')->get();
        return view('refunds.create', compact('clients', 'charges'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'charge_id' => 'nullable|exists:charges,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'initial_amount_charged' => 'nullable|numeric|min:0',
            'stripe_refund_id' => 'nullable|string|max:255',
            'stripe_transaction_id' => 'required|string|max:255|unique:refunds,stripe_transaction_id',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        Refund::create($validated);

        return redirect()->route('refunds.index')->with('success', 'Refund created successfully.');
    }

    public function edit(Refund $refund)
    {
        $clients = Client::orderBy('name')->get();
        $charges = Charge::with('client')->orderBy('date', 'desc')->get();
        return view('refunds.edit', compact('refund', 'clients', 'charges'));
    }

    public function update(Request $request, Refund $refund)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'charge_id' => 'nullable|exists:charges,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'initial_amount_charged' => 'nullable|numeric|min:0',
            'stripe_refund_id' => 'nullable|string|max:255',
            'stripe_transaction_id' => 'required|string|max:255|unique:refunds,stripe_transaction_id,' . $refund->id,
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $refund->update($validated);

        return redirect()->route('refunds.index')->with('success', 'Refund updated successfully.');
    }

    public function destroy(Refund $refund)
    {
        $refund->delete();
        return redirect()->route('refunds.index')->with('success', 'Refund deleted successfully.');
    }
}
