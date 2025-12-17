@extends('layouts.app')

@section('title', 'Charges')

@section('content')
<div class="w-full py-6 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">All Charges</h1>
        <a href="{{ route('charges.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
            Add Charge
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Search/Filter Form -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <form method="GET" action="{{ route('charges.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
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

            <!-- Program -->
            <div>
                <label for="program" class="block text-sm font-medium text-gray-700 mb-1">Program</label>
                <input type="text" name="program" id="program" value="{{ request('program') }}" placeholder="Search program..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
            </div>

            <!-- Coach -->
            <div>
                <label for="coach_id" class="block text-sm font-medium text-gray-700 mb-1">Coach</label>
                <select name="coach_id" id="coach_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                    <option value="">All Coaches</option>
                    @foreach($coaches as $coach)
                        <option value="{{ $coach->id }}" {{ request('coach_id') == $coach->id ? 'selected' : '' }}>{{ $coach->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 font-medium text-sm">
                    Filter
                </button>
                <a href="{{ route('charges.index') }}" class="px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:ring-4 focus:ring-gray-300 font-medium text-sm">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="max-width: 200px;">Program</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Net</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Coach</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Commission</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Payout</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stripe</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($charges as $charge)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $charge->date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            @if($charge->client)
                                <a href="{{ route('clients.show', $charge->client) }}" class="text-indigo-600 hover:text-indigo-900">
                                    {{ $charge->client->name }}
                                </a>
                            @else
                                <span class="text-gray-400 italic">No Client</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500" style="max-width: 200px;">
                            <div class="truncate" title="{{ $charge->program ?? '—' }}">{{ $charge->program ?? '—' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">${{ number_format($charge->amount_charged, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">${{ number_format($charge->net, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($charge->client)
                                <select 
                                    class="coach-select bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 px-3 py-1.5 min-w-[150px]" 
                                    data-client-id="{{ $charge->client->id }}"
                                    data-charge-id="{{ $charge->id }}"
                                    onchange="updateCoach(this, {{ $charge->client->id }}, {{ $charge->id }})">
                                    <option value="">— No Coach —</option>
                                    @foreach($coaches as $coach)
                                        <option value="{{ $coach->id }}" 
                                            {{ $charge->client->coach_id == $coach->id ? 'selected' : '' }}>
                                            {{ $coach->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="coach-update-status ml-2 text-xs" id="status-{{ $charge->client->id }}" style="display: none;"></span>
                            @else
                                <select 
                                    class="coach-select-charge bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 px-3 py-1.5 min-w-[150px]" 
                                    data-charge-id="{{ $charge->id }}"
                                    onchange="updateChargeCoach(this, {{ $charge->id }})">
                                    <option value="">— No Coach —</option>
                                    @foreach($coaches as $coach)
                                        <option value="{{ $coach->id }}" 
                                            {{ $charge->coach_id == $coach->id ? 'selected' : '' }}>
                                            {{ $coach->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="coach-update-status ml-2 text-xs" id="charge-status-{{ $charge->id }}" style="display: none;"></span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <input 
                                    type="number" 
                                    min="0" 
                                    max="100" 
                                    step="1"
                                    class="commission-input w-20 px-2 py-1 text-sm text-right border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" 
                                    value="{{ $charge->commission_percentage ? (int)$charge->commission_percentage : '' }}"
                                    placeholder="—"
                                    data-charge-id="{{ $charge->id }}"
                                    onblur="updateCommission(this, {{ $charge->id }})">
                                <span class="text-gray-500">%</span>
                                <span class="commission-update-status ml-1 text-xs" id="commission-status-{{ $charge->id }}" style="display: none;"></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right" id="payout-{{ $charge->id }}">
                            @if($charge->payout)
                                ${{ number_format($charge->payout, 2) }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($charge->stripe_url)
                                <a href="{{ $charge->stripe_url }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                                    View
                                </a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('charges.edit', $charge) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                            <form action="{{ route('charges.destroy', $charge) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this charge?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-6 py-4 text-center text-sm text-gray-500">No charges found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $charges->links() }}
    </div>
</div>

@push('scripts')
<script>
// Store original values when page loads
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.coach-select, .coach-select-charge').forEach(select => {
        if (!select.dataset.originalValue) {
            select.dataset.originalValue = select.value;
        }
    });
});

function updateCoach(selectElement, clientId, chargeId) {
    const coachId = selectElement.value;
    const statusElement = document.getElementById('status-' + clientId);
    const originalValue = selectElement.dataset.originalValue || '';
    
    // Show loading state
    selectElement.disabled = true;
    statusElement.style.display = 'inline';
    statusElement.textContent = 'Updating...';
    statusElement.className = 'coach-update-status ml-2 text-xs text-blue-600';
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                      document.querySelector('input[name="_token"]')?.value;
    
    // Make AJAX request
    fetch(`/clients/${clientId}/update-coach`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            coach_id: coachId || null
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.message || 'Update failed');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            statusElement.textContent = '✓ Updated';
            statusElement.className = 'coach-update-status ml-2 text-xs text-green-600';
            
            // Update the original value to the new value
            selectElement.dataset.originalValue = coachId;
            
            // Update all other dropdowns for the same client
            document.querySelectorAll(`.coach-select[data-client-id="${clientId}"]`).forEach(otherSelect => {
                if (otherSelect !== selectElement) {
                    otherSelect.value = coachId;
                    otherSelect.dataset.originalValue = coachId;
                }
            });
            
            // Hide status after 2 seconds
            setTimeout(() => {
                statusElement.style.display = 'none';
            }, 2000);
        } else {
            throw new Error(data.message || 'Update failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        statusElement.textContent = '✗ Error';
        statusElement.className = 'coach-update-status ml-2 text-xs text-red-600';
        
        // Revert to original value
        selectElement.value = originalValue;
        
        // Hide status after 3 seconds
        setTimeout(() => {
            statusElement.style.display = 'none';
        }, 3000);
    })
    .finally(() => {
        selectElement.disabled = false;
    });
}

function updateChargeCoach(selectElement, chargeId) {
    const coachId = selectElement.value;
    const statusElement = document.getElementById('charge-status-' + chargeId);
    const originalValue = selectElement.dataset.originalValue || '';
    
    // Show loading state
    selectElement.disabled = true;
    statusElement.style.display = 'inline';
    statusElement.textContent = 'Updating...';
    statusElement.className = 'coach-update-status ml-2 text-xs text-blue-600';
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                      document.querySelector('input[name="_token"]')?.value;
    
    // Make AJAX request
    fetch(`/charges/${chargeId}/update-coach`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            coach_id: coachId || null
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.message || 'Update failed');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            statusElement.textContent = '✓ Updated';
            statusElement.className = 'coach-update-status ml-2 text-xs text-green-600';
            
            // Update the original value to the new value
            selectElement.dataset.originalValue = coachId;
            
            // Update all other dropdowns for the same charge
            document.querySelectorAll(`.coach-select-charge[data-charge-id="${chargeId}"]`).forEach(otherSelect => {
                if (otherSelect !== selectElement) {
                    otherSelect.value = coachId;
                    otherSelect.dataset.originalValue = coachId;
                }
            });
            
            // Hide status after 2 seconds
            setTimeout(() => {
                statusElement.style.display = 'none';
            }, 2000);
        } else {
            throw new Error(data.message || 'Update failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        statusElement.textContent = '✗ Error';
        statusElement.className = 'coach-update-status ml-2 text-xs text-red-600';
        
        // Revert to original value
        selectElement.value = originalValue;
        
        // Hide status after 3 seconds
        setTimeout(() => {
            statusElement.style.display = 'none';
        }, 3000);
    })
    .finally(() => {
        selectElement.disabled = false;
    });
}

// Store original commission values when page loads
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.commission-input').forEach(input => {
        input.dataset.originalValue = input.value || '';
    });
});

