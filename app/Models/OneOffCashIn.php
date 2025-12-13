<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OneOffCashIn extends Model
{
    protected $fillable = [
        'coach_id',
        'appointment_setter_id',
        'closer_id',
        'date',
        'amount',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class);
    }

    public function appointmentSetter(): BelongsTo
    {
        return $this->belongsTo(AppointmentSetter::class);
    }

    public function closer(): BelongsTo
    {
        return $this->belongsTo(Closer::class);
    }
}
