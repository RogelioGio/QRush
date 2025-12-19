<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Resources\KdsOrderResource;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Tables;
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
            'table_id' => 'required|integer|exists:tables,id',
            'status' => 'required|string',
            'order_items' => 'array|min:1',
        ]);

        $table = Tables::find($request->input('table_id'));

        if(!$table->is_active) {
            return response()->json(['error' => 'Cannot place order for an inactive table.'], 400);
        }

        $openSession = $table->tableSessions()->where('status', 'open')->latest('opened_at')->first();
        if(!$openSession) {
            return response()->json(['error' => 'Cannot place order for a table without an active session.'], 400);
        }

        $order = Order::create(
            [
                'table_id' => $validated['table_id'],
                'status' => $validated['status'],
                'table_session_id' => $openSession->id,
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

        return response()->json(['order' => $order->load('orderItems')], 201);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $newStatus = $request->validated('status');

        if(! $order->canTransitionTo($newStatus)) {
            return response()->json(['error' => 'Invalid status transition.'], 400);
        }

        $order->status = $newStatus;
        $order->save();

        return response()->json([
            'message' => 'Order status updated successfully.',
            'order' => $order
        ]);
    }

    public function summary(Request $request){
        $query = Order::with(['orderItems', 'orderItems.menuItem'])->orderBy('created_at', 'desc');

        if($request->filled('status')){
            $query->where('status', $request->status);
        };

        $orders = $query->get();
        $total_orders = $orders->count();

        $orderSummaries = [];
        foreach($orders as $order){

            $orderSummaries[] = [
                'order_id' => $order->id,
                'table_id' => $order->table_id,
                'status' => $order->status,
                'created_at' => $order->created_at,
                'items' => $order->orderItems->count(),
                'total_price' => $order->getTotalPrice,
            ];
        }

        return response()->json([
            'order' => $orderSummaries,
            'total_orders' => $total_orders,
        ]);
    }

    //KDS Endpoints
    public function kdsOrders(Request $request)
    {
        $query = Order::orderBy('created_at', 'asc');

        if($request->filled('status')){
            $query->where('status', $request->status);
        }else{
            $query->whereIn('status', ['confirmed', 'preparing']);
        };
        $orders = $query->get();



        return response()->json(['orders' => KdsOrderResource::collection($orders)]);
    }

    public function updateKdsOrderStatus(Order $order, UpdateOrderStatusRequest $status)
    {
        $validatedStatus = $status->validated();

        if($order->tableSession->status == 'closed'){
            return response()->json(['error' => 'Cannot update order status for a closed table session'], 400);
        }

        if($order->status !== 'confirmed'){
            return response()->json(['error' => 'Only orders with status confirmed can be updated'], 400);
        }

        if(! $order->canTransitionTo($validatedStatus['status'])) {
            return response()->json(['error' => 'Invalid status transition.'], 400);
        }

        $order->status = $validatedStatus['status'];
        $order->save();

        return response()->json([
            'message' => 'KDS Order status updated successfully.',
            'order' => new KdsOrderResource($order)
        ]);

    }
}
