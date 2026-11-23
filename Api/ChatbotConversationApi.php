<?php
require_once(__DIR__ . '/ChatbotStorage.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$conversationId = isset($_GET['conversation_id']) ? trim($_GET['conversation_id']) : '';
$lastId = isset($_GET['last_id']) ? intval($_GET['last_id']) : 0;

if ($conversationId === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Conversation ID is required']);
    exit;
}

$db = new DB_driver();
$db->connect();
$conn = $db->__conn;

list($tablesOk, $tablesError) = ensureChatbotTables($conn);
if (!$tablesOk) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Chatbot storage unavailable']);
    exit;
}

$conversation = getConversationById($db, $conversationId);
if (!$conversation) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Conversation not found']);
    exit;
}

$safeConversationId = mysqli_real_escape_string($db->__conn, $conversationId);
$lastId = max(0, $lastId);

$messages = $db->get_list("SELECT id, sender, message, created_at
                          FROM chatbot_messages
                          WHERE conversation_id = '$safeConversationId' AND id > $lastId
                          ORDER BY id ASC");

echo json_encode([
    'success' => true,
    'conversation_id' => $conversationId,
    'mode' => $conversation['mode'],
    'messages' => $messages
]);
