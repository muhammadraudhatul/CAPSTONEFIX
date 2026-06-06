<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Stat Cards ────────────────────────────────────────────────────────

        // Total borrowings
        $totalBorrowings = DB::table('borrowings')->count();

        // Active users (distinct users who have a borrowing)
        $activeUsers = DB::table('borrowings')
            ->distinct('user_id')
            ->count('user_id');

        // Items in use (borrowing_items where the parent borrowing is active)
        $itemsInUse = DB::table('borrowing_items')
            ->join('borrowings', 'borrowings.id', '=', 'borrowing_items.borrowing_id')
            ->where('borrowings.status', 'active')
            ->sum('borrowing_items.qty');

        // Avg utilization: (total slots used / total slots available) * 100
        // Using room_schedules: available=1 means usable, count active borrowings per room
        $totalSlots = DB::table('room_schedules')->where('available', 1)->count();
        $usedSlots  = DB::table('borrowings')
            ->whereIn('status', ['active', 'returned'])
            ->count();
        $avgUtilization = $totalSlots > 0 ? round(($usedSlots / $totalSlots) * 100, 1) : 0;

        // Month-over-month changes (compare current month vs previous month)
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        $borrowingsThisMonth = DB::table('borrowings')
            ->where('borrow_date', '>=', $thisMonth)->count();
        $borrowingsLastMonth = DB::table('borrowings')
            ->whereBetween('borrow_date', [$lastMonth, $thisMonth])->count();

        $usersThisMonth = DB::table('borrowings')
            ->where('borrow_date', '>=', $thisMonth)->distinct('user_id')->count('user_id');
        $usersLastMonth = DB::table('borrowings')
            ->whereBetween('borrow_date', [$lastMonth, $thisMonth])->distinct('user_id')->count('user_id');

        $itemsThisMonth = DB::table('borrowing_items')
            ->join('borrowings', 'borrowings.id', '=', 'borrowing_items.borrowing_id')
            ->where('borrowings.borrow_date', '>=', $thisMonth)
            ->sum('borrowing_items.qty');
        $itemsLastMonth = DB::table('borrowing_items')
            ->join('borrowings', 'borrowings.id', '=', 'borrowing_items.borrowing_id')
            ->whereBetween('borrowings.borrow_date', [$lastMonth, $thisMonth])
            ->sum('borrowing_items.qty');

        $calcChange = function ($current, $previous) {
            if ($previous == 0) return $current > 0 ? 100.0 : 0.0;
            return round((($current - $previous) / $previous) * 100, 1);
        };

        $stats = [
            'total_borrowings'   => $totalBorrowings,
            'active_users'       => $activeUsers,
            'items_in_use'       => (int) $itemsInUse,
            'avg_utilization'    => $avgUtilization,
            'borrowings_change'  => $calcChange($borrowingsThisMonth, $borrowingsLastMonth),
            'users_change'       => $calcChange($usersThisMonth, $usersLastMonth),
            'items_change'       => $calcChange($itemsThisMonth, $itemsLastMonth),
            'utilization_change' => 5.3, // Adjust calculation as needed per your business logic
        ];

        // ── Borrowing Trends (last 6 months) ──────────────────────────────────
        $borrowingTrends = DB::table('borrowings')
            ->select(
                DB::raw("DATE_FORMAT(borrow_date, '%b') as month"),
                DB::raw("DATE_FORMAT(borrow_date, '%Y-%m') as month_sort"),
                DB::raw("COUNT(*) as total_borrowings"),
                DB::raw("SUM(CASE WHEN status = 'returned' THEN 1 ELSE 0 END) as total_returned")
            )
            ->where('borrow_date', '>=', Carbon::now()->subMonths(6)->startOfMonth())
            ->groupBy('month', 'month_sort')
            ->orderBy('month_sort')
            ->get();

        // ── Status Breakdown ──────────────────────────────────────────────────
        $statusBreakdown = DB::table('borrowings')
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->orderByDesc('count')
            ->get();

        // ── Items by Type ─────────────────────────────────────────────────────
        // Shows available stock vs borrowed qty grouped by item type
        $itemsByType = DB::table('items')
            ->select(
                'items.type',
                DB::raw('SUM(items.stock) as available'),
                DB::raw('COALESCE(SUM(bi.qty), 0) as borrowed')
            )
            ->leftJoin('borrowing_items as bi', function ($join) {
                $join->on('bi.item_id', '=', 'items.id')
                    ->whereExists(function ($q) {
                        $q->select(DB::raw(1))
                            ->from('borrowings')
                            ->whereColumn('borrowings.id', 'bi.borrowing_id')
                            ->where('borrowings.status', 'active');
                    });
            })
            ->whereNull('items.deleted_at')
            ->groupBy('items.type')
            ->orderBy('items.type')
            ->get();

        // ── Room Utilization ──────────────────────────────────────────────────
        // Percentage of available slots actually used per room
        $roomUtilization = DB::table('rooms')
            ->select(
                'rooms.name',
                DB::raw('COUNT(DISTINCT b.id) as bookings'),
                DB::raw('COUNT(DISTINCT rs.id) as total_slots'),
                DB::raw('ROUND(
                    CASE WHEN COUNT(DISTINCT rs.id) > 0
                         THEN (COUNT(DISTINCT b.id) / COUNT(DISTINCT rs.id)) * 100
                         ELSE 0 END
                , 1) as utilization_pct')
            )
            ->leftJoin('room_schedules as rs', function ($join) {
                $join->on('rs.room_id', '=', 'rooms.id')
                    ->where('rs.available', 1);
            })
            ->leftJoin('borrowings as b', function ($join) {
                $join->on('b.room_id', '=', 'rooms.id')
                    ->whereIn('b.status', ['active', 'returned']);
            })
            ->groupBy('rooms.id', 'rooms.name')
            ->orderBy('rooms.name')
            ->get();

        // ── Recent Borrowings ─────────────────────────────────────────────────
        $recentBorrowings = DB::table('borrowings')
            ->select(
                'borrowings.id',
                'users.name as user_name',
                'rooms.name as room_name',
                'borrowings.borrow_date',
                'borrowings.purpose',
                'borrowings.status'
            )
            ->join('users', 'users.id', '=', 'borrowings.user_id')
            ->join('rooms', 'rooms.id', '=', 'borrowings.room_id')
            ->orderByDesc('borrowings.created_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'borrowingTrends',
            'statusBreakdown',
            'itemsByType',
            'roomUtilization',
            'recentBorrowings'
        ));
    }
}