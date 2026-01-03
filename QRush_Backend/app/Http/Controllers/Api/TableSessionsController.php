<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Tables;
use App\Models\TableSessions;
use Illuminate\Http\Request;

class TableSessionsController extends Controller
{
    public function openSession(Tables $table)
    {
        if(! $table->is_active){
            return response()->json(['message' => 'Cannot open session on an inactive table.'], 400);
        }

        $hasSession = $table->tableSessions()->where('status', 'open')->exists();
        if ($hasSession) {
            return response()->json(['message' => 'Table session is already open.'], 400);
        }

        $table->tableSessions()->create([
            'status' => 'open',
            'opened_at' => now(),
        ]);

        return response()->json([
            'message' => 'Table session opened successfully.',
            'data' => $table->tableSessions()->latest('opened_at')->first(),
        ], 200);
    }

    public function closeSession(Tables $table)
    {
        if(! $table->is_active){
            return response()->json(['message' => 'Cannot close session on an inactive table.'], 400);
        }

        $openSession = $table->tableSessions()->where('status', 'open')->latest('opened_at')->first();

        if($openSession->orders()->whereIn('status', Order::ActiveStatuses)->exists()){
            return response()->json(['message' => 'No open table session found to close.'], 400);
        }

        if (!$openSession) {
            return response()->json(['message' => 'No open table session found to close.'], 400);
        } else {
            $openSession->update([
                'status' => 'closed',
                'closed_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Table session closed successfully.',
            'data' => $openSession,
        ], 200);

    }

    public function sessions(Request $request){

        $request->validate([
            'status' => 'in:open,closed',
        ]);

        $query = TableSessions::with('table')->orderBy('opened_at', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        $tables = $query->get()->map(function ($session) {
            $session->is_billable = $session->orders()->whereIn('status', Order::ActiveStatuses)->doesntExist();
            return $session;
        });

        return response()->json([
            'message' => 'Table sessions retrieved successfully.',
            'data' => $tables,
        ], 200);
    }

    public function getSessionDetails(TableSessions $tableSession)
    {
        $tableSession->load('table', 'orders.menuItems');

        return response()->json([
            'message' => 'Table session details retrieved successfully.',
            'data' => $tableSession,
        ], 200);
    }

}
