@extends('admin.layouts.app')

@section('content')

<style>
    /* ── Header ── */
    .page-title {
        font-size: 2.25rem;
        font-weight: 800;
        color: #fff;
        letter-spacing: -0.02em;
        margin: 0 0 0.35rem;
    }

    .page-sub {
        color: rgba(255,255,255,0.4);
        font-size: 0.95rem;
        margin: 0;
    }

    .btn-add {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.85rem 1.5rem;
        background: linear-gradient(135deg, #a855f7, #ec4899);
        border: none;
        border-radius: 0.85rem;
        color: #fff;
        font-size: 0.95rem;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
        box-shadow: 0 6px 20px rgba(168,85,247,0.35);
        transition: opacity 0.18s, transform 0.15s;
        cursor: pointer;
        font-family: inherit;
    }

    .btn-add:hover { opacity: 0.88; transform: translateY(-1px); }

    /* ── Table card ── */
    .table-card {
        background: #1c2544;
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 1.1rem;
        overflow: hidden;
        margin-top: 2rem;
        width: 100%;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table thead tr {
        background: rgba(255,255,255,0.04);
    }

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

    .room-name-cell {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        color: #fff;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .room-name-cell i {
        color: #a855f7;
        font-size: 1.1rem;
    }

    /* action icon buttons */
    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 0.55rem;
        border: none;
        cursor: pointer;
        transition: background 0.15s, color 0.15s;
        text-decoration: none;
    }

    .btn-icon-edit {
        background: rgba(96,165,250,0.12);
        color: #60a5fa;
    }

    .btn-icon-edit:hover { background: rgba(96,165,250,0.25); }

    .btn-icon-delete {
        background: rgba(248,113,113,0.12);
        color: #f87171;
    }

    .btn-icon-delete:hover { background: rgba(248,113,113,0.25); }

    .btn-icon svg { width: 15px; height: 15px; }

    .empty-cell {
        text-align: center;
        padding: 3rem !important;
        color: rgba(255,255,255,0.2) !important;
        font-size: 0.9rem;
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
    }

    .section-title-wrap {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .section-icon {
        color: #a855f7;
        display: flex;
        align-items: center;
    }

    .section-title {
        font-size: 1.6rem;
        font-weight: 800;
        color: #fff;
        letter-spacing: -0.02em;
        margin: 0 0 0.2rem;
    }

    .section-sub {
        font-size: 0.85rem;
        color: rgba(255,255,255,0.35);
        margin: 0;
    }

    /* ── week select ── */
    .week-select-wrap {
        position: relative;
        display: inline-flex;
        align-items: center;
    }

    .week-select-wrap select {
        appearance: none;
        -webkit-appearance: none;
        background: #1c2544;
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 0.75rem;
        padding: 0.65rem 2.75rem 0.65rem 1rem;
        color: #fff;
        font-size: 0.88rem;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        outline: none;
        box-shadow: none;
        transition: border-color 0.18s;
        width: 100%;
    }

    .week-select-wrap select:focus {
        border-color: rgba(168,85,247,0.5);
    }

    .week-select-wrap select option {
        background: #1c2544;
        color: #fff;
    }

    .week-select-wrap .select-arrow {
        pointer-events: none;
        position: absolute;
        right: 0.85rem;
        top: 50%;
        transform: translateY(-50%);
        display: flex;
        align-items: center;
        color: rgba(255,255,255,0.35);
        line-height: 0;
    }

    .week-select-wrap .select-arrow svg { width: 14px; height: 14px; }

    /* ── Room accordion ── */
    .room-accordion {
        background: #1c2544;
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 1rem;
        overflow: hidden;
        margin-bottom: 0.75rem;
        width: 100%;
    }

    .room-accordion-btn {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.1rem 1.5rem;
        background: none;
        border: none;
        cursor: pointer;
        transition: background 0.15s;
    }

    .room-accordion-btn:hover { background: rgba(255,255,255,0.03); }

    .room-accordion-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .room-accordion-left i {
        color: #a855f7;
        font-size: 1.15rem;
    }

    .room-accordion-name {
        font-size: 1rem;
        font-weight: 700;
        color: #fff;
        margin: 0;
    }

    .room-accordion-cap {
        font-size: 0.82rem;
        color: rgba(255,255,255,0.35);
        margin-left: 0.5rem;
    }

    .room-accordion-chevron {
        color: rgba(255,255,255,0.35);
        transition: transform 0.2s;
        display: flex;
    }

    .room-accordion-chevron svg { width: 18px; height: 18px; }

    /* schedule table inside accordion */
    .schedule-wrap {
        padding: 0 1.25rem 1.25rem;
    }

    .schedule-table-container {
        overflow-x: auto;
        border-radius: 0.75rem;
        border: 1px solid rgba(255,255,255,0.07);
    }

    .schedule-table {
        width: 100%;
        border-collapse: collapse;
    }

    .schedule-table thead tr {
        background: rgba(139,92,246,0.2);
    }

    .schedule-table thead th {
        padding: 0.75rem 1rem;
        font-size: 0.82rem;
        font-weight: 600;
        color: #c4b5fd;
        text-align: center;
        border-bottom: 1px solid rgba(255,255,255,0.07);
    }

    .schedule-table thead th:first-child { text-align: left; }

    .schedule-table tbody tr {
        border-top: 1px solid rgba(255,255,255,0.04);
        transition: background 0.12s;
    }

    .schedule-table tbody tr:hover { background: rgba(255,255,255,0.02); }

    .schedule-table td {
        padding: 0.6rem 0.75rem;
        text-align: center;
        vertical-align: middle;
    }

    .schedule-table td:first-child {
        text-align: left;
        font-size: 0.82rem;
        font-weight: 600;
        color: rgba(255,255,255,0.6);
        white-space: nowrap;
        padding-left: 1rem;
    }

    /* ── tombol status — ukuran konsisten ── */
    .btn-schedule {
        width: 100%;
        min-width: 110px;
        padding: 0.45rem 0.5rem;
        border-radius: 0.5rem;
        border: none;
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        transition: background 0.15s, color 0.15s;
        font-family: inherit;
        text-align: center;
    }

    .btn-schedule.tersedia {
        background: rgba(52,211,153,0.15);
        color: #6ee7b7;
    }

    .btn-schedule.tersedia:hover { background: rgba(52,211,153,0.25); }

    .btn-schedule.tidak {
        background: rgba(248,113,113,0.15);
        color: #fca5a5;
    }

    .btn-schedule.tidak:hover { background: rgba(248,113,113,0.25); }

    .schedule-hint {
        font-size: 0.75rem;
        color: rgba(255,255,255,0.2);
        margin-top: 0.6rem;
    }

    /* ══════════════════════════════
       MODAL — Tambah Ruangan
    ══════════════════════════════ */

    /* Overlay: backdrop blur + gelap */
    .modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 50;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;

        /* blur konten di belakang */
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        background: rgba(9, 13, 32, 0.65);

        /* animasi masuk */
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.25s ease;
    }

    .modal-overlay.open {
        opacity: 1;
        pointer-events: all;
    }

    /* Kartu modal */
    .modal-card {
        background: #1a2340;
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 1.25rem;
        width: 100%;
        max-width: 460px;
        box-shadow: 0 25px 60px rgba(0,0,0,0.5), 0 0 0 1px rgba(168,85,247,0.15);
        transform: translateY(18px) scale(0.97);
        transition: transform 0.28s cubic-bezier(0.34,1.56,0.64,1), opacity 0.25s ease;
        opacity: 0;
    }

    .modal-overlay.open .modal-card {
        transform: translateY(0) scale(1);
        opacity: 1;
    }

    /* Header modal */
    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.4rem 1.6rem 1.1rem;
        border-bottom: 1px solid rgba(255,255,255,0.07);
    }

    .modal-header-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .modal-icon {
        width: 40px;
        height: 40px;
        border-radius: 0.75rem;
        background: linear-gradient(135deg, rgba(168,85,247,0.25), rgba(236,72,153,0.2));
        border: 1px solid rgba(168,85,247,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .modal-icon i {
        color: #c084fc;
        font-size: 1.15rem;
    }

    .modal-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #fff;
        margin: 0 0 0.15rem;
        letter-spacing: -0.01em;
    }

    .modal-subtitle {
        font-size: 0.78rem;
        color: rgba(255,255,255,0.35);
        margin: 0;
    }

    /* Close button */
    .modal-close {
        width: 32px;
        height: 32px;
        border-radius: 0.6rem;
        border: none;
        background: rgba(255,255,255,0.06);
        color: rgba(255,255,255,0.4);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.15s, color 0.15s;
        font-family: inherit;
    }

    .modal-close:hover {
        background: rgba(255,255,255,0.12);
        color: rgba(255,255,255,0.8);
    }

    .modal-close svg { width: 16px; height: 16px; }

    /* Body modal */
    .modal-body {
        padding: 1.4rem 1.6rem;
        display: flex;
        flex-direction: column;
        gap: 1.1rem;
    }

    /* Field grup */
    .field-group {
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
    }

    .field-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: rgba(255,255,255,0.6);
        letter-spacing: 0.01em;
    }

    .field-input {
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        color: #fff;
        font-size: 0.92rem;
        font-family: inherit;
        outline: none;
        width: 100%;
        transition: border-color 0.18s, background 0.18s, box-shadow 0.18s;
    }

    .field-input::placeholder {
        color: rgba(255,255,255,0.2);
    }

    .field-input:focus {
        border-color: rgba(168,85,247,0.6);
        background: rgba(168,85,247,0.06);
        box-shadow: 0 0 0 3px rgba(168,85,247,0.12);
    }

    /* Error */
    .field-error {
        font-size: 0.78rem;
        color: #f87171;
        margin: 0;
    }

    /* Footer modal */
    .modal-footer {
        display: flex;
        gap: 0.65rem;
        padding: 1rem 1.6rem 1.4rem;
        justify-content: flex-end;
    }

    .btn-cancel {
        padding: 0.72rem 1.25rem;
        border-radius: 0.75rem;
        border: 1px solid rgba(255,255,255,0.1);
        background: transparent;
        color: rgba(255,255,255,0.5);
        font-size: 0.88rem;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
        transition: background 0.15s, color 0.15s, border-color 0.15s;
    }

    .btn-cancel:hover {
        background: rgba(255,255,255,0.06);
        color: rgba(255,255,255,0.8);
        border-color: rgba(255,255,255,0.2);
    }

    .btn-submit {
        padding: 0.72rem 1.5rem;
        border-radius: 0.75rem;
        border: none;
        background: linear-gradient(135deg, #a855f7, #ec4899);
        color: #fff;
        font-size: 0.88rem;
        font-weight: 700;
        cursor: pointer;
        font-family: inherit;
        box-shadow: 0 4px 14px rgba(168,85,247,0.35);
        transition: opacity 0.18s, transform 0.15s;
    }

    .btn-submit:hover {
        opacity: 0.88;
        transform: translateY(-1px);
    }

    .btn-submit:active {
        transform: translateY(0);
    }
</style>

<!-- ══ PAGE HEADER ══ -->
<div style="display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; flex-wrap:wrap;">
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
                                $schedule = $room->schedules
                                    ->where('week_start', $selectedWeek)
                                    ->where('day', $day)
                                    ->where('time_slot', $slot)
                                    ->first();
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