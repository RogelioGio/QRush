<?php

use App\Http\Controllers\Api\MenuCategoryController;
use App\Http\Controllers\Api\MenuItemController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok'
    ]);
});


Route::prefix('v1')->group(function () {
    Route::get('menu-categories/available', [MenuCategoryController::class, 'indexActiveCategories']);
    Route::put('menu-categories/{menu_category}/activate', [MenuCategoryController::class, 'activate']);

    Route::apiResource('orders', OrderController::class);
    Route::apiResource('menu-items', MenuItemController::class);
    Route::apiResource('menu-categories', MenuCategoryController::class);
});
