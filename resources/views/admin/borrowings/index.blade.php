@extends('admin.layouts.app')

@section('content')

<style>
    /* ── Flip7 muted theme: hanya penyesuaian desain, logic/Blade/JS tidak diubah ── */
    .borrowings-page {
        --primary-teal:  #4B958F;
        --primary-light: #75B6B0;
        --primary-dark:  #356F6B;
        --primary-bg:    #E7EFED;
        --accent-gold:   #D7BD62;
        --coral:         #C97A62;
        --coral-dark:    #A85F49;
        --sky-blue:      #7FA8BF;
        --cream:         #F7F3E8;
        --surface-base:  #EDF2F1;
        --surface-card:  #FAFBFA;
        --success:       #4F8E72;
        --text-primary:  #1F2A29;
        --text-muted:    #657675;
        --text-soft:     #8A9997;
        --shadow-sm:     0 2px 8px rgba(31, 42, 41, 0.05);
        --shadow-card:   0 10px 28px rgba(53, 111, 107, 0.07);
        --shadow-soft:   0 14px 34px rgba(31, 42, 41, 0.06);

        min-height: calc(100vh - 1px);
        padding: 2rem 1.75rem;
        border-radius: 28px;
        color: var(--text-primary);
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(circle at 8% 8%, rgba(215, 189, 98, 0.09), transparent 25%),
            radial-gradient(circle at 88% 12%, rgba(75, 149, 143, 0.08), transparent 28%),
            linear-gradient(135deg, #F3F6F5 0%, var(--surface-base) 52%, #EEF3F1 100%);
    }

    .borrowings-page *,
    .borrowings-page *::before,
    .borrowings-page *::after { box-sizing: border-box; }

    .borrowings-page::before,
    .borrowings-page::after {
        content: '';
        position: absolute;
        border-radius: 28px;
        border: 1px solid rgba(53, 111, 107, 0.05);
        background: rgba(247, 243, 232, 0.20);
        pointer-events: none;
        z-index: 0;
    }

    .borrowings-page::before {
        width: 150px;
        height: 210px;
        right: -74px;
        top: 110px;
        transform: rotate(-8deg);
    }

    .borrowings-page::after {
        width: 108px;
        height: 146px;
        left: -52px;
        bottom: 138px;
        transform: rotate(10deg);
    }

    .borrowings-header,
    .stat-grid,
    .tab-bar,
    .table-card {
        position: relative;
        z-index: 1;
    }

    /* ── Header ── */
    .borrowings-header {
        padding: 1.65rem 1.8rem;
        border-radius: 30px;
        background: linear-gradient(135deg, rgba(247, 243, 232, 0.96), rgba(250, 251, 250, 0.95));
        border: 1px solid rgba(255, 255, 255, 0.80);
        box-shadow: var(--shadow-card);
        overflow: hidden;
        position: relative;
    }

    .borrowings-header::before {
        content: '';
        position: absolute;
        inset: 0 auto 0 0;
        width: 6px;
        background: linear-gradient(180deg, rgba(215, 189, 98, 0.85), rgba(75, 149, 143, 0.85), rgba(201, 122, 98, 0.78));
    }

    .borrowings-header::after {
        content: 'BORROWINGS';
        position: absolute;
        right: 1.5rem;
        bottom: 0.85rem;
        color: rgba(53, 111, 107, 0.05);
        font-weight: 900;
        font-size: clamp(1.8rem, 5vw, 4rem);
        line-height: 1;
        letter-spacing: 0.08em;
        pointer-events: none;
    }

    .borrowings-header h1 {
        position: relative;
        z-index: 1;
        font-size: clamp(1.9rem, 3vw, 2.55rem) !important;
        font-weight: 900 !important;
        color: var(--text-primary) !important;
        letter-spacing: -0.045em !important;
        margin: 0 0 0.45rem !important;
        line-height: 1.05 !important;
        text-shadow: 2px 2px 0 rgba(215, 189, 98, 0.20);
    }

    .borrowings-header p {
        position: relative;
        z-index: 1;
        color: #4C8A85 !important;
        font-size: 0.95rem !important;
        font-weight: 700;
        letter-spacing: 0.02em;
        margin: 0 !important;
    }

    /* ── Stat cards ── */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-top: 1.75rem;
    }

    @media (max-width: 1100px) { .stat-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 640px)  { .stat-grid { grid-template-columns: 1fr; } }

    .stat-card {
        background: linear-gradient(145deg, rgba(252, 252, 251, 0.98) 0%, rgba(247, 243, 232, 0.92) 100%);
        border: 1px solid rgba(255, 255, 255, 0.82);
        border-left: 7px solid var(--primary-teal);
        border-radius: 22px;
        padding: 1.25rem 1.25rem 1.2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 6px 18px rgba(53, 111, 107, 0.06);
        transition: transform 0.24s cubic-bezier(.2,.8,.2,1), box-shadow 0.24s, border-color 0.24s;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        width: 78px;
        height: 78px;
        right: -30px;
        top: -30px;
        border-radius: 999px;
        background: rgba(75, 149, 143, 0.07);
        pointer-events: none;
    }

    .stat-card::after {
        content: '';
        position: absolute;
        left: 1.25rem;
        bottom: 0;
        width: 44px;
        height: 4px;
        border-radius: 999px 999px 0 0;
        background: rgba(215, 189, 98, 0.85);
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 22px rgba(53, 111, 107, 0.08);
        border-color: rgba(75, 149, 143, 0.16);
    }

    .stat-card-purple { border-left-color: var(--primary-teal); }
    .stat-card-blue   { border-left-color: var(--sky-blue); }
    .stat-card-orange { border-left-color: var(--coral); }
    .stat-card-gray   { border-left-color: #A9BCB9; }

    .stat-card-purple::before { background: rgba(75, 149, 143, 0.08); }
    .stat-card-blue::before   { background: rgba(127, 168, 191, 0.10); }
    .stat-card-orange::before { background: rgba(201, 122, 98, 0.10); }
    .stat-card-gray::before   { background: rgba(31, 42, 41, 0.04); }

    .stat-card-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
        position: relative;
        z-index: 1;
    }

    .stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255, 255, 255, 0.62);
        box-shadow: var(--shadow-sm);
    }

    .stat-icon-purple { background: rgba(75,149,143,0.11); }
    .stat-icon-blue   { background: rgba(127,168,191,0.12); }
    .stat-icon-orange { background: rgba(201,122,98,0.12); }
    .stat-icon-gray   { background: rgba(31,42,41,0.045); }

    .stat-icon-purple i { color: var(--primary-dark); font-size: 1.25rem; }
    .stat-icon-blue   i { color: #54768B; font-size: 1.25rem; }
    .stat-icon-orange i { color: var(--coral-dark); font-size: 1.25rem; }
    .stat-icon-gray   i { color: var(--text-soft); font-size: 1.25rem; }

    .stat-trend {
        color: var(--primary-dark);
        font-size: 1rem;
        opacity: 0.86;
    }

    .stat-label {
        position: relative;
        z-index: 1;
        font-size: 0.72rem;
        color: var(--text-muted);
        margin: 0 0 0.45rem;
        font-weight: 900;
        letter-spacing: 0.10em;
        text-transform: uppercase;
    }

    .stat-value {
        position: relative;
        z-index: 1;
        font-size: clamp(1.75rem, 3vw, 2.15rem);
        font-weight: 900;
        color: var(--text-primary);
        margin: 0 0 0.25rem;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .stat-sub {
        position: relative;
        z-index: 1;
        font-size: 0.76rem;
        color: var(--text-soft);
        margin: 0;
        font-weight: 700;
    }

    /* ── Tabs ── */
    .tab-bar {
        display: inline-flex;
        background: rgba(250, 251, 250, 0.94);
        border: 1px solid rgba(75,149,143,0.14);
        border-radius: 999px;
        padding: 0.32rem;
        gap: 0.2rem;
        margin-top: 1.75rem;
        box-shadow: 0 6px 18px rgba(53, 111, 107, 0.05);
        flex-wrap: wrap;
    }

    .tab-btn {
        padding: 0.6rem 1.35rem;
        border-radius: 999px;
        border: none;
        background: none;
        color: var(--text-muted);
        font-size: 0.88rem;
        font-weight: 900;
        cursor: pointer;
        font-family: 'Inter', sans-serif;
        transition: background 0.18s, color 0.18s, transform 0.18s;
        white-space: nowrap;
    }

    .tab-btn:hover {
        color: var(--primary-dark);
        background: rgba(75,149,143,0.08);
        transform: translateY(-1px);
    }

    .tab-btn.tab-active {
        background: linear-gradient(135deg, #7DB4AF, var(--primary-teal));
        color: #fff;
        box-shadow: 0 8px 18px rgba(75,149,143,0.12);
    }

    /* ── Table card ── */
    .table-card {
        background: linear-gradient(180deg, rgba(250,251,250,0.98), rgba(244, 241, 234, 0.88));
        border: 1px solid rgba(255, 255, 255, 0.82);
        border-left: 7px solid var(--primary-teal);
        border-radius: 26px;
        overflow-x: auto;
        width: 100%;
        margin-top: 1.25rem;
        box-shadow: var(--shadow-soft);
        transition: transform 0.24s cubic-bezier(.2,.8,.2,1), box-shadow 0.24s, border-color 0.24s;
    }

    .table-card:hover {
        transform: translateY(-2px);
        border-color: rgba(75,149,143,0.12);
        box-shadow: 0 16px 32px rgba(31, 42, 41, 0.07);
    }

    .table-card::before {
        content: '';
        display: block;
        height: 8px;
        background: linear-gradient(90deg, rgba(215, 189, 98, 0.95), rgba(75, 149, 143, 0.92), rgba(201, 122, 98, 0.86));
    }

    .data-table {
        width: 100%;
        min-width: 980px;
        border-collapse: collapse;
    }

    .data-table thead tr {
        background: rgba(231, 239, 237, 0.88);
    }

    .data-table thead th {
        padding: 0.92rem 1.25rem;
        text-align: left;
        font-size: 0.72rem;
        font-weight: 900;
        color: #4A8C86;
        text-transform: uppercase;
        letter-spacing: 0.10em;
        border-bottom: 2px solid rgba(75, 149, 143, 0.10);
        white-space: nowrap;
    }

    .data-table thead th.th-center { text-align: center; }

    .data-table tbody tr {
        border-bottom: 1px solid rgba(75, 149, 143, 0.08);
        transition: background 0.18s ease;
        vertical-align: top;
    }

    .data-table tbody tr:last-child { border-bottom: none; }
    .data-table tbody tr:hover { background: rgba(247, 243, 232, 0.58); }

    .data-table td {
        padding: 1rem 1.25rem;
        color: var(--text-muted);
        font-size: 0.88rem;
        vertical-align: top;
    }

    /* cell styles */
    .td-name {
        color: var(--text-primary);
        font-weight: 900;
        font-size: 0.92rem;
    }

    .td-nim {
        color: var(--text-soft);
        font-size: 0.78rem;
        margin-top: 0.2rem;
        font-weight: 700;
    }

    .td-room,
    .td-date {
        color: var(--primary-dark);
        font-weight: 900;
    }

    .td-time {
        display: inline-flex;
        margin-top: 0.35rem;
        color: var(--primary-dark);
        font-size: 0.74rem;
        font-weight: 800;
        padding: 0.16rem 0.48rem;
        border-radius: 999px;
        background: rgba(75, 149, 143, 0.10);
    }

    .td-item {
        color: var(--text-muted);
        font-size: 0.84rem;
        margin-bottom: 0.25rem;
        font-weight: 700;
    }

    /* status badges */
    .badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 28px;
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
        font-size: 0.68rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        white-space: nowrap;
        border: 1px solid rgba(255, 255, 255, 0.60);
        box-shadow: var(--shadow-sm);
    }

    .badge-pending {
        background: linear-gradient(135deg, rgba(215,189,98,0.20), rgba(215,189,98,0.10));
        color: #7F6B2A;
        border-color: rgba(215,189,98,0.22);
    }

    .badge-approved {
        background: linear-gradient(135deg, rgba(75,149,143,0.16), rgba(75,149,143,0.08));
        color: var(--primary-dark);
        border-color: rgba(75,149,143,0.18);
    }

    .badge-waiting-return {
        background: linear-gradient(135deg, rgba(127,168,191,0.16), rgba(127,168,191,0.08));
        color: #54768B;
        border-color: rgba(127,168,191,0.18);
    }

    .badge-completed {
        background: linear-gradient(135deg, rgba(79,142,114,0.15), rgba(79,142,114,0.08));
        color: #3C7359;
        border-color: rgba(79,142,114,0.18);
    }

    .badge-rejected {
        background: linear-gradient(135deg, rgba(201,122,98,0.16), rgba(201,122,98,0.08));
        color: var(--coral-dark);
        border-color: rgba(201,122,98,0.18);
    }

    .badge-cancelled {
        background: linear-gradient(135deg, rgba(31,42,41,0.06), rgba(31,42,41,0.03));
        color: var(--text-soft);
        border-color: rgba(31,42,41,0.08);
    }

    /* action buttons */
    .btn-action {
        display: block;
        width: 100%;
        padding: 0.52rem 0.85rem;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 900;
        cursor: pointer;
        font-family: 'Inter', sans-serif;
        transition: background 0.18s, color 0.18s, transform 0.15s, border-color 0.18s;
        text-align: center;
        margin-bottom: 0.45rem;
        white-space: nowrap;
        box-shadow: 0 2px 8px rgba(31, 42, 41, 0.04);
    }

    .btn-action:last-child { margin-bottom: 0; }
    .btn-action:hover { transform: translateY(-1px); }
    .btn-action:active { transform: scale(0.98); }

    .btn-approve {
        background: rgba(75,149,143,0.12);
        color: var(--primary-dark);
        border: 1px solid rgba(75,149,143,0.22);
    }

    .btn-approve:hover {
        background: rgba(75,149,143,0.18);
        border-color: rgba(75,149,143,0.32);
    }

    .btn-reject {
        background: rgba(201,122,98,0.10);
        color: var(--coral-dark);
        border: 1px solid rgba(201,122,98,0.20);
    }

    .btn-reject:hover {
        background: rgba(201,122,98,0.16);
        border-color: rgba(201,122,98,0.30);
    }

    .btn-complete {
        background: rgba(215,189,98,0.16);
        color: #7F6B2A;
        border: 1px solid rgba(215,189,98,0.24);
    }

    .btn-complete:hover {
        background: rgba(215,189,98,0.24);
        border-color: rgba(215,189,98,0.34);
    }

    .btn-cancel {
        background: rgba(201,122,98,0.09);
        color: var(--coral-dark);
        border: 1px solid rgba(201,122,98,0.18);
    }

    .btn-cancel:hover {
        background: rgba(201,122,98,0.15);
        border-color: rgba(201,122,98,0.28);
    }

    .cancel-textarea {
        width: 100%;
        background: rgba(247, 243, 232, 0.88) !important;
        border: 1px solid rgba(75,149,143,0.15) !important;
        border-radius: 16px !important;
        padding: 0.55rem 0.75rem !important;
        color: var(--text-primary) !important;
        font-size: 0.78rem;
        font-weight: 700;
        font-family: 'Inter', sans-serif;
        resize: vertical;
        outline: none;
        margin-bottom: 0.45rem;
        box-shadow: 0 2px 8px rgba(31, 42, 41, 0.04);
    }

    .cancel-textarea::placeholder {
        color: var(--text-soft) !important;
        font-weight: 600;
    }

    .cancel-textarea:focus {
        border-color: rgba(201,122,98,0.36) !important;
        background: #FAFBFA !important;
        box-shadow: 0 0 0 4px rgba(201,122,98,0.09);
    }

    /* error inline */
    .inline-error {
        background: rgba(201,122,98,0.10);
        border: 1px solid rgba(201,122,98,0.20);
        border-radius: 16px;
        padding: 0.75rem 0.9rem;
        margin-bottom: 0.65rem;
        display: flex;
        gap: 0.55rem;
        box-shadow: var(--shadow-sm);
    }

    .inline-error-title {
        font-weight: 900;
        color: var(--coral-dark);
        font-size: 0.82rem;
        margin: 0;
    }

    .inline-error-msg {
        color: var(--coral);
        font-size: 0.78rem;
        margin: 0.15rem 0 0;
        font-weight: 700;
    }

    /* empty state */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 5rem 2rem;
        text-align: center;
        background: rgba(247, 243, 232, 0.46);
    }

    .empty-icon-wrap {
        width: 72px;
        height: 72px;
        background: rgba(75,149,143,0.08);
        border-radius: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.25rem;
        border: 2px dashed rgba(75,149,143,0.18);
    }

    .empty-icon-wrap i {
        font-size: 2rem;
        color: var(--text-soft);
    }

    .empty-title {
        font-size: 1.1rem;
        font-weight: 900;
        color: var(--primary-dark);
        margin: 0 0 0.35rem;
    }

    .empty-sub {
        font-size: 0.85rem;
        color: var(--text-soft);
        margin: 0;
        font-weight: 700;
    }

    @keyframes borrowingFadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .borrowings-header,
    .stat-grid,
    .tab-bar,
    .table-card {
        animation: borrowingFadeUp 0.35s ease both;
    }

    .stat-grid { animation-delay: 0.08s; }
    .tab-bar { animation-delay: 0.12s; }
    .table-card { animation-delay: 0.16s; }

    @media (max-width: 760px) {
        .borrowings-page {
            padding: 1.25rem 1rem;
            border-radius: 22px;
        }

        .borrowings-header {
            padding: 1.35rem 1.25rem;
            border-radius: 24px;
        }

        .tab-bar {
            display: flex;
            width: 100%;
        }

        .tab-btn {
            flex: 1;
            padding-inline: 0.8rem;
        }
    }
</style>

<div class="borrowings-page">

    <!-- HEADER -->
    <div class="borrowings-header">
        <h1 style="font-size:2.25rem; font-weight:800; color:#1a4a44; letter-spacing:-0.02em; margin:0 0 0.35rem;">
            Peminjaman
        </h1>
        <p style="color:#7ab5b0; font-size:0.95rem; margin:0;">
            Kelola semua peminjaman dari seluruh student
        </p>
    </div>

    <!-- STATS -->
    <div class="stat-grid">

        <!-- TOTAL -->
        <div class="stat-card stat-card-purple">
            <div class="stat-card-top">
                <div class="stat-icon stat-icon-purple">
                    <i class="ti ti-box"></i>
                </div>
                <span class="stat-trend">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                </span>
            </div>
            <p class="stat-label">Total Peminjaman</p>
            <h2 class="stat-value">{{ $borrowings->count() }}</h2>
            <p class="stat-sub">Semua peminjaman</p>
        </div>

        <!-- AKTIF -->
        <div class="stat-card stat-card-blue">
            <div class="stat-card-top">
                <div class="stat-icon stat-icon-blue">
                    <i class="ti ti-clock"></i>
                </div>
            </div>
            <p class="stat-label">Peminjaman Aktif</p>
            <h2 class="stat-value">{{ $borrowings->whereIn('status', ['APPROVED','WAITING_RETURN'])->count() }}</h2>
            <p class="stat-sub">Sedang berlangsung</p>
        </div>

        <!-- PENDING -->
        <div class="stat-card stat-card-orange">
            <div class="stat-card-top">
                <div class="stat-icon stat-icon-orange">
                    <i class="ti ti-alert-circle"></i>
                </div>
            </div>
            <p class="stat-label">Menunggu Persetujuan</p>
            <h2 class="stat-value">{{ $borrowings->where('status', 'PENDING')->count() }}</h2>
            <p class="stat-sub">Perlu direview</p>
        </div>

        <!-- HISTORY -->
        <div class="stat-card stat-card-gray">
            <div class="stat-card-top">
                <div class="stat-icon stat-icon-gray">
                    <i class="ti ti-users"></i>
                </div>
            </div>
            <p class="stat-label">History</p>
            <h2 class="stat-value">{{ $borrowings->whereIn('status', ['COMPLETED','REJECTED'])->count() }}</h2>
            <p class="stat-sub">Selesai &amp; dibatalkan</p>
        </div>

    </div>

    <!-- TABS -->
    <div class="tab-bar">
        <button class="tab-btn tab-active" onclick="filterTab('all', this)">Semua ({{ $borrowings->count() }})</button>
        <button class="tab-btn" onclick="filterTab('aktif', this)">Aktif ({{ $borrowings->whereIn('status', ['APPROVED','WAITING_RETURN'])->count() }})</button>
        <button class="tab-btn" onclick="filterTab('history', this)">History ({{ $borrowings->whereIn('status', ['COMPLETED','REJECTED','CANCELLED'])->count() }})</button>
    </div>

    <!-- TABLE -->
    <div class="table-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Ruangan</th>
                    <th>Jadwal</th>
                    <th>Alat</th>
                    <th>Status</th>
                    <th class="th-center">Aksi</th>
                </tr>
            </thead>
            <tbody>

                @forelse($borrowings as $borrowing)
                <tr data-status="{{ strtolower($borrowing->status) }}">

                    <!-- STUDENT -->
                    <td>
                        <div class="td-name">{{ $borrowing->user->name }}</div>
                        <div class="td-nim">{{ $borrowing->user->nim }}</div>
                    </td>

                    <!-- ROOM -->
                    <td>
                        <div class="td-room">{{ $borrowing->room->name }}</div>
                    </td>

                    <!-- SCHEDULE -->
                    <td>
                        <div class="td-date">{{ \Carbon\Carbon::parse($borrowing->borrow_date)->format('d M Y') }}</div>
                        <div class="td-time">{{ $borrowing->time_slot }}</div>
                    </td>

                    <!-- ITEMS -->
                    <td>
                        @foreach($borrowing->items as $item)
                            <div class="td-item">• {{ $item->item->name ?? '-' }} ({{ $item->qty }})</div>
                        @endforeach
                    </td>

                    <!-- STATUS -->
                    <td>
                        @if($borrowing->status == 'PENDING')
                            <span class="badge badge-pending">PENDING</span>
                        @elseif($borrowing->status == 'APPROVED')
                            <span class="badge badge-approved">APPROVED</span>
                        @elseif($borrowing->status == 'WAITING_RETURN')
                            <span class="badge badge-waiting-return">WAITING RETURN</span>
                        @elseif($borrowing->status == 'COMPLETED')
                            <span class="badge badge-completed">COMPLETED</span>
                        @elseif($borrowing->status == 'REJECTED')
                            <span class="badge badge-rejected">REJECTED</span>
                        @elseif($borrowing->status == 'CANCELLED')
                            <span class="badge badge-cancelled">CANCELLED</span>
                        @endif
                    </td>

                    <!-- ACTION -->
                    <td>
                        <div style="min-width:140px;">

                            @if(session('error_borrowing_id') == $borrowing->id)
                                <div class="inline-error">
                                    <span style="font-size:1rem;">⚠️</span>
                                    <div>
                                        <p class="inline-error-title">Terjadi Kesalahan</p>
                                        <p class="inline-error-msg">{{ session('error_message') }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($borrowing->status == 'PENDING')
                                <form method="POST" action="{{ route('admin.borrowings.approve', $borrowing) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn-action btn-approve">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.borrowings.reject', $borrowing) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn-action btn-reject">Reject</button>
                                </form>
                            @endif

                            @if($borrowing->status == 'WAITING_RETURN')
                                <form method="POST" action="{{ route('admin.borrowings.complete', $borrowing) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn-action btn-complete">Confirm Return</button>
                                </form>
                            @endif

                            @if(in_array($borrowing->status, ['APPROVED', 'WAITING_RETURN']))
                                <form method="POST" action="{{ route('admin.borrowings.cancel', $borrowing) }}">
                                    @csrf
                                    @method('PATCH')
                                    <textarea
                                        name="cancel_reason"
                                        required
                                        placeholder="Alasan pembatalan..."
                                        class="cancel-textarea"
                                        rows="2"
                                    ></textarea>
                                    <button
                                        onclick="return confirm('Batalkan peminjaman ini?')"
                                        class="btn-action btn-cancel"
                                    >Cancel Borrowing</button>
                                </form>
                            @endif

                        </div>
                    </td>

                </tr>

                @empty
                <tr>
                    <td colspan="6" style="padding:0;">
                        <div class="empty-state">
                            <div class="empty-icon-wrap">
                                <i class="ti ti-box"></i>
                            </div>
                            <p class="empty-title">Tidak ada peminjaman</p>
                            <p class="empty-sub">Belum ada peminjaman yang dibuat</p>
                        </div>
                    </td>
                </tr>
                @endforelse

            </tbody>
        </table>
    </div>

</div>

<script>
function filterTab(filter, btn) {
    // Update active tab
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('tab-active'));
    btn.classList.add('tab-active');

    const rows = document.querySelectorAll('.data-table tbody tr[data-status]');
    const aktif  = ['approved', 'waiting_return'];
    const history = ['completed', 'rejected', 'cancelled'];

    rows.forEach(row => {
        const status = row.dataset.status;
        if (filter === 'all') {
            row.style.display = '';
        } else if (filter === 'aktif') {
            row.style.display = aktif.includes(status) ? '' : 'none';
        } else if (filter === 'history') {
            row.style.display = history.includes(status) ? '' : 'none';
        }
    });

    // Show/hide empty state
    const visible = [...rows].filter(r => r.style.display !== 'none');
    let emptyRow = document.getElementById('empty-state-row');
    if (visible.length === 0) {
        if (!emptyRow) {
            const tbody = document.querySelector('.data-table tbody');
            const tr = document.createElement('tr');
            tr.id = 'empty-state-row';
            tr.innerHTML = `<td colspan="6" style="padding:0;">
                <div class="empty-state">
                    <div class="empty-icon-wrap"><i class="ti ti-box"></i></div>
                    <p class="empty-title">Tidak ada peminjaman</p>
                    <p class="empty-sub">Tidak ada data untuk filter ini</p>
                </div></td>`;
            tbody.appendChild(tr);
        } else {
            emptyRow.style.display = '';
        }
    } else if (emptyRow) {
        emptyRow.style.display = 'none';
    }
}
</script>

@endsection