<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coach extends Model
{
    protected $fillable = [
        'name',
        'email',
    ];

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }
}
