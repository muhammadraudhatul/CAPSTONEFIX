@extends('admin.layouts.app')

@section('content')

<style>
    /* ── Flip7 muted room inventory theme: hanya desain, logika/script tetap ── */
    :root {
        --roomflip-bg: #EEF4F3;
        --roomflip-card: #FAFBFA;
        --roomflip-cream: #F7F2E4;
        --roomflip-cream-soft: #FBF8EF;
        --roomflip-teal: #4B958F;
        --roomflip-teal-dark: #356F6B;
        --roomflip-teal-soft: #E2EFED;
        --roomflip-gold: #D7BD62;
        --roomflip-gold-soft: #EEE2AE;
        --roomflip-coral: #C97A62;
        --roomflip-coral-soft: #F2DDD5;
        --roomflip-sky: #789FB5;
        --roomflip-text: #1F2A29;
        --roomflip-muted: #667675;
        --roomflip-soft: #8B9A98;
        --roomflip-border: rgba(53, 111, 107, 0.14);
        --roomflip-border-light: rgba(255, 255, 255, 0.76);
        --roomflip-shadow: 0 12px 30px rgba(31, 42, 41, 0.06);
        --roomflip-shadow-sm: 0 4px 14px rgba(31, 42, 41, 0.05);
        --roomflip-shadow-teal: 0 10px 24px rgba(75, 149, 143, 0.12);
        --roomflip-focus: 0 0 0 4px rgba(75, 149, 143, 0.12);
    }


    /* ── Page wrapper: disamakan dengan Inventory Alat dan Bahan ── */
    .roomflip-wrapper {
        min-height: calc(100vh - 1px);
        padding: 2rem 1.75rem;
        border-radius: 28px;
        position: relative;
        overflow: hidden;
        color: var(--roomflip-text);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'PingFang SC', 'Microsoft YaHei', sans-serif;
        background:
            radial-gradient(circle at 8% 8%, rgba(215, 189, 98, 0.09), transparent 25%),
            radial-gradient(circle at 88% 12%, rgba(75, 149, 143, 0.08), transparent 28%),
            linear-gradient(135deg, #F3F6F5 0%, var(--roomflip-bg) 52%, #EEF3F1 100%);
    }

    .roomflip-wrapper *,
    .roomflip-wrapper *::before,
    .roomflip-wrapper *::after {
        box-sizing: border-box;
    }

    .roomflip-wrapper::before,
    .roomflip-wrapper::after {
        content: '';
        position: absolute;
        border-radius: 28px;
        border: 1px solid rgba(53, 111, 107, 0.05);
        background: rgba(247, 243, 232, 0.20);
        pointer-events: none;
        z-index: 0;
    }

    .roomflip-wrapper::before {
        width: 150px;
        height: 210px;
        right: -74px;
        top: 110px;
        transform: rotate(-8deg);
    }

    .roomflip-wrapper::after {
        width: 108px;
        height: 146px;
        left: -52px;
        bottom: 138px;
        transform: rotate(10deg);
    }

    .roomflip-main,
    .room-page-header,
    .table-card,
    .section-header,
    .room-accordion {
        position: relative;
        z-index: 1;
    }

    /* ── Header ── */
    .page-title {
        font-size: clamp(1.9rem, 3vw, 2.55rem);
        font-weight: 900;
        color: var(--roomflip-text);
        letter-spacing: -0.045em;
        margin: 0 0 0.45rem;
        line-height: 1.05;
        text-shadow: 2px 2px 0 rgba(215, 189, 98, 0.20);
    }

    .page-sub {
        color: #4C8A85;
        font-size: 0.95rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        margin: 0;
    }

    .btn-add {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        min-height: 46px;
        padding: 0.78rem 1.45rem;
        background: linear-gradient(135deg, #E6D58C, var(--roomflip-gold));
        border: 1px solid rgba(255,255,255,0.75);
        border-radius: 999px;
        color: #44370F;
        font-size: 0.93rem;
        font-weight: 900;
        text-decoration: none;
        white-space: nowrap;
        box-shadow: 0 8px 20px rgba(215, 189, 98, 0.14);
        transition: transform 0.2s cubic-bezier(.2,.8,.2,1), box-shadow 0.2s, filter 0.2s;
        cursor: pointer;
        font-family: inherit;
    }

    .btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(215, 189, 98, 0.18);
        filter: saturate(0.98);
    }

    .btn-add:active { transform: scale(0.97); }

    /* ── Table card ── */
    .table-card {
        background: linear-gradient(180deg, rgba(250,251,250,0.98), rgba(247,242,228,0.88));
        border: 1px solid var(--roomflip-border-light);
        border-left: 7px solid var(--roomflip-teal);
        border-radius: 26px;
        overflow-x: auto;
        margin-top: 2rem;
        width: 100%;
        box-shadow: var(--roomflip-shadow);
        position: relative;
    }

    .table-card::before {
        content: '';
        display: block;
        height: 8px;
        background: linear-gradient(90deg, rgba(215,189,98,0.92), rgba(75,149,143,0.90), rgba(201,122,98,0.72));
    }

    .data-table {
        width: 100%;
        min-width: 760px;
        border-collapse: collapse;
    }

    .data-table thead tr {
        background: rgba(226, 239, 237, 0.86);
    }

    .data-table thead th {
        padding: 0.92rem 1.25rem;
        text-align: left;
        font-size: 0.72rem;
        font-weight: 900;
        color: #4A8C86;
        text-transform: uppercase;
        letter-spacing: 0.10em;
        border-bottom: 2px solid rgba(75,149,143,0.10);
        white-space: nowrap;
    }

    .data-table tbody tr {
        border-bottom: 1px solid rgba(75,149,143,0.08);
        transition: background 0.16s ease;
    }

    .data-table tbody tr:last-child { border-bottom: none; }
    .data-table tbody tr:hover { background: rgba(247,242,228,0.55); }

    .data-table td {
        padding: 1rem 1.25rem;
        color: var(--roomflip-muted);
        font-size: 0.9rem;
        vertical-align: middle;
        background: transparent;
    }

    .room-name-cell {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        color: var(--roomflip-text);
        font-weight: 850;
        font-size: 0.95rem;
    }

    .room-name-cell i {
        color: var(--roomflip-teal);
        font-size: 1.1rem;
    }

    /* action icon buttons */
    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 12px;
        border: 1px solid rgba(255,255,255,0.66);
        cursor: pointer;
        transition: transform 0.18s ease, background 0.18s, color 0.18s, box-shadow 0.18s;
        text-decoration: none;
        box-shadow: var(--roomflip-shadow-sm);
    }

    .btn-icon-edit {
        background: rgba(120,159,181,0.14);
        color: #5E8397;
    }

    .btn-icon-edit:hover {
        background: rgba(120,159,181,0.22);
        transform: translateY(-1px);
    }

    .btn-icon-delete {
        background: rgba(201,122,98,0.13);
        color: var(--roomflip-coral);
    }

    .btn-icon-delete:hover {
        background: rgba(201,122,98,0.21);
        transform: translateY(-1px);
    }

    .btn-icon svg { width: 15px; height: 15px; }

    .empty-cell {
        text-align: center;
        padding: 3rem !important;
        color: var(--roomflip-soft) !important;
        font-size: 0.9rem;
        font-weight: 750;
        background: rgba(247,242,228,0.46);
    }

    /* ── Schedule section ── */
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-top: 3rem;
        margin-bottom: 1.5rem;
        gap: 1rem;
        flex-wrap: wrap;
        padding-bottom: 1rem;
        border-bottom: 2px dashed rgba(75,149,143,0.18);
    }

    .section-title-wrap {
        display: flex;
        align-items: center;
        gap: 0.8rem;
    }

    .section-icon {
        width: 44px;
        height: 44px;
        border-radius: 16px;
        color: var(--roomflip-teal-dark);
        background: rgba(75,149,143,0.10);
        border: 1px solid rgba(75,149,143,0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: var(--roomflip-shadow-sm);
        flex-shrink: 0;
    }

    .section-title {
        font-size: clamp(1.35rem, 2vw, 1.65rem);
        font-weight: 900;
        color: var(--roomflip-text);
        letter-spacing: -0.035em;
        margin: 0 0 0.25rem;
        line-height: 1.1;
    }

    .section-sub {
        font-size: 0.85rem;
        color: var(--roomflip-muted);
        font-weight: 650;
        margin: 0;
    }

    /* ── week select ── */
    .week-select-wrap {
        position: relative;
        display: inline-flex;
        align-items: center;
        min-width: 220px;
    }

    .week-select-wrap select {
        appearance: none;
        -webkit-appearance: none;
        background: linear-gradient(135deg, var(--roomflip-cream-soft), var(--roomflip-card));
        border: 1px solid rgba(75,149,143,0.16);
        border-radius: 999px;
        padding: 0.68rem 2.75rem 0.68rem 1rem;
        color: var(--roomflip-text);
        font-size: 0.88rem;
        font-weight: 800;
        font-family: inherit;
        cursor: pointer;
        outline: none;
        box-shadow: var(--roomflip-shadow-sm);
        transition: border-color 0.18s, box-shadow 0.18s;
        width: 100%;
    }

    .week-select-wrap select:focus {
        border-color: rgba(75,149,143,0.38);
        box-shadow: var(--roomflip-focus);
    }

    .week-select-wrap select option {
        background: var(--roomflip-card);
        color: var(--roomflip-text);
    }

    .week-select-wrap .select-arrow {
        pointer-events: none;
        position: absolute;
        right: 0.95rem;
        top: 50%;
        transform: translateY(-50%);
        display: flex;
        align-items: center;
        color: var(--roomflip-teal-dark);
        line-height: 0;
    }

    .week-select-wrap .select-arrow svg { width: 14px; height: 14px; }

    /* ── Room accordion ── */
    .room-accordion {
        background: linear-gradient(180deg, rgba(250,251,250,0.98), rgba(247,242,228,0.82));
        border: 1px solid var(--roomflip-border-light);
        border-left: 6px solid rgba(75,149,143,0.78);
        border-radius: 22px;
        overflow: hidden;
        margin-bottom: 0.85rem;
        width: 100%;
        box-shadow: 0 8px 24px rgba(31, 42, 41, 0.045);
    }

    .room-accordion-btn {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.08rem 1.4rem;
        background: transparent;
        border: none;
        cursor: pointer;
        transition: background 0.15s;
    }

    .room-accordion-btn:hover { background: rgba(226,239,237,0.44); }

    .room-accordion-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-width: 0;
    }

    .room-accordion-left i {
        color: var(--roomflip-teal);
        font-size: 1.15rem;
    }

    .room-accordion-name {
        font-size: 1rem;
        font-weight: 900;
        color: var(--roomflip-text);
        margin: 0;
    }

    .room-accordion-cap {
        font-size: 0.82rem;
        color: var(--roomflip-muted);
        margin-left: 0.5rem;
        font-weight: 700;
    }

    .room-accordion-chevron {
        color: var(--roomflip-teal-dark);
        transition: transform 0.2s;
        display: flex;
        flex-shrink: 0;
    }

    .room-accordion-chevron svg { width: 18px; height: 18px; }

    /* schedule table inside accordion */
    .schedule-wrap {
        padding: 0 1.25rem 1.25rem;
    }

    .schedule-table-container {
        overflow-x: auto;
        border-radius: 18px;
        border: 1px solid rgba(75,149,143,0.12);
        background: rgba(250,251,250,0.72);
    }

    .schedule-table {
        width: 100%;
        min-width: 820px;
        border-collapse: collapse;
    }

    .schedule-table thead tr {
        background: rgba(226,239,237,0.92);
    }

    .schedule-table thead th {
        padding: 0.76rem 1rem;
        font-size: 0.78rem;
        font-weight: 900;
        color: #4A8C86;
        text-align: center;
        border-bottom: 1px solid rgba(75,149,143,0.12);
        letter-spacing: 0.04em;
    }

    .schedule-table thead th:first-child { text-align: left; }

    .schedule-table tbody tr {
        border-top: 1px solid rgba(75,149,143,0.07);
        transition: background 0.12s;
    }

    .schedule-table tbody tr:hover { background: rgba(247,242,228,0.54); }

    .schedule-table td {
        padding: 0.64rem 0.75rem;
        text-align: center;
        vertical-align: middle;
    }

    .schedule-table td:first-child {
        text-align: left;
        font-size: 0.82rem;
        font-weight: 850;
        color: var(--roomflip-muted);
        white-space: nowrap;
        padding-left: 1rem;
    }

    /* ── tombol status — ukuran konsisten ── */
    .btn-schedule {
        width: 100%;
        min-width: 110px;
        min-height: 36px;
        padding: 0.45rem 0.5rem;
        border-radius: 999px;
        border: 1px solid rgba(255,255,255,0.65);
        font-size: 0.76rem;
        font-weight: 900;
        cursor: pointer;
        white-space: nowrap;
        transition: transform 0.15s, background 0.15s, color 0.15s, opacity 0.15s;
        font-family: inherit;
        text-align: center;
        box-shadow: var(--roomflip-shadow-sm);
    }

    .btn-schedule.tersedia {
        background: rgba(75, 149, 143, 0.22);
        color: #2F6F6A;
        border-color: rgba(75, 149, 143, 0.35);
    }

    .btn-schedule.tersedia:hover { background: rgba(75, 149, 143, 0.32); }

    .btn-schedule.tidak {
        background: rgba(201, 122, 98, 0.24);
        color: #9A4F3D;
        border-color: rgba(201, 122, 98, 0.38);
    }

    .btn-schedule.tidak:hover { background: rgba(201, 122, 98, 0.34); }

    .btn-schedule:hover { transform: translateY(-1px); }
    .btn-schedule:disabled { opacity: 0.65; cursor: not-allowed; transform: none; }

    .schedule-hint {
        font-size: 0.75rem;
        color: var(--roomflip-soft);
        font-weight: 700;
        margin-top: 0.65rem;
    }

    /* ══════════════════════════════
       MODAL — Tambah / Edit Ruangan
    ══════════════════════════════ */

    .modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 50;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        background: rgba(31, 42, 41, 0.42);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.25s ease;
    }

    .modal-overlay.open {
        opacity: 1;
        pointer-events: all;
    }

    .modal-card {
        background: linear-gradient(180deg, var(--roomflip-card), var(--roomflip-cream-soft));
        border: 1px solid rgba(255,255,255,0.78);
        border-left: 7px solid var(--roomflip-teal);
        border-radius: 26px;
        width: 100%;
        max-width: 460px;
        box-shadow: 0 24px 70px rgba(31,42,41,0.22);
        transform: translateY(18px) scale(0.97);
        transition: transform 0.28s cubic-bezier(0.34,1.56,0.64,1), opacity 0.25s ease;
        opacity: 0;
        overflow: hidden;
    }

    .modal-overlay.open .modal-card {
        transform: translateY(0) scale(1);
        opacity: 1;
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.4rem 1.6rem 1.1rem;
        border-bottom: 1px solid rgba(75,149,143,0.10);
        background: rgba(226,239,237,0.56);
    }

    .modal-header-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .modal-icon {
        width: 42px;
        height: 42px;
        border-radius: 16px;
        background: rgba(75,149,143,0.11);
        border: 1px solid rgba(75,149,143,0.14);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .modal-icon i {
        color: var(--roomflip-teal-dark);
        font-size: 1.15rem;
    }

    .modal-title {
        font-size: 1.05rem;
        font-weight: 900;
        color: var(--roomflip-text);
        margin: 0 0 0.15rem;
        letter-spacing: -0.02em;
    }

    .modal-subtitle {
        font-size: 0.78rem;
        color: var(--roomflip-muted);
        font-weight: 650;
        margin: 0;
    }

    .modal-close {
        width: 34px;
        height: 34px;
        border-radius: 12px;
        border: 1px solid rgba(75,149,143,0.10);
        background: rgba(250,251,250,0.78);
        color: var(--roomflip-muted);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.15s, color 0.15s, transform 0.15s;
        font-family: inherit;
    }

    .modal-close:hover {
        background: rgba(201,122,98,0.12);
        color: var(--roomflip-coral-dark);
        transform: translateY(-1px);
    }

    .modal-close svg { width: 16px; height: 16px; }

    .modal-body {
        padding: 1.4rem 1.6rem;
        display: flex;
        flex-direction: column;
        gap: 1.1rem;
    }

    .field-group {
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
    }

    .field-label {
        font-size: 0.82rem;
        font-weight: 850;
        color: var(--roomflip-muted);
        letter-spacing: 0.01em;
    }

    .field-input {
        background: var(--roomflip-cream-soft);
        border: 1px solid rgba(75,149,143,0.14);
        border-radius: 16px;
        padding: 0.78rem 1rem;
        color: var(--roomflip-text);
        font-size: 0.92rem;
        font-weight: 650;
        font-family: inherit;
        outline: none;
        width: 100%;
        transition: border-color 0.18s, background 0.18s, box-shadow 0.18s;
    }

    .field-input::placeholder {
        color: rgba(102,118,117,0.46);
    }

    .field-input:focus {
        border-color: rgba(75,149,143,0.36);
        background: #FFFFFF;
        box-shadow: var(--roomflip-focus);
    }

    .field-error {
        font-size: 0.78rem;
        color: var(--roomflip-coral-dark);
        font-weight: 700;
        margin: 0;
    }

    .modal-footer {
        display: flex;
        gap: 0.65rem;
        padding: 1rem 1.6rem 1.4rem;
        justify-content: flex-end;
    }

    .btn-cancel {
        min-height: 42px;
        padding: 0.72rem 1.25rem;
        border-radius: 999px;
        border: 1px solid rgba(75,149,143,0.14);
        background: rgba(250,251,250,0.72);
        color: var(--roomflip-muted);
        font-size: 0.88rem;
        font-weight: 850;
        cursor: pointer;
        font-family: inherit;
        transition: background 0.15s, color 0.15s, border-color 0.15s, transform 0.15s;
    }

    .btn-cancel:hover {
        background: rgba(226,239,237,0.72);
        color: var(--roomflip-text);
        border-color: rgba(75,149,143,0.20);
        transform: translateY(-1px);
    }

    .btn-submit {
        min-height: 42px;
        padding: 0.72rem 1.45rem;
        border-radius: 999px;
        border: 1px solid rgba(255,255,255,0.70);
        background: linear-gradient(135deg, #7DB4AF, var(--roomflip-teal));
        color: #FFFFFF;
        font-size: 0.88rem;
        font-weight: 900;
        cursor: pointer;
        font-family: inherit;
        box-shadow: var(--roomflip-shadow-teal);
        transition: transform 0.15s, filter 0.18s, box-shadow 0.18s;
    }

    .btn-submit:hover {
        transform: translateY(-1px);
        filter: saturate(0.96);
        box-shadow: 0 12px 26px rgba(75,149,143,0.14);
    }

    .btn-submit:active {
        transform: scale(0.97);
    }

    .room-page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        flex-wrap: wrap;
        padding: 1.65rem 1.8rem;
        border-radius: 30px;
        background: linear-gradient(135deg, rgba(247, 243, 232, 0.96), rgba(250, 251, 250, 0.95));
        border: 1px solid rgba(255, 255, 255, 0.80);
        box-shadow: var(--roomflip-shadow);
        overflow: hidden;
        position: relative;
    }

    .room-page-header::before {
        content: '';
        position: absolute;
        inset: 0 auto 0 0;
        width: 6px;
        background: linear-gradient(180deg, rgba(215, 189, 98, 0.85), rgba(75, 149, 143, 0.85), rgba(201, 122, 98, 0.78));
    }

    .room-page-header::after {
        content: 'ROOMS';
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

    .room-page-header > div,
    .room-page-header .btn-add {
        position: relative;
        z-index: 1;
    }

    @keyframes roomFadeUp {
        from {
            opacity: 0;
            transform: translateY(12px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .room-page-header,
    .table-card,
    .section-header,
    .room-accordion {
        animation: roomFadeUp 0.35s ease both;
    }

    .table-card {
        animation-delay: 0.08s;
    }

    .section-header {
        animation-delay: 0.12s;
    }

    .room-accordion {
        animation-delay: 0.16s;
    }

    @media (max-width: 760px) {
        .roomflip-wrapper {
            padding: 1.25rem 1rem;
            border-radius: 22px;
        }

        .room-page-header {
            padding: 1.35rem 1.25rem;
            border-radius: 24px;
        }

        .btn-add,
        .week-select-wrap,
        .week-select-wrap select {
            width: 100%;
        }

        .section-title-wrap {
            align-items: flex-start;
        }

        .room-accordion-btn {
            align-items: flex-start;
            gap: 1rem;
        }

        .room-accordion-left {
            flex-wrap: wrap;
        }

        .room-accordion-cap {
            margin-left: 0;
            width: 100%;
        }

        .modal-footer {
            flex-direction: column-reverse;
        }

        .btn-cancel,
        .btn-submit {
            width: 100%;
        }
    }
</style>

<div class="roomflip-wrapper">
    <div class="roomflip-main">

<!-- ══ PAGE HEADER ══ -->
<div class="room-page-header">
    <div>
        <h1 class="page-title">Inventory Ruangan</h1>
        <p class="page-sub">Kelola ruangan laboratorium dan jadwal ketersediaannya</p>
    </div>
    {{-- Tombol sekarang buka modal, bukan pindah halaman --}}
    <button type="button" class="btn-add" onclick="openModal()">
        + Tambah Ruangan
    </button>
</div>

<!-- ══ ROOMS TABLE ══ -->
<div class="table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Ruangan</th>
                <th>Kapasitas</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rooms as $room)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    <div class="room-name-cell">
                        <i class="ti ti-building"></i>
                        {{ $room->name }}
                    </div>
                </td>
                <td>{{ $room->capacity }}</td>
                <td>
                    <div style="display:flex; gap:0.4rem; align-items:center;">
                        <button
                            type="button"
                            class="btn-icon btn-icon-edit"
                            onclick="openEditModal({{ $room->id }}, '{{ addslashes($room->name) }}', {{ $room->capacity }})">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </button>
                        <form method="POST" action="{{ route('rooms.destroy', $room) }}" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                onclick="return confirm('Hapus ruangan ini?')"
                                class="btn-icon btn-icon-delete">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                    <path d="M10 11v6M14 11v6"/>
                                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="empty-cell">Belum ada ruangan</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- ══ SCHEDULE SECTION ══ -->
<div style="margin-top:3rem;">

    <div class="section-header">
        <div class="section-title-wrap">
            <span class="section-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </span>
            <div>
                <h2 class="section-title">Jadwal Ketersediaan Ruangan</h2>
                <p class="section-sub">Klik pada ruangan untuk melihat jadwal ketersediaan</p>
            </div>
        </div>

        <!-- Week dropdown -->
        <form method="GET">
            <div class="week-select-wrap">
                <select name="week" onchange="saveOpenRoomsAndSubmit(this)">
                    @foreach($weeks as $week)
                    <option value="{{ $week['value'] }}" {{ $selectedWeek == $week['value'] ? 'selected' : '' }}>
                        {{ $week['label'] }}
                    </option>
                    @endforeach
                </select>
                <span class="select-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </span>
            </div>
        </form>
    </div>

    <!-- Each room accordion -->
    @foreach($rooms as $room)
    <div class="room-accordion">

        <button
            type="button"
            onclick="toggleRoom({{ $room->id }})"
            class="room-accordion-btn"
        >
            <div class="room-accordion-left">
                <i class="ti ti-building"></i>
                <span class="room-accordion-name">{{ $room->name }}</span>
                <span class="room-accordion-cap">Kapasitas: {{ $room->capacity }}</span>
            </div>
            <span class="room-accordion-chevron" id="icon-{{ $room->id }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </span>
        </button>

        <div id="room-{{ $room->id }}" class="hidden">
            <div class="schedule-wrap">
                <div class="schedule-table-container">
                    <table class="schedule-table">
                        <thead>
                            <tr>
                                <th>Jam</th>
                                @foreach($days as $day)
                                <th>{{ $day }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($timeSlots as $slot)
                            <tr>
                                <td>{{ $slot }}</td>
                                @foreach($days as $day)
                                @php
                                $scheduleKey = $room->id . '|' . $day . '|' . $slot;
                                $schedule = $scheduleMap->get($scheduleKey);
                                @endphp
                                <td>
                                    @if($schedule)
                                    <form method="POST" action="{{ route('room-schedules.toggle', $schedule) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            onclick="toggleSchedule(this, {{ $schedule->id }})"
                                            class="btn-schedule {{ $schedule->available ? 'tersedia' : 'tidak' }}"
                                        >
                                            {{ $schedule->available ? 'Tersedia' : 'Tidak Tersedia' }}
                                        </button>
                                    </form>
                                    @endif
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="schedule-hint">* Klik status untuk mengubah ketersediaan</p>
            </div>
        </div>

    </div>
    @endforeach

</div>


    </div>
</div>

{{-- ══════════════════════════════
     MODAL — Edit Ruangan
══════════════════════════════ --}}
<div id="modal-edit" class="modal-overlay" onclick="handleEditOverlayClick(event)">
    <div class="modal-card">

        <!-- Header -->
        <div class="modal-header">
            <div class="modal-header-left">
                <div class="modal-icon">
                    <i class="ti ti-building-cog"></i>
                </div>
                <div>
                    <p class="modal-title">Edit Ruangan</p>
                    <p class="modal-subtitle">Ubah detail ruangan laboratorium</p>
                </div>
            </div>
            <button type="button" class="modal-close" onclick="closeEditModal()">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <!-- Form — action di-set dinamis via JS -->
        <form id="form-edit" method="POST" action="">
            @csrf
            @method('PUT')

            <div class="modal-body">

                <!-- Nama Ruangan -->
                <div class="field-group">
                    <label class="field-label" for="edit-room-name">Nama Ruangan</label>
                    <input
                        id="edit-room-name"
                        type="text"
                        name="name"
                        class="field-input"
                        placeholder="Contoh: Lab Kimia A"
                        autocomplete="off"
                        required
                    >
                    @error('name')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kapasitas -->
                <div class="field-group">
                    <label class="field-label" for="edit-room-capacity">Kapasitas</label>
                    <input
                        id="edit-room-capacity"
                        type="number"
                        name="capacity"
                        class="field-input"
                        placeholder="Contoh: 30"
                        min="1"
                        required
                    >
                    @error('capacity')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="btn-submit">Simpan Perubahan</button>
            </div>

        </form>
    </div>
</div>


<div id="modal-tambah" class="modal-overlay" onclick="handleOverlayClick(event)">
    <div class="modal-card">

        <!-- Header -->
        <div class="modal-header">
            <div class="modal-header-left">
                <div class="modal-icon">
                    <i class="ti ti-building-plus"></i>
                </div>
                <div>
                    <p class="modal-title">Tambah Ruangan</p>
                    <p class="modal-subtitle">Isi detail ruangan laboratorium baru</p>
                </div>
            </div>
            <button type="button" class="modal-close" onclick="closeModal()">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('rooms.store') }}">
            @csrf

            <div class="modal-body">

                <!-- Nama Ruangan -->
                <div class="field-group">
                    <label class="field-label" for="room-name">Nama Ruangan</label>
                    <input
                        id="room-name"
                        type="text"
                        name="name"
                        class="field-input"
                        placeholder="Contoh: Lab Kimia A"
                        value="{{ old('name') }}"
                        autocomplete="off"
                        required
                    >
                    @error('name')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kapasitas -->
                <div class="field-group">
                    <label class="field-label" for="room-capacity">Kapasitas</label>
                    <input
                        id="room-capacity"
                        type="number"
                        name="capacity"
                        class="field-input"
                        placeholder="Contoh: 30"
                        value="{{ old('capacity') }}"
                        min="1"
                        required
                    >
                    @error('capacity')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn-submit">Simpan Ruangan</button>
            </div>

        </form>
    </div>
</div>


<script>
/* ── Modal ── */
function openModal() {
    const overlay = document.getElementById('modal-tambah');
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden'; // cegah scroll background
    // fokus ke input pertama setelah animasi
    setTimeout(() => {
        document.getElementById('room-name').focus();
    }, 150);
}

function closeModal() {
    const overlay = document.getElementById('modal-tambah');
    overlay.classList.remove('open');
    document.body.style.overflow = '';
}

// Klik di luar kartu modal → tutup
function handleOverlayClick(e) {
    if (e.target === document.getElementById('modal-tambah')) {
        closeModal();
    }
}

// Tekan Escape → tutup modal manapun yang terbuka
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeModal();
        closeEditModal();
    }
});

