<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BattleStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Battle extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'status' => BattleStatus::class,
        'p1_defending' => 'boolean',
        'p2_defending' => 'boolean',
        'stats_p1_json' => 'array',
        'stats_p2_json' => 'array',
        'finished_at' => 'datetime',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(BattleRoom::class, 'room_id');
    }

    public function player1Character(): BelongsTo
    {
        return $this->belongsTo(Character::class, 'player1_character_id');
    }

    public function player2Character(): BelongsTo
    {
        return $this->belongsTo(Character::class, 'player2_character_id');
    }

    public function finalBoss(): BelongsTo
    {
        return $this->belongsTo(FinalBoss::class, 'final_boss_id');
    }

    public function missionRun(): BelongsTo
    {
        return $this->belongsTo(CharacterMissionRun::class, 'mission_run_id');
    }

    public function turns(): HasMany
    {
        return $this->hasMany(BattleTurn::class);
    }
}
