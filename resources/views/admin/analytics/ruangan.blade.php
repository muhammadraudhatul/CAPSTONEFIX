@extends('admin.layouts.app')

@section('title', 'Analisis Ruangan')

@push('styles')
<style>
    .dash-wrapper {
        --bg-base: #0d1117;
        --bg-card: #161b27;
        --bg-card-hover: #1c2333;
        --border: #252d3d;
        --text-primary: #e8edf5;
        --text-muted: #6b7a99;
        --text-dim: #4a5568;
        --accent-blue: #4f7cff;
        --accent-purple: #8b5cf6;
        --accent-green: #22c55e;
        --accent-orange: #f59e0b;
        --accent-red: #ef4444;
        --accent-teal: #2dd4bf;
        --positive: #22c55e;
        --negative: #ef4444;
        --chart-grid: rgba(255,255,255,0.05);
        
        font-family: 'Inter', sans-serif;
        color: var(--text-primary);
        padding: 1.5rem;
    }
    
    .card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.5rem;
        transition: all 0.2s ease;
    }
    
    .card:hover {
        background: var(--bg-card-hover);
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .stat-box {
        border: 1px solid var(--border);
        padding: 1.25rem;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 120px;
    }
    
    .label {
        color: var(--text-muted);
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
    }
    
    .stat-val {
        font-size: 1.5rem;
        font-weight: 800;
        color: #fff;
    }
    
    .stat-sub {
        font-size: 0.7rem;
        color: var(--text-muted);
        margin-top: 0.5rem;
    }
    
    .chart-container {
        height: 320px;
        width: 100%;
        position: relative;
    }
    
    .chart-container-sm {
        height: 280px;
        width: 100%;
        position: relative;
    }
    
    .text-positive {
        color: var(--positive);
    }
    
    .text-negative {
        color: var(--negative);
    }
    
    .tooltip-info {
        cursor: help;
    }
    
    .charts-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-top: 1.5rem;
    }
    
    @media (max-width: 768px) {
        .charts-row {
            grid-template-columns: 1fr;
        }
    }
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
            <div class="label tooltip-info" title="Total peminjaman ruangan ini sepanjang waktu">Total Peminjaman</div>
            <div class="stat-val">{{ number_format($total_peminjaman) }}</div>
        </div>
        <div class="card stat-box">
            <div class="label tooltip-info" title="Persentase peminjaman ruangan ini dibanding semua ruangan dalam 30 hari terakhir">
                Okupansi (30 Hari)
            </div>
            <div class="stat-val">{{ $occupancy }}%</div>
            <div class="stat-sub">
                @php
                    $total30Days = \App\Models\Borrowing::where('borrow_date', '>=', now()->subDays(30))->count();
                @endphp
                {{ $total30Days > 0 ? 'Dari ' . number_format($total30Days) . ' total peminjaman' : 'Belum ada data' }}
            </div>
        </div>
        <div class="card stat-box">
            <div class="label tooltip-info" title="Range waktu yang paling sering dipinjam dalam 30 hari terakhir">
                Jam Terpadat
            </div>
            <div class="stat-val" style="font-size: 1.2rem;">{{ $jam_terpadat }}</div>
        </div>
        <div class="card stat-box">
            <div class="label tooltip-info" title="Durasi rata-rata peminjaman dalam 30 hari terakhir">
                Durasi Rata-rata
            </div>
            <div class="stat-val">{{ $rata_rata_durasi }} <small style="font-size: 0.8rem;">Jam</small></div>
            <div class="stat-sub">
                {{ $rata_rata_durasi > 0 ? 'Rata-rata per peminjaman' : 'Belum ada data' }}
            </div>
        </div>
    </div>

    {{-- Main Chart: Tren Peminjaman --}}
    <div class="card">
        <div style="font-weight: 700; margin-bottom: 0.5rem;">Tren Peminjaman Ruangan</div>
        <p class="label" style="margin-bottom: 1rem;">Frekuensi peminjaman dalam 30 hari terakhir</p>
        <div class="chart-container">
            <canvas id="mainChart"></canvas>
        </div>
    </div>

    {{-- NEW: Distribusi Jam Penggunaan --}}
    <div class="charts-row">
        <div class="card" style="grid-column: span 2;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <div style="font-weight: 700; margin-bottom: 0.25rem;">Distribusi Jam Penggunaan</div>
                    <p class="label" style="margin-bottom: 0;">Frekuensi peminjaman berdasarkan jam mulai dalam 30 hari terakhir</p>
                </div>
                <div style="display: flex; gap: 1rem; font-size: 0.7rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <div style="width: 12px; height: 12px; background: #4f7cff; border-radius: 2px;"></div>
                        <span style="color: var(--text-muted);">Jam Normal</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <div style="width: 12px; height: 12px; background: #2dd4bf; border-radius: 2px;"></div>
                        <span style="color: var(--text-muted);">Jam Padat (Non-Terpadat)</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <div style="width: 12px; height: 12px; background: #f59e0b; border-radius: 2px;"></div>
                        <span style="color: var(--text-muted);">Jam Terpadat</span>
                    </div>
                </div>
            </div>
            <div class="chart-container-sm">
                <canvas id="timeSlotChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    Chart.defaults.color = '#e8edf5';
    
    // ============================================
    // CHART 1: Tren Peminjaman (Line Chart)
    // ============================================
    const ctx1 = document.getElementById('mainChart').getContext('2d');
    const gradient1 = ctx1.createLinearGradient(0, 0, 0, 300);
    gradient1.addColorStop(0, 'rgba(139, 92, 246, 0.4)');
    gradient1.addColorStop(1, 'rgba(139, 92, 246, 0.05)');
    
    new Chart(document.getElementById('mainChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{ 
                label: 'Jumlah Peminjaman', 
                data: {!! json_encode($chartData) !!}, 
                borderColor: '#8b5cf6',
                borderWidth: 3,
                backgroundColor: gradient1,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#8b5cf6',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true, 
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: { 
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15
                    }
                },
                tooltip: {
                    backgroundColor: '#1a1f2e',
                    titleColor: '#e8edf5',
                    bodyColor: '#6b7a99',
                    borderColor: '#252d3d',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            let value = context.raw;
                            return `Peminjaman: ${value} kali`;
                        }
                    }
                }
            },
            scales: { 
                y: { 
                    beginAtZero: true, 
                    grid: { color: 'rgba(255,255,255,0.05)' },
                    ticks: { 
                        stepSize: 1,
                        precision: 0
                    },
                    title: {
                        display: true,
                        text: 'Jumlah Peminjaman',
                        color: '#6b7a99'
                    }
                }, 
                x: { 
                    grid: { display: false },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45,
                        autoSkip: true,
                        maxTicksLimit: 10
                    },
                    title: {
                        display: true,
                        text: 'Tanggal',
                        color: '#6b7a99'
                    }
                } 
            }
        }
    });

    // ============================================
    // CHART 2: Distribusi Jam Penggunaan (Bar Chart)
    // ============================================
    const timeSlotLabels = {!! json_encode($timeSlotLabels) !!};
    const timeSlotData = {!! json_encode($timeSlotData) !!};
    const peakHour = {{ $timeSlotPeakHour ?? 'null' }};
    
    // Buat warna berdasarkan jam (biru tua normal, biru muda padat, kuning terpadat)
    const backgroundColors = timeSlotLabels.map((label, index) => {
        const hour = parseInt(label.split(':')[0]);
        
        // Jam terpadat (peak hour) -> Kuning
        if (peakHour !== null && hour === peakHour) {
            return '#f59e0b'; // Kuning / accent-orange
        }
        
        // Jam padat (selain terpadat, dengan frekuensi > 0) -> Biru Muda
        if (timeSlotData[index] > 0) {
            return '#2dd4bf'; // Biru Muda / accent-teal
        }
        
        // Jam normal (frekuensi 0) -> Biru Tua
        return '#4f7cff'; // Biru Tua / accent-blue
    });
    
    // Border color (slightly darker than background)
    const borderColors = backgroundColors.map(color => {
        if (color === '#f59e0b') return '#d97706';
        if (color === '#2dd4bf') return '#14b8a6';
        return '#3b6ad7';
    });
    
    new Chart(document.getElementById('timeSlotChart'), {
        type: 'bar',
        data: {
            labels: timeSlotLabels,
            datasets: [{
                label: 'Jumlah Peminjaman',
                data: timeSlotData,
                backgroundColor: backgroundColors,
                borderColor: borderColors,
                borderWidth: 1,
                borderRadius: 6,
                barPercentage: 0.7,
                categoryPercentage: 0.85,
                barThickness: 'flex',
                maxBarThickness: 50
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#1a1f2e',
                    titleColor: '#e8edf5',
                    bodyColor: '#6b7a99',
                    borderColor: '#252d3d',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            let value = context.raw;
                            let label = context.label;
                            let hour = parseInt(label.split(':')[0]);
                            
                            let status = '';
                            if (peakHour !== null && hour === peakHour) {
                                status = ' ⭐ Jam Terpadat';
                            } else if (value > 0) {
                                status = ' 🟢 Jam Aktif';
                            } else {
                                status = ' ⚪ Jam Sepi';
                            }
                            
                            return `Peminjaman: ${value} kali${status}`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255,255,255,0.05)'
                    },
                    ticks: {
                        stepSize: 1,
                        precision: 0,
                        callback: function(value) {
                            return value + ' kali';
                        }
                    },
                    title: {
                        display: true,
                        text: 'Jumlah Peminjaman',
                        color: '#6b7a99',
                        font: { size: 11 }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45,
                        font: { size: 10 }
                    },
                    title: {
                        display: true,
                        text: 'Jam Mulai',
                        color: '#6b7a99',
                        font: { size: 11 }
                    }
                }
            },
            layout: {
                padding: {
                    top: 10,
                    bottom: 5,
                    left: 5,
                    right: 5
                }
            }
        }
    });
</script>
@endpush