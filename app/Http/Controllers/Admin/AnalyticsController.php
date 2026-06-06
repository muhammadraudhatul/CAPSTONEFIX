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
        $chartLabels = []; $chartData = []; $chartPinjam = []; $chartKembali = [];
        $status_stok = 'Aman'; $sisa_hari = 0; $rekomendasi_pembelian = 0;
        $rekomendasi_pesan = '-'; $periode_hari = 30; $ai_service_status = 'disconnected';
        
        if ($request->has('item_id') && $request->item_id != '') {
            $selected_tool = Item::find($request->item_id);
            if ($selected_tool) {
                $frekuensi_pakai = DB::table('borrowing_items')->where('item_id', $selected_tool->id)->count();
    
                // Ambil data per tanggal
                $dataTren = DB::table('borrowing_items')
                    ->join('borrowings', 'borrowing_items.borrowing_id', '=', 'borrowings.id')
                    ->selectRaw('
                        DATE(borrowings.borrow_date) as tanggal, 
                        SUM(qty) as total_pinjam,
                        SUM(CASE WHEN borrowings.status = "returned" THEN qty ELSE 0 END) as total_kembali
                    ')
                    ->where('borrowing_items.item_id', $selected_tool->id)
                    ->groupBy('tanggal')
                    ->orderBy('tanggal', 'ASC')
                    ->limit(30)
                    ->get();
                
                $chartLabels = $dataTren->pluck('tanggal')->toArray();
                $chartPinjam = $dataTren->pluck('total_pinjam')->toArray();
                $chartKembali = $dataTren->pluck('total_kembali')->toArray();
                
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
        
        // Logika Distribusi Pie Chart (Data Asli)
        $allDistributions = DB::table('borrowing_items')
            ->join('borrowings', 'borrowing_items.borrowing_id', '=', 'borrowings.id')
            ->join('users', 'borrowings.user_id', '=', 'users.id')
            ->join('items', 'borrowing_items.item_id', '=', 'items.id')
            ->select('items.name', DB::raw('count(*) as total'))
            ->groupBy('items.name')
            ->orderBy('total', 'desc')
            ->get();

        $pieLabels = [];
        $pieData = [];

        if ($allDistributions->count() > 5) {
            $top4 = $allDistributions->take(4);
            $others = $allDistributions->splice(4)->sum('total');

            $pieLabels = $top4->pluck('name')->toArray();
            $pieLabels[] = 'Lainnya';
            $pieData = $top4->pluck('total')->toArray();
            $pieData[] = $others;
        } else {
            $pieLabels = $allDistributions->pluck('name')->toArray();
            $pieData = $allDistributions->pluck('total')->toArray();
        }

        // Data untuk Bar Chart (Peminjaman bulanan oleh Student)
        $monthlyData = DB::table('borrowing_items')
            ->join('borrowings', 'borrowing_items.borrowing_id', '=', 'borrowings.id')
            ->selectRaw("DATE_FORMAT(borrowings.borrow_date, '%M') as bulan, COUNT(*) as total") // Ganti SUM(quantity) dengan COUNT(*)
            ->where('borrowings.borrow_date', '>=', now()->subMonths(6))
            ->groupBy('bulan')
            ->orderByRaw('MIN(borrowings.borrow_date) ASC')
            ->get();

        // Mengubah menjadi array agar mudah dibaca chart.js
        $labels = $monthlyData->pluck('bulan')->toArray();
        $data = $monthlyData->pluck('total')->toArray();
        
        // Data untuk Tabel Ringkasan (Analisis per Item)
        foreach ($all_tools as $tool) {
            // Total jumlah barang keluar dalam 30 hari terakhir
            $totalKeluar = DB::table('borrowing_items')
                ->join('borrowings', 'borrowing_items.borrowing_id', '=', 'borrowings.id')
                ->where('item_id', $tool->id)
                ->where('borrow_date', '>=', now()->subDays(30))
                ->count();
                
            $tool->avg_usage = round($totalKeluar / 30, 2); 
            $tool->prediction_date = ($tool->avg_usage > 0) 
                ? now()->addDays(floor($tool->stock / $tool->avg_usage))->format('d M Y') 
                : '-';
        }

        return view('admin.analytics.alat-bahan', compact(
            'all_tools', 'monthlyData', 'selected_tool', 'prediksi', 'frekuensi_pakai', 
            'prediksi_habis', 'chartLabels', 'chartPinjam', 'chartKembali', 'chartData', 'status_stok', 
            'sisa_hari', 'rekomendasi_pembelian', 'rekomendasi_pesan', 
            'ai_service_status', 'pieLabels', 'pieData', 'labels', 'data'
        ));
    }

    public function ruangan(Request $request)
    {
        $all_rooms = Room::all();
        $selected_room = null;
        
        // Nilai Default
        $total_peminjaman = 0; $occupancy = 0; $jam_terpadat = '-'; $rata_rata_durasi = 0;
        $chartLabels = []; $chartData = [];

        if ($request->has('room_id') && $request->room_id != '') {
            $selected_room = Room::find($request->room_id);
            
            // 1. Total Peminjaman (Tetap)
            $total_peminjaman = Borrowing::where('room_id', $selected_room->id)->count();

            // 2. Okupansi - Menggunakan DB::raw untuk menangani fungsi DATE()
            $hari_dipinjam = Borrowing::where('room_id', $selected_room->id)
                ->where('borrow_date', '>=', now()->subDays(30))
                ->select(DB::raw('COUNT(DISTINCT DATE(borrow_date)) as total_hari'))
                ->first()
                ->total_hari;

            $occupancy = round(($hari_dipinjam / 30) * 100, 1);

            // 3. Jam Terpadat - Menggunakan DB::raw untuk mengekstrak jam
            $jam = Borrowing::where('room_id', $selected_room->id)
                ->select(DB::raw('HOUR(borrow_date) as jam'), DB::raw('COUNT(*) as total'))
                ->groupBy('jam')
                ->orderBy('total', 'desc')
                ->first();
            $jam_terpadat = $jam ? $jam->jam . ':00' : '-';

            // 4. Durasi Rata-rata - Pastikan menggunakan TIMESTAMPDIFF yang benar
            $durasi = Borrowing::where('room_id', $selected_room->id)
                ->whereNotNull('returned_at') // Ganti dengan nama kolom yang ditemukan
                ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, borrow_date, returned_at)) as avg_durasi')) // Ganti return_date dengan nama kolom yang ditemukan
                ->first();
            $rata_rata_durasi = round($durasi->avg_durasi ?? 0, 1);

            // Tren Peminjaman (Data untuk Area Chart)
            $chart = Borrowing::selectRaw('DATE(borrow_date) as tanggal, COUNT(*) as total')
                ->where('room_id', $selected_room->id)
                ->where('borrow_date', '>=', now()->subDays(30))
                ->groupBy('tanggal')
                ->orderBy('tanggal', 'ASC')
                ->get();
            
            $chartLabels = $chart->pluck('tanggal')->toArray();
            $chartData = $chart->pluck('total')->toArray();
        }

        return view('admin.analytics.ruangan', compact(
            'all_rooms', 'selected_room', 'total_peminjaman', 'occupancy', 
            'jam_terpadat', 'rata_rata_durasi', 'chartLabels', 'chartData'
        ));
    }
}