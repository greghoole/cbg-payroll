<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Charge;
use App\Models\Coach;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class StripeDataController extends Controller
{
    /**
     * Receive and store charge data from Stripe/N8N
     */
    public function storeCharge(Request $request)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'net' => 'required|numeric',
                'program' => 'nullable|string',
                'email_address' => 'required|email',
                'client_name' => 'required|string',
                'amount_charged' => 'required|numeric',
                'stripe_url' => 'nullable|url',
                'client_stripe_id' => 'nullable|string',
                'billing_information_is_included' => 'nullable|boolean',
                'country' => 'nullable|string',
                'id' => 'nullable|string',
                'transaction_id' => 'required|string',
                'coach' => 'nullable|string', // Coach name or email
            ]);

            // Find or create client by email
            $client = Client::firstOrCreate(
                ['email' => $validated['email_address']],
                [
                    'name' => $validated['client_name'],
                    'stripe_customer_id' => $validated['client_stripe_id'],
                    'country' => $validated['country'] ?? null,
                ]
            );

            // Update client info if needed
            if (!$client->stripe_customer_id && $validated['client_stripe_id']) {
                $client->stripe_customer_id = $validated['client_stripe_id'];
                $client->save();
            }

            // Find or create charge
            $charge = Charge::updateOrCreate(
                ['stripe_transaction_id' => $validated['transaction_id']],
                [
                    'client_id' => $client->id,
                    'date' => $validated['date'],
                    'net' => $validated['net'],
                    'amount_charged' => $validated['amount_charged'],
                    'program' => $validated['program'] ?? null,
                    'stripe_url' => $validated['stripe_url'] ?? null,
                    'stripe_charge_id' => $validated['id'] ?? $validated['transaction_id'],
                    'billing_information_included' => $validated['billing_information_is_included'] ?? false,
                    'country' => $validated['country'] ?? null,
                ]
            );

            // If coach is provided, assign coach to client
            if (!empty($validated['coach'])) {
                $coach = Coach::firstOrCreate(
                    ['name' => $validated['coach']],
                    ['email' => null]
                );

                // Attach coach to client if not already attached
                if (!$client->coaches->contains($coach->id)) {
                    $client->coaches()->attach($coach->id, ['commission_rate' => 0]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Charge stored successfully',
                'charge_id' => $charge->id,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error storing charge: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error storing charge: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Receive and store refund data from Stripe/N8N
     */
    public function storeRefund(Request $request)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'amount' => 'required|numeric',
                'email_address' => 'required|email',
                'client_name' => 'nullable|string',
                'stripe_refund_id' => 'required|string',
                'transaction_id' => 'nullable|string',
                'charge_id' => 'nullable|string', // Stripe charge ID
                'program' => 'nullable|string',
                'notes' => 'nullable|string',
            ]);

            // Find client by email
            $client = Client::where('email', $validated['email_address'])->first();

            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found. Please create a charge first.',
                ], 404);
            }

            // Find related charge if charge_id is provided
            $charge = null;
            if (!empty($validated['charge_id'])) {
                $charge = Charge::where('stripe_charge_id', $validated['charge_id'])
                    ->orWhere('stripe_transaction_id', $validated['charge_id'])
                    ->first();
            }

            // Create refund
            $refund = Refund::updateOrCreate(
                ['stripe_refund_id' => $validated['stripe_refund_id']],
                [
                    'client_id' => $client->id,
                    'charge_id' => $charge?->id,
                    'date' => $validated['date'],
                    'amount' => $validated['amount'],
                    'stripe_transaction_id' => $validated['transaction_id'] ?? null,
                    'program' => $validated['program'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Refund stored successfully',
                'refund_id' => $refund->id,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error storing refund: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error storing refund: ' . $e->getMessage(),
            ], 500);
        }
    }
}
