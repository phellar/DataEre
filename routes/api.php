<?php

use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// public endpoints
Route::post('login', [AuthController::class, 'handleLogin' ]);
Route::post('register', [AuthController::class, 'handleRegister' ]);



// Protected endpoints
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/upload', [FileController::class, 'upload']);
    Route::get('/file/{id}/report', [ReportController::class, 'generate']);
    Route::get('/file/{id}/dashboard', [DashboardController::class, 'show']);
});