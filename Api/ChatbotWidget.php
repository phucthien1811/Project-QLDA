<?php
/**
 * Chatbot Widget HTML Component
 * This file contains the HTML structure for the chatbot widget
 * Include this in your main layout pages
 */

function renderChatbotWidget() {
    ?>
    <!-- Chatbot Toggle Button -->
    <button id="chatbot-toggle" title="Chat với 3AM" aria-label="Mở chat">
        <svg class="chat-icon" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
    </button>

    <!-- Chatbot Container -->
    <div id="chatbot-container" role="dialog" aria-label="Chatbot">
        <!-- Header -->
        <div class="chatbot-header">
            <div class="chatbot-header-info">
                <div class="chatbot-avatar">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                    </svg>
                </div>
                <div class="chatbot-header-text">
                    <h3>Người hỗ trợ</h3>
                    <span id="chatbot-status-text">Luôn sẵn sàng hỗ trợ</span>
                </div>
            </div>
            <button class="chatbot-header-close" id="chatbot-close" aria-label="Đóng chat">
                ✕
            </button>
        </div>

        <!-- Messages Area -->
        <div class="chatbot-messages" id="chatbot-messages" role="log" aria-live="polite">
            <!-- Welcome Message -->
            <div class="message bot">
                <div class="message-avatar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 9V7c0-1.1-.9-2-2-2h-3c0-1.66-1.34-3-3-3S9 3.34 9 5H6c-1.1 0-2 .9-2 2v2c-1.66 0-3 1.34-3 3s1.34 3 3 3v4c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2v-4c1.66 0 3-1.34 3-3s-1.34-3-3-3zM7.5 11.5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5S9.83 13 9 13s-1.5-.67-1.5-1.5zM16 17H8v-2h8v2zm-1-4c-.83 0-1.5-.67-1.5-1.5S14.17 10 15 10s1.5.67 1.5 1.5S15.83 13 15 13z"/>
                    </svg>
                </div>
                <div class="message-content">
                    Xin chào! 👋<br>
                    Tôi là trợ lý ảo của 3AM. Tôi có thể giúp bạn tìm sản phẩm, kiểm tra đơn hàng hoặc giải đáp thắc mắc.
                </div>
            </div>
        </div>

        <!-- Quick Suggestions -->
        <div class="chatbot-suggestions">
            <button class="suggestion-btn" onclick="sendQuickMessage('Sản phẩm nổi bật')">🔥 Sản phẩm hot</button>
            <button class="suggestion-btn" onclick="sendQuickMessage('Tư vấn mua điện thoại')">📱 Tư vấn</button>
            <button class="suggestion-btn" onclick="sendQuickMessage('Hỗ trợ đơn hàng')">📦 Đơn hàng</button>
        </div>

        <!-- Input Area -->
        <div class="chatbot-input-area">
            <input 
                type="text" 
                id="chatbot-input" 
                placeholder="Nhập tin nhắn..." 
                aria-label="Nhập tin nhắn"
            />
            <button id="chatbot-send" aria-label="Gửi tin nhắn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
                </svg>
            </button>
        </div>
    </div>
    <?php
}
?>
