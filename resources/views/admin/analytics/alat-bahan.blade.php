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

<!-- STATISTIK CARD - DIPERBARUI -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">

    <div class="bg-white p-8 rounded-3xl shadow">
        <p class="text-gray-500">Stok Saat Ini</p>
        <h2 class="text-4xl font-bold mt-2">
            {{ $selected_tool->stock ?? 0 }}
        </h2>
        <p class="text-sm text-gray-400 mt-1">Unit</p>
    </div>

    <div class="bg-white p-8 rounded-3xl shadow">
        <p class="text-gray-500">Frekuensi Dipinjam</p>
        <h2 class="text-4xl font-bold text-blue-600 mt-2">
            {{ $frekuensi_pakai }}x
        </h2>
        <p class="text-sm text-gray-400 mt-1">Total peminjaman</p>
    </div>

    <div class="bg-white p-8 rounded-3xl shadow">
        <p class="text-gray-500">Prediksi Habis</p>
        <h2 class="text-4xl font-bold text-red-500 mt-2">
            {{ $prediksi_habis }}
        </h2>
        <p class="text-sm text-gray-400 mt-1">Perkiraan tanggal</p>
    </div>

    <div class="bg-white p-8 rounded-3xl shadow">
        <p class="text-gray-500">Status Stok</p>
        <h2 class="text-4xl font-bold mt-2 
            {{ $status_stok == 'Kritis' ? 'text-red-600' : ($status_stok == 'Peringatan' ? 'text-yellow-600' : 'text-green-600') }}">
            {{ $status_stok }}
        </h2>
        <p class="text-sm text-gray-400 mt-1">
            @if($sisa_hari > 0 && $sisa_hari < 999)
                Sisa {{ $sisa_hari }} hari
            @else
                Stok mencukupi
            @endif
        </p>
    </div>

</div>

<!-- TAMBAHAN: REKOMENDASI PEMBELIAN (jika ada) -->
@if(isset($rekomendasi_pembelian) && $rekomendasi_pembelian > 0)
<div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-2xl mb-10">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <svg class="h-8 w-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div class="ml-4">
            <p class="text-sm font-medium text-yellow-800">Rekomendasi Pembelian</p>
            <p class="text-lg font-bold text-yellow-900">{{ $rekomendasi_pesan }}</p>
            <p class="text-sm text-yellow-700 mt-1">Estimasi kebutuhan {{ $periode_hari ?? 30 }} hari ke depan</p>
        </div>
    </div>
</div>
@endif

<!-- GRAFIK TREN PENGGUNAAN -->
<div class="bg-white rounded-3xl shadow p-8 mb-10">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">
            Tren Penggunaan {{ $selected_tool->name ?? '' }}
        </h2>
        
        <!-- Tombol refresh data AI -->
        <button onclick="refreshAIData()" class="px-4 py-2 bg-indigo-500 text-white rounded-xl hover:bg-indigo-600 transition">
            <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Refresh Prediksi AI
        </button>
    </div>
    <canvas id="itemChart" class="w-full h-80"></canvas>
</div>

<!-- TAMBAHAN: INFORMASI AI SERVICE -->
@if(isset($ai_service_status))
<div class="bg-gray-50 rounded-2xl p-4 text-center text-sm text-gray-500">
    @if($ai_service_status == 'connected')
        <span class="text-green-500">●</span> AI Service connected
    @else
        <span class="text-yellow-500">●</span> AI Service offline (menggunakan data lokal)
    @endif
    | Last sync: {{ $last_sync ?? now()->format('H:i:s') }}
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation"></script>

<script>
let itemChart;

// Inisialisasi chart
function initChart() {
    const ctx = document.getElementById('itemChart');
    
    if (itemChart) {
        itemChart.destroy();
    }
    
    itemChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Jumlah Dipinjam (Aktual)',
                data: @json($chartData),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `Jumlah: ${context.raw} kali`;
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
                        text: 'Frekuensi Peminjaman'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Tanggal'
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            }
        }
    });
}

// Refresh data dari AI Service via AJAX
async function refreshAIData() {
    const itemId = document.querySelector('select[name="item_id"]').value;
    
    if (!itemId) {
        alert('Pilih alat/bahan terlebih dahulu');
        return;
    }
    
    // Tampilkan loading
    const btn = event.currentTarget;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<svg class="w-5 h-5 inline animate-spin mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Loading...';
    btn.disabled = true;
    
    try {
        // Panggil API untuk prediksi stok
        const response = await fetch('/api/predict/stok', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                item_id: itemId
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Update card dengan data baru
            updateCards(result);
        }
        
    } catch (error) {
        console.error('Error refreshing AI data:', error);
    } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
}

function updateCards(data) {
    // Update card prediksi habis
    const prediksiHabisEl = document.querySelector('.grid .bg-white:nth-child(3) h2');
    if (prediksiHabisEl && data.perkiraan_tanggal_habis) {
        prediksiHabisEl.textContent = data.perkiraan_tanggal_habis;
    }
    
    // Update card status stok
    const statusStokEl = document.querySelector('.grid .bg-white:nth-child(4) h2');
    const statusTextEl = document.querySelector('.grid .bg-white:nth-child(4) .text-sm');
    
    if (statusStokEl && data.status_stok) {
        statusStokEl.textContent = data.status_stok;
        
        // Update warna
        statusStokEl.className = 'text-4xl font-bold mt-2 ';
        if (data.status_stok === 'Kritis') {
            statusStokEl.classList.add('text-red-600');
        } else if (data.status_stok === 'Peringatan') {
            statusStokEl.classList.add('text-yellow-600');
        } else {
            statusStokEl.classList.add('text-green-600');
        }
    }
    
    if (statusTextEl && data.sisa_hari !== undefined) {
        if (data.sisa_hari < 999) {
            statusTextEl.textContent = `Sisa ${data.sisa_hari} hari`;
        } else {
            statusTextEl.textContent = 'Stok mencukupi';
        }
    }
}

// Inisialisasi saat halaman load
document.addEventListener('DOMContentLoaded', function() {
    initChart();
    
    // Auto refresh setiap 5 menit
    setInterval(() => {
        const refreshBtn = document.querySelector('button[onclick="refreshAIData()"]');
        if (refreshBtn) {
            refreshAIData();
        }
    }, 300000);
});
</script>

@endsection