<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
    /**
     * Devuelve los stats efectivos del personaje, considerando la montura especial "max stats" si está equipada.
     * Si la montura equipada tiene bonuses_json['mode'] == 'max_stats', devuelve los caps de la raza.
     * Si no hay caps_json, devuelve los stats actuales (sin romper).
     */
    public function effectiveStats(): array
    {
        $stats = $this->stats_json ?? [];
        $race = $this->race;

        // Buscar si tiene equipada la montura especial (por code o marca JSON)
        $monturaEspecial = false;
        $mount = null;
        // Si hay sistema de equipamiento, buscar en equipment
        if (method_exists($this, 'equipment')) {
            $mountEquip = $this->equipment()->where('slot', 'mount')->with('item')->first();
            if ($mountEquip && $mountEquip->item) {
                $item = $mountEquip->item;
                $bonuses = $item->bonuses_json ?? [];
                if (($item->code ?? null) === 'mount_legendario_caos' || ($bonuses['mode'] ?? null) === 'max_stats') {
                    $monturaEspecial = true;
                }
            }
        }
        // Si no, buscar por mount_id (legacy)
        if (!$monturaEspecial && $this->mount) {
            $bonuses = $this->mount->bonuses_json ?? [];
            if (($this->mount->code ?? null) === 'mount_legendario_caos' || ($bonuses['mode'] ?? null) === 'max_stats') {
                $monturaEspecial = true;
            }
        }

        if ($monturaEspecial && $race) {
            $caps = $race->caps_json ?? null;
            if (is_array($caps) && count($caps) > 0) {
                // Usar caps de la raza
                return [
                    'fuerza' => (int)($caps['fuerza'] ?? $caps['strength'] ?? $race->base_strength ?? 0),
                    'magia' => (int)($caps['magia'] ?? $caps['magic'] ?? $race->base_magic ?? 0),
                    'defensa' => (int)($caps['defensa'] ?? $caps['defense'] ?? $race->base_defense ?? 0),
                    'velocidad' => (int)($caps['velocidad'] ?? $caps['speed'] ?? $race->base_speed ?? 0),
                ];
            } else {
                // Fallback: stats actuales, pero no romper
                return [
                    'fuerza' => (int)($stats['fuerza'] ?? $race->base_strength ?? 0),
                    'magia' => (int)($stats['magia'] ?? $race->base_magic ?? 0),
                    'defensa' => (int)($stats['defensa'] ?? $race->base_defense ?? 0),
                    'velocidad' => (int)($stats['velocidad'] ?? $race->base_speed ?? 0),
                ];
            }
        }
        // Si no hay montura especial, devolver stats normales
        return [
            'fuerza' => (int)($stats['fuerza'] ?? $race->base_strength ?? 0),
            'magia' => (int)($stats['magia'] ?? $race->base_magic ?? 0),
            'defensa' => (int)($stats['defensa'] ?? $race->base_defense ?? 0),
            'velocidad' => (int)($stats['velocidad'] ?? $race->base_speed ?? 0),
        ];
    }
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
