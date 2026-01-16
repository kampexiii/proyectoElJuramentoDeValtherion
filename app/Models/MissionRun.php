<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MissionRun extends Model
{
    protected $fillable = [
        'mission_id',
        'character_id',
        'season_id',
        'final_difficulty',
        'exp_gained',
        'points_gained',
        'completed',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function choices(): HasMany
    {
        return $this->hasMany(MissionRunChoice::class);
    }
}
