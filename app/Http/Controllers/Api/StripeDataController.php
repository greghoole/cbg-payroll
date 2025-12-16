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

            // Find or create client by email address
            $client = Client::firstOrCreate(
                ['email' => $validated['email_address']],
                [
                    'name' => $validated['client_name'],
                    'stripe_customer_id' => $validated['client_stripe_id'] ?? null,
                    'country' => $validated['country'] ?? null,
                ]
            );

            // Update client info if new data is provided (for existing clients)
            $updated = false;
            if ($validated['client_name'] && $client->name !== $validated['client_name']) {
                $client->name = $validated['client_name'];
                $updated = true;
            }
            if ($validated['client_stripe_id'] && $client->stripe_customer_id !== $validated['client_stripe_id']) {
                $client->stripe_customer_id = $validated['client_stripe_id'];
                $updated = true;
            }
            if (isset($validated['country']) && $client->country !== $validated['country']) {
                $client->country = $validated['country'];
                $updated = true;
            }
            if ($updated) {
                $client->save();
            }

            // If coach is provided, assign coach to client
            $coach = null;
            if (!empty($validated['coach'])) {
                $coach = Coach::firstOrCreate(
                    ['name' => $validated['coach']],
                    ['email' => null]
                );

                // Assign coach to client (1:1 relationship)
                if ($client->coach_id !== $coach->id) {
                    $client->update(['coach_id' => $coach->id]);
                }
            } else {
                // If no coach provided, check if client already has a coach
                $client->load('coach');
                $coach = $client->coach;
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

            // If client has a coach and charge doesn't have commission, apply existing commission
            if ($coach && !$charge->commission_percentage) {
                // Find the most recent charge for this client with a commission percentage
                $existingCharge = Charge::where('client_id', $client->id)
                    ->where('id', '!=', $charge->id)
                    ->whereNotNull('commission_percentage')
                    ->orderBy('date', 'desc')
                    ->first();
                
                if ($existingCharge) {
                    $charge->commission_percentage = $existingCharge->commission_percentage;
                    // Payout will be auto-calculated by the model's boot method
                    $charge->save();
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
                'client_stripe_id' => 'nullable|string',
                'stripe_refund_id' => 'nullable|string',
                'transaction_id' => 'required|string',
                'charge_id' => 'nullable|string', // Stripe charge ID
                'reason' => 'nullable|string',
                'initial_amount_charged' => 'nullable|numeric',
                'notes' => 'nullable|string',
            ]);

            // Find or create client by email address
            $client = Client::firstOrCreate(
                ['email' => $validated['email_address']],
                [
                    'name' => $validated['client_name'] ?? 'Unknown',
                    'stripe_customer_id' => $validated['client_stripe_id'] ?? null,
                    'country' => null,
                ]
            );

            // Update client info if new data is provided (for existing clients)
            $updated = false;
            if (!empty($validated['client_name']) && $client->name !== $validated['client_name']) {
                $client->name = $validated['client_name'];
                $updated = true;
            }
            if (!empty($validated['client_stripe_id']) && $client->stripe_customer_id !== $validated['client_stripe_id']) {
                $client->stripe_customer_id = $validated['client_stripe_id'];
                $updated = true;
            }
            if ($updated) {
                $client->save();
            }

            // Find related charge if charge_id is provided
            $charge = null;
            if (!empty($validated['charge_id'])) {
                $charge = Charge::where('stripe_charge_id', $validated['charge_id'])
                    ->orWhere('stripe_transaction_id', $validated['charge_id'])
                    ->first();
            }

            // Find or create refund by transaction_id (primary identifier)
            $refund = Refund::updateOrCreate(
                ['stripe_transaction_id' => $validated['transaction_id']],
                [
                    'client_id' => $client->id,
                    'charge_id' => $charge?->id,
                    'date' => $validated['date'],
                    'amount' => $validated['amount'],
                    'initial_amount_charged' => $validated['initial_amount_charged'] ?? null,
                    'stripe_refund_id' => $validated['stripe_refund_id'] ?? null,
                    'reason' => $validated['reason'] ?? null,
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
