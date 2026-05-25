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
    /*
    |--------------------------------------------------------------------------
    | KONFIGURASI AI SERVICE
    |--------------------------------------------------------------------------
    */
    
    private $aiServiceUrl = 'http://127.0.0.1:5000'; // Ganti dengan URL server AI Anda
    
    /*
    |--------------------------------------------------------------------------
    | ANALYTICS ALAT & BAHAN - WEB VIEW
    |--------------------------------------------------------------------------
    */

    public function alatBahan(Request $request)
    {
        $all_tools = Item::all();
        $selected_tool = null;
        
        // Default values
        $prediksi = 0;
        $frekuensi_pakai = 0;
        $prediksi_habis = '-';
        $chartLabels = [];
        $chartData = [];
        
        // Tambahan variabel baru
        $status_stok = 'Aman';
        $sisa_hari = 0;
        $rekomendasi_pembelian = 0;
        $rekomendasi_pesan = '-';
        $periode_hari = 30;
        $ai_service_status = 'disconnected';
        $last_sync = now()->format('H:i:s');
        
        if ($request->has('item_id') && $request->item_id != '') {
            $selected_tool = Item::find($request->item_id);
            
            if ($selected_tool) {
                // Frekuensi pemakaian real
                $frekuensi_pakai = DB::table('borrowing_items')
                    ->where('item_id', $selected_tool->id)
                    ->count();
                
                // Chart tren penggunaan real
                $chart = DB::table('borrowing_items')
                    ->join('borrowings', 'borrowing_items.borrowing_id', '=', 'borrowings.id')
                    ->selectRaw('DATE(borrowings.borrow_date) as tanggal, COUNT(*) as total')
                    ->where('borrowing_items.item_id', $selected_tool->id)
                    ->groupBy('tanggal')
                    ->orderBy('tanggal', 'DESC')
                    ->limit(30)
                    ->get();
                
                $chartLabels = $chart->pluck('tanggal')->reverse();
                $chartData = $chart->pluck('total')->reverse();
                
                // ========================================
                // PANGGIL AI SERVICE
                // ========================================
                try {
                    $response = Http::timeout(10)->post("{$this->aiServiceUrl}/predict/stok", [
                        'nama_barang' => $selected_tool->name,
                        'tipe' => 'alat',
                        'tanggal' => now()->format('Y-m-d'),
                        'stok_saat_ini' => $selected_tool->stock
                    ]);
                    
                    if ($response->successful()) {
                        $aiResult = $response->json();
                        $ai_service_status = 'connected';
                        
                        if (isset($aiResult['success']) && $aiResult['success']) {
                            $prediksi = $aiResult['prediksi_hari_ini'] ?? 0;
                            $prediksi_habis = $aiResult['perkiraan_tanggal_habis'] ?? '-';
                            $status_stok = $aiResult['status_stok'] ?? 'Aman';
                            $sisa_hari = $aiResult['sisa_hari'] ?? 0;
                        }
                    }
                    
                    // Panggil juga prediksi kebutuhan stok
                    $needResponse = Http::timeout(10)->post("{$this->aiServiceUrl}/predict/kebutuhan-stok", [
                        'nama_barang' => $selected_tool->name,
                        'tipe' => 'alat',
                        'periode_hari' => 30,
                        'stok_saat_ini' => $selected_tool->stock
                    ]);
                    
                    if ($needResponse->successful()) {
                        $needResult = $needResponse->json();
                        if (isset($needResult['success']) && $needResult['success']) {
                            $rekomendasi_pembelian = $needResult['rekomendasi_pembelian'] ?? 0;
                            $rekomendasi_pesan = $needResult['rekomendasi_pesan'] ?? '-';
                            $periode_hari = $needResult['periode_hari'] ?? 30;
                        }
                    }
                    
                } catch (\Exception $e) {
                    // Fallback ke logika sederhana jika AI Service mati
                    $ai_service_status = 'disconnected';
                    $rata_rata = $frekuensi_pakai > 0 ? round($frekuensi_pakai / 30, 2) : 1;
                    $prediksi = max(1, round($rata_rata));
                    
                    if ($prediksi > 0 && $selected_tool->stock > 0) {
                        $sisa_hari = intval($selected_tool->stock / $prediksi);
                        $prediksi_habis = now()->addDays($sisa_hari)->format('Y-m-d');
                    }
                    
                    if ($sisa_hari <= 14) {
                        $status_stok = 'Kritis';
                    } elseif ($sisa_hari <= 30) {
                        $status_stok = 'Peringatan';
                    } else {
                        $status_stok = 'Aman';
                    }
                    
                    $estimasi_kebutuhan = intval($rata_rata * 30);
                    $rekomendasi_pembelian = max(0, $estimasi_kebutuhan - $selected_tool->stock);
                    $rekomendasi_pesan = $rekomendasi_pembelian > 0 
                        ? "Segera pesan {$rekomendasi_pembelian} unit" 
                        : "Stok mencukupi untuk 30 hari";
                }
            }
        }
        
        return view('admin.analytics.alat-bahan', compact(
            'all_tools',
            'selected_tool',
            'prediksi',
            'frekuensi_pakai',
            'prediksi_habis',
            'chartLabels',
            'chartData',
            'status_stok',
            'sisa_hari',
            'rekomendasi_pembelian',
            'rekomendasi_pesan',
            'periode_hari',
            'ai_service_status',
            'last_sync'
        ));
    }
    
    /*
    |--------------------------------------------------------------------------
    | ANALYTICS RUANGAN - WEB VIEW
    |--------------------------------------------------------------------------
    */
    
    public function ruangan(Request $request)
    {
        $all_rooms = Room::all();
        $selected_room = null;
        
        // Default values
        $hasilAi = ['status' => '-', 'rekomendasi_jam' => '-'];
        $occupancy = 0;
        $total_peminjaman = 0;
        $jam_terpadat = '-';
        $hari_terpadat = '-';
        $chartLabels = [];
        $chartData = [];
        $rata_rata_durasi = 0;
        $ai_service_status = 'disconnected';
        
        if ($request->has('room_id') && $request->room_id != '') {
            $selected_room = Room::find($request->room_id);
            
            if ($selected_room) {
                // Total peminjaman
                $total_peminjaman = Borrowing::where('room_id', $selected_room->id)->count();
                
                // Tingkat okupansi (30 hari terakhir)
                $startDate = now()->subDays(30);
                $jumlah_booking_30hari = Borrowing::where('room_id', $selected_room->id)
                    ->where('borrow_date', '>=', $startDate)
                    ->count();
                
                // Asumsi 1 hari = 10 slot (08:00 - 17:00), 30 hari = 300 slot
                $total_slot_tersedia = 300;
                $occupancy = $total_slot_tersedia > 0 
                    ? round(($jumlah_booking_30hari / $total_slot_tersedia) * 100, 1) 
                    : 0;
                
                // Jam terpadat
                $jam = Borrowing::selectRaw('time_slot, COUNT(*) as total')
                    ->where('room_id', $selected_room->id)
                    ->whereNotNull('time_slot')
                    ->where('time_slot', '!=', '')
                    ->groupBy('time_slot')
                    ->orderByDesc('total')
                    ->first();
                $jam_terpadat = $jam->time_slot ?? '-';
                
                // =========================================================
                // PERBAIKAN 1: HARI TERPADAT (SQLite compatible)
                // =========================================================
                try {
                    // SQLite: strftime("%w", borrow_date) -> 0=Sunday, 1=Monday, ..., 6=Saturday
                    $hari = Borrowing::selectRaw('strftime("%w", borrow_date) as hari, COUNT(*) as total')
                        ->where('room_id', $selected_room->id)
                        ->groupBy('hari')
                        ->orderByDesc('total')
                        ->first();
                    
                    if ($hari) {
                        $hariMap = [
                            0 => 'Minggu',
                            1 => 'Senin', 
                            2 => 'Selasa', 
                            3 => 'Rabu', 
                            4 => 'Kamis', 
                            5 => 'Jumat', 
                            6 => 'Sabtu'
                        ];
                        $hari_terpadat = $hariMap[$hari->hari] ?? '-';
                    } else {
                        $hari_terpadat = '-';
                    }
                } catch (\Exception $e) {
                    // Fallback menggunakan Collection
                    $borrowings = Borrowing::where('room_id', $selected_room->id)->get();
                    if ($borrowings->isNotEmpty()) {
                        $grouped = $borrowings->groupBy(function($item) {
                            return date('w', strtotime($item->borrow_date));
                        });
                        $maxCount = 0;
                        $maxDay = null;
                        foreach ($grouped as $day => $items) {
                            if ($items->count() > $maxCount) {
                                $maxCount = $items->count();
                                $maxDay = $day;
                            }
                        }
                        $hariMap = [
                            0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 
                            3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'
                        ];
                        $hari_terpadat = $hariMap[$maxDay] ?? '-';
                    } else {
                        $hari_terpadat = '-';
                    }
                }
                
                // =========================================================
                // PERBAIKAN 2: RATA-RATA DURASI (SQLite compatible)
                // =========================================================
                // SQLite tidak memiliki TIMESTAMPDIFF, hitung manual
                $borrowings = Borrowing::where('room_id', $selected_room->id)
                    ->whereNotNull('start_time')
                    ->whereNotNull('end_time')
                    ->get();
                
                if ($borrowings->isNotEmpty()) {
                    $totalDurasi = 0;
                    $count = 0;
                    foreach ($borrowings as $borrow) {
                        try {
                            $start = strtotime($borrow->start_time);
                            $end = strtotime($borrow->end_time);
                            if ($start && $end && $start < $end) {
                                $totalDurasi += ($end - $start) / 3600; // Konversi detik ke jam
                                $count++;
                            }
                        } catch (\Exception $e) {
                            // Skip jika format waktu tidak valid
                            continue;
                        }
                    }
                    $rata_rata_durasi = $count > 0 ? round($totalDurasi / $count, 1) : 0;
                } else {
                    $rata_rata_durasi = 0;
                }
                
                // Chart data (30 hari terakhir)
                $chart = Borrowing::selectRaw('DATE(borrow_date) as tanggal, COUNT(*) as total')
                    ->where('room_id', $selected_room->id)
                    ->where('borrow_date', '>=', now()->subDays(30))
                    ->groupBy('tanggal')
                    ->orderBy('tanggal')
                    ->get();
                
                $chartLabels = $chart->pluck('tanggal');
                $chartData = $chart->pluck('total');
                
                // Panggil AI SERVICE
                try {
                    $response = Http::timeout(10)->post("{$this->aiServiceUrl}/predict/ruangan", [
                        'laboratorium' => $selected_room->name,
                        'hari_int' => now()->dayOfWeek,
                        'jam_int' => 10
                    ]);
                    
                    if ($response->successful()) {
                        $hasilAi = $response->json();
                        $ai_service_status = 'connected';
                    }
                    
                } catch (\Exception $e) {
                    $hasilAi = ['status' => 'tersedia', 'rekomendasi_jam' => null];
                }
            }
        }
        
        return view('admin.analytics.ruangan', compact(
            'all_rooms',
            'selected_room',
            'hasilAi',
            'occupancy',
            'total_peminjaman',
            'jam_terpadat',
            'hari_terpadat',
            'chartLabels',
            'chartData',
            'rata_rata_durasi',
            'ai_service_status'
        ));
    }
    
    /*
    |--------------------------------------------------------------------------
    | API METHODS - ALAT & BAHAN (UNTUK FLUTTER)
    |--------------------------------------------------------------------------
    */
    
    /**
     * API: Ringkasan semua alat/bahan
     * GET /api/analytics/alat-bahan/summary
     */
    public function apiAlatBahanSummary(Request $request)
    {
        try {
            $response = Http::timeout(10)->get("{$this->aiServiceUrl}/analytics/alat-bahan/summary", [
                'status' => $request->get('status', 'all'),
                'limit' => $request->get('limit', 100)
            ]);
            
            if ($response->successful()) {
                return response()->json($response->json());
            }
            
            return $this->fallbackAlatBahanSummary($request);
            
        } catch (\Exception $e) {
            return $this->fallbackAlatBahanSummary($request);
        }
    }
    
    /**
     * Fallback jika AI Service tidak tersedia
     */
    private function fallbackAlatBahanSummary(Request $request)
    {
        $items = Item::withCount('borrowings')->get();
        $statusFilter = $request->get('status', 'all');
        
        $result = [];
        foreach ($items as $item) {
            $frekuensi = $item->borrowings_count;
            $rata_rata = $frekuensi > 0 ? round($frekuensi / 30, 2) : 0;
            
            if ($rata_rata > 0 && $item->stock > 0) {
                $sisa_hari = intval($item->stock / $rata_rata);
            } else {
                $sisa_hari = 999;
            }
            
            if ($sisa_hari <= 14) {
                $status = 'Kritis';
            } elseif ($sisa_hari <= 30) {
                $status = 'Peringatan';
            } else {
                $status = 'Aman';
            }
            
            if ($statusFilter != 'all' && $status != $statusFilter) {
                continue;
            }
            
            $result[] = [
                'id_barang' => $item->id,
                'nama_barang' => $item->name,
                'tipe' => 'alat',
                'rata_rata_per_hari' => $rata_rata,
                'frekuensi_total' => $frekuensi,
                'tanggal_terakhir_digunakan' => $item->updated_at?->format('Y-m-d') ?? '-',
                'status_stok' => $status
            ];
        }
        
        return response()->json([
            'success' => true,
            'total' => count($result),
            'statistik' => [
                'kritis' => collect($result)->where('status_stok', 'Kritis')->count(),
                'peringatan' => collect($result)->where('status_stok', 'Peringatan')->count(),
                'aman' => collect($result)->where('status_stok', 'Aman')->count()
            ],
            'items' => $result,
            'fallback' => true
        ]);
    }
    
    /**
     * API: Prediksi stok untuk satu barang
     * POST /api/predict/stok
     */
    public function apiPredictStok(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id'
        ]);
        
        $item = Item::find($request->item_id);
        
        try {
            $response = Http::timeout(10)->post("{$this->aiServiceUrl}/predict/stok", [
                'nama_barang' => $item->name,
                'tipe' => 'alat',
                'tanggal' => now()->format('Y-m-d'),
                'stok_saat_ini' => $item->stock
            ]);
            
            if ($response->successful()) {
                return response()->json($response->json());
            }
            
            return $this->fallbackPredictStok($item);
            
        } catch (\Exception $e) {
            return $this->fallbackPredictStok($item);
        }
    }
    
    private function fallbackPredictStok($item)
    {
        $frekuensi = DB::table('borrowing_items')->where('item_id', $item->id)->count();
        $rata_rata = $frekuensi > 0 ? round($frekuensi / 30, 2) : 1;
        $prediksi = max(1, round($rata_rata));
        
        if ($prediksi > 0 && $item->stock > 0) {
            $sisa_hari = intval($item->stock / $prediksi);
            $tanggal_habis = now()->addDays($sisa_hari)->format('Y-m-d');
        } else {
            $sisa_hari = 999;
            $tanggal_habis = '-';
        }
        
        if ($sisa_hari <= 14) {
            $status = 'Kritis';
        } elseif ($sisa_hari <= 30) {
            $status = 'Peringatan';
        } else {
            $status = 'Aman';
        }
        
        return response()->json([
            'success' => true,
            'nama_barang' => $item->name,
            'tipe' => 'alat',
            'rata_rata_per_hari' => $rata_rata,
            'prediksi_hari_ini' => $prediksi,
            'stok_saat_ini' => $item->stock,
            'sisa_hari' => $sisa_hari,
            'perkiraan_tanggal_habis' => $tanggal_habis,
            'status_stok' => $status,
            'fallback' => true
        ]);
    }
    
    /**
     * API: Prediksi kebutuhan stok untuk periode
     * POST /api/predict/kebutuhan-stok
     */
    public function apiPredictKebutuhanStok(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'periode_hari' => 'nullable|integer|min:1|max:365'
        ]);
        
        $item = Item::find($request->item_id);
        $periode = $request->get('periode_hari', 30);
        
        try {
            $response = Http::timeout(10)->post("{$this->aiServiceUrl}/predict/kebutuhan-stok", [
                'nama_barang' => $item->name,
                'tipe' => 'alat',
                'periode_hari' => $periode,
                'stok_saat_ini' => $item->stock
            ]);
            
            if ($response->successful()) {
                return response()->json($response->json());
            }
            
            return $this->fallbackPredictKebutuhanStok($item, $periode);
            
        } catch (\Exception $e) {
            return $this->fallbackPredictKebutuhanStok($item, $periode);
        }
    }
    
    private function fallbackPredictKebutuhanStok($item, $periode)
    {
        $frekuensi = DB::table('borrowing_items')->where('item_id', $item->id)->count();
        $rata_rata = $frekuensi > 0 ? round($frekuensi / 30, 2) : 1;
        $estimasi = intval($rata_rata * $periode);
        $rekomendasi = max(0, $estimasi - $item->stock);
        
        return response()->json([
            'success' => true,
            'nama_barang' => $item->name,
            'tipe' => 'alat',
            'periode_hari' => $periode,
            'rata_rata_per_hari' => $rata_rata,
            'estimasi_kebutuhan' => $estimasi,
            'stok_saat_ini' => $item->stock,
            'rekomendasi_pembelian' => $rekomendasi,
            'rekomendasi_pesan' => $rekomendasi > 0 ? "Segera pesan {$rekomendasi} unit" : "Stok mencukupi",
            'fallback' => true
        ]);
    }
    
    /**
     * API: Daftar semua barang (master)
     * GET /api/master/barang
     */
    public function apiMasterBarang(Request $request)
    {
        $query = Item::query();
        
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $items = $query->get();
        
        return response()->json([
            'success' => true,
            'total' => $items->count(),
            'items' => $items->map(function ($item) {
                return [
                    'id_barang' => $item->id,
                    'nama_barang' => $item->name,
                    'tipe' => 'alat',
                    'stok_saat_ini' => $item->stock,
                    'satuan' => $item->unit ?? 'Unit',
                    'minimal_stok' => $item->min_stock ?? 0
                ];
            })
        ]);
    }
    
    /*
    |--------------------------------------------------------------------------
    | API METHODS - RUANGAN (UNTUK FLUTTER)
    |--------------------------------------------------------------------------
    */
    
    /**
     * API: Ringkasan semua ruangan
     * GET /api/analytics/ruangan/summary
     */
    public function apiRuanganSummary(Request $request)
    {
        try {
            $response = Http::timeout(10)->get("{$this->aiServiceUrl}/analytics/ruangan/summary");
            
            if ($response->successful()) {
                return response()->json($response->json());
            }
            
            return $this->fallbackRuanganSummary();
            
        } catch (\Exception $e) {
            return $this->fallbackRuanganSummary();
        }
    }
    
    private function fallbackRuanganSummary()
    {
        $rooms = Room::withCount('borrowings')->get();
        
        $result = [];
        foreach ($rooms as $room) {
            // Hitung okupansi 30 hari
            $startDate = now()->subDays(30);
            $jumlah_booking = Borrowing::where('room_id', $room->id)
                ->where('borrow_date', '>=', $startDate)
                ->count();
            $okupansi = round(($jumlah_booking / 300) * 100, 1);
            
            // Jam terpadat
            $jam = Borrowing::selectRaw('time_slot, COUNT(*) as total')
                ->where('room_id', $room->id)
                ->groupBy('time_slot')
                ->orderByDesc('total')
                ->first();
            
            $result[] = [
                'nama_ruangan' => $room->name,
                'frekuensi_penggunaan' => $room->borrowings_count,
                'kapasitas' => $room->capacity,
                'jam_tersibuk' => $jam->time_slot ?? '-',
                'hari_tersibuk' => '-',
                'rata_rata_durasi' => 0,
                'tingkat_okupansi' => $okupansi
            ];
        }
        
        $result = collect($result)->sortByDesc('frekuensi_penggunaan')->values()->toArray();
        
        return response()->json([
            'success' => true,
            'total' => count($result),
            'ruangan_tersibuk' => $result[0]['nama_ruangan'] ?? null,
            'rooms' => $result,
            'fallback' => true
        ]);
    }
    
    /**
     * API: Trend penggunaan ruangan per bulan
     * GET /api/analytics/ruangan/trend
     */
    public function apiRuanganTrend(Request $request)
    {
        $roomId = $request->get('room_id');
        
        $query = Borrowing::query();
        
        if ($roomId) {
            $query->where('room_id', $roomId);
        }
        
        $trend = $query->selectRaw('DATE_FORMAT(borrow_date, "%Y-%m") as bulan, COUNT(*) as total')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();
        
        return response()->json([
            'success' => true,
            'room_id' => $roomId,
            'labels' => $trend->pluck('bulan'),
            'data' => $trend->pluck('total')
        ]);
    }
    
    /**
     * API: Heatmap penggunaan ruangan
     * GET /api/analytics/ruangan/heatmap
     */
    public function apiRuanganHeatmap(Request $request)
    {
        $roomId = $request->get('ruangan_id');
        
        if (!$roomId) {
            return response()->json(['error' => 'ruangan_id parameter required'], 400);
        }
        
        $room = Room::find($roomId);
        
        if (!$room) {
            return response()->json(['error' => 'Ruangan tidak ditemukan'], 404);
        }
        
        // Ambil data peminjaman per jam dari borrowing_items atau borrowings
        // Jika tidak ada data time_slot, gunakan data dummy atau data dari jadwal
        
        $heatmapData = [];
        
        // Coba ambil dari database
        $jamData = Borrowing::where('room_id', $roomId)
            ->selectRaw('time_slot as jam, COUNT(*) as frekuensi')
            ->whereNotNull('time_slot')
            ->groupBy('time_slot')
            ->get();
        
        if ($jamData->isNotEmpty()) {
            foreach ($jamData as $data) {
                $heatmapData[] = [
                    'jam' => (int) $data->jam,
                    'frekuensi' => $data->frekuensi
                ];
            }
        } else {
            // Data dummy jika tidak ada
            for ($jam = 8; $jam <= 16; $jam++) {
                $heatmapData[] = [
                    'jam' => $jam,
                    'frekuensi' => rand(0, 20)
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'nama_ruangan' => $room->name,
            'heatmap_data' => $heatmapData
        ]);
    }
    
    /**
     * API: Rekomendasi ruangan kosong
     * POST /api/predict/ruangan
     */
    public function apiPredictRuangan(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'hari_int' => 'nullable|integer|min:0|max:6',
            'jam_int' => 'nullable|integer|min:0|max:23'
        ]);
        
        $room = Room::find($request->room_id);
        $hari = $request->get('hari_int', now()->dayOfWeek);
        $jam = $request->get('jam_int', 10);
        
        try {
            $response = Http::timeout(10)->post("{$this->aiServiceUrl}/predict/ruangan", [
                'laboratorium' => $room->name,
                'hari_int' => $hari,
                'jam_int' => $jam
            ]);
            
            if ($response->successful()) {
                return response()->json($response->json());
            }
            
            // Fallback: cek jadwal dari database
            $existingBooking = Borrowing::where('room_id', $room->id)
                ->whereDate('borrow_date', now()->format('Y-m-d'))
                ->where('time_slot', $jam . ':00')
                ->exists();
            
            if ($existingBooking) {
                return response()->json(['status' => 'penuh', 'rekomendasi_jam' => null]);
            }
            
            return response()->json(['status' => 'tersedia', 'rekomendasi_jam' => null]);
            
        } catch (\Exception $e) {
            return response()->json(['status' => 'tersedia', 'rekomendasi_jam' => null]);
        }
    }
    
    /**
     * API: Daftar semua ruangan (master)
     * GET /api/master/ruangan
     */
    public function apiMasterRuangan(Request $request)
    {
        $rooms = Room::all();
        
        return response()->json([
            'success' => true,
            'total' => $rooms->count(),
            'ruangan' => $rooms->map(function ($room) {
                return [
                    'id_ruangan' => $room->id,
                    'nama_ruangan' => $room->name,
                    'kapasitas' => $room->capacity
                ];
            })
        ]);
    }
    
    /**
     * API: Health check AI Service
     * GET /api/ai-service/health
     */
    public function apiAiServiceHealth(Request $request)
    {
        try {
            $response = Http::timeout(5)->get("{$this->aiServiceUrl}/health");
            
            if ($response->successful()) {
                return response()->json([
                    'status' => 'connected',
                    'ai_service' => $response->json()
                ]);
            }
            
            return response()->json([
                'status' => 'disconnected',
                'error' => 'AI service tidak merespon'
            ], 503);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'disconnected',
                'error' => $e->getMessage()
            ], 503);
        }
    }
}