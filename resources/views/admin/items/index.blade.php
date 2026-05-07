@extends('admin.layouts.app')

@section('content')

<!-- Header -->
<div class="flex justify-between items-start">

    <div>
        <h1 class="text-5xl font-bold text-gray-900">
            Inventory Alat dan Bahan
        </h1>

        <p class="mt-3 text-gray-500 text-xl">
            Kelola alat dan bahan laboratorium
        </p>
    </div>

    <a href="{{ route('items.create') }}"
       class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-6 py-4 rounded-2xl font-semibold shadow hover:opacity-90 transition">

        + Tambah Item
    </a>

</div>

<!-- Search -->
<form method="GET" class="mt-10">

    <input
        type="text"
        name="search"
        value="{{ $search }}"
        placeholder="Cari berdasarkan nama atau lokasi..."
        class="w-full max-w-xl rounded-2xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
    >

</form>

<!-- TOOLS -->
<div class="mt-10 bg-white rounded-3xl shadow overflow-hidden">

    <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-5">

        <h2 class="text-3xl font-bold text-white">
            Alat Laboratorium
        </h2>

    </div>

    <table class="w-full">

        <thead class="bg-gray-50 text-gray-700">
            <tr>
                <th class="px-6 py-4 text-left">No</th>
                <th class="px-6 py-4 text-left">Nama Alat</th>
                <th class="px-6 py-4 text-left">Lokasi</th>
                <th class="px-6 py-4 text-left">Satuan</th>
                <th class="px-6 py-4 text-left">Stok</th>
                <th class="px-6 py-4 text-left">Aksi</th>
            </tr>
        </thead>

        <tbody>

        @forelse($tools as $tool)

            <tr class="border-t">

                <td class="px-6 py-4">
                    {{ $loop->iteration }}
                </td>

                <td class="px-6 py-4 font-semibold">
                    {{ $tool->name }}
                </td>

                <td class="px-6 py-4">
                    {{ $tool->room->name }}
                    -
                    {{ $tool->location }}
                </td>

                <td class="px-6 py-4">
                    {{ $tool->unit }}
                </td>

                <td class="px-6 py-4">
                    {{ $tool->stock }}
                </td>

                <td class="px-6 py-4 flex gap-3">

                    <a href="{{ route('items.edit', $tool) }}"
                       class="text-blue-500">
                        Edit
                    </a>

                    <form method="POST"
                          action="{{ route('items.destroy', $tool) }}">

                        @csrf
                        @method('DELETE')

                        <button class="text-red-500">
                            Delete
                        </button>

                    </form>

                </td>

            </tr>

        @empty

            <tr>
                <td colspan="6"
                    class="text-center py-10 text-gray-400">

                    Tidak ada data alat

                </td>
            </tr>

        @endforelse

        </tbody>

    </table>

</div>

<!-- MATERIAL -->
<div class="mt-10 bg-white rounded-3xl shadow overflow-hidden">

    <div class="bg-gradient-to-r from-green-500 to-teal-400 px-6 py-5">

        <h2 class="text-3xl font-bold text-white">
            Bahan Laboratorium
        </h2>

    </div>

    <table class="w-full">

        <thead class="bg-gray-50 text-gray-700">
            <tr>
                <th class="px-6 py-4 text-left">No</th>
                <th class="px-6 py-4 text-left">Nama Bahan</th>
                <th class="px-6 py-4 text-left">Lokasi</th>
                <th class="px-6 py-4 text-left">Satuan</th>
                <th class="px-6 py-4 text-left">Stok</th>
                <th class="px-6 py-4 text-left">Aksi</th>
            </tr>
        </thead>

        <tbody>

        @forelse($materials as $material)

            <tr class="border-t">

                <td class="px-6 py-4">
                    {{ $loop->iteration }}
                </td>

                <td class="px-6 py-4 font-semibold">
                    {{ $material->name }}
                </td>

                <td class="px-6 py-4">
                    {{ $material->room->name }}
                    -
                    {{ $material->location }}
                </td>

                <td class="px-6 py-4">
                    {{ $material->unit }}
                </td>

                <td class="px-6 py-4">
                    {{ $material->stock }}
                </td>

                <td class="px-6 py-4 flex gap-3">

                    <a href="{{ route('items.edit', $material) }}"
                       class="text-blue-500">
                        Edit
                    </a>

                    <form method="POST"
                          action="{{ route('items.destroy', $material) }}">

                        @csrf
                        @method('DELETE')

                        <button class="text-red-500">
                            Delete
                        </button>

                    </form>

                </td>

            </tr>

        @empty

            <tr>
                <td colspan="6"
                    class="text-center py-10 text-gray-400">

                    Tidak ada data bahan

                </td>
            </tr>

        @endforelse

        </tbody>

    </table>

</div>

@endsection