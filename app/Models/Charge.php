<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Charge extends Model
{
    protected $fillable = [
        'client_id',
        'date',
        'net',
        'amount_charged',
        'program',
        'stripe_url',
        'stripe_transaction_id',
        'stripe_charge_id',
        'billing_information_included',
        'country',
    ];

    protected $casts = [
        'date' => 'date',
        'net' => 'decimal:2',
        'amount_charged' => 'decimal:2',
        'billing_information_included' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    /**
     * Calculate commission for a specific coach on this charge
     */
    public function getCommissionForCoach(Coach $coach): float
    {
        $pivot = $this->client->coaches()->where('coach_id', $coach->id)->first()?->pivot;
        
        if (!$pivot || !$pivot->commission_rate) {
            return 0;
        }

        return ($this->net * $pivot->commission_rate) / 100;
    }
}
