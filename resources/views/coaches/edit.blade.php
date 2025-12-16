@extends('layouts.app')

@section('title', 'Edit Coach')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Coach: {{ $coach->name }}</h1>

    <form action="{{ route('coaches.update', $coach) }}" method="POST" class="bg-white shadow rounded-lg p-6">
        @csrf
        @method('PUT')
        
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

        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Assign Clients</h3>
            <p class="text-sm text-gray-500 mb-4">Note: Commission rates are set per charge, not per client-coach relationship.</p>
            <div class="space-y-3">
                @foreach(\App\Models\Client::all() as $client)
                <div class="flex items-center border-b pb-2">
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


