<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\TableSessions;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function openPayment(Request $request,TableSessions $tableSession)
    {
        $request->validate([
        'payment_method' => 'required|string',
        'reference_no' => 'nullable|string',
    ]);

        $openSession = $tableSession->status === 'open';
        if(!$openSession){
            return response()->json(['message' => 'Cannot open payment for a closed table session.'], 400);
        }

        if($tableSession->orders()->whereIn('status', Order::ActiveStatuses)->exists()){
            return response()->json(['message' => 'Cannot open payment with active orders present.'], 400);
        }

        if($tableSession->payment()->exists()){
            return response()->json(['message' => 'Payment already exists for this table session.'], 400);
        }

        $tablePayment = Payment::create([
            'table_session_id' => $tableSession->id,
            'amount' => $tableSession->orderItems()->whereHas('order', fn($query) => $query->where('status','served'))->get()->sum(fn($item) => $item->price_snapshot * $item->quantity),
            'payment_method' => $request->input('payment_method'),
            'status' => 'pending',
            'paid_at' => null,
            'reference_no' => $request->input('reference_no'),
        ]);

        return response()->json([
            "payment_id" => $tablePayment->id,
            "table_session_id" => $tableSession->id,
            "amount" => $tablePayment->amount,
            "payment_method" => $tablePayment->payment_method,
            "status" => $tablePayment->status,
            "reference_no" => $tablePayment->reference_no,
        ]);
    }

    public function confirmPayment(Payment $payment)
    {
        if($payment->status !== 'pending'){
            return response()->json(['message' => 'Only pending payments can be confirmed.'], 400);
        }

        $openSession = $payment->tableSession->status !== 'open';
        if($openSession){
            return response()->json(['message' => 'Cannot confirm payment for a closed table session.'], 400);
        }

        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $tableSession = $payment->tableSession;
        $tableSession->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return response()->json([
            "payment" => [
                            "payment_id" => $payment->id,
                            "table_session_id" => $tableSession->id,
                            "amount" => $payment->amount,
                            "payment_method" => $payment->payment_method,
                            "status" => $payment->status,
                            "paid_at" => $payment->paid_at,
                            "reference_no" => $payment->reference_no,
            ],
            "table_session" => [
                "table_session_id" => $tableSession->id,
                "final_total" => $payment->amount,
                "closed_at" => $tableSession->closed_at,
            ]
        ]);
    }
}
