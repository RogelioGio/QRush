<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\TableSessions;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function preview(TableSessions $tableSession){

        $openSession = $tableSession->status === 'open';
        if(!$openSession){
            return response()->json(['message' => 'Cannot preview billing for a closed table session.'], 400);
        }

        if($tableSession->orders()->whereIn('status', Order::ActiveStatuses)->exists()){
            return response()->json(['message' => 'Cannot preview billing with active orders present.'], 400);
        }


        return response()->json([
            "table_session_id" => $tableSession->id,
            "table_id" => $tableSession->table_id,
            "total_orders"=> $tableSession->orders()->where('status','served')->count(),
            "total_items" => $tableSession->orders()->where('status','served')->get()->sum(fn($order) => $order->orderItems->sum('quantity')),
            "total_amount" => $tableSession->orderItems()->whereHas('order', fn($query) => $query->where('status','served'))->get()->sum(fn($item) => $item->price_snapshot * $item->quantity),
        ]);
    }

    public function finalize(TableSessions $tableSession){

        $openSession = $tableSession->status === 'open';
        if(!$openSession){
            return response()->json(['message' => 'Cannot provide billing for a closed table session.'], 400);
        }

        if($tableSession->orders()->whereIn('status', Order::ActiveStatuses)->exists()){
            return response()->json(['message' => 'Cannot preview billing with active orders present.'], 400);
        }

        $tableSession->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return response()->json([
            "table_session_id" => $tableSession->id,
            "final_total" => $tableSession->orderItems()->whereHas('order', fn($query) => $query->where('status','served'))->get()->sum(fn($item) => $item->price_snapshot * $item->quantity),
            "closed_at" => $tableSession->closed_at,
        ]);

    }
}
