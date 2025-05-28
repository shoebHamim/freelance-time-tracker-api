<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\TimeLogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    // Client routes
    Route::apiResource('clients', ClientController::class);
    // Project routes
    Route::apiResource('projects', ProjectController::class);

    // Time log routes
    Route::apiResource('time-logs', TimeLogController::class);
    Route::post('/time-logs/start', [TimeLogController::class, 'startTimeLog']);
    Route::post('/time-logs/{timeLog}/end', [TimeLogController::class, 'endTimeLog']);
    Route::get('/time-logs/day', [TimeLogController::class, 'getTimeLogsByDay']);
    Route::get('/time-logs/week', [TimeLogController::class, 'getTimeLogsByWeek']);
    // Report routes
    Route::get('/report/project', [ReportController::class, 'getTotalHoursByProject']);
    Route::get('/report/day', [ReportController::class, 'getTotalHoursByDay']);
    Route::get('/report/client', [ReportController::class, 'getTotalHoursByClient']);
    Route::get('/report', [ReportController::class, 'generateReport']);
});
