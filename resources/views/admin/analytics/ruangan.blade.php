@extends('admin.layouts.app')

@section('content')
<div class="mb-10">
    <h1 class="text-5xl font-bold text-gray-900">Analisis Ruangan</h1>
    <p class="mt-3 text-gray-500 text-xl">Monitoring pemakaian ruangan laboratorium</p>
</div>

<form method="GET" class="mb-8">
    <select name="room_id" onchange="this.form.submit()" class="w-full max-w-xl rounded-2xl border-gray-300 shadow-sm focus:border-indigo-500 p-4">
        <option value="">-- Pilih Ruangan --</option>
        @foreach($all_rooms as $room)
            <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>
                {{ $room->name }}
            </option>
        @endforeach
    </select>
</form>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <div class="bg-white p-8 rounded-3xl shadow">
        <p class="text-gray-500">Tingkat Okupansi</p>
        <h2 class="text-4xl font-bold text-indigo-600 mt-2">{{ $occupancy }}%</h2>
    </div>
    <div class="bg-white p-8 rounded-3xl shadow">
        <p class="text-gray-500">Total Peminjaman</p>
        <h2 class="text-4xl font-bold text-orange-600 mt-2">{{ $total_peminjaman }} Sesi</h2>
    </div>
    <div class="bg-white p-8 rounded-3xl shadow">
        <p class="text-gray-500">Jam Terpadat</p>
        <h2 class="text-4xl font-bold text-teal-600 mt-2">{{ $jam_terpadat }}</h2>
    </div>
</div>

<div class="bg-white rounded-3xl shadow p-8">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Okupansi: {{ $selected_room->name ?? 'Pilih Ruangan' }}</h2>
    <canvas id="roomChart" class="w-full h-80"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const ctx = document.getElementById('roomChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: @json($chartLabels),
        datasets: [{
            label: 'Penggunaan Ruangan',
            data: @json($chartData),
            borderWidth: 2
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