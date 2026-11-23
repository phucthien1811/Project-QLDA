/**
 * Chatbot Client-side Handler
 * Manages UI interactions and API communication
 */

class ChatbotManager {
    constructor() {
        this.container = document.getElementById('chatbot-container');
        this.toggle = document.getElementById('chatbot-toggle');
        this.closeBtn = document.getElementById('chatbot-close');
        this.messagesDiv = document.getElementById('chatbot-messages');
        this.input = document.getElementById('chatbot-input');
        this.sendBtn = document.getElementById('chatbot-send');
        
        this.isOpen = false;
        this.isSending = false;
        this.isHandoff = false;
        this.lastMessageId = 0;
        this.pollTimer = null;
        this.conversationId = this.getOrCreateConversationId();
        
        this.initializeEventListeners();
        this.updateStatus('bot');
    }

    /**
     * Initialize all event listeners
     */
    initializeEventListeners() {
        this.toggle.addEventListener('click', () => this.toggleChat());
        this.closeBtn.addEventListener('click', () => this.closeChat());
        this.sendBtn.addEventListener('click', () => this.sendMessage());
        this.input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });

        // Focus on input when container opens
        this.container.addEventListener('animationend', () => {
            if (this.isOpen) {
                this.input.focus();
            }
        });
    }

    getOrCreateConversationId() {
        const stored = localStorage.getItem('chatbot_conversation_id');
        if (stored) return stored;

        let newId = '';
        if (window.crypto && window.crypto.getRandomValues) {
            const bytes = new Uint8Array(16);
            window.crypto.getRandomValues(bytes);
            newId = Array.from(bytes).map(b => b.toString(16).padStart(2, '0')).join('');
        } else {
            newId = Date.now().toString(16) + Math.random().toString(16).slice(2);
        }

        const finalId = `c_${newId}`;
        localStorage.setItem('chatbot_conversation_id', finalId);
        return finalId;
    }

    updateStatus(mode) {
        const statusEl = document.getElementById('chatbot-status-text');
        if (!statusEl) return;

        if (mode === 'human') {
            statusEl.textContent = 'CSKH đang hỗ trợ';
            this.isHandoff = true;
        } else {
            statusEl.textContent = 'Trợ lý ảo đang hỗ trợ';
            this.isHandoff = false;
        }
    }

    startPolling() {
        if (this.pollTimer) return;
        this.pollTimer = setInterval(() => this.pollMessages(), 4000);
    }

    stopPolling() {
        if (this.pollTimer) {
            clearInterval(this.pollTimer);
            this.pollTimer = null;
        }
    }

    async pollMessages() {
        if (!this.conversationId) return;

        try {
            const url = `Api/ChatbotConversationApi.php?conversation_id=${encodeURIComponent(this.conversationId)}&last_id=${this.lastMessageId}`;
            const response = await fetch(url);
            if (!response.ok) return;

            const data = await response.json();
            if (!data.success) return;

            if (data.mode) {
                this.updateStatus(data.mode);
            }

            if (Array.isArray(data.messages)) {
                data.messages.forEach((msg) => {
                    const msgId = parseInt(msg.id, 10);
                    if (!Number.isNaN(msgId) && msgId > this.lastMessageId) {
                        this.lastMessageId = msgId;
                    }

                    if (msg.sender === 'user') return;

                    const sender = msg.sender === 'human' ? 'human' : (msg.sender === 'system' ? 'system' : 'bot');
                    this.addMessage(msg.message, sender);
                });
            }
        } catch (error) {
            console.warn('Chatbot poll error', error);
        }
    }

    /**
     * Toggle chat window open/close
     */
    toggleChat() {
        this.isOpen ? this.closeChat() : this.openChat();
    }

    /**
     * Open chat window
     */
    openChat() {
        this.isOpen = true;
        this.container.classList.add('active');
        this.toggle.classList.add('active');
        this.toggle.style.display = 'none';
        this.input.focus();
        this.startPolling();
        this.pollMessages();
    }

    /**
     * Close chat window
     */
    closeChat() {
        this.isOpen = false;
        this.container.classList.remove('active');
        this.toggle.classList.remove('active');
        this.toggle.style.display = 'flex';
        this.stopPolling();
    }

    /**
     * Send a message to the chatbot
     */
    async sendMessage(quickMessage = null) {
        const message = quickMessage || this.input.value.trim();
        
        if (!message) return;
        if (this.isSending) return;

        // Add user message to chat
        this.addMessage(message, 'user');
        this.input.value = '';
        this.isSending = true;
        this.sendBtn.disabled = true;

        // Show typing indicator
        this.addMessage('', 'bot', true);

        try {
            const response = await fetch('Api/ChatbotApi.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    message: message,
                    conversation_id: this.conversationId,
                    last_id: this.lastMessageId
                })
            });

            // Remove typing indicator
            this.removeTypingIndicator();

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || 'Failed to get response from chatbot');
            }

            const data = await response.json();
            
            if (data.success) {
                if (data.conversation_id && data.conversation_id !== this.conversationId) {
                    this.conversationId = data.conversation_id;
                    localStorage.setItem('chatbot_conversation_id', data.conversation_id);
                }

                if (data.mode) {
                    this.updateStatus(data.mode);
                }

                if (typeof data.last_message_id === 'number' && data.last_message_id > this.lastMessageId) {
                    this.lastMessageId = data.last_message_id;
                }

                if (data.handoff) {
                    this.addMessage(data.message || 'CSKH sẽ vào hỗ trợ trong giây lát.', 'system');
                } else if (data.message) {
                    this.addMessage(data.message, 'bot');
                }
                this.pollMessages();
            } else {
                this.addMessage('Error: ' + (data.error || 'Unable to process your request'), 'bot');
            }
        } catch (error) {
            console.error('=== Chatbot Error ===');
            console.error('Error:', error);
            console.error('Error message:', error.message);
            console.error('Error stack:', error.stack);
            this.removeTypingIndicator();
            this.addMessage(
                'Hệ thống đang bận. Bạn vui lòng thử lại hoặc yêu cầu CSKH hỗ trợ.',
                'system'
            );
        } finally {
            this.isSending = false;
            this.sendBtn.disabled = false;
            this.input.focus();
        }
    }

    /**
     * Add a message to the chat display
     * @param {string} text - The message text
     * @param {string} sender - 'user' or 'bot'
     * @param {boolean} isTyping - Whether to show typing indicator
     */
    addMessage(text, sender, isTyping = false) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}`;
        
        // Add avatar (except system messages)
        let avatarDiv = null;
        if (sender !== 'system') {
            avatarDiv = document.createElement('div');
            avatarDiv.className = 'message-avatar';
            if (sender === 'bot') {
                avatarDiv.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M20 9V7c0-1.1-.9-2-2-2h-3c0-1.66-1.34-3-3-3S9 3.34 9 5H6c-1.1 0-2 .9-2 2v2c-1.66 0-3 1.34-3 3s1.34 3 3 3v4c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2v-4c1.66 0 3-1.34 3-3s-1.34-3-3-3zM7.5 11.5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5S9.83 13 9 13s-1.5-.67-1.5-1.5zM16 17H8v-2h8v2zm-1-4c-.83 0-1.5-.67-1.5-1.5S14.17 10 15 10s1.5.67 1.5 1.5S15.83 13 15 13z"/></svg>';
            } else if (sender === 'human') {
                avatarDiv.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M6 10a2 2 0 0 1 2-2h1V6a3 3 0 0 1 6 0v2h1a2 2 0 0 1 2 2v4a4 4 0 0 1-4 4h-2v2H9v-2H8a4 4 0 0 1-4-4v-4zm5-2h2V6a1 1 0 0 0-2 0v2z"/></svg>';
            } else {
                avatarDiv.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>';
            }
        }
        
        if (isTyping) {
            messageDiv.classList.add('typing');
            const contentDiv = document.createElement('div');
            contentDiv.className = 'message-content';
            contentDiv.innerHTML = `
                <div class="typing-indicator">
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                </div>
            `;
            if (avatarDiv) {
                messageDiv.appendChild(avatarDiv);
            }
            messageDiv.appendChild(contentDiv);
        } else {
            const contentDiv = document.createElement('div');
            contentDiv.className = 'message-content';
            contentDiv.textContent = text;
            if (avatarDiv) {
                messageDiv.appendChild(avatarDiv);
            }
            messageDiv.appendChild(contentDiv);
        }

        this.messagesDiv.appendChild(messageDiv);
        
        // Auto-scroll to bottom
        setTimeout(() => {
            this.messagesDiv.scrollTop = this.messagesDiv.scrollHeight;
        }, 0);

        return messageDiv;
    }

    /**
     * Remove the typing indicator
     */
    removeTypingIndicator() {
        const typingMessage = this.messagesDiv.querySelector('.message.bot.typing');
        if (typingMessage) {
            typingMessage.remove();
        }
    }
}

/**
 * Send quick message from suggestion buttons
 */
function sendQuickMessage(message) {
    if (window.chatbot) {
        window.chatbot.sendMessage(message);
    }
}

/**
 * Initialize chatbot when DOM is ready
 */
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('chatbot-toggle')) {
        window.chatbot = new ChatbotManager();
    }
});

