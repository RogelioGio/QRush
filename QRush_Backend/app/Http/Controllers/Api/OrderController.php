<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['orderItems', 'orderItems.menuItem'])->orderBy('created_at', 'desc')->get();
        return response()->json(['orders' => $orders]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_id' => 'required|integer',
            'status' => 'required|string',
            'order_items' => 'array|min:1',
        ]);

        $order = Order::create(
            [
                'table_id' => $validated['table_id'],
                'status' => $validated['status'],
            ]
        );

        foreach ($validated['order_items'] as $item) {
            $menuItem = MenuItem::findOrFail($item['menu_item_id']);

            $order->orderItems()->create([
                'order_id' => $order->id,
                'menu_item_id' => $item['menu_item_id'],
                'quantity' => $item['quantity'],
                'price_snapshot' => $menuItem->price,
            ]);
        }

        return response()->json(['order' => $validated], 201);
    }
}
