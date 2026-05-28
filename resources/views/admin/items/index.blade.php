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
       class="bg-gradient-to-r from-green-600 to-teal-700 text-white px-6 py-4 rounded-2xl font-semibold shadow hover:opacity-90 transition">
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
                <th class="px-6 py-4 text-left">Total Stok</th>
                <th class="px-6 py-4 text-left">Aksi</th>
            </tr>
        </thead>

        <tbody>

        @forelse($tools as $toolName => $toolGroup)

            @php
                $stock   = $toolGroup->sum('stock');
                $minimum = $toolGroup->sum('minimum_stock');
            @endphp

            <tr class="border-t align-top">

                <td class="px-6 py-4">
                    {{ $loop->iteration }}
                </td>

                <td class="px-6 py-4">
                    <div class="font-semibold text-gray-800">
                        {{ $toolName }}
                    </div>
                    <div class="mt-1">
                        @if($stock <= $minimum)
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-xl text-xs font-semibold">
                                STOK RENDAH
                            </span>
                        @else
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-xl text-xs font-semibold">
                                STOK AMAN
                            </span>
                        @endif
                    </div>
                </td>

                <td class="px-6 py-4">
                    @foreach($toolGroup as $tool)
                        <div class="{{ !$loop->last ? 'mb-2' : '' }} text-gray-600">
                            {{ $tool->room->name }} - {{ $tool->location }}
                            <span class="text-gray-400 text-sm">({{ $tool->stock }})</span>
                        </div>
                    @endforeach
                </td>

                <td class="px-6 py-4">
                    {{ $toolGroup->first()->unit }}
                </td>

                <td class="px-6 py-4 font-bold text-gray-800">
                    {{ $stock }}
                </td>

                <td class="px-6 py-4">
                    <div class="flex flex-col gap-2">
                        @foreach($toolGroup as $tool)
                            <div class="flex gap-2 items-center">

                                <a href="{{ route('items.edit', $tool) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 text-sm font-semibold hover:bg-blue-100 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                    Edit
                                </a>

                                <form method="POST" action="{{ route('items.destroy', $tool) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        onclick="return confirm('Hapus item ini?')"
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
                        @endforeach
                    </div>
                </td>

            </tr>

        @empty

            <tr>
                <td colspan="6" class="text-center py-10 text-gray-400">
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
                <th class="px-6 py-4 text-left">Total Stok</th>
                <th class="px-6 py-4 text-left">Aksi</th>
            </tr>
        </thead>

        <tbody>

        @forelse($materials as $materialName => $materialGroup)

            @php
                $stock   = $materialGroup->sum('stock');
                $minimum = $materialGroup->sum('minimum_stock');
            @endphp

            <tr class="border-t align-top">

                <td class="px-6 py-4">
                    {{ $loop->iteration }}
                </td>

                <td class="px-6 py-4">
                    <div class="font-semibold text-gray-800">
                        {{ $materialName }}
                    </div>
                    <div class="mt-1">
                        @if($stock <= $minimum)
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-xl text-xs font-semibold">
                                STOK RENDAH
                            </span>
                        @else
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-xl text-xs font-semibold">
                                STOK AMAN
                            </span>
                        @endif
                    </div>
                </td>

                <td class="px-6 py-4">
                    @foreach($materialGroup as $material)
                        <div class="{{ !$loop->last ? 'mb-2' : '' }} text-gray-600">
                            {{ $material->room->name }} - {{ $material->location }}
                            <span class="text-gray-400 text-sm">({{ $material->stock }})</span>
                        </div>
                    @endforeach
                </td>

                <td class="px-6 py-4">
                    {{ $materialGroup->first()->unit }}
                </td>

                <td class="px-6 py-4 font-bold text-gray-800">
                    {{ $stock }}
                </td>

                <td class="px-6 py-4">
                    <div class="flex flex-col gap-2">
                        @foreach($materialGroup as $material)
                            <div class="flex gap-2 items-center">

                                <a href="{{ route('items.edit', $material) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 text-sm font-semibold hover:bg-blue-100 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                    Edit
                                </a>

                                <form method="POST" action="{{ route('items.destroy', $material) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        onclick="return confirm('Hapus item ini?')"
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
                        @endforeach
                    </div>
                </td>

            </tr>

        @empty

            <tr>
                <td colspan="6" class="text-center py-10 text-gray-400">
                    Tidak ada data bahan
                </td>
            </tr>

        @endforelse

        </tbody>

    </table>

</div>

@endsection