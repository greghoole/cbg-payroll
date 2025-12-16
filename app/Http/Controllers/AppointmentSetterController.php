<?php

namespace App\Http\Controllers;

use App\Models\AppointmentSetter;
use App\Models\Client;
use Illuminate\Http\Request;

class AppointmentSetterController extends Controller
{
    public function index()
    {
        $appointmentSetters = AppointmentSetter::with('clients')->paginate(20);
        return view('appointment-setters.index', compact('appointmentSetters'));
    }

    public function create()
    {
        return view('appointment-setters.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:appointment_setters,email',
        ]);

        AppointmentSetter::create($validated);

        return redirect()->route('appointment-setters.index')->with('success', 'Appointment Setter created successfully.');
    }

    public function show(AppointmentSetter $appointmentSetter)
    {
        $appointmentSetter->load('clients');
        
        // Calculate commission for this appointment setter
        $commissionFromCharges = 0;
        foreach ($appointmentSetter->clients as $client) {
            $commissionRate = $client->pivot->commission_rate ?? 0;
            $clientCharges = $client->charges()->sum('net');
            $commissionFromCharges += ($clientCharges * $commissionRate) / 100;
        }
        
        return view('appointment-setters.show', compact('appointmentSetter', 'commissionFromCharges'));
    }

    public function edit(AppointmentSetter $appointmentSetter)
    {
        $clients = Client::all();
        $appointmentSetter->load('clients');
        return view('appointment-setters.edit', compact('appointmentSetter', 'clients'));
    }

    public function update(Request $request, AppointmentSetter $appointmentSetter)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:appointment_setters,email,' . $appointmentSetter->id,
            'clients' => 'array',
            'clients.*.commission_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $appointmentSetter->update([
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
            $appointmentSetter->clients()->sync($syncData);
        }

        return redirect()->route('appointment-setters.show', $appointmentSetter)->with('success', 'Appointment Setter updated successfully.');
    }

    public function destroy(AppointmentSetter $appointmentSetter)
    {
        $appointmentSetter->delete();
        return redirect()->route('appointment-setters.index')->with('success', 'Appointment Setter deleted successfully.');
    }
}
