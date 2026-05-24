<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>

    @vite('resources/css/app.css')
</head>

<body class="bg-[#f5f4ff] min-h-screen">

    <!-- Navbar -->
    <div class="bg-white border-b px-8 py-5 flex justify-between items-center">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Student Dashboard
            </h1>

            <p class="text-gray-500 text-sm mt-1">
                Selamat datang, {{ auth()->user()->name }}
                ({{ auth()->user()->nim }})
            </p>
        </div>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button
                type="submit"
                class="text-red-500 border border-red-300 px-4 py-2 rounded-lg hover:bg-red-50 transition"
            >
                Logout
            </button>
        </form>

    </div>

    <!-- Content -->
    <div class="p-8">

        <!-- Button -->
        <a href="{{ route('student.borrowings.create') }}"
           class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white px-5 py-3 rounded-xl shadow hover:opacity-90 transition">

            ＋ Peminjaman Baru
        </a>

        <!-- Active Borrow -->
        <div class="mt-8">

            <h2 class="font-semibold text-lg text-gray-800 mb-4">
                📦 Peminjaman Aktif
            </h2>

            <div class="bg-white rounded-2xl shadow-sm p-8 text-center text-gray-400">

                @forelse($activeBorrowings as $borrowing)

                    <div class="bg-white rounded-2xl shadow-sm p-6 mb-4">

                        <div class="flex justify-between items-start">

                            <div>

                                <h3 class="text-xl font-bold text-gray-800">

                                    {{ $borrowing->room->name }}

                                </h3>

                                <p class="text-gray-500 mt-1">

                                    {{ \Carbon\Carbon::parse($borrowing->borrow_date)->format('d M Y') }}

                                    •

                                    {{ $borrowing->time_slot }}

                                </p>

                                <p class="mt-3 text-gray-600">

                                    {{ $borrowing->purpose }}

                                </p>

                                <div class="mt-4">

                                    <p class="font-semibold text-gray-700">

                                        Alat:

                                    </p>

                                    <ul class="mt-1 text-gray-500">

                                        @foreach($borrowing->items as $item)

                                            <li>

                                                •
                                                {{ $item->item->name }}
                                                ({{ $item->qty }})

                                            </li>

                                        @endforeach

                                    </ul>

                                </div>

                            </div>

                            <!-- STATUS -->
                            <div>

                                @if($borrowing->status == 'PENDING')

                                    <span class="bg-yellow-100
                                                text-yellow-700
                                                px-4 py-2
                                                rounded-xl
                                                text-sm
                                                font-semibold">

                                        PENDING

                                    </span>

                                @elseif($borrowing->status == 'APPROVED')

                                    <span class="bg-green-100
                                                text-green-700
                                                px-4 py-2
                                                rounded-xl
                                                text-sm
                                                font-semibold">

                                        APPROVED

                                    </span>

                                    <!-- FINISH BUTTON -->
                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'student.borrowings.finish',
                                            $borrowing
                                        ) }}"
                                        class="mt-4"
                                    >

                                        @csrf
                                        @method('PATCH')

                                        <button
                                            class="bg-indigo-600 text-white
                                                px-4 py-2 rounded-xl
                                                text-sm hover:bg-indigo-700 transition"
                                        >

                                            Selesaikan Peminjaman

                                        </button>

                                    </form>

                                @elseif($borrowing->status == 'WAITING_RETURN')

                                    <span class="bg-blue-100
                                                text-blue-700
                                                px-4 py-2
                                                rounded-xl
                                                text-sm
                                                font-semibold">

                                        WAITING RETURN

                                    </span>

                                @elseif($borrowing->status == 'COMPLETED')

                                    <span class="bg-gray-100
                                                text-gray-700
                                                px-4 py-2
                                                rounded-xl
                                                text-sm
                                                font-semibold">

                                        COMPLETED

                                    </span>

                                @elseif($borrowing->status == 'REJECTED')

                                    <span class="bg-red-100
                                                text-red-700
                                                px-4 py-2
                                                rounded-xl
                                                text-sm
                                                font-semibold">

                                        REJECTED

                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="bg-white rounded-2xl shadow-sm p-8 text-center text-gray-400">

                        Tidak ada peminjaman aktif

                    </div>

                @endforelse

            </div>

        </div>

        <!-- History -->
        <div class="mt-10">

            <h2 class="font-semibold text-lg text-gray-800 mb-4">
                🕘 History Peminjaman
            </h2>

            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">

                <table class="w-full">

                    <thead class="bg-gray-50 text-left text-sm text-gray-600">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Ruangan</th>
                            <th class="px-6 py-4">Waktu</th>
                            <th class="px-6 py-4">Alat</th>
                            <th class="px-6 py-4">Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <td colspan="5"
                                class="text-center text-gray-400 py-10">

                                Belum ada history peminjaman

                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</body>
</html>