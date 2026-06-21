<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    @vite('resources/css/app.css')

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

        * {
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
        }

        :root {
            --primary-teal: #4B958F;
            --primary-light: #75B6B0;
            --primary-dark: #356F6B;
            --primary-bg: #E7EFED;
            --accent-gold: #D7BD62;
            --accent-light: #E9DCA7;
            --coral: #C97A62;
            --coral-dark: #A85F49;
            --sky-blue: #7FA8BF;
            --blue: #4C63F4;
            --cream: #F7F3E8;
            --surface-base: #EDF2F1;
            --surface-card: #FAFBFA;
            --text-primary: #1F2A29;
            --text-muted: #657675;
            --text-soft: #8A9997;
            --line: rgba(53, 111, 107, 0.13);
            --line-light: rgba(255, 255, 255, 0.82);
            --shadow-sm: 0 2px 8px rgba(31, 42, 41, 0.05);
            --shadow-card: 0 10px 28px rgba(53, 111, 107, 0.07);
            --shadow-soft: 0 14px 34px rgba(31, 42, 41, 0.06);
            --shadow-blue: 0 14px 28px rgba(76, 99, 244, 0.18);
            --shadow-teal: 0 12px 26px rgba(75, 149, 143, 0.12);
        }

        body {
            margin: 0;
            min-height: 100vh;
            color: var(--text-primary);
            background:
                radial-gradient(circle at 50% 0%, rgba(110, 139, 255, 0.14), transparent 34%),
                radial-gradient(circle at 8% 84%, rgba(75, 149, 143, 0.09), transparent 30%),
                radial-gradient(circle at 90% 72%, rgba(215, 189, 98, 0.10), transparent 26%),
                linear-gradient(180deg, #F8FBFF 0%, #F7F9FD 48%, #F2F6F5 100%);
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(28, 42, 78, 0.045) 1px, transparent 1px),
                linear-gradient(90deg, rgba(28, 42, 78, 0.045) 1px, transparent 1px);
            background-size: 36px 36px;
            mask-image: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.72) 18%, rgba(0,0,0,0.72) 88%, transparent 100%);
            pointer-events: none;
            z-index: 0;
        }

        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background:
                linear-gradient(110deg, transparent 0%, transparent 44%, rgba(255,255,255,0.55) 46%, transparent 58%),
                radial-gradient(circle at 78% 12%, rgba(76, 99, 244, 0.055), transparent 22%);
            pointer-events: none;
            z-index: 0;
        }

        /* ── Navbar ── */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 20;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: 1rem 2rem;
            border-bottom: 1px solid rgba(53, 111, 107, 0.10);
            background: rgba(255, 255, 255, 0.76);
            backdrop-filter: blur(16px);
            box-shadow: 0 8px 24px rgba(31, 42, 41, 0.04);
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            min-width: 0;
        }

        .navbar-icon {
            width: 44px;
            height: 44px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #5E78FF, var(--blue));
            color: #fff;
            box-shadow: var(--shadow-blue);
            flex-shrink: 0;
        }

        .navbar-icon i {
            font-size: 1.35rem;
        }

        .navbar-title {
            font-size: 1.25rem;
            font-weight: 900;
            color: var(--text-primary);
            letter-spacing: -0.04em;
            line-height: 1.1;
            margin: 0;
        }

        .navbar-sub {
            font-size: 0.82rem;
            color: var(--text-muted);
            font-weight: 700;
            margin: 0.25rem 0 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: min(56vw, 680px);
        }

        .btn-logout {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            min-height: 42px;
            background: rgba(201, 122, 98, 0.10);
            border: 1px solid rgba(201, 122, 98, 0.18);
            color: var(--coral-dark);
            padding: 0.62rem 1.05rem;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 900;
            cursor: pointer;
            transition: background 0.18s, transform 0.18s, border-color 0.18s;
            text-decoration: none;
            box-shadow: var(--shadow-sm);
        }

        .btn-logout:hover {
            background: rgba(201, 122, 98, 0.16);
            border-color: rgba(201, 122, 98, 0.26);
            transform: translateY(-1px);
        }

        /* ── Content ── */
        .content {
            position: relative;
            z-index: 1;
            padding: 2rem;
            max-width: 1180px;
            margin: 0 auto;
            width: 100%;
        }

        .dashboard-hero {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1.5rem;
            flex-wrap: wrap;
            padding: 1.65rem 1.8rem;
            border-radius: 30px;
            background: linear-gradient(135deg, rgba(247, 243, 232, 0.96), rgba(250, 251, 250, 0.95));
            border: 1px solid rgba(255, 255, 255, 0.80);
            box-shadow: var(--shadow-card);
            overflow: hidden;
            position: relative;
            margin-bottom: 1.5rem;
        }

        .dashboard-hero::before {
            content: '';
            position: absolute;
            inset: 0 auto 0 0;
            width: 6px;
            background: linear-gradient(180deg, rgba(215, 189, 98, 0.85), rgba(75, 149, 143, 0.85), rgba(76, 99, 244, 0.72));
        }

        .dashboard-hero::after {
            content: 'STUDENT';
            position: absolute;
            right: 1.6rem;
            bottom: 0.9rem;
            color: rgba(53, 111, 107, 0.05);
            font-weight: 900;
            font-size: clamp(2rem, 6vw, 4.4rem);
            line-height: 1;
            letter-spacing: 0.08em;
            pointer-events: none;
        }

        .hero-text,
        .hero-action {
            position: relative;
            z-index: 1;
        }

        .hero-kicker {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            min-height: 30px;
            padding: 0.28rem 0.75rem;
            border-radius: 999px;
            background: rgba(75, 149, 143, 0.10);
            color: var(--primary-dark);
            border: 1px solid rgba(75, 149, 143, 0.12);
            font-size: 0.72rem;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 0.85rem;
        }

        .hero-title {
            font-size: clamp(1.9rem, 3vw, 2.65rem);
            font-weight: 900;
            color: var(--text-primary);
            letter-spacing: -0.055em;
            margin: 0 0 0.45rem;
            line-height: 1.05;
            text-shadow: 2px 2px 0 rgba(215, 189, 98, 0.20);
        }

        .hero-subtitle {
            color: #4C8A85;
            font-size: 0.95rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            margin: 0;
        }

        /* ── New Borrowing Button ── */
        .btn-new {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            min-height: 46px;
            background: linear-gradient(135deg, #5E78FF, var(--blue));
            color: #fff;
            font-weight: 900;
            font-size: 0.9rem;
            padding: 0.75rem 1.4rem;
            border-radius: 999px;
            text-decoration: none;
            transition: transform 0.18s, box-shadow 0.18s, filter 0.18s;
            box-shadow: var(--shadow-blue);
            white-space: nowrap;
        }

        .btn-new:hover {
            transform: translateY(-2px);
            filter: saturate(0.98);
            box-shadow: 0 18px 34px rgba(76, 99, 244, 0.24);
        }

        .btn-new:active {
            transform: scale(0.98);
        }

        /* Alert errors */
        .alert-error {
            background: rgba(201, 122, 98, 0.10);
            border: 1px solid rgba(201, 122, 98, 0.22);
            border-left: 6px solid var(--coral);
            border-radius: 18px;
            padding: 0.85rem 1rem;
            color: var(--coral-dark);
            font-size: 0.85rem;
            font-weight: 800;
            margin-bottom: 1.25rem;
            box-shadow: var(--shadow-sm);
        }

        /* ── Section Heading ── */
        .section-block {
            margin-top: 1.75rem;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            font-size: 1.08rem;
            font-weight: 900;
            color: var(--text-primary);
            margin: 0 0 1rem;
            letter-spacing: -0.02em;
        }

        .section-title i {
            width: 34px;
            height: 34px;
            border-radius: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: var(--primary-dark);
            background: rgba(75, 149, 143, 0.10);
            border: 1px solid rgba(75, 149, 143, 0.12);
            box-shadow: var(--shadow-sm);
        }

        /* ── Glass Card ── */
        .glass-card {
            background: linear-gradient(180deg, rgba(250,251,250,0.98), rgba(244, 241, 234, 0.88));
            border: 1px solid rgba(255, 255, 255, 0.82);
            border-left: 7px solid var(--primary-teal);
            border-radius: 26px;
            backdrop-filter: blur(10px);
            margin-bottom: 1rem;
            box-shadow: var(--shadow-soft);
            transition: transform 0.22s cubic-bezier(.2,.8,.2,1), box-shadow 0.22s, border-color 0.22s;
            overflow: hidden;
        }

        .glass-card:hover {
            transform: translateY(-2px);
            border-color: rgba(75, 149, 143, 0.12);
            box-shadow: 0 16px 32px rgba(31, 42, 41, 0.07);
        }

        /* ── Borrowing Item Card ── */
        .borrow-card {
            padding: 1.5rem;
            position: relative;
        }

        .borrow-card::before {
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

        .borrow-card-inner {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1.25rem;
            flex-wrap: wrap;
        }

        .borrow-left {
            min-width: 0;
            flex: 1;
        }

        .borrow-room {
            font-size: 1.15rem;
            font-weight: 900;
            color: var(--text-primary);
            letter-spacing: -0.025em;
            margin: 0 0 0.35rem;
        }

        .borrow-meta {
            display: inline-flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.4rem;
            font-size: 0.78rem;
            color: var(--primary-dark);
            font-weight: 800;
            margin: 0 0 0.75rem;
            padding: 0.18rem 0.55rem;
            border-radius: 999px;
            background: rgba(75, 149, 143, 0.10);
        }

        .borrow-purpose {
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 700;
            margin: 0 0 0.9rem;
            line-height: 1.6;
        }

        .borrow-items-label {
            font-size: 0.74rem;
            font-weight: 900;
            color: var(--text-muted);
            margin-bottom: 0.35rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .borrow-items-list {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 700;
            line-height: 1.65;
        }

        .borrow-items-list div {
            margin-bottom: 0.1rem;
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
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 28px;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            font-size: 0.68rem;
            font-weight: 900;
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

        .badge-waiting {
            background: linear-gradient(135deg, rgba(127,168,191,0.16), rgba(127,168,191,0.08));
            color: #54768B;
            border-color: rgba(127,168,191,0.18);
        }

        .badge-completed {
            background: linear-gradient(135deg, rgba(31,42,41,0.06), rgba(31,42,41,0.03));
            color: var(--text-soft);
            border-color: rgba(31,42,41,0.08);
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
            min-height: 32px;
            padding: 0.42rem 0.85rem;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 900;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid transparent;
            transition: background 0.18s, color 0.18s, transform 0.15s, border-color 0.18s;
            box-shadow: var(--shadow-sm);
            font-family: inherit;
        }

        .btn-sm:hover {
            transform: translateY(-1px);
        }

        .btn-sm:active {
            transform: scale(0.98);
        }

        .btn-edit {
            background: rgba(76, 99, 244, 0.10);
            color: #4050D3;
            border-color: rgba(76, 99, 244, 0.16);
        }

        .btn-edit:hover {
            background: rgba(76, 99, 244, 0.16);
        }

        .btn-delete,
        .btn-cancel {
            background: rgba(201,122,98,0.10);
            color: var(--coral-dark);
            border-color: rgba(201,122,98,0.20);
        }

        .btn-delete:hover,
        .btn-cancel:hover {
            background: rgba(201,122,98,0.16);
            border-color: rgba(201,122,98,0.28);
        }

        .btn-return {
            background: rgba(75,149,143,0.12);
            color: var(--primary-dark);
            border-color: rgba(75,149,143,0.22);
        }

        .btn-return:hover {
            background: rgba(75,149,143,0.18);
            border-color: rgba(75,149,143,0.30);
        }

        /* Cancel reason box */
        .cancel-reason {
            margin-top: 0.5rem;
            background: rgba(201,122,98,0.10);
            border: 1px solid rgba(201,122,98,0.20);
            border-radius: 16px;
            padding: 0.75rem 0.85rem;
            max-width: 240px;
            text-align: left;
            box-shadow: var(--shadow-sm);
        }

        .cancel-reason-title {
            font-size: 0.75rem;
            font-weight: 900;
            color: var(--coral-dark);
            margin: 0 0 0.2rem;
        }

        .cancel-reason-text {
            font-size: 0.78rem;
            color: var(--coral);
            margin: 0;
            line-height: 1.5;
            font-weight: 700;
        }

        /* ── Empty State ── */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-soft);
            font-size: 0.9rem;
            font-weight: 800;
            background: rgba(247, 243, 232, 0.46);
        }

        /* ── Table ── */
        .table-wrap {
            overflow-x: auto;
        }

        .glass-table {
            width: 100%;
            min-width: 820px;
            border-collapse: collapse;
        }

        .glass-table thead tr {
            background: rgba(231, 239, 237, 0.88);
        }

        .glass-table th {
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

        .glass-table td {
            padding: 1rem 1.25rem;
            font-size: 0.85rem;
            color: var(--text-muted);
            border-top: 1px solid rgba(75, 149, 143, 0.08);
            vertical-align: top;
            font-weight: 700;
            line-height: 1.6;
        }

        .glass-table td:first-child,
        .glass-table td:nth-child(2) {
            color: var(--text-primary);
            font-weight: 900;
        }

        .glass-table tbody tr:hover {
            background: rgba(247, 243, 232, 0.58);
        }

        @keyframes dashFadeUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dashboard-hero,
        .section-block,
        .glass-card {
            animation: dashFadeUp 0.35s ease both;
        }

        .section-block:nth-of-type(1) {
            animation-delay: 0.08s;
        }

        .section-block:nth-of-type(2) {
            animation-delay: 0.12s;
        }

        @media (max-width: 780px) {
            .navbar {
                align-items: flex-start;
                padding: 1rem;
            }

            .navbar-sub {
                max-width: 58vw;
            }

            .content {
                padding: 1.25rem 1rem;
            }

            .dashboard-hero {
                padding: 1.35rem 1.25rem;
                border-radius: 24px;
            }

            .hero-action,
            .btn-new {
                width: 100%;
            }

            .borrow-card-inner {
                flex-direction: column;
            }

            .borrow-right {
                align-items: flex-start;
                width: 100%;
            }

            .action-row {
                justify-content: flex-start;
            }

            .action-row[style] {
                align-items: flex-start !important;
            }

            .cancel-reason {
                max-width: 100%;
            }
        }

        @media (max-width: 520px) {
            .navbar {
                flex-direction: column;
                gap: 0.85rem;
            }

            .navbar-brand {
                width: 100%;
            }

            .navbar-sub {
                max-width: 100%;
                white-space: normal;
            }

            .btn-logout {
                width: 100%;
            }

            .hero-title {
                font-size: 1.8rem;
            }

            .borrow-card {
                padding: 1.25rem;
            }

            .section-title {
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-brand">
            <span class="navbar-icon">
                <i class="ti ti-school"></i>
            </span>
            <div>
                <p class="navbar-title">Student Dashboard</p>
                <p class="navbar-sub">
                    Selamat datang, {{ auth()->user()->name }} ({{ auth()->user()->nim }})
                </p>
            </div>
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

        <div class="dashboard-hero">
            <div class="hero-text">
                <div class="hero-kicker">
                    <i class="ti ti-sparkles"></i>
                    Student Workspace
                </div>
                <h1 class="hero-title">Kelola Peminjaman Laboratorium</h1>
                <p class="hero-subtitle">Pantau peminjaman aktif, ajukan peminjaman baru, dan lihat riwayat peminjamanmu.</p>
            </div>

            <div class="hero-action">
                <!-- New Borrowing -->
                <a href="{{ route('student.borrowings.create') }}" class="btn-new">
                    <i class="ti ti-plus"></i>
                    Peminjaman Baru
                </a>
            </div>
        </div>

        <!-- ── Active Borrowings ── -->
        <div class="section-block">
            <h2 class="section-title">
                <i class="ti ti-package"></i>
                Peminjaman Aktif
            </h2>

            @forelse($activeBorrowings as $borrowing)
                <div class="glass-card borrow-card">
                    <div class="borrow-card-inner">

                        <!-- Left -->
                        <div class="borrow-left">
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
        <div class="section-block">
            <h2 class="section-title">
                <i class="ti ti-clock"></i>
                History Peminjaman
            </h2>

            <div class="glass-card">
                <div class="table-wrap">
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
                                            <span style="color:var(--text-soft); font-weight:800;">{{ $history->status }}</span>
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

    </div>

</body>
</html>
