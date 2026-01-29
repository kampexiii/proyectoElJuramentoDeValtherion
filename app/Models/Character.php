<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    protected $fillable = [
        'user_id',
        'race_id',
        'name',
        'stats_json',
        'has_mount',
    ];

    protected $casts = [
        'stats_json' => 'array',
        'has_mount' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function race()
    {
        return $this->belongsTo(Race::class);
    }
}
