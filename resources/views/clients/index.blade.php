@extends('layouts.app')

@section('title', 'Clients')

@section('content')
<div class="w-full py-6 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Clients</h1>
        <a href="{{ route('clients.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
            Add Client
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Search/Filter Form -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <form method="GET" action="{{ route('clients.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" id="name" value="{{ request('name') }}" placeholder="Search name..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="text" name="email" id="email" value="{{ request('email') }}" placeholder="Search email..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
            </div>

            <!-- Coach -->
            <div x-data="{ 
                open: false, 
                search: '', 
                selectedCoach: {{ request('coach_id') ?: 'null' }},
                selectedCoachName: @js(request('coach_id') ? ($coaches->firstWhere('id', request('coach_id'))?->name ?? 'All Coaches') : 'All Coaches'),
                coaches: @js($coaches->map(fn($c) => ['id' => $c->id, 'name' => $c->name]))
            }" class="relative">
                <label class="block text-sm font-medium text-gray-700 mb-1">Coach</label>
                <input type="hidden" name="coach_id" :value="selectedCoach || ''">
                <button type="button" @click="open = !open" @click.away="open = false" class="w-full px-4 py-2.5 text-left bg-gray-50 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 flex items-center justify-between text-sm">
                    <span x-text="selectedCoachName" :class="selectedCoach ? 'text-gray-900' : 'text-gray-500'"></span>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" x-transition @click.stop class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-auto">
                    <div class="p-2 sticky top-0 bg-white border-b" @click.stop>
                        <input type="text" x-model="search" @click.stop placeholder="Search coaches..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    </div>
                    <ul class="py-1">
                        <li>
                            <button type="button" @click="selectedCoach = null; selectedCoachName = 'All Coaches'; open = false" class="w-full text-left px-4 py-2 text-sm hover:bg-indigo-50 focus:bg-indigo-50 focus:outline-none" :class="!selectedCoach ? 'bg-indigo-100' : ''">
                                <div class="font-medium text-gray-900">All Coaches</div>
                            </button>
                        </li>
                        <template x-for="coach in coaches.filter(c => !search || c.name.toLowerCase().includes(search.toLowerCase()))" :key="coach.id">
                            <li>
                                <button type="button" @click="selectedCoach = coach.id; selectedCoachName = coach.name; open = false" class="w-full text-left px-4 py-2 text-sm hover:bg-indigo-50 focus:bg-indigo-50 focus:outline-none" :class="selectedCoach == coach.id ? 'bg-indigo-100' : ''">
                                    <div class="font-medium text-gray-900" x-text="coach.name"></div>
                                </button>
                            </li>
                        </template>
                        <li x-show="coaches.filter(c => !search || c.name.toLowerCase().includes(search.toLowerCase())).length === 0" class="px-4 py-2 text-sm text-gray-500">No coaches found</li>
                    </ul>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 font-medium text-sm">
                    Filter
                </button>
                <a href="{{ route('clients.index') }}" class="px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:ring-4 focus:ring-gray-300 font-medium text-sm">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Coach</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($clients as $client)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $client->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $client->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $client->coach?->name ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('clients.show', $client) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">View</a>
                        <a href="{{ route('clients.edit', $client) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No clients found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $clients->links() }}
    </div>
</div>
@endsection



