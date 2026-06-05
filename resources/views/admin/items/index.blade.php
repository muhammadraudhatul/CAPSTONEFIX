@extends('admin.layouts.app')

@section('content')

{{-- Header --}}
<div class="flex justify-between items-start">

    <div>
        <h1 class="text-4xl font-bold text-white tracking-tight">
            Inventory Alat dan Bahan
        </h1>
        <p class="mt-2 text-slate-400 text-base">
            Kelola alat dan bahan laboratorium
        </p>
    </div>

    <a href="{{ route('items.create') }}"
       class="flex items-center gap-2 bg-violet-600 hover:bg-violet-500 text-white px-5 py-3 rounded-xl font-semibold text-sm shadow-lg shadow-violet-900/40 transition-all duration-200 hover:-translate-y-0.5">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Tambah Item
    </a>

</div>

{{-- Search --}}
<form method="GET" class="mt-8">
    <div class="relative max-w-lg">
        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input
            type="text"
            name="search"
            value="{{ $search }}"
            placeholder="Cari berdasarkan nama atau lokasi..."
            class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-800 border border-slate-700 text-slate-200 placeholder-slate-500 focus:border-violet-500 focus:ring-1 focus:ring-violet-500 text-sm transition"
        >
    </div>
</form>

{{-- ========== ALAT LABORATORIUM ========== --}}
<div class="mt-10 rounded-2xl overflow-hidden border border-slate-700/60 shadow-xl">

    {{-- Section header - blue/purple gradient --}}
    <div class="flex items-center gap-3 px-6 py-5 bg-gradient-to-r from-blue-700 to-violet-700">
        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-white/15">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-white tracking-wide">
            Alat Laboratorium
        </h2>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full bg-slate-900">

            <thead>
                <tr class="border-b border-slate-700/80">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider w-14">No</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Nama Alat</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Lokasi</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider w-24">Satuan</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider w-28">Total Stok</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider w-36">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-700/50">

            @forelse($tools as $toolName => $toolGroup)

                @php
                    $stock   = $toolGroup->sum('stock');
                    $minimum = $toolGroup->sum('minimum_stock');
                    $isLow   = $stock <= $minimum;
                @endphp

                <tr class="align-top hover:bg-slate-800/50 transition-colors">

                    <td class="px-6 py-5 text-slate-400 text-sm">
                        {{ $loop->iteration }}
                    </td>

                    <td class="px-6 py-5">
                        <div class="font-semibold text-slate-100 text-sm">
                            {{ $toolName }}
                        </div>
                        <div class="mt-1.5">
                            @if($isLow)
                                <span class="inline-flex items-center gap-1 bg-red-500/15 text-red-400 border border-red-500/25 px-2.5 py-0.5 rounded-lg text-xs font-semibold tracking-wide">
                                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                    STOK RENDAH
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-emerald-500/15 text-emerald-400 border border-emerald-500/25 px-2.5 py-0.5 rounded-lg text-xs font-semibold tracking-wide">
                                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    STOK AMAN
                                </span>
                            @endif
                        </div>
                    </td>

                    <td class="px-6 py-5">
                        @foreach($toolGroup as $tool)
                            <div class="{{ !$loop->last ? 'mb-2' : '' }} text-sm text-slate-300">
                                <span class="text-slate-400">{{ $tool->room->name }}</span>
                                <span class="text-slate-600 mx-1">—</span>
                                {{ $tool->location }}
                                <span class="ml-1 text-xs text-slate-500 font-medium">({{ $tool->stock }})</span>
                            </div>
                        @endforeach
                    </td>

                    <td class="px-6 py-5 text-slate-300 text-sm">
                        {{ $toolGroup->first()->unit }}
                    </td>

                    <td class="px-6 py-5">
                        <span class="text-lg font-bold {{ $isLow ? 'text-amber-400' : 'text-emerald-400' }}">
                            {{ $stock }}
                        </span>
                    </td>

                    <td class="px-6 py-5">
                        <div class="flex flex-col gap-2">
                            @foreach($toolGroup as $tool)
                                <div class="flex gap-1.5 items-center">

                                    <a href="{{ route('items.edit', $tool) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-700/60 hover:bg-blue-600/20 text-slate-300 hover:text-blue-400 border border-slate-600/50 hover:border-blue-500/40 text-xs font-medium transition-all duration-150">
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
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-700/60 hover:bg-red-600/20 text-slate-300 hover:text-red-400 border border-slate-600/50 hover:border-red-500/40 text-xs font-medium transition-all duration-150">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                <path d="M10 11v6M14 11v6"/>
                                                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>

                                </div>
                            @endforeach
                        </div>
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="6" class="text-center py-14 text-slate-500">
                        <svg class="mx-auto mb-3 w-10 h-10 text-slate-700" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18"/>
                        </svg>
                        Tidak ada data alat
                    </td>
                </tr>

            @endforelse

            </tbody>

        </table>
    </div>

