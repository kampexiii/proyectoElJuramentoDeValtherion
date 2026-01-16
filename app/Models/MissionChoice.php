<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissionChoice extends Model
{
    protected $fillable = [
        'node_id',
        'choice_text',
        'next_node_id',
        'difficulty_delta',
        'exp_multiplier',
        'points_multiplier',
        'loot_table_id',
    ];

    public function node(): BelongsTo
    {
        return $this->belongsTo(MissionNode::class, 'node_id');
    }

    public function nextNode(): BelongsTo
    {
        return $this->belongsTo(MissionNode::class, 'next_node_id');
    }

    public function lootTable(): BelongsTo
    {
        return $this->belongsTo(LootTable::class);
    }
}
