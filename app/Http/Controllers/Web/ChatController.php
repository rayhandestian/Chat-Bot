<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\GroqService;

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
} 