<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Character extends Model
{
    protected $fillable = [
        'user_id',
        'race_id',
        'hero_id',
        'name',
        'level',
        'exp',
        'gold',
        'hp_bonus',
        'strength_bonus',
        'magic_bonus',
        'defense_bonus',
        'speed_bonus',
        'active_animal_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function race(): BelongsTo
    {
        return $this->belongsTo(Race::class);
    }

    public function hero(): BelongsTo
    {
        return $this->belongsTo(Hero::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'character_items')
            ->withPivot(['quantity'])
            ->withTimestamps();
    }

    public function equipment(): HasMany
    {
        return $this->hasMany(CharacterEquipment::class);
    }

    public function characterAnimals(): HasMany
    {
        return $this->hasMany(CharacterAnimal::class);
    }

    public function activeAnimal(): BelongsTo
    {
        return $this->belongsTo(Animal::class, 'active_animal_id');
    }

    public function missionRuns(): HasMany
    {
        return $this->hasMany(MissionRun::class);
    }

    public function matchParticipants(): HasMany
    {
        return $this->hasMany(MatchParticipant::class);
    }
}
