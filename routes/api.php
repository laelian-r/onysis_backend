<?php

use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [UsersController::class, "register"]);
Route::post('/login', [UsersController::class, "login"]);
Route::post('/logout', [UsersController::class, "logout"])->middleware('auth:sanctum');;

Route::post('/forgot-password', [UsersController::class, 'forgotPassword']);
Route::post('/reset-password', [UsersController::class, 'resetPassword']);


// Routes publiques (Accessibles sans authentification)
Route::get('/articles', [ArticlesController::class, 'index']);
Route::get('/articles/{article}', [ArticlesController::class, 'show']);

// Routes protégées (Utilisateur connecté requis)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/articles', [ArticlesController::class, 'store']);
    Route::put('/articles/{article}', [ArticlesController::class, 'update']); // ou Route::patch
    Route::delete('/articles/{article}', [ArticlesController::class, 'destroy']);
});
