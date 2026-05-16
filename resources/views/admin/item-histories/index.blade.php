@extends('admin.layouts.app')

@section('content')

<div>

    <h1 class="text-5xl font-bold text-gray-900">
        History Inventory
    </h1>

    <p class="mt-3 text-gray-500 text-xl">
        Semua perubahan inventory
    </p>

    <div class="mt-10 bg-white rounded-3xl shadow overflow-hidden">

        <table class="w-full">

            <thead class="bg-gray-50">

                <tr class="text-left text-gray-700">

                    <th class="px-6 py-5">Tanggal</th>

                    <th class="px-6 py-5">Item</th>

                    <th class="px-6 py-5">Aksi</th>

                    <th class="px-6 py-5">Stok</th>

                    <th class="px-6 py-5">Admin</th>

                </tr>

            </thead>

            <tbody>

                @forelse($histories as $history)

                    <tr class="border-t">

                        <td class="px-6 py-5">

                            {{ $history->created_at->format('d M Y H:i') }}

                        </td>

                        <td class="px-6 py-5 font-semibold">

                            {{ $history->item_name }}

                        </td>

                        <td class="px-6 py-5">

                            {{ $history->description }}

                        </td>

                        <td class="px-6 py-5">

                            {{ $history->old_stock ?? '-' }}
                            →
                            {{ $history->new_stock ?? '-' }}

                        </td>

                        <td class="px-6 py-5">

                            {{ $history->user->name ?? '-' }}

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="5"
                            class="text-center py-10 text-gray-400">

                            Belum ada history

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection