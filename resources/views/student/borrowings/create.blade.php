<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Peminjaman Baru</title>

    @vite('resources/css/app.css')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        *, *::before, *::after { box-sizing: border-box; }

        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Inter', sans-serif;
        }

        /* ── Page backdrop: shows the dashboard behind ── */
        body {
            min-height: 100vh;
            background: radial-gradient(ellipse at 20% 50%, #2a3f8f 0%, #1a2a6e 40%, #0d1540 100%);
            display: flex;
            align-items: flex-start;
            justify-content: center;
            /* Blur overlay effect */
            backdrop-filter: blur(0px);
        }

        /* ── Overlay: full-screen dim + blur over the dashboard ── */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(5, 10, 30, 0.55);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 2rem 1rem 1rem;
            overflow-y: auto;
            z-index: 50;
        }

        /* ── Modal card ── */
        .modal-card {
            width: 100%;
            max-width: 660px;
            background: #1c2544;
            border-radius: 1.25rem;
            box-shadow: 0 32px 80px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255,255,255,0.06);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            margin-bottom: 2rem;
        }

        /* ── Scrollable form body ── */
        .modal-body {
            padding: 2.25rem 2.5rem 1.5rem;
            overflow-y: auto;
            max-height: calc(100vh - 120px);
        }

        .modal-title {
            font-size: 1.55rem;
            font-weight: 800;
            color: #fff;
            margin: 0 0 2rem;
            letter-spacing: -0.01em;
        }

        /* ── Fields ── */
        .field {
            margin-bottom: 1.35rem;
        }

        .field > label,
        .field > .field-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: rgba(255,255,255,0.55);
            margin-bottom: 0.45rem;
            letter-spacing: 0.01em;
        }

        .field-hint {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.3);
            margin-top: 0.3rem;
        }

        /* ── Inputs, selects, textareas ── */
        .field input[type="text"],
        .field input[type="number"],
        .field input[type="date"],
        .field select,
        .field textarea,
        .item-row input[type="number"],
        .item-row select {
            display: block;
            width: 100%;
            background: rgba(255,255,255,0.055) !important;
            border: 1px solid rgba(255,255,255,0.09) !important;
            border-radius: 0.7rem !important;
            padding: 0.82rem 1.1rem !important;
            color: #fff !important;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: border-color 0.2s, background 0.2s;
            box-shadow: none !important;
            appearance: auto;
            -webkit-appearance: auto;
        }

        /* Custom arrow for select */
        .field select,
        .item-row select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='rgba(255,255,255,0.4)' d='M1 1l5 5 5-5'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 1rem center !important;
            padding-right: 2.5rem !important;
            -webkit-appearance: none !important;
            appearance: none !important;
            cursor: pointer;
        }

        .field input::placeholder,
        .field textarea::placeholder,
        .item-row input::placeholder {
            color: rgba(255,255,255,0.22) !important;
        }

        .field input:focus,
        .field select:focus,
        .field textarea:focus,
        .item-row input:focus,
        .item-row select:focus {
            border-color: rgba(96,165,250,0.5) !important;
            background: rgba(255,255,255,0.08) !important;
            box-shadow: 0 0 0 3px rgba(96,165,250,0.08) !important;
            outline: none !important;
        }

        .field select option,
        .item-row select option {
            background: #1c2544;
            color: #fff;
        }

        .field textarea {
            resize: vertical;
            min-height: 110px;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(0.55);
            cursor: pointer;
        }

        /* ── Radio group ── */
        .radio-group {
            display: flex;
            gap: 1.5rem;
            margin-top: 0.15rem;
        }

        .radio-label {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.9rem;
            color: rgba(255,255,255,0.7);
            font-weight: 500;
            cursor: pointer;
        }

        .radio-label input[type="radio"] {
            accent-color: #60a5fa;
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        /* ── Items section ── */
        .items-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.65rem;
        }

        .items-header-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: rgba(255,255,255,0.55);
        }

        .btn-add {
            background: none;
            border: none;
            color: #60a5fa;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }

        .btn-add:hover { color: #93c5fd; }

        .item-row {
            display: grid;
            grid-template-columns: 1fr 110px 34px;
            gap: 0.5rem;
            margin-bottom: 0.55rem;
            align-items: start;
        }

        .btn-remove {
            background: none;
            border: none;
            color: rgba(248,113,113,0.55);
            font-size: 1.5rem;
            line-height: 1;
            cursor: pointer;
            padding: 0.55rem 0 0;
            font-family: 'Inter', sans-serif;
        }

        .btn-remove:hover { color: #f87171; }

        /* ── Bottom action bar ── */
        .modal-actions {
            display: flex;
            gap: 0.65rem;
            padding: 1.1rem 2.5rem 1.75rem;
            border-top: 1px solid rgba(255,255,255,0.06);
            background: #1c2544;
        }

        .btn-submit {
            flex: 1;
            padding: 0.9rem;
            background: linear-gradient(90deg, #3b82f6 0%, #06b6d4 100%);
            border: none;
            border-radius: 0.7rem;
            color: #fff;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: opacity 0.18s;
            letter-spacing: 0.01em;
        }

        .btn-submit:hover { opacity: 0.87; }

        .btn-batal {
            flex: 1;
            padding: 0.9rem;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 0.7rem;
            color: rgba(255,255,255,0.65);
            font-size: 0.95rem;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: background 0.18s;
        }

        .btn-batal:hover {
            background: rgba(255,255,255,0.11);
            color: rgba(255,255,255,0.85);
        }

        /* ── Scrollbar ── */
        .modal-body::-webkit-scrollbar { width: 6px; }
        .modal-body::-webkit-scrollbar-track { background: transparent; }
        .modal-body::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.12);
            border-radius: 3px;
        }
        .modal-body::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.22);
        }

        /* ── Responsive ── */
        @media (max-width: 680px) {
            .modal-overlay { padding: 0; align-items: flex-end; }
            .modal-card { max-width: 100%; border-radius: 1.25rem 1.25rem 0 0; margin-bottom: 0; }
            .modal-body { padding: 1.5rem 1.25rem 1rem; max-height: 80vh; }
            .modal-actions { padding: 0.9rem 1.25rem 1.5rem; }
        }
    </style>
