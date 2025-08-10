<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TranslationController;

use App\Http\Controllers\LocaleController;


Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');
});

Route::prefix('translations')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [TranslationController::class, 'index']); 
    Route::post('/', [TranslationController::class, 'store']); 
    Route::put('/{id}', [TranslationController::class, 'update']); 
    Route::delete('/{id}', [TranslationController::class, 'destroy']);
    Route::get('/export', [TranslationController::class, 'export']); 
});

// Locale CRUD routes
Route::middleware('auth:sanctum')->prefix('locales')->group(function () {
    Route::get('/', [LocaleController::class, 'index']);
    Route::get('/{id}', [LocaleController::class, 'show']);
    Route::post('/', [LocaleController::class, 'store']);
    Route::put('/{id}', [LocaleController::class, 'update']);
    Route::delete('/{id}', [LocaleController::class, 'destroy']);
});

// Users endpoint for Swagger example
use App\Http\Controllers\UserController;
Route::middleware('auth:sanctum')->get('/users', [UserController::class, 'index']);
