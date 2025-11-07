<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Route extends Model
{
    protected $fillable = [
        'routefile_id',
        'from_service_id',
        'to_service_id',
        'match_id',
        'rule_id',
        'chainclass',
        'type',
        'priority',
    ];

    public function routeFile(): BelongsTo
    {
        return $this->belongsTo(RouteFile::class, 'routefile_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'from_service_id');
    }

    public function fromService(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'from_service_id');
    }

    public function toService(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'to_service_id');
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(RouteMatch::class, 'match_id');
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(Rule::class);
    }
}
