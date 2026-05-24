<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token"
          content="{{ csrf_token() }}">

    <title>Edit Peminjaman</title>

    @vite('resources/css/app.css')

    <script defer
        src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js">
    </script>

</head>

<body class="bg-[#f5f4ff] min-h-screen">

<div class="max-w-5xl mx-auto py-12 px-6">

    <!-- BACK -->
    <a
        href="{{ route('student.dashboard') }}"
        class="inline-flex items-center gap-2
               text-indigo-600 font-semibold
               hover:text-indigo-800 transition"
    >

        ← Kembali

    </a>

    <!-- TITLE -->
    <h1 class="text-5xl font-bold text-gray-900">
        Edit Peminjaman
    </h1>

    <p class="mt-3 text-gray-500 text-lg">
        Ubah data peminjaman laboratorium
    </p>

    <!-- FORM -->
    <form
        method="POST"
        action="{{ route(
            'student.borrowings.update',
            $borrowing
        ) }}"
        class="mt-10 bg-white rounded-3xl shadow-xl p-10"
        x-data="borrowingForm()"
    >

        @csrf
        @method('PATCH')

        <!-- DATE -->
        <div>

            <label class="block font-semibold text-lg mb-3">

                Tanggal Peminjaman

            </label>

            <input
                type="date"
                name="borrow_date"
                x-model="borrow_date"
                @change="loadSchedules"
                value="{{ $borrowing->borrow_date }}"
                required
                class="w-full rounded-2xl border-gray-300
                       focus:border-indigo-500
                       focus:ring-indigo-500"
            >

        </div>

        <!-- ROOM -->
        <div class="mt-8">

            <label class="block font-semibold text-lg mb-3">

                Ruangan

            </label>

            <select
                name="room_id"
                x-model="room_id"
                @change="loadSchedules"
                required
                class="w-full rounded-2xl border-gray-300
                       focus:border-indigo-500
                       focus:ring-indigo-500"
            >

                <option value="">
                    Pilih Ruangan
                </option>

                @foreach($rooms as $room)

                    <option
                        value="{{ $room->id }}"
                        @selected(
                            $borrowing->room_id ==
                            $room->id
                        )
                    >

                        {{ $room->name }}

                    </option>

                @endforeach

            </select>

        </div>

        <!-- TIME -->
        <div class="mt-8">

            <label class="block font-semibold text-lg mb-3">

                Waktu

            </label>

            <select
                name="time_slot"
                required
                class="w-full rounded-2xl border-gray-300
                       focus:border-indigo-500
                       focus:ring-indigo-500"
            >

                <option value="">
                    Pilih Waktu
                </option>

                <template x-for="slot in schedules">

                    <option
                        :value="slot.time_slot"
                        :selected="
                            slot.time_slot ==
                            '{{ $borrowing->time_slot }}'
                        "
                        x-text="slot.time_slot"
                    ></option>

                </template>

            </select>

        </div>

        <!-- PURPOSE -->
        <div class="mt-8">

            <label class="block font-semibold text-lg mb-3">

                Keperluan

            </label>

            <textarea
                name="purpose"
                rows="5"
                required
                class="w-full rounded-2xl border-gray-300
                       focus:border-indigo-500
                       focus:ring-indigo-500"
            >{{ $borrowing->purpose }}</textarea>

        </div>

        <!-- PEOPLE -->
        <div class="mt-8">

            <label class="block font-semibold text-lg mb-3">

                Jumlah Orang

            </label>

            <input
                type="number"
                name="total_people"
                value="{{ $borrowing->total_people }}"
                required
                min="1"
                class="w-full rounded-2xl border-gray-300
                       focus:border-indigo-500
                       focus:ring-indigo-500"
            >

        </div>

        <!-- LECTURER -->
        <div class="mt-8">

            <label class="block font-semibold text-lg mb-4">

                Dibersamai Dosen?

            </label>

            <div class="flex gap-6">

                <label class="flex items-center gap-2">

                    <input
                        type="radio"
                        name="with_lecturer"
                        value="1"
                        x-model="with_lecturer"
                    >

                    Ya

                </label>

                <label class="flex items-center gap-2">

                    <input
                        type="radio"
                        name="with_lecturer"
                        value="0"
                        x-model="with_lecturer"
                    >

                    Tidak

                </label>

            </div>

        </div>

        <!-- LECTURER NAME -->
        <div
            class="mt-8"
            x-show="with_lecturer == 1"
            x-transition
        >

            <label class="block font-semibold text-lg mb-3">

                Nama Dosen

            </label>

            <input
                type="text"
                name="lecturer_name"
                value="{{ $borrowing->lecturer_name }}"
                class="w-full rounded-2xl border-gray-300
                       focus:border-indigo-500
                       focus:ring-indigo-500"
            >

        </div>

        <!-- ITEMS -->
        <div class="mt-10">

            <div class="flex justify-between items-center">

                <label class="font-semibold text-lg">

                    Alat yang Dipinjam

                </label>

                <button
                    type="button"
                    @click="addItem"
                    class="text-indigo-600 font-semibold"
                >

                    + Tambah Alat

                </button>

            </div>

            <!-- LOOP -->
            <template
                x-for="(item, index) in items"
                :key="index"
            >

                <div class="grid grid-cols-12 gap-4 mt-5">

                    <!-- ITEM -->
                    <div class="col-span-8">

                        <select
                            :name="'items['+index+'][item_id]'"
                            x-model="item.item_id"
                            @change="setStock(index, $event)"
                            class="w-full rounded-2xl border-gray-300
                                   focus:border-indigo-500
                                   focus:ring-indigo-500"
                        >

                            <option value="">
                                Pilih Alat
                            </option>

                            @foreach($items as $item)

                                <option
                                    value="{{ $item->id }}"
                                    data-stock="{{ $item->stock }}"
                                >

                                    {{ $item->name }}
                                    (stok:
                                    {{ $item->stock }})

                                </option>

                            @endforeach

                        </select>

                    </div>

                    <!-- QTY -->
                    <div class="col-span-3">

                        <input
                            type="number"
                            min="1"
                            x-model="item.qty"
                            :max="item.max_stock || 1"
                            @change="validateQty(index)"
                            :name="'items['+index+'][qty]'"
                            class="w-full rounded-2xl border-gray-300
                                   focus:border-indigo-500
                                   focus:ring-indigo-500"
                        >

                        <p
                            class="text-sm text-gray-400 mt-2"
                            x-show="item.max_stock"
                        >

                            Maksimal stok:
                            <span x-text="item.max_stock"></span>

                        </p>

                    </div>

                    <!-- DELETE -->
                    <div class="col-span-1">

                        <button
                            type="button"
                            @click="removeItem(index)"
                            class="text-red-500 text-xl"
                        >

                            ×

                        </button>

                    </div>

                </div>

            </template>

        </div>

        <!-- ACTION -->
        <div class="flex gap-4 mt-12">

            <!-- CANCEL -->
            <a
                href="{{ route('student.dashboard') }}"
                class="w-1/3 bg-gray-100 text-gray-700
                       py-4 rounded-2xl text-lg
                       font-semibold text-center
                       hover:bg-gray-200 transition"
            >

                Cancel

            </a>

            <!-- SUBMIT -->
            <button
                type="submit"
                class="w-2/3 bg-gradient-to-r
                       from-indigo-500 to-purple-600
                       text-white py-4 rounded-2xl
                       text-lg font-semibold
                       hover:opacity-90 transition"
            >

                Update Peminjaman

            </button>

        </div>

    </form>

</div>

<script>

function borrowingForm()
{
    return {

        borrow_date:
            '{{ $borrowing->borrow_date }}',

        room_id:
            '{{ $borrowing->room_id }}',

        schedules: [],

        with_lecturer:
            '{{ $borrowing->with_lecturer }}',

        items: [

            @foreach($borrowing->items as $borrowedItem)

                {
                    item_id:
                        '{{ $borrowedItem->item_id }}',

                    qty:
                        '{{ $borrowedItem->qty }}',

                    max_stock:
                        '{{ $borrowedItem->item->stock }}',
                },

            @endforeach

        ],

        addItem()
        {
            this.items.push({

                item_id: '',

                qty: '',

                max_stock: 0,

            });
        },

        removeItem(index)
        {
            this.items.splice(index, 1);
        },

        async loadSchedules()
        {
            if (!this.borrow_date || !this.room_id)
            {
                return;
            }

            const response = await fetch(
                `/student/available-schedules?room_id=${this.room_id}&date=${this.borrow_date}`
            );

            this.schedules =
                await response.json();
        },

        setStock(index, event)
        {
            const selected =
                event.target.options[
                    event.target.selectedIndex
                ];

            const stock =
                selected.dataset.stock;

            this.items[index].max_stock =
                parseInt(stock);
        },

        validateQty(index)
        {
            const qty =
                parseInt(this.items[index].qty);

            const max =
                this.items[index].max_stock;

            if (qty > max)
            {
                this.items[index].qty = max;

                alert(
                    'Stok maksimal saat ini: ' + max
                );
            }
        },

        init()
        {
            this.loadSchedules();
        }

    }
}

</script>

</body>
</html>