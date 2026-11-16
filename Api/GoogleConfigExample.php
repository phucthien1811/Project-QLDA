<?php
// Google OAuth Configuration - TEMPLATE FILE
// Copy file này thành GoogleConfig.php và điền thông tin của bạn

define('GOOGLE_CLIENT_ID', 'YOUR_GOOGLE_CLIENT_ID_HERE');
define('GOOGLE_CLIENT_SECRET', 'YOUR_GOOGLE_CLIENT_SECRET_HERE');
define('GOOGLE_REDIRECT_URI', 'http://localhost/Project-PHP/Api/GoogleCallback.php');

// Khởi tạo Google Client
require_once __DIR__ . '/../vendor/autoload.php';

function getGoogleClient() {
    $client = new Google_Client();
    $client->setClientId(GOOGLE_CLIENT_ID);
    $client->setClientSecret(GOOGLE_CLIENT_SECRET);
    $client->setRedirectUri(GOOGLE_REDIRECT_URI);
    $client->addScope("email");
    $client->addScope("profile");
    
    // Bắt buộc hiển thị màn hình chọn tài khoản và xác nhận quyền
    $client->setPrompt('consent select_account');
    
    return $client;
}
?>

