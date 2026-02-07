<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    /**
     * Devuelve los stats efectivos del personaje usando el servicio centralizado.
     */
    public function effectiveStats(): array
    {
        return app(\App\Services\StatCalculatorService::class)->effectiveStatsFor($this);
    }
    protected $fillable = [
        'user_id',
        'race_id',
        'mount_id',
        'name',
        'sprite_path',
        'stats_json',
        'has_mount',
        'hp_max',
        'hp_current',
        'level',
    ];

    protected $casts = [
        'stats_json' => 'array',
        'has_mount' => 'boolean',
        'level' => 'integer',
    ];

    public function getSpriteUrlAttribute(): string
    {
        if (!empty($this->sprite_path)) {
            return $this->sprite_path;
        }

        $raceSprite = $this->race?->sprite_path;
        if (!empty($raceSprite)) {
            return $raceSprite;
        }

        return '/assets/characters/placeholder.png';
    }

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
