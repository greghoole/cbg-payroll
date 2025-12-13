<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use Illuminate\Http\Request;

class ChargeController extends Controller
{
    public function index()
    {
        $charges = Charge::with('client')
            ->orderBy('date', 'desc')
            ->paginate(50);
        
        return view('charges.index', compact('charges'));
    }
}
