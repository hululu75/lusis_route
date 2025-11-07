<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rule extends Model
{
    protected $fillable = [
        'name',
        'project_id',
        'class',
        'type',
        'delta_id',
        'on_failure',
        'matching_cond',
        'route_cond_ok',
        'route_cond_ko',
        'delta_next',
        'delta_cond_ok',
        'delta_cond_ko',
        'description',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function delta(): BelongsTo
    {
        return $this->belongsTo(Delta::class);
    }

    public function routes(): HasMany
    {
        return $this->hasMany(Route::class);
    }
}