// Buka otomatis jika ada validation error dari form tambah
@if($errors->any() && old('_method') !== 'PUT')
document.addEventListener('DOMContentLoaded', () => openModal());
@endif

/* ── Modal Edit ── */
function openEditModal(id, name, capacity) {
    // Set action form ke route update room yang sesuai id
    document.getElementById('form-edit').action = `/admin/rooms/${id}`;

    // Populate field dengan data room yang diklik
    document.getElementById('edit-room-name').value = name;
    document.getElementById('edit-room-capacity').value = capacity;

    const overlay = document.getElementById('modal-edit');
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';

    setTimeout(() => {
        const input = document.getElementById('edit-room-name');
        input.focus();
        input.select(); // seleksi semua teks agar langsung bisa diketik
    }, 150);
}

function closeEditModal() {
    document.getElementById('modal-edit').classList.remove('open');
    document.body.style.overflow = '';
}

function handleEditOverlayClick(e) {
    if (e.target === document.getElementById('modal-edit')) {
        closeEditModal();
    }
}


/* ── Accordion & Schedule ── */
function toggleRoom(id) {
    const room = document.getElementById('room-' + id);
    const icon = document.getElementById('icon-' + id);
    room.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
}

async function toggleSchedule(btn, scheduleId) {
    btn.disabled = true;

    const response = await fetch(`/admin/room-schedules/${scheduleId}/toggle`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    });

    const data = await response.json();
    const isAvailable = data.available;

    btn.textContent = isAvailable ? 'Tersedia' : 'Tidak Tersedia';
    btn.className = 'btn-schedule ' + (isAvailable ? 'tersedia' : 'tidak');

    btn.disabled = false;
}

function saveOpenRoomsAndSubmit(select) {
    const openRooms = [];
    document.querySelectorAll('[id^="room-"]').forEach(el => {
        if (!el.classList.contains('hidden')) {
            openRooms.push(el.id.replace('room-', ''));
        }
    });
    sessionStorage.setItem('openRooms', JSON.stringify(openRooms));
    sessionStorage.setItem('scrollY', window.scrollY);
    select.form.submit();
}

document.addEventListener('DOMContentLoaded', () => {
    const openRooms = JSON.parse(sessionStorage.getItem('openRooms') || '[]');
    openRooms.forEach(id => {
        const room = document.getElementById('room-' + id);
        const icon = document.getElementById('icon-' + id);
        if (room) room.classList.remove('hidden');
        if (icon) icon.classList.add('rotate-180');
    });
    sessionStorage.removeItem('openRooms');

    const scrollY = sessionStorage.getItem('scrollY');
    if (scrollY !== null) {
        window.scrollTo(0, parseInt(scrollY));
        sessionStorage.removeItem('scrollY');
    }
});
</script>

@endsection