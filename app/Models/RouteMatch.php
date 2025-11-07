<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RouteMatch extends Model
{
    protected $table = 'matches';

    protected $fillable = [
        'name',
        'project_id',
        'type',
        'description',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function conditions(): HasMany
    {
        return $this->hasMany(MatchCondition::class, 'match_id');
    }

    public function routes(): HasMany
    {
        return $this->hasMany(Route::class, 'match_id');
    }
}
