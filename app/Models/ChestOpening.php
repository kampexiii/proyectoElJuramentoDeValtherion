<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChestOpening extends Model
{
    protected $fillable = [
        'chest_id',
        'character_id',
        'opened_at',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
    ];

    public function chest(): BelongsTo
    {
        return $this->belongsTo(Chest::class);
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function rewards(): HasMany
    {
        return $this->hasMany(ChestOpeningReward::class);
    }
}
