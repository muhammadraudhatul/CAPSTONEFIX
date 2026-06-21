<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiRoomPredictionSummary extends Model
{
    protected $guarded = [];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}