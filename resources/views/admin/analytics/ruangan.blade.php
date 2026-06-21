@extends('admin.layouts.app')

@section('title', 'Analisis Ruangan')

@push('styles')
<style>
    /* ── Flip7 muted theme: desain saja, logic Blade dan script tetap dipertahankan ── */
    .dash-wrapper {
        --bg-base:            #EDF2F1;
        --bg-card:            #FAFBFA;
        --bg-card-hover:      #F7F3E8;
        --border:             rgba(75,149,143,0.14);
        --text-primary:       #1F2A29;
        --text-muted:         #657675;
        --text-dim:           #8A9997;

        --primary-teal:       #4B958F;
        --primary-teal-light: #75B6B0;
        --primary-teal-dark:  #356F6B;
        --primary-bg:         #E7EFED;

        --accent-gold:        #D7BD62;
        --accent-gold-light:  #E9DCA7;
        --accent-gold-dark:   #B69A3E;

        --coral:              #C97A62;
        --coral-light:        #DDA18E;
        --coral-dark:         #A85F49;

        --cream:              #F7F3E8;
        --sky-blue:           #7FA8BF;
        --success:            #4F8E72;
        --error:              #B86155;

        --accent-blue:   var(--sky-blue);
        --accent-purple: var(--coral);
        --accent-teal:   var(--primary-teal);
        --accent-orange: var(--accent-gold-dark);
        --accent-red:    var(--error);
        --accent-green:  var(--success);
        --positive:      var(--success);
        --negative:      var(--error);

        --shadow-sm:          0 2px 8px rgba(31,42,41,0.05);
        --shadow-md:          0 10px 22px rgba(53,111,107,0.08);
        --shadow-lg:          0 18px 38px rgba(31,42,41,0.10);
        --shadow-card:        0 10px 28px rgba(53,111,107,0.07);
        --shadow-soft:        0 14px 34px rgba(31,42,41,0.06);
        --shadow-coral-glow:  0 12px 26px rgba(201,122,98,0.13);
        --shadow-teal-glow:   0 12px 26px rgba(75,149,143,0.12);
        --shadow-accent-glow: 0 12px 26px rgba(215,189,98,0.13);
        --shadow-sky-glow:    0 12px 26px rgba(127,168,191,0.13);
        --shadow-focus:       0 0 0 4px rgba(75,149,143,0.10);

        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'PingFang SC', sans-serif;
        font-size: 14px;
        line-height: 1.6;
        color: var(--text-primary);
        padding: 2rem 1.75rem;
        box-sizing: border-box;
        min-height: calc(100vh - 1px);
        border-radius: 28px;
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(circle at 8% 8%, rgba(215,189,98,0.09), transparent 25%),
            radial-gradient(circle at 88% 12%, rgba(75,149,143,0.08), transparent 28%),
            linear-gradient(135deg, #F3F6F5 0%, var(--bg-base) 52%, #EEF3F1 100%);
    }

    .dash-wrapper *,
    .dash-wrapper *::before,
    .dash-wrapper *::after {
        box-sizing: border-box;
    }

    .dash-wrapper::before,
    .dash-wrapper::after {
        content: '';
        position: absolute;
        border-radius: 28px;
        border: 1px solid rgba(53,111,107,0.05);
        background: rgba(247,243,232,0.20);
        pointer-events: none;
        z-index: 0;
    }

    .dash-wrapper::before {
        width: 150px;
        height: 210px;
        right: -74px;
        top: 110px;
        transform: rotate(-8deg);
    }

    .dash-wrapper::after {
        width: 108px;
        height: 146px;
        left: -52px;
        bottom: 138px;
        transform: rotate(10deg);
    }

    .page-header,
    .stats-grid,
    .ai-forecast-grid,
    .charts-row,
    .charts-row-wide,
    .trend-card,
    .dash-wrapper .card {
        position: relative;
        z-index: 1;
    }

    .page-header {
        z-index: 60;
    }

    .stats-grid,
    .ai-forecast-grid,
    .charts-row,
    .charts-row-wide,
    .trend-card,
    .dash-wrapper .card {
        z-index: 1;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1.75rem;
        padding: 1.65rem 1.8rem;
        border-radius: 30px;
        background: linear-gradient(135deg, rgba(247,243,232,0.96), rgba(250,251,250,0.95));
        border: 1px solid rgba(255,255,255,0.80);
        box-shadow: var(--shadow-card);
        flex-wrap: wrap;
        gap: 1rem;
        overflow: visible;
    }

    .page-header::before {
        content: '';
        position: absolute;
        inset: 0 auto 0 0;
        width: 6px;
        border-radius: 30px 0 0 30px;
        background: linear-gradient(180deg, rgba(215,189,98,0.85), rgba(75,149,143,0.85), rgba(201,122,98,0.78));
    }

    .page-header::after {
        content: 'ROOM ANALYTICS';
        position: absolute;
        right: 1.5rem;
        bottom: 0.85rem;
        color: rgba(53,111,107,0.05);
        font-weight: 900;
        font-size: clamp(1.7rem, 5vw, 4rem);
        line-height: 1;
        letter-spacing: 0.08em;
        pointer-events: none;
    }

    .page-header > div,
    .page-header > form,
    .header-right {
        position: relative;
        z-index: 1;
    }

    .page-title {
        font-size: clamp(1.9rem, 3vw, 2.55rem);
        font-weight: 900;
        color: var(--text-primary);
        letter-spacing: -0.045em;
        line-height: 1.05;
        margin: 0 0 0.45rem;
        padding: 0;
        text-shadow: 2px 2px 0 rgba(215,189,98,0.20);
    }

    .page-subtitle {
        color: #4C8A85;
        font-size: 0.95rem;
        margin: 0;
        padding: 0;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .header-right {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-left: auto;
        text-align: right;
    }

    .header-right form {
        margin-left: auto;
    }

    .timestamp {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(247,243,232,0.88);
        border: 1px solid rgba(75,149,143,0.14);
        border-radius: 999px;
        padding: 0.55rem 1rem;
        font-size: 0.8rem;
        font-weight: 900;
        color: var(--primary-teal-dark);
        box-shadow: var(--shadow-sm);
        white-space: nowrap;
        align-self: center;
    }

    .room-select-wrap {
        position: relative;
        background: rgba(250,251,250,0.96);
        border: 1px solid rgba(75,149,143,0.15);
        color: var(--text-primary);
        padding: 0.55rem 1rem;
        border-radius: 999px;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        user-select: none;
        box-shadow: var(--shadow-sm);
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        min-width: 190px;
    }

    .room-select-wrap:hover {
        border-color: rgba(75,149,143,0.28);
        background: #FFFFFF;
        box-shadow: var(--shadow-teal-glow);
    }

    .room-select-wrap.open {
        border-color: rgba(75,149,143,0.38);
        box-shadow: var(--shadow-focus);
    }

    .room-select-label {
        flex: 1;
        font-size: 0.85rem;
        font-weight: 900;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .room-chevron {
        transition: transform 0.2s;
        flex-shrink: 0;
        color: var(--primary-teal);
    }

    .room-select-wrap.open .room-chevron {
        transform: rotate(180deg);
    }

    .room-dropdown-menu {
        display: none;
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        min-width: 100%;
        background: #FAFBFA;
        border: 1px solid rgba(75,149,143,0.15);
        border-radius: 18px;
        padding: 0.4rem;
        z-index: 120;
        box-shadow: var(--shadow-lg);
        max-height: 260px;
        overflow-y: auto;
    }

    .room-dropdown-menu.open {
        display: block;
    }

    .room-dropdown-item {
        padding: 0.55rem 0.9rem;
        border-radius: 12px;
        font-size: 0.82rem;
        font-weight: 800;
        color: var(--text-muted);
        transition: background 0.15s, color 0.15s;
        cursor: pointer;
        white-space: nowrap;
    }

    .room-dropdown-item:hover {
        background: rgba(231,239,237,0.88);
        color: var(--primary-teal-dark);
    }

    .room-dropdown-item.active {
        background: rgba(215,189,98,0.18);
        color: #7F6B2A;
        font-weight: 900;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: linear-gradient(145deg, rgba(252,252,251,0.98) 0%, rgba(247,243,232,0.92) 100%);
        border: 1px solid rgba(255,255,255,0.82);
        border-left: 7px solid var(--primary-teal-light);
        border-radius: 22px;
        padding: 1.25rem 1.25rem 1.2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 6px 18px rgba(53,111,107,0.06);
        transition: transform 0.24s cubic-bezier(.2,.8,.2,1), box-shadow 0.24s, border-color 0.24s, background 0.24s;
        animation: dashFadeUp 0.35s ease both;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        width: 78px;
        height: 78px;
        right: -30px;
        top: -30px;
        border-radius: 999px;
        background: rgba(75,149,143,0.07);
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
        background: rgba(215,189,98,0.85);
    }

    .stat-card:hover {
        transform: translateY(-2px);
        border-color: rgba(75,149,143,0.16);
        background: linear-gradient(145deg, #FFFFFF 0%, rgba(247,243,232,0.94) 100%);
        box-shadow: var(--shadow-md);
    }

    .stat-card.blue   { border-left-color: var(--sky-blue); }
    .stat-card.purple { border-left-color: var(--coral); }
    .stat-card.teal   { border-left-color: var(--primary-teal); }
    .stat-card.orange { border-left-color: var(--accent-gold); }
    .stat-card.red    { border-left-color: var(--error); }
    .stat-card.green  { border-left-color: var(--success); }

    .stat-card.blue::before   { background: rgba(127,168,191,0.10); }
    .stat-card.purple::before { background: rgba(201,122,98,0.10); }
    .stat-card.teal::before   { background: rgba(75,149,143,0.08); }
    .stat-card.orange::before { background: rgba(215,189,98,0.14); }
    .stat-card.red::before    { background: rgba(184,97,85,0.10); }
    .stat-card.green::before  { background: rgba(79,142,114,0.10); }

    .stat-card.blue:hover   { box-shadow: var(--shadow-sky-glow); }
    .stat-card.purple:hover { box-shadow: var(--shadow-coral-glow); }
    .stat-card.teal:hover   { box-shadow: var(--shadow-teal-glow); }
    .stat-card.orange:hover { box-shadow: var(--shadow-accent-glow); }
    .stat-card.red:hover    { box-shadow: 0 12px 26px rgba(184,97,85,0.13); }
    .stat-card.green:hover  { box-shadow: 0 12px 26px rgba(79,142,114,0.13); }

    .stat-card.red {
        animation: dashFadeUp 0.35s ease both, alertGlow 2.4s ease-in-out infinite;
    }

    .stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 0 0 0.75rem 0;
        position: relative;
        z-index: 1;
    }

    .stat-label-tag {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.10em;
        font-weight: 900;
    }

    .stat-label-tag.blue   { color: #54768B; }
    .stat-label-tag.purple { color: var(--coral-dark); }
    .stat-label-tag.teal   { color: var(--primary-teal-dark); }
    .stat-label-tag.orange { color: #7F6B2A; }
    .stat-label-tag.red    { color: var(--error); }
    .stat-label-tag.green  { color: #3C7359; }

    .stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        font-weight: 900;
        flex-shrink: 0;
        border: 1px solid rgba(255,255,255,0.62);
        box-shadow: var(--shadow-sm);
    }

    .stat-icon.blue   { background: rgba(127,168,191,0.12);  color: #54768B; }
    .stat-icon.purple { background: rgba(201,122,98,0.12);  color: var(--coral-dark); }
    .stat-icon.teal   { background: rgba(75,149,143,0.12);  color: var(--primary-teal-dark); }
    .stat-icon.orange { background: rgba(215,189,98,0.18);  color: #7F6B2A; }
    .stat-icon.red    { background: rgba(184,97,85,0.12);   color: var(--error); }
    .stat-icon.green  { background: rgba(79,142,114,0.12);  color: #3C7359; }

    .stat-value {
        position: relative;
        z-index: 1;
        font-size: 1.75rem;
        font-weight: 900;
        color: var(--text-primary);
        letter-spacing: -0.04em;
        line-height: 1.2;
        margin: 0 0 0.3rem 0;
        display: block;
    }

    .stat-value small {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--text-muted);
        margin-left: 0.1rem;
        letter-spacing: -0.02em;
    }

    .stat-sub {
        position: relative;
        z-index: 1;
        font-size: 0.78rem;
        color: var(--text-soft);
        font-weight: 700;
        margin: 0;
        display: block;
    }

    .dash-wrapper .card {
        background: linear-gradient(180deg, rgba(250,251,250,0.98), rgba(244,241,234,0.88));
        border: 1px solid rgba(255,255,255,0.82);
        border-left: 7px solid var(--primary-teal);
        border-radius: 26px;
        padding: 1.5rem;
        box-shadow: var(--shadow-soft);
        overflow: hidden;
        transition: transform 0.24s cubic-bezier(.2,.8,.2,1), box-shadow 0.24s, border-color 0.24s, background 0.24s;
        animation: dashFadeUp 0.35s ease both;
    }

    .dash-wrapper .card:hover {
        transform: translateY(-2px);
        border-color: rgba(75,149,143,0.12);
        background: linear-gradient(180deg, #FFFFFF, rgba(244,241,234,0.90));
        box-shadow: var(--shadow-hover);
    }

    .card-title {
        font-size: 1rem;
        font-weight: 900;
        color: var(--text-primary);
        margin: 0 0 0.25rem 0;
        padding: 0;
        letter-spacing: -0.01em;
    }

    .card-subtitle {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.09em;
        color: var(--text-muted);
        margin: 0 0 1.25rem 0;
        padding-bottom: 0.85rem;
        border-bottom: 2px dashed rgba(75,149,143,0.14);
        font-weight: 900;
    }

    .chart-wrap              { position: relative; width: 100%; }
    .chart-wrap canvas       { display: block; width: 100% !important; }
    .chart-container         { height: 280px; width: 100%; position: relative; }
    .chart-container-sm      { height: 240px; width: 100%; position: relative; }
    .chart-container-xs      { height: 190px; width: 100%; position: relative; }

    .trend-card { margin-bottom: 1.5rem; }
    .trend-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.25rem;
    }

    .chart-legend {
        display: flex;
        gap: 1.25rem;
        flex-wrap: wrap;
        margin-top: 0.75rem;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.72rem;
        color: var(--text-muted);
        font-weight: 800;
    }

    .legend-line {
        width: 20px;
        height: 3px;
        border-radius: 999px;
    }

    .charts-row-wide {
        display: grid;
        grid-template-columns: 5fr 7fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .charts-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .ai-forecast-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .donut-legend {
        margin-top: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.55rem;
    }

    .donut-legend-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.78rem;
    }

    .donut-legend-left {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        color: var(--text-primary);
        font-weight: 900;
        min-width: 120px;
    }

    .donut-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .donut-bar-wrap {
        flex: 1;
        margin: 0 0.75rem;
        height: 5px;
        background: rgba(75,149,143,0.12);
        border-radius: 999px;
        overflow: hidden;
    }

    .donut-bar-fill {
        height: 100%;
        border-radius: 999px;
    }

    .donut-pct {
        font-size: 0.78rem;
        font-weight: 900;
        color: var(--text-muted);
        min-width: 38px;
        text-align: right;
    }

    .ts-legend {
        display: flex;
        gap: 1.25rem;
        flex-wrap: wrap;
        margin-bottom: 0.75rem;
    }

    .ts-legend-item {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.72rem;
        color: var(--text-muted);
        font-weight: 800;
    }

    .ts-legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 3px;
        flex-shrink: 0;
    }

    .table-wrap {
        overflow-x: auto;
        border-radius: 18px;
    }

    .dash-wrapper table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.865rem;
    }

    .dash-wrapper thead th {
        text-align: left;
        font-size: 0.72rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.10em;
        color: #4A8C86;
        padding: 0.75rem 1rem;
        border-bottom: 2px solid rgba(75,149,143,0.10);
        background: rgba(231,239,237,0.88);
        white-space: nowrap;
    }

    .dash-wrapper tbody tr {
        border-bottom: 1px solid rgba(75,149,143,0.08);
        transition: background 0.18s ease;
    }

    .dash-wrapper tbody tr:last-child { border-bottom: none; }
    .dash-wrapper tbody tr:hover { background: rgba(247,243,232,0.58); }

    .dash-wrapper tbody td {
        padding: 0.85rem 1rem;
        color: var(--text-primary);
        vertical-align: middle;
        border: none;
        background: transparent;
        font-weight: 700;
    }

    .dash-wrapper tbody td.muted {
        color: var(--text-muted);
    }

    .room-name {
        font-weight: 900;
        color: var(--primary-teal-dark);
    }

    .dash-wrapper .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.72rem;
        font-weight: 900;
        padding: 0.25rem 0.7rem;
        border-radius: 999px;
        line-height: 1.4;
        white-space: nowrap;
        border: 1px solid rgba(255,255,255,0.60);
        box-shadow: var(--shadow-sm);
    }

    .badge-approved        { background: rgba(127,168,191,0.14); color: #54768B; }
    .badge-completed,
    .badge-returned        { background: rgba(79,142,114,0.14); color: #3C7359; }
    .badge-waiting_return  { background: rgba(75,149,143,0.14); color: var(--primary-teal-dark); }
    .badge-pending         { background: rgba(215,189,98,0.20); color: #7F6B2A; }
    .badge-rejected,
    .badge-cancelled       { background: rgba(184,97,85,0.13); color: var(--error); }
    .badge-ai-used         { background: rgba(127,168,191,0.14); color: #54768B; }
    .badge-ai-free         { background: rgba(31,42,41,0.06); color: var(--text-soft); }
    .badge-anomaly         { background: rgba(201,122,98,0.16); color: var(--coral-dark); animation: boomPulse 2s infinite; }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--text-dim);
        background: rgba(247,243,232,0.34);
        border-radius: 20px;
    }

    .empty-state svg {
        margin-bottom: 0.75rem;
        opacity: 0.62;
    }

    .empty-state p {
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 800;
        margin: 0;
    }

    .empty-row td {
        text-align: center;
        color: var(--text-muted);
        padding: 2rem;
        font-size: 0.85rem;
    }

    @keyframes dashFadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    @keyframes boomPulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(201,122,98,0.30); }
        50%      { box-shadow: 0 0 0 6px rgba(201,122,98,0.10); }
    }

    @keyframes alertGlow {
        0%, 100% { box-shadow: 0 6px 18px rgba(53,111,107,0.06); }
        50%      { box-shadow: 0 12px 26px rgba(184,97,85,0.13); }
    }

    @media (max-width: 1100px) {
        .stats-grid        { grid-template-columns: repeat(2, 1fr); }
        .ai-forecast-grid  { grid-template-columns: repeat(2, 1fr); }
        .charts-row-wide   { grid-template-columns: 1fr; }
        .charts-row        { grid-template-columns: 1fr; }
    }

    @media (max-width: 640px) {
        .dash-wrapper      { padding: 1.25rem 1rem; border-radius: 22px; }
        .stats-grid        { grid-template-columns: 1fr; }
        .ai-forecast-grid  { grid-template-columns: 1fr; }
        .page-header       { padding: 1.35rem 1.25rem; border-radius: 24px; }
        .page-title        { font-size: 1.65rem; }
        .header-right      { width: 100%; }
        .timestamp,
        .room-select-wrap  { width: 100%; }
    }
</style>
@endpush

@section('content')

@php
    $donutColors = ['#2BA8A2','#EF6C4A','#FFD23F','#5DADE2','#27AE60','#3CC4BD','#FF8A6A','#E6B800'];

    $aiStatus = strtolower($aiRoomSummary->predicted_occupancy_status ?? 'rendah');
    $aiStatusColor = $aiStatus === 'tinggi'
        ? '#E74C3C'
        : ($aiStatus === 'sedang' ? '#E6B800' : '#27AE60');

    $aiUpdateText = $lastAiRun
        ? (($lastAiRun->finished_at ?? $lastAiRun->created_at)->format('d M Y, H:i'))
        : 'AI belum pernah dijalankan';
@endphp

<div class="dash-wrapper">

    <div class="page-header">
        <div>
            <h1 class="page-title">🏢 Analisis Ruangan</h1>
            <p class="page-subtitle">Monitoring tingkat penggunaan, forecasting okupansi, dan anomali ruangan</p>
        </div>

        <div class="header-right">
            <div class="timestamp">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
                {{ now()->isoFormat('D MMM YYYY, HH:mm') }}
            </div>

            <form method="GET" id="roomForm">
                <input type="hidden" name="room_id" id="roomIdInput" value="{{ request('room_id') }}">
                <div class="room-select-wrap" id="roomDropdown">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                        <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                    </svg>

                    <span class="room-select-label" id="roomDropdownLabel">
                        {{ $selected_room ? $selected_room->name : '-- Pilih Ruangan --' }}
                    </span>

                    <svg class="room-chevron" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>

                    <div class="room-dropdown-menu" id="roomDropdownMenu">
                        <div class="room-dropdown-item {{ !request('room_id') ? 'active' : '' }}" data-value="">-- Pilih Ruangan --</div>
                        @foreach($all_rooms as $room)
                            <div class="room-dropdown-item {{ request('room_id') == $room->id ? 'active' : '' }}" data-value="{{ $room->id }}">
                                {{ $room->name }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="stats-grid">

        <div class="stat-card blue">
            <div class="stat-top">
                <div class="stat-label-tag blue">Total Peminjaman</div>
                <div class="stat-icon blue">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                        <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                    </svg>
                </div>
            </div>
            <span class="stat-value">{{ number_format($total_peminjaman) }}</span>
            <span class="stat-sub">{{ $selected_room ? 'Sepanjang waktu untuk ruangan ini' : 'Pilih ruangan untuk detail' }}</span>
        </div>

        <div class="stat-card purple">
            <div class="stat-top">
                <div class="stat-label-tag purple">Okupansi</div>
                <div class="stat-icon purple">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
            </div>
            <span class="stat-value">{{ $occupancy }}<small>%</small></span>
            <span class="stat-sub">Dari total peminjaman 30 hari terakhir</span>
        </div>

        <div class="stat-card teal">
            <div class="stat-top">
                <div class="stat-label-tag teal">Jam Terpadat</div>
                <div class="stat-icon teal">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
            </div>
            <span class="stat-value" style="font-size:1.4rem; line-height:1.3;">
                {{ $jam_terpadat !== '-' ? $jam_terpadat : '-' }}
            </span>
            <span class="stat-sub">Range waktu paling sering dipinjam</span>
        </div>

        <div class="stat-card orange">
            <div class="stat-top">
                <div class="stat-label-tag orange">Durasi Rata-rata</div>
                <div class="stat-icon orange">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
            </div>
            <span class="stat-value">
                {{ $rata_rata_durasi > 0 ? $rata_rata_durasi : '-' }}<small>{{ $rata_rata_durasi > 0 ? ' Jam' : '' }}</small>
            </span>
            <span class="stat-sub">{{ $rata_rata_durasi > 0 ? 'Rata-rata per peminjaman' : 'Belum ada data' }}</span>
        </div>

    </div>

    <div class="ai-forecast-grid">

        <div class="stat-card green">
            <div class="stat-top">
                <div class="stat-label-tag green">Forecast AI Okupansi Hari Kerja Berikutnya</div>
                <div class="stat-icon green">AI</div>
            </div>
            <span class="stat-value">
                @if($selected_room && $aiRoomSummary)
                    {{ number_format($aiRoomSummary->predicted_occupancy_rate * 100, 1) }}<small>%</small>
                @else
                    -
                @endif
            </span>
            <span class="stat-sub">
                {{ $selected_room && $aiRoomSummary ? 'Prediksi tingkat keterpakaian ruangan' : 'Pilih ruangan dan jalankan analisis AI' }}
            </span>
        </div>

        <div class="stat-card blue">
            <div class="stat-top">
                <div class="stat-label-tag blue">Prediksi Slot Hari Kerja Berikutnya</div>
                <div class="stat-icon blue">↗</div>
            </div>
            <span class="stat-value">
                @if($selected_room && $aiRoomSummary)
                    {{ $aiRoomSummary->predicted_used_slot }}<small>/{{ $aiRoomSummary->total_slot }}</small>
                @else
                    -
                @endif
            </span>
            <span class="stat-sub">Jumlah slot yang diprediksi terpakai</span>
        </div>

        <div class="stat-card teal">
            <div class="stat-top">
                <div class="stat-label-tag teal">Jam Terpadat AI</div>
                <div class="stat-icon teal">⏱</div>
            </div>
            <span class="stat-value" style="font-size:1.4rem; line-height:1.3;">
                @if($selected_room && $aiRoomSummary && $aiRoomSummary->predicted_peak_hour !== null)
                    {{ $aiRoomSummary->predicted_peak_hour }}.30
                @else
                    -
                @endif
            </span>
            <span class="stat-sub">
                Probabilitas:
                {{ $selected_room && $aiRoomSummary && $aiRoomSummary->peak_hour_probability !== null ? number_format($aiRoomSummary->peak_hour_probability * 100, 1) . '%' : '-' }}
            </span>
        </div>

        <div class="stat-card {{ $aiStatus === 'tinggi' ? 'red' : ($aiStatus === 'sedang' ? 'orange' : 'green') }}">
            <div class="stat-top">
                <div class="stat-label-tag {{ $aiStatus === 'tinggi' ? 'red' : ($aiStatus === 'sedang' ? 'orange' : 'green') }}">Status AI</div>
                <div class="stat-icon {{ $aiStatus === 'tinggi' ? 'red' : ($aiStatus === 'sedang' ? 'orange' : 'green') }}">●</div>
            </div>
            <span class="stat-value" style="font-size:1.35rem; color: {{ $aiStatusColor }};">
                {{ $selected_room && $aiRoomSummary ? ucfirst($aiRoomSummary->predicted_occupancy_status ?? 'rendah') : '-' }}
            </span>
            <span class="stat-sub">Update AI: {{ $aiUpdateText }}</span>
        </div>

    </div>

    <div class="charts-row">

        <div class="card">
            <div class="card-title">🤖 Forecasting Slot Hari Kerja Berikutnya AI</div>
            <div class="card-subtitle">
                Probabilitas keterpakaian setiap slot UNTUK HARI KERJA BERIKUTNYA
                @if($selected_room) — {{ $selected_room->name }} @endif
            </div>

            @if($selected_room && $aiRoomSlots->count() > 0)
                <div class="chart-container-sm">
                    <canvas id="aiSlotChart"></canvas>
                </div>
            @else
                <div class="empty-state">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                    </svg>
                    <p>{{ $selected_room ? 'Belum ada output forecasting AI untuk ruangan ini' : 'Pilih ruangan untuk melihat forecasting AI' }}</p>
                </div>
            @endif
        </div>

        <div class="card">
            <div class="card-title">🚨 Anomali Penggunaan Ruangan</div>
            <div class="card-subtitle">
                Pola penggunaan tidak biasa berdasarkan Isolation Forest
                @if($selected_room) — {{ $selected_room->name }} @endif
            </div>

            @if($aiRoomAnomalies->count() > 0)
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Ruangan</th>
                                <th>Total</th>
                                <th>Okupansi</th>
                                <th>Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($aiRoomAnomalies as $anomaly)
                                <tr>
                                    <td class="muted" style="white-space:nowrap;">
                                        {{ \Carbon\Carbon::parse($anomaly->date)->isoFormat('D MMM YYYY') }}
                                    </td>
                                    <td class="room-name">{{ $anomaly->room_name }}</td>
                                    <td>{{ $anomaly->total_borrowing }}</td>
                                    <td>{{ number_format($anomaly->occupancy_rate * 100, 1) }}%</td>
                                    <td>
                                        <span class="badge badge-anomaly">
                                            {{ number_format($anomaly->anomaly_score, 4) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <p>Belum ada anomali ruangan yang terdeteksi</p>
                </div>
            @endif
        </div>

    </div>

    <div class="card trend-card">
        <div class="trend-card-header">
            <div>
                <div class="card-title">📈 Tren Peminjaman Ruangan</div>
                <div class="card-subtitle">Frekuensi peminjaman dalam 30 hari terakhir</div>
            </div>
        </div>

        @if($selected_room && count($chartData) > 0 && array_sum($chartData) > 0)
            <div class="chart-container">
                <canvas id="mainChart"></canvas>
            </div>
            <div class="chart-legend">
                <div class="legend-item">
                    <div class="legend-line" style="background:#2BA8A2;"></div>
                    Realisasi Peminjaman
                </div>
            </div>
        @else
            <div class="empty-state">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
                <p>{{ $selected_room ? 'Tidak ada data peminjaman dalam 30 hari terakhir' : 'Pilih ruangan untuk melihat tren peminjaman' }}</p>
            </div>
        @endif
    </div>

    <div class="charts-row-wide">

        <div class="card">
            <div class="card-title">🍩 Distribusi Penggunaan</div>
            <div class="card-subtitle">Berdasarkan Ruangan (30 Hari Terakhir)</div>

            @if($roomUsage->count() > 0)
                <div class="chart-container-xs" style="display:flex; align-items:center; justify-content:center;">
                    <canvas id="donutChart"></canvas>
                </div>

                <div class="donut-legend">
                    @foreach($roomUsage->take(5) as $i => $room)
                        @php
                            $pct = $totalRoomUsage > 0 ? round(($room['total'] / $totalRoomUsage) * 100, 1) : 0;
                            $color = $donutColors[$i % count($donutColors)];
                        @endphp

                        <div class="donut-legend-row">
                            <div class="donut-legend-left">
                                <div class="donut-dot" style="background:{{ $color }};"></div>
                                {{ \Illuminate\Support\Str::limit($room['name'], 18) }}
                            </div>
                            <div class="donut-bar-wrap">
                                <div class="donut-bar-fill" style="width:{{ $pct }}%; background:{{ $color }};"></div>
                            </div>
                            <div class="donut-pct">{{ $pct }}%</div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <p>Belum ada data peminjaman dalam 30 hari terakhir</p>
                </div>
            @endif
        </div>

        <div class="card">
            <div class="card-title">⏰ Distribusi Jam Penggunaan</div>
            <div class="card-subtitle">Pola Frekuensi Peminjaman per Jam</div>

            @if($selected_room && isset($timeSlotLabels) && count($timeSlotLabels) > 0 && array_sum($timeSlotData) > 0)
                <div class="ts-legend">
                    <div class="ts-legend-item">
                        <div class="ts-legend-dot" style="background:#FFD23F;"></div> Jam Terpadat
                    </div>
                    <div class="ts-legend-item">
                        <div class="ts-legend-dot" style="background:#2BA8A2;"></div> Jam Aktif
                    </div>
                    <div class="ts-legend-item">
                        <div class="ts-legend-dot" style="background:#CFE6E4;"></div> Jam Sepi
                    </div>
                </div>

                <div class="chart-container-sm">
                    <canvas id="timeSlotChart"></canvas>
                </div>
            @else
                <div class="empty-state">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>
                    </svg>
                    <p>{{ $selected_room ? 'Tidak ada data distribusi jam dalam 30 hari terakhir' : 'Pilih ruangan untuk melihat distribusi jam' }}</p>
                </div>
            @endif
        </div>

    </div>

    <div class="charts-row">

        <div class="card">
            <div class="card-title">📅 Kepadatan per Hari</div>
            <div class="card-subtitle">
                Distribusi Sesi dalam Seminggu
                @if($selected_room) — {{ $selected_room->name }} @endif
            </div>

            @if(array_sum($dayTotals) > 0)
                <div class="chart-container-sm">
                    <canvas id="dayChart"></canvas>
                </div>
            @else
                <div class="empty-state">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <p>Belum ada data peminjaman dalam 30 hari terakhir</p>
                </div>
            @endif
        </div>

        <div class="card" style="overflow:auto;">
            <div class="card-title">📋 Riwayat Peminjaman Terbaru</div>
            <div class="card-subtitle">
                5 Transaksi Completed Terakhir
                @if($selected_room) — {{ $selected_room->name }} @endif
            </div>

            @if($recentBorrowings->count() > 0)
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Ruangan</th>
                                <th>Peminjam</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBorrowings as $b)
                            @php
                                $sKey = strtolower($b->status ?? '');
                                $sInfo = $statusMap[$sKey] ?? ['label' => ucfirst($sKey), 'icon' => '•', 'class' => 'badge-pending'];
                            @endphp

                            <tr>
                                <td class="room-name">{{ $b->room->name ?? '-' }}</td>
                                <td class="muted">{{ $b->user->name ?? '-' }}</td>
                                <td class="muted" style="white-space:nowrap;">
                                    {{ \Carbon\Carbon::parse($b->borrow_date)->isoFormat('D MMM YYYY') }}
                                </td>
                                <td class="muted" style="white-space:nowrap; font-size:0.78rem;">
                                    {{ $b->time_slot ?? '-' }}
                                </td>
                                <td>
                                    <span class="badge {{ $sInfo['class'] }}">
                                        {{ $sInfo['icon'] }} {{ $sInfo['label'] }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                    <p>Belum ada riwayat peminjaman completed</p>
                </div>
            @endif
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    Chart.defaults.color = '#5F8481';
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size = 12;

    const gridColor = 'rgba(43,168,162,0.08)';
    const tickColor = '#7C9D9A';

    @if($selected_room && $aiRoomSlots->count() > 0)
    (function() {
        const labels = {!! json_encode($aiSlotLabels) !!};
        const data = {!! json_encode($aiSlotProbabilities) !!};

        new Chart(document.getElementById('aiSlotChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Probabilitas Terpakai (%)',
                    data: data,
                    backgroundColor: data.map(v => v >= 75 ? '#EF6C4A' : (v >= 50 ? '#2BA8A2' : '#CFE6E4')),
                    borderRadius: 6,
                    borderSkipped: false,
                    barPercentage: 0.6,
                    categoryPercentage: 0.8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1E8C86',
                        titleColor: '#FFF8E7',
                        bodyColor: '#E8F6F5',
                        borderColor: 'rgba(255,255,255,0.18)',
                        borderWidth: 1,
                        padding: 10,
                        cornerRadius: 10,
                        callbacks: { label: ctx => ` ${ctx.raw}% kemungkinan terpakai` }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: { color: gridColor },
                        ticks: { color: tickColor, callback: value => value + '%' },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 9 }, maxRotation: 0, color: tickColor },
                        border: { display: false }
                    }
                }
            }
        });
    })();
    @endif

    @if($selected_room && array_sum($chartData) > 0)
    (function() {
        const ctx = document.getElementById('mainChart').getContext('2d');
        const grad = ctx.createLinearGradient(0, 0, 0, 280);
        grad.addColorStop(0, 'rgba(43,168,162,0.20)');
        grad.addColorStop(1, 'rgba(43,168,162,0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_map(fn($d) => \Carbon\Carbon::parse($d)->isoFormat('D MMM'), $chartLabels)) !!},
                datasets: [{
                    label: 'Peminjaman',
                    data: {!! json_encode($chartData) !!},
                    borderColor: '#2BA8A2',
                    borderWidth: 2.5,
                    backgroundColor: grad,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#2BA8A2',
                    pointBorderColor: '#FFFFFF',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1E8C86',
                        titleColor: '#FFF8E7',
                        bodyColor: '#E8F6F5',
                        borderColor: 'rgba(255,255,255,0.18)',
                        borderWidth: 1,
                        padding: 10,
                        cornerRadius: 10,
                        callbacks: { label: ctx => ` ${ctx.raw} peminjaman` }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor },
                        ticks: { stepSize: 1, precision: 0, color: tickColor },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { autoSkip: true, maxTicksLimit: 12, font: { size: 10 }, color: tickColor },
                        border: { display: false }
                    }
                }
            }
        });
    })();
    @endif

    @if($roomUsage->count() > 0)
    (function() {
        const roomNames = {!! json_encode($roomUsage->take(5)->pluck('name')->values()) !!};
        const roomTotals = {!! json_encode($roomUsage->take(5)->pluck('total')->values()) !!};
        const colors = {!! json_encode(array_slice($donutColors, 0, $roomUsage->take(5)->count())) !!};

        new Chart(document.getElementById('donutChart'), {
            type: 'doughnut',
            data: {
                labels: roomNames,
                datasets: [{
                    data: roomTotals,
                    backgroundColor: colors,
                    borderColor: '#FFFFFF',
                    borderWidth: 3,
                    hoverBorderWidth: 0,
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1E8C86',
                        titleColor: '#FFF8E7',
                        bodyColor: '#E8F6F5',
                        borderColor: 'rgba(255,255,255,0.18)',
                        borderWidth: 1,
                        padding: 10,
                        cornerRadius: 10,
                        callbacks: { label: ctx => ` ${ctx.label}: ${ctx.raw} peminjaman` }
                    }
                }
            }
        });
    })();
    @endif

    @if($selected_room && isset($timeSlotLabels) && array_sum($timeSlotData) > 0)
    (function() {
        const labels = {!! json_encode($timeSlotLabels) !!};
        const data = {!! json_encode($timeSlotData) !!};
        const peakHour = {{ $timeSlotPeakHour ?? 'null' }};

        const colors = labels.map((label, i) => {
            const h = parseInt(label.split('.')[0]);
            if (peakHour !== null && h === peakHour) return '#FFD23F';
            if (data[i] > 0) return '#2BA8A2';
            return '#CFE6E4';
        });

        new Chart(document.getElementById('timeSlotChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Peminjaman',
                    data: data,
                    backgroundColor: colors,
                    borderRadius: 6,
                    borderSkipped: false,
                    barPercentage: 0.6,
                    categoryPercentage: 0.8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1E8C86',
                        titleColor: '#FFF8E7',
                        bodyColor: '#E8F6F5',
                        borderColor: 'rgba(255,255,255,0.18)',
                        borderWidth: 1,
                        padding: 10,
                        cornerRadius: 10,
                        callbacks: {
                            label: ctx => {
                                const h = parseInt(ctx.label.split('.')[0]);
                                const tag = (peakHour !== null && h === peakHour) ? ' ⭐ Jam Terpadat' : '';
                                return ` ${ctx.raw} peminjaman${tag}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor },
                        ticks: { stepSize: 1, precision: 0, color: tickColor },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 9 }, maxRotation: 0, color: tickColor },
                        border: { display: false }
                    }
                }
            }
        });
    })();
    @endif

    @if(array_sum($dayTotals) > 0)
    (function() {
        new Chart(document.getElementById('dayChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($dayLabels) !!},
                datasets: [{
                    label: 'Jumlah Peminjaman',
                    data: {!! json_encode(array_values($dayTotals)) !!},
                    backgroundColor: 'rgba(93,173,226,0.85)',
                    borderRadius: 6,
                    borderSkipped: false,
                    barPercentage: 0.55,
                    categoryPercentage: 0.8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1E8C86',
                        titleColor: '#FFF8E7',
                        bodyColor: '#E8F6F5',
                        borderColor: 'rgba(255,255,255,0.18)',
                        borderWidth: 1,
                        padding: 10,
                        cornerRadius: 10,
                        callbacks: { label: ctx => ` ${ctx.raw} peminjaman` }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor },
                        ticks: { stepSize: 1, precision: 0, color: tickColor },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: tickColor },
                        border: { display: false }
                    }
                }
            }
        });
    })();
    @endif

    (function() {
        const wrap = document.getElementById('roomDropdown');
        const menu = document.getElementById('roomDropdownMenu');
        const label = document.getElementById('roomDropdownLabel');
        const input = document.getElementById('roomIdInput');
        const form = document.getElementById('roomForm');

        if (!wrap) return;

        wrap.addEventListener('click', function(e) {
            e.stopPropagation();
            const isOpen = menu.classList.contains('open');
            menu.classList.toggle('open', !isOpen);
            wrap.classList.toggle('open', !isOpen);
        });

        document.querySelectorAll('.room-dropdown-item').forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.stopPropagation();
                input.value = this.dataset.value;
                label.textContent = this.textContent.trim();

                document.querySelectorAll('.room-dropdown-item').forEach(i => i.classList.remove('active'));
                this.classList.add('active');

                menu.classList.remove('open');
                wrap.classList.remove('open');

                form.submit();
            });
        });

        document.addEventListener('click', function() {
            menu.classList.remove('open');
            wrap.classList.remove('open');
        });
    })();
</script>
@endpush