<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Race extends Model
{
    protected $fillable = [
        'name',
        'min_role',
        'stat_points_total',
        'base_hp',
        'base_strength',
        'base_magic',
        'base_defense',
        'base_speed',
        'lore',
        'caps_json',
        'bonuses_json',
        'sprite',
    ];

    protected $casts = [
        'caps_json' => 'array',
        'bonuses_json' => 'array',
    ];

    public function getSpriteUrlAttribute(): string
    {
        $fallback = asset('assets/sprites/razas/human.png');

        $pathsToTry = [];
        $sprite = $this->sprite;

        if (!empty($sprite)) {
            if (str_contains($sprite, '/') || str_contains($sprite, '\\')) {
                $normalized = str_replace('\\', '/', $sprite);
                $normalized = ltrim($normalized, '/');
                if (!Str::startsWith($normalized, 'assets/')) {
                    $normalized = 'assets/' . $normalized;
                }
                $pathsToTry[] = $normalized;
            } else {
                $pathsToTry[] = 'assets/sprites/razas/' . $sprite;
            }
        }

        $nameMap = [
            'Humanos' => 'human.png',
            'Enanos' => 'dwarf.png',
            'Altos Elfos' => 'elfHigh.png',
            'Elfos Silvanos' => 'elfSilvan.png',
            'Elfos Oscuros' => 'elfDark.png',
            'Orcos' => 'orc.png',
            'Skaven' => 'skaven.png',
            'Hombres Bestia' => 'humanBeast.png',
            'Condes Vampiro' => 'countVampire.png',
            'Reyes Funerarios' => 'squeleton.png',
            'Hombres Lagarto' => 'humanLizard.png',
            'Enanos del Caos' => 'dwarfChaos.png',
            'Demonios del Caos' => 'demonChaos.png',
            'Señor Legendario del Caos' => 'Aldrik.png',
        ];

        if (!empty($this->name) && isset($nameMap[$this->name])) {
            $pathsToTry[] = 'assets/sprites/razas/' . $nameMap[$this->name];
        }

        if (!empty($this->name)) {
            $pathsToTry[] = 'assets/sprites/razas/' . Str::slug($this->name) . '.png';
        }

        foreach ($pathsToTry as $path) {
            if (file_exists(public_path($path))) {
                return asset($path);
            }
        }

        return $fallback;
    }
}
