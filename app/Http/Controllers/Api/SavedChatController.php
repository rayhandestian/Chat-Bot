<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SavedChat;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SavedChatController extends Controller
{
    public function index(): JsonResponse
    {
        $chats = SavedChat::orderBy('created_at', 'desc')->get();
        return response()->json(['chats' => $chats]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'messages' => 'required|array'
        ]);

        $chat = SavedChat::create($request->all());
        return response()->json(['chat' => $chat], 201);
    }

    public function show(SavedChat $chat): JsonResponse
    {
        return response()->json(['chat' => $chat]);
    }

    public function update(Request $request, SavedChat $chat): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255'
        ]);

        $chat->update(['title' => $request->title]);
        return response()->json(['chat' => $chat]);
    }

    public function destroy(SavedChat $chat): JsonResponse
    {
        $chat->delete();
        return response()->json(null, 204);
    }
} 