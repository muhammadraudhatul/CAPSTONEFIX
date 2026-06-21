@extends('admin.layouts.app')

@section('content')

<style>
    /* ── Flip7 muted restyle: desain lebih lembut, tidak terlalu terang / ramai ── */
    .history-flip7-wrapper {
        --primary-teal:  #4B958F;
        --primary-light: #6FB2AC;
        --primary-dark:  #356F6B;
        --primary-bg:    #E7EFED;
        --accent-gold:   #D7BD62;
        --accent-light:  #E9DCA7;
        --accent-dark:   #B69A3E;
        --coral:         #C97A62;
        --coral-light:   #DDA18E;
        --coral-dark:    #A85F49;
        --cream:         #F7F3E8;
        --sky-blue:      #7FA8BF;
        --surface-base:  #EDF2F1;
        --surface-card:  #FAFBFA;
        --success:       #4F8E72;
        --error:         #B86155;

        --text-primary:  #1F2A29;
        --text-muted:    #657675;
        --text-soft:     #8A9997;
        --border-soft:   rgba(53, 111, 107, 0.12);
        --shadow-sm:     0 2px 8px rgba(31, 42, 41, 0.05);
        --shadow-card:   0 10px 28px rgba(53, 111, 107, 0.07);
        --shadow-soft:   0 14px 34px rgba(31, 42, 41, 0.06);

        min-height: calc(100vh - 1px);
        padding: 2rem 1.75rem;
        color: var(--text-primary);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'PingFang SC', 'Microsoft YaHei', sans-serif;
        font-size: 14px;
        line-height: 1.6;
        box-sizing: border-box;
        border-radius: 28px;
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(circle at 10% 10%, rgba(215, 189, 98, 0.10), transparent 24%),
            radial-gradient(circle at 88% 12%, rgba(75, 149, 143, 0.08), transparent 28%),
            linear-gradient(135deg, #F3F6F5 0%, var(--surface-base) 52%, #EEF3F1 100%);
    }

    .history-flip7-wrapper *,
    .history-flip7-wrapper *::before,
    .history-flip7-wrapper *::after {
        box-sizing: border-box;
    }

    .history-flip7-wrapper::before,
    .history-flip7-wrapper::after {
        content: '';
        position: absolute;
        border-radius: 28px;
        border: 1px solid rgba(53, 111, 107, 0.05);
        background: rgba(247, 243, 232, 0.20);
        pointer-events: none;
        z-index: 0;
    }

    .history-flip7-wrapper::before {
        width: 150px;
        height: 210px;
        right: -72px;
        top: 110px;
        transform: rotate(-8deg);
    }

    .history-flip7-wrapper::after {
        width: 108px;
        height: 146px;
        left: -52px;
        bottom: 138px;
        transform: rotate(10deg);
    }

    .history-flip7-main,
    .history-flip7-header,
    .export-actions,
    .table-card,
    .pagination-wrap {
        position: relative;
        z-index: 1;
    }

    .history-flip7-main {
        display: block;
    }

    /* ── Header ── */
    .history-flip7-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1.5rem;
        flex-wrap: wrap;
        padding: 1.65rem 1.8rem;
        border-radius: 30px;
        background: linear-gradient(135deg, rgba(247, 243, 232, 0.96), rgba(250, 251, 250, 0.95));
        border: 1px solid rgba(255, 255, 255, 0.80);
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }

    .history-flip7-header::before {
        content: '';
        position: absolute;
        inset: 0 auto 0 0;
        width: 6px;
        background: linear-gradient(180deg, rgba(215, 189, 98, 0.85), rgba(75, 149, 143, 0.85), rgba(201, 122, 98, 0.78));
    }

    .history-flip7-header::after {
        content: 'HISTORY';
        position: absolute;
        right: 1.5rem;
        bottom: 1rem;
        color: rgba(53, 111, 107, 0.05);
        font-weight: 900;
        font-size: clamp(1.7rem, 5vw, 3.4rem);
        line-height: 1;
        letter-spacing: 0.08em;
        pointer-events: none;
    }

    .history-title-block,
    .summary-grid {
        position: relative;
        z-index: 1;
    }

    .history-flip7-wrapper h1 {
        font-size: clamp(1.9rem, 3vw, 2.55rem);
        font-weight: 900;
        color: var(--text-primary);
        letter-spacing: -0.045em;
        margin: 0 0 0.45rem;
        line-height: 1.05;
        text-shadow: 2px 2px 0 rgba(215, 189, 98, 0.22);
    }

    .history-flip7-wrapper .history-subtitle {
        color: #4C8A85;
        font-size: 0.9rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        margin: 0;
    }

    /* ── Summary cards ── */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(150px, 1fr));
        gap: 0.85rem;
        min-width: min(100%, 500px);
    }

    .history-flip7-wrapper .stat-card {
        background: linear-gradient(145deg, rgba(252, 252, 251, 0.98) 0%, rgba(247, 243, 232, 0.92) 100%);
        border: 1px solid rgba(255, 255, 255, 0.82);
        border-left: 7px solid var(--primary-light);
        border-radius: 22px;
        padding: 1.1rem 1.2rem 1.15rem;
        min-width: 160px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 6px 18px rgba(53, 111, 107, 0.06);
        transition: transform 0.24s cubic-bezier(.2,.8,.2,1), box-shadow 0.24s, border-color 0.24s;
    }

    .history-flip7-wrapper .stat-card::before {
        content: '';
        position: absolute;
        width: 76px;
        height: 76px;
        right: -30px;
        top: -30px;
        border-radius: 999px;
        background: rgba(75, 149, 143, 0.07);
        pointer-events: none;
    }

    .history-flip7-wrapper .stat-card::after {
        content: '';
        position: absolute;
        left: 1.2rem;
        bottom: 0;
        width: 42px;
        height: 4px;
        border-radius: 999px 999px 0 0;
        background: rgba(215, 189, 98, 0.85);
    }

    .history-flip7-wrapper .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 22px rgba(53, 111, 107, 0.08);
        border-color: rgba(75, 149, 143, 0.16);
    }

    .history-flip7-wrapper .stat-card.stat-total {
        border-left-color: var(--accent-gold);
    }

    .history-flip7-wrapper .stat-card.stat-borrow {
        border-left-color: var(--coral);
    }

    .history-flip7-wrapper .stat-card.stat-return {
        border-left-color: var(--primary-teal);
    }

    .history-flip7-wrapper .stat-card.stat-total::before {
        background: rgba(215, 189, 98, 0.14);
    }

    .history-flip7-wrapper .stat-card.stat-borrow::before {
        background: rgba(201, 122, 98, 0.10);
    }

    .history-flip7-wrapper .stat-label {
        position: relative;
        z-index: 1;
        font-size: 0.72rem;
        color: var(--text-muted);
        margin: 0 0 0.55rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.11em;
    }

    .history-flip7-wrapper .stat-value {
        position: relative;
        z-index: 1;
        font-size: clamp(1.65rem, 3vw, 2.05rem);
        font-weight: 900;
        margin: 0;
        line-height: 1;
        letter-spacing: -0.04em;
    }

    .history-flip7-wrapper .stat-value.total-value { color: var(--text-primary); }
    .history-flip7-wrapper .stat-value.borrow-value { color: #C56E57; }
    .history-flip7-wrapper .stat-value.return-value { color: #46827D; }

    /* ── Export buttons ── */
    .export-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }

    .history-flip7-wrapper .btn-export-excel,
    .history-flip7-wrapper .btn-export-csv {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        min-height: 46px;
        padding: 0.75rem 1.35rem;
        border-radius: 999px;
        font-size: 0.88rem;
        font-weight: 900;
        text-decoration: none;
        letter-spacing: 0.01em;
        border: 1px solid rgba(255, 255, 255, 0.75);
        overflow: hidden;
        transition: transform 0.25s cubic-bezier(.2,.8,.2,1), box-shadow 0.25s, filter 0.25s;
        box-shadow: 0 8px 20px rgba(31, 42, 41, 0.05);
    }

    .history-flip7-wrapper .btn-export-excel::before,
    .history-flip7-wrapper .btn-export-csv::before {
        content: '';
        position: absolute;
        inset: 0 0 auto 0;
        height: 42%;
        background: linear-gradient(180deg, rgba(255,255,255,0.18), rgba(255,255,255,0));
        pointer-events: none;
    }

    .history-flip7-wrapper .btn-export-excel::after {
        content: '⬇';
        position: relative;
        z-index: 1;
        font-size: 0.92rem;
    }

    .history-flip7-wrapper .btn-export-csv::after {
        content: '↗';
        position: relative;
        z-index: 1;
        font-size: 0.92rem;
    }

    .history-flip7-wrapper .btn-export-excel {
        color: #43340E;
        background: linear-gradient(135deg, #E6D58C, #D7BD62);
    }

    .history-flip7-wrapper .btn-export-csv {
        color: #F8FBFB;
        background: linear-gradient(135deg, #7DB4AF, #4B958F);
    }

    .history-flip7-wrapper .btn-export-excel:hover,
    .history-flip7-wrapper .btn-export-csv:hover {
        transform: translateY(-2px);
        filter: saturate(0.96);
    }

    .history-flip7-wrapper .btn-export-excel:active,
    .history-flip7-wrapper .btn-export-csv:active {
        transform: scale(0.97);
    }

    /* ── Table card ── */
    .history-flip7-wrapper .table-card {
        background: linear-gradient(180deg, rgba(250,251,250,0.98), rgba(244, 241, 234, 0.88));
        border: 1px solid rgba(255, 255, 255, 0.82);
        border-left: 7px solid var(--primary-teal);
        border-radius: 26px;
        overflow-x: auto;
        width: 100%;
        margin-top: 1.75rem;
        box-shadow: var(--shadow-soft);
        transition: transform 0.24s cubic-bezier(.2,.8,.2,1), box-shadow 0.24s, border-color 0.24s;
    }

    .history-flip7-wrapper .table-card:hover {
        transform: translateY(-2px);
        border-color: rgba(75, 149, 143, 0.12);
        box-shadow: 0 16px 32px rgba(31, 42, 41, 0.07);
    }

    .history-flip7-wrapper .table-card::before {
        content: '';
        display: block;
        height: 8px;
        background: linear-gradient(90deg, rgba(215, 189, 98, 0.95), rgba(75, 149, 143, 0.92), rgba(201, 122, 98, 0.86));
    }

    .history-flip7-wrapper .data-table {
        width: 100%;
        min-width: 820px;
        border-collapse: collapse;
    }

    .history-flip7-wrapper .data-table thead tr {
        background: rgba(231, 239, 237, 0.88);
    }

    .history-flip7-wrapper .data-table thead th {
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

    .history-flip7-wrapper .data-table tbody tr {
        border-bottom: 1px solid rgba(75, 149, 143, 0.08);
        transition: background 0.18s ease, transform 0.18s ease;
    }

    .history-flip7-wrapper .data-table tbody tr:last-child {
        border-bottom: none;
    }

    .history-flip7-wrapper .data-table tbody tr:hover {
        background: rgba(247, 243, 232, 0.58);
    }

    .history-flip7-wrapper .data-table td {
        padding: 1rem 1.25rem;
        color: var(--text-muted);
        font-size: 0.9rem;
        vertical-align: middle;
        background: transparent;
    }

    .history-flip7-wrapper .td-date-main {
        color: var(--text-primary);
        font-weight: 900;
        font-size: 0.9rem;
        white-space: nowrap;
    }

    .history-flip7-wrapper .td-date-time {
        display: inline-flex;
        margin-top: 0.35rem;
        color: var(--primary-dark);
        font-size: 0.74rem;
        font-weight: 800;
        padding: 0.16rem 0.48rem;
        border-radius: 999px;
        background: rgba(75, 149, 143, 0.10);
    }

    .history-flip7-wrapper .td-item-name {
        color: var(--text-primary);
        font-weight: 900;
    }

    .history-flip7-wrapper .td-desc {
        color: var(--text-muted);
        font-weight: 700;
        max-width: 420px;
    }

    /* action badges */
    .history-flip7-wrapper .badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 28px;
        padding: 0.25rem 0.7rem;
        border-radius: 999px;
        font-size: 0.68rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        margin-top: 0.45rem;
        border: 1px solid rgba(255, 255, 255, 0.60);
        box-shadow: var(--shadow-sm);
    }

    .history-flip7-wrapper .badge-borrow {
        background: linear-gradient(135deg, rgba(201,122,98,0.14), rgba(201,122,98,0.08));
        color: var(--coral-dark);
    }

    .history-flip7-wrapper .badge-return {
        background: linear-gradient(135deg, rgba(75,149,143,0.15), rgba(75,149,143,0.08));
        color: var(--primary-dark);
    }

    .history-flip7-wrapper .badge-cancel {
        background: linear-gradient(135deg, rgba(215,189,98,0.18), rgba(215,189,98,0.10));
        color: #7F6B2A;
    }

    .history-flip7-wrapper .badge-create {
        background: linear-gradient(135deg, rgba(127,168,191,0.16), rgba(127,168,191,0.08));
        color: #54768B;
    }

    .history-flip7-wrapper .badge-update {
        background: linear-gradient(135deg, rgba(215,189,98,0.18), rgba(75,149,143,0.08));
        color: var(--primary-dark);
    }

    .history-flip7-wrapper .badge-delete {
        background: linear-gradient(135deg, rgba(31,42,41,0.06), rgba(201,122,98,0.08));
        color: #7A645D;
    }

    /* stock change */
    .history-flip7-wrapper .stock-row {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        color: var(--text-muted);
        font-weight: 900;
        padding: 0.35rem 0.7rem;
        border-radius: 999px;
        background: rgba(231, 239, 237, 0.90);
        border: 1px solid rgba(75, 149, 143, 0.10);
    }

    .history-flip7-wrapper .stock-arrow {
        color: var(--coral);
        font-weight: 900;
    }

    .history-flip7-wrapper .stock-new {
        color: var(--text-primary);
    }

    .history-flip7-wrapper .stock-diff-pos,
    .history-flip7-wrapper .stock-diff-neg,
    .history-flip7-wrapper .stock-diff-zero {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 24px;
        padding: 0.12rem 0.55rem;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 900;
        margin-top: 0.35rem;
    }

    .history-flip7-wrapper .stock-diff-pos {
        color: var(--primary-dark);
        background: rgba(75, 149, 143, 0.10);
    }

    .history-flip7-wrapper .stock-diff-neg {
        color: var(--coral-dark);
        background: rgba(201, 122, 98, 0.10);
    }

    .history-flip7-wrapper .stock-diff-zero {
        color: var(--text-soft);
        background: rgba(31, 42, 41, 0.05);
    }

    .history-flip7-wrapper .stock-empty {
        color: var(--text-soft);
        font-weight: 900;
    }

    .history-flip7-wrapper .td-user {
        color: var(--text-primary);
        font-weight: 800;
    }

    .history-flip7-wrapper .empty-cell {
        text-align: center;
        padding: 3.5rem !important;
        color: var(--text-soft) !important;
        font-weight: 800;
        background: rgba(247, 243, 232, 0.46);
    }

    /* pagination override */
    .history-flip7-wrapper .pagination-wrap {
        margin-top: 1.5rem;
    }

    .history-flip7-wrapper .pagination-wrap nav span,
    .history-flip7-wrapper .pagination-wrap nav a {
        background: linear-gradient(135deg, #F7F3E8, #FAFBFA) !important;
        border-color: rgba(75, 149, 143, 0.12) !important;
        color: var(--primary-dark) !important;
        border-radius: 12px !important;
        font-weight: 800 !important;
        box-shadow: 0 2px 8px rgba(53, 111, 107, 0.05) !important;
    }

    .history-flip7-wrapper .pagination-wrap nav a:hover {
        background: linear-gradient(135deg, #7DB4AF, #4B958F) !important;
        color: #FFFFFF !important;
        box-shadow: 0 8px 18px rgba(75, 149, 143, 0.10) !important;
    }

    .history-flip7-wrapper .pagination-wrap nav [aria-current="page"] span {
        background: linear-gradient(135deg, #E6D58C, #D7BD62) !important;
        border-color: rgba(182, 154, 62, 0.25) !important;
        color: #43340E !important;
        box-shadow: 0 8px 18px rgba(215, 189, 98, 0.10) !important;
    }

    /* ── Animate in ── */
    @keyframes historyFadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .history-flip7-header,
    .export-actions,
    .table-card,
    .pagination-wrap,
    .history-flip7-wrapper .stat-card {
        animation: historyFadeUp 0.35s ease both;
    }

    .history-flip7-wrapper .stat-card:nth-child(1) { animation-delay: 0.04s; }
    .history-flip7-wrapper .stat-card:nth-child(2) { animation-delay: 0.08s; }
    .history-flip7-wrapper .stat-card:nth-child(3) { animation-delay: 0.12s; }
    .export-actions { animation-delay: 0.14s; }
    .table-card { animation-delay: 0.18s; }

    @media (max-width: 1100px) {
        .summary-grid {
            width: 100%;
            min-width: 0;
        }
    }

    @media (max-width: 760px) {
        .history-flip7-wrapper {
            padding: 1.25rem 1rem;
            border-radius: 22px;
        }

        .history-flip7-header {
            padding: 1.35rem 1.25rem;
            border-radius: 24px;
        }

        .summary-grid {
            grid-template-columns: 1fr;
        }

        .history-flip7-wrapper .stat-card {
            min-width: 0;
        }

        .export-actions {
            flex-direction: column;
        }

        .history-flip7-wrapper .btn-export-excel,
        .history-flip7-wrapper .btn-export-csv {
            width: 100%;
        }
    }
</style>

<div class="history-flip7-wrapper">
    <div class="history-flip7-main">

        <!-- HEADER -->
        <div class="history-flip7-header">

            <div class="history-title-block">
                <h1>
                    History Inventory
                </h1>
                <p class="history-subtitle">
                    Semua aktivitas perubahan inventory laboratorium
                </p>
            </div>

            <!-- SUMMARY -->
            <div class="summary-grid">

                <div class="stat-card stat-total">
                    <p class="stat-label">Total Aktivitas</p>
                    <h2 class="stat-value total-value">{{ $histories->total() }}</h2>
                </div>

                <div class="stat-card stat-borrow">
                    <p class="stat-label">Peminjaman</p>
                    <h2 class="stat-value borrow-value">{{ $histories->where('action', 'borrow')->count() }}</h2>
                </div>

                <div class="stat-card stat-return">
                    <p class="stat-label">Pengembalian</p>
                    <h2 class="stat-value return-value">{{ $histories->where('action', 'return')->count() }}</h2>
                </div>

            </div>

        </div>

        <!-- EXPORT -->
        <div class="export-actions">
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
                                <span class="stock-empty">-</span>
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
        <div class="pagination-wrap">
            {{ $histories->links() }}
        </div>

    </div>
</div>

@endsection
