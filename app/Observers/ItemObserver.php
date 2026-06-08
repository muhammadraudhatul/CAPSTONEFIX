<?php

namespace App\Observers;

use App\Models\Item;
use App\Models\ItemHistory;

class ItemObserver
{
    /**
     * CREATE
     */
    public function created(Item $item): void
    {
        ItemHistory::create([

            'item_id' => $item->id,

            'user_id' => auth()->id(),

            'action' => 'create',

            'item_name' => $item->name,

            'old_stock'   => 0,

            'new_stock' => $item->stock,

            'description' =>
                'Menambahkan item baru',

        ]);
    }

    /**
     * UPDATE
     */
    public function updated(Item $item): void
    {
        ItemHistory::create([

            'item_id' => $item->id,

            'user_id' => auth()->id(),

            'action' => 'update',

            'item_name' => $item->name,

            'old_stock' => $item->getOriginal('stock'),

            'new_stock' => $item->stock,

            'description' =>
                'Mengubah data item',

        ]);
    }

    /**
     * DELETE
     */
    public function deleted(Item $item): void
    {
        ItemHistory::create([

            'user_id' => auth()->id(),

            'action' => 'delete',

            'item_name' => $item->name,

            'new_stock'   => 0,

            'old_stock' => $item->stock,

            'description' =>
                'Menghapus item',

        ]);
    }
}