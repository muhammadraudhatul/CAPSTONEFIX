<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Item;
use App\Models\RoomSchedule;
use Carbon\Carbon;

class BorrowingController extends Controller
{
    public function index()
    {
        $borrowings = Borrowing::with([

                'user',
                'room',
                'items.item',

            ])
            ->latest()
            ->get();

        return view(
            'admin.borrowings.index',
            compact('borrowings')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE
    |--------------------------------------------------------------------------
    */

    public function approve(Borrowing $borrowing)
    {
        if ($borrowing->status != 'PENDING') {

            return back();
        }

        /*
        |--------------------------------------------------------------------------
        | REDUCE STOCK
        |--------------------------------------------------------------------------
        */

        foreach ($borrowing->items as $borrowedItem) {

            $item = $borrowedItem->item;

            $item->decrement(
                'stock',
                $borrowedItem->qty
            );
        }

        /*
        |--------------------------------------------------------------------------
        | LOCK ROOM SCHEDULE
        |--------------------------------------------------------------------------
        */

        $date = Carbon::parse(
            $borrowing->borrow_date
        );

        $weekStart = $date
            ->copy()
            ->startOfWeek(Carbon::MONDAY)
            ->format('Y-m-d');

        RoomSchedule::where(
                'room_id',
                $borrowing->room_id
            )
            ->where(
                'week_start',
                $weekStart
            )
            ->where(
                'day',
                $borrowing->day
            )
            ->where(
                'time_slot',
                $borrowing->time_slot
            )
            ->update([
                'available' => false
            ]);

        /*
        |--------------------------------------------------------------------------
        | UPDATE STATUS
        |--------------------------------------------------------------------------
        */

        $borrowing->update([

            'status' => 'APPROVED',

        ]);

        return back();
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT
    |--------------------------------------------------------------------------
    */

    public function reject(Borrowing $borrowing)
    {
        $borrowing->update([

            'status' => 'REJECTED',

        ]);

        return back();
    }

    /*
    |--------------------------------------------------------------------------
    | COMPLETE
    |--------------------------------------------------------------------------
    */

    public function complete(Borrowing $borrowing)
    {
        if (
            $borrowing->status != 'WAITING_RETURN'
        ) {
            return back();
        }

        /*
        |--------------------------------------------------------------------------
        | RETURN STOCK
        |--------------------------------------------------------------------------
        */

        foreach ($borrowing->items as $borrowedItem) {

            $item = $borrowedItem->item;

            $item->increment(
                'stock',
                $borrowedItem->qty
            );
        }

        /*
        |--------------------------------------------------------------------------
        | UNLOCK ROOM
        |--------------------------------------------------------------------------
        */

        $date = Carbon::parse(
            $borrowing->borrow_date
        );

        $weekStart = $date
            ->copy()
            ->startOfWeek(Carbon::MONDAY)
            ->format('Y-m-d');

        RoomSchedule::where(
                'room_id',
                $borrowing->room_id
            )
            ->where(
                'week_start',
                $weekStart
            )
            ->where(
                'day',
                $borrowing->day
            )
            ->where(
                'time_slot',
                $borrowing->time_slot
            )
            ->update([
                'available' => true
            ]);

        /*
        |--------------------------------------------------------------------------
        | COMPLETE
        |--------------------------------------------------------------------------
        */

        $borrowing->update([

            'status' => 'COMPLETED',

            'returned_at' => now(),

        ]);

        return back();
    }
}