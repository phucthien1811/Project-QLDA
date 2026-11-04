<?php
/**
 * MoMo Payment API - Test Environment
 * Sử dụng MoMo Test Sandbox
 */

header('Content-Type: application/json');
require_once("../Backend/Database/DbClasses.php");

// MoMo Test Credentials
$endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
$partnerCode = "MOMO";
$accessKey = "F8BBA842ECF85";
$secretKey = "K951B6PE1waDMi640xX08PD3vg6EkVlz";

// Get POST data
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

if (!$data) {
    $data = $_POST;
}

$amount = isset($data['amount']) ? $data['amount'] : 0;
$orderInfo = isset($data['orderInfo']) ? $data['orderInfo'] : 'Thanh toan don hang';
$maHD = isset($data['maHD']) ? $data['maHD'] : 0;
$maNguoiDung = isset($data['maNguoiDung']) ? $data['maNguoiDung'] : 0;

// Generate unique IDs
$requestId = time() . "";
$orderId = $maHD . "_" . $requestId;

// URLs
$baseUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF'], 2);
$redirectUrl = $baseUrl . "/momo_return.php";
$ipnUrl = $baseUrl . "/Api/momo_ipn.php";

// Extra data (base64 encoded JSON)
$extraData = base64_encode(json_encode([
    "maHD" => $maHD,
    "maNguoiDung" => $maNguoiDung,
    "paymentType" => "captureWallet"
]));

$requestType = "captureWallet";

// Create signature
$rawHash = "accessKey=" . $accessKey .
    "&amount=" . $amount .
    "&extraData=" . $extraData .
    "&ipnUrl=" . $ipnUrl .
    "&orderId=" . $orderId .
    "&orderInfo=" . $orderInfo .
    "&partnerCode=" . $partnerCode .
    "&redirectUrl=" . $redirectUrl .
    "&requestId=" . $requestId .
    "&requestType=" . $requestType;

$signature = hash_hmac("sha256", $rawHash, $secretKey);

// Request body
$requestBody = [
    'partnerCode' => $partnerCode,
    'accessKey' => $accessKey,
    'requestId' => $requestId,
    'amount' => $amount,
    'orderId' => $orderId,
    'orderInfo' => $orderInfo,
    'redirectUrl' => $redirectUrl,
    'ipnUrl' => $ipnUrl,
    'extraData' => $extraData,
    'requestType' => $requestType,
    'signature' => $signature,
    'lang' => 'vi'
];

// Send request to MoMo
$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen(json_encode($requestBody))
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo json_encode([
        'success' => false,
        'message' => 'CURL Error: ' . $error
    ]);
    exit;
}

$response = json_decode($result, true);

// Log request and response
$logData = [
    'request' => $requestBody,
    'response' => $response,
    'timestamp' => date('Y-m-d H:i:s')
];
file_put_contents("../logs/momo_create_{$maHD}_{$requestId}.json", json_encode($logData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

// Return response
if ($response && $response['resultCode'] == 0) {
    echo json_encode([
        'success' => true,
        'payUrl' => $response['payUrl'],
        'qrCodeUrl' => $response['qrCodeUrl'] ?? null,
        'deeplink' => $response['deeplink'] ?? null,
        'orderId' => $orderId,
        'amount' => $amount
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => $response['message'] ?? 'Có lỗi xảy ra',
        'resultCode' => $response['resultCode'] ?? -1
    ]);
}
?>

