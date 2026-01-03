<?php

use App\Http\Controllers\Api\BillingController;
use App\Http\Controllers\Api\MenuCategoryController;
use App\Http\Controllers\Api\MenuItemController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\QRController;
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

    //Kitchen
    Route::middleware(['auth:sanctum', 'role:kitchen'])->prefix('kds')->group(function (){
        Route::get('orders', [OrderController::class, 'kdsOrders']);
        Route::patch('orders/{order}/status', [OrderController::class, 'updateKdsOrderStatus']);
    });

    //Cashier
    Route::middleware(['auth:sanctum', 'role:cashier'])->prefix('cashier')->group(function (){
        Route::get('orders/summary', [OrderController::class, 'summary']);
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus']);

        Route::post('table_sessions/{table}/open', [TableSessionsController::class, 'openSession']);
        Route::get('tables/summary', [TablesController::class, 'summary']);

        Route::get('billing/{tableSession}/preview', [BillingController::class, 'preview']);
        Route::put('billing/{tableSession}/finalize', [BillingController::class, 'finalize']);

        Route::post('billing/{tableSession}/payment', [PaymentController::class, 'openPayment']);
        Route::post('billing/{payment}/payment/confirm', [PaymentController::class, 'confirmPayment']);
    });

    //Management
    Route::middleware(['auth:sanctum', 'role:management'])->prefix('management')->group(function (){
        Route::put('menu-categories/{menu_category}/activate', [MenuCategoryController::class, 'activate']);

        Route::patch('tables/{table}/availability', [TablesController::class, 'availability']);

        Route::get('tables/summary', [TablesController::class, 'summary']);

        Route::get('reports/daily', [App\Http\Controllers\Api\ReportsController::class, 'dailySalesReport']);

        Route::apiResource('tables', TablesController::class);
        Route::apiResource('orders', OrderController::class);
        Route::apiResource('menu-items', MenuItemController::class);
        Route::apiResource('menu-categories', MenuCategoryController::class);
    });

    //General Routes
    Route::get('qr/table_sessions/{token}', [QRController::class, 'validate_table_session']);
    Route::get('qr/menu', [QRController::class, 'get_menu']);
    Route::post('qr/create_order/{token}', [QRController::class, 'create_order']);



    Route::post('orders', [OrderController::class, 'store']);
    Route::get('menu-categories/available', [MenuCategoryController::class, 'indexActiveCategories']);
    Route::patch('table_sessions/{table}/close', [TableSessionsController::class, 'closeSession']);

});