function updateCommission(inputElement, chargeId) {
    // Prevent duplicate calls if already updating
    if (inputElement.dataset.updating === 'true') {
        return;
    }
    
    const commissionValue = inputElement.value === '' ? null : parseInt(inputElement.value);
    const statusElement = document.getElementById('commission-status-' + chargeId);
    const originalValue = inputElement.dataset.originalValue || '';
    
    // Don't update if value hasn't changed
    if (commissionValue === (originalValue === '' ? null : parseInt(originalValue))) {
        return;
    }
    
    // Validate range
    if (commissionValue !== null && (commissionValue < 0 || commissionValue > 100)) {
        statusElement.style.display = 'inline';
        statusElement.textContent = '✗ Invalid (0-100)';
        statusElement.className = 'commission-update-status ml-1 text-xs text-red-600';
        inputElement.value = originalValue;
        setTimeout(() => {
            statusElement.style.display = 'none';
        }, 3000);
        return;
    }
    
    // Mark as updating
    inputElement.dataset.updating = 'true';
    
    // Show loading state
    inputElement.disabled = true;
    statusElement.style.display = 'inline';
    statusElement.textContent = 'Updating...';
    statusElement.className = 'commission-update-status ml-1 text-xs text-blue-600';
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                      document.querySelector('input[name="_token"]')?.value;
    
    // Make AJAX request
    fetch(`/charges/${chargeId}/update-commission`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            commission_percentage: commissionValue
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.message || 'Update failed');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            statusElement.textContent = '✓';
            statusElement.className = 'commission-update-status ml-1 text-xs text-green-600';
            
            // Update the original value to the new value
            inputElement.dataset.originalValue = commissionValue === null ? '' : commissionValue.toString();
            
            // Update payout display
            const payoutElement = document.getElementById('payout-' + chargeId);
            if (payoutElement && data.payout !== null && data.payout !== undefined) {
                payoutElement.textContent = '$' + parseFloat(data.payout).toFixed(2);
            } else if (payoutElement) {
                payoutElement.textContent = '—';
            }
            
            // Hide status after 2 seconds
            setTimeout(() => {
                statusElement.style.display = 'none';
            }, 2000);
        } else {
            throw new Error(data.message || 'Update failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        statusElement.textContent = '✗ Error';
        statusElement.className = 'commission-update-status ml-1 text-xs text-red-600';
        
        // Revert to original value
        inputElement.value = originalValue;
        
        // Hide status after 3 seconds
        setTimeout(() => {
            statusElement.style.display = 'none';
        }, 3000);
    })
    .finally(() => {
        inputElement.disabled = false;
        inputElement.dataset.updating = 'false';
    });
}
</script>
@endpush
@endsection
