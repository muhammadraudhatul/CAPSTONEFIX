<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiItemPrediction extends Model
{
    protected $guarded = [];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}