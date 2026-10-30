<?php
require_once(__DIR__ . '/../Backend/Database/DbClasses.php');

function generateConversationId() {
    return 'c_' . bin2hex(random_bytes(16));
}

function ensureChatbotTables($conn) {
    $queries = [
        "CREATE TABLE IF NOT EXISTS chatbot_conversations (
            id VARCHAR(64) NOT NULL PRIMARY KEY,
            session_id VARCHAR(128) DEFAULT NULL,
            user_id INT DEFAULT NULL,
            status ENUM('open','closed') NOT NULL DEFAULT 'open',
            mode ENUM('bot','human') NOT NULL DEFAULT 'bot',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            last_message_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        "CREATE TABLE IF NOT EXISTS chatbot_messages (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            conversation_id VARCHAR(64) NOT NULL,
            sender ENUM('user','bot','human','system') NOT NULL,
            message TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_chatbot_conversation_id (conversation_id),
            CONSTRAINT fk_chatbot_conversation
                FOREIGN KEY (conversation_id) REFERENCES chatbot_conversations(id)
                ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];

    foreach ($queries as $sql) {
        if (!mysqli_query($conn, $sql)) {
            return [false, mysqli_error($conn)];
        }
    }

    return [true, null];
}

function getConversationById($db, $conversationId) {
    $safeId = mysqli_real_escape_string($db->__conn, $conversationId);
    return $db->get_row("SELECT * FROM chatbot_conversations WHERE id = '$safeId'");
}

function createConversation($db, $conversationId, $sessionId, $userId) {
    $safeId = mysqli_real_escape_string($db->__conn, $conversationId);
    $safeSession = mysqli_real_escape_string($db->__conn, $sessionId);
    $userValue = $userId !== null ? intval($userId) : 'NULL';

    $sql = "INSERT INTO chatbot_conversations (id, session_id, user_id, status, mode, created_at, updated_at, last_message_at)
            VALUES ('$safeId', '$safeSession', $userValue, 'open', 'bot', NOW(), NOW(), NOW())";
    return mysqli_query($db->__conn, $sql);
}

function updateConversationMode($db, $conversationId, $mode) {
    $safeId = mysqli_real_escape_string($db->__conn, $conversationId);
    $safeMode = $mode === 'human' ? 'human' : 'bot';
    $sql = "UPDATE chatbot_conversations SET mode = '$safeMode', updated_at = NOW() WHERE id = '$safeId'";
    return mysqli_query($db->__conn, $sql);
}

function touchConversation($db, $conversationId) {
    $safeId = mysqli_real_escape_string($db->__conn, $conversationId);
    $sql = "UPDATE chatbot_conversations SET last_message_at = NOW(), updated_at = NOW() WHERE id = '$safeId'";
    return mysqli_query($db->__conn, $sql);
}

function insertChatMessage($db, $conversationId, $sender, $message) {
    $safeId = mysqli_real_escape_string($db->__conn, $conversationId);
    $safeSender = mysqli_real_escape_string($db->__conn, $sender);
    $safeMessage = mysqli_real_escape_string($db->__conn, $message);

    $sql = "INSERT INTO chatbot_messages (conversation_id, sender, message, created_at)
            VALUES ('$safeId', '$safeSender', '$safeMessage', NOW())";
    if (!mysqli_query($db->__conn, $sql)) {
        return [false, null];
    }

    return [true, mysqli_insert_id($db->__conn)];
}

function shouldHandoffToHuman($message) {
    $keywords = [
        'cskh', 'nhân viên', 'nguoi that', 'người thật', 'gặp người',
        'gặp nhân viên', 'gặp cskh', 'gặp tư vấn', 'tư vấn viên',
        'gọi điện', 'support', 'hotline'
    ];

    $normalized = function_exists('mb_strtolower') ? mb_strtolower($message, 'UTF-8') : strtolower($message);
    foreach ($keywords as $keyword) {
        if (strpos($normalized, $keyword) !== false) {
            return true;
        }
    }

    return false;
}
