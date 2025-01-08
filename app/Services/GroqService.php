<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GroqService
{
    private string $apiKey;
    private string $systemPrompt;
    private string $baseUrl = 'https://api.groq.com/openai/v1';

    public function __construct()
    {
        $this->apiKey = env('GROQ_API_KEY');
        $this->systemPrompt = env('GROQ_SYSTEM_PROMPT');
    }

    public function getModels()
    {
        // Cache models for 1 hour to avoid frequent API calls
        return Cache::remember('groq_models', 3600, function () {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ])->get($this->baseUrl . '/models');

            if ($response->successful()) {
                return collect($response->json()['data'])
                    ->pluck('id')
                    ->toArray();
            }

            return ['llama-3.3-70b-versatile']; // Fallback to default model
        });
    }

    public function chat(string $message, string $model = null)
    {
        if (!$model) {
            $model = env('GROQ_MODEL', 'llama-3.3-70b-versatile');
        }

        $messages = [
            ['role' => 'system', 'content' => $this->systemPrompt],
            ['role' => 'user', 'content' => $message]
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json'
        ])->post($this->baseUrl . '/chat/completions', [
            'messages' => $messages,
            'model' => $model
        ]);

        if ($response->successful()) {
            return $response->json()['choices'][0]['message']['content'];
        }

        return 'Sorry, I encountered an error while processing your request.';
    }
} 