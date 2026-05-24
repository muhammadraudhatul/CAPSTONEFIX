<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItemHistory;
use App\Exports\ItemHistoriesExport;
use Maatwebsite\Excel\Facades\Excel;

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
    
    public function exportExcel()
    {
        return Excel::download(
            new ItemHistoriesExport,
            'inventory-history.xlsx'
        );
    }

    public function exportCsv()
    {
        return Excel::download(
            new ItemHistoriesExport,
            'inventory-history.csv'
        );
    }

}