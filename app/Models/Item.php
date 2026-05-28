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

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /*
    |--------------------------------------------------------------------------
    | RESERVED QTY
    |--------------------------------------------------------------------------
    */

    public function getReservedQty(
        $borrowDate,
        $timeSlot,
        $excludeBorrowingId = null
    ) {
        return BorrowingItem::where(
                'item_id',
                $this->id
            )
            ->whereHas('borrowing', function ($query) use (
                $borrowDate,
                $timeSlot,
                $excludeBorrowingId
            ) {

                $query->where(
                        'borrow_date',
                        $borrowDate
                    )
                    ->where(
                        'time_slot',
                        $timeSlot
                    )
                    ->whereIn('status', [

                        'APPROVED',
                        'WAITING_RETURN',

                    ]);

                if ($excludeBorrowingId)
                {
                    $query->where(
                        'id',
                        '!=',
                        $excludeBorrowingId
                    );
                }

            })
            ->sum('qty');
    }
}