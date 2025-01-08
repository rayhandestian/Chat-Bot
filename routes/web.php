<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\ChatController as WebChatController;
use App\Http\Controllers\Api\ChatController as ApiChatController;

// Web Routes
Route::get('/', [WebChatController::class, 'index']);

// API Routes
Route::post('/chat', [ApiChatController::class, 'sendMessage']);
Route::post('/chat/clear', [ApiChatController::class, 'clearChat']);
Route::post('/chat/settings', [ApiChatController::class, 'updateSettings']);
Route::post('/chat/restore', [ApiChatController::class, 'restoreHistory']);
