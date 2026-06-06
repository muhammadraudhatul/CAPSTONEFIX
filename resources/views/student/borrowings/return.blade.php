<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Items</title>

    @vite('resources/css/app.css')

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        *, *::before, *::after { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            background: radial-gradient(ellipse at 20% 50%, #2a3f8f 0%, #1a2a6e 40%, #0d1540 100%);
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 2.5rem 1rem 3rem;
        }

        /* ── Page content wrapper ── */
        .page-content {
            width: 100%;
            max-width: 700px;
            display: flex;
            flex-direction: column;
        }

        /* ── Back link ── */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            color: rgba(255,255,255,0.55);
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            margin-bottom: 1.5rem;
            transition: color 0.2s;
        }

        .back-link:hover { color: rgba(255,255,255,0.9); }

        /* ── Header ── */
        .page-title {
            font-size: 2rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.02em;
            margin: 0 0 0.35rem;
        }

        .page-sub {
            color: rgba(255,255,255,0.4);
            font-size: 0.9rem;
            margin: 0 0 1.5rem;
        }

        /* ── Info card ── */
        .info-card {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 1rem;
            padding: 1.5rem 1.75rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.25rem;
        }

        .info-label {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.35);
            margin: 0 0 0.35rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .info-value {
            font-size: 1.15rem;
            font-weight: 700;
            color: #fff;
            margin: 0;
        }

        /* ── Form card ── */
        .form-card {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 1rem;
            overflow: hidden;
        }

        /* ── Table ── */
        .ret-table { width: 100%; border-collapse: collapse; }

        .ret-table thead tr {
            background: rgba(255,255,255,0.04);
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }

        .ret-table thead th {
            padding: 0.85rem 1.25rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            color: rgba(255,255,255,0.35);
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .ret-table tbody tr {
            border-top: 1px solid rgba(255,255,255,0.05);
            transition: background 0.15s;
        }

        .ret-table tbody tr:hover { background: rgba(255,255,255,0.025); }

        .ret-table td {
            padding: 1rem 1.25rem;
            font-size: 0.9rem;
            color: rgba(255,255,255,0.55);
            vertical-align: middle;
        }

        .td-item-name { color: #fff; font-weight: 600; }

        /* number input */
        .qty-input {
            width: 100px;
            background: rgba(255,255,255,0.07) !important;
            border: 1px solid rgba(255,255,255,0.1) !important;
            border-radius: 0.6rem !important;
            padding: 0.55rem 0.85rem !important;
            color: #fff !important;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .qty-input:focus {
            border-color: rgba(96,165,250,0.5) !important;
            box-shadow: 0 0 0 3px rgba(96,165,250,0.1) !important;
        }

        .qty-hint {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.25);
            margin-top: 0.3rem;
        }

        /* type badges */
        .badge {
            display: inline-block;
            padding: 0.28rem 0.7rem;
            border-radius: 0.5rem;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .badge-tool     { background: rgba(96,165,250,0.15); color: #93c5fd; }
        .badge-material { background: rgba(251,146,60,0.15); color: #fdba74; }

        /* ── Actions ── */
        .form-actions {
            display: flex;
            gap: 0.75rem;
            padding: 1.25rem 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.06);
        }

        .btn-cancel {
            flex: 1;
            padding: 0.9rem;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 0.75rem;
            color: rgba(255,255,255,0.55);
            font-size: 0.95rem;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.18s;
            font-family: 'Inter', sans-serif;
        }

        .btn-cancel:hover { background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.8); }

        .btn-submit {
            flex: 2;
            padding: 0.9rem;
            background: linear-gradient(90deg, #3b82f6, #06b6d4);
            border: none;
            border-radius: 0.75rem;
            color: #fff;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            box-shadow: 0 6px 20px rgba(59,130,246,0.3);
            transition: opacity 0.18s, transform 0.15s;
        }

        .btn-submit:hover { opacity: 0.88; transform: translateY(-1px); }
    </style>
</head>

<body>

<div class="page-content">

    <!-- BACK -->
    <a href="{{ route('student.dashboard') }}" class="back-link">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali
    </a>

    <!-- HEADER -->
    <h1 class="page-title">Pengembalian Peminjaman</h1>
    <p class="page-sub">Isi jumlah item yang dikembalikan</p>

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
                    <td>{{ $borrowedItem->qty }}</td>

                    <!-- RETURN -->
                    <td>
                        <input
                            type="number"
                            min="0"
                            max="{{ $borrowedItem->qty }}"
                            value="{{ $borrowedItem->qty }}"
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

        <!-- ACTIONS -->
        <div class="form-actions">
            <a href="{{ route('student.dashboard') }}" class="btn-cancel">Cancel</a>
            <button type="submit" class="btn-submit">Kirim Pengembalian</button>
        </div>

    </form>

</div>

</body>
</html>