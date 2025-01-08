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
            <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Groq Chatbot</h1>
            
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