<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../App/Services/DashboardService.php';

$service = new DashboardService();
$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
    exit;
}

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'getstats':
            echo json_encode($service->getStats());
            break;
        case 'getrevenuechart':
            echo json_encode($service->getRevenueChart($_GET['period'] ?? '7days'));
            break;
        case 'getrecentorders':
            echo json_encode($service->getRecentOrders((int) ($_GET['limit'] ?? 5)));
            break;
        case 'gettopproducts':
            echo json_encode($service->getTopProducts((int) ($_GET['limit'] ?? 5)));
            break;
        case 'getorderstatus':
            echo json_encode($service->getOrderStatus());
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

