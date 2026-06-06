@extends('admin.layouts.app')

@section('title', 'Analisis Stok Barang')

@push('styles')
<style>
    .dash-wrapper { --bg-card: #161b27; --border: #252d3d; --text-primary: #e8edf5; --text-muted: #6b7a99; font-family: 'Inter', sans-serif; color: var(--text-primary); padding: 1.5rem; }
    .card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; padding: 1.5rem; }
    
    /* Grid 4 Widget */
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
    .stat-box { border: 1px solid var(--border); padding: 1.25rem; border-radius: 12px; display: flex; flex-direction: column; justify-content: space-between; min-height: 120px; }
    
    .label { color: var(--text-muted); font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem; }
    .stat-val { font-size: 1.5rem; font-weight: 800; color: #fff; }
    .trend-text { font-size: 0.75rem; color: var(--text-muted); margin-top: auto; }
    
    .charts-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem; }
    .chart-container { height: 320px; width: 100%; position: relative; }
</style>
@endpush

@section('content')
<div class="dash-wrapper">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800;">Analisis Stok Barang</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Monitoring penggunaan alat dan bahan laboratorium</p>
        </div>
        <form method="GET">
            <select name="item_id" onchange="this.form.submit()" style="background:var(--bg-card); border:1px solid var(--border); color:white; padding:0.6rem; border-radius:8px;">
                <option value="">-- Pilih Alat --</option>
                @foreach($all_tools as $tool)
                    <option value="{{ $tool->id }}" {{ request('item_id') == $tool->id ? 'selected' : '' }}>{{ $tool->name }}</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Widget Statistik --}}
    <div class="stats-grid">
        @php $dataExists = !empty($selected_tool); @endphp
        <div class="card stat-box">
            <div class="label">Stok Saat Ini</div>
            <div class="stat-val">{{ $dataExists ? $selected_tool->stock : '-' }}</div>
        </div>
        <div class="card stat-box">
            <div class="label">Frekuensi Dipinjam</div>
            <div class="stat-val">{{ $dataExists ? $frekuensi_pakai : '-' }}</div>
        </div>
        <div class="card stat-box">
            <div class="label">Prediksi Habis</div>
            <div class="stat-val">{{ $dataExists ? $prediksi_habis : '-' }}</div>
            <div class="trend-text">{{ $dataExists ? $sisa_hari . ' hari tersisa' : '-' }}</div>
        </div>
        <div class="card stat-box">
            <div class="label">Status Stok</div>
            <div class="stat-val" style="color: {{ ($status_stok ?? '') == 'Aman' ? '#22c55e' : '#f59e0b' }}">{{ $dataExists ? $status_stok : '-' }}</div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="charts-row">
        <div class="card">
            <div style="font-weight: 700; margin-bottom: 0.5rem;">Tren Penggunaan</div>
            <p class="label" style="margin-bottom: 1rem;">Visualisasi peminjaman vs pengembalian</p>
            <div class="chart-container">
                <canvas id="lineChart"></canvas>
            </div>
        </div>
        <div class="card">
            <div style="font-weight: 700; margin-bottom: 0.5rem;">Distribusi per Kategori</div>
            <p class="label" style="margin-bottom: 1rem;">Frekuensi peminjaman bulan ini</p>
            <div class="chart-container">
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Bar Chart: Penggunaan Bulanan --}}
    <div class="card" style="margin-top: 1.5rem;">
        <div style="font-weight: 700; margin-bottom: 0.5rem;">Penggunaan Bulanan (Total Semua Barang)</div>
        <p class="label" style="margin-bottom: 1rem;">Total peminjaman barang berdasarkan data transaksi asli</p>
        <div style="height: 300px;">
            <canvas id="barChart"></canvas>
        </div>
    </div>

    <script>
        new Chart(document.getElementById('barChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($monthlyData->pluck('bulan')) !!},
                datasets: [{
                    label: 'Total Unit Dipinjam',
                    data: {!! json_encode($monthlyData->pluck('total')) !!},
                    backgroundColor: '#8b5cf6',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, grid: { color: '#252d3d' } }, x: { grid: { display: false } } }
            }
        });
    </script>

    {{-- Tabel Ringkasan Stok Barang --}}
    <div class="card" style="margin-top: 1.5rem;">
        <div style="margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.25rem; font-weight: 700;">Ringkasan Stok Barang</h2>
            <p style="color: var(--text-muted); font-size: 0.85rem;">Status dan prediksi semua item laboratorium berdasarkan data penggunaan terkini.</p>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="color: var(--text-muted); font-size: 0.8rem; border-bottom: 1px solid var(--border);">
                        <th style="padding: 1rem;">Nama Barang</th>
                        <th style="padding: 1rem;">Stok</th>
                        <th style="padding: 1rem;">Min Stok</th>
                        <th style="padding: 1rem;">Rata-rata/Hari</th>
                        <th style="padding: 1rem;">Prediksi Habis</th>
                        <th style="padding: 1rem;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($all_tools as $item)
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 1rem; font-weight: 500;">{{ $item->name }}</td>
                        <td style="padding: 1rem;">{{ $item->stock }}</td>
                        <td style="padding: 1rem;">{{ $item->minimum_stock }}</td> <td style="padding: 1rem;">{{ $item->avg_usage }}</td>
                        <td style="padding: 1rem;">{{ $item->prediction_date }}</td>
                        <td style="padding: 1rem;">
                            @php $isLow = $item->stock <= $item->minimum_stock; @endphp
                            <span style="color: {{ $isLow ? '#ef4444' : '#22c55e' }}; font-weight: 600;">
                                {{ $isLow ? 'Perlu Tindakan' : 'Aman' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    Chart.defaults.color = '#e8edf5';
    Chart.defaults.font.family = "'Inter', sans-serif";

    // Line Chart
    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [
                { 
                    label: 'Permintaan', 
                    data: {!! json_encode($chartPinjam) !!}, 
                    borderColor: '#4f7cff', 
                    backgroundColor: 'rgba(79,124,255,0.1)', 
                    tension: 0.3, borderWidth: 3 
                },
                { 
                    label: 'Pengembalian', 
                    data: {!! json_encode($chartKembali) !!}, 
                    borderColor: '#22c55e', // Warna hijau
                    backgroundColor: 'rgba(34,197,94,0.1)', 
                    tension: 0.3, borderWidth: 3 
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } } },
            scales: { 
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 } // Memastikan angka di sumbu Y adalah bilangan bulat
                }
            }
        }
    });

    // Pie Chart
    new Chart(document.getElementById('pieChart'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($pieLabels) !!},
            datasets: [{ 
                data: {!! json_encode($pieData) !!}, 
                backgroundColor: ['#4f7cff', '#8b5cf6', '#22c55e', '#f59e0b', '#6b7a99'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true, 
            maintainAspectRatio: false,
            plugins: { 
                legend: { 
                    position: 'bottom', 
                    labels: { 
                        color: '#e8edf5', 
                        usePointStyle: true, 
                        padding: 20,
                        font: { size: 12 }
                    } 
                } 
            }
        }
    });

    // Bar Chart untuk Penggunaan Bulanan
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($monthlyData->pluck('bulan')) !!}, // Ambil dari Controller
            datasets: [{
                label: 'Total Peminjaman',
                data: {!! json_encode($monthlyData->pluck('total')) !!}, // Ambil dari Controller
                backgroundColor: '#8b5cf6',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { grid: { color: '#252d3d' }, ticks: { precision: 0 } }, x: { grid: { display: false } } }
        }
    });
</script>
@endpush