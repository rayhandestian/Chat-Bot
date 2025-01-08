<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

Route::get('/', [ChatController::class, 'index']);
Route::post('/chat', [ChatController::class, 'sendMessage']);
Route::post('/chat/clear', [ChatController::class, 'clearChat']);
Route::post('/chat/settings', [ChatController::class, 'updateSettings']);
