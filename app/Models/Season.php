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

    // Helper: dado year/month devuelve el season anterior [year, month]
    public static function previousSeasonFrom(int $year, int $month): array
    {
        if ($month === 1) {
            return ['year' => $year - 1, 'month' => 12];
        }
        return ['year' => $year, 'month' => $month - 1];
    }

    // Helper: devuelve [year, month] del mes anterior respecto a now()
    public static function previousSeasonFromNow(): array
    {
        $now = now();
        $py = (int) $now->copy()->subMonth()->format('Y');
        $pm = (int) $now->copy()->subMonth()->format('n');
        return ['year' => $py, 'month' => $pm];
    }
}
