<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MissionNode extends Model
{
    protected $guarded = ['id'];

    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }

    public function choices(): HasMany
    {
        return $this->hasMany(MissionChoice::class);
    }
}
