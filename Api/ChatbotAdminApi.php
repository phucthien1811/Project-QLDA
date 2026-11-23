<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['currentUser']) || $_SESSION['currentUser']['MaQuyen'] < 2) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once(__DIR__ . '/ChatbotStorage.php');

$db = new DB_driver();
$db->connect();
$conn = $db->__conn;

list($tablesOk, $tablesError) = ensureChatbotTables($conn);
if (!$tablesOk) {
    echo json_encode(['success' => false, 'message' => 'Chatbot storage unavailable']);
    exit;
}

$request = isset($_GET['request']) ? $_GET['request'] : (isset($_POST['request']) ? $_POST['request'] : '');

switch ($request) {
    case 'list':
        listConversations($db);
        break;
    case 'messages':
        getMessages($db);
        break;
    case 'set_mode':
        setMode($db);
        break;
    case 'send_message':
        sendMessage($db);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

function listConversations($db) {
    $sql = "SELECT c.id, c.mode, c.created_at, c.last_message_at,
            (SELECT message FROM chatbot_messages m WHERE m.conversation_id = c.id ORDER BY m.id DESC LIMIT 1) AS last_message,
            (SELECT sender FROM chatbot_messages m WHERE m.conversation_id = c.id ORDER BY m.id DESC LIMIT 1) AS last_sender,
            (SELECT COUNT(*) FROM chatbot_messages m WHERE m.conversation_id = c.id) AS message_count
            FROM chatbot_conversations c
            ORDER BY c.last_message_at DESC, c.created_at DESC
            LIMIT 100";

    $data = $db->get_list($sql);
    echo json_encode(['success' => true, 'data' => $data]);
}

function getMessages($db) {
    $conversationId = isset($_GET['conversation_id']) ? trim($_GET['conversation_id']) : '';
    if ($conversationId === '') {
        echo json_encode(['success' => false, 'message' => 'Conversation ID is required']);
        return;
    }

    $conversation = getConversationById($db, $conversationId);
    if (!$conversation) {
        echo json_encode(['success' => false, 'message' => 'Conversation not found']);
        return;
    }

    $safeConversationId = mysqli_real_escape_string($db->__conn, $conversationId);
    $messages = $db->get_list("SELECT id, sender, message, created_at
                              FROM chatbot_messages
                              WHERE conversation_id = '$safeConversationId'
                              ORDER BY id ASC");

    echo json_encode([
        'success' => true,
        'conversation' => $conversation,
        'messages' => $messages
    ]);
}

function setMode($db) {
    $conversationId = isset($_POST['conversation_id']) ? trim($_POST['conversation_id']) : '';
    $mode = isset($_POST['mode']) ? trim($_POST['mode']) : 'bot';

    if ($conversationId === '') {
        echo json_encode(['success' => false, 'message' => 'Conversation ID is required']);
        return;
    }

    $conversation = getConversationById($db, $conversationId);
    if (!$conversation) {
        echo json_encode(['success' => false, 'message' => 'Conversation not found']);
        return;
    }

    $targetMode = $mode === 'human' ? 'human' : 'bot';
    if ($conversation['mode'] !== $targetMode) {
        updateConversationMode($db, $conversationId, $targetMode);
        $systemMessage = $targetMode === 'human'
            ? 'CSKH đã tiếp quản. Bạn vui lòng chờ phản hồi từ nhân viên.'
            : 'Đã chuyển lại cho trợ lý ảo.';
        insertChatMessage($db, $conversationId, 'system', $systemMessage);
        touchConversation($db, $conversationId);
    }

    echo json_encode(['success' => true, 'mode' => $targetMode]);
}

function sendMessage($db) {
    $conversationId = isset($_POST['conversation_id']) ? trim($_POST['conversation_id']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    if ($conversationId === '' || $message === '') {
        echo json_encode(['success' => false, 'message' => 'Conversation ID and message are required']);
        return;
    }

    $conversation = getConversationById($db, $conversationId);
    if (!$conversation) {
        echo json_encode(['success' => false, 'message' => 'Conversation not found']);
        return;
    }

    if ($conversation['mode'] !== 'human') {
        updateConversationMode($db, $conversationId, 'human');
        insertChatMessage($db, $conversationId, 'system', 'CSKH đã tiếp quản. Bạn vui lòng chờ phản hồi từ nhân viên.');
    }

    list($insertOk, $messageId) = insertChatMessage($db, $conversationId, 'human', $message);
    if (!$insertOk) {
        echo json_encode(['success' => false, 'message' => 'Failed to send message']);
        return;
    }

    touchConversation($db, $conversationId);
    echo json_encode(['success' => true, 'message_id' => $messageId]);
}
