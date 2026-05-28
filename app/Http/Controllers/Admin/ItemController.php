<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Room;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $search = $request->search;

        $items = Item::with('room')

            ->when($search, function ($query) use ($search) {

                $query->where(function ($q) use ($search) {

                    $q->where(
                        'name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'location',
                        'like',
                        "%{$search}%"
                    );

                });

            })

            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | GROUPING
        |--------------------------------------------------------------------------
        */

        $tools = $items
            ->where('type', 'tool')
            ->groupBy('name');

        $materials = $items
            ->where('type', 'material')
            ->groupBy('name');

        return view(
            'admin.items.index',
            compact(
                'tools',
                'materials',
                'search'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $rooms = Room::all();

        return view(
            'admin.items.create',
            compact('rooms')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([

            'name' => 'required',

            'type' => 'required',

            'room_id' => 'required',

            'location' => 'required',

            'unit' => 'required',

            'stock' =>
                'required|integer|min:0',

            'minimum_stock' =>
                'required|integer|min:0',

        ]);

        /*
        |--------------------------------------------------------------------------
        | DUPLICATE CHECK
        |--------------------------------------------------------------------------
        |
        | Sama persis:
        | - nama
        | - room
        | - lokasi
        |
        | Maka stock ditambahkan saja
        |
        */

        $existingItem = Item::where(

                'name',
                $request->name

            )
            ->where(
                'room_id',
                $request->room_id
            )
            ->where(
                'location',
                $request->location
            )
            ->first();

        /*
        |--------------------------------------------------------------------------
        | UPDATE STOCK
        |--------------------------------------------------------------------------
        */

        if ($existingItem)
        {
            $existingItem->increment(

                'stock',

                $request->stock

            );

            return redirect()
                ->route('items.index')
                ->with([

                    'success' =>
                        'Stock item berhasil ditambahkan.'

                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE NEW
        |--------------------------------------------------------------------------
        */

        Item::create([

            'name' =>
                $request->name,

            'type' =>
                $request->type,

            'room_id' =>
                $request->room_id,

            'location' =>
                $request->location,

            'unit' =>
                $request->unit,

            'stock' =>
                $request->stock,

            'minimum_stock' =>
                $request->minimum_stock,

            'description' =>
                $request->description,

        ]);

        return redirect()
            ->route('items.index')
            ->with([

                'success' =>
                    'Item berhasil ditambahkan.'

            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(Item $item)
    {
        $rooms = Room::all();

        return view(
            'admin.items.edit',
            compact(
                'item',
                'rooms'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Item $item
    ) {
        $request->validate([

            'name' => 'required',

            'type' => 'required',

            'room_id' => 'required',

            'location' => 'required',

            'unit' => 'required',

            'stock' =>
                'required|integer|min:0',

            'minimum_stock' =>
                'required|integer|min:0',

        ]);

        $item->update([

            'name' =>
                $request->name,

            'type' =>
                $request->type,

            'room_id' =>
                $request->room_id,

            'location' =>
                $request->location,

            'unit' =>
                $request->unit,

            'stock' =>
                $request->stock,

            'minimum_stock' =>
                $request->minimum_stock,

            'description' =>
                $request->description,

        ]);

        return redirect()
            ->route('items.index')
            ->with([

                'success' =>
                    'Item berhasil diperbarui.'

            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(Item $item)
    {
        $item->delete();

        return back()->with([

            'success' =>
                'Item berhasil dihapus.'

        ]);
    }
}