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
        'commission_percentage',
        'payout',
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
        'commission_percentage' => 'decimal:2',
        'payout' => 'decimal:2',
        'billing_information_included' => 'boolean',
    ];

    /**
     * Calculate payout based on net and commission percentage
     */
    public function calculatePayout(): ?float
    {
        if (!$this->net || !$this->commission_percentage) {
            return null;
        }
        
        return $this->net * ($this->commission_percentage / 100);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($charge) {
            // Auto-calculate payout when saving
            if ($charge->net && $charge->commission_percentage) {
                $charge->payout = $charge->calculatePayout();
            } else {
                $charge->payout = null;
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

}
