<?php
require_once __DIR__ . '/../App/Services/AccountService.php';

if (!isset($_POST['request']) && !isset($_GET['request'])) {
    die();
}

session_start();
$service = new AccountService();

if (isset($_GET['request'])) {
    switch ($_GET['request']) {
        case 'getMyOrders':
            if (!isset($_SESSION['currentUser'])) {
                die(json_encode(['success' => false, 'message' => 'Chưa đăng nhập']));
            }
            die(json_encode($service->getMyOrders((int) $_SESSION['currentUser']['MaND'])));

        case 'getOrderDetail':
            if (!isset($_SESSION['currentUser'])) {
                die(json_encode(['success' => false, 'message' => 'Chưa đăng nhập']));
            }
            $orderId = isset($_GET['MaHD']) ? (int) $_GET['MaHD'] : 0;
            die(json_encode($service->getOrderDetail((int) $_SESSION['currentUser']['MaND'], $orderId)));
    }
}

$request = $_POST['request'] ?? '';
switch ($request) {
    case 'dangxuat':
        if (isset($_SESSION['currentUser'])) {
            unset($_SESSION['currentUser']);
            die('ok');
        }
        die('no_ok');

    case 'dangnhap':
        $user = $service->loginLegacy($_POST['data_username'] ?? '', $_POST['data_pass'] ?? '');
        if ($user) {
            $_SESSION['currentUser'] = $user;
            die(json_encode($user));
        }
        die(json_encode(null));

    case 'login':
        $result = $service->login($_POST['username'] ?? '', $_POST['password'] ?? '');
        if (!empty($result['success'])) {
            $_SESSION['currentUser'] = $result['user'];
            if (isset($_POST['rememberMe']) && ($_POST['rememberMe'] === 'true' || $_POST['rememberMe'] === true)) {
                setcookie('remember_user', $_POST['username'], time() + (86400 * 30), '/');
            }
        }
        die(json_encode($result));

    case 'dangky':
        $user = $service->registerLegacy([
            'Ho' => $_POST['data_ho'] ?? '',
            'Ten' => $_POST['data_ten'] ?? '',
            'SDT' => $_POST['data_sdt'] ?? '',
            'Email' => $_POST['data_email'] ?? '',
            'DiaChi' => $_POST['data_diachi'] ?? '',
            'TaiKhoan' => $_POST['data_newUser'] ?? '',
            'MatKhau' => $_POST['data_newPass'] ?? '',
        ]);
        if ($user) {
            $_SESSION['currentUser'] = $user;
            die(json_encode($user));
        }
        die(json_encode(null));

    case 'register':
        $result = $service->register([
            'firstName' => $_POST['firstName'] ?? '',
            'lastName' => $_POST['lastName'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'password' => $_POST['password'] ?? '',
            'dob' => $_POST['dob'] ?? '',
            'address' => $_POST['address'] ?? '',
        ]);
        if (!empty($result['success'])) {
            $_SESSION['currentUser'] = $result['user'];
        }
        die(json_encode($result));

    case 'updateProfile':
        if (!isset($_SESSION['currentUser'])) {
            die(json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']));
        }
        $targetUserId = isset($_POST['MaND']) ? (int) $_POST['MaND'] : 0;
        $result = $service->updateProfile((int) $_SESSION['currentUser']['MaND'], $targetUserId, [
            'Ho' => trim($_POST['Ho'] ?? ''),
            'Ten' => trim($_POST['Ten'] ?? ''),
            'GioiTinh' => trim($_POST['GioiTinh'] ?? ''),
            'NgaySinh' => trim($_POST['NgaySinh'] ?? ''),
            'Email' => trim($_POST['Email'] ?? ''),
            'SDT' => trim($_POST['SDT'] ?? ''),
            'DiaChi' => trim($_POST['DiaChi'] ?? ''),
        ]);
        if (!empty($result['success'])) {
            foreach (['Ho', 'Ten', 'GioiTinh', 'NgaySinh', 'Email', 'SDT', 'DiaChi'] as $field) {
                $_SESSION['currentUser'][$field] = $_POST[$field] ?? '';
            }
        }
        die(json_encode($result));

    case 'changePassword':
        if (!isset($_SESSION['currentUser'])) {
            die(json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']));
        }
        $result = $service->changePassword(
            (int) $_SESSION['currentUser']['MaND'],
            (int) ($_POST['MaND'] ?? 0),
            $_POST['currentPassword'] ?? '',
            $_POST['newPassword'] ?? ''
        );
        die(json_encode($result));

    case 'getCurrentUser':
        die(json_encode($_SESSION['currentUser'] ?? null));
}

