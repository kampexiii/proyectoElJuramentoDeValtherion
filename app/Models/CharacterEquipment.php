<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CharacterEquipment extends Model
{
    protected $table = 'character_equipment';

    protected $fillable = [
        'character_id',
        'slot',
        'item_id',
    ];

    public function character()
    {
        return $this->belongsTo(Character::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
