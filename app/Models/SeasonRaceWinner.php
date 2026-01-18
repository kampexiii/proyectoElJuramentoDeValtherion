<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeasonRaceWinner extends Model
{
    protected $fillable = [
        'season_id',
        'race_id',
        'winner_character_id',
        'chest_id',
        'granted_at',
        'claimed_at',
    ];

    protected $casts = [
        'granted_at' => 'datetime',
        'claimed_at' => 'datetime',
    ];

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function race(): BelongsTo
    {
        return $this->belongsTo(Race::class);
    }

    public function winnerCharacter(): BelongsTo
    {
        return $this->belongsTo(Character::class, 'winner_character_id');
    }

    public function chest(): BelongsTo
    {
        return $this->belongsTo(Chest::class);
    }
}
