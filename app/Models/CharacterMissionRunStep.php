<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterMissionRunStep extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'effects_snapshot_json' => 'array',
    ];

    public function run(): BelongsTo
    {
        return $this->belongsTo(CharacterMissionRun::class, 'run_id');
    }

    public function node(): BelongsTo
    {
        return $this->belongsTo(MissionNode::class, 'node_id');
    }

    public function choice(): BelongsTo
    {
        return $this->belongsTo(MissionChoice::class, 'choice_id');
    }
}
