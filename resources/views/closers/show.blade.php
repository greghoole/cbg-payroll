@extends('layouts.app')

@section('title', $closer->name)

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('closers.index') }}" class="text-indigo-600 hover:text-indigo-900">← Back to Closers</a>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ $closer->name }}</h1>
        <p class="text-gray-600">{{ $closer->email ?? 'No email' }}</p>
        <div class="mt-4">
            <a href="{{ route('closers.edit', $closer) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-6">
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-sm font-medium text-gray-500">Commission from Charges</h3>
            <p class="text-2xl font-bold text-gray-900 mt-2">${{ number_format($commissionFromCharges, 2) }}</p>
        </div>
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-sm font-medium text-gray-500">Total Commission</h3>
            <p class="text-2xl font-bold text-indigo-600 mt-2">${{ number_format($commissionFromCharges, 2) }}</p>
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
                @forelse($closer->clients as $client)
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

