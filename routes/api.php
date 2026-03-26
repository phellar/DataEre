<?php

use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Auth\AuthController;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// public endpoints
Route::post('login', [AuthController::class, 'handleLogin' ]);
Route::post('register', [AuthController::class, 'handleRegister' ]);



Route::middleware('auth:sanctum')->group(function () {
    Route::post('/upload', [FileController::class, 'upload']);
});