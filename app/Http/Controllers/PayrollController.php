<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use App\Models\Charge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $coachesWithPayouts = collect();

        // Only show results if both date_from and date_to are provided
        if ($request->filled('date_from') && $request->filled('date_to')) {
            // Build query for charges with date filters
            $chargesQuery = Charge::query()
                ->join('clients', 'charges.client_id', '=', 'clients.id')
                ->whereNotNull('charges.payout')
                ->where('charges.payout', '>', 0)
                ->where('charges.date', '>=', $request->date_from)
                ->where('charges.date', '<=', $request->date_to);

            // Group by coach and sum payouts
            $payoutsByCoach = $chargesQuery
                ->select('clients.coach_id', DB::raw('SUM(charges.payout) as total_payout'))
                ->groupBy('clients.coach_id')
                ->get()
                ->keyBy('coach_id');

            // Get all coaches with payouts
            $coachIds = $payoutsByCoach->keys()->filter();
            $coaches = Coach::whereIn('id', $coachIds)->get();

            // Map coaches with their payouts
            $coachesWithPayouts = $coaches->map(function ($coach) use ($payoutsByCoach) {
                $payoutData = $payoutsByCoach->get($coach->id);
                return [
                    'coach' => $coach,
                    'total_payout' => $payoutData ? (float) $payoutData->total_payout : 0,
                ];
            })->filter(function ($item) {
                return $item['total_payout'] > 0;
            })->sortByDesc('total_payout');
        }

        $hasDateRange = $request->filled('date_from') && $request->filled('date_to');

        return view('payroll.index', compact('coachesWithPayouts', 'hasDateRange'));
    }
}
