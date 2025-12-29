<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\TableSessions;
use Illuminate\Http\Request;

class QRController extends Controller
{
    public function validate_table_session($token){
        $table = TableSessions::whereHas('table', function ($q) use ($token){
            $q->where('qr_token', $token);
        })->get();

        if($table->isEmpty()){
            return response()->json([
                'message' => 'No table found with the provided token.',
            ], 404);
        }

        $OpenTable = $table->where('status', 'open')->first();

        if(!$OpenTable){
            return response()->json([
                'message' => 'No valid open table session found.',
            ], 404);
        }

        return response()->json([
            'message' => 'Table session is valid.',
            'data' => $OpenTable,
        ], 200);
    }

    public function get_menu() {
        $menu = MenuCategory::where('is_active', true)->get();

        $reponse = $menu->map(function ($category) {
            return [
                'id' => $category->id,
                'category' => $category->name,
                'menu_items' => $category->items()->where('is_available', true)->get(['id', 'name' ,'price', 'is_available']),
            ];
        });

        return response()->json([
            'message' => 'Menu retrieved successfully.',
            'data' => $reponse,
        ], 200);
    }

    public function create_order($token, Request $request) {
        $openTable = TableSessions::whereHas('table', function ($q) use ($token){
            $q->where('qr_token', $token);
        })->where('status', 'open')->first();

        if(!$openTable){
            return response()->json([
                'message' => 'No valid open table session found.',
            ], 404);
        }

        $validated = $request->validate([
            'status' => 'required|string',
            'order_items' => 'array|min:1',
            'order_items.*.quantity'   => 'required|integer|min:1',
            'order_items.*.menu_item_id' => 'required|integer|exists:menu_items,id',
        ]);

        $order = Order::create(
            [
                'table_id' => $openTable->table_id,
                'status' => $validated['status'],
                'table_session_id' => $openTable->id,
            ]
        );

        foreach ($validated['order_items'] as $item) {
            $menuItem = MenuItem::where('id', $item['menu_item_id'])->where('is_available', true)->first();

            if(!$menuItem) {
                return response()->json(['error' => 'Menu item with named' . $item['menu_item_id'] . ' is not available.'], 400);
            }

            $order->orderItems()->create([
                'order_id' => $order->id,
                'menu_item_id' => $item['menu_item_id'],
                'quantity' => $item['quantity'],
                'price_snapshot' => $menuItem->price,
            ]);
        }

        return response()->json(['order' => $order->load('orderItems')], 201);

    }

}
