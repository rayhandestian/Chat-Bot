<?php

namespace App\Http\Controllers;

use App\Services\GroqService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    private GroqService $groqService;

    public function __construct(GroqService $groqService)
    {
        $this->groqService = $groqService;
    }

    public function index()
    {
        $models = $this->groqService->getModels();
        $currentSystemPrompt = $this->groqService->getSystemPrompt();
        $hasCustomApiKey = session()->has('custom_api_key');
        return view('chat', compact('models', 'currentSystemPrompt', 'hasCustomApiKey'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'model' => 'required|string'
        ]);

        $response = $this->groqService->chat($request->message, $request->model);

        return response()->json(['response' => $response]);
    }

    public function clearChat()
    {
        $this->groqService->clearHistory();
        return response()->json(['success' => true]);
    }

    public function updateSettings(Request $request)
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
} 