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
                // ✅ Hitung total unit yang dipinjam (SUM qty)
                $frekuensi_pakai = DB::table('borrowing_items')
                    ->where('item_id', $selected_tool->id)
                    ->sum('qty');
                    
                // ✅ AMBIL DATA UNTUK LINE CHART (Quantity berdasarkan tanggal)
                $dataTren = DB::table('borrowing_items')
                    ->join('borrowings', 'borrowing_items.borrowing_id', '=', 'borrowings.id')
                    ->selectRaw('
                        DATE(borrowings.borrow_date) as tanggal,
                        -- Total unit DIPINJAM pada tanggal tersebut
                        SUM(borrowing_items.qty) as total_dipinjam,
                        -- Total unit DIKEMBALIKAN pada tanggal tersebut
                        SUM(borrowing_items.returned_qty) as total_dikembalikan
                    ')
                    ->where('borrowing_items.item_id', $selected_tool->id)
                    ->whereNotNull('borrowings.borrow_date')
                    ->groupBy('tanggal')
                    ->orderBy('tanggal', 'ASC')
                    ->limit(30) // Ambil 30 hari terakhir
                    ->get();
                
                // Siapkan data untuk Chart.js
                $chartLabels = $dataTren->pluck('tanggal')->toArray();
                $chartPinjam = $dataTren->pluck('total_dipinjam')->toArray();    // ✅ Quantity dipinjam
                $chartKembali = $dataTren->pluck('total_dikembalikan')->toArray(); // ✅ Quantity dikembalikan
                
                try {
                    $response = Http::timeout(5)->post("{$this->aiServiceUrl}/predict/stok", [
                        'nama_barang' => $selected_tool->name, 
                        'tipe' => 'alat',
                        'tanggal' => now()->format('Y-m-d'), 
                        'stok_saat_ini' => $selected_tool->stock
                    ]);
                    if ($response->successful()) {
                        $aiResult = $response->json();
                        $ai_service_status = 'connected';
                        $prediksi = $aiResult['prediksi_hari_ini'] ?? 0;
                        $prediksi_habis = $aiResult['perkiraan_tanggal_habis'] ?? '-';
                        $status_stok = $aiResult['status_stok'] ?? 'Aman';
                        $sisa_hari = $aiResult['sisa_hari'] ?? 0;
                    } else {
                        $prediksiData = $this->calculateManualPrediction($selected_tool);
                        $prediksi_habis = $prediksiData['tanggal_habis'];
                        $sisa_hari = $prediksiData['sisa_hari'];
                        $status_stok = $prediksiData['status_stok'];
                    }
                } catch (\Exception $e) {
                    $ai_service_status = 'disconnected';
                    $prediksiData = $this->calculateManualPrediction($selected_tool);
                    $prediksi_habis = $prediksiData['tanggal_habis'];
                    $sisa_hari = $prediksiData['sisa_hari'];
                    $status_stok = $prediksiData['status_stok'];
                }
            }
        }
        
        // Logika Distribusi Pie Chart (berdasarkan quantity)
        $allDistributions = DB::table('borrowing_items')
            ->join('borrowings', 'borrowing_items.borrowing_id', '=', 'borrowings.id')
            ->join('items', 'borrowing_items.item_id', '=', 'items.id')
            ->select('items.name', DB::raw('SUM(borrowing_items.qty) as total_quantity'))
            ->groupBy('items.name')
            ->orderBy('total_quantity', 'desc')
            ->get();

        $pieLabels = [];
        $pieData = [];

        if ($allDistributions->count() > 5) {
            $top4 = $allDistributions->take(4);
            $others = $allDistributions->splice(4)->sum('total_quantity');

            $pieLabels = $top4->pluck('name')->toArray();
            $pieLabels[] = 'Lainnya';
            $pieData = $top4->pluck('total_quantity')->toArray();
            $pieData[] = $others;
        } else {
            $pieLabels = $allDistributions->pluck('name')->toArray();
            $pieData = $allDistributions->pluck('total_quantity')->toArray();
        }

        // Data untuk Bar Chart (Peminjaman bulanan berdasarkan quantity)
        $monthlyData = DB::table('borrowing_items')
            ->join('borrowings', 'borrowing_items.borrowing_id', '=', 'borrowings.id')
            ->selectRaw("DATE_FORMAT(borrowings.borrow_date, '%M') as bulan, SUM(borrowing_items.qty) as total_quantity")
            ->where('borrowings.borrow_date', '>=', now()->subMonths(6))
            ->groupBy('bulan')
            ->orderByRaw('MIN(borrowings.borrow_date) ASC')
            ->get();

        // Data untuk Tabel Ringkasan (Analisis per Item)
        foreach ($all_tools as $tool) {
            // Hitung total unit yang keluar dalam 30 hari terakhir (berdasarkan qty)
            $totalKeluar = DB::table('borrowing_items')
                ->join('borrowings', 'borrowing_items.borrowing_id', '=', 'borrowings.id')
                ->where('borrowing_items.item_id', $tool->id)
                ->where('borrowings.borrow_date', '>=', now()->subDays(30))
                ->sum('borrowing_items.qty');
                
            // Hitung rata-rata per hari
            $tool->avg_usage = round($totalKeluar / 30, 2);
            
            // Hitung sisa hari dan prediksi tanggal habis
            if ($tool->avg_usage > 0 && $tool->stock > 0) {
                $daysLeft = floor($tool->stock / $tool->avg_usage);
                $tool->remaining_days = $daysLeft;
                
                if ($daysLeft > 365) {
                    $tool->prediction_date = '> 1 tahun';
                    $tool->remaining_days = 365;
                } else {
                    $tool->prediction_date = now()->addDays($daysLeft)->format('d M Y');
                }
            } else {
                $tool->avg_usage = 0;
                $tool->remaining_days = 0;
                $tool->prediction_date = $tool->stock <= 0 ? 'Habis' : 'Belum ada data';
            }
        }

        return view('admin.analytics.alat-bahan', compact(
            'all_tools', 'monthlyData', 'selected_tool', 'prediksi', 'frekuensi_pakai', 
            'prediksi_habis', 'chartLabels', 'chartPinjam', 'chartKembali', 'chartData', 'status_stok', 
            'sisa_hari', 'rekomendasi_pembelian', 'rekomendasi_pesan', 
            'ai_service_status', 'pieLabels', 'pieData'
        ));
    }

    /**
     * Hitung prediksi manual dengan benar
     */
    private function calculateManualPrediction($tool)
    {
        $result = [
            'tanggal_habis' => '-',
            'sisa_hari' => 0,
            'status_stok' => 'Aman'
        ];
        
        if (!$tool || $tool->stock <= 0) {
            $result['tanggal_habis'] = 'Habis';
            $result['status_stok'] = 'Habis';
            return $result;
        }
        
        // Hitung total unit yang dipinjam dalam 30 hari terakhir
        $totalKeluar = DB::table('borrowing_items')
            ->join('borrowings', 'borrowing_items.borrowing_id', '=', 'borrowings.id')
            ->where('borrowing_items.item_id', $tool->id)
            ->where('borrowings.borrow_date', '>=', now()->subDays(30))
            ->sum('borrowing_items.qty');
        
        $hariRange = 30;
        
        // Jika tidak ada data 30 hari, cek 90 hari
        if ($totalKeluar == 0) {
            $totalKeluar = DB::table('borrowing_items')
                ->join('borrowings', 'borrowing_items.borrowing_id', '=', 'borrowings.id')
                ->where('borrowing_items.item_id', $tool->id)
                ->where('borrowings.borrow_date', '>=', now()->subDays(90))
                ->sum('borrowing_items.qty');
            $hariRange = 90;
        }
        
        if ($totalKeluar == 0) {
            $result['tanggal_habis'] = 'Tidak terprediksi';
            $result['sisa_hari'] = 999;
            $result['status_stok'] = 'Aman';
            return $result;
        }
        
        // Hitung rata-rata per hari
        $avgUsage = $totalKeluar / $hariRange;
        
        // Hitung sisa hari
        $sisa_hari = floor($tool->stock / $avgUsage);
        
        if ($sisa_hari > 365) {
            $sisa_hari = 365;
            $tanggal_habis = '> 1 tahun';
        } else {
            $tanggal_habis = now()->addDays($sisa_hari)->format('d M Y');
        }
        
        // Tentukan status stok
        if ($sisa_hari <= 7) {
            $status_stok = 'Kritis';
        } elseif ($sisa_hari <= 14) {
            $status_stok = 'Peringatan';
        } else {
            $status_stok = 'Aman';
        }
        
        return [
            'tanggal_habis' => $tanggal_habis,
            'sisa_hari' => $sisa_hari,
            'status_stok' => $status_stok
        ];
    }

        public function ruangan(Request $request)
    {
        $all_rooms = Room::all();
        $selected_room = null;
        
        // Nilai Default
        $total_peminjaman = 0; 
        $occupancy = 0; 
        $jam_terpadat = '-'; 
        $rata_rata_durasi = 0;
        $chartLabels = []; 
        $chartData = [];
        
        // Data untuk Distribusi Jam Penggunaan
        $timeSlotLabels = [];
        $timeSlotData = [];
        $timeSlotPeakHour = null; // Jam terpadat (untuk highlight)

        if ($request->has('room_id') && $request->room_id != '') {
            $selected_room = Room::find($request->room_id);
            
            if ($selected_room) {
                // ============================================
                // 1. TOTAL PEMINJAMAN (Seluruh waktu)
                // ============================================
                $total_peminjaman = Borrowing::where('room_id', $selected_room->id)->count();

                // ============================================
                // 2. OKUPANSI (Persentase peminjaman dibanding semua ruangan dalam 30 hari)
                // ============================================
                $thisRoomBorrowings = Borrowing::where('room_id', $selected_room->id)
                    ->where('borrow_date', '>=', now()->subDays(30))
                    ->count();
                
                $allRoomsBorrowings = Borrowing::where('borrow_date', '>=', now()->subDays(30))
                    ->count();
                
                if ($allRoomsBorrowings > 0) {
                    $occupancy = round(($thisRoomBorrowings / $allRoomsBorrowings) * 100, 1);
                } else {
                    $occupancy = 0;
                }

                // ============================================
                // 3. JAM TERPADAT (Range waktu yang paling sering dipinjam)
                // ============================================
                $timeSlots = Borrowing::where('room_id', $selected_room->id)
                    ->where('borrow_date', '>=', now()->subDays(30))
                    ->select('time_slot', DB::raw('COUNT(*) as total'))
                    ->groupBy('time_slot')
                    ->orderBy('total', 'desc')
                    ->first();
                
                if ($timeSlots) {
                    $jam_terpadat = $timeSlots->time_slot;
                } else {
                    $jam_terpadat = '-';
                }

                // ============================================
                // 4. DURASI RATA-RATA (Dari time_slot dalam 30 hari terakhir)
                // ============================================
                $borrowings = Borrowing::where('room_id', $selected_room->id)
                    ->where('borrow_date', '>=', now()->subDays(30))
                    ->whereNotNull('time_slot')
                    ->get();
                
                $totalDuration = 0;
                $validCount = 0;
                
                foreach ($borrowings as $borrowing) {
                    $duration = $this->calculateDurationFromTimeSlot($borrowing->time_slot);
                    if ($duration > 0) {
                        $totalDuration += $duration;
                        $validCount++;
                    }
                }
                
                if ($validCount > 0) {
                    $rata_rata_durasi = round($totalDuration / $validCount, 1);
                } else {
                    $rata_rata_durasi = 0;
                }

                // ============================================
                // 5. TREN PEMINJAMAN (Untuk Chart - 30 hari terakhir)
                // ============================================
                $chart = Borrowing::selectRaw('DATE(borrow_date) as tanggal, COUNT(*) as total')
                    ->where('room_id', $selected_room->id)
                    ->where('borrow_date', '>=', now()->subDays(30))
                    ->groupBy('tanggal')
                    ->orderBy('tanggal', 'ASC')
                    ->get();
                
                $dateRange = [];
                $dateCounts = [];
                
                for ($i = 29; $i >= 0; $i--) {
                    $date = now()->subDays($i)->format('Y-m-d');
                    $dateRange[] = $date;
                    $dateCounts[$date] = 0;
                }
                
                foreach ($chart as $item) {
                    $dateCounts[$item->tanggal] = $item->total;
                }
                
                $chartLabels = array_keys($dateCounts);
                $chartData = array_values($dateCounts);
                
                // ============================================
                // 6. DISTRIBUSI JAM PENGGUNAAN (Bar Chart per Hour)
                // ============================================
                $timeSlotDistribution = $this->getTimeSlotDistribution($selected_room->id);
                $timeSlotLabels = $timeSlotDistribution['labels'];
                $timeSlotData = $timeSlotDistribution['data'];
                $timeSlotPeakHour = $timeSlotDistribution['peak_hour'];
            }
        }

        return view('admin.analytics.ruangan', compact(
            'all_rooms', 'selected_room', 'total_peminjaman', 'occupancy', 
            'jam_terpadat', 'rata_rata_durasi', 'chartLabels', 'chartData',
            'timeSlotLabels', 'timeSlotData', 'timeSlotPeakHour'
        ));
    }

    /**
     * Mendapatkan distribusi jam penggunaan (per jam dari time_slot)
     * 
     * @param int $roomId
     * @return array
     */
    private function getTimeSlotDistribution($roomId)
    {
        // Rentang jam operasional (07:00 - 16:00)
        $hourRange = range(7, 16);
        $labels = [];
        $data = [];
        
        // Inisialisasi semua jam dengan 0
        foreach ($hourRange as $hour) {
            $labels[] = sprintf('%02d:00', $hour);
            $data[$hour] = 0;
        }
        
        // Ambil semua time_slot dalam 30 hari terakhir
        $borrowings = Borrowing::where('room_id', $roomId)
            ->where('borrow_date', '>=', now()->subDays(30))
            ->whereNotNull('time_slot')
            ->get();
        
        foreach ($borrowings as $borrowing) {
            // Ekstrak jam mulai dari time_slot (format: "08:00 - 09:30")
            $startHour = $this->extractStartHourFromTimeSlot($borrowing->time_slot);
            
            if ($startHour !== null && isset($data[$startHour])) {
                $data[$startHour]++;
            }
        }
        
        // Cari jam terpadat (nilai tertinggi)
        $peakHour = null;
        $maxValue = 0;
        foreach ($data as $hour => $count) {
            if ($count > $maxValue) {
                $maxValue = $count;
                $peakHour = $hour;
            }
        }
        
        // Konversi ke array untuk chart
        $chartData = array_values($data);
        
        return [
            'labels' => $labels,
            'data' => $chartData,
            'peak_hour' => $peakHour,
            'peak_value' => $maxValue
        ];
    }

    /**
     * Ekstrak jam mulai dari format time_slot
     * Contoh: "08:00 - 09:30" → 8
     * 
     * @param string $timeSlot
     * @return int|null
     */
    private function extractStartHourFromTimeSlot($timeSlot)
    {
        if (empty($timeSlot)) {
            return null;
        }
        
        // Normalisasi format
        $timeSlot = str_replace([' s/d ', ' sampai '], ' - ', $timeSlot);
        
        // Pisahkan start time
        $parts = preg_split('/\s*-\s*/', $timeSlot);
        
        if (count($parts) < 1) {
            return null;
        }
        
        $startTime = trim($parts[0]);
        
        // Parse jam dari format HH:MM
        if (preg_match('/^(\d{1,2}):\d{2}$/', $startTime, $matches)) {
            return (int)$matches[1];
        }
        
        return null;
    }

    /**
     * Menghitung durasi dalam jam dari format time_slot
     */
    private function calculateDurationFromTimeSlot($timeSlot)
    {
        if (empty($timeSlot)) {
            return 0;
        }
        
        $timeSlot = str_replace([' s/d ', ' sampai '], ' - ', $timeSlot);
        $parts = preg_split('/\s*-\s*/', $timeSlot);
        
        if (count($parts) != 2) {
            return 0;
        }
        
        $startTime = trim($parts[0]);
        $endTime = trim($parts[1]);
        
        $start = $this->parseTimeToMinutes($startTime);
        $end = $this->parseTimeToMinutes($endTime);
        
        if ($start === null || $end === null) {
            return 0;
        }
        
        $durationMinutes = $end - $start;
        
        if ($durationMinutes < 0) {
            $durationMinutes += 24 * 60;
        }
        
        return round($durationMinutes / 60, 2);
    }
    
    private function parseTimeToMinutes($time)
    {
        $time = str_replace('.', ':', $time);
        
        if (!preg_match('/^(\d{1,2}):(\d{2})$/', $time, $matches)) {
            return null;
        }
        
        $hours = (int)$matches[1];
        $minutes = (int)$matches[2];
        
        if ($hours < 0 || $hours > 23 || $minutes < 0 || $minutes > 59) {
            return null;
        }
        
        return ($hours * 60) + $minutes;
    }
}