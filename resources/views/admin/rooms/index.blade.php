@extends('admin.layouts.app')

@section('content')

<div class="flex justify-between items-center">

    <div>

        <h1 class="text-5xl font-bold text-gray-900">
            Inventory Ruangan
        </h1>

        <p class="text-gray-500 mt-3 text-lg">
            Kelola ruangan laboratorium dan jadwal ketersediaannya
        </p>

    </div>

    <a href="{{ route('rooms.create') }}"
       class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-6 py-4 rounded-2xl font-semibold shadow hover:opacity-90">

        + Tambah Ruangan

    </a>

</div>

<!-- ROOMS TABLE -->
<div class="bg-white rounded-3xl shadow-sm overflow-hidden mt-10">

    <table class="w-full">

        <thead class="bg-gray-50">

            <tr>

                <th class="p-5 text-left">
                    No
                </th>

                <th class="p-5 text-left">
                    Nama Ruangan
                </th>

                <th class="p-5 text-left">
                    Kapasitas
                </th>

                <th class="p-5 text-left">
                    Aksi
                </th>

            </tr>

        </thead>

        <tbody>

            @forelse($rooms as $room)

            <tr class="border-t">

                <td class="p-5">
                    {{ $loop->iteration }}
                </td>

                <td class="p-5 font-semibold">
                    {{ $room->name }}
                </td>

                <td class="p-5">
                    {{ $room->capacity }}
                </td>

                <td class="p-5 flex gap-4">

                    <a href="{{ route('rooms.edit', $room) }}"
                       class="text-blue-500 text-xl">
                        ✏️
                    </a>

                    <form method="POST"
                          action="{{ route('rooms.destroy', $room) }}">

                        @csrf
                        @method('DELETE')

                        <button
                            class="text-red-500 text-xl">
                            🗑️
                        </button>

                    </form>

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="4"
                    class="text-center py-10 text-gray-400">

                    Belum ada ruangan

                </td>

            </tr>

            @endforelse

        </tbody>

    </table>

</div>

<!-- SCHEDULE -->
<div class="mt-16">

    <div class="flex justify-between items-center mb-8">

        <div>

            <h2 class="text-4xl font-bold">
                Jadwal Ketersediaan Ruangan
            </h2>

            <p class="text-gray-500 mt-2">
                Atur ketersediaan ruangan per minggu
            </p>

        </div>

        <!-- WEEK DROPDOWN -->
        <form method="GET">

            <select
                name="week"
                onchange="saveOpenRoomsAndSubmit(this)"
                class="rounded-2xl border-gray-300 px-5 py-3"
            >

                @foreach($weeks as $week)

                <option
                    value="{{ $week['value'] }}"
                    {{ $selectedWeek == $week['value'] ? 'selected' : '' }}
                >

                    {{ $week['label'] }}

                </option>

                @endforeach

            </select>

        </form>

    </div>

    <!-- EACH ROOM -->
    <div class="space-y-6">

        @foreach($rooms as $room)

        <div class="bg-white rounded-3xl shadow-sm overflow-hidden">

            <!-- HEADER -->
            <button
                type="button"
                onclick="toggleRoom({{ $room->id }})"
                class="w-full flex justify-between items-center p-6 text-left"
            >

                <div class="flex items-center gap-4">

                    <div class="text-purple-600 text-2xl">
                        🏛️
                    </div>

                    <div>

                        <h3 class="text-2xl font-bold">
                            {{ $room->name }}
                        </h3>

                        <p class="text-gray-500">
                            Kapasitas: {{ $room->capacity }}
                        </p>

                    </div>

                </div>

                <div
                    id="icon-{{ $room->id }}"
                    class="text-2xl text-gray-500 transition"
                >
                    ▼
                </div>

            </button>

            <!-- CONTENT -->
            <div
                id="room-{{ $room->id }}"
                class="hidden px-6 pb-6"
            >

                <div class="overflow-auto">

                    <table class="w-full border">

                        <thead class="bg-[#eef0ff]">

                            <tr>

                                <th class="border p-4">
                                    Jam
                                </th>

                                @foreach($days as $day)

                                <th class="border p-4">
                                    {{ $day }}
                                </th>

                                @endforeach

                            </tr>

                        </thead>

                        <tbody>

                            @foreach($timeSlots as $slot)

                            <tr>

                                <td class="border p-4 font-semibold">
                                    {{ $slot }}
                                </td>

                                @foreach($days as $day)

                                @php

                                $schedule = $room->schedules
                                    ->where('week_start', $selectedWeek)
                                    ->where('day', $day)
                                    ->where('time_slot', $slot)
                                    ->first();

                                @endphp

                                <td class="border p-3 text-center w-32">

                                    @if($schedule)

                                    <form
                                        method="POST"
                                        action="{{ route('room-schedules.toggle', $schedule) }}"
                                    >

                                        @csrf
                                        @method('PATCH')

                                        <button
                                        onclick="toggleSchedule(this, {{ $schedule->id }})"
                                        class="w-full py-3 rounded-2xl text-sm font-semibold transition whitespace-nowrap
                                        {{ $schedule->available
                                            ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                            : 'bg-red-100 text-red-700 hover:bg-red-200'
                                        }}"
                                    >
                                        {{ $schedule->available ? 'Tersedia' : 'Tidak Tersedia' }}
                                        </button>

                                    </form>

                                    @endif

                                </td>

                                @endforeach

                            </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

                <p class="text-gray-400 text-sm mt-4">
                    * Klik status untuk mengubah ketersediaan
                </p>

            </div>

        </div>

        @endforeach

    </div>

</div>
<script>
function toggleRoom(id) {
    const room = document.getElementById('room-' + id);
    const icon = document.getElementById('icon-' + id);
    room.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
}

async function toggleSchedule(btn, scheduleId) {
    btn.disabled = true;

    const response = await fetch(`/admin/room-schedules/${scheduleId}/toggle`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    });

    const data = await response.json();
    const isAvailable = data.available;

    btn.textContent = isAvailable ? 'Tersedia' : 'Tidak Tersedia';
    btn.className = `w-full py-3 rounded-2xl text-sm font-semibold transition ${
        isAvailable
            ? 'bg-green-100 text-green-700 hover:bg-green-200'
            : 'bg-red-100 text-red-700 hover:bg-red-200'
    }`;

    btn.disabled = false;
}

function saveOpenRoomsAndSubmit(select) {
    // simpan room id yang sedang terbuka
    const openRooms = [];
    document.querySelectorAll('[id^="room-"]').forEach(el => {
        if (!el.classList.contains('hidden')) {
            openRooms.push(el.id.replace('room-', ''));
        }
    });
    sessionStorage.setItem('openRooms', JSON.stringify(openRooms));
    select.form.submit();
}

// restore saat halaman load
document.addEventListener('DOMContentLoaded', () => {
    const openRooms = JSON.parse(sessionStorage.getItem('openRooms') || '[]');
    openRooms.forEach(id => {
        const room = document.getElementById('room-' + id);
        const icon = document.getElementById('icon-' + id);
        if (room) room.classList.remove('hidden');
        if (icon) icon.classList.add('rotate-180');
    });
    sessionStorage.removeItem('openRooms');
});
</script>
@endsection