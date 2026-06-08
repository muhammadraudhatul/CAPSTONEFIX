<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\BorrowingItem;
use App\Models\Item;
use App\Models\ItemHistory;
use App\Models\Room;
use App\Models\RoomSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BorrowingController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    private function getDayName($date)
    {
        $dayMap = [

            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',

        ];

        return $dayMap[
            Carbon::parse($date)
                ->format('l')
        ] ?? null;
    }

    private function authorizeBorrowing(
        Borrowing $borrowing
    ) {
        if (
            $borrowing->user_id != auth()->id()
        ) {
            abort(403);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $rooms = Room::all();

        $items = Item::orderBy('name')->get();

        return view(
            'student.borrowings.create',
            compact('rooms', 'items')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | AVAILABLE SCHEDULES
    |--------------------------------------------------------------------------
    */

    public function availableSchedules(Request $request)
    {
        $date = Carbon::parse(
            $request->date
        );

        $weekStart = $date
            ->copy()
            ->startOfWeek(Carbon::MONDAY)
            ->format('Y-m-d');

        $day = $this->getDayName(
            $request->date
        );

        // Hari Sabtu/Minggu
        if (!$day) {
            return response()->json([
                'message' => 'Peminjaman hanya tersedia pada hari Senin–Jumat'
            ]);
        }

        $schedules = RoomSchedule::where(
                'room_id',
                $request->room_id
            )
            ->where(
                'week_start',
                $weekStart
            )
            ->where(
                'day',
                $day
            )
            ->where(
                'available',
                true
            )
            ->get([
                'id',
                'time_slot'
            ]);

        return response()->json(
            $schedules
        );
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([

            'borrow_date' => 'required|date',
            'room_id' => 'required',
            'time_slot' => 'required',
            'purpose' => 'required',
            'total_people' => 'required|integer',
            'with_lecturer' => 'required',
            'lecturer_name' => 'nullable',
            'items' => 'required|array',

        ]);

        $day = $this->getDayName(
            $request->borrow_date
        );

        /*
        |--------------------------------------------------------------------------
        | VALIDATE ROOM
        |--------------------------------------------------------------------------
        */

        $date = Carbon::parse(
            $request->borrow_date
        );

        $weekStart = $date
            ->copy()
            ->startOfWeek(Carbon::MONDAY)
            ->format('Y-m-d');

        $schedule = RoomSchedule::where(
                'room_id',
                $request->room_id
            )
            ->where(
                'week_start',
                $weekStart
            )
            ->where(
                'day',
                $day
            )
            ->where(
                'time_slot',
                $request->time_slot
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

                    'schedule' =>
                        'Jadwal sudah tidak tersedia.'

                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDATE RESERVED STOCK
        |--------------------------------------------------------------------------
        */

        foreach ($request->items as $item)
        {
            if (
                !empty($item['item_id']) &&
                !empty($item['qty'])
            ) {

                $itemModel = Item::find(
                    $item['item_id']
                );

                $reservedQty =
                    $itemModel->getReservedQty(

                        $request->borrow_date,

                        $request->time_slot

                    );

                $availableStock =
                    $itemModel->stock -
                    $reservedQty;

                if (
                    $item['qty'] >
                    $availableStock
                ) {
                    return back()
                        ->withErrors([

                            'stock' =>
                                'Stock item tidak cukup.'

                        ]);
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $request,
            $day
        ) {

            $borrowing = Borrowing::create([

                'user_id' => auth()->id(),

                'room_id' =>
                    $request->room_id,

                'borrow_date' =>
                    $request->borrow_date,

                'day' =>
                    $day,

                'time_slot' =>
                    $request->time_slot,

                'purpose' =>
                    $request->purpose,

                'total_people' =>
                    $request->total_people,

                'with_lecturer' =>
                    $request->with_lecturer,

                'lecturer_name' =>
                    $request->lecturer_name,

                'status' => 'PENDING',

            ]);

            foreach ($request->items as $item)
            {
                if (
                    !empty($item['item_id']) &&
                    !empty($item['qty'])
                ) {

                    BorrowingItem::create([

                        'borrowing_id' =>
                            $borrowing->id,

                        'item_id' =>
                            $item['item_id'],

                        'qty' =>
                            $item['qty'],

                    ]);
                }
            }

        });

        return redirect()
            ->route('student.dashboard');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(Borrowing $borrowing)
    {
        $this->authorizeBorrowing(
            $borrowing
        );

        if (
            $borrowing->status != 'PENDING'
        ) {
            return back();
        }

        $rooms = Room::all();

        $items = Item::orderBy('name')->get();

        $borrowing->load([
            'items.item'
        ]);

        return view(
            'student.borrowings.edit',
            compact(
                'borrowing',
                'rooms',
                'items'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Borrowing $borrowing
    ) {
        $this->authorizeBorrowing(
            $borrowing
        );

        if (
            $borrowing->status != 'PENDING'
        ) {
            return back();
        }

        $request->validate([

            'borrow_date' => 'required|date',
            'room_id' => 'required',
            'time_slot' => 'required',
            'purpose' => 'required',
            'total_people' => 'required|integer',
            'with_lecturer' => 'required',
            'items' => 'required|array',

        ]);

        $day = $this->getDayName(
            $request->borrow_date
        );

        /*
        |--------------------------------------------------------------------------
        | VALIDATE ROOM
        |--------------------------------------------------------------------------
        */

        $date = Carbon::parse(
            $request->borrow_date
        );

        $weekStart = $date
            ->copy()
            ->startOfWeek(Carbon::MONDAY)
            ->format('Y-m-d');

        $schedule = RoomSchedule::where(
                'room_id',
                $request->room_id
            )
            ->where(
                'week_start',
                $weekStart
            )
            ->where(
                'day',
                $day
            )
            ->where(
                'time_slot',
                $request->time_slot
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

                    'schedule' =>
                        'Jadwal sudah tidak tersedia.'

                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDATE RESERVED STOCK
        |--------------------------------------------------------------------------
        */

        foreach ($request->items as $item)
        {
            if (
                !empty($item['item_id']) &&
                !empty($item['qty'])
            ) {

                $itemModel = Item::find(
                    $item['item_id']
                );

                $reservedQty =
                    $itemModel->getReservedQty(

                        $request->borrow_date,

                        $request->time_slot,

                        $borrowing->id

                    );

                $availableStock =
                    $itemModel->stock -
                    $reservedQty;

                if (
                    $item['qty'] >
                    $availableStock
                ) {
                    return back()
                        ->withErrors([

                            'stock' =>
                                'Stock item tidak cukup.'

                        ]);
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $request,
            $borrowing,
            $day
        ) {

            $borrowing->update([

                'room_id' =>
                    $request->room_id,

                'borrow_date' =>
                    $request->borrow_date,

                'day' =>
                    $day,

                'time_slot' =>
                    $request->time_slot,

                'purpose' =>
                    $request->purpose,

                'total_people' =>
                    $request->total_people,

                'with_lecturer' =>
                    $request->with_lecturer,

                'lecturer_name' =>
                    $request->lecturer_name,

            ]);

            $borrowing->items()->delete();

            foreach ($request->items as $item)
            {
                if (
                    !empty($item['item_id']) &&
                    !empty($item['qty'])
                ) {

                    BorrowingItem::create([

                        'borrowing_id' =>
                            $borrowing->id,

                        'item_id' =>
                            $item['item_id'],

                        'qty' =>
                            $item['qty'],

                    ]);
                }
            }

        });

        return redirect()
            ->route('student.dashboard');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(Borrowing $borrowing)
    {
        $this->authorizeBorrowing(
            $borrowing
        );

        if (
            $borrowing->status != 'PENDING'
        ) {
            return back();
        }

        $borrowing->delete();

        return back();
    }

    /*
    |--------------------------------------------------------------------------
    | CANCEL
    |--------------------------------------------------------------------------
    */

    public function cancel(Borrowing $borrowing)
    {
        $this->authorizeBorrowing(
            $borrowing
        );

        if (
            $borrowing->status != 'APPROVED'
        ) {
            return back();
        }

        DB::transaction(function () use (
            $borrowing
        ) {

            foreach (
                $borrowing->items
                as $borrowedItem
            ) {

                ItemHistory::create([

                    'item_id' =>
                        $borrowedItem->item->id,

                    'user_id' =>
                        auth()->id(),

                    'action' =>
                        'cancel',

                    'item_name' =>
                        $borrowedItem->item->name,

                    'description' =>
                        'Peminjaman dibatalkan mahasiswa',

                ]);
            }

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

            $borrowing->update([

                'status' => 'CANCELLED',

            ]);

        });

        return back();
    }

    /*
    |--------------------------------------------------------------------------
    | RETURN FORM
    |--------------------------------------------------------------------------
    */

    public function returnForm(Borrowing $borrowing)
    {
        $this->authorizeBorrowing(
            $borrowing
        );

        $borrowing->load([
            'items.item'
        ]);

        return view(
            'student.borrowings.return',
            compact('borrowing')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SUBMIT RETURN
    |--------------------------------------------------------------------------
    */

    public function submitReturn(Request $request, Borrowing $borrowing)
    {
        $this->authorizeBorrowing($borrowing);

        DB::transaction(function () use ($request, $borrowing) {

            foreach ($borrowing->items as $borrowedItem) {

                $returnedQty = $request->returned_qty[$borrowedItem->id] ?? 0;

                if ($returnedQty > $borrowedItem->qty) {
                    $returnedQty = $borrowedItem->qty;
                }

                $borrowedItem->update([
                    'returned_qty' => $returnedQty,
                ]);

                // Catat history pengembalian
                ItemHistory::create([
                    'item_id'     => $borrowedItem->item->id,
                    'user_id'     => auth()->id(), // student yang return
                    'action'      => 'return',
                    'item_name'   => $borrowedItem->item->name,
                    'description' => 'Mahasiswa mengajukan pengembalian item',
                ]);
            }

            $borrowing->update(['status' => 'WAITING_RETURN']);
        });

        return redirect()->route('student.dashboard');
    }
}