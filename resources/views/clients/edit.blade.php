@extends('layouts.app')

@section('title', 'Edit Client')

@section('content')
<div class="max-w-2xl mx-auto py-6 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Client</h1>

    <form action="{{ route('clients.update', $client) }}" method="POST" class="bg-white shadow rounded-lg p-6">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
            <input type="email" name="email" id="email" required value="{{ old('email', $client->email) }}"
                class="mt-1 block w-full px-4 py-2.5 rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
            <input type="text" name="name" id="name" required value="{{ old('name', $client->name) }}"
                class="mt-1 block w-full px-4 py-2.5 rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="stripe_customer_id" class="block text-sm font-medium text-gray-700">Stripe Customer ID</label>
            <input type="text" name="stripe_customer_id" id="stripe_customer_id" value="{{ old('stripe_customer_id', $client->stripe_customer_id) }}"
                class="mt-1 block w-full px-4 py-2.5 rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900">
        </div>

        <div class="mb-4">
            <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
            <input type="text" name="country" id="country" value="{{ old('country', $client->country) }}"
                class="mt-1 block w-full px-4 py-2.5 rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900">
        </div>

        <div class="flex justify-end">
            <a href="{{ route('clients.index') }}" class="mr-3 px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Update Client
            </button>
        </div>
    </form>
</div>
@endsection


