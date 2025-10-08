<?php

use Illuminate\Support\Facades\Route;

Route::get('/yes', function () {
    return response()->json([
        'status' => 'ok',
        'command' => 'kis'
    ]);
});
