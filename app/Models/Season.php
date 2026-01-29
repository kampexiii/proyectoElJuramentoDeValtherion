<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Season extends Model
{
    protected $fillable = [
        'year',
        'month',
        'status',
        'starts_at',
        'ends_at',
        'closed_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function raceRankings(): HasMany
    {
        return $this->hasMany(SeasonRaceRanking::class);
    }

    public function winner(): HasMany
    {
        return $this->hasMany(SeasonRaceWinner::class);
    }
}
