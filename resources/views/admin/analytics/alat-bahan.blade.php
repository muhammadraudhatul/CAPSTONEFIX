@extends('admin.layouts.app')
@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold">Analisis Stok Barang</h1>
    <form method="GET" class="mt-4">
        <select name="item_id" onchange="this.form.submit()" class="w-full max-w-md rounded-xl border-gray-300 p-3">
            <option value="">-- Pilih Alat --</option>
            @foreach($all_tools as $tool)
                <option value="{{ $tool->id }}" {{ request('item_id') == $tool->id ? 'selected' : '' }}>{{ $tool->name }}</option>
            @endforeach
        </select>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow">
        <p class="text-gray-500">Stok</p>
        <h2 class="text-2xl font-bold">{{ $selected_tool->stock ?? 0 }}</h2>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow">
        <p class="text-gray-500">Frekuensi</p>
        <h2 class="text-2xl font-bold">{{ $frekuensi_pakai }}x</h2>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow">
        <p class="text-gray-500">Status Stok</p>
        <h2 class="text-2xl font-bold">{{ $status_stok }}</h2>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow">
        <p class="text-gray-500">Sisa Hari</p>
        <h2 class="text-2xl font-bold">{{ $sisa_hari }}</h2>
    </div>
</div>

<div class="bg-white p-6 rounded-2xl shadow">
    <canvas id="mainChart" height="100"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('mainChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{ label: 'Tren Penggunaan', data: {!! json_encode($chartData) !!}, borderColor: '#3b82f6', tension: 0.3 }]
        }
    });
</script>
@endsection