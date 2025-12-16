@extends('layouts.app')

@section('title', 'Refunds')

@section('content')
<div class="w-full py-6 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">All Refunds</h1>
        <a href="{{ route('refunds.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
            Add Refund
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Search/Filter Form -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <form method="GET" action="{{ route('refunds.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Date From -->
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <input datepicker datepicker-format="yyyy-mm-dd" type="text" name="date_from" id="date_from" value="{{ request('date_from') }}" placeholder="Select date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 p-2.5">
                </div>
            </div>

            <!-- Date To -->
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <input datepicker datepicker-format="yyyy-mm-dd" type="text" name="date_to" id="date_to" value="{{ request('date_to') }}" placeholder="Select date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 p-2.5">
                </div>
            </div>

            <!-- Client -->
            <div x-data="{ 
                open: false, 
                search: '', 
                selectedClient: {{ request('client_id') ?: 'null' }},
                selectedClientName: @js(request('client_id') ? ($clients->firstWhere('id', request('client_id'))?->name ?? 'All Clients') : 'All Clients'),
                clients: @js($clients->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'email' => $c->email]))
            }" class="relative">
                <label class="block text-sm font-medium text-gray-700 mb-1">Client</label>
                <input type="hidden" name="client_id" :value="selectedClient || ''">
                <button type="button" @click="open = !open" @click.away="open = false" class="w-full px-4 py-2.5 text-left bg-gray-50 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 flex items-center justify-between text-sm">
                    <span x-text="selectedClientName" :class="selectedClient ? 'text-gray-900' : 'text-gray-500'"></span>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" x-transition @click.stop class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-auto">
                    <div class="p-2 sticky top-0 bg-white border-b" @click.stop>
                        <input type="text" x-model="search" @click.stop placeholder="Search clients..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    </div>
                    <ul class="py-1">
                        <li>
                            <button type="button" @click="selectedClient = null; selectedClientName = 'All Clients'; open = false" class="w-full text-left px-4 py-2 text-sm hover:bg-indigo-50 focus:bg-indigo-50 focus:outline-none" :class="!selectedClient ? 'bg-indigo-100' : ''">
                                <div class="font-medium text-gray-900">All Clients</div>
                            </button>
                        </li>
                        <template x-for="client in clients.filter(c => !search || c.name.toLowerCase().includes(search.toLowerCase()) || c.email.toLowerCase().includes(search.toLowerCase()))" :key="client.id">
                            <li>
                                <button type="button" @click="selectedClient = client.id; selectedClientName = client.name; open = false" class="w-full text-left px-4 py-2 text-sm hover:bg-indigo-50 focus:bg-indigo-50 focus:outline-none" :class="selectedClient == client.id ? 'bg-indigo-100' : ''">
                                    <div class="font-medium text-gray-900" x-text="client.name"></div>
                                    <div class="text-xs text-gray-500" x-text="client.email"></div>
                                </button>
                            </li>
                        </template>
                        <li x-show="clients.filter(c => !search || c.name.toLowerCase().includes(search.toLowerCase()) || c.email.toLowerCase().includes(search.toLowerCase())).length === 0" class="px-4 py-2 text-sm text-gray-500">No clients found</li>
                    </ul>
                </div>
            </div>

            <!-- Reason -->
            <div>
                <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                <input type="text" name="reason" id="reason" value="{{ request('reason') }}" placeholder="Search reason..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
            </div>

            <!-- Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 font-medium text-sm">
                    Filter
                </button>
                <a href="{{ route('refunds.index') }}" class="px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:ring-4 focus:ring-gray-300 font-medium text-sm">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="max-width: 200px;">Reason</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Charge</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($refunds as $refund)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $refund->date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <a href="{{ route('clients.show', $refund->client) }}" class="text-indigo-600 hover:text-indigo-900">
                                {{ $refund->client->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500" style="max-width: 200px;">
                            <div class="truncate" title="{{ $refund->reason ?? '—' }}">{{ $refund->reason ?? '—' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600 text-right">${{ number_format($refund->amount, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($refund->charge)
                                <span class="text-gray-500">Charge #{{ $refund->charge->id }}</span>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ \Illuminate\Support\Str::limit($refund->notes ?? '—', 50) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('refunds.edit', $refund) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                            <form action="{{ route('refunds.destroy', $refund) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this refund?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No refunds found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $refunds->links() }}
    </div>
</div>
@endsection
