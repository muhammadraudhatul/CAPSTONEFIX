@extends('admin.layouts.app')

@section('content')

<div>

    <!-- HEADER -->
    <div class="flex items-start justify-between">

        <div>

            <h1 class="text-5xl font-bold text-gray-900">
                Peminjaman
            </h1>

            <p class="mt-3 text-gray-500 text-xl">
                Kelola semua peminjaman dari seluruh student
            </p>

        </div>

    </div>

    <!-- STATS -->
    <div class="grid grid-cols-4 gap-6 mt-10">

        <!-- TOTAL -->
        <div class="bg-white rounded-3xl shadow p-8 border-l-4 border-indigo-500">

            <p class="text-gray-500 text-lg">
                Total Peminjaman
            </p>

            <h2 class="text-5xl font-bold mt-3">

                {{ $borrowings->count() }}

            </h2>

        </div>

        <!-- ACTIVE -->
        <div class="bg-white rounded-3xl shadow p-8 border-l-4 border-blue-500">

            <p class="text-gray-500 text-lg">
                Peminjaman Aktif
            </p>

            <h2 class="text-5xl font-bold mt-3">

                {{
                    $borrowings
                        ->whereIn('status', [

                            'APPROVED',
                            'WAITING_RETURN',

                        ])
                        ->count()
                }}

            </h2>

        </div>

        <!-- PENDING -->
        <div class="bg-white rounded-3xl shadow p-8 border-l-4 border-orange-500">

            <p class="text-gray-500 text-lg">
                Menunggu Persetujuan
            </p>

            <h2 class="text-5xl font-bold mt-3">

                {{
                    $borrowings
                        ->where('status', 'PENDING')
                        ->count()
                }}

            </h2>

        </div>

        <!-- HISTORY -->
        <div class="bg-white rounded-3xl shadow p-8 border-l-4 border-purple-500">

            <p class="text-gray-500 text-lg">
                History
            </p>

            <h2 class="text-5xl font-bold mt-3">

                {{
                    $borrowings
                        ->whereIn('status', [

                            'COMPLETED',
                            'REJECTED',

                        ])
                        ->count()
                }}

            </h2>

        </div>

    </div>

    <!-- TABLE -->
    <div class="mt-10 bg-white rounded-3xl shadow overflow-hidden">

        <table class="w-full">

            <thead class="bg-gray-50">

                <tr class="text-left text-gray-700">

                    <th class="px-6 py-5">
                        Student
                    </th>

                    <th class="px-6 py-5">
                        Ruangan
                    </th>

                    <th class="px-6 py-5">
                        Jadwal
                    </th>

                    <th class="px-6 py-5">
                        Alat
                    </th>

                    <th class="px-6 py-5">
                        Status
                    </th>

                    <th class="px-6 py-5 text-center">
                        Aksi
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($borrowings as $borrowing)

                    <tr class="border-t align-top">

                        <!-- STUDENT -->
                        <td class="px-6 py-6">

                            <div class="font-bold text-gray-800">

                                {{ $borrowing->user->name }}

                            </div>

                            <div class="text-gray-500 text-sm mt-1">

                                {{ $borrowing->user->nim }}

                            </div>

                        </td>

                        <!-- ROOM -->
                        <td class="px-6 py-6">

                            <div class="font-semibold text-gray-700">

                                {{ $borrowing->room->name }}

                            </div>

                        </td>

                        <!-- SCHEDULE -->
                        <td class="px-6 py-6">

                            <div class="font-semibold text-gray-700">

                                {{
                                    \Carbon\Carbon::parse(
                                        $borrowing->borrow_date
                                    )->format('d M Y')
                                }}

                            </div>

                            <div class="text-gray-500 mt-1">

                                {{ $borrowing->time_slot }}

                            </div>

                        </td>

                        <!-- ITEMS -->
                        <td class="px-6 py-6">

                            @foreach($borrowing->items as $item)

                                <div class="mb-2">

                                    •
                                    {{ $item->item->name ?? '-' }}

                                    ({{ $item->qty }})

                                </div>

                            @endforeach

                        </td>

                        <!-- STATUS -->
                        <td class="px-6 py-6">

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

                            @endif

                        </td>

                        <!-- ACTION -->
                        <td class="px-6 py-6">

                            <div class="flex flex-col gap-3">

                                @if(
                                    session('error_borrowing_id')
                                    == $borrowing->id
                                )

                                    <div class="mb-4 bg-red-50
                                                border border-red-200
                                                rounded-2xl p-5">

                                        <div class="flex gap-3">

                                            <div class="text-red-500 text-2xl">

                                                ⚠️

                                            </div>

                                            <div>

                                                <p class="font-bold text-red-700">

                                                    Terjadi Kesalahan

                                                </p>

                                                <p class="text-red-600 mt-1">

                                                    {{ session('error_message') }}

                                                </p>

                                            </div>

                                        </div>

                                    </div>

                                @endif

                                <!-- APPROVE -->
                                @if($borrowing->status == 'PENDING')

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'admin.borrowings.approve',
                                            $borrowing
                                        ) }}"
                                    >

                                        @csrf
                                        @method('PATCH')

                                        <button
                                            class="w-full bg-green-600
                                                   text-white px-4 py-2
                                                   rounded-xl hover:bg-green-700"
                                        >

                                            Approve

                                        </button>

                                    </form>

                                    <!-- REJECT -->
                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'admin.borrowings.reject',
                                            $borrowing
                                        ) }}"
                                    >

                                        @csrf
                                        @method('PATCH')

                                        <button
                                            class="w-full bg-red-600
                                                   text-white px-4 py-2
                                                   rounded-xl hover:bg-red-700"
                                        >

                                            Reject

                                        </button>

                                    </form>

                                @endif

                                <!-- COMPLETE -->
                                @if($borrowing->status == 'WAITING_RETURN')

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'admin.borrowings.complete',
                                            $borrowing
                                        ) }}"
                                    >

                                        @csrf
                                        @method('PATCH')

                                        <button
                                            class="w-full bg-indigo-600
                                                   text-white px-4 py-2
                                                   rounded-xl hover:bg-indigo-700"
                                        >

                                            Confirm Return

                                        </button>

                                    </form>

                                @endif

                                <!-- ADMIN CANCEL -->
                                @if(
                                    in_array(
                                        $borrowing->status,
                                        [

                                            'APPROVED',
                                            'WAITING_RETURN',

                                        ]
                                    )
                                )

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'admin.borrowings.cancel',
                                            $borrowing
                                        ) }}"
                                    >

                                        @csrf
                                        @method('PATCH')

                                        <textarea
                                            name="cancel_reason"
                                            required
                                            placeholder="Alasan pembatalan..."
                                            class="w-full border rounded-xl
                                                px-3 py-2 text-sm"
                                        ></textarea>

                                        <button
                                            onclick="return confirm(
                                                'Batalkan peminjaman ini?'
                                            )"
                                            class="mt-2 w-full bg-red-600
                                                text-white px-4 py-2
                                                rounded-xl hover:bg-red-700"
                                        >

                                            Cancel Borrowing

                                        </button>

                                    </form>

                                @endif

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="6"
                            class="text-center py-20 text-gray-400"
                        >

                            Tidak ada peminjaman

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection