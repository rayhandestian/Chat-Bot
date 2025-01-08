<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GroqService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    private GroqService $groqService;

    public function __construct(GroqService $groqService)
    {
        $this->groqService = $groqService;
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'model' => 'required|string'
        ]);

        $response = $this->groqService->chat($request->message, $request->model);

        return response()->json(['response' => $response]);
    }

    public function clearChat(): JsonResponse
    {
        $this->groqService->clearHistory();
        return response()->json(['success' => true]);
    }

    public function updateSettings(Request $request): JsonResponse
    {
        $request->validate([
            'system_prompt' => 'nullable|string|max:1000',
            'api_key' => 'nullable|string'
        ]);

        $this->groqService->setSystemPrompt($request->system_prompt);
        $this->groqService->setApiKey($request->api_key);
        $this->groqService->clearHistory(); // Clear history when settings change

        return response()->json([
            'success' => true,
            'system_prompt' => $this->groqService->getSystemPrompt(),
            'has_custom_api_key' => session()->has('custom_api_key')
        ]);
    }

    public function restoreHistory(Request $request): JsonResponse
    {
        $request->validate([
            'messages' => 'required|array'
        ]);

        $this->groqService->restoreHistory($request->messages);
        return response()->json(['success' => true]);
    }
} 