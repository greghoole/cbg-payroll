<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use App\Models\Client;
use Illuminate\Http\Request;

class ChargeController extends Controller
{
    public function index(Request $request)
    {
        $query = Charge::with('client.coach');

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
        if ($request->filled('program')) {
            $query->where('program', 'like', '%' . $request->program . '%');
        }
        if ($request->filled('coach_id')) {
            $query->whereHas('client', function ($q) use ($request) {
                $q->where('coach_id', $request->coach_id);
            });
        }

        $charges = $query->orderBy('date', 'desc')->paginate(50)->withQueryString();
        
        $clients = Client::orderBy('name')->get();
        $coaches = \App\Models\Coach::orderBy('name')->get();
        
        return view('charges.index', compact('charges', 'clients', 'coaches'));
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get();
        return view('charges.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'date' => 'required|date',
            'net' => 'required|numeric|min:0',
            'amount_charged' => 'required|numeric|min:0',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'program' => 'nullable|string|max:255',
            'stripe_url' => 'nullable|url|max:255',
            'stripe_transaction_id' => 'nullable|string|max:255|unique:charges,stripe_transaction_id',
            'stripe_charge_id' => 'nullable|string|max:255|unique:charges,stripe_charge_id',
            'billing_information_included' => 'boolean',
            'country' => 'nullable|string|max:255',
        ]);

        $validated['billing_information_included'] = $request->has('billing_information_included');

        Charge::create($validated);

        return redirect()->route('charges.index')->with('success', 'Charge created successfully.');
    }

    public function edit(Charge $charge)
    {
        $clients = Client::orderBy('name')->get();
        return view('charges.edit', compact('charge', 'clients'));
    }

    public function update(Request $request, Charge $charge)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'date' => 'required|date',
            'net' => 'required|numeric|min:0',
            'amount_charged' => 'required|numeric|min:0',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'program' => 'nullable|string|max:255',
            'stripe_url' => 'nullable|url|max:255',
            'stripe_transaction_id' => 'nullable|string|max:255|unique:charges,stripe_transaction_id,' . $charge->id,
            'stripe_charge_id' => 'nullable|string|max:255|unique:charges,stripe_charge_id,' . $charge->id,
            'billing_information_included' => 'boolean',
            'country' => 'nullable|string|max:255',
        ]);

        $validated['billing_information_included'] = $request->has('billing_information_included');

        $charge->update($validated);

        return redirect()->route('charges.index')->with('success', 'Charge updated successfully.');
    }

    public function destroy(Charge $charge)
    {
        $charge->delete();
        return redirect()->route('charges.index')->with('success', 'Charge deleted successfully.');
    }

    /**
     * Update the commission percentage for a charge
     */
    public function updateCommission(Request $request, Charge $charge)
    {
        $validated = $request->validate([
            'commission_percentage' => 'nullable|integer|min:0|max:100',
        ]);

        $charge->update([
            'commission_percentage' => $validated['commission_percentage'] ?: null,
        ]);

        // Reload to get calculated payout
        $charge->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Commission percentage updated successfully.',
            'commission_percentage' => $charge->commission_percentage,
            'payout' => $charge->payout,
        ]);
    }
}
