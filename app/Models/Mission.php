<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mission extends Model
{
    protected $fillable = [
        'title',
        'description',
        'min_level',
        'base_difficulty',
        'base_exp',
        'base_points',
    ];

    public function nodes(): HasMany
    {
        return $this->hasMany(MissionNode::class);
    }

    public function runs(): HasMany
    {
        return $this->hasMany(MissionRun::class);
    }
}
