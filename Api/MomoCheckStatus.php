<?php
/**
 * MoMo Payment Status Check API
 * Kiểm tra trạng thái thanh toán MoMo
 */

header('Content-Type: application/json');

// MoMo Test Credentials
$endpoint = "https://test-payment.momo.vn/v2/gateway/api/query";
$partnerCode = "MOMO";
$accessKey = "F8BBA842ECF85";
$secretKey = "K951B6PE1waDMi640xX08PD3vg6EkVlz";

// Get order ID from request
$orderId = isset($_GET['orderId']) ? $_GET['orderId'] : (isset($_POST['orderId']) ? $_POST['orderId'] : '');

if (empty($orderId)) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing orderId'
    ]);
    exit;
}

$requestId = time() . "";

// Create signature for query
$rawHash = "accessKey=" . $accessKey .
    "&orderId=" . $orderId .
    "&partnerCode=" . $partnerCode .
    "&requestId=" . $requestId;

$signature = hash_hmac("sha256", $rawHash, $secretKey);

// Request body
$requestBody = [
    'partnerCode' => $partnerCode,
    'accessKey' => $accessKey,
    'requestId' => $requestId,
    'orderId' => $orderId,
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

// Log query
file_put_contents("../logs/momo_query_{$orderId}.json", json_encode([
    'request' => $requestBody,
    'response' => $response,
    'timestamp' => date('Y-m-d H:i:s')
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Check payment status
// resultCode = 0 means success
if ($response && isset($response['resultCode'])) {
    $isPaid = ($response['resultCode'] == 0);
    
    echo json_encode([
        'success' => true,
        'isPaid' => $isPaid,
        'resultCode' => $response['resultCode'],
        'message' => $response['message'] ?? '',
        'amount' => $response['amount'] ?? 0,
        'transId' => $response['transId'] ?? ''
    ]);
} else {
    echo json_encode([
        'success' => false,
        'isPaid' => false,
        'message' => 'Could not get payment status'
    ]);
}
?>
