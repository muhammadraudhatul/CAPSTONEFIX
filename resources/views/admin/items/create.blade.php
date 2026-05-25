@extends('admin.layouts.app')

@section('content')

<div class="max-w-4xl">

    <!-- Header -->
    <div class="flex items-center justify-between">

        <div>

            <h1 class="text-5xl font-bold text-gray-900">
                Tambah Item
            </h1>

            <p class="mt-3 text-gray-500 text-xl">
                Tambahkan alat atau bahan laboratorium
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
              action="{{ route('items.store') }}">

            @csrf

            <!-- Nama -->
            <div>

                <label class="block text-lg font-semibold text-gray-700 mb-3">

                    Nama Item

                </label>

                <input
                    type="text"
                    name="name"
                    required
                    class="w-full rounded-2xl border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                    placeholder="Masukkan nama item"
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

                    <option value="">
                        Pilih Jenis
                    </option>

                    <option value="tool">
                        Alat
                    </option>

                    <option value="material">
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

                    <option value="">
                        Pilih Ruangan
                    </option>

                    @foreach($rooms as $room)

                        <option value="{{ $room->id }}">

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
                    class="w-full rounded-2xl border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                    placeholder="Rak A1 / Lemari B / dll"
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
                    class="w-full rounded-2xl border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                    placeholder="Unit / Botol / Box / Liter"
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
                    placeholder="Deskripsi item..."
                ></textarea>

            </div>

            <!-- Button -->
            <button
                type="submit"
                class="mt-10 bg-gradient-to-r from-green-600 to-teal-700 text-white px-8 py-4 rounded-2xl font-semibold shadow hover:opacity-90 transition"
            >

                Simpan Item

            </button>

        </form>

    </div>

</div>

@endsection