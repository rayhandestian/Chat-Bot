<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Groq Chatbot</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <style>
        .markdown-content pre {
            background-color: #f3f4f6;
            padding: 1rem;
            border-radius: 0.5rem;
            margin: 0.5rem 0;
            overflow-x: auto;
        }
        .markdown-content code {
            background-color: #f3f4f6;
            padding: 0.2rem 0.4rem;
            border-radius: 0.25rem;
            font-family: monospace;
        }
        .markdown-content p {
            margin: 0.5rem 0;
        }
        .markdown-content ul, .markdown-content ol {
            margin: 0.5rem 0;
            padding-left: 1.5rem;
        }
        .markdown-content ul {
            list-style-type: disc;
        }
        .markdown-content ol {
            list-style-type: decimal;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto p-4 max-w-3xl">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Groq Chatbot</h1>
                <div class="flex gap-2">
                    <button id="saved-chats-btn" 
                            class="px-4 py-2 text-sm text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg transition-colors">
                        Saved Chats
                    </button>
                    <button id="save-chat-btn" 
                            class="px-4 py-2 text-sm text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg transition-colors">
                        Save Chat
                    </button>
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

            <!-- Save Chat Modal -->
            <div id="save-chat-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Save Chat</h3>
                        <form id="save-chat-form" class="space-y-4">
                            <div>
                                <label for="chat-title" class="block text-sm font-medium text-gray-700 mb-1">Chat Title</label>
                                <input type="text" 
                                       id="chat-title" 
                                       class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:border-blue-500"
                                       placeholder="Enter a title for this chat"
                                       required>
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" 
                                        class="close-modal px-4 py-2 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-lg transition-colors">
                                    Cancel
                                </button>
                                <button type="submit" 
                                        class="px-4 py-2 text-sm text-white bg-green-500 hover:bg-green-600 rounded-lg transition-colors">
                                    Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Saved Chats Modal -->
            <div id="saved-chats-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
                <div class="relative top-20 mx-auto p-5 border w-[32rem] shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Saved Chats</h3>
                        <div id="saved-chats-list" class="space-y-2 max-h-96 overflow-y-auto">
                            <!-- Saved chats will be inserted here -->
                        </div>
                        <div class="mt-4 flex justify-end">
                            <button type="button" 
                                    class="close-modal px-4 py-2 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-lg transition-colors">
                                Close
                            </button>
                        </div>
                    </div>
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
            } markdown-content`;
            
            // Parse markdown for both user and bot messages
            messageBubble.innerHTML = marked.parse(content);
            
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

        // Save Chat Modal
        const saveChatBtn = document.getElementById('save-chat-btn');
        const saveChatModal = document.getElementById('save-chat-modal');
        const saveChatForm = document.getElementById('save-chat-form');

        // Saved Chats Modal
        const savedChatsBtn = document.getElementById('saved-chats-btn');
        const savedChatsModal = document.getElementById('saved-chats-modal');
        const savedChatsList = document.getElementById('saved-chats-list');

        // Close modals when clicking outside
        document.querySelectorAll('.close-modal').forEach(button => {
            button.addEventListener('click', () => {
                saveChatModal.classList.add('hidden');
                savedChatsModal.classList.add('hidden');
            });
        });

        // Save Chat
        saveChatBtn.addEventListener('click', () => {
            saveChatModal.classList.remove('hidden');
        });

        saveChatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const title = document.getElementById('chat-title').value.trim();
            const messages = Array.from(chatMessages.children).map(div => ({
                isUser: div.querySelector('div').classList.contains('bg-blue-500'),
                content: div.querySelector('div').textContent
            }));

            try {
                const response = await fetch('/api/chats', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ title, messages })
                });

                if (response.ok) {
                    saveChatModal.classList.add('hidden');
                    document.getElementById('chat-title').value = '';
                    appendMessage('Chat saved successfully!', false);
                } else {
                    throw new Error('Failed to save chat');
                }
            } catch (error) {
                appendMessage('Failed to save chat. Please try again.', false);
            }
        });

        // Load Saved Chats
        savedChatsBtn.addEventListener('click', async () => {
            savedChatsModal.classList.remove('hidden');
            await loadSavedChats();
        });

        async function loadSavedChats() {
            try {
                const response = await fetch('/api/chats');
                const data = await response.json();
                
                savedChatsList.innerHTML = data.chats.map(chat => `
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="font-medium">${chat.title}</span>
                        <div class="flex gap-2">
                            <button onclick="loadChat(${chat.id})" 
                                    class="px-3 py-1 text-sm text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors">
                                Load
                            </button>
                            <button onclick="editChatTitle(${chat.id}, '${chat.title}')" 
                                    class="px-3 py-1 text-sm text-green-600 hover:text-green-800 hover:bg-green-50 rounded transition-colors">
                                Edit
                            </button>
                            <button onclick="deleteChat(${chat.id})" 
                                    class="px-3 py-1 text-sm text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors">
                                Delete
                            </button>
                        </div>
                    </div>
                `).join('');
            } catch (error) {
                savedChatsList.innerHTML = '<p class="text-red-500">Failed to load saved chats</p>';
            }
        }

        async function loadChat(id) {
            try {
                const response = await fetch(`/api/chats/${id}`);
                const data = await response.json();
                
                // Clear and display messages in UI
                chatMessages.innerHTML = '';
                data.chat.messages.forEach(msg => {
                    appendMessage(msg.content, msg.isUser);
                });

                // Restore chat history in session
                await fetch('/chat/restore', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ messages: data.chat.messages })
                });
                
                savedChatsModal.classList.add('hidden');
            } catch (error) {
                appendMessage('Failed to load chat. Please try again.', false);
            }
        }

        async function editChatTitle(id, currentTitle) {
            const newTitle = prompt('Enter new title:', currentTitle);
            if (!newTitle || newTitle === currentTitle) return;

            try {
                const response = await fetch(`/api/chats/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ title: newTitle })
                });

                if (response.ok) {
                    await loadSavedChats();
                } else {
                    throw new Error('Failed to update chat title');
                }
            } catch (error) {
                alert('Failed to update chat title. Please try again.');
            }
        }

        async function deleteChat(id) {
            if (!confirm('Are you sure you want to delete this chat?')) return;

            try {
                const response = await fetch(`/api/chats/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    await loadSavedChats();
                } else {
                    throw new Error('Failed to delete chat');
                }
            } catch (error) {
                alert('Failed to delete chat. Please try again.');
            }
        }
    </script>
</body>
</html> 