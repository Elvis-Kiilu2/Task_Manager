<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json(['status' => 'ok']));

Route::apiResource('tasks', TaskController::class)
    ->only(['index', 'store', 'update', 'destroy']);

Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus']);
Route::get('tasks/report', [TaskController::class, 'report']);
