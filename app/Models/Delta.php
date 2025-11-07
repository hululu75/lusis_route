<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Delta extends Model
{
    protected $fillable = [
        'name',
        'next',
        'definition',
        'description',
    ];

    public function rules(): HasMany
    {
        return $this->hasMany(Rule::class);
    }
}
