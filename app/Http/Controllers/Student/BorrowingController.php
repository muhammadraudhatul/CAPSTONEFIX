<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\BorrowingItem;
use App\Models\Item;
use App\Models\Room;
use App\Models\RoomSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;


class BorrowingController extends Controller
{
    public function create()
    {
        $rooms = Room::all();

        $items = Item::orderBy('name')->get();

        return view(
            'student.borrowings.create',
            compact('rooms', 'items')
        );
    }

    public function availableSchedules(Request $request)
    {
        $date = Carbon::parse(
            $request->date
        );

        $weekStart = $date
            ->copy()
            ->startOfWeek(Carbon::MONDAY)
            ->format('Y-m-d');

        $dayMap = [

            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',

        ];

        $day = $dayMap[
            $date->format('l')
        ];

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

    public function finish(Borrowing $borrowing)
    {
        if (
            $borrowing->user_id != auth()->id()
        ) {
            abort(403);
        }

        $borrowing->update([

            'status' => 'WAITING_RETURN',

        ]);

        return back();
    }

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

        $day = Carbon::parse(
            $request->borrow_date
        )->locale('id')->translatedFormat('l');

        $dayMap = [

            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',

        ];

        $day = $dayMap[
            Carbon::parse($request->borrow_date)
                ->format('l')
        ];

        $borrowing = Borrowing::create([

            'user_id' => auth()->id(),

            'room_id' => $request->room_id,

            'borrow_date' => $request->borrow_date,

            'day' => $day,

            'time_slot' => $request->time_slot,

            'purpose' => $request->purpose,

            'total_people' => $request->total_people,

            'with_lecturer' =>
                $request->with_lecturer,

            'lecturer_name' =>
                $request->lecturer_name,

            'status' => 'PENDING',

        ]);

        foreach ($request->items as $item) {

            if (
                !empty($item['item_id']) &&
                !empty($item['qty'])
            ) {

                BorrowingItem::create([

                    'borrowing_id' => $borrowing->id,

                    'item_id' => $item['item_id'],

                    'qty' => $item['qty'],

                ]);
            }
        }

        return redirect()
            ->route('student.dashboard');
    }
}