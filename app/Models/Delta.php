<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Delta extends Model
{
    protected $fillable = [
        'name',
        'project_id',
        'next',
        'definition',
        'description',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function rules(): HasMany
    {
        return $this->hasMany(Rule::class);
    }
}
