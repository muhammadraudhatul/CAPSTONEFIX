<?php

namespace App\Exports;

use App\Models\ItemHistory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ItemHistoriesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return ItemHistory::latest()
            ->get()
            ->map(function ($history) {

                return [

                    'item_name' => $history->item_name,

                    'action' => $history->action,

                    'old_stock' => $history->old_stock,

                    'new_stock' => $history->new_stock,

                    'description' => $history->description,

                    'created_at' =>
                        $history->created_at
                            ->format('d M Y H:i'),

                ];

            });
    }

    public function headings(): array
    {
        return [

            'Item',

            'Action',

            'Old Stock',

            'New Stock',

            'Description',

            'Date',

        ];
    }
}