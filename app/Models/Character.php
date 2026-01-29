<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    protected $fillable = [
        'user_id',
        'race_id',
        'mount_id',
        'name',
        'stats_json',
        'has_mount',
        'hp_max',
        'hp_current',
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

    public function mount()
    {
        return $this->belongsTo(Mount::class);
    }

    public function equipment()
    {
        return $this->hasMany(CharacterEquipment::class);
    }

    public function inventory()
    {
        return $this->hasMany(CharacterItem::class);
    }
}
