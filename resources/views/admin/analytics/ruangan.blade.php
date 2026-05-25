@extends('admin.layouts.app')

@section('content')
<div class="mb-10">
    <h1 class="text-5xl font-bold text-gray-900">Analisis Ruangan</h1>
    <p class="mt-3 text-gray-500 text-xl">Monitoring pemakaian ruangan laboratorium</p>
</div>

<form method="GET" class="mb-8" id="filterForm">
    <select 
        name="room_id" 
        id="room_id"
        onchange="this.form.submit()" 
        class="w-full max-w-xl rounded-2xl border-gray-300 shadow-sm focus:border-indigo-500 p-4"
    >
        <option value="">-- Pilih Ruangan --</option>
        @foreach($all_rooms as $room)
            <option 
                value="{{ $room->id }}" 
                {{ request('room_id') == $room->id ? 'selected' : '' }}
            >
                {{ $room->name }} (Kapasitas: {{ $room->capacity }})
            </option>
        @endforeach
    </select>
</form>

@if($selected_room)
<!-- STATISTIK CARD -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
    <div class="bg-white p-8 rounded-3xl shadow">
        <p class="text-gray-500">Tingkat Okupansi</p>
        <h2 class="text-4xl font-bold text-indigo-600 mt-2">{{ $occupancy }}%</h2>
        <p class="text-sm text-gray-400 mt-1">30 hari terakhir</p>
    </div>
    
    <div class="bg-white p-8 rounded-3xl shadow">
        <p class="text-gray-500">Total Peminjaman</p>
        <h2 class="text-4xl font-bold text-orange-600 mt-2">{{ $total_peminjaman }} Sesi</h2>
        <p class="text-sm text-gray-400 mt-1">Seumur hidup</p>
    </div>
    
    <div class="bg-white p-8 rounded-3xl shadow">
        <p class="text-gray-500">Jam Terpadat</p>
        <h2 class="text-4xl font-bold text-teal-600 mt-2">{{ $jam_terpadat }}</h2>
        <p class="text-sm text-gray-400 mt-1">Paling sering dipinjam</p>
    </div>
    
    <div class="bg-white p-8 rounded-3xl shadow">
        <p class="text-gray-500">Hari Terpadat</p>
        <h2 class="text-4xl font-bold text-purple-600 mt-2">{{ $hari_terpadat }}</h2>
        <p class="text-sm text-gray-400 mt-1">Berdasarkan data historis</p>
    </div>
</div>

<!-- REKOMENDASI AI -->
@if(isset($hasilAi) && $hasilAi['status'] == 'penuh')
<div class="bg-blue-50 border-l-4 border-blue-400 p-6 rounded-2xl mb-10">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <svg class="h-8 w-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="ml-4">
            <p class="text-sm font-medium text-blue-800">Rekomendasi AI</p>
            <p class="text-lg font-bold text-blue-900">
                @if($hasilAi['rekomendasi_jam'])
                    Jam {{ $hasilAi['rekomendasi_jam'] }}:00 tersedia
                @else
                    Ruangan penuh sepanjang hari
                @endif
            </p>
        </div>
    </div>
</div>
@endif

<!-- ========================================== -->
<!-- SATU GRAFIK: BAR CHART TREN PENGGUNAAN -->
<!-- ========================================== -->
<div class="bg-white rounded-3xl shadow p-8">
    <h2 class="text-3xl font-bold text-gray-800 mb-2">
        Tren Penggunaan Ruangan
    </h2>
    <p class="text-gray-500 mb-6">
        Grafik peminjaman ruangan <span class="font-semibold text-indigo-600">{{ $selected_room->name }}</span> (30 hari terakhir)
    </p>
    
    @if($chartLabels->isNotEmpty() && $chartData->isNotEmpty())
        <canvas id="roomChart" class="w-full h-96"></canvas>
    @else
        <div class="text-center py-20 text-gray-500">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <p>Belum ada data peminjaman untuk ruangan ini</p>
            <p class="text-sm">Data akan muncul setelah ada peminjaman</p>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let roomChart;

function initRoomChart() {
    const ctx = document.getElementById('roomChart');
    if (!ctx) return;
    
    const labels = @json($chartLabels);
    const data = @json($chartData);
    
    if (labels.length === 0 || data.length === 0) return;
    
    if (roomChart) {
        roomChart.destroy();
    }
    
    roomChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Peminjaman',
                data: data,
                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1,
                borderRadius: 8,
                barPercentage: 0.7,
                categoryPercentage: 0.8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            let value = context.raw;
                            return `${label}: ${value} kali`;
                        }
                    }
                },
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Peminjaman (kali)'
                    },
                    ticks: {
                        stepSize: 1,
                        precision: 0
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Tanggal'
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45,
                        autoSkip: true,
                        maxTicksLimit: 10
                    }
                }
            }
        }
    });
}

// Inisialisasi saat halaman load
document.addEventListener('DOMContentLoaded', function() {
    initRoomChart();
});
</script>

@else
<div class="bg-white rounded-3xl shadow p-20 text-center">
    <svg class="w-20 h-20 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
    </svg>
    <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Ruangan Dipilih</h3>
    <p class="text-gray-500">Silakan pilih ruangan dari dropdown di atas</p>
</div>
@endif

@endsection