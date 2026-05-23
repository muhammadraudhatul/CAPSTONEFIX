<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item; // Pastikan Model Item ada
use App\Models\Room; // Pastikan Model Room ada
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AnalyticsController extends Controller
{
    // Halaman Alat dan Bahan
    public function alatBahan(Request $request){
        $all_tools = Item::all();
        $selected_tool = null;
        $prediksi = 0;

        // statistik
        $frekuensi_pakai = 0;
        $tren_mingguan = 0;
        $prediksi_habis = '-';

        if ($request->has('item_id') && $request->item_id != '') {
            $selected_tool = Item::find($request->item_id);
            $frekuensi_pakai = rand(1, 50);
            $tren_mingguan = rand(1, 20);

            if ($selected_tool->stock > 0) {
                $prediksi_habis = ceil(
                    $selected_tool->stock / max($frekuensi_pakai, 1)
                ) . ' Hari';
            }
            try {
                $response = Http::post('http://127.0.0.1:5000/predict/stok', [
                    'item_id' => $selected_tool->id,
                    'nama' => $selected_tool->name
                ]);
                $prediksi = $response->json()['prediksi'] ?? 0;
            } catch (\Exception $e) {
                $prediksi = 0;
            }
        }

        return view('admin.analytics.alat-bahan', compact(
            'all_tools',
            'selected_tool',
            'prediksi',
            'frekuensi_pakai',
            'tren_mingguan',
            'prediksi_habis'
        ));
    }

    // Halaman Ruangan
    public function ruangan(Request $request){
        $all_rooms = Room::all();
        $selected_room = null;
        $hasilAi = [
            'status' => '-',
            'rekomendasi_jam' => '-'
        ];

        // statistik
        $occupancy = 0;
        $total_peminjaman = 0;
        $jam_terpadat = '-';

        if ($request->has('room_id') && $request->room_id != '') {
            $selected_room = Room::find($request->room_id);
            // contoh statistik sederhana
            $total_peminjaman = rand(10, 100);
            $occupancy = rand(50, 100);
            $jam_terpadat = rand(7, 16) . ':00';
            try {
                $response = Http::post('http://127.0.0.1:5000/predict/ruangan', [
                    'laboratorium' => $selected_room->name
                ]);
                $hasilAi = $response->json();
            } catch (\Exception $e) {

            }
        }

        return view('admin.analytics.ruangan', compact(
            'all_rooms',
            'selected_room',
            'hasilAi',
            'occupancy',
            'total_peminjaman',
            'jam_terpadat'
        ));
    }
}