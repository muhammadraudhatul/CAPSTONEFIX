<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomSchedule extends Model
{
    protected $fillable = [
        'room_id',
        'week_start',
        'day',
        'time_slot',
        'available',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
