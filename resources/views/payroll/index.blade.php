@extends('layouts.app')

@section('title', 'Payroll')

@section('content')
<div class="w-full py-6 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Payroll</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Search/Filter Form -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <form method="GET" action="{{ route('payroll.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
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

            <!-- Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 font-medium text-sm">
                    Filter
                </button>
                <a href="{{ route('payroll.index') }}" class="px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:ring-4 focus:ring-gray-300 font-medium text-sm">
                    Clear
                </a>
            </div>
        </form>
    </div>

    @if(!$hasDateRange)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <p class="text-blue-800 text-center">
                Please select a date range to view payroll data.
            </p>
        </div>
    @else
        @if($coachesWithPayouts->count() > 0)
            <div class="space-y-4 mb-6">
                @foreach($coachesWithPayouts as $coachData)
                    <div class="bg-white shadow rounded-lg border border-gray-200" x-data="{ open: false }">
                        <button 
                            @click="open = !open" 
                            class="flex items-center justify-between w-full text-left focus:outline-none px-6 py-4 hover:bg-gray-50 rounded-t-lg"
                        >
                            <div class="flex items-center justify-between w-full pr-4">
                                <div>
                                    <a href="{{ route('coaches.show', $coachData['coach']) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900" onclick="event.stopPropagation();">
                                        {{ $coachData['coach']->name }}
                                    </a>
                                    @if($coachData['coach']->email)
                                        <div class="text-xs text-gray-500">{{ $coachData['coach']->email }}</div>
                                    @endif
                                </div>
                                <span class="text-sm font-medium text-gray-900">${{ number_format($coachData['total_payout'], 2) }}</span>
                            </div>
                            <svg 
                                class="w-5 h-5 text-gray-500 transition-transform duration-200 flex-shrink-0" 
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
                            class="border-t border-gray-200"
                        >
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client Name</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email Address</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Commission %</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Payout Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($coachData['charges'] as $charge)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $charge['date'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $charge['client_name'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $charge['client_email'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">${{ number_format($charge['amount'], 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($charge['commission_percentage'], 2) }}%</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">${{ number_format($charge['payout'], 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Total Summary -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-bold text-gray-900">Total</span>
                    <span class="text-sm font-bold text-gray-900">${{ number_format($coachesWithPayouts->sum('total_payout'), 2) }}</span>
                </div>
            </div>
        @else
            <div class="bg-white shadow rounded-lg p-6">
                <p class="text-sm text-gray-500 text-center">No payroll data found for the selected date range</p>
            </div>
        @endif
    @endif
</div>
@endsection

