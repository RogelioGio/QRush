<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function dailySalesReport(){
        //$date = date('Y-m-d');
        $date = "2025-12-22" ; //for testing purposes
        $totalSales = Payment::whereDate('paid_at', $date)
                        ->where('status', 'paid')->get();
        $totalTransactions = Payment::whereDate('paid_at', $date)
                            ->where('status', 'paid')
                            ->count();
        $sales_by_transaction = $totalSales->groupBy('payment_method')
                                ->map(function ($method) {
                                    return round($method->sum('amount'), 2);
                                })->sortKeys();


        return response()->json([
            'date' => $date,
            'total_sales' => round($totalSales->sum('amount'), 2),
            'total_transactions' => $totalTransactions,
            'by_payment_method' => $sales_by_transaction

        ]);

    }
}
