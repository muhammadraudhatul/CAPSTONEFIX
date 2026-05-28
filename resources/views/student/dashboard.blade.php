<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
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

                Selamat datang,
                {{ auth()->user()->name }}

                ({{ auth()->user()->nim }})

            </p>

        </div>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}">

            @csrf

            <button
                type="submit"
                class="flex items-center gap-2 text-red-500 border border-red-200
                    bg-red-50 px-4 py-2 rounded-xl
                    hover:bg-red-100 transition text-sm font-medium"
            >
                <i class="ti ti-logout text-base"></i>
                Logout
            </button>

        </form>

    </div>

    <!-- Content -->
    <div class="p-8">
        <x-alert-error />
        <!-- Create -->
        <a
            href="{{ route('student.borrowings.create') }}"
            class="inline-flex items-center gap-2
                   bg-gradient-to-r
                   from-green-600 to-teal-700
                   text-white px-5 py-3 rounded-xl
                   shadow hover:opacity-90 transition"
        >

            ＋ Peminjaman Baru

        </a>

        <!-- ACTIVE -->
        <div class="mt-8">

            <h2 class="font-bold text-lg text-gray-800 mb-4 flex items-center gap-2">
                <i class="ti ti-package text-green-600 text-xl"></i>
                Peminjaman Aktif
            </h2>

            @forelse($activeBorrowings as $borrowing)

                <div class="bg-white rounded-2xl shadow-sm p-6 mb-4">

                    <div class="flex justify-between items-start">

                        <!-- LEFT -->
                        <div>

                            <!-- ROOM -->
                            <h3 class="text-xl font-bold text-gray-800">

                                {{ $borrowing->room->name }}

                            </h3>

                            <!-- DATE -->
                            <p class="text-gray-500 mt-1">

                                {{
                                    \Carbon\Carbon::parse(
                                        $borrowing->borrow_date
                                    )->format('d M Y')
                                }}

                                •

                                {{ $borrowing->time_slot }}

                            </p>

                            <!-- PURPOSE -->
                            <p class="mt-3 text-gray-600">

                                {{ $borrowing->purpose }}

                            </p>

                            <!-- ITEMS -->
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

                        <!-- RIGHT -->
                        <div class="text-right">

                            <!-- STATUS -->
                            @if($borrowing->status == 'PENDING')

                                <span class="bg-yellow-100
                                             text-yellow-700
                                             px-4 py-2
                                             rounded-xl
                                             text-sm
                                             font-semibold">

                                    PENDING

                                </span>

                                <!-- ACTION -->
                                <div class="mt-4 flex gap-3">

                                    <!-- EDIT -->
                                    <a
                                        href="{{ route(
                                            'student.borrowings.edit',
                                            $borrowing
                                        ) }}"
                                        class="bg-blue-600 text-white
                                               px-4 py-2 rounded-xl
                                               text-sm hover:bg-blue-700 transition"
                                    >

                                        Edit

                                    </a>

                                    <!-- DELETE -->
                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'student.borrowings.destroy',
                                            $borrowing
                                        ) }}"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            onclick="return confirm(
                                                'Hapus peminjaman ini?'
                                            )"
                                            class="bg-red-600 text-white
                                                   px-4 py-2 rounded-xl
                                                   text-sm hover:bg-red-700 transition"
                                        >

                                            Delete

                                        </button>

                                    </form>

                                </div>

                            @elseif($borrowing->status == 'APPROVED')

                                <span class="bg-green-100
                                             text-green-700
                                             px-4 py-2
                                             rounded-xl
                                             text-sm
                                             font-semibold">

                                    APPROVED

                                </span>

                                <!-- ACTION -->
                                <div class="mt-4 flex flex-col gap-3">

                                    <!-- RETURN -->
                                    <a
                                        href="{{ route(
                                            'student.borrowings.return.form',
                                            $borrowing
                                        ) }}"
                                        class="bg-indigo-600 text-white
                                               px-4 py-2 rounded-xl
                                               text-sm hover:bg-indigo-700
                                               transition text-center"
                                    >

                                        Selesaikan Peminjaman

                                    </a>

                                    <!-- CANCEL -->
                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'student.borrowings.cancel',
                                            $borrowing
                                        ) }}"
                                    >

                                        @csrf
                                        @method('PATCH')

                                        <button
                                            onclick="return confirm(
                                                'Batalkan peminjaman ini?'
                                            )"
                                            class="w-full bg-red-600
                                                   text-white px-4 py-2
                                                   rounded-xl text-sm
                                                   hover:bg-red-700 transition"
                                        >

                                            Batalkan Peminjaman

                                        </button>

                                    </form>

                                </div>

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

                            @elseif($borrowing->status == 'CANCELLED')

                                <span class="bg-gray-200
                                            text-gray-700
                                            px-4 py-2
                                            rounded-xl
                                            text-sm
                                            font-semibold">

                                    CANCELLED

                                </span>

                                @if($borrowing->cancel_reason)

                                    <div class="mt-4 bg-red-50
                                                border border-red-200
                                                rounded-xl p-4 text-left">

                                        <p class="text-sm font-semibold text-red-700">

                                            Peminjaman dibatalkan admin

                                        </p>

                                        <p class="text-sm text-red-600 mt-2">

                                            {{ $borrowing->cancel_reason }}

                                        </p>

                                    </div>

                                @endif

                            @endif

                        </div>

                    </div>

                </div>

            @empty

                <div class="bg-white rounded-2xl shadow-sm
                            p-8 text-center text-gray-400">

                    Tidak ada peminjaman aktif

                </div>

            @endforelse

        </div>

        <!-- HISTORY -->
        <div class="mt-10">

            <h2 class="font-bold text-lg text-gray-800 mb-4 flex items-center gap-2">
                <i class="ti ti-clock text-gray-500 text-xl"></i>
                History Peminjaman
            </h2>

            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">

                <table class="w-full">

                    <thead class="bg-gray-50 text-left
                                  text-sm text-gray-600">

                        <tr>

                            <th class="px-6 py-4">
                                Tanggal
                            </th>

                            <th class="px-6 py-4">
                                Ruangan
                            </th>

                            <th class="px-6 py-4">
                                Waktu
                            </th>

                            <th class="px-6 py-4">
                                Alat
                            </th>

                            <th class="px-6 py-4">
                                Status
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($histories as $history)

                            <tr class="border-t">

                                <!-- DATE -->
                                <td class="px-6 py-5">

                                    {{
                                        \Carbon\Carbon::parse(
                                            $history->borrow_date
                                        )->format('d M Y')
                                    }}

                                </td>

                                <!-- ROOM -->
                                <td class="px-6 py-5">

                                    {{ $history->room->name }}

                                </td>

                                <!-- TIME -->
                                <td class="px-6 py-5">

                                    {{ $history->time_slot }}

                                </td>

                                <!-- ITEMS -->
                                <td class="px-6 py-5">

                                    @foreach($history->items as $item)

                                        <div>

                                            •
                                            {{ $item->item->name }}

                                            ({{ $item->qty }})

                                        </div>

                                    @endforeach

                                </td>

                                <!-- STATUS -->
                                <td class="px-6 py-5">

                                    <!-- STATUS BADGE -->
                                    @if($history->status == 'COMPLETED')

                                        <span class="bg-gray-100
                                                    text-gray-700
                                                    px-3 py-1
                                                    rounded-xl
                                                    text-sm
                                                    font-semibold">

                                            COMPLETED

                                        </span>

                                    @elseif($history->status == 'REJECTED')

                                        <span class="bg-red-100
                                                    text-red-700
                                                    px-3 py-1
                                                    rounded-xl
                                                    text-sm
                                                    font-semibold">

                                            REJECTED

                                        </span>

                                    @elseif($history->status == 'CANCELLED')

                                        <span class="bg-gray-200
                                                    text-gray-700
                                                    px-3 py-1
                                                    rounded-xl
                                                    text-sm
                                                    font-semibold">

                                            CANCELLED

                                        </span>

                                        @if($history->cancel_reason)

                                            <div class="mt-3
                                                        bg-red-50
                                                        border border-red-200
                                                        rounded-xl
                                                        p-3">

                                                <p class="text-xs
                                                        font-semibold
                                                        text-red-700">

                                                    Dibatalkan Admin

                                                </p>

                                                <p class="text-sm
                                                        text-red-600
                                                        mt-1">

                                                    {{ $history->cancel_reason }}

                                                </p>

                                            </div>

                                        @endif

                                    @else

                                        <span class="text-gray-700">

                                            {{ $history->status }}

                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="5"
                                    class="text-center text-gray-400 py-10"
                                >

                                    Belum ada history peminjaman

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</body>
</html>