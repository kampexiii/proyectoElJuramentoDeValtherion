<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MissionRunStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CharacterMissionRun extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'status' => MissionRunStatus::class,
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }

    public function currentNode(): BelongsTo
    {
        return $this->belongsTo(MissionNode::class, 'current_node_id');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(CharacterMissionRunStep::class, 'run_id');
    }

    public function scopeActiveOrBossPending($query)
    {
        return $query->whereIn('status', [
            MissionRunStatus::Active,
            MissionRunStatus::BossPending,
        ]);
    }
}
