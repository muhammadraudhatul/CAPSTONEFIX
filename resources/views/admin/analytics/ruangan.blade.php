@extends('admin.layouts.app')

@section('title', 'Analisis Ruangan')

@push('styles')
<style>
    .dash-wrapper { --bg-card: #161b27; --border: #252d3d; --text-primary: #e8edf5; --text-muted: #6b7a99; font-family: 'Inter', sans-serif; color: var(--text-primary); padding: 1.5rem; }
    .card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; padding: 1.5rem; }
    
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
    .stat-box { border: 1px solid var(--border); padding: 1.25rem; border-radius: 12px; display: flex; flex-direction: column; justify-content: space-between; min-height: 120px; }
    
    .label { color: var(--text-muted); font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem; }
    .stat-val { font-size: 1.5rem; font-weight: 800; color: #fff; }
    .chart-container { height: 320px; width: 100%; position: relative; }

    .text-positive { color: var(--accent-green); }
    .text-negative { color: var(--accent-red); }
</style>
@endpush

@section('content')
<div class="dash-wrapper">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800;">Analisis Ruangan</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Monitoring tingkat penggunaan dan kepadatan ruangan</p>
        </div>
        <form method="GET">
            <select name="room_id" onchange="this.form.submit()" style="background:var(--bg-card); border:1px solid var(--border); color:white; padding:0.6rem; border-radius:8px;">
                <option value="">-- Pilih Ruangan --</option>
                @foreach($all_rooms as $room)
                    <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Widget Statistik --}}
    <div class="stats-grid">
        <div class="card stat-box">
            <div class="label">Total Peminjaman</div>
            <div class="stat-val">{{ $total_peminjaman }}</div>
        </div>
        <div class="card stat-box">
            <div class="label">Okupansi</div>
            <div class="stat-val">{{ $occupancy }}%</div>
        </div>
        <div class="card stat-box">
            <div class="label">Jam Terpadat</div>
            <div class="stat-val">{{ $jam_terpadat }}</div>
        </div>
        <div class="card stat-box">
            <div class="label">Durasi Rata-rata</div>
            <div class="stat-val">{{ $rata_rata_durasi }} <small style="font-size: 0.8rem;">Jam</small></div>
        </div>
    </div>

    {{-- Main Chart --}}
    <div class="card">
        <div style="font-weight: 700; margin-bottom: 0.5rem;">Tren Peminjaman Ruangan</div>
        <p class="label" style="margin-bottom: 1rem;">Frekuensi peminjaman dalam 30 hari terakhir</p>
        <div class="chart-container">
            <canvas id="mainChart"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    Chart.defaults.color = '#e8edf5';
    new Chart(document.getElementById('mainChart'), {
        type: 'line', // Gunakan line chart
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{ 
                label: 'Jumlah Peminjaman', 
                data: {!! json_encode($chartData) !!}, 
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(139, 92, 246, 0.2)', // Warna latar untuk efek Area
                fill: true, // Mengaktifkan efek Area
                tension: 0.4 // Melengkungkan garis
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, grid: { color: '#252d3d' } }, x: { grid: { display: false } } },
            plugins: { legend: { display: false } }
        }
    });
</script>
@endpush