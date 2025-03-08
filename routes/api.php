<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TranslationController;
use App\Http\Controllers\Api\AuthController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::middleware('cors')->group(function () {
    // Public route - login with throttle limit
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:60,1'); // 60 requests per minute

    // Protected routes with authentication
    Route::middleware('auth:sanctum')->group(function () {
        // Throttle for authenticated users (e.g., 100 requests per minute)
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('throttle:100,1');

        // Translations CRUD operations with throttle
        Route::prefix('/translations')->group(function () {
            Route::get('/', [TranslationController::class, 'index'])->middleware('throttle:100,1');
            Route::get('/export/{localeCode}', [TranslationController::class, 'export'])->middleware('throttle:20,1'); // Lower throttle for resource-intensive export route
            Route::get('/{id}', [TranslationController::class, 'show'])->middleware('throttle:100,1');
            Route::post('/', [TranslationController::class, 'store'])->middleware('throttle:100,1');
            Route::put('/{id}', [TranslationController::class, 'update'])->middleware('throttle:100,1');
            Route::delete('/{id}', [TranslationController::class, 'destroy'])->middleware('throttle:100,1');
        });
    });
});
