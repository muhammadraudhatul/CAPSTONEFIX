<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItemHistory;

class ItemHistoryController extends Controller
{
    public function index()
    {
        $histories = ItemHistory::latest()
            ->paginate(20);

        return view(
            'admin.item-histories.index',
            compact('histories')
        );
    }
}