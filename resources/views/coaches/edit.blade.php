@extends('layouts.app')

@section('title', 'Edit Coach')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Coach: {{ $coach->name }}</h1>

    <form action="{{ route('coaches.update', $coach) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
                <input type="text" name="name" id="name" required value="{{ old('name', $coach->name) }}"
                    class="mt-1 block w-full px-4 py-2.5 rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $coach->email) }}"
                    class="mt-1 block w-full px-4 py-2.5 rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6 mb-6" x-data="{ search: '' }">
            <h3 class="text-lg font-medium text-gray-900 mb-2">Assign Clients</h3>
            <p class="text-sm text-amber-600 bg-amber-50 border border-amber-200 rounded-md p-3 mb-4">
                <strong>Note:</strong> Assigning a client to a coach does not update existing charges. Commission calculations are based on the current client-coach assignment and will only affect future commission views. Historical charge records remain unchanged.
            </p>
            <div class="flex gap-2 mb-4">
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
            <div class="border-b pb-2 mb-2">
                <div class="flex items-center">
                    <div class="w-6"></div>
                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Client</div>
                </div>
            </div>
            <div class="space-y-3">
                @foreach($clients as $client)
                <div 
                    class="flex items-center border-b pb-2 client-item" 
                    x-show="!search || '{{ strtolower($client->name . ' ' . $client->email) }}'.includes(search.toLowerCase())"
                >
                    <label class="flex items-center">
                        <input type="checkbox" name="clients[{{ $client->id }}][assigned]" value="1"
                            {{ $client->coach_id == $coach->id ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">{{ $client->name }} ({{ $client->email }})</span>
                    </label>
                </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end">
            <a href="{{ route('coaches.index') }}" class="mr-3 px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Update Coach
            </button>
        </div>
    </form>
</div>
@endsection


