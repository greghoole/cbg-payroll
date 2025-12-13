<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Client extends Model
{
    protected $fillable = [
        'email',
        'name',
        'stripe_customer_id',
        'country',
    ];

    public function charges(): HasMany
    {
        return $this->hasMany(Charge::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function coaches(): BelongsToMany
    {
        return $this->belongsToMany(Coach::class)
            ->withPivot('commission_rate')
            ->withTimestamps();
    }

    public function appointmentSetters(): BelongsToMany
    {
        return $this->belongsToMany(AppointmentSetter::class, 'client_appointment_setter')
            ->withPivot('commission_rate')
            ->withTimestamps();
    }

    public function closers(): BelongsToMany
    {
        return $this->belongsToMany(Closer::class, 'client_closer')
            ->withPivot('commission_rate')
            ->withTimestamps();
    }
}
