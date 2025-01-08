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
        return view('chat', compact('models'));
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
} 