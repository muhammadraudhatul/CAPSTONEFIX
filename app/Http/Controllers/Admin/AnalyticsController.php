<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Room;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AnalyticsController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ANALYTICS ALAT & BAHAN
    |--------------------------------------------------------------------------
    */

    public function alatBahan(Request $request)
    {
        $all_tools = Item::all();

        $selected_tool = null;

        /*
        |--------------------------------------------------------------------------
        | DEFAULT VALUE
        |--------------------------------------------------------------------------
        */

        $prediksi = 0;

        $frekuensi_pakai = 0;

        $prediksi_habis = '-';

        $chartLabels = [];

        $chartData = [];

        /*
        |--------------------------------------------------------------------------
        | ITEM DIPILIH
        |--------------------------------------------------------------------------
        */

        if ($request->has('item_id') && $request->item_id != '') {

            $selected_tool = Item::find($request->item_id);

            /*
            |--------------------------------------------------------------------------
            | FREKUENSI PAKAI REAL
            |--------------------------------------------------------------------------
            |
            | Menghitung total peminjaman item oleh student
            |
            */

            $frekuensi_pakai = DB::table('borrowing_items')
                ->where('item_id', $selected_tool->id)
                ->count();

            /*
            |--------------------------------------------------------------------------
            | CHART TREN PENGGUNAAN REAL
            |--------------------------------------------------------------------------
            |
            | Mengambil histori penggunaan item per hari
            |
            */

            $chart = DB::table('borrowing_items')
                ->join(
                    'borrowings',
                    'borrowing_items.borrowing_id',
                    '=',
                    'borrowings.id'
                )
                ->selectRaw(
                    'DATE(borrowings.borrow_date) as tanggal,
                    COUNT(*) as total'
                )
                ->where('borrowing_items.item_id', $selected_tool->id)
                ->groupBy('tanggal')
                ->orderBy('tanggal')
                ->get();

            $chartLabels = $chart->pluck('tanggal');

            $chartData = $chart->pluck('total');

            /*
            |--------------------------------------------------------------------------
            | AI MODEL XGBOOST
            |--------------------------------------------------------------------------
            */

            try {

                $response = Http::post(
                    'http://127.0.0.1:5000/predict/stok',
                    [
                        'item_id' => $selected_tool->id,
                        'nama' => $selected_tool->name,
                        'stok_sekarang' => $selected_tool->stock,
                        'frekuensi_pakai' => $frekuensi_pakai
                    ]
                );

                $aiResult = $response->json();

                /*
                |--------------------------------------------------------------------------
                | HASIL MODEL
                |--------------------------------------------------------------------------
                */

                $prediksi = $aiResult['prediksi'] ?? 0;

                /*
                |--------------------------------------------------------------------------
                | PREDIKSI HABIS
                |--------------------------------------------------------------------------
                */

                $prediksi_habis = $aiResult['prediksi_habis'] ?? '-';

            } catch (\Exception $e) {

                $prediksi = 0;

                $prediksi_habis = '-';
            }
        }

        return view('admin.analytics.alat-bahan', compact(
            'all_tools',
            'selected_tool',
            'prediksi',
            'frekuensi_pakai',
            'prediksi_habis',
            'chartLabels',
            'chartData'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | ANALYTICS RUANGAN
    |--------------------------------------------------------------------------
    */

    public function ruangan(Request $request)
    {
        $all_rooms = Room::all();

        $selected_room = null;

        /*
        |--------------------------------------------------------------------------
        | DEFAULT VALUE
        |--------------------------------------------------------------------------
        */

        $hasilAi = [
            'status' => '-',
            'rekomendasi_jam' => '-'
        ];

        $occupancy = 0;

        $total_peminjaman = 0;

        $jam_terpadat = '-';

        $chartLabels = [];

        $chartData = [];

        /*
        |--------------------------------------------------------------------------
        | ROOM DIPILIH
        |--------------------------------------------------------------------------
        */

        if ($request->has('room_id') && $request->room_id != '') {

            $selected_room = Room::find($request->room_id);

            /*
            |--------------------------------------------------------------------------
            | TOTAL PEMINJAMAN
            |--------------------------------------------------------------------------
            */

            $total_peminjaman = Borrowing::where(
                'room_id',
                $selected_room->id
            )->count();

            /*
            |--------------------------------------------------------------------------
            | OCCUPANCY RATE REAL
            |--------------------------------------------------------------------------
            |
            | Tingkat okupansi =
            | total jam digunakan / total jam tersedia
            |
            */

            $jumlah_booking = Borrowing::where(
                    'room_id',
                    $selected_room->id
                )
                ->count();

            /*
            |--------------------------------------------------------------------------
            | ASUMSI:
            | 1 hari ada 10 slot
            | 30 hari = 300 slot tersedia
            |--------------------------------------------------------------------------
            */

            $total_slot_tersedia = 300;

            if ($total_slot_tersedia > 0) {

                $occupancy = round(
                    ($jumlah_booking / $total_slot_tersedia) * 100,
                    1
                );

            } else {

                $occupancy = 0;
            }

            /*
            |--------------------------------------------------------------------------
            | JAM TERPADAT
            |--------------------------------------------------------------------------
            */

            $jam = Borrowing::selectRaw(
                    'time_slot,
                    COUNT(*) as total'
                )
                ->where('room_id', $selected_room->id)
                ->groupBy('time_slot')
                ->orderByDesc('total')
                ->first();

            $jam_terpadat = $jam->time_slot ?? '-';

            /*
            |--------------------------------------------------------------------------
            | GRAFIK PENGGUNAAN RUANGAN
            |--------------------------------------------------------------------------
            */

            $chart = Borrowing::selectRaw(
                    'DATE(borrow_date) as tanggal,
                    COUNT(*) as total'
                )
                ->where('room_id', $selected_room->id)
                ->groupBy('tanggal')
                ->orderBy('tanggal')
                ->get();

            $chartLabels = $chart->pluck('tanggal');

            $chartData = $chart->pluck('total');

            /*
            |--------------------------------------------------------------------------
            | AI MODEL
            |--------------------------------------------------------------------------
            */

            try {

                $response = Http::post(
                    'http://127.0.0.1:5000/predict/ruangan',
                    [
                        'laboratorium' => $selected_room->name,
                        'occupancy' => $occupancy,
                        'total_peminjaman' => $total_peminjaman
                    ]
                );

                $hasilAi = $response->json();

            } catch (\Exception $e) {

                $hasilAi = [
                    'status' => '-',
                    'rekomendasi_jam' => '-'
                ];
            }
        }

        return view('admin.analytics.ruangan', compact(
            'all_rooms',
            'selected_room',
            'hasilAi',
            'occupancy',
            'total_peminjaman',
            'jam_terpadat',
            'chartLabels',
            'chartData'
        ));
    }
}