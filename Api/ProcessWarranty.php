<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . "/../Backend/Database/DbDriver.php";

// Initialize database connection
$db = new DB_driver();
$db->connect();
$conn = $db->__conn;

// Get request type
$request = isset($_GET['request']) ? $_GET['request'] : (isset($_POST['request']) ? $_POST['request'] : '');

switch ($request) {
    case 'getmyproducts':
        getMyProducts();
        break;
    case 'getrepairhistory':
        getRepairHistory();
        break;
    case 'createbooking':
        createRepairBooking();
        break;
    case 'getrepairdetail':
        getRepairDetail();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        break;
}

// Get products purchased by user (from completed orders)
function getMyProducts() {
    global $conn;
    
    if (!isset($_SESSION['currentUser']) || !isset($_SESSION['currentUser']['MaND'])) {
        echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
        return;
    }
    
    $maND = $_SESSION['currentUser']['MaND'];
    
    // Get products from completed orders (TrangThai = 5)
    // Products are stored in SeriesConfig JSON field
    $sql = "SELECT 
                cthd.MaHD as MaDH,
                cthd.TenSP,
                cthd.HinhAnh,
                cthd.DonGia,
                cthd.SoLuong,
                cthd.SeriesConfig,
                hd.NgayLap as NgayMua,
                12 as ThoiGianBaoHanh
            FROM hoadon hd
            JOIN chitiethoadon cthd ON hd.MaHD = cthd.MaHD
            WHERE hd.MaND = ? AND hd.TrangThai = '5'
            ORDER BY hd.NgayLap DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $maND);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        // Parse SeriesConfig to get product details
        $seriesConfig = json_decode($row['SeriesConfig'], true);
        
        // Use seriesId or variantId as MaSP for reference
        $row['MaSP'] = isset($seriesConfig['variantId']) ? $seriesConfig['variantId'] : 
                       (isset($seriesConfig['seriesId']) ? $seriesConfig['seriesId'] : $row['MaDH']);
        
        // Default warranty 12 months
        $row['ThoiGianBaoHanh'] = 12;
        
        $products[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $products]);
}

// Get repair history
function getRepairHistory() {
    global $conn;
    
    if (!isset($_SESSION['currentUser']) || !isset($_SESSION['currentUser']['MaND'])) {
        echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
        return;
    }
    
    $maND = $_SESSION['currentUser']['MaND'];
    $dateFrom = isset($_GET['dateFrom']) ? $_GET['dateFrom'] : date('Y-m-d', strtotime('-3 years'));
    $dateTo = isset($_GET['dateTo']) ? $_GET['dateTo'] : date('Y-m-d');
    
    $sql = "SELECT 
                ps.MaPhieu,
                ps.MaSP,
                ps.TenSP,
                ps.HinhAnhSP as HinhAnh,
                ps.LoaiSuCo,
                ps.MoTa,
                ps.NgayHen,
                ps.GioHen,
                ps.TrangThai,
                ps.NgayTao,
                ps.NgayCapNhat,
                ps.ChiPhiSuaChua,
                ps.KetQuaSuaChua,
                ps.NgayHenTra,
                CASE ps.TrangThai
                    WHEN 1 THEN 'Chờ xác nhận'
                    WHEN 2 THEN 'Đang sửa chữa'
                    WHEN 3 THEN 'Hoàn tất'
                    WHEN 4 THEN 'Đã hủy'
                    ELSE 'Không xác định'
                END as TrangThaiText
            FROM phieusuachua ps
            WHERE ps.MaND = ? 
            AND DATE(ps.NgayTao) BETWEEN ? AND ?
            ORDER BY ps.NgayTao DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $maND, $dateFrom, $dateTo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $repairs = [];
    while ($row = $result->fetch_assoc()) {
        $repairs[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $repairs]);
}

// Create repair booking
function createRepairBooking() {
    global $conn;
    
    if (!isset($_SESSION['currentUser']) || !isset($_SESSION['currentUser']['MaND'])) {
        echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
        return;
    }
    
    $maND = $_SESSION['currentUser']['MaND'];
    $maSP = isset($_POST['productId']) ? $_POST['productId'] : '';
    $tenSP = isset($_POST['productName']) ? $_POST['productName'] : '';
    $hinhAnhSP = isset($_POST['productImage']) ? $_POST['productImage'] : '';
    $loaiSuCo = isset($_POST['issueType']) ? $_POST['issueType'] : '';
    $moTa = isset($_POST['issueDescription']) ? $_POST['issueDescription'] : '';
    $ngayHen = isset($_POST['bookingDate']) ? $_POST['bookingDate'] : '';
    $gioHen = isset($_POST['bookingTime']) ? $_POST['bookingTime'] : '';
    $hoTen = isset($_POST['contactName']) ? $_POST['contactName'] : '';
    $sdt = isset($_POST['contactPhone']) ? $_POST['contactPhone'] : '';
    $email = isset($_POST['contactEmail']) ? $_POST['contactEmail'] : '';
    $ghiChu = isset($_POST['contactNote']) ? $_POST['contactNote'] : '';
    
    // Validate
    if (empty($loaiSuCo) || empty($ngayHen) || empty($gioHen) || empty($hoTen) || empty($sdt)) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin']);
        return;
    }
    
    // Map issue type to Vietnamese
    $issueTypeMap = [
        'screen' => 'Màn hình',
        'battery' => 'Pin',
        'camera' => 'Camera',
        'speaker' => 'Loa/Mic',
        'software' => 'Phần mềm',
        'other' => 'Khác'
    ];
    $loaiSuCoText = isset($issueTypeMap[$loaiSuCo]) ? $issueTypeMap[$loaiSuCo] : $loaiSuCo;
    
    // Insert into PhieuSuaChua table
    $sql = "INSERT INTO phieusuachua (MaND, MaSP, TenSP, HinhAnhSP, LoaiSuCo, MoTa, NgayHen, GioHen, HoTen, SDT, Email, GhiChu, TrangThai, NgayTao)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssssssss", $maND, $maSP, $tenSP, $hinhAnhSP, $loaiSuCoText, $moTa, $ngayHen, $gioHen, $hoTen, $sdt, $email, $ghiChu);
    
    if ($stmt->execute()) {
        $bookingId = $conn->insert_id;
        echo json_encode(['success' => true, 'bookingId' => $bookingId, 'message' => 'Đặt lịch thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $conn->error]);
    }
}

// Get repair detail
function getRepairDetail() {
    global $conn;
    
    if (!isset($_SESSION['currentUser']) || !isset($_SESSION['currentUser']['MaND'])) {
        echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
        return;
    }
    
    $maND = $_SESSION['currentUser']['MaND'];
    $maPhieu = isset($_GET['id']) ? $_GET['id'] : '';
    
    $sql = "SELECT 
                ps.*,
                ps.TenSP,
                ps.HinhAnhSP as HinhAnh,
                CASE ps.TrangThai
                    WHEN 1 THEN 'Chờ xác nhận'
                    WHEN 2 THEN 'Đang sửa chữa'
                    WHEN 3 THEN 'Hoàn tất'
                    WHEN 4 THEN 'Đã hủy'
                    ELSE 'Không xác định'
                END as TrangThaiText
            FROM phieusuachua ps
            WHERE ps.MaPhieu = ? AND ps.MaND = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $maPhieu, $maND);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode(['success' => true, 'data' => $row]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy phiếu sửa chữa']);
    }
}
?>

