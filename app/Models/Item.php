<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'room_id',
        'location',
        'unit',
        'stock',
        'minimum_stock',
        'description',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
