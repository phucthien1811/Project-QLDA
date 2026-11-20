<?php
/**
 * API xử lý My Rewards
 * Tính điểm thưởng và hạng thành viên dựa trên tổng chi tiêu
 */

require_once("../Backend/Database/DbClasses.php");
session_start();

// Set JSON header
header('Content-Type: application/json; charset=utf-8');

// Hằng số cấu hình rewards
define('POINTS_RATE_BLUE', 0.01);      // 1% cho Blue
define('POINTS_RATE_GOLD', 0.03);      // 3% cho Gold  
define('POINTS_RATE_PLATINUM', 0.05);  // 5% cho Platinum
define('GOLD_THRESHOLD', 45000000);    // 45 triệu để lên Gold
define('PLATINUM_THRESHOLD', 110000000); // 110 triệu để lên Platinum

$request = isset($_GET['request']) ? $_GET['request'] : (isset($_POST['request']) ? $_POST['request'] : '');

switch ($request) {
    case 'getmyrewards':
        getMyRewards();
        break;
    default:
        die(json_encode(['success' => false, 'message' => 'Invalid request']));
}

/**
 * Lấy thông tin rewards của user hiện tại
 */
function getMyRewards() {
    // Kiểm tra đăng nhập
    if (!isset($_SESSION['currentUser'])) {
        die(json_encode(['success' => false, 'message' => 'Chưa đăng nhập']));
    }
    
    $maNguoiDung = $_SESSION['currentUser']['MaND'];
    
    // Lấy thông tin user
    $nguoiDungBUS = new NguoiDungBUS();
    $user = $nguoiDungBUS->get_list("SELECT * FROM nguoidung WHERE MaND = $maNguoiDung");
    
    if (empty($user)) {
        die(json_encode(['success' => false, 'message' => 'Không tìm thấy người dùng']));
    }
    
    $user = $user[0];
    
    // Lấy tổng chi tiêu từ các đơn hàng hoàn thành (TrangThai = 5)
    $hoadonBUS = new HoaDonBUS();
    $orders = $hoadonBUS->get_list("SELECT * FROM hoadon WHERE MaND = $maNguoiDung AND TrangThai = 5");
    
    $totalSpent = 0;
    $totalPoints = 0;
    $firstOrderDate = null;
    
    foreach ($orders as $order) {
        $orderTotal = floatval($order['TongTien']);
        $totalSpent += $orderTotal;
        
        // Ghi nhận ngày đơn hàng đầu tiên
        if (!$firstOrderDate || strtotime($order['NgayLap']) < strtotime($firstOrderDate)) {
            $firstOrderDate = $order['NgayLap'];
        }
        
        // Tính điểm dựa trên hạng tại thời điểm đặt hàng
        // (Đơn giản hóa: tính theo hạng hiện tại)
        $pointsRate = getPointsRate($totalSpent);
        $totalPoints += floor($orderTotal * $pointsRate);
    }
    
    // Xác định hạng
    $rank = getRank($totalSpent);
    
    // Ngày tham gia (lấy từ đơn hàng đầu tiên hoặc ngày tạo tài khoản)
    $memberSince = $firstOrderDate ? $firstOrderDate : date('Y-m-d');
    
    // Trả về dữ liệu
    die(json_encode([
        'success' => true,
        'data' => [
            'points' => $totalPoints,
            'totalSpent' => $totalSpent,
            'rank' => $rank,
            'memberSince' => $memberSince,
            'ordersCount' => count($orders)
        ]
    ]));
}

/**
 * Lấy tỷ lệ điểm thưởng dựa trên tổng chi tiêu
 */
function getPointsRate($totalSpent) {
    if ($totalSpent >= PLATINUM_THRESHOLD) {
        return POINTS_RATE_PLATINUM;
    } else if ($totalSpent >= GOLD_THRESHOLD) {
        return POINTS_RATE_GOLD;
    } else {
        return POINTS_RATE_BLUE;
    }
}

/**
 * Xác định hạng thành viên
 */
function getRank($totalSpent) {
    if ($totalSpent >= PLATINUM_THRESHOLD) {
        return 'Platinum';
    } else if ($totalSpent >= GOLD_THRESHOLD) {
        return 'Gold';
    } else {
        return 'Blue';
    }
}
?>

