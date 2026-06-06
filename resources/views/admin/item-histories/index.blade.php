@extends('admin.layouts.app')

@section('content')

<style>
    /* ── Summary cards ── */
    .stat-card {
        background: #1c2544;
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 0.9rem;
        padding: 1.25rem 1.5rem;
        min-width: 160px;
    }

    .stat-label {
        font-size: 0.78rem;
        color: rgba(255,255,255,0.4);
        margin: 0 0 0.5rem;
    }

    .stat-value {
        font-size: 1.9rem;
        font-weight: 800;
        margin: 0;
        line-height: 1;
    }

    /* ── Export buttons ── */
    .btn-export-excel {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.7rem 1.25rem;
        background: rgba(52,211,153,0.15);
        border: 1px solid rgba(52,211,153,0.25);
        border-radius: 0.7rem;
        color: #6ee7b7;
        font-size: 0.88rem;
        font-weight: 600;
        text-decoration: none;
        transition: background 0.18s;
    }

    .btn-export-excel:hover { background: rgba(52,211,153,0.25); }

    .btn-export-csv {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.7rem 1.25rem;
        background: rgba(96,165,250,0.15);
        border: 1px solid rgba(96,165,250,0.25);
        border-radius: 0.7rem;
        color: #93c5fd;
        font-size: 0.88rem;
        font-weight: 600;
        text-decoration: none;
        transition: background 0.18s;
    }

    .btn-export-csv:hover { background: rgba(96,165,250,0.25); }

    /* ── Table card ── */
    .table-card {
        background: #1c2544;
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 1.1rem;
        overflow: hidden;
        width: 100%;
        margin-top: 2rem;
    }

    .data-table { width: 100%; border-collapse: collapse; }

    .data-table thead tr { background: rgba(255,255,255,0.04); }

    .data-table thead th {
        padding: 0.9rem 1.25rem;
        text-align: left;
        font-size: 0.78rem;
        font-weight: 600;
        color: rgba(255,255,255,0.4);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        border-bottom: 1px solid rgba(255,255,255,0.06);
    }

    .data-table tbody tr {
        border-bottom: 1px solid rgba(255,255,255,0.05);
        transition: background 0.15s;
    }

    .data-table tbody tr:last-child { border-bottom: none; }
    .data-table tbody tr:hover { background: rgba(255,255,255,0.03); }

    .data-table td {
        padding: 1rem 1.25rem;
        color: rgba(255,255,255,0.55);
        font-size: 0.9rem;
        vertical-align: middle;
    }

    .td-date-main  { color: #fff; font-weight: 600; font-size: 0.9rem; }
    .td-date-time  { color: rgba(255,255,255,0.3); font-size: 0.78rem; margin-top: 0.2rem; }
    .td-item-name  { color: #fff; font-weight: 600; }
    .td-desc       { color: rgba(255,255,255,0.7); font-weight: 500; }

    /* action badges */
    .badge {
        display: inline-block;
        padding: 0.2rem 0.65rem;
        border-radius: 0.4rem;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-top: 0.4rem;
    }

    .badge-borrow  { background: rgba(248,113,113,0.15); color: #fca5a5; }
    .badge-return  { background: rgba(52,211,153,0.15);  color: #6ee7b7; }
    .badge-cancel  { background: rgba(251,191,36,0.15);  color: #fcd34d; }
    .badge-create  { background: rgba(96,165,250,0.15);  color: #93c5fd; }
    .badge-update  { background: rgba(167,139,250,0.15); color: #c4b5fd; }
    .badge-delete  { background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.4); }

    /* stock change */
    .stock-row { display: flex; align-items: center; gap: 0.5rem; color: rgba(255,255,255,0.6); font-weight: 600; }
    .stock-arrow { color: rgba(255,255,255,0.2); }
    .stock-new  { color: #fff; }
    .stock-diff-pos { color: #6ee7b7; font-size: 0.82rem; font-weight: 700; margin-top: 0.25rem; }
    .stock-diff-neg { color: #fca5a5; font-size: 0.82rem; font-weight: 700; margin-top: 0.25rem; }
    .stock-diff-zero{ color: rgba(255,255,255,0.25); font-size: 0.82rem; margin-top: 0.25rem; }

    .td-user { color: rgba(255,255,255,0.7); font-weight: 500; }

    .empty-cell {
        text-align: center;
        padding: 3.5rem !important;
        color: rgba(255,255,255,0.2) !important;
    }

    /* pagination override */
    .pagination-wrap nav span,
    .pagination-wrap nav a {
        background: #1c2544 !important;
        border-color: rgba(255,255,255,0.08) !important;
        color: rgba(255,255,255,0.5) !important;
    }

    .pagination-wrap nav a:hover {
        background: rgba(139,92,246,0.2) !important;
        color: #fff !important;
    }

    .pagination-wrap nav [aria-current="page"] span {
        background: linear-gradient(90deg,#8b5cf6,#ec4899) !important;
        border-color: transparent !important;
        color: #fff !important;
    }
</style>

<div>

    <!-- HEADER -->
    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:1.5rem; flex-wrap:wrap;">

        <div>
            <h1 style="font-size:2.25rem; font-weight:800; color:#fff; letter-spacing:-0.02em; margin:0 0 0.35rem;">
                History Inventory
            </h1>
            <p style="color:rgba(255,255,255,0.4); font-size:0.95rem; margin:0;">
                Semua aktivitas perubahan inventory laboratorium
            </p>
        </div>

        <!-- SUMMARY -->
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:0.75rem;">

            <div class="stat-card">
                <p class="stat-label">Total Aktivitas</p>
                <h2 class="stat-value" style="color:#fff;">{{ $histories->total() }}</h2>
            </div>

            <div class="stat-card">
                <p class="stat-label">Peminjaman</p>
                <h2 class="stat-value" style="color:#fca5a5;">{{ $histories->where('action', 'borrow')->count() }}</h2>
            </div>

            <div class="stat-card">
                <p class="stat-label">Pengembalian</p>
                <h2 class="stat-value" style="color:#6ee7b7;">{{ $histories->where('action', 'return')->count() }}</h2>
            </div>

        </div>

    </div>

    <!-- EXPORT -->
    <div style="display:flex; gap:0.65rem; margin-top:1.75rem;">
        <a href="{{ route('item-histories.export.excel') }}" class="btn-export-excel">
            Download Excel
        </a>
        <a href="{{ route('item-histories.export.csv') }}" class="btn-export-csv">
            Download CSV
        </a>
    </div>

    <!-- TABLE -->
    <div class="table-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Item</th>
                    <th>Aktivitas</th>
                    <th>Perubahan Stok</th>
                    <th>User</th>
                </tr>
            </thead>
            <tbody>

                @forelse($histories as $history)
                <tr>

                    <!-- DATE -->
                    <td style="white-space:nowrap;">
                        <div class="td-date-main">{{ $history->created_at->format('d M Y') }}</div>
                        <div class="td-date-time">{{ $history->created_at->format('H:i') }}</div>
                    </td>

                    <!-- ITEM -->
                    <td>
                        <div class="td-item-name">{{ $history->item_name }}</div>
                    </td>

                    <!-- ACTIVITY -->
                    <td>
                        <div class="td-desc">{{ $history->description }}</div>
                        <div>
                            @if($history->action == 'borrow')
                                <span class="badge badge-borrow">Borrow</span>
                            @elseif($history->action == 'return')
                                <span class="badge badge-return">Return</span>
                            @elseif($history->action == 'cancel')
                                <span class="badge badge-cancel">Cancel</span>
                            @elseif($history->action == 'create')
                                <span class="badge badge-create">Create</span>
                            @elseif($history->action == 'update')
                                <span class="badge badge-update">Update</span>
                            @elseif($history->action == 'delete')
                                <span class="badge badge-delete">Delete</span>
                            @endif
                        </div>
                    </td>

                    <!-- STOCK -->
                    <td>
                        @if(!is_null($history->old_stock) && !is_null($history->new_stock))
                            <div class="stock-row">
                                <span>{{ $history->old_stock }}</span>
                                <span class="stock-arrow">→</span>
                                <span class="stock-new">{{ $history->new_stock }}</span>
                            </div>
                            @php $difference = $history->new_stock - $history->old_stock; @endphp
                            @if($difference > 0)
                                <div class="stock-diff-pos">+{{ $difference }}</div>
                            @elseif($difference < 0)
                                <div class="stock-diff-neg">{{ $difference }}</div>
                            @else
                                <div class="stock-diff-zero">0</div>
                            @endif
                        @else
                            <span style="color:rgba(255,255,255,0.2);">-</span>
                        @endif
                    </td>

                    <!-- USER -->
                    <td>
                        <div class="td-user">{{ $history->user->name ?? '-' }}</div>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="5" class="empty-cell">Belum ada history inventory</td>
                </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    <!-- PAGINATION -->
    <div class="pagination-wrap" style="margin-top:1.5rem;">
        {{ $histories->links() }}
    </div>

</div>

@endsection