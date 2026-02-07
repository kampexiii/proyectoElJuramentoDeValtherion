<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissionReward extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'items_json' => 'array',
    ];

    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }
}
