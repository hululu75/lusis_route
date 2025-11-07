<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchCondition extends Model
{
    protected $fillable = [
        'match_id',
        'field',
        'operator',
        'value',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(RouteMatch::class, 'match_id');
    }
}
