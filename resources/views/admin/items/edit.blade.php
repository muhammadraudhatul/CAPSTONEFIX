@extends('admin.layouts.app')

@section('content')

<style>
    .page-wrap { max-width: 720px; }

    .field { margin-bottom: 1.4rem; }

    .field label {
        display: block;
        font-size: 0.82rem;
        font-weight: 600;
        color: rgba(255,255,255,0.55);
        margin-bottom: 0.5rem;
        letter-spacing: 0.01em;
    }

    .field input[type="text"],
    .field input[type="number"],
    .field select,
    .field textarea {
        display: block;
        width: 100%;
        background: rgba(255,255,255,0.055) !important;
        border: 1px solid rgba(255,255,255,0.09) !important;
        border-radius: 0.75rem !important;
        padding: 0.82rem 1.1rem !important;
        color: #fff !important;
        font-size: 0.9rem;
        font-family: 'Inter', sans-serif;
        outline: none;
        transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
        box-shadow: none !important;
    }

    .field input::placeholder,
    .field textarea::placeholder { color: rgba(255,255,255,0.2) !important; }

    .field input:focus,
    .field select:focus,
    .field textarea:focus {
        border-color: rgba(168,85,247,0.5) !important;
        background: rgba(255,255,255,0.08) !important;
        box-shadow: 0 0 0 3px rgba(168,85,247,0.1) !important;
    }

    .field select {
        appearance: none !important;
        -webkit-appearance: none !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='rgba(255,255,255,0.35)' d='M1 1l5 5 5-5'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 1rem center !important;
        padding-right: 2.5rem !important;
        cursor: pointer;
    }

    .field select option { background: #1c2544; color: #fff; }

    .field textarea { resize: vertical; min-height: 110px; }

    .field-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .form-card {
        background: #1c2544;
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 1.25rem;
        padding: 2rem 2.25rem;
        margin-top: 1.75rem;
    }

    .btn-submit {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.5rem;
        padding: 0.85rem 2rem;
        background: linear-gradient(90deg, #8b5cf6, #ec4899);
        border: none;
        border-radius: 0.85rem;
        color: #fff;
        font-size: 0.95rem;
        font-weight: 700;
        font-family: 'Inter', sans-serif;
        cursor: pointer;
        box-shadow: 0 6px 20px rgba(139,92,246,0.3);
        transition: opacity 0.18s, transform 0.15s;
    }

    .btn-submit:hover { opacity: 0.88; transform: translateY(-1px); }
</style>

<div class="page-wrap">

    <!-- HEADER -->
    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:1rem;">
        <div>
            <h1 style="font-size:2.25rem; font-weight:800; color:#fff; letter-spacing:-0.02em; margin:0 0 0.35rem;">
                Edit Item
            </h1>
            <p style="color:rgba(255,255,255,0.4); font-size:0.95rem; margin:0;">
                Perbarui data alat atau bahan laboratorium
            </p>
        </div>
        <a href="{{ route('items.index') }}"
           style="color:#a78bfa; font-size:0.88rem; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:0.3rem; margin-top:0.4rem; transition:color 0.18s;"
           onmouseover="this.style.color='#c4b5fd'" onmouseout="this.style.color='#a78bfa'">
            ← Kembali
        </a>
    </div>

    <!-- FORM CARD -->
    <div class="form-card">
        <form method="POST" action="{{ route('items.update', $item) }}">
            @csrf
            @method('PUT')

            <!-- Nama -->
            <div class="field">
                <label>Nama Item</label>
                <input type="text" name="name" required value="{{ $item->name }}">
            </div>

            <!-- Jenis -->
            <div class="field">
                <label>Jenis</label>
                <select name="type" required>
                    <option value="tool"     {{ $item->type == 'tool'     ? 'selected' : '' }}>Alat</option>
                    <option value="material" {{ $item->type == 'material' ? 'selected' : '' }}>Bahan</option>
                </select>
            </div>

            <!-- Ruangan -->
            <div class="field">
                <label>Ruangan</label>
                <select name="room_id" required>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}" {{ $item->room_id == $room->id ? 'selected' : '' }}>
                            {{ $room->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Lokasi -->
            <div class="field">
                <label>Lokasi Penyimpanan</label>
                <input type="text" name="location" required value="{{ $item->location }}">
            </div>

            <!-- Satuan -->
            <div class="field">
                <label>Satuan</label>
                <input type="text" name="unit" required value="{{ $item->unit }}">
            </div>

            <!-- Stok (2 col) -->
            <div class="field field-grid-2">
                <div>
                    <label>Jumlah Stok</label>
                    <input type="number" name="stock" required min="0" value="{{ $item->stock }}">
                </div>
                <div>
                    <label>Minimum Stok</label>
                    <input type="number" name="minimum_stock" required min="0" value="{{ $item->minimum_stock }}">
                </div>
            </div>

            <!-- Deskripsi -->
            <div class="field">
                <label>Deskripsi</label>
                <textarea name="description" rows="4">{{ $item->description }}</textarea>
            </div>

            <button type="submit" class="btn-submit">
                Update Item
            </button>

        </form>
    </div>

</div>

@endsection