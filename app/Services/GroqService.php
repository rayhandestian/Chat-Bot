<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class GroqService
{
    private string $baseUrl = 'https://api.groq.com/openai/v1';

    public function getApiKey()
    {
        return Session::get('custom_api_key', env('GROQ_API_KEY'));
    }

    public function setApiKey(?string $apiKey)
    {
        if ($apiKey) {
            Session::put('custom_api_key', $apiKey);
        } else {
            Session::forget('custom_api_key');
        }
    }

    public function getSystemPrompt()
    {
        return Session::get('custom_system_prompt', env('GROQ_SYSTEM_PROMPT', 'You are a helpful and knowledgeable AI assistant.'));
    }

    public function setSystemPrompt(?string $prompt)
    {
        if ($prompt) {
            Session::put('custom_system_prompt', $prompt);
        } else {
            Session::forget('custom_system_prompt');
        }
    }

    public function getModels()
    {
        // Cache models for 1 hour to avoid frequent API calls
        return Cache::remember('groq_models', 3600, function () {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->getApiKey(),
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

    private function getMessageHistory()
    {
        if (!Session::has('chat_history')) {
            Session::put('chat_history', [
                ['role' => 'system', 'content' => $this->getSystemPrompt()]
            ]);
        }
        return Session::get('chat_history');
    }

    private function addToHistory($message, $role = 'user')
    {
        $history = $this->getMessageHistory();
        $history[] = ['role' => $role, 'content' => $message];
        Session::put('chat_history', $history);
    }

    public function clearHistory()
    {
        Session::forget('chat_history');
    }

    public function chat(string $message, string $model = null)
    {
        if (!$model) {
            $model = env('GROQ_MODEL', 'llama-3.3-70b-versatile');
        }

        // Add user message to history
        $this->addToHistory($message, 'user');
        
        // Get full conversation history
        $messages = $this->getMessageHistory();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getApiKey(),
            'Content-Type' => 'application/json'
        ])->post($this->baseUrl . '/chat/completions', [
            'messages' => $messages,
            'model' => $model
        ]);

        if ($response->successful()) {
            $assistantMessage = $response->json()['choices'][0]['message']['content'];
            // Add assistant's response to history
            $this->addToHistory($assistantMessage, 'assistant');
            return $assistantMessage;
        }

        return 'Sorry, I encountered an error while processing your request.';
    }
} 