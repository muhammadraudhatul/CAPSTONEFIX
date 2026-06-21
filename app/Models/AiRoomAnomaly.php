<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiRoomAnomaly extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}