<?php

namespace App\Http\Controllers;

use App\Models\OneOffCashIn;
use App\Models\Coach;
use Illuminate\Http\Request;

class OneOffCashInController extends Controller
{
    public function index()
    {
        $oneOffCashIns = OneOffCashIn::with('coach')->latest('date')->paginate(20);
        return view('one-off-cash-ins.index', compact('oneOffCashIns'));
    }

    public function create()
    {
        $coaches = Coach::all();
        return view('one-off-cash-ins.create', compact('coaches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'coach_id' => 'required|exists:coaches,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        OneOffCashIn::create($validated);

        return redirect()->route('one-off-cash-ins.index')->with('success', 'One-off cash in created successfully.');
    }

    public function edit(OneOffCashIn $oneOffCashIn)
    {
        $coaches = Coach::all();
        return view('one-off-cash-ins.edit', compact('oneOffCashIn', 'coaches'));
    }

    public function update(Request $request, OneOffCashIn $oneOffCashIn)
    {
        $validated = $request->validate([
            'coach_id' => 'required|exists:coaches,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $oneOffCashIn->update($validated);

        return redirect()->route('one-off-cash-ins.index')->with('success', 'One-off cash in updated successfully.');
    }

    public function destroy(OneOffCashIn $oneOffCashIn)
    {
        $oneOffCashIn->delete();
        return redirect()->route('one-off-cash-ins.index')->with('success', 'One-off cash in deleted successfully.');
    }
}
