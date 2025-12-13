@extends('layouts.app')

@section('title', $client->name)

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('clients.index') }}" class="text-indigo-600 hover:text-indigo-900">← Back to Clients</a>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ $client->name }}</h1>
        <p class="text-gray-600 mb-2"><strong>Email:</strong> {{ $client->email }}</p>
        @if($client->stripe_customer_id)
            <p class="text-gray-600 mb-2"><strong>Stripe ID:</strong> {{ $client->stripe_customer_id }}</p>
        @endif
        @if($client->country)
            <p class="text-gray-600 mb-2"><strong>Country:</strong> {{ $client->country }}</p>
        @endif
        <div class="mt-4">
            <a href="{{ route('clients.edit', $client) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-6">
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Assigned Coaches</h2>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Coach</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commission Rate</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($client->coaches as $coach)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $coach->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ number_format($coach->pivot->commission_rate, 2) }}%</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="px-4 py-3 text-center text-sm text-gray-500">No coaches assigned</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Recent Charges</h2>
            <div class="space-y-2">
                @forelse($client->charges->take(5) as $charge)
                <div class="flex justify-between text-sm">
                    <span>{{ $charge->date->format('M d, Y') }}</span>
                    <span class="font-medium">${{ number_format($charge->net, 2) }}</span>
                </div>
                @empty
                <p class="text-sm text-gray-500">No charges yet</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection


