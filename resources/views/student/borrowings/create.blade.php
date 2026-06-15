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

        body {
            min-height: 100vh;
            background: radial-gradient(ellipse at 20% 50%, #2a3f8f 0%, #1a2a6e 40%, #0d1540 100%);
            display: flex;
            align-items: flex-start;
            justify-content: center;
            backdrop-filter: blur(0px);
        }

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

        /* ── PERUBAHAN 1: grid item-row disesuaikan ── */
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

        .modal-body::-webkit-scrollbar { width: 6px; }
        .modal-body::-webkit-scrollbar-track { background: transparent; }
        .modal-body::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.12);
            border-radius: 3px;
        }
        .modal-body::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.22);
        }

        /* ── PERUBAHAN 2: CSS combobox baru ── */
        [x-cloak] { display: none !important; }

        .combobox-wrap {
            position: relative;
        }

        .combobox-input {
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
            cursor: text;
        }

        .combobox-input:focus {
            border-color: rgba(96,165,250,0.5) !important;
            background: rgba(255,255,255,0.08) !important;
            box-shadow: 0 0 0 3px rgba(96,165,250,0.08) !important;
        }

        .combobox-input::placeholder {
            color: rgba(255,255,255,0.22) !important;
        }

        .combobox-dropdown {
            position: absolute;
            top: calc(100% + 6px);
            left: 0; right: 0;
            background: #1e2d5a;
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 0.7rem;
            max-height: 220px;
            overflow-y: auto;
            z-index: 100;
            box-shadow: 0 12px 40px rgba(0,0,0,0.5);
        }

        .combobox-option {
            padding: 0.65rem 1rem;
            font-size: 0.88rem;
            color: rgba(255,255,255,0.8);
            cursor: pointer;
            transition: background 0.15s;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .combobox-option:hover,
        .combobox-option.is-active {
            background: rgba(96,165,250,0.15);
            color: #fff;
        }

        .combobox-option .stock-badge {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.4);
            background: rgba(255,255,255,0.07);
            padding: 0.15rem 0.5rem;
            border-radius: 999px;
            white-space: nowrap;
        }

        .combobox-empty {
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
            color: rgba(255,255,255,0.3);
            text-align: center;
        }

        .combobox-dropdown::-webkit-scrollbar { width: 5px; }
        .combobox-dropdown::-webkit-scrollbar-track { background: transparent; }
        .combobox-dropdown::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.15);
            border-radius: 3px;
        }

        @media (max-width: 680px) {
            .modal-overlay { padding: 0; align-items: flex-end; }
            .modal-card { max-width: 100%; border-radius: 1.25rem 1.25rem 0 0; margin-bottom: 0; }
            .modal-body { padding: 1.5rem 1.25rem 1rem; max-height: 80vh; }
            .modal-actions { padding: 0.9rem 1.25rem 1.5rem; }
        }
    </style>
</head>

<body>

    <div class="modal-overlay" x-data="borrowingForm()">

        <div class="modal-card">

            <div class="modal-body">

                <p class="modal-title">Peminjaman Baru</p>

                <x-alert-error />

                <form method="POST" action="{{ route('student.borrowings.store') }}" id="theForm">
                    @csrf

                    <div class="field">
                        <label>Tanggal Peminjaman</label>
                        <input type="date" name="borrow_date" x-model="borrow_date" @change="loadSchedules" required>
                    </div>

                    <div class="field">
                        <label>Ruangan</label>
                        <select name="room_id" x-model="room_id" @change="loadSchedules" required>
                            <option value="">Pilih Ruangan</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>

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

                    <div class="field">
                        <label>Keperluan</label>
                        <textarea name="purpose" rows="4" required placeholder="Jelaskan keperluan peminjaman"></textarea>
                    </div>

                    <div class="field">
                        <label>Jumlah Orang</label>
                        <input type="number" name="total_people" required min="1"
                               placeholder="Masukkan jumlah orang yang akan menggunakan">
                    </div>

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

                    <div class="field" x-show="with_lecturer == 1" x-transition>
                        <label>Nama Dosen</label>
                        <input type="text" name="lecturer_name" placeholder="Masukkan nama dosen">
                    </div>

                    <!-- ── PERUBAHAN 3: item-row pakai combobox, bukan <select> ── -->
                    <div class="field">
                        <div class="items-header">
                            <span class="items-header-label">Alat yang Dipinjam</span>
                            <button type="button" @click="addItem" class="btn-add">+ Tambah Alat</button>
                        </div>

                        <template x-for="(item, index) in items" :key="index">
                            <div class="item-row" x-data="combobox(index, items)">

                                <!-- PERUBAHAN 3a: hidden input untuk submit -->
                                <input type="hidden" :name="'items['+index+'][item_id]'" :value="items[index].item_id">

                                <!-- PERUBAHAN 3b: searchable combobox -->
                                <div class="combobox-wrap">
                                    <input
                                        type="text"
                                        class="combobox-input"
                                        placeholder="Cari alat..."
                                        x-model="search"
                                        @focus="open = true"
                                        @input="open = true"
                                        @keydown.escape="open = false"
                                        @keydown.arrow-down.prevent="moveDown"
                                        @keydown.arrow-up.prevent="moveUp"
                                        @keydown.enter.prevent="selectHighlighted(index)"
                                        @blur="handleBlur"
                                        autocomplete="off"
                                    >

                                    <div class="combobox-dropdown" x-show="open" x-cloak>
                                        <template x-if="filtered.length === 0">
                                            <div class="combobox-empty">Alat tidak ditemukan</div>
                                        </template>
                                        <template x-for="(opt, i) in filtered" :key="opt.id">
                                            <div
                                                class="combobox-option"
                                                :class="{ 'is-active': highlighted === i }"
                                                @mousedown.prevent="selectOption(opt, index)"
                                            >
                                                <span x-text="opt.name"></span>
                                                <span class="stock-badge">stok: <span x-text="opt.stock"></span></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>

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

            <div class="modal-actions">
                <button type="submit" form="theForm" class="btn-submit">Submit</button>
                <a href="{{ route('student.dashboard') }}" class="btn-batal">Batal</a>
            </div>

        </div>
    </div>

    <!-- ── PERUBAHAN 4: expose $items sebagai JSON untuk Alpine combobox ── -->
    <script>
        const ALL_ITEMS = @json($items->map(fn($i) => ['id' => $i->id, 'name' => $i->name, 'stock' => $i->stock]));
    </script>

    <script>
    // ── PERUBAHAN 5: borrowingForm() — items kini punya item_id, hapus setStock ──
    function borrowingForm() {
        return {
            borrow_date: '',
            room_id: '',
            schedules: [],
            time_slot: '',
            scheduleMessage: '',
            with_lecturer: 0,
            items: [{ item_id: '', qty: '', max_stock: 0 }],

            addItem()         { this.items.push({ item_id: '', qty: '', max_stock: 0 }); },
            removeItem(index) { this.items.splice(index, 1); },

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

    // ── PERUBAHAN 5: fungsi combobox() baru ──
    function combobox(index, items) {
        return {
            search: '',
            open: false,
            highlighted: 0,

            get filtered() {
                if (!this.search.trim()) return ALL_ITEMS;
                const q = this.search.toLowerCase();
                return ALL_ITEMS.filter(o => o.name.toLowerCase().includes(q));
            },

            selectOption(opt, index) {
                items[index].item_id   = opt.id;
                items[index].max_stock = opt.stock;
                this.search      = opt.name;
                this.open        = false;
                this.highlighted = 0;
            },

            selectHighlighted(index) {
                if (this.filtered[this.highlighted]) {
                    this.selectOption(this.filtered[this.highlighted], index);
                }
            },

            moveDown() {
                this.highlighted = this.highlighted < this.filtered.length - 1
                    ? this.highlighted + 1
                    : 0;
            },

            moveUp() {
                this.highlighted = this.highlighted > 0
                    ? this.highlighted - 1
                    : this.filtered.length - 1;
            },

            handleBlur() {
                // delay agar mousedown pada option sempat terpanggil dulu
                setTimeout(() => { this.open = false; }, 150);
            }
        }
    }
    </script>

</body>
</html>