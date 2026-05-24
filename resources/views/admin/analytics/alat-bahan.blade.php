@extends('admin.layouts.app')

@section('content')
<div class="mb-10">
    <h1 class="text-5xl font-bold text-gray-900">
        Analisis Stok Barang
    </h1>

    <p class="mt-3 text-gray-500 text-xl">
        Monitoring penggunaan alat dan bahan laboratorium
    </p>
</div>

<form method="GET" class="mb-8">

    <select
        name="item_id"
        onchange="this.form.submit()"
        class="w-full max-w-xl rounded-2xl border-gray-300 shadow-sm p-4"
    >
        <option value="">
            -- Pilih Alat dan Bahan --
        </option>

        @foreach($all_tools as $tool)

            <option
                value="{{ $tool->id }}"
                {{ request('item_id') == $tool->id ? 'selected' : '' }}
            >
                {{ $tool->name }}
            </option>

        @endforeach

    </select>

</form>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">

    <div class="bg-white p-8 rounded-3xl shadow">

        <p class="text-gray-500">
            Stok Saat Ini
        </p>

        <h2 class="text-4xl font-bold mt-2">
            {{ $selected_tool->stock ?? 0 }}
        </h2>

    </div>

    <div class="bg-white p-8 rounded-3xl shadow">

        <p class="text-gray-500">
            Frekuensi Dipinjam
        </p>

        <h2 class="text-4xl font-bold text-blue-600 mt-2">
            {{ $frekuensi_pakai }}x
        </h2>

    </div>

    <div class="bg-white p-8 rounded-3xl shadow">

        <p class="text-gray-500">
            Prediksi Habis
        </p>

        <h2 class="text-4xl font-bold text-red-500 mt-2">
            {{ $prediksi_habis }}
        </h2>

    </div>

</div>

<div class="bg-white rounded-3xl shadow p-8">

    <h2 class="text-3xl font-bold text-gray-800 mb-6">

        Tren Penggunaan
        {{ $selected_tool->name ?? '' }}

    </h2>

    <canvas id="itemChart" class="w-full h-80"></canvas>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const ctx = document.getElementById('itemChart');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($chartLabels),
        datasets: [{
            label: 'Jumlah Dipinjam',
            data: @json($chartData),
            borderWidth: 2,
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

</script>

@endsection