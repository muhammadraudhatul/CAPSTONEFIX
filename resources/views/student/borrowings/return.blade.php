<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Items</title>

    @vite('resources/css/app.css')

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

        *, *::before, *::after {
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
            --blue: #4C63F4;
            --cream: #F7F3E8;
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
            --focus-blue: 0 0 0 4px rgba(76, 99, 244, 0.10);
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
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

        .page-shell {
            position: relative;
            z-index: 1;
            width: 100%;
            min-height: 100vh;
            padding: 2rem 1rem;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .page-content {
            width: 100%;
            max-width: 1040px;
            display: flex;
            flex-direction: column;
            animation: returnFadeUp 0.38s ease both;
        }

        /* ── Back link ── */
        .back-link {
            width: fit-content;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-muted);
            font-size: 0.88rem;
            font-weight: 900;
            text-decoration: none;
            margin-bottom: 1.25rem;
            padding: 0.55rem 0.85rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.68);
            border: 1px solid rgba(99, 115, 146, 0.12);
            box-shadow: 0 8px 22px rgba(28, 42, 78, 0.06);
            backdrop-filter: blur(10px);
            transition: color 0.2s, background 0.2s, transform 0.2s, border-color 0.2s;
        }

        .back-link:hover {
            color: var(--blue);
            background: rgba(255, 255, 255, 0.92);
            border-color: rgba(76, 99, 244, 0.16);
            transform: translateY(-1px);
        }

        .back-link svg {
            width: 15px;
            height: 15px;
        }

        .return-shell {
            display: grid;
            grid-template-columns: 0.88fr 1.45fr;
            width: 100%;
            min-height: calc(100vh - 6.5rem);
            border-radius: 34px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.84);
            border: 1px solid rgba(255, 255, 255, 0.88);
            box-shadow: 0 26px 70px rgba(25, 35, 60, 0.08);
            backdrop-filter: blur(16px);
            position: relative;
        }

        .return-shell::before {
            content: '';
            position: absolute;
            inset: 0 auto 0 0;
            width: 7px;
            background: linear-gradient(180deg, rgba(215, 189, 98, 0.88), rgba(75, 149, 143, 0.86), rgba(76, 99, 244, 0.78));
            pointer-events: none;
            z-index: 2;
        }

        .return-side {
            position: relative;
            padding: 2.6rem 2.35rem;
            background:
                radial-gradient(circle at 8% 10%, rgba(215, 189, 98, 0.15), transparent 26%),
                linear-gradient(145deg, rgba(247, 243, 232, 0.98), rgba(231, 239, 237, 0.88));
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        .return-side::before {
            content: '';
            position: absolute;
            width: 340px;
            height: 340px;
            right: -145px;
            top: -125px;
            border-radius: 0 0 0 100%;
            background: rgba(255, 255, 255, 0.52);
            pointer-events: none;
        }

        .return-side::after {
            content: 'RETURN';
            position: absolute;
            left: 2.25rem;
            bottom: 1.7rem;
            color: rgba(53, 111, 107, 0.06);
            font-size: clamp(3.1rem, 6vw, 4.65rem);
            line-height: 1;
            font-weight: 900;
            letter-spacing: 0.04em;
            pointer-events: none;
        }

        .side-content,
        .side-footer {
            position: relative;
            z-index: 1;
        }

        .system-badge {
            width: fit-content;
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.42rem 0.95rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.74);
            border: 1px solid rgba(53, 111, 107, 0.12);
            color: #4C5870;
            font-size: 0.72rem;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 2rem;
            box-shadow: 0 8px 20px rgba(31, 42, 41, 0.05);
        }

        .system-dot {
            width: 9px;
            height: 9px;
            border-radius: 999px;
            background: #52C96C;
            box-shadow: 0 0 0 4px rgba(82, 201, 108, 0.10);
        }

        .side-title {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 900;
            letter-spacing: -0.065em;
            line-height: 1.05;
            color: var(--text-primary);
            margin: 0 0 1.05rem;
        }

        .side-title span {
            display: inline-block;
            color: var(--blue);
        }

        .side-copy {
            color: var(--text-muted);
            font-size: 0.96rem;
            font-weight: 700;
            line-height: 1.65;
            margin: 0;
        }

        .side-list {
            display: grid;
            gap: 0.9rem;
            list-style: none;
            margin: 2.25rem 0 0;
            padding: 0;
        }

        .side-list li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #3C475E;
            font-size: 0.9rem;
            font-weight: 800;
        }

        .side-list-icon {
            width: 30px;
            height: 30px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--blue);
            background: rgba(76, 99, 244, 0.06);
            border: 1px solid rgba(76, 99, 244, 0.10);
            flex-shrink: 0;
        }

        .side-footer {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            color: var(--text-soft);
            font-size: 0.8rem;
            font-weight: 800;
        }

        .side-footer-mark {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #43340E;
            background: linear-gradient(135deg, #E6D58C, #D7BD62);
            border: 1px solid rgba(255, 255, 255, 0.62);
            flex-shrink: 0;
        }

        .return-main {
            min-width: 0;
            background: rgba(255, 255, 255, 0.96);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .return-main::before {
            content: '';
            position: absolute;
            width: 280px;
            height: 280px;
            right: -82px;
            top: -98px;
            border-radius: 0 0 0 100%;
            background: linear-gradient(135deg, rgba(248,250,255,0.95), rgba(236,242,251,0.64));
            pointer-events: none;
        }

        .main-inner {
            position: relative;
            z-index: 1;
            padding: 2.45rem 2.65rem 1.5rem;
            overflow-y: auto;
        }

        /* ── Header ── */
        .title-row {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.55rem;
        }

        .title-icon {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            background: linear-gradient(135deg, #5E78FF, var(--blue));
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            box-shadow: var(--shadow-blue);
            flex-shrink: 0;
        }

        .title-icon svg {
            width: 27px;
            height: 27px;
        }

        .page-title {
            font-size: 1.85rem;
            font-weight: 900;
            color: var(--text-primary);
            letter-spacing: -0.055em;
            line-height: 1.1;
            margin: 0 0 0.35rem;
        }

        .page-sub {
            color: var(--text-muted);
            font-size: 0.92rem;
            font-weight: 700;
            line-height: 1.55;
            margin: 0;
        }

        .attention-card {
            display: flex;
            align-items: flex-start;
            gap: 0.8rem;
            margin-bottom: 1.25rem;
            padding: 1rem 1.1rem;
            border-radius: 20px;
            background: linear-gradient(135deg, rgba(215,189,98,0.15), rgba(247,243,232,0.72));
            border: 1px solid rgba(215, 189, 98, 0.24);
            box-shadow: var(--shadow-sm);
        }

        .attention-icon {
            width: 34px;
            height: 34px;
            border-radius: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #7F6B2A;
            background: rgba(215, 189, 98, 0.18);
            flex-shrink: 0;
            font-weight: 900;
        }

        .attention-title {
            margin: 0 0 0.2rem;
            color: #7F6B2A;
            font-size: 0.83rem;
            font-weight: 900;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .attention-text {
            margin: 0;
            color: var(--text-muted);
            font-size: 0.84rem;
            line-height: 1.55;
            font-weight: 700;
        }

        /* ── Info card ── */
        .info-card {
            background: linear-gradient(180deg, rgba(250,251,250,0.98), rgba(244, 241, 234, 0.80));
            border: 1px solid rgba(255, 255, 255, 0.82);
            border-left: 7px solid var(--primary-teal);
            border-radius: 24px;
            padding: 1.35rem 1.45rem;
            display: grid;
            grid-template-columns: 1fr 1.25fr;
            gap: 1rem;
            margin-bottom: 1.25rem;
            box-shadow: var(--shadow-soft);
        }

        .info-label {
            font-size: 0.72rem;
            color: var(--text-muted);
            margin: 0 0 0.35rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 900;
        }

        .info-value {
            font-size: 1rem;
            font-weight: 900;
            color: var(--text-primary);
            margin: 0;
            line-height: 1.4;
        }

        /* ── Form card ── */
        .form-card {
            background: linear-gradient(180deg, rgba(250,251,250,0.98), rgba(244, 241, 234, 0.88));
            border: 1px solid rgba(255, 255, 255, 0.82);
            border-left: 7px solid var(--primary-teal);
            border-radius: 26px;
            overflow: hidden;
            box-shadow: var(--shadow-soft);
        }

        .table-wrap {
            overflow-x: auto;
        }

        /* ── Table ── */
        .ret-table {
            width: 100%;
            min-width: 660px;
            border-collapse: collapse;
        }

        .ret-table thead tr {
            background: rgba(231, 239, 237, 0.88);
        }

        .ret-table thead th {
            padding: 0.92rem 1.15rem;
            text-align: left;
            font-size: 0.72rem;
            font-weight: 900;
            color: #4A8C86;
            text-transform: uppercase;
            letter-spacing: 0.10em;
            border-bottom: 2px solid rgba(75, 149, 143, 0.10);
            white-space: nowrap;
        }

        .ret-table tbody tr {
            border-top: 1px solid rgba(75, 149, 143, 0.08);
            transition: background 0.16s ease;
        }

        .ret-table tbody tr:hover {
            background: rgba(247, 243, 232, 0.58);
        }

        .ret-table td {
            padding: 1rem 1.15rem;
            font-size: 0.88rem;
            color: var(--text-muted);
            font-weight: 700;
            vertical-align: middle;
            line-height: 1.5;
        }

        .td-item-name {
            color: var(--text-primary);
            font-weight: 900;
            letter-spacing: -0.01em;
        }

        .borrowed-qty {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 42px;
            min-height: 30px;
            padding: 0.18rem 0.65rem;
            border-radius: 999px;
            background: rgba(75, 149, 143, 0.10);
            color: var(--primary-dark);
            border: 1px solid rgba(75, 149, 143, 0.12);
            font-weight: 900;
        }

        /* number input */
        .qty-input {
            width: 112px;
            min-height: 44px;
            background: rgba(247, 243, 232, 0.72) !important;
            border: 1px solid rgba(99, 115, 146, 0.14) !important;
            border-radius: 16px !important;
            padding: 0.65rem 0.85rem !important;
            color: var(--text-primary) !important;
            font-size: 0.92rem;
            font-weight: 900;
            font-family: 'Inter', sans-serif;
            outline: none;
            box-shadow: 0 4px 14px rgba(53, 111, 107, 0.04);
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }

        .qty-input:focus {
            border-color: rgba(76, 99, 244, 0.34) !important;
            background: #FFFFFF !important;
            box-shadow: var(--focus-blue) !important;
        }

        .qty-hint {
            font-size: 0.75rem;
            color: var(--text-soft);
            font-weight: 800;
            margin: 0.35rem 0 0;
        }

        /* type badges */
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

        .badge-tool {
            background: linear-gradient(135deg, rgba(76,99,244,0.13), rgba(76,99,244,0.06));
            color: #4050D3;
            border-color: rgba(76,99,244,0.16);
        }

        .badge-material {
            background: linear-gradient(135deg, rgba(215,189,98,0.20), rgba(215,189,98,0.10));
            color: #7F6B2A;
            border-color: rgba(215,189,98,0.22);
        }

        /* ── Actions ── */
        .form-actions {
            display: flex;
            gap: 0.75rem;
            padding: 1rem 1.45rem 1.35rem;
            border-top: 1px solid rgba(75, 149, 143, 0.10);
            background: rgba(250,251,250,0.82);
        }

        .btn-cancel {
            flex: 1;
            min-height: 52px;
            padding: 0.9rem 1.2rem;
            background: rgba(247, 243, 232, 0.78);
            border: 1px solid rgba(75, 149, 143, 0.14);
            border-radius: 16px;
            color: var(--text-muted);
            font-size: 0.95rem;
            font-weight: 900;
            text-align: center;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.18s, color 0.18s, border-color 0.18s, transform 0.18s;
            font-family: 'Inter', sans-serif;
        }

        .btn-cancel:hover {
            background: rgba(231, 239, 237, 0.88);
            color: var(--text-primary);
            border-color: rgba(75, 149, 143, 0.22);
            transform: translateY(-1px);
        }

        .btn-submit {
            flex: 2;
            min-height: 52px;
            padding: 0.9rem 1.2rem;
            background: linear-gradient(135deg, #5E78FF, var(--blue));
            border: none;
            border-radius: 16px;
            color: #fff;
            font-size: 0.95rem;
            font-weight: 900;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            box-shadow: var(--shadow-blue);
            transition: transform 0.18s, box-shadow 0.18s, filter 0.18s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            filter: saturate(0.98);
            box-shadow: 0 18px 34px rgba(76, 99, 244, 0.24);
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        @keyframes returnFadeUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 920px) {
            .return-shell {
                grid-template-columns: 1fr;
                min-height: auto;
                border-radius: 30px;
            }

            .return-side {
                min-height: auto;
                padding: 2.1rem 2rem;
            }

            .return-side::after,
            .side-list,
            .side-footer {
                display: none;
            }

            .main-inner {
                padding: 2.15rem 2rem 1.5rem;
            }
        }

        @media (max-width: 680px) {
            .page-shell {
                padding: 1rem 0;
            }

            .page-content {
                max-width: 100%;
            }

            .back-link {
                margin-left: 1rem;
            }

            .return-shell {
                border-radius: 0;
                min-height: calc(100vh - 4rem);
            }

            .return-side {
                padding: 1.6rem 1.25rem;
            }

            .main-inner {
                padding: 1.45rem 1.25rem 1rem;
            }

            .title-row {
                gap: 0.85rem;
            }

            .title-icon {
                width: 50px;
                height: 50px;
                border-radius: 16px;
            }

            .page-title {
                font-size: 1.55rem;
            }

            .info-card {
                grid-template-columns: 1fr;
                padding: 1.15rem 1.1rem;
            }

            .form-actions {
                flex-direction: column;
                padding: 0.9rem 1.1rem 1.35rem;
            }

            .btn-cancel,
            .btn-submit {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<div class="page-shell">
    <div class="page-content">

        <!-- BACK -->
        <a href="{{ route('student.dashboard') }}" class="back-link">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>

        <div class="return-shell">

            <aside class="return-side">
                <div class="side-content">
                    <div class="system-badge">
                        <span class="system-dot"></span>
                        Return Confirmation
                    </div>

                    <h1 class="side-title">
                        Konfirmasi<br><span>Pengembalian</span>
                    </h1>
                    <p class="side-copy">
                        Isi jumlah item yang benar-benar dikembalikan. Nilai awal sengaja dibuat 0 agar setiap item diperiksa satu per satu.
                    </p>

                    <ul class="side-list">
                        <li>
                            <span class="side-list-icon">1</span>
                            Periksa setiap item yang dipinjam
                        </li>
                        <li>
                            <span class="side-list-icon">2</span>
                            Ubah angka 0 sesuai jumlah yang dikembalikan
                        </li>
                        <li>
                            <span class="side-list-icon">3</span>
                            Pastikan tidak melebihi jumlah maksimal
                        </li>
                    </ul>
                </div>

                <div class="side-footer">
                    <span class="side-footer-mark">✓</span>
                    Laboratory Management System
                </div>
            </aside>

            <main class="return-main">
                <div class="main-inner">

                    <!-- HEADER -->
                    <div class="title-row">
                        <span class="title-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 14 4 9l5-5"/>
                                <path d="M4 9h11a5 5 0 1 1 0 10H9"/>
                            </svg>
                        </span>
                        <div>
                            <h1 class="page-title">Pengembalian Peminjaman</h1>
                            <p class="page-sub">Isi jumlah item yang dikembalikan dengan teliti.</p>
                        </div>
                    </div>

                    <div class="attention-card">
                        <span class="attention-icon">!</span>
                        <div>
                            <p class="attention-title">Perhatikan angka pengembalian</p>
                            <p class="attention-text">
                                Semua input pengembalian dimulai dari <strong>0</strong>. Ubah jumlahnya sesuai item yang benar-benar dikembalikan sebelum menekan tombol kirim.
                            </p>
                        </div>
                    </div>

                    <!-- INFO -->
                    <div class="info-card">
                        <div>
                            <p class="info-label">Ruangan</p>
                            <h2 class="info-value">{{ $borrowing->room->name }}</h2>
                        </div>
                        <div>
                            <p class="info-label">Jadwal</p>
                            <h2 class="info-value">
                                {{ \Carbon\Carbon::parse($borrowing->borrow_date)->format('d M Y') }}
                                &bull;
                                {{ $borrowing->time_slot }}
                            </h2>
                        </div>
                    </div>

                    <!-- FORM -->
                    <form
                        method="POST"
                        action="{{ route('student.borrowings.return.submit', $borrowing) }}"
                        class="form-card"
                    >
                        @csrf
                        @method('PATCH')

                        <!-- TABLE -->
                        <div class="table-wrap">
                            <table class="ret-table">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Dipinjam</th>
                                        <th>Dikembalikan</th>
                                        <th>Tipe</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($borrowing->items as $borrowedItem)
                                    <tr>

                                        <!-- ITEM -->
                                        <td>
                                            <div class="td-item-name">{{ $borrowedItem->item->name }}</div>
                                        </td>

                                        <!-- BORROWED -->
                                        <td>
                                            <span class="borrowed-qty">{{ $borrowedItem->qty }}</span>
                                        </td>

                                        <!-- RETURN -->
                                        <td>
                                            <input
                                                type="number"
                                                min="0"
                                                max="{{ $borrowedItem->qty }}"
                                                value="0"
                                                name="returned_qty[{{ $borrowedItem->id }}]"
                                                class="qty-input"
                                            >
                                            <p class="qty-hint">Maksimal: {{ $borrowedItem->qty }}</p>
                                        </td>

                                        <!-- TYPE -->
                                        <td>
                                            @if($borrowedItem->item->type == 'tool')
                                                <span class="badge badge-tool">TOOL</span>
                                            @else
                                                <span class="badge badge-material">MATERIAL</span>
                                            @endif
                                        </td>

                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- ACTIONS -->
                        <div class="form-actions">
                            <a href="{{ route('student.dashboard') }}" class="btn-cancel">Cancel</a>
                            <button type="submit" class="btn-submit">Kirim Pengembalian</button>
                        </div>

                    </form>

                </div>
            </main>

        </div>

    </div>
</div>

</body>
</html>
