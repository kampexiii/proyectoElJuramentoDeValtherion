<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CharacterItem extends Model
{
    protected $table = 'character_items';

    protected $fillable = [
        'character_id',
        'item_id',
        'quantity',
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
