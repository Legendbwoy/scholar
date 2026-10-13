<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

Route::prefix('v1')->group(function () {
    Route::get('/health', [ApiController::class, 'health']);
    Route::get('/courses', [ApiController::class, 'courses']);
    Route::get('/attendance/stats', [ApiController::class, 'attendanceStats']);
    Route::post('/broadcast/send', [ApiController::class, 'broadcastSend']);
});