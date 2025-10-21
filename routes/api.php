<?php

use App\Http\Controllers\Auth\AuthenticatedTokenController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ClassroomController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [RegisteredUserController::class, 'store']);   // register user
Route::post('/auth/token', [AuthenticatedTokenController::class, 'store']);   // generate token (login)
Route::delete('/auth/token', [AuthenticatedTokenController::class, 'destroy'])->middleware('auth:sanctum'); // revoke token (logout)

Route::middleware(['auth:sanctum', 'tenant'])->group(function () {
    Route::apiResource('classroom', ClassroomController::class);
    Route::post('classroom/{classroom}/enroll', [ClassroomController::class, 'enroll']);
});