</div>

{{-- ========== BAHAN LABORATORIUM ========== --}}
<div class="mt-8 rounded-2xl overflow-hidden border border-slate-700/60 shadow-xl">

    {{-- Section header - indigo/slate gradient (menggantikan hijau terang) --}}
    <div class="flex items-center gap-3 px-6 py-5 bg-gradient-to-r from-indigo-800 to-slate-700">
        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-white/15">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10 2v7.31"/>
                <path d="M14 9.3V1.99"/>
                <path d="M8.5 2h7"/>
                <path d="M14 9.3a6.5 6.5 0 1 1-4 0"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-white tracking-wide">
            Bahan Laboratorium
        </h2>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full bg-slate-900">

            <thead>
                <tr class="border-b border-slate-700/80">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider w-14">No</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Nama Bahan</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Lokasi</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider w-24">Satuan</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider w-28">Total Stok</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider w-36">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-700/50">

            @forelse($materials as $materialName => $materialGroup)

                @php
                    $stock   = $materialGroup->sum('stock');
                    $minimum = $materialGroup->sum('minimum_stock');
                    $isLow   = $stock <= $minimum;
                @endphp

                <tr class="align-top hover:bg-slate-800/50 transition-colors">

                    <td class="px-6 py-5 text-slate-400 text-sm">
                        {{ $loop->iteration }}
                    </td>

                    <td class="px-6 py-5">
                        <div class="font-semibold text-slate-100 text-sm">
                            {{ $materialName }}
                        </div>
                        <div class="mt-1.5">
                            @if($isLow)
                                <span class="inline-flex items-center gap-1 bg-red-500/15 text-red-400 border border-red-500/25 px-2.5 py-0.5 rounded-lg text-xs font-semibold tracking-wide">
                                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                    STOK RENDAH
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-emerald-500/15 text-emerald-400 border border-emerald-500/25 px-2.5 py-0.5 rounded-lg text-xs font-semibold tracking-wide">
                                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    STOK AMAN
                                </span>
                            @endif
                        </div>
                    </td>

                    <td class="px-6 py-5">
                        @foreach($materialGroup as $material)
                            <div class="{{ !$loop->last ? 'mb-2' : '' }} text-sm text-slate-300">
                                <span class="text-slate-400">{{ $material->room->name }}</span>
                                <span class="text-slate-600 mx-1">—</span>
                                {{ $material->location }}
                                <span class="ml-1 text-xs text-slate-500 font-medium">({{ $material->stock }})</span>
                            </div>
                        @endforeach
                    </td>

                    <td class="px-6 py-5 text-slate-300 text-sm">
                        {{ $materialGroup->first()->unit }}
                    </td>

                    <td class="px-6 py-5">
                        <span class="text-lg font-bold {{ $isLow ? 'text-amber-400' : 'text-emerald-400' }}">
                            {{ $stock }}
                        </span>
                    </td>

                    <td class="px-6 py-5">
                        <div class="flex flex-col gap-2">
                            @foreach($materialGroup as $material)
                                <div class="flex gap-1.5 items-center">

                                    <a href="{{ route('items.edit', $material) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-700/60 hover:bg-blue-600/20 text-slate-300 hover:text-blue-400 border border-slate-600/50 hover:border-blue-500/40 text-xs font-medium transition-all duration-150">
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
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-700/60 hover:bg-red-600/20 text-slate-300 hover:text-red-400 border border-slate-600/50 hover:border-red-500/40 text-xs font-medium transition-all duration-150">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                <path d="M10 11v6M14 11v6"/>
                                                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>

                                </div>
                            @endforeach
                        </div>
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="6" class="text-center py-14 text-slate-500">
                        <svg class="mx-auto mb-3 w-10 h-10 text-slate-700" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M10 2v7.31"/><path d="M14 9.3V1.99"/><path d="M8.5 2h7"/>
                            <path d="M14 9.3a6.5 6.5 0 1 1-4 0"/>
                        </svg>
                        Tidak ada data bahan
                    </td>
                </tr>

            @endforelse

            </tbody>

        </table>
    </div>

</div>

@endsection