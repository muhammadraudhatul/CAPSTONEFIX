<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
     protected $fillable = [
        'name',
        'capacity',
    ];

    public function schedules()
    {
        return $this->hasMany(RoomSchedule::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
