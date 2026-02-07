<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MissionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Mission extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'status' => MissionStatus::class,
    ];

    public function finalBoss(): BelongsTo
    {
        return $this->belongsTo(FinalBoss::class);
    }

    public function nodes(): HasMany
    {
        return $this->hasMany(MissionNode::class);
    }

    public function reward(): HasOne
    {
        return $this->hasOne(MissionReward::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', MissionStatus::Published);
    }
}
