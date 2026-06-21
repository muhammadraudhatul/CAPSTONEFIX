@extends('admin.layouts.app')

@section('content')

<style>
    /* ── Flip7 muted create item page: hanya desain, logic/form tetap dipertahankan ── */
    .item-create-flip7-wrapper {
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

    .item-create-flip7-wrapper *,
    .item-create-flip7-wrapper *::before,
    .item-create-flip7-wrapper *::after {
        box-sizing: border-box;
    }

    .item-create-flip7-wrapper::before,
    .item-create-flip7-wrapper::after {
        content: '';
        position: absolute;
        border-radius: 28px;
        border: 1px solid rgba(53, 111, 107, 0.05);
        background: rgba(247, 243, 232, 0.20);
        pointer-events: none;
        z-index: 0;
    }

    .item-create-flip7-wrapper::before {
        width: 150px;
        height: 210px;
        right: -74px;
        top: 110px;
        transform: rotate(-8deg);
    }

    .item-create-flip7-wrapper::after {
        width: 108px;
        height: 146px;
        left: -52px;
        bottom: 120px;
        transform: rotate(10deg);
    }

    .item-create-flip7-main {
        position: relative;
        z-index: 1;
        max-width: 760px;
    }

    .item-create-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
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

    .item-create-header::before {
        content: '';
        position: absolute;
        inset: 0 auto 0 0;
        width: 6px;
        background: linear-gradient(180deg, rgba(215, 189, 98, 0.85), rgba(75, 149, 143, 0.85), rgba(201, 122, 98, 0.78));
    }

    .item-create-header::after {
        content: 'CREATE ITEM';
        position: absolute;
        right: 1.5rem;
        bottom: 0.9rem;
        color: rgba(53, 111, 107, 0.05);
        font-weight: 900;
        font-size: clamp(1.9rem, 6vw, 4.1rem);
        line-height: 1;
        letter-spacing: 0.08em;
        pointer-events: none;
    }

    .item-create-title-block,
    .btn-back {
        position: relative;
        z-index: 1;
    }

    .item-create-title {
        font-size: clamp(1.9rem, 3vw, 2.55rem);
        font-weight: 900;
        color: var(--text-primary);
        letter-spacing: -0.045em;
        margin: 0 0 0.45rem;
        line-height: 1.05;
        text-shadow: 2px 2px 0 rgba(215, 189, 98, 0.20);
    }

    .item-create-subtitle {
        color: #4C8A85;
        font-size: 0.95rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        margin: 0;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        min-height: 40px;
        padding: 0.62rem 1rem;
        border-radius: 999px;
        color: var(--primary-dark);
        background: rgba(231, 239, 237, 0.82);
        border: 1px solid rgba(75, 149, 143, 0.14);
        font-size: 0.86rem;
        font-weight: 900;
        text-decoration: none;
        transition: background 0.18s, transform 0.18s, border-color 0.18s;
        box-shadow: 0 4px 14px rgba(53, 111, 107, 0.04);
    }

    .btn-back:hover {
        background: rgba(75, 149, 143, 0.12);
        border-color: rgba(75, 149, 143, 0.24);
        transform: translateY(-1px);
        color: var(--primary-dark);
    }

    /* ── Card ── */
    .form-card {
        background: linear-gradient(180deg, rgba(250,251,250,0.98), rgba(244, 241, 234, 0.88));
        border: 1px solid rgba(255, 255, 255, 0.82);
        border-left: 7px solid var(--primary-teal);
        border-radius: 26px;
        padding: 2rem 2.25rem;
        margin-top: 1.75rem;
        box-shadow: var(--shadow-soft);
        position: relative;
        overflow: hidden;
    }

    .form-card::before {
        content: '';
        display: block;
        position: absolute;
        inset: 0 0 auto 0;
        height: 8px;
        background: linear-gradient(90deg, rgba(215, 189, 98, 0.95), rgba(75, 149, 143, 0.92), rgba(201, 122, 98, 0.84));
    }

    /* ── Field ── */
    .field {
        margin-bottom: 1.4rem;
        position: relative;
    }

    .field label {
        display: block;
        font-size: 0.78rem;
        font-weight: 900;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .field input[type="text"],
    .field input[type="number"],
    .field select,
    .field textarea {
        display: block;
        width: 100%;
        background: rgba(247, 243, 232, 0.88) !important;
        border: 1px solid rgba(75, 149, 143, 0.16) !important;
        border-radius: 16px !important;
        padding: 0.86rem 1rem !important;
        color: var(--text-primary) !important;
        font-size: 0.92rem;
        font-weight: 700;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'PingFang SC', 'Microsoft YaHei', sans-serif;
        outline: none;
        transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
        box-shadow: 0 4px 14px rgba(53, 111, 107, 0.04) !important;
    }

    .field input::placeholder,
    .field textarea::placeholder {
        color: var(--text-soft) !important;
        font-weight: 600;
    }

    .field input:focus,
    .field select:focus,
    .field textarea:focus {
        border-color: rgba(75, 149, 143, 0.38) !important;
        background: #FAFBFA !important;
        box-shadow: 0 0 0 4px rgba(75, 149, 143, 0.10) !important;
    }

    .field select {
        appearance: none !important;
        -webkit-appearance: none !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23657675' d='M1 1l5 5 5-5'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 1rem center !important;
        padding-right: 2.5rem !important;
        cursor: pointer;
    }

    .field select option {
        background: #FAFBFA;
        color: var(--text-primary);
    }

    .field textarea {
        resize: vertical;
        min-height: 110px;
    }

    .field-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    /* ── Submit ── */
    .btn-submit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        min-height: 46px;
        margin-top: 0.5rem;
        padding: 0.78rem 1.65rem;
        background: linear-gradient(135deg, #E6D58C, #D7BD62);
        border: 1px solid rgba(255, 255, 255, 0.75);
        border-radius: 999px;
        color: #43340E;
        font-size: 0.92rem;
        font-weight: 900;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'PingFang SC', 'Microsoft YaHei', sans-serif;
        cursor: pointer;
        box-shadow: 0 8px 20px rgba(31, 42, 41, 0.05);
        transition: filter 0.18s, transform 0.18s, box-shadow 0.18s;
    }

    .btn-submit:hover {
        filter: saturate(0.96);
        transform: translateY(-2px);
        box-shadow: 0 10px 22px rgba(53, 111, 107, 0.08);
    }

    .btn-submit:active {
        transform: scale(0.97);
    }

    @keyframes itemCreateFadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .item-create-header,
    .form-card {
        animation: itemCreateFadeUp 0.35s ease both;
    }

    .form-card {
        animation-delay: 0.08s;
    }

    @media (max-width: 760px) {
        .item-create-flip7-wrapper {
            padding: 1.25rem 1rem;
            border-radius: 22px;
        }

        .item-create-main {
            max-width: none;
        }

        .item-create-header {
            padding: 1.35rem 1.25rem;
            border-radius: 24px;
        }

        .btn-back {
            width: 100%;
        }

        .form-card {
            padding: 1.5rem 1.25rem;
            border-radius: 24px;
        }

        .field-grid-2 {
            grid-template-columns: 1fr;
            gap: 0;
        }
    }
</style>

<div class="item-create-flip7-wrapper">
    <div class="item-create-flip7-main">

        <!-- HEADER -->
        <div class="item-create-header">
            <div class="item-create-title-block">
                <h1 class="item-create-title">
                    Tambah Item
                </h1>
                <p class="item-create-subtitle">
                    Tambahkan alat atau bahan laboratorium
                </p>
            </div>
            <a href="{{ route('items.index') }}"
               class="btn-back">
                ← Kembali
            </a>
        </div>

        <!-- FORM CARD -->
        <div class="form-card">
            <form method="POST" action="{{ route('items.store') }}">
                @csrf

                <!-- Nama -->
                <div class="field">
                    <label>Nama Item</label>
                    <input type="text" name="name" required placeholder="Masukkan nama item">
                </div>

                <!-- Jenis -->
                <div class="field">
                    <label>Jenis</label>
                    <select name="type" required>
                        <option value="">Pilih Jenis</option>
                        <option value="tool">Alat</option>
                        <option value="material">Bahan</option>
                    </select>
                </div>

                <!-- Ruangan -->
                <div class="field">
                    <label>Ruangan</label>
                    <select name="room_id" required>
                        <option value="">Pilih Ruangan</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}">{{ $room->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Lokasi -->
                <div class="field">
                    <label>Lokasi Penyimpanan</label>
                    <input type="text" name="location" required placeholder="Rak A1 / Lemari B / dll">
                </div>

                <!-- Satuan -->
                <div class="field">
                    <label>Satuan</label>
                    <input type="text" name="unit" required placeholder="Unit / Botol / Box / Liter">
                </div>

                <!-- Stok (2 col) -->
                <div class="field field-grid-2">
                    <div>
                        <label>Jumlah Stok</label>
                        <input type="number" name="stock" required min="0">
                    </div>
                    <div>
                        <label>Minimum Stok</label>
                        <input type="number" name="minimum_stock" required min="0">
                    </div>
                </div>

                <!-- Deskripsi -->
                <div class="field">
                    <label>Deskripsi</label>
                    <textarea name="description" rows="4" placeholder="Deskripsi item..."></textarea>
                </div>

                <button type="submit" class="btn-submit">
                    Simpan Item
                </button>

            </form>
        </div>

    </div>
</div>

@endsection
