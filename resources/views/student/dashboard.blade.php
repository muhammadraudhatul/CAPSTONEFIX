<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    @vite('resources/css/app.css')

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        * {
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: radial-gradient(ellipse at 30% 40%, #2a3f8f 0%, #1a2a6e 35%, #0d1540 100%);
            color: #fff;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
        }

        /* ── Navbar ── */
        .navbar {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 2rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            background: rgba(255,255,255,0.04);
            backdrop-filter: blur(12px);
        }

        .navbar-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: #fff;
            margin: 0;
        }

        .navbar-sub {
            font-size: 0.82rem;
            color: rgba(255,255,255,0.45);
            margin: 0.2rem 0 0;
        }

        .btn-logout {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(239,68,68,0.12);
            border: 1px solid rgba(239,68,68,0.3);
            color: #f87171;
            padding: 0.5rem 1.1rem;
            border-radius: 0.75rem;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
        }

        .btn-logout:hover {
            background: rgba(239,68,68,0.22);
        }

        /* ── Content ── */
        .content {
            position: relative;
            z-index: 1;
            padding: 2rem;
        }

        /* ── New Borrowing Button ── */
        .btn-new {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(90deg, #3b82f6, #06b6d4);
            color: #fff;
            font-weight: 700;
            font-size: 0.9rem;
            padding: 0.7rem 1.4rem;
            border-radius: 0.875rem;
            text-decoration: none;
            transition: opacity 0.2s, transform 0.15s;
            margin-bottom: 1.75rem;
        }

        .btn-new:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        /* ── Section Heading ── */
        .section-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.05rem;
            font-weight: 700;
            color: #fff;
            margin: 0 0 1rem;
        }

        .section-title i {
            font-size: 1.2rem;
            color: rgba(255,255,255,0.6);
        }

        /* ── Glass Card ── */
        .glass-card {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 1.1rem;
            backdrop-filter: blur(10px);
            margin-bottom: 1rem;
        }

        /* ── Borrowing Item Card ── */
        .borrow-card {
            padding: 1.5rem;
        }

        .borrow-card-inner {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .borrow-room {
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
            margin: 0 0 0.25rem;
        }

        .borrow-meta {
            font-size: 0.82rem;
            color: rgba(255,255,255,0.45);
            margin: 0 0 0.5rem;
        }

        .borrow-purpose {
            font-size: 0.88rem;
            color: rgba(255,255,255,0.65);
            margin: 0 0 0.75rem;
        }

        .borrow-items-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: rgba(255,255,255,0.55);
            margin-bottom: 0.25rem;
        }

        .borrow-items-list {
            font-size: 0.82rem;
            color: rgba(255,255,255,0.45);
            line-height: 1.6;
        }

        /* ── Right side actions ── */
        .borrow-right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.75rem;
            flex-shrink: 0;
        }

        /* ── Status Badges ── */
        .badge {
            display: inline-block;
            padding: 0.3rem 0.9rem;
            border-radius: 0.6rem;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.03em;
        }

        .badge-pending   { background: rgba(234,179,8,0.15);  color: #fbbf24; border: 1px solid rgba(234,179,8,0.25); }
        .badge-approved  { background: rgba(34,197,94,0.12);  color: #4ade80; border: 1px solid rgba(34,197,94,0.2); }
        .badge-waiting   { background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.25); }
        .badge-completed { background: rgba(148,163,184,0.12);color: #94a3b8; border: 1px solid rgba(148,163,184,0.2); }
        .badge-rejected  { background: rgba(239,68,68,0.12);  color: #f87171; border: 1px solid rgba(239,68,68,0.2); }
        .badge-cancelled { background: rgba(100,116,139,0.12);color: #94a3b8; border: 1px solid rgba(100,116,139,0.2); }

        /* ── Action Buttons ── */
        .action-row {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .btn-sm {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.4rem 0.9rem;
            border-radius: 0.6rem;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: opacity 0.2s;
        }

        .btn-sm:hover { opacity: 0.85; }

        .btn-edit     { background: rgba(59,130,246,0.25);  color: #93c5fd; border: 1px solid rgba(59,130,246,0.3); }
        .btn-delete   { background: rgba(239,68,68,0.2);    color: #fca5a5; border: 1px solid rgba(239,68,68,0.3); }
        .btn-return   { background: rgba(99,102,241,0.25);  color: #a5b4fc; border: 1px solid rgba(99,102,241,0.3); }
        .btn-cancel   { background: rgba(239,68,68,0.15);   color: #fca5a5; border: 1px solid rgba(239,68,68,0.25); }

        /* Cancel reason box */
        .cancel-reason {
            margin-top: 0.5rem;
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.2);
            border-radius: 0.6rem;
            padding: 0.65rem 0.85rem;
            max-width: 220px;
            text-align: left;
        }

        .cancel-reason-title { font-size: 0.75rem; font-weight: 700; color: #f87171; margin: 0 0 0.2rem; }
        .cancel-reason-text  { font-size: 0.78rem; color: rgba(248,113,113,0.8); margin: 0; }

        /* ── Empty State ── */
        .empty-state {
            text-align: center;
            padding: 2.5rem 1rem;
            color: rgba(255,255,255,0.3);
            font-size: 0.9rem;
        }

        /* ── Table ── */
        .glass-table {
            width: 100%;
            border-collapse: collapse;
        }

        .glass-table thead tr {
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .glass-table th {
            padding: 0.85rem 1.25rem;
            text-align: left;
            font-size: 0.8rem;
            font-weight: 600;
            color: rgba(255,255,255,0.45);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .glass-table td {
            padding: 1rem 1.25rem;
            font-size: 0.85rem;
            color: rgba(255,255,255,0.7);
            border-top: 1px solid rgba(255,255,255,0.06);
            vertical-align: top;
        }

        .glass-table tbody tr:hover {
            background: rgba(255,255,255,0.03);
        }

        /* Alert errors */
        .alert-error {
            background: rgba(239,68,68,0.12);
            border: 1px solid rgba(239,68,68,0.25);
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            color: #fca5a5;
            font-size: 0.85rem;
            margin-bottom: 1.25rem;
        }

        @media (max-width: 640px) {
            .navbar { padding: 1rem; }
            .content { padding: 1rem; }
            .borrow-card-inner { flex-direction: column; }
            .borrow-right { align-items: flex-start; }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div>
            <p class="navbar-title">Student Dashboard</p>
            <p class="navbar-sub">
                Selamat datang, {{ auth()->user()->name }} ({{ auth()->user()->nim }})
            </p>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="ti ti-logout"></i>
                Logout
            </button>
        </form>
    </nav>

    <!-- Content -->
    <div class="content">

        <x-alert-error />

        <!-- New Borrowing -->
        <a href="{{ route('student.borrowings.create') }}" class="btn-new">
            <i class="ti ti-plus"></i>
            Peminjaman Baru
        </a>

        <!-- ── Active Borrowings ── -->
        <div class="mt-2">
            <h2 class="section-title">
                <i class="ti ti-package"></i>
                Peminjaman Aktif
            </h2>

            @forelse($activeBorrowings as $borrowing)
                <div class="glass-card borrow-card">
                    <div class="borrow-card-inner">

                        <!-- Left -->
                        <div>
                            <p class="borrow-room">{{ $borrowing->room->name }}</p>
                            <p class="borrow-meta">
                                {{ \Carbon\Carbon::parse($borrowing->borrow_date)->format('d M Y') }}
                                &nbsp;•&nbsp;
                                {{ $borrowing->time_slot }}
                            </p>
                            <p class="borrow-purpose">{{ $borrowing->purpose }}</p>
                            <p class="borrow-items-label">Alat:</p>
                            <div class="borrow-items-list">
                                @foreach($borrowing->items as $item)
                                    <div>• {{ $item->item->name }} ({{ $item->qty }})</div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Right -->
                        <div class="borrow-right">

                            @if($borrowing->status == 'PENDING')
                                <span class="badge badge-pending">PENDING</span>
                                <div class="action-row">
                                    <a href="{{ route('student.borrowings.edit', $borrowing) }}" class="btn-sm btn-edit">Edit</a>
                                    <form method="POST" action="{{ route('student.borrowings.destroy', $borrowing) }}">
                                        @csrf @method('DELETE')
                                        <button onclick="return confirm('Hapus peminjaman ini?')" class="btn-sm btn-delete">Delete</button>
                                    </form>
                                </div>

                            @elseif($borrowing->status == 'APPROVED')
                                <span class="badge badge-approved">APPROVED</span>
                                <div class="action-row" style="flex-direction:column; align-items:flex-end;">
                                    <a href="{{ route('student.borrowings.return.form', $borrowing) }}" class="btn-sm btn-return">Selesaikan Peminjaman</a>
                                    <form method="POST" action="{{ route('student.borrowings.cancel', $borrowing) }}">
                                        @csrf @method('PATCH')
                                        <button onclick="return confirm('Batalkan peminjaman ini?')" class="btn-sm btn-cancel">Batalkan Peminjaman</button>
                                    </form>
                                </div>

                            @elseif($borrowing->status == 'WAITING_RETURN')
                                <span class="badge badge-waiting">WAITING RETURN</span>

                            @elseif($borrowing->status == 'COMPLETED')
                                <span class="badge badge-completed">COMPLETED</span>

                            @elseif($borrowing->status == 'REJECTED')
                                <span class="badge badge-rejected">REJECTED</span>

                            @elseif($borrowing->status == 'CANCELLED')
                                <span class="badge badge-cancelled">CANCELLED</span>
                                @if($borrowing->cancel_reason)
                                    <div class="cancel-reason">
                                        <p class="cancel-reason-title">Dibatalkan Admin</p>
                                        <p class="cancel-reason-text">{{ $borrowing->cancel_reason }}</p>
                                    </div>
                                @endif
                            @endif

                        </div>
                    </div>
                </div>

            @empty
                <div class="glass-card">
                    <div class="empty-state">Tidak ada peminjaman aktif</div>
                </div>
            @endforelse
        </div>

        <!-- ── History ── -->
        <div class="mt-8">
            <h2 class="section-title">
                <i class="ti ti-clock"></i>
                History Peminjaman
            </h2>

            <div class="glass-card" style="overflow:hidden;">
                <table class="glass-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Ruangan</th>
                            <th>Waktu</th>
                            <th>Alat</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($histories as $history)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($history->borrow_date)->format('d M Y') }}</td>
                                <td>{{ $history->room->name }}</td>
                                <td>{{ $history->time_slot }}</td>
                                <td>
                                    @foreach($history->items as $item)
                                        <div>• {{ $item->item->name }} ({{ $item->qty }})</div>
                                    @endforeach
                                </td>
                                <td>
                                    @if($history->status == 'COMPLETED')
                                        <span class="badge badge-completed">COMPLETED</span>
                                    @elseif($history->status == 'REJECTED')
                                        <span class="badge badge-rejected">REJECTED</span>
                                    @elseif($history->status == 'CANCELLED')
                                        <span class="badge badge-cancelled">CANCELLED</span>
                                        @if($history->cancel_reason)
                                            <div class="cancel-reason" style="max-width:180px; margin-top:0.4rem;">
                                                <p class="cancel-reason-title">Dibatalkan Admin</p>
                                                <p class="cancel-reason-text">{{ $history->cancel_reason }}</p>
                                            </div>
                                        @endif
                                    @else
                                        <span style="color:rgba(255,255,255,0.45);">{{ $history->status }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-state" style="border:none;">Belum ada history peminjaman</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>
</html>