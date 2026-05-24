<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Item;
use App\Models\ItemHistory;
use App\Models\RoomSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BorrowingController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

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
        /*
        |--------------------------------------------------------------------------
        | ONLY PENDING
        |--------------------------------------------------------------------------
        */

        if (
            $borrowing->status != 'PENDING'
        ) {
            return back();
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDATE ROOM
        |--------------------------------------------------------------------------
        */

        $date = Carbon::parse(
            $borrowing->borrow_date
        );

        $weekStart = $date
            ->copy()
            ->startOfWeek(Carbon::MONDAY)
            ->format('Y-m-d');

        $schedule = RoomSchedule::where(
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
            ->where(
                'available',
                true
            )
            ->first();

        if (!$schedule)
        {
            return back()
                ->withErrors([

                    'room' =>
                        'Ruangan sudah tidak tersedia.'

                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $borrowing,
            $weekStart
        ) {

            /*
            |--------------------------------------------------------------------------
            | REDUCE STOCK
            |--------------------------------------------------------------------------
            */

            foreach (
                $borrowing->items as $borrowedItem
            ) {

                $item =
                    $borrowedItem->item;

                /*
                |--------------------------------------------------------------------------
                | VALIDATE STOCK
                |--------------------------------------------------------------------------
                */

                if (
                    $borrowedItem->qty >
                    $item->stock
                ) {
                    abort(
                        400,
                        'Stock item tidak cukup.'
                    );
                }

                $oldStock =
                    $item->stock;

                /*
                |--------------------------------------------------------------------------
                | REDUCE STOCK
                |--------------------------------------------------------------------------
                */

                Item::withoutEvents(function () use (
                    $item,
                    $borrowedItem
                ) {

                    $item->decrement(

                        'stock',

                        $borrowedItem->qty

                    );

                });

                $item->refresh();

                /*
                |--------------------------------------------------------------------------
                | HISTORY
                |--------------------------------------------------------------------------
                */

                ItemHistory::create([

                    'item_id' =>
                        $item->id,

                    'user_id' =>
                        auth()->id(),

                    'action' =>
                        'borrow',

                    'item_name' =>
                        $item->name,

                    'old_stock' =>
                        $oldStock,

                    'new_stock' =>
                        $item->stock,

                    'description' =>
                        'Dipinjam mahasiswa',

                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | LOCK ROOM
            |--------------------------------------------------------------------------
            */

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
        });

        return back();
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT
    |--------------------------------------------------------------------------
    */

    public function reject(Borrowing $borrowing)
    {
        /*
        |--------------------------------------------------------------------------
        | ONLY PENDING
        |--------------------------------------------------------------------------
        */

        if (
            $borrowing->status != 'PENDING'
        ) {
            return back();
        }

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
        /*
        |--------------------------------------------------------------------------
        | ONLY WAITING RETURN
        |--------------------------------------------------------------------------
        */

        if (
            $borrowing->status != 'WAITING_RETURN'
        ) {
            return back();
        }

        /*
        |--------------------------------------------------------------------------
        | DATE
        |--------------------------------------------------------------------------
        */

        $date = Carbon::parse(
            $borrowing->borrow_date
        );

        $weekStart = $date
            ->copy()
            ->startOfWeek(Carbon::MONDAY)
            ->format('Y-m-d');

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $borrowing,
            $weekStart
        ) {

            /*
            |--------------------------------------------------------------------------
            | RETURN STOCK
            |--------------------------------------------------------------------------
            */

            foreach (
                $borrowing->items as $borrowedItem
            ) {

                $item =
                    $borrowedItem->item;

                $oldStock =
                    $item->stock;

                /*
                |--------------------------------------------------------------------------
                | RETURN STOCK
                |--------------------------------------------------------------------------
                */

                Item::withoutEvents(function () use (
                    $item,
                    $borrowedItem
                ) {

                    $item->increment(

                        'stock',

                        $borrowedItem->returned_qty

                    );

                });

                $item->refresh();

                /*
                |--------------------------------------------------------------------------
                | HISTORY
                |--------------------------------------------------------------------------
                */

                ItemHistory::create([

                    'item_id' =>
                        $item->id,

                    'user_id' =>
                        auth()->id(),

                    'action' =>
                        'return',

                    'item_name' =>
                        $item->name,

                    'old_stock' =>
                        $oldStock,

                    'new_stock' =>
                        $item->stock,

                    'description' =>
                        'Pengembalian alat/bahan',

                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | UNLOCK ROOM
            |--------------------------------------------------------------------------
            */

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
        });

        return back();
    }
}