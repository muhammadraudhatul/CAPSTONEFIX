{{-- Borrowing Management Dashboard --}}
{{-- Controller should pass: $stats, $borrowingTrends, $statusBreakdown, $itemsByType, $roomUtilization, $recentBorrowings --}}

@extends('admin.layouts.app')

@section('title', 'Borrowing Management Dashboard')

@push('styles')
{{-- Inter sudah diload dari layout, tidak perlu load font tambahan --}}
<style>
    /* ── Semua CSS di-scope ke .dash-wrapper agar tidak menimpa layout induk ── */

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
        --chart-grid:    rgba(255,255,255,0.05);


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

    /* ── Header ── */
    .dash-header {
        margin-bottom: 2rem;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .dash-header h1 {
        font-family: 'Inter', sans-serif;
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--text-primary);
        margin: 0;
        padding: 0;
        line-height: 1.2;
    }
    .dash-header p {
        color: var(--text-muted);
        font-size: 0.875rem;
        margin: 0.2rem 0 0 0;
        padding: 0;
    }
    .dash-date {
        font-size: 0.8rem;
        color: var(--text-muted);
        background: var(--bg-card);
        border: 1px solid var(--border);
        padding: 0.4rem 0.85rem;
        border-radius: 8px;
        white-space: nowrap;
        align-self: center;
    }

    /* ── Card ── */
    .dash-wrapper .card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.5rem;
        transition: border-color 0.2s, background 0.2s;
    }
    .dash-wrapper .card:hover {
        border-color: #2f3a52;
        background: var(--bg-card-hover);
    }
    .card-title {
        font-family: 'Inter', sans-serif;
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 1.25rem 0;
        padding: 0;
        letter-spacing: -0.01em;
    }

    /* ── Stats Grid ── */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 1100px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 600px)  { .stats-grid { grid-template-columns: 1fr; } }

    .stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.25rem 1.5rem 1.4rem;
        transition: border-color 0.2s, background 0.2s;
        position: relative;
        overflow: hidden;
    }
    .stat-card:hover {
        border-color: #2f3a52;
        background: var(--bg-card-hover);
    }

    .stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 0 0 0.75rem 0;
        padding: 0;
    }
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
    .stat-icon.blue   { background: rgba(79,124,255,0.18); color: var(--accent-blue); }
    .stat-icon.purple { background: rgba(139,92,246,0.18); color: var(--accent-purple); }
    .stat-icon.green  { background: rgba(34,197,94,0.18);  color: var(--accent-green); }
    .stat-icon.orange { background: rgba(245,158,11,0.18); color: var(--accent-orange); }

    .stat-badge {
        font-size: 0.78rem;
        font-weight: 600;
        padding: 0.2rem 0.55rem;
        border-radius: 999px;
        line-height: 1.4;
    }
    .stat-badge.positive { color: var(--positive); background: rgba(34,197,94,0.12); }
    .stat-badge.negative { color: var(--negative); background: rgba(239,68,68,0.12); }

    .stat-value {
        font-family: 'Inter', sans-serif;
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -0.02em;
        line-height: 1.2;
        margin: 0 0 0.3rem 0;
        padding: 0;
        display: block;
    }
    .stat-label {
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 400;
        margin: 0;
        padding: 0;
        display: block;
    }

    /* ── Charts Row ── */
    .charts-row {
        display: grid;
        grid-template-columns: 1.4fr 1fr;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 900px) { .charts-row { grid-template-columns: 1fr; } }

    .charts-row-equal {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 900px) { .charts-row-equal { grid-template-columns: 1fr; } }

    /* ── Chart Canvas ── */
    .chart-wrap {
        position: relative;
        width: 100%;
    }
    .chart-wrap canvas { display: block; width: 100% !important; }

    /* ── Pie Legend ── */
    .pie-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 1.5rem;
    }
    .pie-canvas-wrap {     width: 220px;
        height: 220px;
        position: relative;
        margin: 0 auto;}
    .pie-legend { flex: 1; min-width: 140px; }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin: 0 0 0.7rem 0;
        padding: 0;
        font-size: 0.83rem;
    }
    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
        display: inline-block;
    }
    .legend-label { color: var(--text-muted); flex: 1; }
    .legend-pct   { font-weight: 600; color: var(--text-primary); }

    /* ── Table ── */
    .table-wrap { overflow-x: auto; border-radius: 12px; }
    .dash-wrapper table {
        width: 100%;
        border-collapse: collapse;
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
        font-size: 0.865rem;
        color: var(--text-primary);
        vertical-align: middle;
        border: none;
        background: transparent;
    }
    .dash-wrapper tbody td.muted { color: var(--text-muted); }

    /* ── Status Badge ── */
    .dash-wrapper .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.65rem;
        border-radius: 999px;
        line-height: 1.4;
    }
    .badge-active    { background: rgba(79,124,255,0.15);  color: #7da8ff; }
    .badge-returned  { background: rgba(34,197,94,0.15);   color: #4ade80; }
    .badge-pending   { background: rgba(245,158,11,0.15);  color: #fbbf24; }
    .badge-cancelled { background: rgba(239,68,68,0.15);   color: #f87171; }

    /* ── Empty state ── */
    .empty-row td {
        text-align: center;
        color: var(--text-muted);
        padding: 2rem;
        font-size: 0.85rem;
    }

    /* ── Animate in ── */
    @keyframes dashFadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .stat-card {
        animation: dashFadeUp 0.35s ease both;
    }
    .dash-wrapper .card {
        animation: dashFadeUp 0.35s ease both;
    }
    .stat-card:nth-child(1) { animation-delay: 0.04s; }
    .stat-card:nth-child(2) { animation-delay: 0.08s; }
    .stat-card:nth-child(3) { animation-delay: 0.12s; }
    .stat-card:nth-child(4) { animation-delay: 0.16s; }
</style>
@endpush

@section('content')
<div class="dash-wrapper">

    {{-- ── Header ── --}}
    <div class="dash-header">
        <div>
            <h1>Borrowing Management Dashboard</h1>
            <p>Overview of room and item borrowings</p>
        </div>
        <span class="dash-date">{{ now()->format('D, d M Y') }}</span>
    </div>

    {{-- ── Stat Cards ── --}}
    <div class="stats-grid">

        {{-- Total Borrowings --}}
        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon blue">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <span class="stat-badge {{ ($stats['borrowings_change'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
                    {{ ($stats['borrowings_change'] ?? 0) >= 0 ? '+' : '' }}{{ number_format($stats['borrowings_change'] ?? 0, 1) }}%
                </span>
            </div>
            <span class="stat-value">{{ number_format($stats['total_borrowings'] ?? 0) }}</span>
            <span class="stat-label">Total Borrowings</span>
        </div>

        {{-- Active Users --}}
        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon purple">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <span class="stat-badge {{ ($stats['users_change'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
                    {{ ($stats['users_change'] ?? 0) >= 0 ? '+' : '' }}{{ number_format($stats['users_change'] ?? 0, 1) }}%
                </span>
            </div>
            <span class="stat-value">{{ number_format($stats['active_users'] ?? 0) }}</span>
            <span class="stat-label">Active Users</span>
        </div>

        {{-- Items in Use --}}
        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon green">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73L13 2.27a2 2 0 0 0-2 0L4 6.27A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73L11 21.73a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                    </svg>
                </div>
                <span class="stat-badge {{ ($stats['items_change'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
                    {{ ($stats['items_change'] ?? 0) >= 0 ? '+' : '' }}{{ number_format($stats['items_change'] ?? 0, 1) }}%
                </span>
            </div>
            <span class="stat-value">{{ number_format($stats['items_in_use'] ?? 0) }}</span>
            <span class="stat-label">Items in Use</span>
        </div>

        {{-- Avg Utilization --}}
        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon orange">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/>
                    </svg>
                </div>
                <span class="stat-badge {{ ($stats['utilization_change'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
                    {{ ($stats['utilization_change'] ?? 0) >= 0 ? '+' : '' }}{{ number_format($stats['utilization_change'] ?? 0, 1) }}%
                </span>
            </div>
            <span class="stat-value">{{ number_format($stats['avg_utilization'] ?? 0, 1) }}%</span>
            <span class="stat-label">Avg Utilization</span>
        </div>

    </div>{{-- /stats-grid --}}

    {{-- ── Row: Borrowing Trends + Status Pie ── --}}
    <div class="charts-row">

        {{-- Borrowing Trends --}}
        <div class="card">
            <div class="card-title">Borrowing Trends</div>
            <div class="chart-wrap" style="height:220px;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        {{-- Borrowings by Status --}}
        <div class="card">
            <div class="card-title">Borrowings by Status</div>
            <div class="pie-wrap">
                <div class="pie-canvas-wrap">
                    <canvas id="statusChart" width="200" height="200"></canvas>
                </div>
                <div class="pie-legend">
                    @php
                        $statusColors = [
                            'completed'      => '#22c55e', // hijau
                            'approved'       => '#4f7cff', // biru
                            'waiting_return' => '#06b6d4', // cyan
                            'pending'        => '#f59e0b', // kuning
                            'rejected'       => '#ef4444', // merah
                            'cancelled'      => '#dc2626', // merah tua
                        ];
                        $total = collect($statusBreakdown ?? [])->sum('count');
                    @endphp
                    @forelse($statusBreakdown ?? [] as $row)
                        @php
                            $pct = $total > 0 ? round(($row->count / $total) * 100) : 0;
                            $color = $statusColors[strtolower($row->status)] ?? '#6b7a99';
                        @endphp
                        <div class="legend-item">
                            <span class="legend-dot" style="background:{{ $color }}"></span>
                            <span class="legend-label">{{ ucfirst($row->status) }}</span>
                            <span class="legend-pct">{{ $pct }}%</span>
                        </div>
                    @empty
                        <p style="color:#6b7a99;font-size:.8rem;margin:0;">No data</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>{{-- /charts-row --}}

    {{-- ── Row: Items by Type + Room Utilization ── --}}
    <div class="charts-row-equal">

        {{-- Items by Type --}}
        <div class="card">
            <div class="card-title">Items by Type</div>
            <div class="chart-wrap" style="height:240px;">
                <canvas id="itemTypeChart"></canvas>
            </div>
        </div>

        {{-- Room Utilization --}}
        <div class="card">
            <div class="card-title">Room Utilization</div>
            <div class="chart-wrap" style="height:240px;">
                <canvas id="roomChart"></canvas>
            </div>
        </div>

    </div>{{-- /charts-row-equal --}}

    {{-- ── Recent Borrowings Table ── --}}
    <div class="card">
        <div class="card-title">Recent Borrowings</div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Room</th>
                        <th>Date</th>
                        <th>Purpose</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentBorrowings ?? [] as $b)
                    <tr>
                        <td class="muted">#{{ $b->id }}</td>
                        <td>{{ $b->user_name ?? '—' }}</td>
                        <td>{{ $b->room_name ?? '—' }}</td>
                        <td class="muted">{{ \Carbon\Carbon::parse($b->borrow_date)->format('Y-m-d') }}</td>
                        <td class="muted" style="max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $b->purpose ?? '—' }}
                        </td>
                        <td>
                            @php
                                $s = strtolower($b->status ?? '');
                                $icon = match($s) {
                                    'active'    => '◷',
                                    'returned'  => '✓',
                                    'pending'   => '⏳',
                                    'cancelled' => '✕',
                                    default     => '•',
                                };
                            @endphp
                            <span class="badge badge-{{ $s }}">{{ $icon }} {{ ucfirst($s) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-row"><td colspan="6">No recent borrowings found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>{{-- /recent borrowings --}}

</div>{{-- /dash-wrapper --}}
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    Chart.defaults.color = '#6b7a99';
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size   = 12;

    const gridColor = 'rgba(255,255,255,0.05)';
    const tickColor = '#4a5568';

    @php
        $chartTrendLabels   = collect($borrowingTrends ?? [])->pluck('month')->values()->toArray();
        $chartTrendBorrowed = collect($borrowingTrends ?? [])->pluck('total_borrowings')->values()->toArray();
        $chartTrendReturned = collect($borrowingTrends ?? [])->pluck('total_returned')->values()->toArray();

        $chartStatusLabels = collect($statusBreakdown ?? [])->map(fn($r) => ucfirst($r->status))->values()->toArray();
        $chartStatusCounts = collect($statusBreakdown ?? [])->pluck('count')->values()->toArray();
        $chartStatusColors = collect($statusBreakdown ?? [])->map(function($r) {
            return [
                'completed'      => '#22c55e',
                'approved'       => '#4f7cff',
                'waiting_return' => '#06b6d4',
                'pending'        => '#f59e0b',
                'rejected'       => '#ef4444',
                'cancelled'      => '#dc2626',
            ][strtolower($r->status)] ?? '#6b7a99';
        })->values()->toArray();

        $chartItemLabels    = collect($itemsByType ?? [])->pluck('type')->values()->toArray();
        $chartItemBorrowed  = collect($itemsByType ?? [])->pluck('borrowed')->values()->toArray();
        $chartItemAvailable = collect($itemsByType ?? [])->pluck('available')->values()->toArray();

        $chartRoomLabels = collect($roomUtilization ?? [])->pluck('name')->values()->toArray();
        $chartRoomPct    = collect($roomUtilization ?? [])->pluck('utilization_pct')->values()->toArray();
    @endphp

    const trendLabels   = @json($chartTrendLabels);
    const trendBorrowed = @json($chartTrendBorrowed);
    const trendReturned = @json($chartTrendReturned);
    const statusLabels  = @json($chartStatusLabels);
    const statusCounts  = @json($chartStatusCounts);
    const statusColors  = @json($chartStatusColors);
    const itemLabels    = @json($chartItemLabels);
    const itemBorrowed  = @json($chartItemBorrowed);
    const itemAvailable = @json($chartItemAvailable);
    const roomLabels    = @json($chartRoomLabels);
    const roomPct       = @json($chartRoomPct);

    // ── 1. Borrowing Trends (Line) ────────────────────────────────────────────
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    const blueGrad = trendCtx.createLinearGradient(0, 0, 0, 200);
    blueGrad.addColorStop(0, 'rgba(79,124,255,0.18)');
    blueGrad.addColorStop(1, 'rgba(79,124,255,0)');
    const greenGrad = trendCtx.createLinearGradient(0, 0, 0, 200);
    greenGrad.addColorStop(0, 'rgba(34,197,94,0.18)');
    greenGrad.addColorStop(1, 'rgba(34,197,94,0)');

    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [
                {
                    label: 'borrowings',
                    data: trendBorrowed,
                    borderColor: '#4f7cff',
                    backgroundColor: blueGrad,
                    borderWidth: 2.5,
                    pointRadius: 4,
                    pointBackgroundColor: '#4f7cff',
                    pointBorderColor: '#0d1117',
                    pointBorderWidth: 2,
                    tension: 0.4,
                    fill: true,
                },
                {
                    label: 'returned',
                    data: trendReturned,
                    borderColor: '#22c55e',
                    backgroundColor: greenGrad,
                    borderWidth: 2.5,
                    pointRadius: 4,
                    pointBackgroundColor: '#22c55e',
                    pointBorderColor: '#0d1117',
                    pointBorderWidth: 2,
                    tension: 0.4,
                    fill: true,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { usePointStyle: true, pointStyle: 'circle', padding: 20, color: '#6b7a99', font: { size: 12 } }
                },
                tooltip: {
                    backgroundColor: '#1c2333', borderColor: '#252d3d', borderWidth: 1,
                    titleColor: '#e8edf5', bodyColor: '#6b7a99', padding: 10, cornerRadius: 8,
                }
            },
            scales: {
                x: { grid: { color: gridColor }, ticks: { color: tickColor } },
                y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: tickColor } }
            }
        }
    });

    // ── 2. Status Pie ─────────────────────────────────────────────────────────
    new Chart(document.getElementById('statusChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusCounts,
                backgroundColor: statusColors,
                borderColor: '#161b27',
                borderWidth: 3,
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1c2333', borderColor: '#252d3d', borderWidth: 1,
                    titleColor: '#e8edf5', bodyColor: '#6b7a99', padding: 10, cornerRadius: 8,
                    callbacks: {
                        label: (ctx) => {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const pct = total > 0 ? Math.round((ctx.raw / total) * 100) : 0;
                            return ` ${ctx.label}: ${ctx.raw} (${pct}%)`;
                        }
                    }
                }
            }
        }
    });

    // ── 3. Items by Type (Grouped Bar) ───────────────────────────────────────
    new Chart(document.getElementById('itemTypeChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: itemLabels,
            datasets: [
                {
                    label: 'borrowed',
                    data: itemBorrowed,
                    backgroundColor: 'rgba(79,124,255,0.75)',
                    borderRadius: 5,
                    borderSkipped: false,
                },
                {
                    label: 'available',
                    data: itemAvailable,
                    backgroundColor: 'rgba(34,197,94,0.75)',
                    borderRadius: 5,
                    borderSkipped: false,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { usePointStyle: true, pointStyle: 'rect', padding: 20, color: '#6b7a99' }
                },
                tooltip: {
                    backgroundColor: '#1c2333', borderColor: '#252d3d', borderWidth: 1,
                    titleColor: '#e8edf5', bodyColor: '#6b7a99', padding: 10, cornerRadius: 8,
                }
            },
            scales: {
                x: { grid: { color: gridColor }, ticks: { color: tickColor } },
                y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: tickColor } }
            }
        }
    });

    // ── 4. Room Utilization (Horizontal Bar) ──────────────────────────────────
    new Chart(document.getElementById('roomChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: roomLabels,
            datasets: [{
                label: 'Utilization %',
                data: roomPct,
                backgroundColor: 'rgba(245,158,11,0.80)',
                borderRadius: 5,
                borderSkipped: false,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1c2333', borderColor: '#252d3d', borderWidth: 1,
                    titleColor: '#e8edf5', bodyColor: '#6b7a99', padding: 10, cornerRadius: 8,
                    callbacks: { label: (ctx) => ` ${ctx.raw}%` }
                }
            },
            scales: {
                x: {
                    beginAtZero: true, max: 100,
                    grid: { color: gridColor },
                    ticks: { color: tickColor, callback: (v) => v + '%' }
                },
                y: { grid: { display: false }, ticks: { color: tickColor } }
            }
        }
    });
</script>
@endpush