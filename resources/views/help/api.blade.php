@extends('layouts.app')

@section('title', 'API Documentation')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">API Documentation</h1>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Authentication</h2>
        <p class="text-gray-700 mb-4">
            All API endpoints require Bearer token authentication. Include the API token in the <code class="bg-gray-100 px-2 py-1 rounded">Authorization</code> header of your requests.
        </p>
        <div class="bg-gray-50 p-4 rounded-md">
            <code class="text-sm">
                Authorization: Bearer your-api-token-from-env
            </code>
        </div>
        <p class="text-gray-600 text-sm mt-4">
            The API token is managed in the <a href="{{ route('settings.index') }}" class="text-indigo-600 hover:text-indigo-800 underline">Settings</a> page. You can view and regenerate the token from there. If no token is set in the database, the system will fall back to checking the <code class="bg-gray-100 px-1 rounded">API_TOKEN</code> environment variable.
        </p>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Base URL</h2>
        <p class="text-gray-700 mb-2">
            All API endpoints are prefixed with <code class="bg-gray-100 px-2 py-1 rounded">/api</code>
        </p>
        <div class="bg-gray-50 p-4 rounded-md">
            <code class="text-sm">
                {{ url('/api') }}
            </code>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Store Charge</h2>
        <p class="text-gray-700 mb-2">
            <span class="inline-block bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded mr-2">POST</span>
            <code class="text-lg">/api/stripe/charge</code>
        </p>
        <p class="text-gray-600 mb-4">
            Creates or updates a charge record. If the client doesn't exist, it will be created automatically. If a coach is provided, the coach will be assigned to the client.
        </p>

        <h3 class="text-lg font-semibold text-gray-800 mt-6 mb-3">Request Body</h3>
        <div class="bg-gray-50 p-4 rounded-md mb-4">
            <pre class="text-sm overflow-x-auto"><code>{
  "date": "2024-01-15",
  "net": 100.00,
  "program": "Program Name",
  "email_address": "client@example.com",
  "client_name": "Client Name",
  "amount_charged": 110.00,
  "stripe_url": "https://dashboard.stripe.com/...",
  "client_stripe_id": "cus_xxxxx",
  "billing_information_is_included": true,
  "country": "US",
  "id": "ch_xxxxx",
  "transaction_id": "txn_xxxxx",
  "coach": "Coach Name"
}</code></pre>
        </div>

        <h3 class="text-lg font-semibold text-gray-800 mt-6 mb-3">Required Fields</h3>
        <ul class="list-disc list-inside text-gray-700 mb-4 space-y-1">
            <li><code class="bg-gray-100 px-1 rounded">date</code> - Date in YYYY-MM-DD format</li>
            <li><code class="bg-gray-100 px-1 rounded">net</code> - Net amount (numeric)</li>
            <li><code class="bg-gray-100 px-1 rounded">email_address</code> - Client email address</li>
            <li><code class="bg-gray-100 px-1 rounded">client_name</code> - Client name</li>
            <li><code class="bg-gray-100 px-1 rounded">amount_charged</code> - Total amount charged (numeric)</li>
            <li><code class="bg-gray-100 px-1 rounded">transaction_id</code> - Stripe transaction ID (unique identifier)</li>
        </ul>

        <h3 class="text-lg font-semibold text-gray-800 mt-6 mb-3">Optional Fields</h3>
        <ul class="list-disc list-inside text-gray-700 mb-4 space-y-1">
            <li><code class="bg-gray-100 px-1 rounded">program</code> - Program name</li>
            <li><code class="bg-gray-100 px-1 rounded">stripe_url</code> - URL to Stripe dashboard</li>
            <li><code class="bg-gray-100 px-1 rounded">client_stripe_id</code> - Stripe customer ID</li>
            <li><code class="bg-gray-100 px-1 rounded">billing_information_is_included</code> - Boolean</li>
            <li><code class="bg-gray-100 px-1 rounded">country</code> - Country code</li>
            <li><code class="bg-gray-100 px-1 rounded">id</code> - Stripe charge ID</li>
            <li><code class="bg-gray-100 px-1 rounded">coach</code> - Coach name or email (will create coach if doesn't exist)</li>
        </ul>

        <h3 class="text-lg font-semibold text-gray-800 mt-6 mb-3">Success Response</h3>
        <div class="bg-gray-50 p-4 rounded-md mb-4">
            <pre class="text-sm overflow-x-auto"><code>{
  "success": true,
  "message": "Charge stored successfully",
  "charge_id": 123
}</code></pre>
        </div>
        <p class="text-gray-600 text-sm">Status Code: <span class="font-semibold">201 Created</span></p>

        <h3 class="text-lg font-semibold text-gray-800 mt-6 mb-3">Error Responses</h3>
        <div class="space-y-4">
            <div>
                <p class="text-gray-700 mb-2"><span class="font-semibold">401 Unauthorized</span> - Invalid or missing API token</p>
                <div class="bg-gray-50 p-4 rounded-md">
                    <pre class="text-sm overflow-x-auto"><code>{
  "success": false,
  "message": "Unauthorized. Invalid or missing bearer token."
}</code></pre>
                </div>
            </div>
            <div>
                <p class="text-gray-700 mb-2"><span class="font-semibold">422 Validation Error</span> - Invalid request data</p>
                <div class="bg-gray-50 p-4 rounded-md">
                    <pre class="text-sm overflow-x-auto"><code>{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email_address": ["The email address field is required."]
  }
}</code></pre>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Store Refund</h2>
        <p class="text-gray-700 mb-2">
            <span class="inline-block bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded mr-2">POST</span>
            <code class="text-lg">/api/stripe/refund</code>
        </p>
        <p class="text-gray-600 mb-4">
            Creates or updates a refund record. If the client doesn't exist, it will be created automatically.
        </p>

        <h3 class="text-lg font-semibold text-gray-800 mt-6 mb-3">Request Body</h3>
        <div class="bg-gray-50 p-4 rounded-md mb-4">
            <pre class="text-sm overflow-x-auto"><code>{
  "date": "2024-01-20",
  "amount": 50.00,
  "email_address": "client@example.com",
  "client_name": "Client Name",
  "client_stripe_id": "cus_xxxxx",
  "transaction_id": "txn_xxxxx",
  "stripe_refund_id": "re_xxxxx",
  "charge_id": "ch_xxxxx",
  "reason": "Refund reason",
  "initial_amount_charged": 110.00,
  "notes": "Refund notes"
}</code></pre>
        </div>

        <h3 class="text-lg font-semibold text-gray-800 mt-6 mb-3">Required Fields</h3>
        <ul class="list-disc list-inside text-gray-700 mb-4 space-y-1">
            <li><code class="bg-gray-100 px-1 rounded">date</code> - Date in YYYY-MM-DD format</li>
            <li><code class="bg-gray-100 px-1 rounded">amount</code> - Refund amount (numeric)</li>
            <li><code class="bg-gray-100 px-1 rounded">email_address</code> - Client email address</li>
            <li><code class="bg-gray-100 px-1 rounded">transaction_id</code> - Stripe transaction ID (unique identifier)</li>
        </ul>

        <h3 class="text-lg font-semibold text-gray-800 mt-6 mb-3">Optional Fields</h3>
        <ul class="list-disc list-inside text-gray-700 mb-4 space-y-1">
            <li><code class="bg-gray-100 px-1 rounded">client_name</code> - Client name</li>
            <li><code class="bg-gray-100 px-1 rounded">client_stripe_id</code> - Stripe customer ID</li>
            <li><code class="bg-gray-100 px-1 rounded">stripe_refund_id</code> - Stripe refund ID</li>
            <li><code class="bg-gray-100 px-1 rounded">charge_id</code> - Stripe charge ID (to link refund to charge)</li>
            <li><code class="bg-gray-100 px-1 rounded">reason</code> - Refund reason</li>
            <li><code class="bg-gray-100 px-1 rounded">initial_amount_charged</code> - Original amount charged before refund (numeric)</li>
            <li><code class="bg-gray-100 px-1 rounded">notes</code> - Additional notes</li>
        </ul>

        <h3 class="text-lg font-semibold text-gray-800 mt-6 mb-3">Success Response</h3>
        <div class="bg-gray-50 p-4 rounded-md mb-4">
            <pre class="text-sm overflow-x-auto"><code>{
  "success": true,
  "message": "Refund stored successfully",
  "refund_id": 456
}</code></pre>
        </div>
        <p class="text-gray-600 text-sm">Status Code: <span class="font-semibold">201 Created</span></p>

        <h3 class="text-lg font-semibold text-gray-800 mt-6 mb-3">Error Responses</h3>
        <div class="space-y-4">
            <div>
                <p class="text-gray-700 mb-2"><span class="font-semibold">401 Unauthorized</span> - Invalid or missing API token</p>
                <div class="bg-gray-50 p-4 rounded-md">
                    <pre class="text-sm overflow-x-auto"><code>{
  "success": false,
  "message": "Unauthorized. Invalid or missing bearer token."
}</code></pre>
                </div>
            </div>
            <div>
                <p class="text-gray-700 mb-2"><span class="font-semibold">422 Validation Error</span> - Invalid request data</p>
                <div class="bg-gray-50 p-4 rounded-md">
                    <pre class="text-sm overflow-x-auto"><code>{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "transaction_id": ["The transaction id field is required."]
  }
}</code></pre>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-2">Important Notes</h3>
        <ul class="list-disc list-inside text-blue-800 space-y-2">
            <li>Both charges and refunds are identified by <code class="bg-blue-100 px-1 rounded">transaction_id</code> - duplicate transaction IDs will update existing records</li>
            <li>Clients are automatically created when a charge or refund is received with a new email address</li>
            <li>If a client already exists (by email address), the charge or refund will be associated with that existing client</li>
            <li>If a coach is provided in the charge, the coach will be automatically created and assigned to the client</li>
            <li>Commission rates must be set manually in the web interface after coaches are assigned</li>
        </ul>
    </div>
</div>
@endsection

