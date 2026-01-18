<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Animal extends Model
{
    protected $fillable = [
        'name',
        'rarity_id',
        'required_level',
        'mountable',
        'hp',
        'strength',
        'magic',
        'defense',
        'speed',
        'description',
    ];

    protected $casts = [
        'mountable' => 'boolean',
    ];

    public function rarity(): BelongsTo
    {
        return $this->belongsTo(Rarity::class);
    }
}
