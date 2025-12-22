<?php

use App\Http\Controllers\Api\BillingController;
use App\Http\Controllers\Api\MenuCategoryController;
use App\Http\Controllers\Api\MenuItemController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\TablesController;
use App\Http\Controllers\Api\TableSessionsController;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Prompts\Table;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::get('menu-categories/available', [MenuCategoryController::class, 'indexActiveCategories']);
    Route::put('menu-categories/{menu_category}/activate', [MenuCategoryController::class, 'activate']);

    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus']);
    Route::get('orders/summary', [OrderController::class, 'summary']);

    Route::patch('tables/{table}/availability', [TablesController::class, 'availability']);
    Route::get('tables/summary', [TablesController::class, 'summary']);

    Route::get('kds/orders', [OrderController::class, 'kdsOrders']);
    Route::patch('kds/orders/{order}/status', [OrderController::class, 'updateKdsOrderStatus']);

    Route::post('table_sessions/{table}/open', [TableSessionsController::class, 'openSession']);
    Route::patch('table_sessions/{table}/close', [TableSessionsController::class, 'closeSession']);

    Route::get('billing/{tableSession}/preview', [BillingController::class, 'preview']);
    Route::put('billing/{tableSession}/finalize', [BillingController::class, 'finalize']);

    Route::post('billing/{tableSession}/payment', [PaymentController::class, 'openPayment']);
    Route::post('billing/{payment}/payment/confirm', [PaymentController::class, 'confirmPayment']);

    Route::apiResource('tables', TablesController::class);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('menu-items', MenuItemController::class);
    Route::apiResource('menu-categories', MenuCategoryController::class);
});
