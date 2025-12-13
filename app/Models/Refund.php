<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    protected $fillable = [
        'client_id',
        'charge_id',
        'date',
        'amount',
        'initial_amount_charged',
        'stripe_refund_id',
        'stripe_transaction_id',
        'reason',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'initial_amount_charged' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function charge(): BelongsTo
    {
        return $this->belongsTo(Charge::class);
    }
}
