<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Room;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    private $aiServiceUrl = 'http://127.0.0.1:5000';

    public function alatBahan(Request $request)
    {
        $all_tools = Item::all();
        $selected_tool = null;
        
        $prediksi = 0; $frekuensi_pakai = 0; $prediksi_habis = '-';
        $chartLabels = []; $chartData = [];
        $status_stok = 'Aman'; $sisa_hari = 0; $rekomendasi_pembelian = 0;
        $rekomendasi_pesan = '-'; $periode_hari = 30; $ai_service_status = 'disconnected';
        
        if ($request->has('item_id') && $request->item_id != '') {
            $selected_tool = Item::find($request->item_id);
            if ($selected_tool) {
                $frekuensi_pakai = DB::table('borrowing_items')->where('item_id', $selected_tool->id)->count();
                
                $chart = DB::table('borrowing_items')
                    ->join('borrowings', 'borrowing_items.borrowing_id', '=', 'borrowings.id')
                    ->selectRaw('DATE(borrowings.borrow_date) as tanggal, COUNT(*) as total')
                    ->where('borrowing_items.item_id', $selected_tool->id)
                    ->groupBy('tanggal')
                    ->orderBy('tanggal', 'ASC')
                    ->limit(30)
                    ->get();
                
                $chartLabels = $chart->pluck('tanggal')->toArray();
                $chartData = $chart->pluck('total')->toArray();
                
                try {
                    $response = Http::timeout(5)->post("{$this->aiServiceUrl}/predict/stok", [
                        'nama_barang' => $selected_tool->name, 'tipe' => 'alat',
                        'tanggal' => now()->format('Y-m-d'), 'stok_saat_ini' => $selected_tool->stock
                    ]);
                    if ($response->successful()) {
                        $aiResult = $response->json();
                        $ai_service_status = 'connected';
                        $prediksi = $aiResult['prediksi_hari_ini'] ?? 0;
                        $prediksi_habis = $aiResult['perkiraan_tanggal_habis'] ?? '-';
                        $status_stok = $aiResult['status_stok'] ?? 'Aman';
                        $sisa_hari = $aiResult['sisa_hari'] ?? 0;
                    }
                } catch (\Exception $e) {
                    $ai_service_status = 'disconnected';
                }
            }
        }
        
        return view('admin.analytics.alat-bahan', compact('all_tools', 'selected_tool', 'prediksi', 'frekuensi_pakai', 'prediksi_habis', 'chartLabels', 'chartData', 'status_stok', 'sisa_hari', 'rekomendasi_pembelian', 'rekomendasi_pesan', 'ai_service_status'));
    }

    public function ruangan(Request $request)
    {
        $all_rooms = Room::all();
        $selected_room = null;
        $hasilAi = ['status' => '-', 'rekomendasi_jam' => '-'];
        $occupancy = 0; $total_peminjaman = 0; $jam_terpadat = '-'; $hari_terpadat = '-';
        $chartLabels = []; $chartData = []; $rata_rata_durasi = 0;
        
        if ($request->has('room_id') && $request->room_id != '') {
            $selected_room = Room::find($request->room_id);
            if ($selected_room) {
                $total_peminjaman = Borrowing::where('room_id', $selected_room->id)->count();
                $chart = Borrowing::selectRaw('DATE(borrow_date) as tanggal, COUNT(*) as total')
                    ->where('room_id', $selected_room->id)
                    ->where('borrow_date', '>=', now()->subDays(30))
                    ->groupBy('tanggal')
                    ->orderBy('tanggal', 'ASC')
                    ->get();
                
                $chartLabels = $chart->pluck('tanggal')->toArray();
                $chartData = $chart->pluck('total')->toArray();
            }
        }
        
        return view('admin.analytics.ruangan', compact('all_rooms', 'selected_room', 'hasilAi', 'occupancy', 'total_peminjaman', 'jam_terpadat', 'hari_terpadat', 'chartLabels', 'chartData', 'rata_rata_durasi'));
    }
}