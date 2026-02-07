<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BattleRoomStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BattleRoom extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'status' => BattleRoomStatus::class,
        'closed_at' => 'datetime',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guest_user_id');
    }

    public function battle(): BelongsTo
    {
        return $this->belongsTo(Battle::class, 'battle_id');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            BattleRoomStatus::Open,
            BattleRoomStatus::InProgress,
            BattleRoomStatus::Finished,
        ]);
    }
}
