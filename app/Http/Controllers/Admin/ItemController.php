<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Room;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $items = Item::with('room')

            ->when($search, function ($query) use ($search) {

                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");

            })

            ->latest()
            ->get();

        $tools = $items->where('type', 'tool');

        $materials = $items->where('type', 'material');

        return view('admin.items.index', compact(
            'tools',
            'materials',
            'search'
        ));
    }

    public function create()
    {
        $rooms = Room::all();

        return view('admin.items.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required',
            'room_id' => 'required',
            'location' => 'required',
            'unit' => 'required',
            'stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
        ]);

        Item::create($request->all());

        return redirect()
            ->route('items.index');
    }

    public function edit(Item $item)
    {
        $rooms = Room::all();

        return view('admin.items.edit', compact(
            'item',
            'rooms'
        ));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required',
            'room_id' => 'required',
            'location' => 'required',
            'unit' => 'required',
            'stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
        ]);

        $item->update($request->all());

        return redirect()
            ->route('items.index');
    }

    public function destroy(Item $item)
    {
        $item->delete();

        return back();
    }
}