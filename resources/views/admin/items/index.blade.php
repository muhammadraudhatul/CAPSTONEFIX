@extends('admin.layouts.app')

@section('content')

<style>
    /* ── Flip7 muted theme, scoped supaya tidak mengubah layout induk ── */
    .items-flip7-wrapper {
        --primary-teal:  #4B958F;
        --primary-light: #75B6B0;
        --primary-dark:  #356F6B;
        --primary-bg:    #E7EFED;
        --accent-gold:   #D7BD62;
        --accent-light:  #E9DCA7;
        --accent-dark:   #B69A3E;
        --coral:         #C97A62;
        --coral-dark:    #A85F49;
        --cream:         #F7F3E8;
        --surface-base:  #EDF2F1;
        --surface-card:  #FAFBFA;
        --text-primary:  #1F2A29;
        --text-muted:    #657675;
        --text-soft:     #8A9997;
        --border-soft:   rgba(53, 111, 107, 0.13);
        --shadow-soft:   0 14px 34px rgba(31, 42, 41, 0.06);
        --shadow-card:   0 10px 28px rgba(53, 111, 107, 0.07);

        min-height: calc(100vh - 1px);
        padding: 2rem 1.75rem;
        border-radius: 28px;
        position: relative;
        overflow: hidden;
        color: var(--text-primary);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'PingFang SC', 'Microsoft YaHei', sans-serif;
        background:
            radial-gradient(circle at 8% 8%, rgba(215, 189, 98, 0.09), transparent 25%),
            radial-gradient(circle at 88% 12%, rgba(75, 149, 143, 0.08), transparent 28%),
            linear-gradient(135deg, #F3F6F5 0%, var(--surface-base) 52%, #EEF3F1 100%);
    }

    .items-flip7-wrapper *,
    .items-flip7-wrapper *::before,
    .items-flip7-wrapper *::after {
        box-sizing: border-box;
    }

    .items-flip7-wrapper::before,
    .items-flip7-wrapper::after {
        content: '';
        position: absolute;
        border-radius: 28px;
        border: 1px solid rgba(53, 111, 107, 0.05);
        background: rgba(247, 243, 232, 0.20);
        pointer-events: none;
        z-index: 0;
    }

    .items-flip7-wrapper::before {
        width: 150px;
        height: 210px;
        right: -74px;
        top: 110px;
        transform: rotate(-8deg);
    }

    .items-flip7-wrapper::after {
        width: 108px;
        height: 146px;
        left: -52px;
        bottom: 138px;
        transform: rotate(10deg);
    }

    .items-flip7-main {
        position: relative;
        z-index: 1;
    }

    .items-page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        flex-wrap: wrap;
        padding: 1.65rem 1.8rem;
        border-radius: 30px;
        background: linear-gradient(135deg, rgba(247, 243, 232, 0.96), rgba(250, 251, 250, 0.95));
        border: 1px solid rgba(255, 255, 255, 0.80);
        box-shadow: var(--shadow-card);
        overflow: hidden;
        position: relative;
    }

    .items-page-header::before {
        content: '';
        position: absolute;
        inset: 0 auto 0 0;
        width: 6px;
        background: linear-gradient(180deg, rgba(215, 189, 98, 0.85), rgba(75, 149, 143, 0.85), rgba(201, 122, 98, 0.78));
    }

    .items-page-header::after {
        content: 'INVENTORY';
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

    .items-title-block,
    .items-add-link {
        position: relative;
        z-index: 1;
    }

    .items-page-title {
        font-size: clamp(1.9rem, 3vw, 2.55rem);
        font-weight: 900;
        color: var(--text-primary);
        letter-spacing: -0.045em;
        margin: 0 0 0.45rem;
        line-height: 1.05;
        text-shadow: 2px 2px 0 rgba(215, 189, 98, 0.20);
    }

    .items-page-subtitle {
        margin: 0;
        color: #4C8A85;
        font-size: 0.95rem;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .items-add-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        min-height: 46px;
        padding: 0.75rem 1.35rem;
        border-radius: 999px;
        color: #43340E;
        background: linear-gradient(135deg, #E6D58C, #D7BD62);
        border: 1px solid rgba(255, 255, 255, 0.75);
        font-size: 0.9rem;
        font-weight: 900;
        text-decoration: none;
        white-space: nowrap;
        box-shadow: 0 8px 20px rgba(31, 42, 41, 0.05);
        transition: transform 0.22s cubic-bezier(.2,.8,.2,1), filter 0.22s, box-shadow 0.22s;
    }

    .items-add-link:hover {
        transform: translateY(-2px);
        filter: saturate(0.96);
        box-shadow: 0 10px 22px rgba(53, 111, 107, 0.08);
    }

    .items-add-link svg {
        width: 16px;
        height: 16px;
    }

    .items-search-form {
        margin-top: 1.5rem;
    }

    .items-search-box {
        position: relative;
        max-width: 34rem;
    }

    .items-search-box svg {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        width: 1rem;
        height: 1rem;
        color: var(--text-soft);
        pointer-events: none;
    }

    .items-search-input {
        width: 100%;
        padding: 0.82rem 1rem 0.82rem 2.75rem;
        border-radius: 999px;
        background: rgba(247, 243, 232, 0.88);
        border: 1px solid rgba(75, 149, 143, 0.16);
        color: var(--text-primary);
        font-size: 0.9rem;
        font-weight: 700;
        outline: none;
        box-shadow: 0 6px 18px rgba(53, 111, 107, 0.04);
        transition: border-color 0.18s, box-shadow 0.18s, background 0.18s;
    }

    .items-search-input::placeholder {
        color: var(--text-soft);
        font-weight: 600;
    }

    .items-search-input:focus {
        background: #FAFBFA;
        border-color: rgba(75, 149, 143, 0.38);
        box-shadow: 0 0 0 4px rgba(75, 149, 143, 0.10);
    }

    .items-section-card {
        margin-top: 2rem;
        border-radius: 26px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.82);
        border-left: 7px solid var(--primary-teal);
        background: linear-gradient(180deg, rgba(250,251,250,0.98), rgba(244, 241, 234, 0.88));
        box-shadow: var(--shadow-soft);
        transition: transform 0.24s cubic-bezier(.2,.8,.2,1), box-shadow 0.24s, border-color 0.24s;
    }

    .items-section-card:hover {
        transform: translateY(-2px);
        border-color: rgba(75, 149, 143, 0.12);
        box-shadow: 0 16px 32px rgba(31, 42, 41, 0.07);
    }

    .items-section-card.material-section {
        border-left-color: rgba(215, 189, 98, 0.95);
    }

    .items-section-header {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        padding: 1.15rem 1.45rem;
        background: linear-gradient(135deg, rgba(75, 149, 143, 0.14), rgba(247, 243, 232, 0.78));
        border-bottom: 1px dashed rgba(75, 149, 143, 0.18);
    }

    .items-section-card.material-section .items-section-header {
        background: linear-gradient(135deg, rgba(215, 189, 98, 0.16), rgba(247, 243, 232, 0.80));
    }

    .items-section-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 14px;
        background: rgba(250, 251, 250, 0.78);
        color: var(--primary-dark);
        border: 1px solid rgba(75, 149, 143, 0.13);
        box-shadow: 0 4px 14px rgba(53, 111, 107, 0.05);
        flex-shrink: 0;
    }

    .items-section-card.material-section .items-section-icon {
        color: #7F6B2A;
        border-color: rgba(215, 189, 98, 0.22);
    }

    .items-section-icon svg {
        width: 20px;
        height: 20px;
    }

    .items-section-title {
        margin: 0;
        color: var(--text-primary);
        font-size: 1.2rem;
        line-height: 1.2;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .items-table-wrap {
        overflow-x: auto;
    }

    .items-table {
        width: 100%;
        min-width: 820px;
        border-collapse: collapse;
        background: transparent;
    }

    .items-table thead tr {
        background: rgba(231, 239, 237, 0.88);
    }

    .items-table th {
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

    .items-table th.col-number { width: 3.5rem; }
    .items-table th.col-unit { width: 6rem; }
    .items-table th.col-stock { width: 7rem; }
    .items-table th.col-action { width: 9rem; }

    .items-table tbody {
        background: transparent;
    }

    .items-table tbody tr {
        vertical-align: top;
        border-bottom: 1px solid rgba(75, 149, 143, 0.08);
        transition: background 0.18s ease;
    }

    .items-table tbody tr:last-child {
        border-bottom: none;
    }

    .items-table tbody tr:hover {
        background: rgba(247, 243, 232, 0.58);
    }

    .items-table td {
        padding: 1rem 1.25rem;
        color: var(--text-muted);
        font-size: 0.9rem;
        vertical-align: top;
    }

    .items-number {
        color: var(--text-soft);
        font-size: 0.86rem;
        font-weight: 800;
    }

    .item-name {
        color: var(--text-primary);
        font-size: 0.92rem;
        font-weight: 900;
    }

    .stock-badge-wrap {
        margin-top: 0.45rem;
    }

    .stock-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        min-height: 26px;
        padding: 0.18rem 0.65rem;
        border-radius: 999px;
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: 0.06em;
        border: 1px solid rgba(255, 255, 255, 0.60);
        box-shadow: 0 2px 8px rgba(31, 42, 41, 0.04);
    }

    .stock-badge svg {
        width: 12px;
        height: 12px;
        flex-shrink: 0;
    }

    .stock-badge.low {
        background: linear-gradient(135deg, rgba(201,122,98,0.16), rgba(201,122,98,0.08));
        color: var(--coral-dark);
        border-color: rgba(201, 122, 98, 0.18);
    }

    .stock-badge.safe {
        background: linear-gradient(135deg, rgba(75,149,143,0.16), rgba(75,149,143,0.08));
        color: var(--primary-dark);
        border-color: rgba(75, 149, 143, 0.18);
    }

    .location-line {
        color: var(--text-muted);
        font-size: 0.88rem;
        font-weight: 700;
    }

    .location-line + .location-line {
        margin-top: 0.5rem;
    }

    .location-room {
        color: var(--primary-dark);
        font-weight: 800;
    }

    .location-separator {
        color: var(--text-soft);
        margin: 0 0.25rem;
    }

    .location-stock {
        margin-left: 0.25rem;
        color: var(--text-soft);
        font-size: 0.78rem;
        font-weight: 800;
    }

    .unit-cell {
        color: var(--text-muted);
        font-size: 0.9rem;
        font-weight: 800;
    }

    .stock-total {
        font-size: 1.05rem;
        font-weight: 900;
    }

    .stock-total.low {
        color: #A56E25;
    }

    .stock-total.safe {
        color: var(--primary-dark);
    }

    .action-stack {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .action-row {
        display: flex;
        gap: 0.4rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.3rem;
        min-height: 32px;
        padding: 0.42rem 0.72rem;
        border-radius: 999px;
        border: 1px solid rgba(75, 149, 143, 0.13);
        background: rgba(231, 239, 237, 0.78);
        color: var(--primary-dark);
        font-size: 0.76rem;
        font-weight: 900;
        line-height: 1;
        text-decoration: none;
        cursor: pointer;
        font-family: inherit;
        transition: background 0.16s, color 0.16s, border-color 0.16s, transform 0.16s;
    }

    .action-btn:hover {
        transform: translateY(-1px);
        background: rgba(75, 149, 143, 0.14);
        border-color: rgba(75, 149, 143, 0.24);
    }

    .action-btn svg {
        width: 14px;
        height: 14px;
    }

    .action-btn.delete {
        color: var(--coral-dark);
        border-color: rgba(201, 122, 98, 0.14);
        background: rgba(201, 122, 98, 0.09);
    }

    .action-btn.delete:hover {
        background: rgba(201, 122, 98, 0.15);
        border-color: rgba(201, 122, 98, 0.24);
    }

    .empty-cell {
        text-align: center;
        padding: 3.5rem 1rem !important;
        color: var(--text-soft) !important;
        font-weight: 800;
        background: rgba(247, 243, 232, 0.46);
    }

    .empty-cell svg {
        display: block;
        width: 42px;
        height: 42px;
        margin: 0 auto 0.75rem;
        color: rgba(75, 149, 143, 0.28);
    }

    @keyframes itemsFadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .items-page-header,
    .items-search-form,
    .items-section-card {
        animation: itemsFadeUp 0.35s ease both;
    }

    .items-search-form { animation-delay: 0.08s; }
    .items-section-card:nth-of-type(1) { animation-delay: 0.12s; }
    .items-section-card:nth-of-type(2) { animation-delay: 0.16s; }

    @media (max-width: 760px) {
        .items-flip7-wrapper {
            padding: 1.25rem 1rem;
            border-radius: 22px;
        }

        .items-page-header {
            padding: 1.35rem 1.25rem;
            border-radius: 24px;
        }

        .items-add-link {
            width: 100%;
        }
    }
</style>

<div class="items-flip7-wrapper">
    <div class="items-flip7-main">

        {{-- Header --}}
        <div class="items-page-header">

            <div class="items-title-block">
                <h1 class="items-page-title">
                    Inventory Alat dan Bahan
                </h1>
                <p class="items-page-subtitle">
                    Kelola alat dan bahan laboratorium
                </p>
            </div>

            <a href="{{ route('items.create') }}"
               class="items-add-link">
                <svg xmlns="http://www.w3.org/2000/svg" class="items-add-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Tambah Item
            </a>

        </div>

        {{-- Search --}}
        <form method="GET" class="items-search-form">
            <div class="items-search-box">
                <svg class="items-search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari berdasarkan nama atau lokasi..."
                    class="items-search-input"
                >
            </div>
        </form>

        {{-- ========== ALAT LABORATORIUM ========== --}}
        <div class="items-section-card tool-section">

            {{-- Section header --}}
            <div class="items-section-header">
                <div class="items-section-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="items-section-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18"/>
                    </svg>
                </div>
                <h2 class="items-section-title">
                    Alat Laboratorium
                </h2>
            </div>

            <div class="items-table-wrap">
                <table class="items-table">

                    <thead>
                        <tr>
                            <th class="col-number">No</th>
                            <th>Nama Alat</th>
                            <th>Lokasi</th>
                            <th class="col-unit">Satuan</th>
                            <th class="col-stock">Total Stok</th>
                            <th class="col-action">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                    @forelse($tools as $toolName => $toolGroup)

                        @php
                            $stock   = $toolGroup->sum('stock');
                            $minimum = $toolGroup->sum('minimum_stock');
                            $isLow   = $stock <= $minimum;
                        @endphp

                        <tr>

                            <td>
                                <span class="items-number">{{ $loop->iteration }}</span>
                            </td>

                            <td>
                                <div class="item-name">
                                    {{ $toolName }}
                                </div>
                                <div class="stock-badge-wrap">
                                    @if($isLow)
                                        <span class="stock-badge low">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                            STOK RENDAH
                                        </span>
                                    @else
                                        <span class="stock-badge safe">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                            STOK AMAN
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td>
                                @foreach($toolGroup as $tool)
                                    <div class="location-line">
                                        <span class="location-room">{{ $tool->room->name }}</span>
                                        <span class="location-separator">—</span>
                                        {{ $tool->location }}
                                        <span class="location-stock">({{ $tool->stock }})</span>
                                    </div>
                                @endforeach
                            </td>

                            <td class="unit-cell">
                                {{ $toolGroup->first()->unit }}
                            </td>

                            <td>
                                <span class="stock-total {{ $isLow ? 'low' : 'safe' }}">
                                    {{ $stock }}
                                </span>
                            </td>

                            <td>
                                <div class="action-stack">
                                    @foreach($toolGroup as $tool)
                                        <div class="action-row">

                                            <a href="{{ route('items.edit', $tool) }}"
                                               class="action-btn edit">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="action-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                                Edit
                                            </a>

                                            <form method="POST" action="{{ route('items.destroy', $tool) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    onclick="return confirm('Hapus item ini?')"
                                                    class="action-btn delete">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="action-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="3 6 5 6 21 6"/>
                                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                        <path d="M10 11v6M14 11v6"/>
                                                        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>

                                        </div>
                                    @endforeach
                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="6" class="empty-cell">
                                <svg class="empty-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18"/>
                                </svg>
                                Tidak ada data alat
                            </td>
                        </tr>

                    @endforelse

                    </tbody>

                </table>
            </div>

        </div>

        {{-- ========== BAHAN LABORATORIUM ========== --}}
        <div class="items-section-card material-section">

            {{-- Section header --}}
            <div class="items-section-header">
                <div class="items-section-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="items-section-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 2v7.31"/>
                        <path d="M14 9.3V1.99"/>
                        <path d="M8.5 2h7"/>
                        <path d="M14 9.3a6.5 6.5 0 1 1-4 0"/>
                    </svg>
                </div>
                <h2 class="items-section-title">
                    Bahan Laboratorium
                </h2>
            </div>

            <div class="items-table-wrap">
                <table class="items-table">

                    <thead>
                        <tr>
                            <th class="col-number">No</th>
                            <th>Nama Bahan</th>
                            <th>Lokasi</th>
                            <th class="col-unit">Satuan</th>
                            <th class="col-stock">Total Stok</th>
                            <th class="col-action">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                    @forelse($materials as $materialName => $materialGroup)

                        @php
                            $stock   = $materialGroup->sum('stock');
                            $minimum = $materialGroup->sum('minimum_stock');
                            $isLow   = $stock <= $minimum;
                        @endphp

                        <tr>

                            <td>
                                <span class="items-number">{{ $loop->iteration }}</span>
                            </td>

                            <td>
                                <div class="item-name">
                                    {{ $materialName }}
                                </div>
                                <div class="stock-badge-wrap">
                                    @if($isLow)
                                        <span class="stock-badge low">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                            STOK RENDAH
                                        </span>
                                    @else
                                        <span class="stock-badge safe">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                            STOK AMAN
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td>
                                @foreach($materialGroup as $material)
                                    <div class="location-line">
                                        <span class="location-room">{{ $material->room->name }}</span>
                                        <span class="location-separator">—</span>
                                        {{ $material->location }}
                                        <span class="location-stock">({{ $material->stock }})</span>
                                    </div>
                                @endforeach
                            </td>

                            <td class="unit-cell">
                                {{ $materialGroup->first()->unit }}
                            </td>

                            <td>
                                <span class="stock-total {{ $isLow ? 'low' : 'safe' }}">
                                    {{ $stock }}
                                </span>
                            </td>

                            <td>
                                <div class="action-stack">
                                    @foreach($materialGroup as $material)
                                        <div class="action-row">

                                            <a href="{{ route('items.edit', $material) }}"
                                               class="action-btn edit">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="action-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                                Edit
                                            </a>

                                            <form method="POST" action="{{ route('items.destroy', $material) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    onclick="return confirm('Hapus item ini?')"
                                                    class="action-btn delete">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="action-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="3 6 5 6 21 6"/>
                                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                        <path d="M10 11v6M14 11v6"/>
                                                        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>

                                        </div>
                                    @endforeach
                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="6" class="empty-cell">
                                <svg class="empty-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M10 2v7.31"/><path d="M14 9.3V1.99"/><path d="M8.5 2h7"/>
                                    <path d="M14 9.3a6.5 6.5 0 1 1-4 0"/>
                                </svg>
                                Tidak ada data bahan
                            </td>
                        </tr>

                    @endforelse

                    </tbody>

                </table>
            </div>

        </div>

    </div>
</div>

@endsection