</head>

<body>

    {{-- ── The dashboard / page content sits here behind the overlay ── --}}

    <!-- Modal overlay -->
    <div class="modal-overlay" x-data="borrowingForm()">

        <!-- Modal card -->
        <div class="modal-card">

            <!-- Scrollable body -->
            <div class="modal-body">

                <p class="modal-title">Peminjaman Baru</p>

                <x-alert-error />

                <form method="POST" action="{{ route('student.borrowings.store') }}" id="theForm">
                    @csrf

                    <!-- Tanggal Peminjaman -->
                    <div class="field">
                        <label>Tanggal Peminjaman</label>
                        <input type="date" name="borrow_date" x-model="borrow_date" @change="loadSchedules" required>
                    </div>

                    <!-- Ruangan -->
                    <div class="field">
                        <label>Ruangan</label>
                        <select name="room_id" x-model="room_id" @change="loadSchedules" required>
                            <option value="">Pilih Ruangan</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Waktu -->
                    <div class="field">
                        <label>Waktu</label>
                        <select name="time_slot" x-model="time_slot" required>
                            <option value="">Pilih Waktu</option>
                            <template x-for="slot in schedules">
                                <option :value="slot.time_slot" x-text="slot.time_slot"></option>
                            </template>
                        </select>
                        <p class="field-hint">Pilih ruangan dan tanggal terlebih dahulu</p>
                        <p class="field-hint"
                            x-show="scheduleMessage"
                            x-text="scheduleMessage">
                        </p>
                    </div>

                    <!-- Keperluan -->
                    <div class="field">
                        <label>Keperluan</label>
                        <textarea name="purpose" rows="4" required placeholder="Jelaskan keperluan peminjaman"></textarea>
                    </div>

                    <!-- Jumlah Orang -->
                    <div class="field">
                        <label>Jumlah Orang</label>
                        <input type="number" name="total_people" required min="1"
                               placeholder="Masukkan jumlah orang yang akan menggunakan">
                    </div>

                    <!-- Dibersamai Dosen -->
                    <div class="field">
                        <label>Dibersamai Dosen?</label>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="with_lecturer" value="1" x-model="with_lecturer"> Ya
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="with_lecturer" value="0" x-model="with_lecturer"> Tidak
                            </label>
                        </div>
                    </div>

                    <!-- Nama Dosen (conditional) -->
                    <div class="field" x-show="with_lecturer == 1" x-transition>
                        <label>Nama Dosen</label>
                        <input type="text" name="lecturer_name" placeholder="Masukkan nama dosen">
                    </div>

                    <!-- Alat yang Dipinjam -->
                    <div class="field">
                        <div class="items-header">
                            <span class="items-header-label">Alat yang Dipinjam</span>
                            <button type="button" @click="addItem" class="btn-add">+ Tambah Alat</button>
                        </div>

                        <template x-for="(item, index) in items" :key="index">
                            <div class="item-row">
                                <select :name="'items['+index+'][item_id]'" @change="setStock(index, $event)">
                                    <option value="">Pilih Alat</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}" data-stock="{{ $item->stock }}">
                                            {{ $item->name }} (stok: {{ $item->stock }})
                                        </option>
                                    @endforeach
                                </select>

                                <div>
                                    <input
                                        type="number" min="1"
                                        :max="items[index].max_stock || 1"
                                        @change="validateQty(index)"
                                        x-model="items[index].qty"
                                        placeholder="Qty"
                                        :name="'items['+index+'][qty]'"
                                    >
                                    <p class="field-hint" x-show="items[index].max_stock">
                                        Maks: <span x-text="items[index].max_stock"></span>
                                    </p>
                                </div>

                                <button type="button" @click="removeItem(index)" class="btn-remove">×</button>
                            </div>
                        </template>
                    </div>

                </form>
            </div>

            <!-- Action buttons -->
            <div class="modal-actions">
                <button type="submit" form="theForm" class="btn-submit">Submit</button>
                <a href="{{ route('student.dashboard') }}" class="btn-batal">Batal</a>
            </div>

        </div><!-- /.modal-card -->
    </div><!-- /.modal-overlay -->

    <script>
    function borrowingForm() {
        return {
            borrow_date: '',
            room_id: '',
            schedules: [],
            time_slot: '',
            scheduleMessage: '',
            with_lecturer: 0,
            items: [{ qty: '', max_stock: 0 }],

            addItem()             { this.items.push({ qty: '', max_stock: 0 }); },
            removeItem(index)     { this.items.splice(index, 1); },

            async loadSchedules() {

                this.time_slot = '';
                this.schedules = [];
                this.scheduleMessage = '';

                if (!this.borrow_date || !this.room_id) return;

                const res = await fetch(
                    `/student/available-schedules?room_id=${this.room_id}&date=${this.borrow_date}`
                );

                const data = await res.json();

                if (data.message) {
                    this.scheduleMessage = data.message;
                    return;
                }

                this.schedules = data;
            },

            setStock(index, event) {
                const opt = event.target.options[event.target.selectedIndex];
                this.items[index].max_stock = parseInt(opt.dataset.stock) || 0;
            },

            validateQty(index) {
                const qty = parseInt(this.items[index].qty);
                const max = this.items[index].max_stock;
                if (qty > max) {
                    this.items[index].qty = max;
                    alert('Stok maksimal saat ini: ' + max);
                }
            }
        }
    }
    </script>

</body>
</html>