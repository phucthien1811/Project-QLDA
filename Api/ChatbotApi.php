<?php
/**
 * Chatbot API Handler
 * Handles messages from the frontend and communicates with Google Gemini API
 * * Usage: POST /Api/ChatbotApi.php
 * Parameters: {"message": "user message here"}
 */

// Load database classes
require_once(__DIR__ . '/../Backend/Database/DbClasses.php');
session_start();

// Logging function
function logDebug($message, $data = null) {
    $logFile = __DIR__ . '/../logs/chatbot_debug.log';
    $logDir = dirname($logFile);
    
    if (!file_exists($logDir)) {
        mkdir($logDir, 0777, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message";
    
    if ($data !== null) {
        $logMessage .= "\n" . print_r($data, true);
    }
    
    $logMessage .= "\n" . str_repeat('-', 80) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

require_once(__DIR__ . '/ChatbotStorage.php');

function respondHandoff($db, $conversationId, $userMessageId, $message) {
    updateConversationMode($db, $conversationId, 'human');
    list($systemOk, $systemId) = insertChatMessage($db, $conversationId, 'system', $message);
    touchConversation($db, $conversationId);

    $lastMessageId = $systemOk ? $systemId : $userMessageId;
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'handoff' => true,
        'message' => $message,
        'conversation_id' => $conversationId,
        'mode' => 'human',
        'last_message_id' => $lastMessageId,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

function sendGeminiRequest($apiUrl, $requestBody) {
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    $curlInfo = curl_getinfo($ch);
    curl_close($ch);

    return [
        'response' => $response,
        'httpCode' => $httpCode,
        'error' => $error,
        'curlInfo' => $curlInfo
    ];
}

logDebug('=== Chatbot API Request Started ===');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    logDebug('OPTIONS request received');
    http_response_code(200);
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    logDebug('Invalid request method: ' . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

logDebug('Request method: POST');

// Load API key from config file
$configFile = __DIR__ . '/Config.php';
if (file_exists($configFile)) {
    require_once $configFile;
    $GEMINI_API_KEY = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : '';
} else {
    logDebug('ERROR: Config.php not found. Please copy ConfigExample.php to Config.php');
    $GEMINI_API_KEY = '';
}

logDebug('API Key configured: ' . (empty($GEMINI_API_KEY) ? 'NO' : 'YES'));

// Get the message from the request
$rawInput = file_get_contents('php://input');
logDebug('Raw input received', $rawInput);

$input = json_decode($rawInput, true);
logDebug('Decoded input', $input);

if (!isset($input['message']) || empty(trim($input['message']))) {
    logDebug('ERROR: Message is missing or empty');
    http_response_code(400);
    echo json_encode(['error' => 'Message is required']);
    exit;
}

$userMessage = trim($input['message']);
logDebug('User message: ' . $userMessage);

// Prepare conversation context
$conversationId = isset($input['conversation_id']) ? trim($input['conversation_id']) : '';
if ($conversationId === '') {
    $conversationId = generateConversationId();
}

$db = new DB_driver();
$db->connect();
$conn = $db->__conn;

list($tablesOk, $tablesError) = ensureChatbotTables($conn);
if (!$tablesOk) {
    logDebug('ERROR: Failed to ensure chatbot tables', $tablesError);
    http_response_code(500);
    echo json_encode(['error' => 'Chatbot storage unavailable']);
    exit;
}

$sessionId = session_id();
$userId = isset($_SESSION['currentUser']['MaND']) ? intval($_SESSION['currentUser']['MaND']) : null;

$conversation = getConversationById($db, $conversationId);
if (!$conversation) {
    $created = createConversation($db, $conversationId, $sessionId, $userId);
    if (!$created) {
        logDebug('ERROR: Failed to create conversation');
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create conversation']);
        exit;
    }
    $conversation = getConversationById($db, $conversationId);
}

$mode = $conversation && isset($conversation['mode']) ? $conversation['mode'] : 'bot';

list($userInsertOk, $userMessageId) = insertChatMessage($db, $conversationId, 'user', $userMessage);
if (!$userInsertOk) {
    logDebug('ERROR: Failed to insert user message');
    http_response_code(500);
    echo json_encode(['error' => 'Failed to store message']);
    exit;
}
touchConversation($db, $conversationId);

$handoffRequested = shouldHandoffToHuman($userMessage);
if ($handoffRequested && $mode !== 'human') {
    updateConversationMode($db, $conversationId, 'human');
    $mode = 'human';
    list($systemOk, $systemId) = insertChatMessage($db, $conversationId, 'system', 'CSKH đã tiếp quản. Bạn vui lòng chờ phản hồi từ nhân viên.');
    if ($systemOk) {
        $userMessageId = $systemId;
    }
    touchConversation($db, $conversationId);
}

if ($mode === 'human') {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'handoff' => true,
        'message' => 'CSKH đang hỗ trợ. Bạn vui lòng chờ phản hồi.',
        'conversation_id' => $conversationId,
        'mode' => 'human',
        'last_message_id' => $userMessageId,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

// Validate API key is set
if ($GEMINI_API_KEY === 'YOUR_GEMINI_API_KEY_HERE') {
    logDebug('ERROR: API key not configured');
    http_response_code(500);
    echo json_encode(['error' => 'API key not configured. Please set your Google Gemini API key.']);
    exit;
}

// --- START FIX ---

// Use gemini-2.5-flash - newest model
$MODEL_NAME = 'gemini-2.5-flash';
$API_BASE = 'https://generativelanguage.googleapis.com/v1beta/models/';
$apiUrl = $API_BASE . $MODEL_NAME . ':generateContent?key=' . $GEMINI_API_KEY;
$fallbackModel = 'gemini-1.5-flash';
$fallbackApiUrl = $API_BASE . $fallbackModel . ':generateContent?key=' . $GEMINI_API_KEY;

logDebug('API URL: ' . $API_BASE . $MODEL_NAME . ':generateContent?key=***');
logDebug('Model: ' . $MODEL_NAME);

// --- END FIX ---

// System prompt for the chatbot (customize this for your website)
$systemPrompt = "Bạn là trợ lý AI thông minh cho website bán điện thoại di động tại Việt Nam. "
    . "Nhiệm vụ của bạn:\n"
    . "1. Tư vấn sản phẩm điện thoại dựa trên thông tin được cung cấp\n"
    . "2. Giải đáp thắc mắc về đơn hàng, giao hàng, bảo hành\n"
    . "3. Hỗ trợ khách hàng một cách thân thiện, chuyên nghiệp\n"
    . "4. Luôn trả lời bằng tiếng Việt\n"
    . "5. Nếu khách hỏi về sản phẩm cụ thể, hãy dựa vào dữ liệu sản phẩm có sẵn để tư vấn\n"
    . "6. Trả lời ngắn gọn, ưu tiên 2-4 câu; cần liệt kê thì dùng gạch đầu dòng\n"
    . "7. Nếu thiếu thông tin, hỏi lại đúng 1 câu ngắn để làm rõ\n"
    . "8. Nếu không chắc chắn, đề nghị chuyển CSKH thay vì suy đoán\n\n";

// Get product information from database (product_series) to enhance chatbot knowledge
try {
    $products = $db->get_list("SELECT ps.*, 
                                (SELECT MIN(vso.price) FROM product_variants pv 
                                 JOIN variant_storage_options vso ON pv.id = vso.variant_id 
                                 WHERE pv.series_id = ps.id) as min_price
                               FROM product_series ps 
                               ORDER BY ps.created_at DESC LIMIT 20");
    
    if ($products && count($products) > 0) {
        $systemPrompt .= "Dưới đây là danh sách sản phẩm hiện có trên website:\n\n";
        
        foreach ($products as $sp) {
            $price = $sp['display_price'] ?: $sp['min_price'];
            $systemPrompt .= "- " . $sp['series_name'] . "\n";
            $systemPrompt .= "  + Giá từ: " . number_format($price, 0, ',', '.') . "đ\n";
            if (!empty($sp['description'])) {
                $systemPrompt .= "  + Mô tả: " . substr($sp['description'], 0, 150) . "...\n";
            }
            $systemPrompt .= "\n";
        }
        
        $systemPrompt .= "\nHãy sử dụng thông tin trên để tư vấn chính xác cho khách hàng.\n";
    }
} catch (Exception $e) {
    logDebug('Error loading products: ' . $e->getMessage());
}

$historyText = '';
$safeConversationId = mysqli_real_escape_string($db->__conn, $conversationId);
$safeMessageId = intval($userMessageId);
$historyRows = $db->get_list("SELECT sender, message FROM chatbot_messages 
                              WHERE conversation_id = '$safeConversationId' AND id < $safeMessageId
                              ORDER BY id DESC LIMIT 8");
if ($historyRows && count($historyRows) > 0) {
    $historyRows = array_reverse($historyRows);
    $historyText .= "Lịch sử gần đây:\n";
    foreach ($historyRows as $row) {
        if ($row['sender'] === 'system') {
            continue;
        }
        $label = $row['sender'] === 'user' ? 'Khách' : ($row['sender'] === 'human' ? 'CSKH' : 'Bot');
        $historyText .= $label . ": " . $row['message'] . "\n";
    }
    $historyText .= "\n";
}

logDebug('System prompt prepared', substr($systemPrompt, 0, 500) . '...');

// Prepare request body - Updated format for Gemini API
$requestBody = [
    'contents' => [
        [
            'role' => 'user',
            'parts' => [
                ['text' => $systemPrompt . $historyText . "Khách: " . $userMessage]
            ]
        ]
    ],
    'generationConfig' => [
        'temperature' => 0.7,
        'topP' => 0.95,
        'topK' => 40,
        'maxOutputTokens' => 300,
    ]
];

logDebug('Request body prepared', $requestBody);

// Make the API request
logDebug('Initiating cURL request...');

$apiResult = sendGeminiRequest($apiUrl, $requestBody);
$response = $apiResult['response'];
$httpCode = $apiResult['httpCode'];
$error = $apiResult['error'];
$curlInfo = $apiResult['curlInfo'];

logDebug('cURL completed');
logDebug('HTTP Code: ' . $httpCode);
logDebug('cURL Error: ' . ($error ?: 'None'));
logDebug('cURL Info', $curlInfo);
logDebug('API Response', $response);

// Handle API errors (retry with fallback model for 429/503)
if ($error) {
    logDebug('CURL ERROR: ' . $error);
    respondHandoff($db, $conversationId, $userMessageId, 'Hệ thống đang bận. CSKH sẽ hỗ trợ bạn ngay.');
}

if ($httpCode === 429 || $httpCode === 503) {
    logDebug('Primary model unavailable. Retrying with fallback model: ' . $fallbackModel);
    logDebug('Fallback API URL: ' . $API_BASE . $fallbackModel . ':generateContent?key=***');
    $apiResult = sendGeminiRequest($fallbackApiUrl, $requestBody);
    $response = $apiResult['response'];
    $httpCode = $apiResult['httpCode'];
    $error = $apiResult['error'];
    $curlInfo = $apiResult['curlInfo'];

    logDebug('Fallback cURL completed');
    logDebug('Fallback HTTP Code: ' . $httpCode);
    logDebug('Fallback cURL Error: ' . ($error ?: 'None'));
    logDebug('Fallback cURL Info', $curlInfo);
    logDebug('Fallback API Response', $response);

    if ($error) {
        logDebug('Fallback CURL ERROR: ' . $error);
        respondHandoff($db, $conversationId, $userMessageId, 'Hệ thống đang bận. CSKH sẽ hỗ trợ bạn ngay.');
    }
}

if ($httpCode !== 200) {
    logDebug('API ERROR - HTTP Code: ' . $httpCode);
    $errorResponse = json_decode($response, true);
    logDebug('Error response decoded', $errorResponse);
    
    $errorMsg = 'Unknown error';
    if (isset($errorResponse['error']['message'])) {
        $errorMsg = $errorResponse['error']['message'];
    }
    
    logDebug('Final error message: ' . $errorMsg);
    respondHandoff($db, $conversationId, $userMessageId, 'Hệ thống đang bận. CSKH sẽ hỗ trợ bạn ngay.');
}

// Parse the response
$apiResponse = json_decode($response, true);
logDebug('API response decoded', $apiResponse);

if (!isset($apiResponse['candidates'][0]['content']['parts'][0]['text'])) {
    logDebug('ERROR: Unexpected response format');
    logDebug('Response structure', $apiResponse);
    http_response_code(500);
    echo json_encode([
        'error' => 'Unexpected response format from Gemini API',
        'response' => $apiResponse
    ]);
    exit;
}

$botMessage = $apiResponse['candidates'][0]['content']['parts'][0]['text'];
logDebug('Bot message extracted: ' . $botMessage);

list($botInsertOk, $botMessageId) = insertChatMessage($db, $conversationId, 'bot', $botMessage);
if (!$botInsertOk) {
    logDebug('ERROR: Failed to insert bot message');
    http_response_code(500);
    echo json_encode(['error' => 'Failed to store bot response']);
    exit;
}
touchConversation($db, $conversationId);

// Return the bot's response
http_response_code(200);
$successResponse = [
    'success' => true,
    'message' => $botMessage,
    'timestamp' => date('Y-m-d H:i:s'),
    'conversation_id' => $conversationId,
    'mode' => $mode,
    'last_message_id' => $botMessageId
];

logDebug('SUCCESS - Sending response', $successResponse);
logDebug('=== Chatbot API Request Completed Successfully ===');

echo json_encode($successResponse);
?>
