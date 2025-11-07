<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RouteMatch extends Model
{
    protected $table = 'matches';

    protected $fillable = [
        'name',
        'type',
        'description',
    ];

    public function conditions(): HasMany
    {
        return $this->hasMany(MatchCondition::class, 'match_id');
    }

    public function routes(): HasMany
    {
        return $this->hasMany(Route::class, 'match_id');
    }
}
