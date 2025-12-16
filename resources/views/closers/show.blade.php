@extends('layouts.app')

@section('title', $closer->name)

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('closers.index') }}" class="text-indigo-600 hover:text-indigo-900">← Back to Closers</a>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $closer->name }}</h1>
                <p class="text-gray-600">{{ $closer->email ?? 'No email' }}</p>
                <div class="mt-4">
                    <a href="{{ route('closers.edit', $closer) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                </div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 lg:min-w-[200px]">
                <h3 class="text-sm font-medium text-gray-500">Total Commission</h3>
                <p class="text-2xl font-bold text-indigo-600 mt-2">${{ number_format($commissionFromCharges, 2) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-6" x-data="{ open: true }">
        <button 
            @click="open = !open" 
            class="flex items-center justify-between w-full text-left focus:outline-none"
        >
            <h2 class="text-lg font-medium text-gray-900">Commissions by Month</h2>
            <svg 
                class="w-5 h-5 text-gray-500 transition-transform duration-200" 
                :class="{ 'rotate-180': open }"
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div 
            x-show="open" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform -translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform -translate-y-2"
            class="mt-4"
        >
            @if(count($commissionsByMonth) > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Commission</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($commissionsByMonth as $monthData)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $monthData['month'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">${{ number_format($monthData['total'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-sm text-gray-500">No commissions recorded yet.</p>
            @endif
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6" x-data="{ 
        open: true,
        search: '',
        allClients: @js($clients->map(fn($client) => ['id' => $client->id, 'name' => $client->name, 'email' => $client->email, 'commission_rate' => $client->pivot->commission_rate ?? 0])),
        get filteredClients() {
            if (!this.search.trim()) {
                return this.allClients;
            }
            const searchLower = this.search.toLowerCase();
            return this.allClients.filter(client => 
                client.name.toLowerCase().includes(searchLower) || 
                client.email.toLowerCase().includes(searchLower)
            );
        }
    }">
        <button 
            @click="open = !open" 
            class="flex items-center justify-between w-full text-left focus:outline-none mb-4"
        >
            <h2 class="text-lg font-medium text-gray-900">Assigned Clients</h2>
            <svg 
                class="w-5 h-5 text-gray-500 transition-transform duration-200" 
                :class="{ 'rotate-180': open }"
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div 
            x-show="open" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform -translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform -translate-y-2"
        >
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-4">
                <div class="flex gap-2">
                    <input 
                        type="text" 
                        x-model="search"
                        placeholder="Search clients..." 
                        class="block w-full sm:w-64 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    >
                    <button 
                        @click="search = ''"
                        x-show="search"
                        type="button"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Clear
                    </button>
                </div>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commission Rate</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="client in filteredClients" :key="client.id">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="client.name + ' (' + client.email + ')'"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="parseFloat(client.commission_rate).toFixed(2) + '%'"></td>
                        </tr>
                    </template>
                    <tr x-show="filteredClients.length === 0">
                        <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500">
                            <span x-show="search.trim()" x-text="'No clients found matching \"' + search + '\"'"></span>
                            <span x-show="!search.trim() && allClients.length === 0">No clients assigned</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

