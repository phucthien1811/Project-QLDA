<?php
require_once __DIR__ . '/../App/Services/UserService.php';
session_start();
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Asia/Ho_Chi_Minh');

$service = new UserService();
$request = $_POST['request'] ?? '';

switch ($request) {
    case 'getall':
        die(json_encode($service->getAll()));

    case 'getone':
        die(json_encode($service->getOne((int) ($_POST['MaND'] ?? 0))));

    case 'add':
        die(json_encode($service->add($_POST)));

    case 'update':
        die(json_encode($service->update($_POST)));

    case 'delete':
        die(json_encode($service->delete((int) ($_POST['MaND'] ?? 0))));

    case 'toggle_status':
        die(json_encode($service->toggleStatus((int) ($_POST['MaND'] ?? 0))));

    default:
        die(json_encode(['success' => false, 'message' => 'Invalid request']));
}

