<?php
require_once 'Includes/AuthCheck.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Memory - Admin Dashboard</title>
    <link rel="shortcut icon" href="/Project-PHP/img/favicon.ico?v=2">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../Styles/Admin/AdminDashboard.css">

    <style>
        #admin-dashboard-app .chatbot-layout {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 20px;
        }

        #admin-dashboard-app .conversation-list {
            max-height: 600px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        #admin-dashboard-app .conversation-item {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #fff;
        }

        #admin-dashboard-app .conversation-item.active {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        #admin-dashboard-app .conversation-item:hover {
            border-color: #94a3b8;
        }

        #admin-dashboard-app .conversation-title {
            font-weight: 600;
            color: #1f2937;
        }

        #admin-dashboard-app .conversation-meta {
            display: flex;
            gap: 8px;
            align-items: center;
            font-size: 12px;
            color: #6b7280;
            margin-top: 6px;
        }

        #admin-dashboard-app .conversation-preview {
            margin-top: 8px;
            font-size: 13px;
            color: #475569;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        #admin-dashboard-app .conversation-panel {
            display: flex;
            flex-direction: column;
            gap: 12px;
            height: 100%;
        }

        #admin-dashboard-app .conversation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        #admin-dashboard-app .conversation-header h2 {
            font-size: 20px;
            margin: 0;
        }

        #admin-dashboard-app .conversation-messages {
            flex: 1;
            background: #f8fafc;
            border-radius: 12px;
            padding: 16px;
            overflow-y: auto;
            min-height: 420px;
        }

        #admin-dashboard-app .message-row {
            margin-bottom: 12px;
        }

        #admin-dashboard-app .message-bubble {
            display: inline-block;
            padding: 10px 14px;
            border-radius: 12px;
            max-width: 80%;
            font-size: 14px;
            line-height: 1.5;
        }

        #admin-dashboard-app .message-user {
            text-align: right;
        }

        #admin-dashboard-app .message-user .message-bubble {
            background: #111827;
            color: #fff;
        }

        #admin-dashboard-app .message-bot .message-bubble {
            background: #fff;
            border: 1px solid #e5e7eb;
        }

        #admin-dashboard-app .message-human .message-bubble {
            background: #fff7ed;
            border: 1px solid #fed7aa;
        }

        #admin-dashboard-app .message-system {
            text-align: center;
        }

        #admin-dashboard-app .message-system .message-bubble {
            background: #e2e8f0;
            color: #475569;
            border: 1px dashed #cbd5f5;
            font-size: 12px;
        }

        #admin-dashboard-app .message-meta {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 4px;
        }

        #admin-dashboard-app .conversation-input {
            display: flex;
            gap: 10px;
        }

        #admin-dashboard-app .conversation-input input {
            flex: 1;
            border-radius: 10px;
        }

        #admin-dashboard-app .mode-badge {
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
        }

        #admin-dashboard-app .mode-bot {
            background: #e0f2fe;
            color: #0369a1;
        }

        #admin-dashboard-app .mode-human {
            background: #fef3c7;
            color: #92400e;
        }
    </style>
