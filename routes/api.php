<?php

use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\SavedChatController;
use Illuminate\Support\Facades\Route;

Route::post('/chat', [ChatController::class, 'sendMessage']);
Route::post('/chat/clear', [ChatController::class, 'clearChat']);
Route::post('/chat/settings', [ChatController::class, 'updateSettings']);

Route::apiResource('chats', SavedChatController::class);
