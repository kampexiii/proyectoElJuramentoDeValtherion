<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CharacterPotionEffect extends Model
{
    protected $fillable = [
        'character_id',
        'effect_type',
        'stat',
        'bonus',
        'remaining_missions',
    ];

    public function character()
    {
        return $this->belongsTo(Character::class);
    }
}
