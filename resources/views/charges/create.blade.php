@extends('layouts.app')

@section('title', 'Create Charge')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('charges.index') }}" class="text-indigo-600 hover:text-indigo-900">← Back to Charges</a>
    </div>

    <h1 class="text-2xl font-bold text-gray-900 mb-6">Create Charge</h1>

    <form action="{{ route('charges.store') }}" method="POST" class="bg-white shadow rounded-lg p-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Client -->
            <div x-data="{ 
                open: false, 
                search: '', 
                selectedClient: {{ old('client_id') ?: 'null' }},
                selectedClientName: @js(old('client_id') ? ($clients->firstWhere('id', old('client_id'))?->name ?? 'Select a client') : 'Select a client'),
                clients: @js($clients->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'email' => $c->email]))
            }" class="relative">
                <label class="block text-sm font-medium text-gray-700 mb-2">Client</label>
                <input type="hidden" name="client_id" :value="selectedClient">
                <button type="button" @click="open = !open" @click.away="open = false" class="mt-1 w-full px-4 py-2.5 text-left bg-white border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 flex items-center justify-between">
                    <span x-text="selectedClientName" :class="selectedClient ? 'text-gray-900' : 'text-gray-500'"></span>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" x-transition @click.stop class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
                    <div class="p-2 sticky top-0 bg-white border-b" @click.stop>
                        <input type="text" x-model="search" @click.stop placeholder="Search clients..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    </div>
                    <ul class="py-1">
                        <li>
                            <button type="button" @click="selectedClient = null; selectedClientName = 'No Client'; open = false" class="w-full text-left px-4 py-2 text-sm hover:bg-indigo-50 focus:bg-indigo-50 focus:outline-none" :class="!selectedClient ? 'bg-indigo-100' : ''">
                                <div class="font-medium text-gray-900">No Client</div>
                                <div class="text-xs text-gray-500">For bonuses or other non-client charges</div>
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
                @error('client_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Date -->
            <div>
                <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Date *</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <input datepicker datepicker-format="yyyy-mm-dd" type="text" name="date" id="date" required value="{{ old('date', date('Y-m-d')) }}" placeholder="Select date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 p-2.5">
                </div>
                @error('date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Amount Charged -->
            <div>
                <label for="amount_charged" class="block text-sm font-medium text-gray-700 mb-2">Amount Charged *</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <span class="text-gray-500">$</span>
                    </div>
                    <input type="number" step="0.01" min="0" name="amount_charged" id="amount_charged" required value="{{ old('amount_charged') }}" class="block w-full pl-7 pr-4 py-2.5 rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900" placeholder="0.00">
                </div>
                @error('amount_charged')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Net -->
            <div>
                <label for="net" class="block text-sm font-medium text-gray-700 mb-2">Net Amount *</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <span class="text-gray-500">$</span>
                    </div>
                    <input type="number" step="0.01" min="0" name="net" id="net" required value="{{ old('net') }}" class="block w-full pl-7 pr-4 py-2.5 rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900" placeholder="0.00">
                </div>
                @error('net')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Commission Percentage -->
            <div>
                <label for="commission_percentage" class="block text-sm font-medium text-gray-700 mb-2">Commission Percentage</label>
                <div class="relative">
                    <input type="number" step="0.01" min="0" max="100" name="commission_percentage" id="commission_percentage" value="{{ old('commission_percentage') }}" class="block w-full px-4 py-2.5 rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900" placeholder="0.00">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <span class="text-gray-500">%</span>
                    </div>
                </div>
                @error('commission_percentage')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Program -->
            <div>
                <label for="program" class="block text-sm font-medium text-gray-700 mb-2">Program</label>
                <input type="text" name="program" id="program" value="{{ old('program') }}" class="mt-1 block w-full px-4 py-2.5 rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900" placeholder="e.g., 16 week 1-1 nutrition coaching">
                @error('program')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Country -->
            <div>
                <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                <input type="text" name="country" id="country" value="{{ old('country') }}" class="mt-1 block w-full px-4 py-2.5 rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900" placeholder="e.g., US">
                @error('country')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Stripe URL -->
            <div>
                <label for="stripe_url" class="block text-sm font-medium text-gray-700 mb-2">Stripe URL</label>
                <input type="url" name="stripe_url" id="stripe_url" value="{{ old('stripe_url') }}" class="mt-1 block w-full px-4 py-2.5 rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900" placeholder="https://...">
                @error('stripe_url')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Stripe Transaction ID -->
            <div>
                <label for="stripe_transaction_id" class="block text-sm font-medium text-gray-700 mb-2">Stripe Transaction ID</label>
                <input type="text" name="stripe_transaction_id" id="stripe_transaction_id" value="{{ old('stripe_transaction_id') }}" class="mt-1 block w-full px-4 py-2.5 rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900" placeholder="txn_...">
                @error('stripe_transaction_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Stripe Charge ID -->
            <div>
                <label for="stripe_charge_id" class="block text-sm font-medium text-gray-700 mb-2">Stripe Charge ID</label>
                <input type="text" name="stripe_charge_id" id="stripe_charge_id" value="{{ old('stripe_charge_id') }}" class="mt-1 block w-full px-4 py-2.5 rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900" placeholder="ch_...">
                @error('stripe_charge_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Billing Information Included -->
            <div class="md:col-span-2">
                <div class="flex items-center">
                    <input type="checkbox" name="billing_information_included" id="billing_information_included" value="1" {{ old('billing_information_included') ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="billing_information_included" class="ml-2 block text-sm text-gray-900">
                        Billing Information Included
                    </label>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('charges.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Create Charge
            </button>
        </div>
    </form>
</div>
@endsection

