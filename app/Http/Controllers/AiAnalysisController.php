<?php

namespace App\Http\Controllers;
use App\Models\Room;
use App\Models\Borrowing;
use App\Models\AiItemPrediction;
use App\Models\AiRoomSlotPrediction;
use App\Models\AiRoomPredictionSummary;
use App\Models\AiRoomAnomaly;
use App\Models\AiModelEvaluation;
use App\Models\AiRun;
use App\Models\Item;
use App\Models\BorrowingItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AiAnalysisController extends Controller
{
    public function alatBahan(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Ambil semua item untuk dropdown dan tabel ringkasan
        |--------------------------------------------------------------------------
        */
        $all_tools = Item::query()
            ->with('room')
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 2. Ambil item yang dipilih dari dropdown
        |--------------------------------------------------------------------------
        */
        $selected_tool = null;

        if ($request->filled('item_id')) {
            $selected_tool = Item::query()
                ->with('room')
                ->where('id', $request->item_id)
                ->first();
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Ambil hasil AI terakhir untuk item yang dipilih
        |--------------------------------------------------------------------------
        */
        $aiPrediction = null;

        if ($selected_tool) {
            $aiPrediction = AiItemPrediction::query()
                ->where('item_id', $selected_tool->id)
                ->first();
        }

        $lastAiRun = AiRun::latest()->first();

        /*
        |--------------------------------------------------------------------------
        | 4. Hitung frekuensi peminjaman item yang dipilih
        |    Hanya borrowing completed yang dihitung
        |--------------------------------------------------------------------------
        */
        $frekuensi_pakai = 0;

        if ($selected_tool) {
            $frekuensi_pakai = DB::table('borrowing_items')
                ->join('borrowings', 'borrowings.id', '=', 'borrowing_items.borrowing_id')
                ->where('borrowings.status', 'COMPLETED')
                ->where('borrowing_items.item_id', $selected_tool->id)
                ->sum('borrowing_items.qty');
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Hitung rata-rata penggunaan per hari item yang dipilih
        |--------------------------------------------------------------------------
        */
        $avgUsageSelected = 0;

        if ($selected_tool) {
            $usageDays = DB::table('borrowing_items')
                ->join('borrowings', 'borrowings.id', '=', 'borrowing_items.borrowing_id')
                ->where('borrowings.status', 'COMPLETED')
                ->where('borrowing_items.item_id', $selected_tool->id)
                ->whereNotNull('borrowings.borrow_date')
                ->select(
                    'borrowings.borrow_date',
                    DB::raw('SUM(borrowing_items.qty) as total_qty')
                )
                ->groupBy('borrowings.borrow_date')
                ->get();

            if ($usageDays->count() > 0) {
                $avgUsageSelected = $usageDays->avg('total_qty');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 6. Prediksi habis versi stok sederhana
        |    Ini bukan CatBoost, ini prediksi stok berdasarkan rata-rata historis.
        |    AI CatBoost tetap ditampilkan di widget AI terpisah.
        |--------------------------------------------------------------------------
        */
        $prediksi_habis = '-';
        $sisa_hari = 999;
        $status_stok = '-';

        if ($selected_tool) {
            if ($selected_tool->stock <= 0) {
                $prediksi_habis = 'Habis';
                $sisa_hari = 0;
                $status_stok = 'Habis';
            } elseif ($avgUsageSelected > 0) {
                $sisa_hari = (int) ceil($selected_tool->stock / $avgUsageSelected);
                $prediksi_habis = now()->addDays($sisa_hari)->format('Y-m-d');

                if ($selected_tool->stock <= $selected_tool->minimum_stock) {
                    $status_stok = 'Kritis';
                } elseif ($sisa_hari <= 7) {
                    $status_stok = 'Kritis';
                } elseif ($sisa_hari <= 14) {
                    $status_stok = 'Peringatan';
                } else {
                    $status_stok = 'Aman';
                }
            } else {
                $prediksi_habis = 'Tidak terprediksi';
                $sisa_hari = 999;

                if ($selected_tool->stock <= $selected_tool->minimum_stock) {
                    $status_stok = 'Peringatan';
                } else {
                    $status_stok = 'Aman';
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 7. Data line chart item yang dipilih
        |    Chart lama tetap dipakai:
        |    - Jumlah unit dipinjam
        |    - Jumlah unit dikembalikan
        |--------------------------------------------------------------------------
        */
        $chartLabels = [];
        $chartPinjam = [];
        $chartKembali = [];

        if ($selected_tool) {
            $chartRows = DB::table('borrowing_items')
                ->join('borrowings', 'borrowings.id', '=', 'borrowing_items.borrowing_id')
                ->where('borrowings.status', 'COMPLETED')
                ->where('borrowing_items.item_id', $selected_tool->id)
                ->whereNotNull('borrowings.borrow_date')
                ->select(
                    'borrowings.borrow_date',
                    DB::raw('SUM(borrowing_items.qty) as total_pinjam'),
                    DB::raw('SUM(borrowing_items.returned_qty) as total_kembali')
                )
                ->groupBy('borrowings.borrow_date')
                ->orderBy('borrowings.borrow_date')
                ->get();

            $chartLabels = $chartRows
                ->pluck('borrow_date')
                ->map(function ($date) {
                    return Carbon::parse($date)->format('d M');
                })
                ->toArray();

            $chartPinjam = $chartRows
                ->pluck('total_pinjam')
                ->map(fn ($value) => (int) $value)
                ->toArray();

            $chartKembali = $chartRows
                ->pluck('total_kembali')
                ->map(fn ($value) => (int) $value)
                ->toArray();
        }

        /*
        |--------------------------------------------------------------------------
        | 8. Pie chart distribusi per kategori bulan ini
        |--------------------------------------------------------------------------
        */
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $categoryRows = DB::table('borrowing_items')
            ->join('borrowings', 'borrowings.id', '=', 'borrowing_items.borrowing_id')
            ->join('items', 'items.id', '=', 'borrowing_items.item_id')
            ->where('borrowings.status', 'COMPLETED')
            ->whereMonth('borrowings.borrow_date', $currentMonth)
            ->whereYear('borrowings.borrow_date', $currentYear)
            ->select(
                'items.type',
                DB::raw('SUM(borrowing_items.qty) as total')
            )
            ->groupBy('items.type')
            ->get();

        $pieLabels = $categoryRows->pluck('type')->toArray();

        $pieData = $categoryRows
            ->pluck('total')
            ->map(fn ($value) => (int) $value)
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | 9. Bar chart penggunaan bulanan semua barang
        |--------------------------------------------------------------------------
        */
        $monthlyData = DB::table('borrowing_items')
            ->join('borrowings', 'borrowings.id', '=', 'borrowing_items.borrowing_id')
            ->where('borrowings.status', 'COMPLETED')
            ->whereNotNull('borrowings.borrow_date')
            ->select(
                DB::raw("DATE_FORMAT(borrowings.borrow_date, '%Y-%m') as bulan"),
                DB::raw('SUM(borrowing_items.qty) as total')
            )
            ->groupBy(DB::raw("DATE_FORMAT(borrowings.borrow_date, '%Y-%m')"))
            ->orderBy('bulan')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 10. Tempelkan data prediksi AI ke setiap item untuk tabel ringkasan
        |--------------------------------------------------------------------------
        */
        $aiPredictions = AiItemPrediction::query()
            ->get()
            ->keyBy('item_id');

        /*
        |--------------------------------------------------------------------------
        | 11. Hitung ringkasan stok setiap item
        |     Variabel lama pada blade tetap tersedia:
        |     avg_usage, prediction_date, remaining_days
        |--------------------------------------------------------------------------
        */
        $all_tools = $all_tools->map(function ($item) use ($aiPredictions) {
            $ai = $aiPredictions->get($item->id);

            $usageRows = DB::table('borrowing_items')
                ->join('borrowings', 'borrowings.id', '=', 'borrowing_items.borrowing_id')
                ->where('borrowings.status', 'COMPLETED')
                ->where('borrowing_items.item_id', $item->id)
                ->whereNotNull('borrowings.borrow_date')
                ->select(
                    'borrowings.borrow_date',
                    DB::raw('SUM(borrowing_items.qty) as total_qty')
                )
                ->groupBy('borrowings.borrow_date')
                ->get();

            $avgUsage = 0;

            if ($usageRows->count() > 0) {
                $avgUsage = $usageRows->avg('total_qty');
            }

            $remainingDays = 999;
            $predictionDate = '-';

            if ($item->stock <= 0) {
                $remainingDays = 0;
                $predictionDate = 'Habis';
            } elseif ($avgUsage > 0) {
                $remainingDays = (int) ceil($item->stock / $avgUsage);
                $predictionDate = now()->addDays($remainingDays)->format('d M Y');
            }

            $item->avg_usage = $avgUsage;
            $item->remaining_days = $remainingDays;
            $item->prediction_date = $predictionDate;

            $item->ai_prediction = $ai;

            return $item;
        });

        /*
        |--------------------------------------------------------------------------
        | 12. Evaluasi model barang
        |--------------------------------------------------------------------------
        */
        $barangEvaluation = AiModelEvaluation::query()
            ->where('model_type', 'barang')
            ->latest()
            ->first();

        /*
        |--------------------------------------------------------------------------
        | 13. Kirim ke blade lama
        |--------------------------------------------------------------------------
        */
        return view('admin.analytics.alat-bahan', compact(
            'all_tools',
            'selected_tool',
            'frekuensi_pakai',
            'prediksi_habis',
            'sisa_hari',
            'status_stok',
            'chartLabels',
            'chartPinjam',
            'chartKembali',
            'pieLabels',
            'pieData',
            'monthlyData',
            'aiPrediction',
            'lastAiRun',
            'barangEvaluation'
        ));
    }

    public function ruangan(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Data master ruangan dan ruangan terpilih
        |--------------------------------------------------------------------------
        */
        $all_rooms = Room::query()
            ->orderBy('name')
            ->get();

        $selected_room = null;

        if ($request->filled('room_id')) {
            $selected_room = Room::query()
                ->where('id', $request->room_id)
                ->first();
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Data AI untuk ruangan terpilih
        |--------------------------------------------------------------------------
        */
        $aiRoomSummary = null;
        $aiRoomSlots = collect();
        $aiRoomAnomalies = collect();

        if ($selected_room) {
            $aiRoomSummary = AiRoomPredictionSummary::query()
                ->where('room_id', $selected_room->id)
                ->first();

            $aiRoomSlots = AiRoomSlotPrediction::query()
                ->where('room_id', $selected_room->id)
                ->orderBy('jam_slot')
                ->get();

            $aiRoomAnomalies = AiRoomAnomaly::query()
                ->where('room_id', $selected_room->id)
                ->orderBy('anomaly_score')
                ->orderByDesc('date')
                ->take(10)
                ->get();
        } else {
            $aiRoomAnomalies = AiRoomAnomaly::query()
                ->orderBy('anomaly_score')
                ->orderByDesc('date')
                ->take(10)
                ->get();
        }

        $lastAiRun = AiRun::latest()->first();

        $roomEvaluation = AiModelEvaluation::query()
            ->where('model_type', 'ruangan')
            ->latest()
            ->first();

        $anomalyEvaluation = AiModelEvaluation::query()
            ->where('model_type', 'anomali')
            ->latest()
            ->first();

        /*
        |--------------------------------------------------------------------------
        | 3. Total peminjaman ruangan
        |--------------------------------------------------------------------------
        */
        $total_peminjaman = 0;

        if ($selected_room) {
            $total_peminjaman = Borrowing::query()
                ->where('status', 'COMPLETED')
                ->where('room_id', $selected_room->id)
                ->count();
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Okupansi 30 hari terakhir
        |    Jika ruangan dipilih:
        |    okupansi = peminjaman ruangan itu / semua peminjaman ruangan 30 hari
        |--------------------------------------------------------------------------
        */
        $totalAllRoomsLast30 = Borrowing::query()
            ->where('status', 'COMPLETED')
            ->where('borrow_date', '>=', now()->subDays(30))
            ->count();

        $selectedRoomLast30 = 0;

        if ($selected_room) {
            $selectedRoomLast30 = Borrowing::query()
                ->where('status', 'COMPLETED')
                ->where('borrow_date', '>=', now()->subDays(30))
                ->where('room_id', $selected_room->id)
                ->count();
        }

        $occupancy = $totalAllRoomsLast30 > 0
            ? round(($selectedRoomLast30 / $totalAllRoomsLast30) * 100, 1)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | 5. Jam terpadat historis
        |--------------------------------------------------------------------------
        */
        $jam_terpadat = '-';

        if ($selected_room) {
            $peakSlot = Borrowing::query()
                ->where('status', 'COMPLETED')
                ->where('room_id', $selected_room->id)
                ->whereNotNull('time_slot')
                ->select('time_slot', DB::raw('COUNT(*) as total'))
                ->groupBy('time_slot')
                ->orderByDesc('total')
                ->first();

            if ($peakSlot) {
                $jam_terpadat = $peakSlot->time_slot;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 6. Durasi rata-rata
        |--------------------------------------------------------------------------
        */
        $rata_rata_durasi = 0;

        if ($selected_room) {
            $borrowingsForDuration = Borrowing::query()
                ->where('status', 'COMPLETED')
                ->where('room_id', $selected_room->id)
                ->whereNotNull('time_slot')
                ->get();

            $durations = $borrowingsForDuration->map(function ($borrowing) {
                [$start, $end] = $this->parseTimeSlotToDecimal($borrowing->time_slot);

                if ($start === null || $end === null || $end <= $start) {
                    return null;
                }

                return $end - $start;
            })->filter();

            if ($durations->count() > 0) {
                $rata_rata_durasi = round($durations->avg(), 1);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 7. Tren peminjaman 30 hari terakhir untuk line chart
        |--------------------------------------------------------------------------
        */
        $chartLabels = [];
        $chartData = [];

        if ($selected_room) {
            $startDate = now()->subDays(29)->startOfDay();

            $rawTrend = Borrowing::query()
                ->where('status', 'COMPLETED')
                ->where('room_id', $selected_room->id)
                ->where('borrow_date', '>=', $startDate)
                ->select('borrow_date', DB::raw('COUNT(*) as total'))
                ->groupBy('borrow_date')
                ->orderBy('borrow_date')
                ->get()
                ->keyBy(function ($row) {
                    return Carbon::parse($row->borrow_date)->format('Y-m-d');
                });

            for ($i = 0; $i < 30; $i++) {
                $date = $startDate->copy()->addDays($i)->format('Y-m-d');

                $chartLabels[] = $date;
                $chartData[] = (int) ($rawTrend->get($date)->total ?? 0);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 8. Distribusi pemakaian per ruangan 30 hari terakhir
        |--------------------------------------------------------------------------
        */
        $roomUsage = Borrowing::query()
            ->where('status', 'COMPLETED')
            ->where('borrow_date', '>=', now()->subDays(30))
            ->selectRaw('room_id, COUNT(*) as total')
            ->groupBy('room_id')
            ->with('room')
            ->get()
            ->map(fn ($b) => [
                'name' => $b->room->name ?? 'Tidak diketahui',
                'total' => $b->total,
            ])
            ->sortByDesc('total')
            ->values();

        $totalRoomUsage = $roomUsage->sum('total');

        /*
        |--------------------------------------------------------------------------
        | 9. Distribusi jam penggunaan historis
        |--------------------------------------------------------------------------
        */
        $timeSlotLabels = [];
        $timeSlotData = [];
        $timeSlotPeakHour = null;

        if ($selected_room) {
            $timeSlotRows = Borrowing::query()
                ->where('status', 'COMPLETED')
                ->where('room_id', $selected_room->id)
                ->whereNotNull('time_slot')
                ->select('time_slot', DB::raw('COUNT(*) as total'))
                ->groupBy('time_slot')
                ->orderBy('time_slot')
                ->get();

            $timeSlotLabels = $timeSlotRows->pluck('time_slot')->toArray();
            $timeSlotData = $timeSlotRows->pluck('total')->map(fn ($v) => (int) $v)->toArray();

            $peakSlot = $timeSlotRows->sortByDesc('total')->first();

            if ($peakSlot) {
                [$start] = $this->parseTimeSlotToDecimal($peakSlot->time_slot);
                $timeSlotPeakHour = $start !== null ? (int) floor($start) : null;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 10. Kepadatan per hari
        |--------------------------------------------------------------------------
        */
        $dayQuery = Borrowing::query()
            ->where('status', 'COMPLETED')
            ->where('borrow_date', '>=', now()->subDays(30))
            ->whereNotNull('day');

        if ($selected_room) {
            $dayQuery->where('room_id', $selected_room->id);
        }

        $dayRaw = $dayQuery
            ->selectRaw('day, COUNT(*) as total')
            ->groupBy('day')
            ->get()
            ->keyBy('day');

        $dayOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $dayLabels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $dayTotals = array_map(fn ($d) => (int) ($dayRaw->get($d)?->total ?? 0), $dayOrder);

        /*
        |--------------------------------------------------------------------------
        | 11. Riwayat peminjaman terbaru
        |--------------------------------------------------------------------------
        */
        $recentQuery = Borrowing::query()
            ->with(['room', 'user'])
            ->where('status', 'COMPLETED')
            ->orderByDesc('created_at')
            ->limit(5);

        if ($selected_room) {
            $recentQuery->where('room_id', $selected_room->id);
        }

        $recentBorrowings = $recentQuery->get();

        /*
        |--------------------------------------------------------------------------
        | 12. Chart AI forecasting slot
        |--------------------------------------------------------------------------
        */
        $aiSlotLabels = $aiRoomSlots
            ->pluck('time_slot')
            ->map(fn ($v) => $v ?: '-')
            ->toArray();

        $aiSlotProbabilities = $aiRoomSlots
            ->pluck('probability_used')
            ->map(fn ($v) => round((float) $v * 100, 1))
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | 13. Status badge helper
        |--------------------------------------------------------------------------
        */
        $statusMap = [
            'approved' => ['label' => 'approved', 'icon' => '◷', 'class' => 'badge-approved'],
            'completed' => ['label' => 'completed', 'icon' => '✓', 'class' => 'badge-completed'],
            'returned' => ['label' => 'returned', 'icon' => '✓', 'class' => 'badge-returned'],
            'waiting_return' => ['label' => 'waiting_return', 'icon' => '⏳', 'class' => 'badge-waiting_return'],
            'pending' => ['label' => 'Pending', 'icon' => '⏳', 'class' => 'badge-pending'],
            'rejected' => ['label' => 'rejected', 'icon' => '✕', 'class' => 'badge-rejected'],
            'cancelled' => ['label' => 'cancelled', 'icon' => '✕', 'class' => 'badge-cancelled'],
        ];

        return view('admin.analytics.ruangan', compact(
            'all_rooms',
            'selected_room',
            'total_peminjaman',
            'occupancy',
            'jam_terpadat',
            'rata_rata_durasi',
            'chartLabels',
            'chartData',
            'roomUsage',
            'totalRoomUsage',
            'timeSlotLabels',
            'timeSlotData',
            'timeSlotPeakHour',
            'dayLabels',
            'dayTotals',
            'recentBorrowings',
            'statusMap',
            'aiRoomSummary',
            'aiRoomSlots',
            'aiRoomAnomalies',
            'lastAiRun',
            'roomEvaluation',
            'anomalyEvaluation',
            'aiSlotLabels',
            'aiSlotProbabilities'
        ));
    }

    private function parseTimeSlotToDecimal(?string $timeSlot): array
    {
        if (!$timeSlot) {
            return [null, null];
        }

        $timeSlot = str_replace(':', '.', trim($timeSlot));
        $parts = preg_split('/\s*-\s*/', $timeSlot);

        if (count($parts) !== 2) {
            return [null, null];
        }

        $parse = function ($value) {
            $value = trim($value);

            if (!preg_match('/^(\d{1,2})(?:\.(\d{1,2}))?$/', $value, $matches)) {
                return null;
            }

            $hour = (int) $matches[1];
            $minute = isset($matches[2]) ? (int) $matches[2] : 0;

            return $hour + ($minute / 60);
        };

        return [$parse($parts[0]), $parse($parts[1])];
    }
}