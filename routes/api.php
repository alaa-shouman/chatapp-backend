<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::post('auth/logout', [App\Http\Controllers\AuthController::class, 'logout']);
    Route::apiResource('users', App\Http\Controllers\UserController::class);
});

Route::post('auth/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('auth/signup', [App\Http\Controllers\AuthController::class, 'signup']);

