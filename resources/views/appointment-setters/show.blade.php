@extends('layouts.app')

@section('title', $appointmentSetter->name)

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('appointment-setters.index') }}" class="text-indigo-600 hover:text-indigo-900">← Back to Appointment Setters</a>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ $appointmentSetter->name }}</h1>
        <p class="text-gray-600">{{ $appointmentSetter->email ?? 'No email' }}</p>
        <div class="mt-4">
            <a href="{{ route('appointment-setters.edit', $appointmentSetter) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-6">
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-sm font-medium text-gray-500">Commission from Charges</h3>
            <p class="text-2xl font-bold text-gray-900 mt-2">${{ number_format($commissionFromCharges, 2) }}</p>
        </div>
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-sm font-medium text-gray-500">One-Off Cash Ins</h3>
            <p class="text-2xl font-bold text-gray-900 mt-2">${{ number_format($oneOffAmount, 2) }}</p>
        </div>
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-sm font-medium text-gray-500">Total</h3>
            <p class="text-2xl font-bold text-indigo-600 mt-2">${{ number_format($commissionFromCharges + $oneOffAmount, 2) }}</p>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Assigned Clients</h2>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commission Rate</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($appointmentSetter->clients as $client)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $client->name }} ({{ $client->email }})</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($client->pivot->commission_rate, 2) }}%</td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500">No clients assigned</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

