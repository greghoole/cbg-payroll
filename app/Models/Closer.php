<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Closer extends Model
{
    protected $fillable = [
        'name',
        'email',
    ];

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'client_closer')
            ->withPivot('commission_rate')
            ->withTimestamps();
    }
}
