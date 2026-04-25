<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LaundryOrderController;
use App\Models\LaundryService;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| PROTECTED API ROUTES (TOKEN BASED)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:api')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout',  [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);


    // ✅ TAMBAHKAN INI — Orders
    Route::get('/orders',         [LaundryOrderController::class, 'indexApi']);
    Route::post('/orders',        [LaundryOrderController::class, 'store']);
    Route::put('/orders/{id}',    [LaundryOrderController::class, 'update']);
    Route::delete('/orders/{id}', [LaundryOrderController::class, 'destroy']);
});