<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterEquipment extends Model
{
    protected $table = 'character_equipment';

    protected $fillable = [
        'character_id',
        'slot_id',
        'item_id',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(EquipmentSlot::class, 'slot_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
