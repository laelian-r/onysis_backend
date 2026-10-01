<?php

use App\Http\Controllers\ReleasesController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [UsersController::class, "register"]);
Route::post('/login', [UsersController::class, "login"]);
Route::post('/logout', [UsersController::class, "logout"])->middleware('auth:sanctum');

Route::post('/forgot-password', [UsersController::class, 'forgotPassword']);
Route::post('/reset-password', [UsersController::class, 'resetPassword']);


// Routes publiques (Accessibles sans authentification)
Route::get('/releases', [ReleasesController::class, 'index']);
Route::get('/releases/{release}', [ReleasesController::class, 'show']);

// Routes protégées (Utilisateur connecté requis)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/releases', [ReleasesController::class, 'store']);
    Route::put('/releases/{release}', [ReleasesController::class, 'update']); // ou Route::patch
    Route::delete('/releases/{release}', [ReleasesController::class, 'destroy']);
});
