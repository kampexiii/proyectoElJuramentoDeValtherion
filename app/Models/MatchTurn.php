<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchTurn extends Model
{
    protected $fillable = [
        'match_id',
        'turn_number',
        'acting_participant_id',
        'action_text',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchModel::class, 'match_id');
    }

    public function actingParticipant(): BelongsTo
    {
        return $this->belongsTo(MatchParticipant::class, 'acting_participant_id');
    }
}
