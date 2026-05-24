@extends('admin.layouts.app')

@section('content')

<div>

    <!-- HEADER -->
    <div class="flex items-start justify-between">

        <div>

            <h1 class="text-5xl font-bold text-gray-900">
                History Inventory
            </h1>

            <p class="mt-3 text-gray-500 text-xl">
                Semua aktivitas perubahan inventory laboratorium
            </p>

        </div>

        <!-- SUMMARY -->
        <div class="grid grid-cols-3 gap-4">

            <!-- TOTAL -->
            <div class="bg-white rounded-2xl shadow-sm px-6 py-5 min-w-[170px]">

                <p class="text-gray-400 text-sm">
                    Total Aktivitas
                </p>

                <h2 class="text-3xl font-bold text-gray-800 mt-2">

                    {{ $histories->total() }}

                </h2>

            </div>

            <!-- BORROW -->
            <div class="bg-white rounded-2xl shadow-sm px-6 py-5 min-w-[170px]">

                <p class="text-gray-400 text-sm">
                    Peminjaman
                </p>

                <h2 class="text-3xl font-bold text-red-500 mt-2">

                    {{
                        $histories->where('action', 'borrow')->count()
                    }}

                </h2>

            </div>

            <!-- RETURN -->
            <div class="bg-white rounded-2xl shadow-sm px-6 py-5 min-w-[170px]">

                <p class="text-gray-400 text-sm">
                    Pengembalian
                </p>

                <h2 class="text-3xl font-bold text-green-500 mt-2">

                    {{
                        $histories->where('action', 'return')->count()
                    }}

                </h2>

            </div>

        </div>

    </div>

    <!-- EXPORT -->
    <div class="flex gap-4 mt-8">

        <!-- EXCEL -->
        <a
            href="{{ route('item-histories.export.excel') }}"
            class="bg-green-500 text-white
                   px-5 py-3 rounded-2xl
                   font-semibold hover:bg-green-600
                   transition"
        >

            Download Excel

        </a>

        <!-- CSV -->
        <a
            href="{{ route('item-histories.export.csv') }}"
            class="bg-blue-500 text-white
                   px-5 py-3 rounded-2xl
                   font-semibold hover:bg-blue-600
                   transition"
        >

            Download CSV

        </a>

    </div>

    <!-- TABLE -->
    <div class="mt-10 bg-white rounded-3xl shadow overflow-hidden">

        <table class="w-full">

            <!-- HEAD -->
            <thead class="bg-gray-50 border-b">

                <tr class="text-left text-gray-700">

                    <th class="px-6 py-5">
                        Tanggal
                    </th>

                    <th class="px-6 py-5">
                        Item
                    </th>

                    <th class="px-6 py-5">
                        Aktivitas
                    </th>

                    <th class="px-6 py-5">
                        Perubahan Stok
                    </th>

                    <th class="px-6 py-5">
                        User
                    </th>

                </tr>

            </thead>

            <!-- BODY -->
            <tbody>

                @forelse($histories as $history)

                    <tr class="border-t hover:bg-gray-50 transition">

                        <!-- DATE -->
                        <td class="px-6 py-5 whitespace-nowrap">

                            <div class="font-medium text-gray-800">

                                {{
                                    $history->created_at
                                        ->format('d M Y')
                                }}

                            </div>

                            <div class="text-sm text-gray-400 mt-1">

                                {{
                                    $history->created_at
                                        ->format('H:i')
                                }}

                            </div>

                        </td>

                        <!-- ITEM -->
                        <td class="px-6 py-5">

                            <div class="font-semibold text-gray-800">

                                {{ $history->item_name }}

                            </div>

                        </td>

                        <!-- ACTIVITY -->
                        <td class="px-6 py-5">

                            <!-- DESCRIPTION -->
                            <div class="font-medium text-gray-800">

                                {{ $history->description }}

                            </div>

                            <!-- ACTION -->
                            <div class="mt-2">

                                @if($history->action == 'borrow')

                                    <span class="bg-red-100
                                                 text-red-700
                                                 px-3 py-1 rounded-lg
                                                 text-xs font-semibold uppercase">

                                        Borrow

                                    </span>

                                @elseif($history->action == 'return')

                                    <span class="bg-green-100
                                                 text-green-700
                                                 px-3 py-1 rounded-lg
                                                 text-xs font-semibold uppercase">

                                        Return

                                    </span>

                                @elseif($history->action == 'cancel')

                                    <span class="bg-yellow-100
                                                 text-yellow-700
                                                 px-3 py-1 rounded-lg
                                                 text-xs font-semibold uppercase">

                                        Cancel

                                    </span>

                                @elseif($history->action == 'create')

                                    <span class="bg-blue-100
                                                 text-blue-700
                                                 px-3 py-1 rounded-lg
                                                 text-xs font-semibold uppercase">

                                        Create

                                    </span>

                                @elseif($history->action == 'update')

                                    <span class="bg-indigo-100
                                                 text-indigo-700
                                                 px-3 py-1 rounded-lg
                                                 text-xs font-semibold uppercase">

                                        Update

                                    </span>

                                @elseif($history->action == 'delete')

                                    <span class="bg-gray-200
                                                 text-gray-700
                                                 px-3 py-1 rounded-lg
                                                 text-xs font-semibold uppercase">

                                        Delete

                                    </span>

                                @endif

                            </div>

                        </td>

                        <!-- STOCK -->
                        <td class="px-6 py-5">

                            @if(
                                !is_null($history->old_stock)
                                &&
                                !is_null($history->new_stock)
                            )

                                <div class="flex items-center gap-3">

                                    <!-- OLD -->
                                    <span class="font-semibold text-gray-700">

                                        {{ $history->old_stock }}

                                    </span>

                                    <span class="text-gray-400">
                                        →
                                    </span>

                                    <!-- NEW -->
                                    <span class="font-semibold text-gray-900">

                                        {{ $history->new_stock }}

                                    </span>

                                </div>

                                <!-- DIFFERENCE -->
                                <div class="mt-2">

                                    @php

                                        $difference =
                                            $history->new_stock -
                                            $history->old_stock;

                                    @endphp

                                    @if($difference > 0)

                                        <span class="text-green-600
                                                     text-sm font-semibold">

                                            +{{ $difference }}

                                        </span>

                                    @elseif($difference < 0)

                                        <span class="text-red-600
                                                     text-sm font-semibold">

                                            {{ $difference }}

                                        </span>

                                    @else

                                        <span class="text-gray-400 text-sm">

                                            0

                                        </span>

                                    @endif

                                </div>

                            @else

                                <span class="text-gray-400">
                                    -
                                </span>

                            @endif

                        </td>

                        <!-- USER -->
                        <td class="px-6 py-5">

                            <div class="font-medium text-gray-800">

                                {{ $history->user->name ?? '-' }}

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="5"
                            class="text-center py-14 text-gray-400"
                        >

                            Belum ada history inventory

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <!-- PAGINATION -->
    <div class="mt-8">

        {{ $histories->links() }}

    </div>

</div>

@endsection