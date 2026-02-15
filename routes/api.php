<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExternalDataController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // CRUD User
    Route::apiResource('users', UserController::class);

    // External Data Search (per requirement C/D/E)
    Route::prefix('external')->group(function () {
        Route::get('/name/{name}', [ExternalDataController::class, 'searchByName']);
        Route::get('/nim/{nim}', [ExternalDataController::class, 'searchByNim']);
        Route::get('/ymd/{ymd}', [ExternalDataController::class, 'searchByYmd']);
        Route::get('/search', [ExternalDataController::class, 'search']);
    });
});
