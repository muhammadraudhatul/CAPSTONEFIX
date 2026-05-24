<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;

class DashboardController extends Controller
{
    public function index()
    {
        $activeBorrowings = Borrowing::with([
                'room',
                'items.item',
            ])
            ->where(
                'user_id',
                auth()->id()
            )
            ->whereIn('status', [

                'PENDING',
                'APPROVED',
                'WAITING_RETURN',

            ])
            ->latest()
            ->get();

        $histories = Borrowing::with([
                'room',
                'items.item',
            ])
            ->where(
                'user_id',
                auth()->id()
            )
            ->whereIn('status', [

                'COMPLETED',
                'REJECTED',
                'CANCELLED',

            ])
            ->latest()
            ->get();

        return view(
            'student.dashboard',
            compact(
                'activeBorrowings',
                'histories'
            )
        );
    }
}