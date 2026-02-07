<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinalBoss extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'base_stats_json' => 'array',
    ];

    public function missions(): HasMany
    {
        return $this->hasMany(Mission::class);
    }
}
