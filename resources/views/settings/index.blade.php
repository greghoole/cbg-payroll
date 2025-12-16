@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Settings</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">API Token</h2>
        <p class="text-gray-700 mb-4">
            The API token is used to authenticate requests to the API endpoints. Keep this token secure and never share it publicly.
        </p>

        @if($apiToken)
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Current API Token</label>
                <div class="flex items-center space-x-2">
                    <input type="text" 
                           id="api-token" 
                           value="{{ $apiToken }}" 
                           readonly
                           class="flex-1 block w-full px-4 py-2.5 rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 font-mono text-sm text-gray-900">
                    <button onclick="copyToClipboard('api-token')" 
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 text-sm">
                        Copy
                    </button>
                </div>
                <p class="text-gray-500 text-xs mt-2">
                    Click "Copy" to copy the token to your clipboard.
                </p>
            </div>
        @else
            <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                <p class="text-yellow-800 text-sm">
                    No API token has been generated yet. Click "Generate API Token" below to create one.
                </p>
            </div>
        @endif

        <form action="{{ route('settings.regenerate-token') }}" method="POST" onsubmit="return confirm('Are you sure you want to regenerate the API token? This will invalidate the current token and you will need to update all integrations using it.');">
            @csrf
            @method('POST')
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                {{ $apiToken ? 'Regenerate API Token' : 'Generate API Token' }}
            </button>
        </form>

        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
            <h3 class="text-sm font-semibold text-blue-900 mb-2">Important Notes</h3>
            <ul class="list-disc list-inside text-blue-800 text-sm space-y-1">
                <li>Regenerating the token will invalidate the current token immediately</li>
                <li>You must update all integrations (N8N, webhooks, etc.) with the new token</li>
                <li>The token is stored securely in the database</li>
                <li>If you have an <code class="bg-blue-100 px-1 rounded">API_TOKEN</code> in your <code class="bg-blue-100 px-1 rounded">.env</code> file, it will be used as a fallback until a database token is set</li>
            </ul>
        </div>
    </div>

    @auth
        @if(auth()->user()->is_admin)
            <div class="bg-red-50 border-2 border-red-300 shadow-lg rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-red-900 mb-4">⚠️ Danger Zone</h2>
                <p class="text-red-800 mb-4">
                    <strong>Warning:</strong> This action is irreversible and will permanently delete all charges, refunds, and clients in the system. This cannot be undone.
                </p>
                <p class="text-red-700 text-sm mb-6">
                    Use this feature only when you need to completely reset the financial data. All charge, refund, and client records will be permanently removed from the database.
                </p>
                <form action="{{ route('settings.reset-data') }}" method="POST" onsubmit="return confirm('⚠️ WARNING: This will permanently delete ALL charges, refunds, and clients in the system. This action CANNOT be undone.\n\nAre you absolutely sure you want to proceed?');">
                    @csrf
                    @method('POST')
                    <button type="submit" class="px-6 py-3 bg-red-600 text-white font-semibold rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                        Reset All Data
                    </button>
                </form>
            </div>
        @endif
    @endauth
</div>

<script>
function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    element.select();
    element.setSelectionRange(0, 99999); // For mobile devices
    
    navigator.clipboard.writeText(element.value).then(function() {
        // Show a temporary success message
        const button = event.target;
        const originalText = button.textContent;
        button.textContent = 'Copied!';
        button.classList.add('bg-green-100', 'text-green-800');
        button.classList.remove('bg-gray-100', 'text-gray-700');
        
        setTimeout(function() {
            button.textContent = originalText;
            button.classList.remove('bg-green-100', 'text-green-800');
            button.classList.add('bg-gray-100', 'text-gray-700');
        }, 2000);
    }).catch(function(err) {
        console.error('Failed to copy: ', err);
        alert('Failed to copy to clipboard');
    });
}
</script>
@endsection

