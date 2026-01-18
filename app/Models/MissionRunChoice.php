<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissionRunChoice extends Model
{
    protected $fillable = [
        'mission_run_id',
        'node_id',
        'choice_id',
        'step_number',
    ];

    public function missionRun(): BelongsTo
    {
        return $this->belongsTo(MissionRun::class);
    }

    public function node(): BelongsTo
    {
        return $this->belongsTo(MissionNode::class);
    }

    public function choice(): BelongsTo
    {
        return $this->belongsTo(MissionChoice::class);
    }
}
