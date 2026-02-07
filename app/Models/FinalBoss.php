<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinalBoss extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'lore',
        'base_stats_json',
        'sprite_path',
    ];

    protected $casts = [
        'base_stats_json' => 'array',
    ];

    public function missions(): HasMany
    {
        return $this->hasMany(Mission::class);
    }

    public function getSpriteUrlAttribute(): string
    {
        $path = (string) ($this->sprite_path ?? '');

        return $path !== '' ? $path : '/assets/bosses/placeholder.png';
    }
}
