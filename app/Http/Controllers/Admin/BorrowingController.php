<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\ItemHistory;
use App\Models\RoomSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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
                ->with(

                    'error_borrowing_id',
                    $borrowing->id

                )->with(

                    'error_message',
                    'Ruangan sudah tidak tersedia.'

                );
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDATE STOCK RESERVATION
        |--------------------------------------------------------------------------
        */

        foreach (
            $borrowing->items as $borrowedItem
        ) {

            $item =
                $borrowedItem->item;

            $reservedQty =
                $item->getReservedQty(

                    $borrowing->borrow_date,

                    $borrowing->time_slot,

                    $borrowing->id

                );

            $availableStock =
                $item->stock -
                $reservedQty;

            if (
                $borrowedItem->qty >
                $availableStock
            ) {
                return back()
                    ->with(

                        'error_borrowing_id',
                        $borrowing->id

                    )->with(

                        'error_message',
                        'Stock item tidak cukup.'

                    );
            }
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
            | HISTORY RESERVATION
            |--------------------------------------------------------------------------
            */

            foreach (
                $borrowing->items as $borrowedItem
            ) {

                ItemHistory::create([

                    'item_id' =>
                        $borrowedItem->item->id,

                    'user_id' =>
                        $borrowing->user_id, // ← user peminjam, bukan admin

                    'action' =>
                        'borrow',

                    'item_name' =>
                        $borrowedItem->item->name,

                    'description' =>
                        'Item direservasi untuk peminjaman',

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
            | APPROVED
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
        if (
            $borrowing->status != 'WAITING_RETURN'
        ) {
            return back();
        }

        $date = Carbon::parse(
            $borrowing->borrow_date
        );

        $weekStart = $date
            ->copy()
            ->startOfWeek(Carbon::MONDAY)
            ->format('Y-m-d');

        DB::transaction(function () use (
            $borrowing,
            $weekStart
        ) {

            /*
            |--------------------------------------------------------------------------
            | FINAL STOCK REDUCTION
            |--------------------------------------------------------------------------
            */

            foreach (
                $borrowing->items as $borrowedItem
            ) {

                $item =
                    $borrowedItem->item;

                $usedQty =
                    $borrowedItem->qty -
                    $borrowedItem->returned_qty;

                /*
                |--------------------------------------------------------------------------
                | HISTORY - ITEM RETURNED
                |--------------------------------------------------------------------------
                */

                if ($borrowedItem->returned_qty > 0)
                {
                    ItemHistory::create([

                        'item_id' =>
                            $item->id,

                        'user_id' =>
                            $borrowing->user_id, // ← user peminjam

                        'action' =>
                            'return',

                        'item_name' =>
                            $item->name,

                        'description' =>
                            'Item berhasil dikembalikan',

                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | HISTORY - STOCK USED / LOST
                |--------------------------------------------------------------------------
                */

                if ($usedQty > 0)
                {
                    $oldStock =
                        $item->stock;

                    $item->decrement(
                        'stock',
                        $usedQty
                    );

                    $item->refresh();

                    ItemHistory::create([

                        'item_id' =>
                            $item->id,

                        'user_id' =>
                            $borrowing->user_id, // ← user peminjam

                        'action' =>
                            'stock_used',

                        'item_name' =>
                            $item->name,

                        'old_stock' =>
                            $oldStock,

                        'new_stock' =>
                            $item->stock,

                        'description' =>
                            'Stock berkurang setelah peminjaman selesai',

                    ]);
                }
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

    /*
    |--------------------------------------------------------------------------
    | ADMIN CANCEL
    |--------------------------------------------------------------------------
    */

    public function adminCancel(
        Request $request,
        Borrowing $borrowing
    ) {

        /*
        |--------------------------------------------------------------------------
        | ONLY ACTIVE
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                $borrowing->status,
                [

                    'APPROVED',
                    'WAITING_RETURN',

                ]
            )
        ) {
            return back();
        }

        $request->validate([

            'cancel_reason' =>
                'required|string|max:1000',

        ]);

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

        DB::transaction(function () use (
            $borrowing,
            $weekStart,
            $request
        ) {

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
            | CANCEL
            |--------------------------------------------------------------------------
            */

            $borrowing->update([

                'status' => 'CANCELLED',

                'cancelled_by' =>
                    auth()->id(),

                'cancel_reason' =>
                    $request->cancel_reason,

            ]);
        });

        return back();
    }
}