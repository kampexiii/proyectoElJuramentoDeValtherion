<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BattleTurn extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'notes_json' => 'array',
    ];

    public function battle(): BelongsTo
    {
        return $this->belongsTo(Battle::class);
    }
}
