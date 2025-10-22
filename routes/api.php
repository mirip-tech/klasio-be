<?php

use App\Http\Controllers\Auth\AuthenticatedTokenController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ClassroomController;
use App\Http\Middleware\SetTenant;
use Illuminate\Support\Facades\Route;

Route::withoutMiddleware(['auth:sanctum', SetTenant::class])->group(function () {
    Route::post('/auth/register', [RegisteredUserController::class, 'store']);   // register user
    Route::post('/auth/token', [AuthenticatedTokenController::class, 'store']);   // generate token (login)
});

Route::delete('/auth/token', [AuthenticatedTokenController::class, 'destroy'])->withoutMiddleware(SetTenant::class); // revoke token (logout)

Route::apiResource('classroom', ClassroomController::class);
Route::post('classroom/{classroom}/enroll', [ClassroomController::class, 'enroll']);
