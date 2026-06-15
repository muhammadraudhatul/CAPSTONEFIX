@extends('admin.layouts.app')

@section('title', 'Analisis Ruangan')

@push('styles')
<style>
    .dash-wrapper {
        --bg-base:       #0d1117;
        --bg-card:       #161b27;
        --bg-card-hover: #1c2333;
        --border:        #252d3d;
        --text-primary:  #e8edf5;
        --text-muted:    #6b7a99;
        --text-dim:      #4a5568;
        --accent-blue:   #4f7cff;
        --accent-purple: #8b5cf6;
        --accent-green:  #22c55e;
        --accent-orange: #f59e0b;
        --accent-red:    #ef4444;
        --accent-teal:   #2dd4bf;
        --positive:      #22c55e;
        --negative:      #ef4444;

        font-family: 'Inter', sans-serif;
        font-size: 14px;
        line-height: 1.6;
        color: var(--text-primary);
        padding: 2rem 1.75rem;
        box-sizing: border-box;
    }

    .dash-wrapper *, .dash-wrapper *::before, .dash-wrapper *::after {
        box-sizing: border-box;
    }

    /* ─── HEADER ─── */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .page-title {
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        line-height: 1.2;
        margin: 0;
        padding: 0;
        color: var(--text-primary);
    }
    
    .page-subtitle {
        color: var(--text-muted);
        font-size: 0.875rem;
        margin: 0.2rem 0 0 0;
        padding: 0;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .timestamp {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 0.4rem 0.85rem;
        font-size: 0.8rem;
        color: var(--text-muted);
        white-space: nowrap;
        align-self: center;
    }
    /* ─── CUSTOM ROOM DROPDOWN ─── */
    .room-select-wrap {
        position: relative;
        background: var(--bg-card);
        border: 1px solid var(--border);
        color: var(--text-primary);
        padding: 0.4rem 0.85rem;
        border-radius: 8px;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        user-select: none;
        transition: border-color 0.2s;
        min-width: 180px;
    }
    .room-select-wrap:hover {
        border-color: #2f3a52;
    }
    .room-select-wrap.open {
        border-color: var(--accent-blue);
    }
    .room-select-label {
        flex: 1;
        font-size: 0.85rem;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .room-chevron {
        transition: transform 0.2s;
        flex-shrink: 0;
        color: var(--text-muted);
    }
    .room-select-wrap.open .room-chevron {
        transform: rotate(180deg);
    }
    .room-dropdown-menu {
        display: none;
        position: absolute;
        top: calc(100% + 6px);
        right: 0;
        min-width: 100%;
        background: #1c2333;
        border: 1px solid #2f3a52;
        border-radius: 10px;
        padding: 0.35rem;
        z-index: 9999;
        box-shadow: 0 8px 24px rgba(0,0,0,0.45);
        max-height: 260px;
        overflow-y: auto;
    }
    .room-dropdown-menu.open {
        display: block;
    }
    .room-dropdown-item {
        padding: 0.5rem 0.85rem;
        border-radius: 7px;
        font-size: 0.82rem;
        color: var(--text-muted);
        transition: background 0.15s, color 0.15s;
        cursor: pointer;
        white-space: nowrap;
    }
    .room-dropdown-item:hover {
        background: rgba(79,124,255,0.1);
        color: var(--text-primary);
    }
    .room-dropdown-item.active {
        background: rgba(79,124,255,0.18);
        color: #7da8ff;
        font-weight: 600;
    }

    /* ─── STAT CARDS ─── */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.25rem 1.5rem 1.4rem;
        position: relative;
        overflow: hidden;
        transition: border-color 0.2s, background 0.2s;
        animation: dashFadeUp 0.35s ease both;
    }
    .stat-card:hover {
        border-color: #2f3a52;
        background: var(--bg-card-hover);
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 2px;
        border-radius: 16px 16px 0 0;
    }
    .stat-card.blue::before   { background: var(--accent-blue); }
    .stat-card.purple::before { background: var(--accent-purple); }
    .stat-card.teal::before   { background: var(--accent-teal); }
    .stat-card.orange::before { background: var(--accent-orange); }

    .stat-card:nth-child(1) { animation-delay: 0.04s; }
    .stat-card:nth-child(2) { animation-delay: 0.08s; }
    .stat-card:nth-child(3) { animation-delay: 0.12s; }
    .stat-card:nth-child(4) { animation-delay: 0.16s; }

    .stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 0 0 0.75rem 0;
    }
    .stat-label-tag {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 700;
    }
    .stat-label-tag.blue   { color: var(--accent-blue); }
    .stat-label-tag.purple { color: var(--accent-purple); }
    .stat-label-tag.teal   { color: var(--accent-teal); }
    .stat-label-tag.orange { color: var(--accent-orange); }

    .stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .stat-icon.blue   { background: rgba(79,124,255,0.18);  color: var(--accent-blue); }
    .stat-icon.purple { background: rgba(139,92,246,0.18);  color: var(--accent-purple); }
    .stat-icon.teal   { background: rgba(45,212,191,0.18);  color: var(--accent-teal); }
    .stat-icon.orange { background: rgba(245,158,11,0.18);  color: var(--accent-orange); }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -0.02em;
        line-height: 1.2;
        margin: 0 0 0.3rem 0;
        display: block;
    }
    .stat-value small {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-muted);
        margin-left: 0.1rem;
    }
    .stat-sub {
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 400;
        margin: 0;
        display: block;
    }

    /* ─── CARDS ─── */
    .dash-wrapper .card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.5rem;
        transition: border-color 0.2s, background 0.2s;
        animation: dashFadeUp 0.35s ease both;
    }
    .dash-wrapper .card:hover {
        border-color: #2f3a52;
        background: var(--bg-card-hover);
    }
    .card-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 0.2rem 0;
        padding: 0;
        letter-spacing: -0.01em;
    }
    .card-subtitle {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: var(--text-muted);
        margin: 0 0 1.25rem 0;
    }

    /* ─── CHART CONTAINERS ─── */
    .chart-wrap              { position: relative; width: 100%; }
    .chart-wrap canvas       { display: block; width: 100% !important; }
    .chart-container         { height: 280px; width: 100%; position: relative; }
    .chart-container-sm      { height: 240px; width: 100%; position: relative; }
    .chart-container-xs      { height: 190px; width: 100%; position: relative; }

    /* ─── TREND CARD ─── */
    .trend-card { margin-bottom: 1.5rem; }
    .trend-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.25rem;
    }

    /* ─── CHART LEGEND ─── */
    .chart-legend {
        display: flex;
        gap: 1.25rem;
        flex-wrap: wrap;
        margin-top: 0.75rem;
    }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.72rem;
        color: var(--text-muted);
    }
    .legend-line {
        width: 20px;
        height: 2px;
        border-radius: 2px;
    }

    /* ─── BOTTOM GRID ─── */
    .charts-row-wide {
        display: grid;
        grid-template-columns: 5fr 7fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .charts-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    /* ─── DONUT LEGEND ─── */
    .donut-legend {
        margin-top: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.55rem;
    }
    .donut-legend-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.78rem;
    }
    .donut-legend-left {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        color: var(--text-primary);
        min-width: 120px;
    }
    .donut-dot {
        width: 10px; height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .donut-bar-wrap {
        flex: 1;
        margin: 0 0.75rem;
        height: 3px;
        background: var(--border);
        border-radius: 2px;
        overflow: hidden;
    }
    .donut-bar-fill { height: 100%; border-radius: 2px; }
    .donut-pct {
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--text-muted);
        min-width: 38px;
        text-align: right;
    }

    /* ─── TIME SLOT LEGEND ─── */
    .ts-legend {
        display: flex;
        gap: 1.25rem;
        flex-wrap: wrap;
        margin-bottom: 0.75rem;
    }
    .ts-legend-item {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.72rem;
        color: var(--text-muted);
    }
    .ts-legend-dot {
        width: 10px; height: 10px;
        border-radius: 2px;
        flex-shrink: 0;
    }

    /* ─── TABLE ─── */
    .table-wrap { overflow-x: auto; border-radius: 12px; }
    .dash-wrapper table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.865rem;
    }
    .dash-wrapper thead th {
        text-align: left;
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-muted);
        padding: 0.65rem 1rem;
        border-bottom: 1px solid var(--border);
        background: transparent;
    }
    .dash-wrapper tbody tr {
        border-bottom: 1px solid rgba(37,45,61,0.6);
        transition: background 0.15s;
    }
    .dash-wrapper tbody tr:last-child { border-bottom: none; }
    .dash-wrapper tbody tr:hover { background: rgba(255,255,255,0.025); }
    .dash-wrapper tbody td {
        padding: 0.8rem 1rem;
        color: var(--text-primary);
        vertical-align: middle;
        border: none;
        background: transparent;
    }
    .dash-wrapper tbody td.muted { color: var(--text-muted); }
    .room-name { font-weight: 700; }

    /* ─── STATUS BADGE ─── */
    .dash-wrapper .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.65rem;
        border-radius: 999px;
        line-height: 1.4;
        white-space: nowrap;
    }
    .badge-approved        { background: rgba(79,124,255,0.15);  color: #7da8ff; }
    .badge-completed,
    .badge-returned        { background: rgba(34,197,94,0.15);   color: #4ade80; }
    .badge-waiting_return  { background: rgba(6,182,212,0.15);   color: #22d3ee; }
    .badge-pending         { background: rgba(245,158,11,0.15);  color: #fbbf24; }
    .badge-rejected,
    .badge-cancelled       { background: rgba(239,68,68,0.15);   color: #f87171; }

    /* ─── EMPTY STATE ─── */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--text-muted);
    }
    .empty-state svg { margin-bottom: 0.75rem; opacity: 0.4; }
    .empty-state p { font-size: 0.85rem; }
    .empty-row td {
        text-align: center;
        color: var(--text-muted);
        padding: 2rem;
        font-size: 0.85rem;
    }

    /* ─── ANIMATE IN ─── */
    @keyframes dashFadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ─── RESPONSIVE ─── */
    @media (max-width: 1100px) {
        .stats-grid        { grid-template-columns: repeat(2, 1fr); }
        .charts-row-wide   { grid-template-columns: 1fr; }
        .charts-row        { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .stats-grid  { grid-template-columns: 1fr; }
        .page-title  { font-size: 1.3rem; }
    }
</style>
@endpush

@section('content')

@php
    // ── Warna untuk donut chart (per ruangan) ──
    $donutColors = ['#2dd4bf','#8b5cf6','#22c55e','#f59e0b','#ef4444','#4f7cff','#f472b6','#fb923c'];

    // ── Distribusi pemakaian per ruangan (30 hari terakhir) ──
    $roomUsage = \App\Models\Borrowing::where('borrow_date', '>=', now()->subDays(30))
        ->selectRaw('room_id, COUNT(*) as total')
        ->groupBy('room_id')
        ->with('room')
        ->get()
        ->map(fn($b) => [
            'name'  => $b->room->name ?? 'Tidak diketahui',
            'total' => $b->total,
        ])
        ->sortByDesc('total')
        ->values();

    $totalRoomUsage = $roomUsage->sum('total');

    // ── Kepadatan per hari (30 hari terakhir, sesuai room jika dipilih) ──
    $dayQuery = \App\Models\Borrowing::where('borrow_date', '>=', now()->subDays(30))
        ->whereNotNull('day');

    if (isset($selected_room) && $selected_room) {
        $dayQuery->where('room_id', $selected_room->id);
    }

    $dayRaw = $dayQuery->selectRaw('day, COUNT(*) as total')
        ->groupBy('day')
        ->get()
        ->keyBy('day');

    $dayOrder  = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
    $dayLabels = ['Sen','Sel','Rab','Kam','Jum','Sab','Min'];
    $dayTotals = array_map(fn($d) => $dayRaw->get($d)?->total ?? 0, $dayOrder);

    // ── Riwayat peminjaman terbaru ──
    $recentQuery = \App\Models\Borrowing::with(['room','user'])
        ->orderBy('created_at', 'desc')
        ->limit(5);

    if (isset($selected_room) && $selected_room) {
        $recentQuery->where('room_id', $selected_room->id);
    }

    $recentBorrowings = $recentQuery->get();

    // ── Status badge helper ──
    $statusMap = [
        'approved'       => ['label' => 'approved',      'icon' => '◷', 'class' => 'badge-approved'],
        'completed'      => ['label' => 'completed',         'icon' => '✓', 'class' => 'badge-completed'],
        'returned'       => ['label' => 'returned',    'icon' => '✓', 'class' => 'badge-returned'],
        'waiting_return' => ['label' => 'waiting_return','icon' => '⏳','class' => 'badge-waiting_return'],
        'pending'        => ['label' => 'Pending',         'icon' => '⏳', 'class' => 'badge-pending'],
        'rejected'       => ['label' => 'rejected',         'icon' => '✕', 'class' => 'badge-rejected'],
        'cancelled'      => ['label' => 'cancelled',      'icon' => '✕', 'class' => 'badge-cancelled'],
    ];
@endphp

<div class="dash-wrapper">

    {{-- ─── HEADER ─── --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Analisis Ruangan</span></h1>
            <p class="page-subtitle">Monitoring tingkat penggunaan dan kepadatan ruangan</p>
        </div>
        <div class="header-right">
            <div class="timestamp">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
                {{ now()->isoFormat('D MMM YYYY, HH:mm') }}
            </div>
            <form method="GET" id="roomForm">
                <input type="hidden" name="room_id" id="roomIdInput" value="{{ request('room_id') }}">
                <div class="room-select-wrap" id="roomDropdown">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                        <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                    </svg>
                    <span class="room-select-label" id="roomDropdownLabel">
                        {{ $selected_room ? $selected_room->name : '-- Pilih Ruangan --' }}
                    </span>
                    <svg class="room-chevron" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>

                    <div class="room-dropdown-menu" id="roomDropdownMenu">
                        <div class="room-dropdown-item {{ !request('room_id') ? 'active' : '' }}"
                             data-value="">-- Pilih Ruangan --</div>
                        @foreach($all_rooms as $room)
                            <div class="room-dropdown-item {{ request('room_id') == $room->id ? 'active' : '' }}"
                                 data-value="{{ $room->id }}">{{ $room->name }}</div>
                        @endforeach
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ─── STAT CARDS ─── --}}
    <div class="stats-grid">

        <div class="stat-card blue">
            <div class="stat-top">
                <div class="stat-label-tag blue">Total Peminjaman</div>
                <div class="stat-icon blue">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                        <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                    </svg>
                </div>
            </div>
            <span class="stat-value">{{ number_format($total_peminjaman) }}</span>
            <span class="stat-sub">
                {{ $selected_room ? 'Sepanjang waktu untuk ruangan ini' : 'Pilih ruangan untuk detail' }}
            </span>
        </div>

        <div class="stat-card purple">
            <div class="stat-top">
                <div class="stat-label-tag purple">Okupansi</div>
                <div class="stat-icon purple">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
            </div>
            <span class="stat-value">{{ $occupancy }}<small>%</small></span>
            <span class="stat-sub">Dari total peminjaman 30 hari terakhir</span>
        </div>

        <div class="stat-card teal">
            <div class="stat-top">
                <div class="stat-label-tag teal">Jam Terpadat</div>
                <div class="stat-icon teal">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
            </div>
            <span class="stat-value" style="font-size:1.4rem; line-height:1.3;">
                {{ $jam_terpadat !== '-' ? $jam_terpadat : '-' }}
            </span>
            <span class="stat-sub">Range waktu paling sering dipinjam</span>
        </div>

        <div class="stat-card orange">
            <div class="stat-top">
                <div class="stat-label-tag orange">Durasi Rata-rata</div>
                <div class="stat-icon orange">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
            </div>
            <span class="stat-value">
                {{ $rata_rata_durasi > 0 ? $rata_rata_durasi : '-' }}<small>{{ $rata_rata_durasi > 0 ? ' Jam' : '' }}</small>
            </span>
            <span class="stat-sub">
                {{ $rata_rata_durasi > 0 ? 'Rata-rata per peminjaman' : 'Belum ada data' }}
            </span>
        </div>

    </div>

    {{-- ─── TREN PEMINJAMAN ─── --}}
    <div class="card trend-card">
        <div class="trend-card-header">
            <div>
                <div class="card-title">Tren Peminjaman Ruangan</div>
                <div class="card-subtitle">Frekuensi peminjaman dalam 30 hari terakhir</div>
            </div>
        </div>

        @if($selected_room && count($chartData) > 0 && array_sum($chartData) > 0)
            <div class="chart-container">
                <canvas id="mainChart"></canvas>
            </div>
            <div class="chart-legend">
                <div class="legend-item">
                    <div class="legend-line" style="background:#4f7cff;"></div>
                    Realisasi Peminjaman
                </div>
            </div>
        @else
            <div class="empty-state">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
                <p>{{ $selected_room ? 'Tidak ada data peminjaman dalam 30 hari terakhir' : 'Pilih ruangan untuk melihat tren peminjaman' }}</p>
            </div>
        @endif
    </div>

    {{-- ─── ROW: Donut + Distribusi Jam ─── --}}
    <div class="charts-row-wide">

        {{-- Distribusi Pemakaian per Ruangan --}}
        <div class="card">
            <div class="card-title">Distribusi Penggunaan</div>
            <div class="card-subtitle">Berdasarkan Ruangan (30 Hari Terakhir)</div>

            @if($roomUsage->count() > 0)
                <div class="chart-container-xs" style="display:flex; align-items:center; justify-content:center;">
                    <canvas id="donutChart"></canvas>
                </div>
                <div class="donut-legend">
                    @foreach($roomUsage->take(5) as $i => $room)
                        @php
                            $pct   = $totalRoomUsage > 0 ? round(($room['total'] / $totalRoomUsage) * 100, 1) : 0;
                            $color = $donutColors[$i % count($donutColors)];
                        @endphp
                        <div class="donut-legend-row">
                            <div class="donut-legend-left">
                                <div class="donut-dot" style="background:{{ $color }};"></div>
                                {{ Str::limit($room['name'], 18) }}
                            </div>
                            <div class="donut-bar-wrap">
                                <div class="donut-bar-fill" style="width:{{ $pct }}%; background:{{ $color }};"></div>
                            </div>
                            <div class="donut-pct">{{ $pct }}%</div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <p>Belum ada data peminjaman dalam 30 hari terakhir</p>
                </div>
            @endif
        </div>

        {{-- Distribusi Jam Penggunaan --}}
        <div class="card">
            <div class="card-title">Distribusi Jam Penggunaan</div>
            <div class="card-subtitle">Pola Frekuensi Peminjaman per Jam</div>

            @if($selected_room && isset($timeSlotLabels) && count($timeSlotLabels) > 0 && array_sum($timeSlotData) > 0)
                <div class="ts-legend">
                    <div class="ts-legend-item">
                        <div class="ts-legend-dot" style="background:#f59e0b;"></div> Jam Terpadat
                    </div>
                    <div class="ts-legend-item">
                        <div class="ts-legend-dot" style="background:#4f7cff;"></div> Jam Aktif
                    </div>
                    <div class="ts-legend-item">
                        <div class="ts-legend-dot" style="background:#2d3f5c;"></div> Jam Sepi
                    </div>
                </div>
                <div class="chart-container-sm">
                    <canvas id="timeSlotChart"></canvas>
                </div>
            @else
                <div class="empty-state">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>
                    </svg>
                    <p>{{ $selected_room ? 'Tidak ada data distribusi jam dalam 30 hari terakhir' : 'Pilih ruangan untuk melihat distribusi jam' }}</p>
                </div>
            @endif
        </div>

    </div>

    {{-- ─── ROW: Kepadatan per Hari + Riwayat Peminjaman ─── --}}
    <div class="charts-row">

        {{-- Kepadatan per Hari --}}
        <div class="card">
            <div class="card-title">Kepadatan per Hari</div>
            <div class="card-subtitle">
                Distribusi Sesi dalam Seminggu
                @if($selected_room) — {{ $selected_room->name }} @endif
            </div>

            @if(array_sum($dayTotals) > 0)
                <div class="chart-container-sm">
                    <canvas id="dayChart"></canvas>
                </div>
            @else
                <div class="empty-state">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <p>Belum ada data peminjaman dalam 30 hari terakhir</p>
                </div>
            @endif
        </div>

        {{-- Riwayat Peminjaman Terbaru --}}
        <div class="card" style="overflow:auto;">
            <div class="card-title">Riwayat Peminjaman Terbaru</div>
            <div class="card-subtitle">
                5 Transaksi Terakhir
                @if($selected_room) — {{ $selected_room->name }} @endif
            </div>

            @if($recentBorrowings->count() > 0)
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Ruangan</th>
                                <th>Peminjam</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBorrowings as $b)
                            @php
                                $sKey   = strtolower($b->status ?? '');
                                $sInfo  = $statusMap[$sKey] ?? ['label' => ucfirst($sKey), 'icon' => '•', 'class' => 'badge-pending'];
                            @endphp
                            <tr>
                                <td class="room-name">{{ $b->room->name ?? '-' }}</td>
                                <td class="muted">{{ $b->user->name ?? '-' }}</td>
                                <td class="muted" style="white-space:nowrap;">
                                    {{ \Carbon\Carbon::parse($b->borrow_date)->isoFormat('D MMM YYYY') }}
                                </td>
                                <td class="muted" style="white-space:nowrap; font-size:0.78rem;">
                                    {{ $b->time_slot ?? '-' }}
                                </td>
                                <td>
                                    <span class="badge {{ $sInfo['class'] }}">
                                        {{ $sInfo['icon'] }} {{ $sInfo['label'] }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                    <p>Belum ada riwayat peminjaman</p>
                </div>
            @endif
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    Chart.defaults.color      = '#6b7a99';
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size   = 12;

    const gridColor = 'rgba(255,255,255,0.05)';
    const tickColor = '#4a5568';

    // ─────────────────────────────────────────
    // CHART 1: Tren Peminjaman
    // ─────────────────────────────────────────
    @if($selected_room && array_sum($chartData) > 0)
    (function() {
        const ctx  = document.getElementById('mainChart').getContext('2d');
        const grad = ctx.createLinearGradient(0, 0, 0, 280);
        grad.addColorStop(0, 'rgba(79,124,255,0.18)');
        grad.addColorStop(1, 'rgba(79,124,255,0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_map(fn($d) => \Carbon\Carbon::parse($d)->isoFormat('D MMM'), $chartLabels)) !!},
                datasets: [{
                    label: 'Peminjaman',
                    data: {!! json_encode($chartData) !!},
                    borderColor: '#4f7cff',
                    borderWidth: 2.5,
                    backgroundColor: grad,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#4f7cff',
                    pointBorderColor: '#0d1117',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1c2333',
                        titleColor: '#e8edf5',
                        bodyColor: '#6b7a99',
                        borderColor: '#252d3d',
                        borderWidth: 1,
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: { label: ctx => ` ${ctx.raw} peminjaman` }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor },
                        ticks: { stepSize: 1, precision: 0, color: tickColor },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { autoSkip: true, maxTicksLimit: 12, font: { size: 10 }, color: tickColor },
                        border: { display: false }
                    }
                }
            }
        });
    })();
    @endif

    // ─────────────────────────────────────────
    // CHART 2: Donut – Distribusi per Ruangan
    // ─────────────────────────────────────────
    @if($roomUsage->count() > 0)
    (function() {
        const roomNames  = {!! json_encode($roomUsage->take(5)->pluck('name')->values()) !!};
        const roomTotals = {!! json_encode($roomUsage->take(5)->pluck('total')->values()) !!};
        const colors     = {!! json_encode(array_slice($donutColors, 0, $roomUsage->take(5)->count())) !!};

        new Chart(document.getElementById('donutChart'), {
            type: 'doughnut',
            data: {
                labels: roomNames,
                datasets: [{
                    data: roomTotals,
                    backgroundColor: colors,
                    borderColor: '#161b27',
                    borderWidth: 3,
                    hoverBorderWidth: 0,
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1c2333',
                        titleColor: '#e8edf5',
                        bodyColor: '#6b7a99',
                        borderColor: '#252d3d',
                        borderWidth: 1,
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: { label: ctx => ` ${ctx.label}: ${ctx.raw} peminjaman` }
                    }
                }
            }
        });
    })();
    @endif

    // ─────────────────────────────────────────
    // CHART 3: Bar – Distribusi Jam
    // ─────────────────────────────────────────
    @if($selected_room && isset($timeSlotLabels) && array_sum($timeSlotData) > 0)
    (function() {
        const labels   = {!! json_encode($timeSlotLabels) !!};
        const data     = {!! json_encode($timeSlotData) !!};
        const peakHour = {{ $timeSlotPeakHour ?? 'null' }};

        const colors = labels.map((label, i) => {
            const h = parseInt(label.split(':')[0]);
            if (peakHour !== null && h === peakHour) return '#f59e0b';
            if (data[i] > 0) return '#4f7cff';
            return '#2d3f5c';
        });

        new Chart(document.getElementById('timeSlotChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Peminjaman',
                    data: data,
                    backgroundColor: colors,
                    borderRadius: 5,
                    borderSkipped: false,
                    barPercentage: 0.6,
                    categoryPercentage: 0.8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1c2333',
                        titleColor: '#e8edf5',
                        bodyColor: '#6b7a99',
                        borderColor: '#252d3d',
                        borderWidth: 1,
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: ctx => {
                                const h   = parseInt(ctx.label.split(':')[0]);
                                const tag = (peakHour !== null && h === peakHour) ? ' ⭐ Jam Terpadat' : '';
                                return ` ${ctx.raw} peminjaman${tag}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor },
                        ticks: { stepSize: 1, precision: 0, color: tickColor },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 9 }, maxRotation: 0, color: tickColor },
                        border: { display: false }
                    }
                }
            }
        });
    })();
    @endif

    // ─────────────────────────────────────────
    // CHART 4: Bar – Kepadatan per Hari
    // ─────────────────────────────────────────
    @if(array_sum($dayTotals) > 0)
    (function() {
        new Chart(document.getElementById('dayChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($dayLabels) !!},
                datasets: [{
                    label: 'Jumlah Peminjaman',
                    data: {!! json_encode(array_values($dayTotals)) !!},
                    backgroundColor: 'rgba(45,212,191,0.80)',
                    borderRadius: 5,
                    borderSkipped: false,
                    barPercentage: 0.55,
                    categoryPercentage: 0.8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1c2333',
                        titleColor: '#e8edf5',
                        bodyColor: '#6b7a99',
                        borderColor: '#252d3d',
                        borderWidth: 1,
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: { label: ctx => ` ${ctx.raw} peminjaman` }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor },
                        ticks: { stepSize: 1, precision: 0, color: tickColor },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: tickColor },
                        border: { display: false }
                    }
                }
            }
        });
    })();
    @endif

    // ─────────────────────────────────────────
    // CUSTOM ROOM DROPDOWN
    // ─────────────────────────────────────────
    (function() {
        const wrap  = document.getElementById('roomDropdown');
        const menu  = document.getElementById('roomDropdownMenu');
        const label = document.getElementById('roomDropdownLabel');
        const input = document.getElementById('roomIdInput');
        const form  = document.getElementById('roomForm');

        if (!wrap) return;

        // Toggle buka/tutup
        wrap.addEventListener('click', function(e) {
            e.stopPropagation();
            const isOpen = menu.classList.contains('open');
            menu.classList.toggle('open', !isOpen);
            wrap.classList.toggle('open', !isOpen);
        });

        // Pilih item
        document.querySelectorAll('.room-dropdown-item').forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.stopPropagation();
                input.value        = this.dataset.value;
                label.textContent  = this.textContent.trim();
                document.querySelectorAll('.room-dropdown-item').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
                menu.classList.remove('open');
                wrap.classList.remove('open');
                form.submit();
            });
        });

        // Tutup jika klik di luar
        document.addEventListener('click', function() {
            menu.classList.remove('open');
            wrap.classList.remove('open');
        });
    })();
</script>
@endpush