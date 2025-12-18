<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Tables;
use Illuminate\Http\Request;

class TablesController extends Controller
{
    public function index(){
        $tables = Tables::orderBy('table_number', 'asc')->get();

        return response()->json($tables);
    }

    public function store(Request $request){
        $request->validate([
            'table_number' => 'required|integer|unique:tables,table_number',
            'is_active' => 'boolean',
        ]);

        $table = Tables::create([
            'table_number' => $request->table_number,
            'is_active' => $request->input('is_active', true),
        ]);

        return response()->json($table, 201);
    }

    public function availability(Tables $table, Request $request){

        if ($table->hasActiveOrder) {
            return response()->json([
                'error' => 'Cannot change availability of a table with active orders.'
            ], 400);
        }

        $status = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $table->update(['is_active' => $status['is_active']]);

        return response()->json([
            'message' => 'Table availability updated successfully.',
            'table' => $table
        ]);
    }

    public function summary(){
        $tables = Tables::orderBy('table_number', 'asc')->get();
        $totalTables = Tables::count();
        $occupiedTables = Tables::where('is_active', true)->whereHas('orders', function ($query) {
            $query->whereIn('status', Order::ActiveStatuses);
        })->count();
        $availableTables = Tables::where('is_active', false)->whereHas('orders',function($query){

            $query->whereNotIn('status', Order::ActiveStatuses);
        })->count();

        foreach ($tables as $table) {
            $table->active_order_count = $table->orders()
                ->whereIn('status', Order::ActiveStatuses)->count();
            $table->latest_order_status = $table->orders()->latest('created_at')->value('status');
        }


        return response()->json([
            'table' => $tables,
            'total_tables' => $totalTables,
            'occupied_tables' => $occupiedTables,
            'available_tables' => $availableTables,
        ]);
    }
}
