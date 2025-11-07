<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'name',
        'type',
        'description',
    ];

    public function routes(): HasMany
    {
        return $this->hasMany(Route::class, 'from_service_id');
    }
}
