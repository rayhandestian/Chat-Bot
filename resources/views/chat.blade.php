<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Groq Chatbot</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto p-4 max-w-3xl">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Groq Chatbot</h1>
                <div class="flex gap-2">
                    <button id="settings-btn" 
                            class="px-4 py-2 text-sm text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors">
                        Settings
                    </button>
                    <button id="clear-chat" 
                            class="px-4 py-2 text-sm text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors">
                        Clear Chat
                    </button>
                </div>
            </div>

            <!-- Settings Modal -->
            <div id="settings-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Settings</h3>
                        <form id="settings-form" class="space-y-4">
                            <div>
                                <label for="api-key" class="block text-sm font-medium text-gray-700 mb-1">API Key (optional)</label>
                                <input type="password" 
                                       id="api-key" 
                                       class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:border-blue-500"
                                       placeholder="Enter your Groq API key"
                                       value="{{ $hasCustomApiKey ? '********' : '' }}">
                            </div>
                            <div>
                                <label for="system-prompt" class="block text-sm font-medium text-gray-700 mb-1">System Prompt</label>
                                <textarea id="system-prompt" 
                                          class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:border-blue-500"
                                          rows="3">{{ $currentSystemPrompt }}</textarea>
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" 
                                        id="close-settings"
                                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-lg transition-colors">
                                    Cancel
                                </button>
                                <button type="submit" 
                                        class="px-4 py-2 text-sm text-white bg-blue-500 hover:bg-blue-600 rounded-lg transition-colors">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="mb-4">
                <label for="model-select" class="block text-sm font-medium text-gray-700 mb-2">Select Model:</label>
                <select id="model-select" class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:border-blue-500">
                    @foreach($models as $model)
                        <option value="{{ $model }}" {{ $model === 'llama-3.3-70b-versatile' ? 'selected' : '' }}>
                            {{ $model }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div id="chat-messages" class="space-y-4 mb-6 h-[500px] overflow-y-auto">
                <!-- Messages will be inserted here -->
            </div>

            <form id="chat-form" class="flex gap-2">
                <input type="text" 
                       id="message-input" 
                       class="flex-1 rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:border-blue-500"
                       placeholder="Type your message...">
                <button type="submit" 
                        class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                    Send
                </button>
            </form>
        </div>
    </div>

    <script>
        const chatMessages = document.getElementById('chat-messages');
        const chatForm = document.getElementById('chat-form');
        const messageInput = document.getElementById('message-input');
        const modelSelect = document.getElementById('model-select');
        const clearChatButton = document.getElementById('clear-chat');
        const settingsBtn = document.getElementById('settings-btn');
        const settingsModal = document.getElementById('settings-modal');
        const closeSettingsBtn = document.getElementById('close-settings');
        const settingsForm = document.getElementById('settings-form');

        // Settings Modal
        settingsBtn.addEventListener('click', () => {
            settingsModal.classList.remove('hidden');
        });

        closeSettingsBtn.addEventListener('click', () => {
            settingsModal.classList.add('hidden');
        });

        settingsModal.addEventListener('click', (e) => {
            if (e.target === settingsModal) {
                settingsModal.classList.add('hidden');
            }
        });

        settingsForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const apiKey = document.getElementById('api-key').value.trim();
            const systemPrompt = document.getElementById('system-prompt').value.trim();

            try {
                const response = await fetch('/chat/settings', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        api_key: apiKey === '********' ? null : apiKey,
                        system_prompt: systemPrompt
                    })
                });

                if (response.ok) {
                    settingsModal.classList.add('hidden');
                    chatMessages.innerHTML = '';
                    appendMessage('Settings updated! Starting a new conversation...', false);
                }
            } catch (error) {
                appendMessage('Failed to update settings. Please try again.', false);
            }
        });

        function appendMessage(content, isUser = false) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `flex ${isUser ? 'justify-end' : 'justify-start'}`;
            
            const messageBubble = document.createElement('div');
            messageBubble.className = `max-w-[70%] rounded-lg p-3 ${
                isUser ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-800'
            }`;
            messageBubble.textContent = content;
            
            messageDiv.appendChild(messageBubble);
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        clearChatButton.addEventListener('click', async () => {
            try {
                const response = await fetch('/chat/clear', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    chatMessages.innerHTML = '';
                    appendMessage('Chat history cleared. Start a new conversation!', false);
                }
            } catch (error) {
                appendMessage('Failed to clear chat history. Please try again.', false);
            }
        });

        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const message = messageInput.value.trim();
            if (!message) return;

            appendMessage(message, true);
            messageInput.value = '';
            messageInput.disabled = true;

            try {
                const response = await fetch('/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ 
                        message,
                        model: modelSelect.value
                    })
                });

                const data = await response.json();
                appendMessage(data.response);
            } catch (error) {
                appendMessage('Sorry, something went wrong. Please try again.');
            }

            messageInput.disabled = false;
            messageInput.focus();
        });
    </script>
</body>
</html> 