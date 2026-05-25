@extends('admin.layouts.app')

@section('content')

<div class="max-w-3xl">

    <!-- Header -->
    <div class="mb-10">

        <h1 class="text-5xl font-bold text-gray-900">
            Tambah Ruangan
        </h1>

        <p class="text-gray-500 mt-3 text-lg">
            Tambahkan ruangan laboratorium baru
        </p>

    </div>

    <!-- Form -->
    <div class="bg-white rounded-3xl shadow-sm p-10">

        <form method="POST"
              action="{{ route('rooms.store') }}">

            @csrf

            <!-- Nama -->
            <div>

                <label class="block text-lg font-semibold text-gray-700 mb-3">

                    Nama Ruangan

                </label>

                <input
                    type="text"
                    name="name"
                    required
                    placeholder="Contoh: Chemistry Lab A"
                    class="w-full rounded-2xl border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                >

            </div>

            <!-- Kapasitas -->
            <div class="mt-8">

                <label class="block text-lg font-semibold text-gray-700 mb-3">

                    Kapasitas

                </label>

                <input
                    type="number"
                    name="capacity"
                    required
                    min="1"
                    placeholder="Contoh: 30"
                    class="w-full rounded-2xl border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                >

            </div>

            <!-- Buttons -->
            <div class="flex gap-4 mt-10">

                <a href="{{ route('rooms.index') }}"
                   class="px-6 py-4 rounded-2xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">

                    Batal

                </a>

                <button
                    type="submit"
                    class="bg-gradient-to-r from-green-600 to-teal-700 text-white px-8 py-4 rounded-2xl font-semibold shadow hover:opacity-90"
                >

                    Simpan Ruangan

                </button>

            </div>

        </form>

    </div>

</div>

@endsection