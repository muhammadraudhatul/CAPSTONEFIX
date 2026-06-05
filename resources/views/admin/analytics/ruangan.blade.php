@extends('admin.layouts.app')
@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold">Analisis Ruangan</h1>
    <form method="GET" class="mt-4">
        <select name="room_id" onchange="this.form.submit()" class="w-full max-w-md rounded-xl border-gray-300 p-3">
            <option value="">-- Pilih Ruangan --</option>
            @foreach($all_rooms as $room)
                <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
            @endforeach
        </select>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow">
        <p class="text-gray-500">Total Pinjam</p>
        <h2 class="text-2xl font-bold">{{ $total_peminjaman }}</h2>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow">
        <p class="text-gray-500">Okupansi</p>
        <h2 class="text-2xl font-bold">{{ $occupancy }}%</h2>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow">
        <p class="text-gray-500">Jam Terpadat</p>
        <h2 class="text-2xl font-bold">{{ $jam_terpadat }}</h2>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow">
        <p class="text-gray-500">Durasi Rata-rata</p>
        <h2 class="text-2xl font-bold">{{ $rata_rata_durasi }} Jam</h2>
    </div>
</div>

<div class="bg-white p-6 rounded-2xl shadow">
    <canvas id="mainChart" height="100"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('mainChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{ label: 'Tren Peminjaman Ruangan', data: {!! json_encode($chartData) !!}, backgroundColor: '#8b5cf6' }]
        }
    });
</script>
@endsection