<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RouteFile extends Model
{
    protected $fillable = [
        'project_id',
        'name',
        'file_name',
        'description',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function routes(): HasMany
    {
        return $this->hasMany(Route::class, 'routefile_id');
    }
}
