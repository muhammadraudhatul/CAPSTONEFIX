<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    private $days = [
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
    ];

    private $timeSlots = [
        '07:30 - 08:30',
        '08:30 - 09:30',
        '09:30 - 10:30',
        '10:30 - 11:30',
        '11:30 - 12:30',
        '13:30 - 14:30',
        '14:30 - 15:30',
        '15:30 - 16:30',
    ];

    public function index(Request $request)
    {
        $weeks = [];

        $start = Carbon::now()->startOfWeek(Carbon::MONDAY);

        for ($i = 0; $i < 12; $i++) {
            $weekStart = $start->copy()->addWeeks($i);
            $weekEnd = $weekStart->copy()->addDays(6);

            $weeks[] = [
                'value' => $weekStart->format('Y-m-d'),
                'label' =>
                    $weekStart->format('d M') .
                    ' - ' .
                    $weekEnd->format('d M'),
            ];
        }

        $selectedWeek = $request->week ?? $weeks[0]['value'];

        $rooms = Room::query()
            ->orderBy('name')
            ->get();

        $schedules = RoomSchedule::query()
            ->where('week_start', $selectedWeek)
            ->whereIn('room_id', $rooms->pluck('id'))
            ->get();

        $scheduleMap = $schedules->keyBy(function ($schedule) {
            return $schedule->room_id . '|' . $schedule->day . '|' . $schedule->time_slot;
        });

        return view('admin.rooms.index', [
            'rooms' => $rooms,
            'days' => $this->days,
            'timeSlots' => $this->timeSlots,
            'weeks' => $weeks,
            'selectedWeek' => $selectedWeek,
            'scheduleMap' => $scheduleMap,
        ]);
    }

    public function create()
    {
        return view('admin.rooms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'capacity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($validated) {
            $room = Room::create([
                'name' => $validated['name'],
                'capacity' => $validated['capacity'],
            ]);

            $start = Carbon::now()->startOfWeek(Carbon::MONDAY);
            $now = now();

            $rows = [];

            for ($week = 0; $week < 12; $week++) {
                $weekStart = $start->copy()->addWeeks($week);
                $weekStartStr = $weekStart->format('Y-m-d');

                foreach ($this->days as $day) {
                    foreach ($this->timeSlots as $slot) {
                        $rows[] = [
                            'room_id' => $room->id,
                            'week_start' => $weekStartStr,
                            'day' => $day,
                            'time_slot' => $slot,
                            'available' => true,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            }

            RoomSchedule::insert($rows);
        });

        return redirect()
            ->route('rooms.index')
            ->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function edit(Room $room)
    {
        return view('admin.rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'name' => 'required',
            'capacity' => 'required',
        ]);

        $room->update([
            'name' => $request->name,
            'capacity' => $request->capacity,
        ]);

        return redirect()
            ->route('rooms.index');
    }

    public function destroy(Room $room)
    {
        $room->delete();

        return back();
    }

    public function toggleSchedule(RoomSchedule $schedule)
    {
        $schedule->update([
        'available' => !$schedule->available
        ]);

    return response()->json([
        'available' => $schedule->fresh()->available
        ]);
    }
}