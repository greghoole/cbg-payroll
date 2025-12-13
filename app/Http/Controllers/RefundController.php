<?php

namespace App\Http\Controllers;

use App\Models\Refund;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    public function index()
    {
        $refunds = Refund::with(['client', 'charge'])
            ->orderBy('date', 'desc')
            ->paginate(50);
        
        return view('refunds.index', compact('refunds'));
    }
}
