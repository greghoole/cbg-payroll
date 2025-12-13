<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppointmentSetter extends Model
{
    protected $fillable = [
        'name',
        'email',
    ];

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'client_appointment_setter')
            ->withPivot('commission_rate')
            ->withTimestamps();
    }

    public function oneOffCashIns(): HasMany
    {
        return $this->hasMany(OneOffCashIn::class, 'appointment_setter_id');
    }
}
