<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use App\Models\Charge;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $coachesWithPayouts = collect();

        // Only show results if both date_from and date_to are provided
        if ($request->filled('date_from') && $request->filled('date_to')) {
            // Get charges within date range that have a coach (either through client or direct coach_id)
            $charges = Charge::query()
                ->with(['client'])
                ->whereNotNull('payout')
                ->where('payout', '>', 0)
                ->where('date', '>=', $request->date_from)
                ->where('date', '<=', $request->date_to)
                ->where(function($query) {
                    // Charges with client that has a coach_id
                    $query->whereHas('client', function($q) {
                        $q->whereNotNull('coach_id');
                    })
                    // OR charges with direct coach_id
                    ->orWhereNotNull('coach_id');
                })
                ->get();

            // Group charges by coach (either client->coach_id or direct coach_id)
            $chargesByCoach = $charges->groupBy(function($charge) {
                // Use direct coach_id if available, otherwise use client's coach_id
                return $charge->coach_id ?? ($charge->client ? $charge->client->coach_id : null);
            })->filter(function($group, $coachId) {
                return $coachId !== null;
            });

            // Get all coaches with charges
            $coachIds = $chargesByCoach->keys()->filter();
            $coaches = Coach::whereIn('id', $coachIds)->get();

            // Map coaches with their payouts and charges
            $coachesWithPayouts = $coaches->map(function ($coach) use ($chargesByCoach) {
                $coachCharges = $chargesByCoach->get($coach->id, collect());
                
                $totalPayout = $coachCharges->sum('payout');
                
                // Prepare charge details
                $chargeDetails = $coachCharges->map(function($charge) {
                    return [
                        'date' => $charge->date->format('M d, Y'),
                        'client_name' => $charge->client ? $charge->client->name : 'No Client',
                        'client_email' => $charge->client ? $charge->client->email : '—',
                        'amount' => $charge->amount_charged ?? $charge->net,
                        'commission_percentage' => $charge->commission_percentage,
                        'payout' => $charge->payout,
                        'program' => $charge->program,
                    ];
                })->sortByDesc(function($charge) {
                    return strtotime($charge['date']);
                })->values()->toArray();

                return [
                    'coach' => $coach,
                    'total_payout' => $totalPayout,
                    'charges' => $chargeDetails,
                ];
            })->filter(function ($item) {
                return $item['total_payout'] > 0;
            })->sortByDesc('total_payout');
        }

        $hasDateRange = $request->filled('date_from') && $request->filled('date_to');

        return view('payroll.index', compact('coachesWithPayouts', 'hasDateRange'));
    }
}
