<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    protected $fillable = [

        'user_id',
        'room_id',
        'borrow_date',
        'day',
        'time_slot',
        'purpose',
        'total_people',
        'with_lecturer',
        'lecturer_name',
        'status',
        'returned_at',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function items()
    {
        return $this->hasMany(BorrowingItem::class);
    }
}