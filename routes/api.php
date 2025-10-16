<?php

use App\Http\Controllers\ClassroomController;
use Illuminate\Support\Facades\Route;

Route::apiResource('classroom', ClassroomController::class);
Route::post('classroom/{classroom}/enroll', [ClassroomController::class, 'enroll']);
