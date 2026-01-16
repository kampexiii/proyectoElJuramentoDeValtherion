<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MissionNode extends Model
{
    protected $fillable = [
        'mission_id',
        'node_key',
        'text',
        'is_end',
    ];

    protected $casts = [
        'is_end' => 'boolean',
    ];

    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }

    public function choices(): HasMany
    {
        return $this->hasMany(MissionChoice::class, 'node_id');
    }
}
