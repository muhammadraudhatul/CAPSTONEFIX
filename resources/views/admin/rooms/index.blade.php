@extends('admin.layouts.app')

@section('content')

<!-- Header -->
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
       class="bg-gradient-to-r from-green-600 to-teal-700 text-white px-6 py-4 rounded-2xl font-semibold shadow hover:opacity-90 transition">
        + Tambah Ruangan
    </a>

</div>

<!-- ROOMS TABLE -->
<div class="bg-white rounded-3xl shadow-sm overflow-hidden mt-10">

    <table class="w-full">

        <thead class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wide">
            <tr>
                <th class="px-6 py-4 text-left font-semibold">No</th>
                <th class="px-6 py-4 text-left font-semibold">Nama Ruangan</th>
                <th class="px-6 py-4 text-left font-semibold">Kapasitas</th>
                <th class="px-6 py-4 text-left font-semibold">Aksi</th>
            </tr>
        </thead>

        <tbody>

            @forelse($rooms as $room)

            <tr class="border-t hover:bg-gray-50 transition">

                <td class="px-6 py-4 text-gray-500">
                    {{ $loop->iteration }}
                </td>

                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <span class="font-semibold text-gray-800">{{ $room->name }}</span>
                    </div>
                </td>

                <td class="px-6 py-4 text-gray-600">
                    {{ $room->capacity }}
                </td>

                <td class="px-6 py-4">
                    <div class="flex gap-2 items-center">

                        <a href="{{ route('rooms.edit', $room) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 text-sm font-semibold hover:bg-blue-100 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                            Edit
                        </a>

                        <form method="POST" action="{{ route('rooms.destroy', $room) }}">
                            @csrf
                            @method('DELETE')
                            <button
                                onclick="return confirm('Hapus ruangan ini?')"
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-red-50 text-red-600 text-sm font-semibold hover:bg-red-100 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                    <path d="M10 11v6M14 11v6"/>
                                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                </svg>
                                Delete
                            </button>
                        </form>

                    </div>
                </td>

            </tr>

            @empty

            <tr>
                <td colspan="4" class="text-center py-10 text-gray-400">
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

        <div class="flex items-center gap-3">
            <div class="text-purple-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
            <div>
                <h2 class="text-4xl font-bold text-gray-900">
                    Jadwal Ketersediaan Ruangan
                </h2>
                <p class="text-gray-500 mt-1">
                    Atur ketersediaan ruangan per minggu
                </p>
            </div>
        </div>

        <!-- WEEK DROPDOWN -->
        <form method="GET">
            <div class="relative">
                <select
                    name="week"
                    onchange="saveOpenRoomsAndSubmit(this)"
                    class="appearance-none bg-white border border-gray-200 rounded-2xl pl-5 pr-10 py-3 text-gray-700 font-semibold shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent cursor-pointer"
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
                <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </div>
            </div>
        </form>

    </div>

    <!-- EACH ROOM -->
    <div class="space-y-4">

        @foreach($rooms as $room)

        <div class="bg-white rounded-3xl shadow-sm overflow-hidden border border-gray-100">

            <!-- HEADER -->
            <button
                type="button"
                onclick="toggleRoom({{ $room->id }})"
                class="w-full flex justify-between items-center px-6 py-5 text-left hover:bg-gray-50 transition"
            >

                <div class="flex items-center gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">
                            {{ $room->name }}
                        </h3>
                        <p class="text-gray-400 text-sm">
                            Kapasitas: {{ $room->capacity }}
                        </p>
                    </div>
                </div>

                <div
                    id="icon-{{ $room->id }}"
                    class="text-gray-400 transition-transform duration-200"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </div>

            </button>

            <!-- CONTENT -->
            <div
                id="room-{{ $room->id }}"
                class="hidden px-6 pb-6"
            >

                <div class="overflow-auto rounded-2xl border border-gray-100">

                    <table class="w-full">

                        <thead class="bg-purple-50">
                            <tr>
                                <th class="border-b border-gray-100 px-4 py-3 text-purple-700 font-semibold text-sm text-left">
                                    Jam
                                </th>
                                @foreach($days as $day)
                                <th class="border-b border-gray-100 px-4 py-3 text-purple-700 font-semibold text-sm text-center">
                                    {{ $day }}
                                </th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($timeSlots as $slot)

                            <tr class="border-t border-gray-50 hover:bg-gray-50 transition">

                                <td class="px-4 py-3 font-semibold text-gray-700 text-sm whitespace-nowrap">
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

                                <td class="px-3 py-2 text-center w-32">

                                    @if($schedule)
                                    <form
                                        method="POST"
                                        action="{{ route('room-schedules.toggle', $schedule) }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            onclick="toggleSchedule(this, {{ $schedule->id }})"
                                            class="w-full px-3 py-2 rounded-xl text-xs font-semibold transition whitespace-nowrap
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

                <p class="text-gray-400 text-xs mt-3">
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
    btn.className = `w-full px-3 py-2 rounded-xl text-xs font-semibold transition whitespace-nowrap ${
        isAvailable
            ? 'bg-green-100 text-green-700 hover:bg-green-200'
            : 'bg-red-100 text-red-700 hover:bg-red-200'
    }`;

    btn.disabled = false;
}

function saveOpenRoomsAndSubmit(select) {
    const openRooms = [];
    document.querySelectorAll('[id^="room-"]').forEach(el => {
        if (!el.classList.contains('hidden')) {
            openRooms.push(el.id.replace('room-', ''));
        }
    });
    sessionStorage.setItem('openRooms', JSON.stringify(openRooms));
    sessionStorage.setItem('scrollY', window.scrollY);
    select.form.submit();
}

document.addEventListener('DOMContentLoaded', () => {
    const openRooms = JSON.parse(sessionStorage.getItem('openRooms') || '[]');
    openRooms.forEach(id => {
        const room = document.getElementById('room-' + id);
        const icon = document.getElementById('icon-' + id);
        if (room) room.classList.remove('hidden');
        if (icon) icon.classList.add('rotate-180');
    });
    sessionStorage.removeItem('openRooms');

    const scrollY = sessionStorage.getItem('scrollY');
    if (scrollY !== null) {
        window.scrollTo(0, parseInt(scrollY));
        sessionStorage.removeItem('scrollY');
    }
});
</script>

@endsection