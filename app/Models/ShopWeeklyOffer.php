<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopWeeklyOffer extends Model
{
    protected $fillable = [
        'week_year',
        'week_number',
        'category',
        'position',
        'item_id',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
