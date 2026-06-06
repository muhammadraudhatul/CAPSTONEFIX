@extends('admin.layouts.app')

@section('content')

<style>
    /* ── Stat cards ── */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-top: 1.75rem;
    }

    .stat-card {
        background: #1c2544;
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 1rem;
        padding: 1.4rem 1.4rem 1.2rem;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 3px;
        border-radius: 3px 0 0 3px;
    }

    .stat-card-purple::before { background: #8b5cf6; }
    .stat-card-blue::before   { background: #3b82f6; }
    .stat-card-orange::before { background: #f59e0b; }
    .stat-card-gray::before   { background: #64748b; }

    .stat-card-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .stat-icon {
        width: 46px; height: 46px;
        border-radius: 0.75rem;
        display: flex; align-items: center; justify-content: center;
    }

    .stat-icon-purple { background: linear-gradient(135deg,#a855f7,#ec4899); }
    .stat-icon-blue   { background: linear-gradient(135deg,#3b82f6,#06b6d4); }
    .stat-icon-orange { background: linear-gradient(135deg,#f59e0b,#ef4444); }
    .stat-icon-gray   { background: rgba(255,255,255,0.1); }

    .stat-icon i { font-size: 1.25rem; color: #fff; }

    .stat-trend { color: #6ee7b7; font-size: 1rem; }

    .stat-label {
        font-size: 0.82rem;
        color: rgba(255,255,255,0.45);
        margin: 0 0 0.35rem;
    }

    .stat-value {
        font-size: 2.25rem;
        font-weight: 800;
        color: #fff;
        margin: 0 0 0.25rem;
        line-height: 1;
    }

    .stat-sub {
        font-size: 0.75rem;
        color: rgba(255,255,255,0.25);
        margin: 0;
    }

    /* ── Tabs ── */
    .tab-bar {
        display: inline-flex;
        background: #1c2544;
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 0.85rem;
        padding: 0.3rem;
        gap: 0.2rem;
        margin-top: 1.75rem;
    }

    .tab-btn {
        padding: 0.55rem 1.25rem;
        border-radius: 0.6rem;
        border: none;
        background: none;
        color: rgba(255,255,255,0.45);
        font-size: 0.88rem;
        font-weight: 600;
        cursor: pointer;
        font-family: 'Inter', sans-serif;
        transition: background 0.18s, color 0.18s;
        white-space: nowrap;
    }

    .tab-btn:hover { color: rgba(255,255,255,0.75); }

    .tab-btn.tab-active {
        background: linear-gradient(90deg,#8b5cf6,#ec4899);
        color: #fff;
        box-shadow: 0 3px 10px rgba(139,92,246,0.3);
    }

    /* ── Table card ── */
    .table-card {
        background: #1c2544;
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 1.1rem;
        overflow: hidden;
        width: 100%;
        margin-top: 1.25rem;
    }

    .data-table { width: 100%; border-collapse: collapse; }

    .data-table thead tr { background: rgba(255,255,255,0.04); }

    .data-table thead th {
        padding: 0.9rem 1.25rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 600;
        color: rgba(255,255,255,0.35);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        border-bottom: 1px solid rgba(255,255,255,0.06);
    }

    .data-table thead th.th-center { text-align: center; }

    .data-table tbody tr {
        border-bottom: 1px solid rgba(255,255,255,0.05);
        transition: background 0.15s;
        vertical-align: top;
    }

    .data-table tbody tr:last-child { border-bottom: none; }
    .data-table tbody tr:hover { background: rgba(255,255,255,0.025); }

    .data-table td { padding: 1rem 1.25rem; font-size: 0.88rem; }

    /* cell styles */
    .td-name    { color: #fff; font-weight: 700; font-size: 0.9rem; }
    .td-nim     { color: rgba(255,255,255,0.35); font-size: 0.78rem; margin-top: 0.2rem; }
    .td-room    { color: rgba(255,255,255,0.75); font-weight: 600; }
    .td-date    { color: rgba(255,255,255,0.75); font-weight: 600; }
    .td-time    { color: rgba(255,255,255,0.35); font-size: 0.8rem; margin-top: 0.2rem; }
    .td-item    { color: rgba(255,255,255,0.55); font-size: 0.82rem; margin-bottom: 0.2rem; }

    /* status badges */
    .badge {
        display: inline-block;
        padding: 0.3rem 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        white-space: nowrap;
    }

    .badge-pending        { background: rgba(251,191,36,0.15);  color: #fcd34d; }
    .badge-approved       { background: rgba(52,211,153,0.15);  color: #6ee7b7; }
    .badge-waiting-return { background: rgba(96,165,250,0.15);  color: #93c5fd; }
    .badge-completed      { background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.4); }
    .badge-rejected       { background: rgba(248,113,113,0.15); color: #fca5a5; }
    .badge-cancelled      { background: rgba(255,255,255,0.06); color: rgba(255,255,255,0.3); }

    /* action buttons */
    .btn-action {
        display: block;
        width: 100%;
        padding: 0.5rem 0.85rem;
        border-radius: 0.55rem;
        border: none;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        font-family: 'Inter', sans-serif;
        transition: opacity 0.18s;
        text-align: center;
        margin-bottom: 0.4rem;
        white-space: nowrap;
    }

    .btn-action:last-child { margin-bottom: 0; }
    .btn-action:hover { opacity: 0.82; }

    .btn-approve  { background: rgba(52,211,153,0.2);  color: #6ee7b7; border: 1px solid rgba(52,211,153,0.3); }
    .btn-reject   { background: rgba(248,113,113,0.2); color: #fca5a5; border: 1px solid rgba(248,113,113,0.3); }
    .btn-complete { background: rgba(167,139,250,0.2); color: #c4b5fd; border: 1px solid rgba(167,139,250,0.3); }
    .btn-cancel   { background: rgba(248,113,113,0.15); color: #fca5a5; border: 1px solid rgba(248,113,113,0.2); }

    .cancel-textarea {
        width: 100%;
        background: rgba(255,255,255,0.05) !important;
        border: 1px solid rgba(255,255,255,0.1) !important;
        border-radius: 0.55rem !important;
        padding: 0.5rem 0.75rem !important;
        color: #fff !important;
        font-size: 0.78rem;
        font-family: 'Inter', sans-serif;
        resize: vertical;
        outline: none;
        margin-bottom: 0.4rem;
    }

    .cancel-textarea::placeholder { color: rgba(255,255,255,0.2) !important; }
    .cancel-textarea:focus { border-color: rgba(248,113,113,0.4) !important; }

    /* error inline */
    .inline-error {
        background: rgba(239,68,68,0.1);
        border: 1px solid rgba(239,68,68,0.25);
        border-radius: 0.65rem;
        padding: 0.75rem 1rem;
        margin-bottom: 0.6rem;
        display: flex;
        gap: 0.5rem;
    }

    .inline-error-title { font-weight: 700; color: #fca5a5; font-size: 0.82rem; }
    .inline-error-msg   { color: rgba(248,113,113,0.8); font-size: 0.78rem; margin-top: 0.15rem; }

    /* empty state */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 5rem 2rem;
        text-align: center;
    }

    .empty-icon-wrap {
        width: 72px; height: 72px;
        background: rgba(255,255,255,0.07);
        border-radius: 1.1rem;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 1.25rem;
    }

    .empty-icon-wrap i { font-size: 2rem; color: rgba(255,255,255,0.3); }
    .empty-title { font-size: 1.1rem; font-weight: 700; color: rgba(255,255,255,0.6); margin: 0 0 0.35rem; }
    .empty-sub   { font-size: 0.85rem; color: rgba(255,255,255,0.25); margin: 0; }
</style>

<div>

    <!-- HEADER -->
    <h1 style="font-size:2.25rem; font-weight:800; color:#fff; letter-spacing:-0.02em; margin:0 0 0.35rem;">
        Peminjaman
    </h1>
    <p style="color:rgba(255,255,255,0.4); font-size:0.95rem; margin:0;">
        Kelola semua peminjaman dari seluruh student
    </p>

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