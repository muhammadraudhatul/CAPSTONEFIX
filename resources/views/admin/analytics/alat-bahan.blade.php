@extends('admin.layouts.app')

@section('title', 'Analisis Stok Barang')

@push('styles')
<style>
    /* ─── Flip7 muted theme: hanya desain, komponen/logic/script tetap dipertahankan ─── */
    .f7-wrapper {
        --teal:          #4B958F;
        --teal-light:    #75B6B0;
        --teal-dark:     #356F6B;
        --teal-bg:       #E7EFED;
        --gold:          #D7BD62;
        --gold-light:    #E9DCA7;
        --gold-dark:     #B69A3E;
        --coral:         #C97A62;
        --coral-light:   #DDA18E;
        --coral-dark:    #A85F49;
        --cream:         #F7F3E8;
        --sky:           #7FA8BF;
        --surface:       #EDF2F1;
        --surface-card:  #FAFBFA;
        --success:       #4F8E72;
        --error:         #B86155;
        --text-dark:     #1F2A29;
        --text-mid:      #4C615F;
        --text-muted:    #657675;
        --text-soft:     #8A9997;
        --border:        rgba(75, 149, 143, 0.14);

        --shadow-card:   0 10px 28px rgba(53, 111, 107, 0.07);
        --shadow-soft:   0 14px 34px rgba(31, 42, 41, 0.06);
        --shadow-hover:  0 16px 32px rgba(31, 42, 41, 0.07);
        --shadow-sm:     0 2px 8px rgba(31, 42, 41, 0.05);
        --shadow-focus:  0 0 0 4px rgba(75,149,143,0.10);

        --radius-sm:  8px;
        --radius-md:  16px;
        --radius-lg:  24px;
        --radius-xl:  32px;
        --radius-pill:999px;

        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'PingFang SC', 'Microsoft YaHei', sans-serif;
        color: var(--text-dark);
        padding: 2rem 1.75rem;
        min-height: 100vh;
        border-radius: 28px;
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(circle at 8% 8%, rgba(215, 189, 98, 0.09), transparent 25%),
            radial-gradient(circle at 88% 12%, rgba(75, 149, 143, 0.08), transparent 28%),
            linear-gradient(135deg, #F3F6F5 0%, var(--surface) 52%, #EEF3F1 100%);
    }

    .f7-wrapper *,
    .f7-wrapper *::before,
    .f7-wrapper *::after {
        box-sizing: border-box;
    }

    .f7-wrapper::before,
    .f7-wrapper::after {
        content: '';
        position: absolute;
        border-radius: 28px;
        border: 1px solid rgba(53, 111, 107, 0.05);
        background: rgba(247, 243, 232, 0.20);
        pointer-events: none;
        z-index: 0;
    }

    .f7-wrapper::before {
        width: 150px;
        height: 210px;
        right: -74px;
        top: 110px;
        transform: rotate(-8deg);
    }

    .f7-wrapper::after {
        width: 108px;
        height: 146px;
        left: -52px;
        bottom: 138px;
        transform: rotate(10deg);
    }

    .f7-page-header,
    .stats-grid,
    .f7-card,
    .charts-row {
        position: relative;
        z-index: 1;
    }

    /* ─── Page Header ─── */
    .f7-page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1.75rem;
        gap: 1rem;
        flex-wrap: wrap;
        padding: 1.65rem 1.8rem;
        border-radius: 30px;
        background: linear-gradient(135deg, rgba(247, 243, 232, 0.96), rgba(250, 251, 250, 0.95));
        border: 1px solid rgba(255, 255, 255, 0.80);
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }

    .f7-page-header::before {
        content: '';
        position: absolute;
        inset: 0 auto 0 0;
        width: 6px;
        background: linear-gradient(180deg, rgba(215, 189, 98, 0.85), rgba(75, 149, 143, 0.85), rgba(201, 122, 98, 0.78));
    }

    .f7-page-header::after {
        content: 'STOCK ANALYTICS';
        position: absolute;
        right: 1.5rem;
        bottom: 0.85rem;
        color: rgba(53, 111, 107, 0.05);
        font-weight: 900;
        font-size: clamp(1.6rem, 5vw, 3.8rem);
        line-height: 1;
        letter-spacing: 0.08em;
        pointer-events: none;
    }

    .f7-page-header > div,
    .f7-page-header > form {
        position: relative;
        z-index: 1;
    }

    .f7-page-title {
        font-size: clamp(1.9rem, 3vw, 2.55rem);
        font-weight: 900;
        color: var(--text-dark);
        letter-spacing: -0.045em;
        line-height: 1.05;
        position: relative;
        display: inline-block;
        margin: 0 0 0.45rem;
        text-shadow: 2px 2px 0 rgba(215, 189, 98, 0.20);
    }

    .f7-page-title::after {
        content: '';
        display: block;
        height: 4px;
        width: 58%;
        background: linear-gradient(90deg, rgba(215, 189, 98, 0.95), transparent);
        border-radius: var(--radius-pill);
        margin-top: 8px;
    }

    .f7-page-subtitle {
        color: #4C8A85;
        font-size: 0.95rem;
        margin-top: 0.25rem;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    /* ─── Select Dropdown ─── */
    .f7-select {
        background: rgba(247, 243, 232, 0.88);
        border: 1px solid rgba(75,149,143,0.18);
        color: var(--text-dark);
        padding: 0.7rem 1rem;
        border-radius: var(--radius-pill);
        font-size: 0.875rem;
        font-weight: 900;
        cursor: pointer;
        box-shadow: 0 6px 18px rgba(53, 111, 107, 0.05);
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23657675' stroke-width='2' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        padding-right: 2.5rem;
        min-width: 240px;
    }

    .f7-select:focus {
        border-color: rgba(75,149,143,0.38);
        background-color: #FAFBFA;
        box-shadow: var(--shadow-focus);
    }

    .f7-select option {
        background: #FAFBFA;
        color: var(--text-dark);
    }

    /* ─── Card Base ─── */
    .f7-card {
        background: linear-gradient(180deg, rgba(250,251,250,0.98), rgba(244, 241, 234, 0.88));
        border: 1px solid rgba(255, 255, 255, 0.82);
        border-left: 7px solid var(--teal);
        border-radius: 26px;
        padding: 1.5rem;
        box-shadow: var(--shadow-soft);
        overflow: hidden;
        transition: transform 0.24s cubic-bezier(.2,.8,.2,1), box-shadow 0.24s, border-color 0.24s;
    }

    .f7-card:hover {
        transform: translateY(-2px);
        border-color: rgba(75,149,143,0.12);
        box-shadow: var(--shadow-hover);
    }

    /* ─── Stats Grid ─── */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .stat-box {
        background: linear-gradient(145deg, rgba(252, 252, 251, 0.98) 0%, rgba(247, 243, 232, 0.92) 100%);
        border: 1px solid rgba(255, 255, 255, 0.82);
        border-left: 7px solid var(--teal);
        border-radius: 22px;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 130px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 6px 18px rgba(53, 111, 107, 0.06);
        transition: transform 0.24s cubic-bezier(.2,.8,.2,1), box-shadow 0.24s, border-color 0.24s;
    }

    .stat-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 22px rgba(53, 111, 107, 0.08);
        border-color: rgba(75, 149, 143, 0.16);
    }

    .stat-box::before {
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

    .stat-box::after {
        content: '';
        position: absolute;
        left: 1.25rem;
        bottom: 0;
        width: 44px;
        height: 4px;
        border-radius: 999px 999px 0 0;
        background: rgba(215, 189, 98, 0.85);
    }

    .stat-box:nth-child(2) {
        border-left-color: var(--sky);
    }

    .stat-box:nth-child(2)::before {
        background: rgba(127,168,191,0.10);
    }

    .stat-box:nth-child(3) {
        border-left-color: var(--gold);
    }

    .stat-box:nth-child(3)::before {
        background: rgba(215,189,98,0.14);
    }

    .stat-box:nth-child(4) {
        border-left-color: var(--coral);
    }

    .stat-box:nth-child(4)::before {
        background: rgba(201,122,98,0.10);
    }

    .label {
        position: relative;
        z-index: 1;
        color: var(--text-muted);
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.10em;
        font-weight: 900;
        margin-bottom: 0.55rem;
    }

    .stat-val {
        position: relative;
        z-index: 1;
        font-size: 1.6rem;
        font-weight: 900;
        color: var(--text-dark);
        line-height: 1.1;
        letter-spacing: -0.04em;
    }

    .trend-text {
        position: relative;
        z-index: 1;
        font-size: 0.76rem;
        color: var(--text-soft);
        margin-top: auto;
        font-weight: 700;
    }

    /* ─── Section Title ─── */
    .f7-section-title {
        font-size: 1rem;
        font-weight: 900;
        color: var(--text-dark);
        letter-spacing: -0.01em;
        margin-bottom: 0.25rem;
    }

    .f7-section-sub {
        color: var(--text-muted);
        font-size: 0.82rem;
        margin-bottom: 1rem;
        font-weight: 700;
    }

    /* ─── AI Grid ─── */
    .ai-grid {
        display: grid;
        grid-template-columns: 1.4fr 1fr 1fr;
        gap: 1rem;
        align-items: stretch;
    }

    .ai-box {
        background: rgba(247, 243, 232, 0.62);
        border: 1px solid rgba(75,149,143,0.12);
        border-left: 5px solid var(--teal);
        border-radius: 20px;
        padding: 1.25rem;
        position: relative;
        overflow: hidden;
        transition: transform 0.22s, box-shadow 0.22s, border-color 0.22s;
    }

    .ai-box::before {
        content: '';
        position: absolute;
        width: 70px;
        height: 70px;
        right: -28px;
        top: -28px;
        border-radius: 999px;
        background: rgba(75,149,143,0.06);
        pointer-events: none;
    }

    .ai-box:first-child {
        border-left-color: var(--teal);
    }

    .ai-box:nth-child(2) {
        border-left-color: var(--gold);
    }

    .ai-box:nth-child(2)::before {
        background: rgba(215,189,98,0.10);
    }

    .ai-box:nth-child(3) {
        border-left-color: var(--coral);
    }

    .ai-box:nth-child(3)::before {
        background: rgba(201,122,98,0.08);
    }

    .ai-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 22px rgba(53, 111, 107, 0.07);
        border-color: rgba(75,149,143,0.18);
    }

    /* ─── Charts Row ─── */
    .charts-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .chart-container {
        height: 320px;
        width: 100%;
        position: relative;
    }

    /* ─── Table ─── */
    .f7-table-wrap {
        overflow-x: auto;
        border-radius: 20px;
    }

    .f7-table {
        width: 100%;
        min-width: 980px;
        border-collapse: collapse;
        text-align: left;
        font-size: 0.875rem;
    }

    .f7-table thead tr {
        background: rgba(231, 239, 237, 0.88);
        border-bottom: 2px solid rgba(75,149,143,0.10);
    }

    .f7-table th {
        padding: 0.92rem 1rem;
        color: #4A8C86;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.10em;
        font-weight: 900;
        white-space: nowrap;
    }

    .f7-table tbody tr {
        border-bottom: 1px solid rgba(75,149,143,0.08);
        transition: background 0.18s ease;
    }

    .f7-table tbody tr:hover {
        background: rgba(247, 243, 232, 0.58);
    }

    .f7-table td {
        padding: 0.92rem 1rem;
        color: var(--text-muted);
        font-weight: 700;
        vertical-align: middle;
    }

    .f7-table td:first-child {
        font-weight: 900;
        color: var(--text-dark);
    }

    /* ─── Status Badge ─── */
    .f7-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 28px;
        padding: 0.25rem 0.75rem;
        border-radius: var(--radius-pill);
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: 0.07em;
        border: 1px solid rgba(255, 255, 255, 0.60);
        box-shadow: var(--shadow-sm);
    }

    .f7-badge-danger {
        background: linear-gradient(135deg, rgba(201,122,98,0.16), rgba(201,122,98,0.08));
        color: var(--coral-dark);
        border-color: rgba(201,122,98,0.18);
    }

    .f7-badge-warning {
        background: linear-gradient(135deg, rgba(215,189,98,0.20), rgba(215,189,98,0.10));
        color: #7F6B2A;
        border-color: rgba(215,189,98,0.22);
    }

    .f7-badge-success {
        background: linear-gradient(135deg, rgba(79,142,114,0.15), rgba(79,142,114,0.08));
        color: #3C7359;
        border-color: rgba(79,142,114,0.18);
    }

    .f7-badge-muted {
        background: linear-gradient(135deg, rgba(31,42,41,0.06), rgba(31,42,41,0.03));
        color: var(--text-soft);
        border-color: rgba(31,42,41,0.08);
    }

    .f7-badge-sky {
        background: linear-gradient(135deg, rgba(127,168,191,0.16), rgba(127,168,191,0.08));
        color: #54768B;
        border-color: rgba(127,168,191,0.18);
    }

    /* ─── Stat Value Colors (status) ─── */
    .stat-val-green  { color: var(--success) !important; }
    .stat-val-gold   { color: #9F7D29 !important; }
    .stat-val-coral  { color: var(--coral-dark) !important; }
    .stat-val-muted  { color: var(--text-soft) !important; }

    /* ─── Divider ─── */
    .f7-divider {
        border: none;
        border-top: 2px dashed rgba(75,149,143,0.14);
        margin: 1rem 0;
    }

    /* ─── Page Enter Animation ─── */
    @keyframes f7FadeUp {
        from {
            opacity: 0;
            transform: translateY(12px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .f7-page-header,
    .stats-grid,
    .f7-wrapper > .f7-card,
    .charts-row {
        animation: f7FadeUp 0.35s ease both;
    }

    .stats-grid {
        animation-delay: 0.08s;
    }

    .f7-wrapper > .f7-card:nth-of-type(3) {
        animation-delay: 0.12s;
    }

    .charts-row {
        animation-delay: 0.16s;
    }

    .f7-wrapper > .f7-card:nth-of-type(5) {
        animation-delay: 0.20s;
    }

    .f7-wrapper > .f7-card:nth-of-type(6) {
        animation-delay: 0.24s;
    }

    /* ─── Responsive ─── */
    @media (max-width: 1100px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .ai-grid    { grid-template-columns: 1fr; }
    }

    @media (max-width: 768px) {
        .f7-wrapper {
            padding: 1.25rem 1rem;
            border-radius: 22px;
        }

        .charts-row { grid-template-columns: 1fr; }
        .stats-grid { grid-template-columns: 1fr; }

        .f7-page-header {
            flex-direction: column;
            padding: 1.35rem 1.25rem;
            border-radius: 24px;
        }

        .f7-select {
            width: 100%;
            min-width: 0;
        }
    }
</style>
@endpush

@section('content')
<div class="f7-wrapper">

    {{-- ─── Page Header ─── --}}
    <div class="f7-page-header">
        <div>
            <h1 class="f7-page-title">Analisis Stok Barang</h1>
            <p class="f7-page-subtitle">Monitoring penggunaan alat dan bahan laboratorium</p>
        </div>

        <form method="GET">
            <select name="item_id" onchange="this.form.submit()" class="f7-select">
                <option value="">— Pilih Alat / Bahan —</option>
                @foreach($all_tools as $tool)
                    <option value="{{ $tool->id }}" {{ request('item_id') == $tool->id ? 'selected' : '' }}>
                        {{ $tool->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- ─── Widget Statistik ─── --}}
    <div class="stats-grid">
        @php $dataExists = !empty($selected_tool); @endphp

        <div class="stat-box">
            <div class="label">Stok Saat Ini</div>
            <div class="stat-val">{{ $dataExists ? $selected_tool->stock : '—' }}</div>
        </div>

        <div class="stat-box">
            <div class="label">Frekuensi Dipinjam</div>
            <div class="stat-val">{{ $dataExists ? number_format($frekuensi_pakai) : '—' }}</div>
        </div>

        <div class="stat-box">
            <div class="label">Prediksi Habis</div>
            <div class="stat-val" style="font-size: 1.2rem;">
                {{ $dataExists ? ($prediksi_habis != '-' && $prediksi_habis != 'Habis' && $prediksi_habis != 'Tidak terprediksi' ? \Carbon\Carbon::parse($prediksi_habis)->format('d M Y') : $prediksi_habis) : '—' }}
            </div>
            <div class="trend-text">
                @if($dataExists && $sisa_hari > 0 && $sisa_hari < 999)
                    {{ $sisa_hari }} hari tersisa
                @elseif($dataExists && $sisa_hari >= 999)
                    Stok mencukupi
                @elseif($dataExists && $sisa_hari == 0)
                    Stok habis
                @else
                    —
                @endif
            </div>
        </div>

        <div class="stat-box">
            <div class="label">Status Stok</div>
            <div class="stat-val
                @if($dataExists)
                    @if($status_stok == 'Aman') stat-val-green
                    @elseif($status_stok == 'Peringatan') stat-val-gold
                    @elseif($status_stok == 'Kritis') stat-val-coral
                    @elseif($status_stok == 'Habis') stat-val-muted
                    @else stat-val-green
                    @endif
                @else stat-val-muted
                @endif
            ">
                {{ $dataExists ? $status_stok : '—' }}
            </div>
        </div>
    </div>

    {{-- ─── Widget Prediksi AI ─── --}}
    <div class="f7-card" style="margin-bottom: 1.5rem;">
        <div class="f7-section-title">Prediksi AI Penggunaan Berikutnya</div>
        <div class="f7-section-sub">Prediksi penggunaan item terpilih berdasarkan hasil pipeline AI terbaru</div>
        <hr class="f7-divider">

        <div class="ai-grid">
            <div class="ai-box">
                <div class="label">Item Terpilih</div>
                @if($dataExists)
                    <div class="stat-val" style="font-size: 1.25rem;">{{ $selected_tool->name }}</div>
                    <div class="trend-text" style="margin-top: 0.75rem;">
                        {{ $selected_tool->type ?? '—' }}
                        @if($selected_tool->room)
                            &nbsp;·&nbsp; {{ $selected_tool->room->name }}
                        @endif
                    </div>
                @else
                    <div class="stat-val stat-val-muted">—</div>
                    <div class="trend-text">Pilih alat atau bahan terlebih dahulu</div>
                @endif
            </div>

            <div class="ai-box">
                <div class="label">Prediksi Penggunaan Berikutnya</div>
                @if($dataExists && $aiPrediction)
                    <div style="display: flex; align-items: baseline; gap: 0.5rem;">
                        <div class="stat-val">{{ number_format($aiPrediction->predicted_next_usage, 2) }}</div>
                        <div style="color: var(--text-muted); font-size: 0.85rem; font-weight: 600;">unit</div>
                    </div>
                    <div class="trend-text" style="margin-top: 0.75rem;">
                        Histori: {{ $aiPrediction->history_count }} peminjaman
                        &nbsp;·&nbsp; {{ str_replace('_', ' ', $aiPrediction->data_status) }}
                    </div>
                @elseif($dataExists)
                    <div class="stat-val stat-val-muted" style="font-size: 1.1rem;">Belum tersedia</div>
                    <div class="trend-text">Jalankan ulang analisis setelah item memiliki data completed</div>
                @else
                    <div class="stat-val stat-val-muted">—</div>
                    <div class="trend-text">Belum ada item dipilih</div>
                @endif
            </div>

            <div class="ai-box">
                <div class="label">Level Prediksi Penggunaan</div>
                @if($dataExists && $aiPrediction)
                    @php
                        $aiStatus = strtolower($aiPrediction->prediction_status ?? 'rendah');
                        $aiBadgeClass = $aiStatus === 'tinggi' ? 'f7-badge-danger'
                                      : ($aiStatus === 'sedang' ? 'f7-badge-warning' : 'f7-badge-success');
                    @endphp
                    <div style="margin-bottom: 0.75rem;">
                        <span class="f7-badge {{ $aiBadgeClass }}" style="font-size: 0.9rem; padding: 0.35rem 1rem;">
                            {{ ucfirst($aiPrediction->prediction_status ?? 'rendah') }}
                        </span>
                    </div>
                    <div class="trend-text">
                        @if($lastAiRun)
                            Update AI:
                            {{ $lastAiRun->finished_at ? $lastAiRun->finished_at->format('d M Y H:i') : $lastAiRun->created_at->format('d M Y H:i') }}
                        @else
                            AI belum pernah dijalankan
                        @endif
                    </div>
                @elseif($dataExists)
                    <div class="stat-val stat-val-muted" style="font-size: 1.1rem;">—</div>
                    <div class="trend-text">Belum ada hasil AI untuk item ini</div>
                @else
                    <div class="stat-val stat-val-muted">—</div>
                    <div class="trend-text">Belum ada item dipilih</div>
                @endif
            </div>
        </div>
    </div>

    {{-- ─── Widget Prediksi Kebutuhan Semester ─── --}}
    <div class="f7-card" style="margin-bottom: 1.5rem;">
        <div class="f7-section-title">Prediksi Kebutuhan 6 Bulan / Semester</div>
        <div class="f7-section-sub">
            Estimasi kebutuhan item terpilih selama 6 bulan ke depan berdasarkan prediksi AI dan histori frekuensi peminjaman
        </div>
        <hr class="f7-divider">

        <div class="ai-grid">
            <div class="ai-box">
                <div class="label">Kebutuhan Semester</div>

                @if($dataExists && $aiPrediction)
                    <div style="display: flex; align-items: baseline; gap: 0.5rem;">
                        <div class="stat-val">
                            {{ number_format($aiPrediction->predicted_semester_usage ?? 0, 2) }}
                        </div>
                        <div style="color: var(--text-muted); font-size: 0.85rem; font-weight: 600;">
                            unit
                        </div>
                    </div>

                    <div class="trend-text" style="margin-top: 0.75rem;">
                        Estimasi total kebutuhan untuk 6 bulan ke depan
                    </div>
                @elseif($dataExists)
                    <div class="stat-val stat-val-muted" style="font-size: 1.1rem;">Belum tersedia</div>
                    <div class="trend-text">Jalankan ulang analisis setelah item memiliki data completed</div>
                @else
                    <div class="stat-val stat-val-muted">—</div>
                    <div class="trend-text">Belum ada item dipilih</div>
                @endif
            </div>

            <div class="ai-box">
                <div class="label">Estimasi Frekuensi Semester</div>

                @if($dataExists && $aiPrediction)
                    <div style="display: flex; align-items: baseline; gap: 0.5rem;">
                        <div class="stat-val">
                            {{ number_format($aiPrediction->estimated_semester_borrowing_count ?? 0, 2) }}
                        </div>
                        <div style="color: var(--text-muted); font-size: 0.85rem; font-weight: 600;">
                            peminjaman
                        </div>
                    </div>

                    <div class="trend-text" style="margin-top: 0.75rem;">
                        Berdasarkan pola frekuensi peminjaman historis
                    </div>
                @elseif($dataExists)
                    <div class="stat-val stat-val-muted" style="font-size: 1.1rem;">Belum tersedia</div>
                    <div class="trend-text">Belum ada histori cukup untuk estimasi semester</div>
                @else
                    <div class="stat-val stat-val-muted">—</div>
                    <div class="trend-text">Belum ada item dipilih</div>
                @endif
            </div>

            <div class="ai-box">
                <div class="label">Status Kecukupan Semester</div>

                @if($dataExists && $aiPrediction)
                    @php
                        $semesterStatus = strtolower($aiPrediction->semester_stock_status ?? 'belum ada data');

                        $semesterBadgeClass = str_contains($semesterStatus, 'kritis') || str_contains($semesterStatus, 'restock')
                            ? 'f7-badge-danger'
                            : (str_contains($semesterStatus, 'pantau')
                                ? 'f7-badge-warning'
                                : (str_contains($semesterStatus, 'aman')
                                    ? 'f7-badge-success'
                                    : 'f7-badge-warning'));
                    @endphp

                    <div style="margin-bottom: 0.75rem;">
                        <span class="f7-badge {{ $semesterBadgeClass }}" style="font-size: 0.9rem; padding: 0.35rem 1rem;">
                            {{ $aiPrediction->semester_stock_status ?? 'Belum ada data' }}
                        </span>
                    </div>

                    <div class="trend-text">
                        Kekurangan estimasi:
                        <strong>
                            {{ number_format($aiPrediction->semester_stock_gap ?? 0, 2) }} unit
                        </strong>
                    </div>
                @elseif($dataExists)
                    <div class="stat-val stat-val-muted" style="font-size: 1.1rem;">—</div>
                    <div class="trend-text">Belum ada status semester untuk item ini</div>
                @else
                    <div class="stat-val stat-val-muted">—</div>
                    <div class="trend-text">Belum ada item dipilih</div>
                @endif
            </div>
        </div>

        @if($dataExists && $aiPrediction)
            <div style="
                margin-top: 1rem;
                padding: 1rem 1.1rem;
                border-radius: 1rem;
                background: rgba(124, 58, 237, 0.10);
                border: 1px solid rgba(124, 58, 237, 0.20);
            ">
                <div style="font-weight: 800; color: var(--text-primary); margin-bottom: 0.4rem;">
                    {{ $aiPrediction->semester_recommendation ?? 'Belum ada rekomendasi' }}
                </div>

                <div class="trend-text" style="line-height: 1.6;">
                    {{ $aiPrediction->semester_reason ?? 'Belum ada alasan rekomendasi semester.' }}
                </div>
            </div>
        @endif
    </div>

    {{-- ─── Charts Row ─── --}}
    <div class="charts-row">
        <div class="f7-card">
            <div class="f7-section-title">Tren Penggunaan</div>
            <div class="f7-section-sub">Visualisasi peminjaman vs pengembalian</div>
            <div class="chart-container">
                <canvas id="lineChart"></canvas>
            </div>
        </div>

        <div class="f7-card">
            <div class="f7-section-title">Distribusi per Kategori</div>
            <div class="f7-section-sub">Frekuensi peminjaman bulan ini</div>
            <div class="chart-container">
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ─── Bar Chart: Penggunaan Bulanan ─── --}}
    <div class="f7-card" style="margin-top: 1.5rem;">
        <div class="f7-section-title">Penggunaan Bulanan (Total Semua Barang)</div>
        <div class="f7-section-sub">Total peminjaman barang berdasarkan data transaksi asli</div>
        <div style="height: 300px;">
            <canvas id="barChart"></canvas>
        </div>
    </div>

    {{-- ─── Tabel Ringkasan Stok Barang ─── --}}
    <div class="f7-card" style="margin-top: 1.5rem;">
        <div style="margin-bottom: 1.25rem;">
            <h2 class="f7-section-title" style="font-size: 1.15rem;">Ringkasan Stok Barang</h2>
            <p class="f7-section-sub" style="margin-bottom: 0;">
                Status dan prediksi semua item laboratorium berdasarkan data penggunaan terkini.
            </p>
        </div>
        <hr class="f7-divider">

        <div class="f7-table-wrap">
            <table class="f7-table">
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th>Stok</th>
                        <th>Min Stok</th>
                        <th>Rata-rata/Hari</th>
                        <th>Prediksi Habis</th>
                        <th>Prediksi AI</th>
                        <th>Status AI</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($all_tools as $item)
                    <tr>
                        <td>{{ $item->name }}</td>

                        <td>
                            <span style="font-weight: 700; color: var(--teal-dark);">{{ $item->stock }}</span>
                        </td>

                        <td style="color: var(--text-muted);">{{ $item->minimum_stock }}</td>

                        <td>{{ number_format($item->avg_usage ?? 0, 2) }}</td>

                        <td>
                            @if(($item->prediction_date ?? '-') != '-')
                                <span style="font-weight: 600;">{{ $item->prediction_date }}</span>
                                <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 2px;">
                                    @if(($item->remaining_days ?? 999) > 0 && ($item->remaining_days ?? 999) < 999)
                                        {{ $item->remaining_days }} hari lagi
                                    @elseif(($item->remaining_days ?? 999) == 0)
                                        Habis
                                    @else
                                        —
                                    @endif
                                </div>
                            @else
                                <span style="color: var(--text-muted);">—</span>
                            @endif
                        </td>

                        <td>
                            @if($item->ai_prediction)
                                <span style="font-weight: 700;">{{ number_format($item->ai_prediction->predicted_next_usage, 2) }}</span>
                                <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 2px;">
                                    {{ str_replace('_', ' ', $item->ai_prediction->data_status) }}
                                </div>
                            @else
                                <span style="color: var(--text-muted);">—</span>
                            @endif
                        </td>

                        <td>
                            @if($item->ai_prediction)
                                @php
                                    $aiTableStatus = strtolower($item->ai_prediction->prediction_status ?? 'rendah');
                                    $aiTableBadge  = $aiTableStatus === 'tinggi' ? 'f7-badge-danger'
                                                   : ($aiTableStatus === 'sedang' ? 'f7-badge-warning' : 'f7-badge-success');
                                @endphp
                                <span class="f7-badge {{ $aiTableBadge }}">
                                    {{ ucfirst($item->ai_prediction->prediction_status ?? 'rendah') }}
                                </span>
                            @else
                                <span class="f7-badge f7-badge-muted">—</span>
                            @endif
                        </td>

                        <td>
                            @php 
                                $isLow      = $item->stock <= $item->minimum_stock;
                                $isCritical = ($item->remaining_days ?? 999) <= 7 && ($item->remaining_days ?? 999) > 0;
                                $isWarning  = ($item->remaining_days ?? 999) <= 14 && ($item->remaining_days ?? 999) > 7;
                            @endphp

                            @if($isLow || $isCritical)
                                <span class="f7-badge f7-badge-danger">Perlu Tindakan</span>
                            @elseif($isWarning)
                                <span class="f7-badge f7-badge-warning">Peringatan</span>
                            @else
                                <span class="f7-badge f7-badge-success">Aman</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="padding: 2rem; color: var(--text-muted); text-align: center; font-weight: 500;">
                            Belum ada data item.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ─── Flip7 Chart Defaults ───
    Chart.defaults.color = '#2d5a58';
    Chart.defaults.font.family = "-apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif";

    // Line Chart — Tren Penggunaan
    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [
                {
                    label: 'Jumlah Unit Dipinjam',
                    data: {!! json_encode($chartPinjam) !!},
                    borderColor: '#2BA8A2',
                    backgroundColor: 'rgba(43,168,162,0.10)',
                    tension: 0.3,
                    borderWidth: 3,
                    pointBackgroundColor: '#2BA8A2',
                    pointRadius: 4,
                    fill: true
                },
                {
                    label: 'Jumlah Unit Dikembalikan',
                    data: {!! json_encode($chartKembali) !!},
                    borderColor: '#FFD23F',
                    backgroundColor: 'rgba(255,210,63,0.10)',
                    tension: 0.3,
                    borderWidth: 3,
                    pointBackgroundColor: '#FFD23F',
                    pointRadius: 4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: { size: 12, weight: '600' },
                        color: '#2d5a58'
                    }
                },
                tooltip: {
                    backgroundColor: '#fff',
                    titleColor: '#1E3A39',
                    bodyColor: '#2d5a58',
                    borderColor: '#c3e0de',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: ${context.raw} unit`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        stepSize: 1,
                        color: '#6b9997',
                        callback: v => v + ' unit'
                    },
                    title: { display: true, text: 'Jumlah Unit', color: '#2d5a58', font: { weight: '600' } },
                    grid: { color: '#e0f0ef' }
                },
                x: {
                    title: { display: true, text: 'Tanggal', color: '#2d5a58', font: { weight: '600' } },
                    ticks: { maxRotation: 45, minRotation: 45, color: '#6b9997' },
                    grid: { color: '#e0f0ef' }
                }
            }
        }
    });

    // Pie Chart — Distribusi per Kategori
    new Chart(document.getElementById('pieChart'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($pieLabels) !!},
            datasets: [{
                data: {!! json_encode($pieData) !!},
                backgroundColor: ['#2BA8A2', '#FFD23F', '#EF6C4A', '#5DADE2', '#27AE60'],
                borderColor: '#fff',
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#2d5a58',
                        usePointStyle: true,
                        padding: 20,
                        font: { size: 12, weight: '600' }
                    }
                },
                tooltip: {
                    backgroundColor: '#fff',
                    titleColor: '#1E3A39',
                    bodyColor: '#2d5a58',
                    borderColor: '#c3e0de',
                    borderWidth: 1
                }
            }
        }
    });

    // Bar Chart — Penggunaan Bulanan
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($monthlyData->pluck('bulan')) !!},
            datasets: [{
                label: 'Total Unit Dipinjam',
                data: {!! json_encode($monthlyData->pluck('total')) !!},
                backgroundColor: '#2BA8A2',
                hoverBackgroundColor: '#FFD23F',
                borderRadius: 10,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#fff',
                    titleColor: '#1E3A39',
                    bodyColor: '#2d5a58',
                    borderColor: '#c3e0de',
                    borderWidth: 1
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#e0f0ef' },
                    ticks: { precision: 0, color: '#6b9997' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#6b9997', font: { weight: '600' } }
                }
            }
        }
    });
</script>
@endpush