<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RacePointsEvent extends Model
{
    public const UPDATED_AT = null;

    protected $guarded = ['id'];

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function race(): BelongsTo
    {
        return $this->belongsTo(Race::class);
    }
}
