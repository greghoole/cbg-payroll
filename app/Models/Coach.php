<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coach extends Model
{
    protected $fillable = [
        'name',
        'email',
    ];

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class)
            ->withPivot('commission_rate')
            ->withTimestamps();
    }

    public function oneOffCashIns(): HasMany
    {
        return $this->hasMany(OneOffCashIn::class);
    }
}
