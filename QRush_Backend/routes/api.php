<?php

use App\Http\Controllers\Api\MenuCategoryController;
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
    Route::apiResource('menu-categories', MenuCategoryController::class);
    Route::put('menu-categories/{menu_category}/activate', [MenuCategoryController::class, 'activate']);
});
