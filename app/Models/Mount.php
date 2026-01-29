<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mount extends Model
{
    protected $fillable = [
        'name',
        'bonus_strength',
        'bonus_magic',
        'bonus_defense',
        'bonus_speed',
        'is_admin_fixed',
    ];

    protected $casts = [
        'is_admin_fixed' => 'boolean',
    ];
}
