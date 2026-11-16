<?php
session_start();
require_once 'GoogleConfig.php';

// Tạo Google Client
$client = getGoogleClient();

// Tạo URL để đăng nhập
$authUrl = $client->createAuthUrl();

// Chuyển hướng đến trang đăng nhập Google
header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
exit();
?>

