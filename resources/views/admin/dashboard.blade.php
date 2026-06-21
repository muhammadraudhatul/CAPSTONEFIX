{{-- Borrowing Management Dashboard --}}
{{-- Controller should pass: $stats, $borrowingTrends, $statusBreakdown, $itemsByType, $roomUtilization, $recentBorrowings --}}

@extends('admin.layouts.app')

@section('title', 'Dashboard Manajemen Peminjaman')

@push('styles')
{{-- Inter sudah diload dari layout, tidak perlu load font tambahan --}}
<style>
    /* ── Semua CSS di-scope ke .dash-wrapper agar tidak menimpa layout induk ── */

    .dash-wrapper {
        /* Flip7 Muted Design System */
        --primary-teal:  #4B958F;
        --primary-light: #75B6B0;
        --primary-dark:  #356F6B;
        --primary-bg:    #E7EFED;
        --accent-gold:   #D7BD62;
        --accent-light:  #E9DCA7;
        --accent-dark:   #9A8235;
        --coral:         #C97A62;
        --coral-light:   #DCA18F;
        --coral-dark:    #A85F49;
        --cream:         #F7F3E8;
        --sky-blue:      #7FA8BF;
        --surface-base:  #EDF2F1;
        --surface-card:  #FAFBFA;
        --success:       #4F8E72;
        --error:         #C97A62;

        --bg-base:       var(--surface-base);
        --bg-card:       var(--surface-card);
        --bg-card-hover: #FFFFFF;
        --border:        rgba(53, 111, 107, 0.13);
        --text-primary:  #1F2A29;
        --text-muted:    #657675;
        --text-dim:      #8A9997;
        --accent-blue:   var(--sky-blue);
        --accent-purple: var(--coral);
        --accent-green:  var(--success);
        --accent-orange: var(--accent-gold);
        --accent-red:    var(--coral);
        --accent-teal:   var(--primary-teal);
        --positive:      var(--success);
        --negative:      var(--error);
        --chart-grid:    rgba(75, 149, 143, 0.10);
        --shadow-sm:     0 2px 8px rgba(31, 42, 41, 0.05);
        --shadow-md:     0 4px 16px rgba(31, 42, 41, 0.07);
        --shadow-card:   0 10px 28px rgba(53, 111, 107, 0.07);
        --shadow-soft:   0 14px 34px rgba(31, 42, 41, 0.06);
        --shadow-teal:   0 14px 32px rgba(75, 149, 143, 0.12);
        --shadow-gold:   0 14px 32px rgba(215, 189, 98, 0.13);
        --shadow-coral:  0 14px 32px rgba(201, 122, 98, 0.12);
        --shadow-sky:    0 14px 28px rgba(127, 168, 191, 0.12);

        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'PingFang SC', 'Microsoft YaHei', sans-serif;
        font-size: 14px;
        line-height: 1.6;
        color: var(--text-primary);
        padding: 2rem 1.75rem;
        box-sizing: border-box;
        min-height: calc(100vh - 1px);
        background:
            radial-gradient(circle at 12% 10%, rgba(215, 189, 98, 0.09), transparent 26%),
            radial-gradient(circle at 88% 12%, rgba(75, 149, 143, 0.08), transparent 30%),
            radial-gradient(circle at 80% 78%, rgba(201, 122, 98, 0.06), transparent 26%),
            linear-gradient(135deg, #F3F6F5 0%, var(--surface-base) 52%, #EEF3F1 100%);
        border-radius: 28px;
        overflow: hidden;
        position: relative;
    }

    .dash-wrapper::before,
    .dash-wrapper::after {
        content: '';
        position: absolute;
        border-radius: 28px;
        border: 1px solid rgba(53, 111, 107, 0.05);
        background: rgba(247, 243, 232, 0.20);
        pointer-events: none;
        z-index: 0;
        transform: rotate(-8deg);
    }

    .dash-wrapper::before {
        width: 150px;
        height: 210px;
        right: -74px;
        top: 110px;
    }

    .dash-wrapper::after {
        width: 108px;
        height: 146px;
        left: -52px;
        bottom: 128px;
        transform: rotate(10deg);
    }

    .dash-wrapper *, .dash-wrapper *::before, .dash-wrapper *::after {
        box-sizing: border-box;
    }

    .dash-header,
    .stats-grid,
    .charts-row,
    .charts-row-equal,
    .dash-wrapper > .card {
        position: relative;
        z-index: 1;
    }

    /* ── Header ── */
    .dash-header {
        margin-bottom: 1.5rem;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        padding: 1.65rem 1.8rem;
        border-radius: 30px;
        background: linear-gradient(135deg, rgba(247, 243, 232, 0.96), rgba(250, 251, 250, 0.95));
        border: 1px solid rgba(255, 255, 255, 0.80);
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }

    .dash-header::before {
        content: '';
        position: absolute;
        inset: 0 auto 0 0;
        width: 6px;
        background: linear-gradient(180deg, rgba(215, 189, 98, 0.85), rgba(75, 149, 143, 0.85), rgba(201, 122, 98, 0.78));
    }

    .dash-header::after {
        content: 'DASHBOARD';
        position: absolute;
        right: 1.6rem;
        bottom: 0.9rem;
        color: rgba(53, 111, 107, 0.05);
        font-weight: 900;
        font-size: clamp(1.8rem, 5vw, 4.25rem);
        line-height: 1;
        letter-spacing: 0.08em;
        pointer-events: none;
    }

    .dash-header > div,
    .dash-date {
        position: relative;
        z-index: 1;
    }

    .dash-header h1 {
        font-size: clamp(1.9rem, 3vw, 2.55rem);
        font-weight: 900;
        letter-spacing: -0.045em;
        color: var(--text-primary);
        margin: 0;
        padding: 0;
        line-height: 1.05;
        text-shadow: 2px 2px 0 rgba(215, 189, 98, 0.20);
    }

    .dash-header p {
        color: #4C8A85;
        font-size: 0.92rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        margin: 0.55rem 0 0 0;
        padding: 0;
        text-transform: none;
    }

    .dash-date {
        font-size: 0.82rem;
        color: var(--primary-dark);
        background: rgba(247, 243, 232, 0.82);
        border: 1px solid rgba(75, 149, 143, 0.14);
        padding: 0.65rem 1.1rem;
        border-radius: 999px;
        white-space: nowrap;
        align-self: center;
        font-weight: 900;
        box-shadow: var(--shadow-sm);
    }

    /* ── Card ── */
    .dash-wrapper .card {
        background: linear-gradient(180deg, rgba(250,251,250,0.98), rgba(244, 241, 234, 0.88));
        border: 1px solid rgba(255, 255, 255, 0.82);
        border-left: 7px solid var(--primary-teal);
        border-radius: 26px;
        padding: 1.5rem;
        transition: transform 0.24s cubic-bezier(.2,.8,.2,1), box-shadow 0.24s, border-color 0.24s, background 0.24s;
        box-shadow: var(--shadow-soft);
        position: relative;
        overflow: hidden;
    }

    .dash-wrapper .card::before {
        content: '';
        position: absolute;
        width: 110px;
        height: 110px;
        right: -52px;
        top: -52px;
        border-radius: 999px;
        background: rgba(75, 149, 143, 0.07);
        pointer-events: none;
    }

    .dash-wrapper .card:hover {
        transform: translateY(-2px);
        border-color: rgba(75, 149, 143, 0.12);
        background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(247, 243, 232, 0.92));
        box-shadow: 0 16px 32px rgba(31, 42, 41, 0.07);
    }

    .card-title {
        font-size: 1.05rem;
        font-weight: 900;
        color: var(--text-primary);
        margin: 0 0 1.2rem 0;
        padding: 0 0 0.85rem 0;
        letter-spacing: -0.02em;
        border-bottom: 1px solid rgba(75, 149, 143, 0.10);
        position: relative;
        z-index: 1;
    }

    .card-title::before {
        content: '';
        display: inline-block;
        width: 11px;
        height: 11px;
        border-radius: 999px;
        background: var(--accent-gold);
        border: 1px solid rgba(53, 111, 107, 0.18);
        margin-right: 0.55rem;
        box-shadow: 0 0 0 4px rgba(215, 189, 98, 0.12);
        vertical-align: 1px;
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
        background: linear-gradient(145deg, rgba(250,251,250,0.98), rgba(247, 243, 232, 0.84));
        border: 1px solid rgba(255, 255, 255, 0.82);
        border-left: 7px solid var(--primary-teal);
        border-radius: 24px;
        padding: 1.35rem 1.5rem 1.45rem;
        transition: transform 0.24s cubic-bezier(.2,.8,.2,1), box-shadow 0.24s, border-color 0.24s;
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow-soft);
        min-height: 150px;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        width: 104px;
        height: 104px;
        right: -36px;
        top: -42px;
        border-radius: 32px;
        opacity: 0.13;
        background: var(--primary-teal);
        transform: rotate(16deg);
        transition: transform 0.24s ease, opacity 0.24s ease;
    }

    .stat-card::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(115deg, rgba(255,255,255,0.32), transparent 36%);
        pointer-events: none;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        border-color: rgba(75, 149, 143, 0.12);
        box-shadow: 0 16px 32px rgba(31, 42, 41, 0.07);
    }

    .stat-card:hover::before {
        transform: rotate(24deg) scale(1.05);
        opacity: 0.20;
    }

    .stat-card:nth-child(1) { border-left-color: var(--accent-gold); }
    .stat-card:nth-child(1)::before { background: var(--accent-gold); }
    .stat-card:nth-child(2) { border-left-color: var(--primary-teal); }
    .stat-card:nth-child(2)::before { background: var(--primary-teal); }
    .stat-card:nth-child(3) { border-left-color: var(--coral); }
    .stat-card:nth-child(3)::before { background: var(--coral); }
    .stat-card:nth-child(4) { border-left-color: var(--sky-blue); }
    .stat-card:nth-child(4)::before { background: var(--sky-blue); }

    .stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 0 0 0.85rem 0;
        padding: 0;
        position: relative;
        z-index: 1;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
        border: 1px solid rgba(53, 111, 107, 0.12);
        box-shadow: var(--shadow-sm);
    }

    .stat-icon.blue   { background: linear-gradient(135deg, rgba(233, 220, 167, 0.92), var(--accent-gold)); color: #43340E; }
    .stat-icon.purple { background: linear-gradient(135deg, rgba(117, 182, 176, 0.34), var(--primary-light)); color: var(--primary-dark); }
    .stat-icon.green  { background: linear-gradient(135deg, rgba(220, 161, 143, 0.30), var(--coral-light)); color: var(--coral-dark); }
    .stat-icon.orange { background: linear-gradient(135deg, rgba(127, 168, 191, 0.24), var(--sky-blue)); color: #486F84; }

    .stat-badge {
        font-size: 0.76rem;
        font-weight: 900;
        padding: 0.26rem 0.66rem;
        border-radius: 999px;
        line-height: 1.4;
        border: 1px solid rgba(255, 255, 255, 0.70);
        box-shadow: var(--shadow-sm);
    }

    .stat-badge.positive { color: var(--primary-dark); background: rgba(75, 149, 143, 0.13); }
    .stat-badge.negative { color: var(--coral-dark); background: rgba(201, 122, 98, 0.13); }

    .stat-value {
        font-size: clamp(1.85rem, 2.5vw, 2.25rem);
        font-weight: 900;
        color: var(--text-primary);
        letter-spacing: -0.04em;
        line-height: 1.08;
        margin: 0 0 0.36rem 0;
        padding: 0;
        display: block;
        position: relative;
        z-index: 1;
    }

    .stat-label {
        font-size: 0.76rem;
        color: var(--text-muted);
        font-weight: 900;
        margin: 0;
        padding: 0;
        display: block;
        letter-spacing: 0.10em;
        text-transform: uppercase;
        position: relative;
        z-index: 1;
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
        background: rgba(247, 243, 232, 0.62);
        border: 1px solid rgba(75, 149, 143, 0.10);
        border-radius: 22px;
        padding: 0.7rem;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.68);
        z-index: 1;
    }

    .chart-wrap canvas { display: block; width: 100% !important; }

    /* ── Pie Legend ── */
    .pie-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 1.4rem;
        position: relative;
        z-index: 1;
    }

    .pie-canvas-wrap {
        width: 220px;
        height: 220px;
        position: relative;
        margin: 0 auto;
        border-radius: 999px;
        padding: 0.35rem;
        background: radial-gradient(circle, rgba(247,243,232,0.92) 0%, rgba(250,251,250,0.82) 70%);
        box-shadow: inset 0 0 0 1px rgba(75, 149, 143, 0.08), var(--shadow-sm);
    }

    .pie-legend {
        flex: 1;
        min-width: 140px;
        width: 100%;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin: 0 0 0.7rem 0;
        padding: 0.5rem 0.65rem;
        font-size: 0.83rem;
        border-radius: 999px;
        background: rgba(247, 243, 232, 0.68);
        border: 1px solid rgba(75, 149, 143, 0.08);
    }

    .legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        flex-shrink: 0;
        display: inline-block;
        box-shadow: 0 0 0 4px rgba(75, 149, 143, 0.06);
    }

    .legend-label { color: var(--text-primary); flex: 1; font-weight: 800; }
    .legend-pct   { font-weight: 900; color: var(--primary-dark); }

    /* ── Table ── */
    .table-wrap {
        overflow-x: auto;
        border-radius: 20px;
        background: rgba(247, 243, 232, 0.58);
        border: 1px solid rgba(75, 149, 143, 0.10);
        position: relative;
        z-index: 1;
    }

    .dash-wrapper table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .dash-wrapper thead th {
        text-align: left;
        font-size: 0.72rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.10em;
        color: #4A8C86;
        padding: 0.85rem 1rem;
        border-bottom: 1px solid rgba(75, 149, 143, 0.10);
        background: rgba(231, 239, 237, 0.88);
        white-space: nowrap;
    }

    .dash-wrapper tbody tr {
        border-bottom: 1px solid rgba(75, 149, 143, 0.08);
        transition: background 0.18s;
    }

    .dash-wrapper tbody tr:last-child { border-bottom: none; }
    .dash-wrapper tbody tr:hover { background: rgba(247, 243, 232, 0.58); }

    .dash-wrapper tbody td {
        padding: 0.9rem 1rem;
        font-size: 0.865rem;
        color: var(--text-primary);
        vertical-align: middle;
        border: none;
        border-bottom: 1px solid rgba(75, 149, 143, 0.08);
        background: transparent;
        font-weight: 700;
    }

    .dash-wrapper tbody tr:last-child td { border-bottom: none; }
    .dash-wrapper tbody td.muted { color: var(--text-muted); font-weight: 700; }

    /* ── Status Badge ── */
    .dash-wrapper .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.74rem;
        font-weight: 900;
        padding: 0.30rem 0.72rem;
        border-radius: 999px;
        line-height: 1.4;
        border: 1px solid rgba(255,255,255,0.70);
        box-shadow: var(--shadow-sm);
        white-space: nowrap;
    }

    .badge-active         { background: rgba(127, 168, 191, 0.14); color: #486F84; }
    .badge-returned       { background: rgba(79, 142, 114, 0.13); color: #3C7359; }
    .badge-pending        { background: rgba(215, 189, 98, 0.18); color: #7F6B2A; }
    .badge-cancelled      { background: rgba(201, 122, 98, 0.13); color: var(--coral-dark); }
    .badge-completed      { background: rgba(79, 142, 114, 0.13); color: #3C7359; }
    .badge-approved       { background: rgba(75, 149, 143, 0.13); color: var(--primary-dark); }
    .badge-waiting_return { background: rgba(127, 168, 191, 0.14); color: #486F84; }
    .badge-rejected       { background: rgba(201, 122, 98, 0.13); color: var(--coral-dark); }

    /* ── Empty state ── */
    .empty-row td {
        text-align: center;
        color: var(--text-muted);
        padding: 2rem;
        font-size: 0.9rem;
        font-weight: 800;
    }

    /* ── Animate in ── */
    @keyframes dashFadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .stat-card,
    .dash-wrapper .card {
        animation: dashFadeUp 0.35s cubic-bezier(.2,.8,.2,1) both;
    }

    .stat-card:nth-child(1) { animation-delay: 0.04s; }
    .stat-card:nth-child(2) { animation-delay: 0.08s; }
    .stat-card:nth-child(3) { animation-delay: 0.12s; }
    .stat-card:nth-child(4) { animation-delay: 0.16s; }

    @media (max-width: 640px) {
        .dash-wrapper { padding: 1rem; border-radius: 20px; }
        .dash-header { padding: 1.35rem; border-radius: 24px; }
        .dash-header::after { display: none; }
        .dash-date { width: 100%; text-align: center; }
        .stat-card, .dash-wrapper .card { border-radius: 20px; }
        .chart-wrap { padding: 0.45rem; }
        .pie-canvas-wrap { width: 190px; height: 190px; }
    }
</style>
@endpush

@section('content')
<div class="dash-wrapper">

    {{-- ── Header ── --}}
    <div class="dash-header">
        <div>
            <h1>Dashboard Manajemen Peminjaman</h1>
            <p>Ringkasan peminjaman ruangan, alat, dan bahan</p>
        </div>
        <span class="dash-date">{{ now()->translatedFormat('D, d M Y') }}</span>
    </div>

    {{-- ── Stat Cards ── --}}
    <div class="stats-grid">

        {{-- Total Peminjaman --}}
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
            <span class="stat-label">Total Peminjaman</span>
        </div>

        {{-- Pengguna Aktif --}}
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
            <span class="stat-label">Pengguna Aktif</span>
        </div>

        {{-- Item Digunakan --}}
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
            <span class="stat-label">Item Digunakan</span>
        </div>

        {{-- Rata-rata Pemakaian --}}
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
            <span class="stat-label">Rata-rata Pemakaian</span>
        </div>

    </div>{{-- /stats-grid --}}

    {{-- ── Row: Tren Peminjaman + Status Pie ── --}}
    <div class="charts-row">

        {{-- Tren Peminjaman --}}
        <div class="card">
            <div class="card-title">Tren Peminjaman</div>
            <div class="chart-wrap" style="height:220px;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        {{-- Peminjaman Berdasarkan Status --}}
        <div class="card">
            <div class="card-title">Peminjaman Berdasarkan Status</div>
            <div class="pie-wrap">
                <div class="pie-canvas-wrap">
                    <canvas id="statusChart" width="200" height="200"></canvas>
                </div>
                <div class="pie-legend">
                    @php
                        $statusColors = [
                            'completed'      => '#4F8E72', // hijau
                            'approved'       => '#4B958F', // teal
                            'waiting_return' => '#7FA8BF', // sky blue
                            'pending'        => '#D7BD62', // gold
                            'rejected'       => '#C97A62', // coral
                            'cancelled'      => '#A85F49', // coral dark
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
                            @php
                                $statusLabel = [
                                    'completed'      => 'Selesai',
                                    'approved'       => 'Disetujui',
                                    'waiting_return' => 'Menunggu Pengembalian',
                                    'pending'        => 'Menunggu',
                                    'rejected'       => 'Ditolak',
                                    'cancelled'      => 'Dibatalkan',
                                    'active'         => 'Aktif',
                                    'returned'       => 'Dikembalikan',
                                ][strtolower($row->status)] ?? ucfirst($row->status);
                            @endphp
                            <span class="legend-label">{{ $statusLabel }}</span>
                            <span class="legend-pct">{{ $pct }}%</span>
                        </div>
                    @empty
                        <p style="color:#657675;font-size:.8rem;margin:0;font-weight:800;">Tidak ada data</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>{{-- /charts-row --}}

    {{-- ── Row: Item Berdasarkan Tipe + Pemakaian Ruangan ── --}}
    <div class="charts-row-equal">

        {{-- Item Berdasarkan Tipe --}}
        <div class="card">
            <div class="card-title">Item Berdasarkan Tipe</div>
            <div class="chart-wrap" style="height:240px;">
                <canvas id="itemTypeChart"></canvas>
            </div>
        </div>

        {{-- Pemakaian Ruangan --}}
        <div class="card">
            <div class="card-title">Pemakaian Ruangan</div>
            <div class="chart-wrap" style="height:240px;">
                <canvas id="roomChart"></canvas>
            </div>
        </div>

    </div>{{-- /charts-row-equal --}}

    {{-- ── Peminjaman Terbaru Table ── --}}
    <div class="card">
        <div class="card-title">Peminjaman Terbaru</div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pengguna</th>
                        <th>Ruangan</th>
                        <th>Tanggal</th>
                        <th>Keperluan</th>
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
                            @php
                                $statusLabel = [
                                    'active'         => 'Aktif',
                                    'returned'       => 'Dikembalikan',
                                    'pending'        => 'Menunggu',
                                    'cancelled'      => 'Dibatalkan',
                                    'completed'      => 'Selesai',
                                    'approved'       => 'Disetujui',
                                    'waiting_return' => 'Menunggu Pengembalian',
                                    'rejected'       => 'Ditolak',
                                ][$s] ?? ucfirst($s);
                            @endphp
                            <span class="badge badge-{{ $s }}">{{ $icon }} {{ $statusLabel }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-row"><td colspan="6">Tidak ada peminjaman terbaru.</td></tr>
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
    Chart.defaults.color = '#657675';
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size   = 12;

    const gridColor = 'rgba(75, 149, 143, 0.10)';
    const tickColor = '#657675';

    @php
        $chartTrendLabels   = collect($borrowingTrends ?? [])->pluck('month')->values()->toArray();
        $chartTrendBorrowed = collect($borrowingTrends ?? [])->pluck('total_borrowings')->values()->toArray();
        $chartTrendReturned = collect($borrowingTrends ?? [])->pluck('total_returned')->values()->toArray();

        $chartStatusLabels = collect($statusBreakdown ?? [])->map(function($r) {
            return [
                'completed'      => 'Selesai',
                'approved'       => 'Disetujui',
                'waiting_return' => 'Menunggu Pengembalian',
                'pending'        => 'Menunggu',
                'rejected'       => 'Ditolak',
                'cancelled'      => 'Dibatalkan',
                'active'         => 'Aktif',
                'returned'       => 'Dikembalikan',
            ][strtolower($r->status)] ?? ucfirst($r->status);
        })->values()->toArray();
        $chartStatusCounts = collect($statusBreakdown ?? [])->pluck('count')->values()->toArray();
        $chartStatusColors = collect($statusBreakdown ?? [])->map(function($r) {
            return [
                'completed'      => '#4F8E72',
                'approved'       => '#4B958F',
                'waiting_return' => '#7FA8BF',
                'pending'        => '#D7BD62',
                'rejected'       => '#C97A62',
                'cancelled'      => '#A85F49',
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

    // ── 1. Tren Peminjaman (Line) ────────────────────────────────────────────
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    const blueGrad = trendCtx.createLinearGradient(0, 0, 0, 200);
    blueGrad.addColorStop(0, 'rgba(75, 149, 143, 0.16)');
    blueGrad.addColorStop(1, 'rgba(75, 149, 143, 0)');
    const greenGrad = trendCtx.createLinearGradient(0, 0, 0, 200);
    greenGrad.addColorStop(0, 'rgba(215, 189, 98, 0.18)');
    greenGrad.addColorStop(1, 'rgba(215, 189, 98, 0)');

    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [
                {
                    label: 'peminjaman',
                    data: trendBorrowed,
                    borderColor: '#4B958F',
                    backgroundColor: blueGrad,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointBackgroundColor: '#4B958F',
                    pointBorderColor: '#F7F3E8',
                    pointBorderWidth: 2,
                    tension: 0.4,
                    fill: true,
                },
                {
                    label: 'dikembalikan',
                    data: trendReturned,
                    borderColor: '#D7BD62',
                    backgroundColor: greenGrad,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointBackgroundColor: '#D7BD62',
                    pointBorderColor: '#F7F3E8',
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
                    labels: { usePointStyle: true, pointStyle: 'circle', padding: 20, color: '#657675', font: { size: 12, weight: '700' } }
                },
                tooltip: {
                    backgroundColor: '#F7F3E8', borderColor: 'rgba(75, 149, 143, 0.16)', borderWidth: 2,
                    titleColor: '#1F2A29', bodyColor: '#657675', padding: 12, cornerRadius: 14,
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
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusCounts,
                backgroundColor: statusColors,
                borderColor: '#F7F3E8',
                borderWidth: 4,
                hoverOffset: 10,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '62%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#F7F3E8', borderColor: 'rgba(75, 149, 143, 0.16)', borderWidth: 2,
                    titleColor: '#1F2A29', bodyColor: '#657675', padding: 12, cornerRadius: 14,
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

    // ── 3. Item Berdasarkan Tipe (Grouped Bar) ───────────────────────────────────────
    new Chart(document.getElementById('itemTypeChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: itemLabels,
            datasets: [
                {
                    label: 'dipinjam',
                    data: itemBorrowed,
                    backgroundColor: 'rgba(75, 149, 143, 0.78)',
                    borderRadius: 9,
                    borderSkipped: false,
                },
                {
                    label: 'tersedia',
                    data: itemAvailable,
                    backgroundColor: 'rgba(215, 189, 98, 0.78)',
                    borderRadius: 9,
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
                    labels: { usePointStyle: true, pointStyle: 'rectRounded', padding: 20, color: '#657675', font: { weight: '700' } }
                },
                tooltip: {
                    backgroundColor: '#F7F3E8', borderColor: 'rgba(75, 149, 143, 0.16)', borderWidth: 2,
                    titleColor: '#1F2A29', bodyColor: '#657675', padding: 12, cornerRadius: 14,
                }
            },
            scales: {
                x: { grid: { color: gridColor }, ticks: { color: tickColor } },
                y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: tickColor } }
            }
        }
    });

    // ── 4. Pemakaian Ruangan (Horizontal Bar) ──────────────────────────────────
    new Chart(document.getElementById('roomChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: roomLabels,
            datasets: [{
                label: 'Pemakaian %',
                data: roomPct,
                backgroundColor: 'rgba(201, 122, 98, 0.72)',
                borderRadius: 9,
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
                    backgroundColor: '#F7F3E8', borderColor: 'rgba(75, 149, 143, 0.16)', borderWidth: 2,
                    titleColor: '#1F2A29', bodyColor: '#657675', padding: 12, cornerRadius: 14,
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