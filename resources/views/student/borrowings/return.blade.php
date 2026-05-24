<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Return Items</title>

    @vite('resources/css/app.css')

</head>

<body class="bg-[#f5f4ff] min-h-screen">

<div class="max-w-5xl mx-auto py-12 px-6">

    <!-- BACK -->
    <a
        href="{{ route('student.dashboard') }}"
        class="inline-flex items-center gap-2
               text-indigo-600 font-semibold
               hover:text-indigo-800 transition"
    >

        ← Kembali

    </a>

    <!-- HEADER -->
    <div class="mt-5">

        <h1 class="text-5xl font-bold text-gray-900">
            Pengembalian Peminjaman
        </h1>

        <p class="mt-3 text-gray-500 text-lg">
            Isi jumlah item yang dikembalikan
        </p>

    </div>

    <!-- INFO -->
    <div class="mt-10 bg-white rounded-3xl shadow p-8">

        <div class="grid grid-cols-2 gap-8">

            <!-- ROOM -->
            <div>

                <p class="text-gray-500 text-sm">
                    Ruangan
                </p>

                <h2 class="mt-2 text-2xl font-bold text-gray-800">

                    {{ $borrowing->room->name }}

                </h2>

            </div>

            <!-- DATE -->
            <div>

                <p class="text-gray-500 text-sm">
                    Jadwal
                </p>

                <h2 class="mt-2 text-2xl font-bold text-gray-800">

                    {{
                        \Carbon\Carbon::parse(
                            $borrowing->borrow_date
                        )->format('d M Y')
                    }}

                    •

                    {{ $borrowing->time_slot }}

                </h2>

            </div>

        </div>

    </div>

    <!-- FORM -->
    <form
        method="POST"
        action="{{ route(
            'student.borrowings.return.submit',
            $borrowing
        ) }}"
        class="mt-10 bg-white rounded-3xl shadow overflow-hidden"
    >

        @csrf
        @method('PATCH')

        <!-- TABLE -->
        <table class="w-full">

            <thead class="bg-gray-50">

                <tr class="text-left text-gray-700">

                    <th class="px-6 py-5">
                        Item
                    </th>

                    <th class="px-6 py-5">
                        Dipinjam
                    </th>

                    <th class="px-6 py-5">
                        Dikembalikan
                    </th>

                    <th class="px-6 py-5">
                        Tipe
                    </th>

                </tr>

            </thead>

            <tbody>

                @foreach($borrowing->items as $borrowedItem)

                    <tr class="border-t">

                        <!-- ITEM -->
                        <td class="px-6 py-5">

                            <div class="font-semibold text-gray-800">

                                {{ $borrowedItem->item->name }}

                            </div>

                        </td>

                        <!-- BORROWED -->
                        <td class="px-6 py-5">

                            {{ $borrowedItem->qty }}

                        </td>

                        <!-- RETURN -->
                        <td class="px-6 py-5">

                            <input
                                type="number"
                                min="0"
                                max="{{ $borrowedItem->qty }}"
                                value="{{ $borrowedItem->qty }}"
                                name="returned_qty[{{ $borrowedItem->id }}]"
                                class="w-32 rounded-xl border-gray-300
                                       focus:border-indigo-500
                                       focus:ring-indigo-500"
                            >

                            <p class="text-sm text-gray-400 mt-2">

                                Maksimal:
                                {{ $borrowedItem->qty }}

                            </p>

                        </td>

                        <!-- TYPE -->
                        <td class="px-6 py-5">

                            @if($borrowedItem->item->type == 'tool')

                                <span class="bg-blue-100
                                             text-blue-700
                                             px-4 py-2
                                             rounded-xl
                                             text-sm
                                             font-semibold">

                                    TOOL

                                </span>

                            @else

                                <span class="bg-orange-100
                                             text-orange-700
                                             px-4 py-2
                                             rounded-xl
                                             text-sm
                                             font-semibold">

                                    MATERIAL

                                </span>

                            @endif

                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

        <!-- ACTION -->
        <div class="p-8 flex gap-4">

            <!-- CANCEL -->
            <a
                href="{{ route('student.dashboard') }}"
                class="w-1/3 bg-gray-100 text-gray-700
                       py-4 rounded-2xl text-lg
                       font-semibold text-center
                       hover:bg-gray-200 transition"
            >

                Cancel

            </a>

            <!-- SUBMIT -->
            <button
                type="submit"
                class="w-2/3 bg-gradient-to-r
                       from-indigo-500 to-purple-600
                       text-white py-4 rounded-2xl
                       text-lg font-semibold
                       hover:opacity-90 transition"
            >

                Kirim Pengembalian

            </button>

        </div>

    </form>

</div>

</body>
</html>