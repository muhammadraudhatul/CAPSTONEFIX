@extends('admin.layouts.app')

@section('content')

<div class="max-w-4xl">

    <!-- Header -->
    <div class="flex items-center justify-between">

        <div>

            <h1 class="text-5xl font-bold text-gray-900">
                Edit Item
            </h1>

            <p class="mt-3 text-gray-500 text-xl">
                Perbarui data alat atau bahan laboratorium
            </p>

        </div>

        <a href="{{ route('items.index') }}"
           class="text-purple-600 font-semibold hover:underline">

            ← Kembali

        </a>

    </div>

    <!-- Card -->
    <div class="mt-10 bg-white rounded-3xl shadow p-10">

        <form method="POST"
              action="{{ route('items.update', $item) }}">

            @csrf
            @method('PUT')

            <!-- Nama -->
            <div>

                <label class="block text-lg font-semibold text-gray-700 mb-3">

                    Nama Item

                </label>

                <input
                    type="text"
                    name="name"
                    required
                    value="{{ $item->name }}"
                    class="w-full rounded-2xl border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                >

            </div>

            <!-- Type -->
            <div class="mt-8">

                <label class="block text-lg font-semibold text-gray-700 mb-3">

                    Jenis

                </label>

                <select
                    name="type"
                    required
                    class="w-full rounded-2xl border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                >

                    <option value="tool"
                        {{ $item->type == 'tool' ? 'selected' : '' }}>
                        Alat
                    </option>

                    <option value="material"
                        {{ $item->type == 'material' ? 'selected' : '' }}>
                        Bahan
                    </option>

                </select>

            </div>

            <!-- Room -->
            <div class="mt-8">

                <label class="block text-lg font-semibold text-gray-700 mb-3">

                    Ruangan

                </label>

                <select
                    name="room_id"
                    required
                    class="w-full rounded-2xl border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                >

                    @foreach($rooms as $room)

                        <option value="{{ $room->id }}"
                            {{ $item->room_id == $room->id ? 'selected' : '' }}>

                            {{ $room->name }}

                        </option>

                    @endforeach

                </select>

            </div>

            <!-- Lokasi -->
            <div class="mt-8">

                <label class="block text-lg font-semibold text-gray-700 mb-3">

                    Lokasi Penyimpanan

                </label>

                <input
                    type="text"
                    name="location"
                    required
                    value="{{ $item->location }}"
                    class="w-full rounded-2xl border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                >

            </div>

            <!-- Unit -->
            <div class="mt-8">

                <label class="block text-lg font-semibold text-gray-700 mb-3">

                    Satuan

                </label>

                <input
                    type="text"
                    name="unit"
                    required
                    value="{{ $item->unit }}"
                    class="w-full rounded-2xl border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                >

            </div>

            <!-- Stock -->
            <div class="grid grid-cols-2 gap-6 mt-8">

                <div>

                    <label class="block text-lg font-semibold text-gray-700 mb-3">

                        Jumlah Stok

                    </label>

                    <input
                        type="number"
                        name="stock"
                        required
                        min="0"
                        value="{{ $item->stock }}"
                        class="w-full rounded-2xl border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                    >

                </div>

                <div>

                    <label class="block text-lg font-semibold text-gray-700 mb-3">

                        Minimum Stok

                    </label>

                    <input
                        type="number"
                        name="minimum_stock"
                        required
                        min="0"
                        value="{{ $item->minimum_stock }}"
                        class="w-full rounded-2xl border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                    >

                </div>

            </div>

            <!-- Description -->
            <div class="mt-8">

                <label class="block text-lg font-semibold text-gray-700 mb-3">

                    Deskripsi

                </label>

                <textarea
                    name="description"
                    rows="5"
                    class="w-full rounded-2xl border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                >{{ $item->description }}</textarea>

            </div>

            <!-- Button -->
            <button
                type="submit"
                class="mt-10 bg-gradient-to-r from-green-600 to-teal-700 text-white px-8 py-4 rounded-2xl font-semibold shadow hover:opacity-90 transition"
            >

                Update Item

            </button>

        </form>

    </div>

</div>

@endsection