</head>
<body>
    <div id="admin-dashboard-app">
        <?php include 'Includes/Sidebar.php'; ?>

        <div class="adm-main-content">
            <div class="adm-header">
                <h1 class="adm-header-title">Chatbot Memory</h1>
                <div class="adm-user-menu">
                    <button class="adm-user-btn">
                        <i class="fa fa-user-circle"></i>
                    </button>
                </div>
            </div>

            <div class="adm-content">
                <div class="adm-card">
                    <div class="chatbot-layout">
                        <div>
                            <div class="adm-card-header">Hội thoại gần đây</div>
                            <div class="conversation-list" id="conversationList">
                                <div class="text-muted text-center p-4">Đang tải...</div>
                            </div>
                        </div>
                        <div class="conversation-panel">
                            <div class="conversation-header">
                                <div>
                                    <h2 id="conversationTitle">Chọn hội thoại</h2>
                                    <div id="conversationMeta" class="text-muted" style="font-size: 13px;"></div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-primary" id="takeOverBtn" disabled>Tiếp quản</button>
                                    <button class="btn btn-sm btn-outline-secondary" id="returnBotBtn" disabled>Trả lại AI</button>
                                </div>
                            </div>

                            <div class="conversation-messages" id="conversationMessages">
                                <div class="text-muted text-center p-4">Chọn một hội thoại để xem nội dung</div>
                            </div>

                            <div class="conversation-input">
                                <input type="text" class="form-control" id="adminMessageInput" placeholder="Nhập phản hồi cho khách hàng..." disabled>
                                <button class="btn btn-primary" id="adminSendBtn" disabled>Gửi</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentConversationId = null;
        let currentMode = 'bot';

        const conversationListEl = document.getElementById('conversationList');
        const conversationMessagesEl = document.getElementById('conversationMessages');
        const conversationTitleEl = document.getElementById('conversationTitle');
        const conversationMetaEl = document.getElementById('conversationMeta');
        const takeOverBtn = document.getElementById('takeOverBtn');
        const returnBotBtn = document.getElementById('returnBotBtn');
        const adminMessageInput = document.getElementById('adminMessageInput');
        const adminSendBtn = document.getElementById('adminSendBtn');

        async function fetchJson(url, options = {}) {
            const response = await fetch(url, options);
            if (!response.ok) {
                throw new Error('Request failed');
            }
            return await response.json();
        }

        function escapeHtml(text) {
            return String(text || '').replace(/[&<>"']/g, (char) => {
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                };
                return map[char] || char;
            });
        }

        async function loadConversations() {
            try {
                const data = await fetchJson('../Api/ChatbotAdminApi.php?request=list');
                if (!data.success) {
                    conversationListEl.innerHTML = '<div class="text-danger text-center p-4">Không thể tải hội thoại.</div>';
                    return;
                }
                renderConversations(data.data || []);
            } catch (error) {
                conversationListEl.innerHTML = '<div class="text-danger text-center p-4">Không thể tải hội thoại.</div>';
            }
        }

        function renderConversations(conversations) {
            if (!conversations.length) {
                conversationListEl.innerHTML = '<div class="text-muted text-center p-4">Chưa có hội thoại.</div>';
                return;
            }

            conversationListEl.innerHTML = conversations.map(conv => {
                const rawId = conv.id || '';
                const shortId = rawId ? rawId.replace('c_', '').slice(0, 8) : 'N/A';
                const modeClass = conv.mode === 'human' ? 'mode-human' : 'mode-bot';
                const modeLabel = conv.mode === 'human' ? 'CSKH' : 'AI';
                const lastMessage = conv.last_message ? conv.last_message : 'Chưa có tin nhắn';
                const lastTime = conv.last_message_at || conv.created_at || '';
                const activeClass = conv.id === currentConversationId ? 'active' : '';

                return `
                    <div class="conversation-item ${activeClass}" data-id="${escapeHtml(rawId)}">
                        <div class="conversation-title">#${escapeHtml(shortId)}</div>
                        <div class="conversation-meta">
                            <span class="mode-badge ${modeClass}">${modeLabel}</span>
                            <span>${escapeHtml(lastTime)}</span>
                            <span>${conv.message_count || 0} tin nhắn</span>
                        </div>
                        <div class="conversation-preview">${escapeHtml(lastMessage)}</div>
                    </div>
                `;
            }).join('');

            conversationListEl.querySelectorAll('.conversation-item').forEach(item => {
                item.addEventListener('click', () => {
                    const conversationId = item.getAttribute('data-id');
                    selectConversation(conversationId);
                });
            });
        }

        async function selectConversation(conversationId) {
            currentConversationId = conversationId;
            await loadMessages();
            loadConversations();
        }

        async function loadMessages() {
            if (!currentConversationId) return;

            try {
                const data = await fetchJson(`../Api/ChatbotAdminApi.php?request=messages&conversation_id=${encodeURIComponent(currentConversationId)}`);
                if (!data.success) {
                    conversationMessagesEl.innerHTML = '<div class="text-danger text-center p-4">Không thể tải tin nhắn.</div>';
                    return;
                }

                const conversation = data.conversation || {};
                const messages = data.messages || [];
                currentMode = conversation.mode || 'bot';

                conversationTitleEl.textContent = `Hội thoại #${currentConversationId.replace('c_', '').slice(0, 8)}`;
                conversationMetaEl.textContent = `Trạng thái: ${currentMode === 'human' ? 'CSKH đang hỗ trợ' : 'Trợ lý ảo'}`;
                updateActionButtons();
                renderMessages(messages);
            } catch (error) {
                conversationMessagesEl.innerHTML = '<div class="text-danger text-center p-4">Không thể tải tin nhắn.</div>';
            }
        }

        function renderMessages(messages) {
            if (!messages.length) {
                conversationMessagesEl.innerHTML = '<div class="text-muted text-center p-4">Chưa có tin nhắn.</div>';
                return;
            }

            conversationMessagesEl.innerHTML = messages.map(msg => {
                const sender = msg.sender || 'bot';
                const rowClass = sender === 'user' ? 'message-user' :
                    (sender === 'human' ? 'message-human' :
                        (sender === 'system' ? 'message-system' : 'message-bot'));

                const senderLabel = sender === 'user' ? 'Khách' :
                    (sender === 'human' ? 'CSKH' :
                        (sender === 'system' ? 'Hệ thống' : 'AI'));

                const messageText = escapeHtml(msg.message);
                const metaTime = escapeHtml(msg.created_at || '');
                return `
                    <div class="message-row ${rowClass}">
                        <div class="message-bubble">${messageText}</div>
                        <div class="message-meta">${senderLabel} • ${metaTime}</div>
                    </div>
                `;
            }).join('');

            conversationMessagesEl.scrollTop = conversationMessagesEl.scrollHeight;
        }

        function updateActionButtons() {
            const hasConversation = !!currentConversationId;
            takeOverBtn.disabled = !hasConversation || currentMode === 'human';
            returnBotBtn.disabled = !hasConversation || currentMode === 'bot';
            adminMessageInput.disabled = !hasConversation;
            adminSendBtn.disabled = !hasConversation;
        }

        async function setMode(mode) {
            if (!currentConversationId) return;

            const formData = new FormData();
            formData.append('request', 'set_mode');
            formData.append('conversation_id', currentConversationId);
            formData.append('mode', mode);

            try {
                const response = await fetch('../Api/ChatbotAdminApi.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                if (data.success) {
                    currentMode = data.mode;
                    await loadMessages();
                }
            } catch (error) {
                console.error(error);
            }
        }

        async function sendMessage() {
            const message = adminMessageInput.value.trim();
            if (!message || !currentConversationId) return;

            const formData = new FormData();
            formData.append('request', 'send_message');
            formData.append('conversation_id', currentConversationId);
            formData.append('message', message);

            adminSendBtn.disabled = true;
            try {
                const response = await fetch('../Api/ChatbotAdminApi.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                if (data.success) {
                    adminMessageInput.value = '';
                    await loadMessages();
                }
            } catch (error) {
                console.error(error);
            } finally {
                adminSendBtn.disabled = false;
            }
        }

        takeOverBtn.addEventListener('click', () => setMode('human'));
        returnBotBtn.addEventListener('click', () => setMode('bot'));
        adminSendBtn.addEventListener('click', sendMessage);
        adminMessageInput.addEventListener('keypress', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                sendMessage();
            }
        });

        loadConversations();
        setInterval(() => {
            loadConversations();
            if (currentConversationId) {
                loadMessages();
            }
        }, 8000);
    </script>
</body>
</html>
