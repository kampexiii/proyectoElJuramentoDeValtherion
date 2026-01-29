<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardCode extends Model
{
    protected $fillable = [
        'code',
        'type',
        'payload_json',
        'uses_max',
        'uses_count',
        'used_by_user_id',
        'used_at',
        'is_active',
    ];

    protected $casts = [
        'payload_json' => 'array',
        'uses_max' => 'integer',
        'uses_count' => 'integer',
        'used_at' => 'datetime',
        'is_active' => 'boolean',
    ];
}
