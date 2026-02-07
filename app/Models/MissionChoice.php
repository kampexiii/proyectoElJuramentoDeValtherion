<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissionChoice extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'effects_json' => 'array',
    ];

    public function node(): BelongsTo
    {
        return $this->belongsTo(MissionNode::class, 'mission_node_id');
    }

    public function nextNode(): BelongsTo
    {
        return $this->belongsTo(MissionNode::class, 'next_node_id');
    }
}
