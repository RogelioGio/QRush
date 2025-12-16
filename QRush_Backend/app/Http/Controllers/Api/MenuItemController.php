<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MenuItemRequest;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = MenuItem::orderBy('name')->get();
        return response()->json($items);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MenuItemRequest $request)
    {
        $validated = $request->validated();

        $item = MenuItem::create([
            'menu_category_id' => $validated['menu_category_id'],
            'name' => $validated['name'],
            'price' => $validated['price'],
            'is_available' => $validated['is_available'] ?? true,
        ]);

        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(MenuItem $menu_item)
    {
        return response()->json($menu_item);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MenuItemRequest $request, MenuItem $menu_item)
    {
        $validated = $request->validated();

        $menu_item->update([
            'menu_category_id' => $validated['menu_category_id'],
            'name' => $validated['name'],
            'price' => $validated['price'],
            'is_available' => $validated['is_available'],
        ]);

        return response()->json($menu_item);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MenuItem $menu_item)
    {
        $menu_item->update(['is_available' => false]);

        return response()->json([
            'message' => 'Menu item marked as unavailable successfully',
            'data' => $menu_item
        ]);
    }
}
