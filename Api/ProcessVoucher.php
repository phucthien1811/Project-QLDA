<?php
header('Content-Type: application/json; charset=utf-8');

// Database connection
require_once '../Backend/Database/DbDriver.php';

$db = new DB_driver();
$db->connect();
$conn = $db->__conn;

$request = isset($_GET['request']) ? $_GET['request'] : (isset($_POST['request']) ? $_POST['request'] : '');

switch ($request) {
    case 'getAll':
        getAllVouchers();
        break;
    case 'getById':
        getVoucherById($_GET['id']);
        break;
    case 'add':
        addVoucher();
        break;
    case 'update':
        updateVoucher();
        break;
    case 'delete':
        deleteVoucher();
        break;
    case 'toggle':
        toggleVoucherStatus();
        break;
    case 'validate':
        validateVoucher();
        break;
    case 'apply':
        applyVoucher();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        break;
}

// Get all vouchers
function getAllVouchers() {
    global $conn;
    
    $sql = "SELECT * FROM vouchers ORDER BY NgayTao DESC";
    $result = $conn->query($sql);
    
    $vouchers = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $vouchers[] = $row;
        }
    }
    
    echo json_encode(['success' => true, 'data' => $vouchers]);
}

// Get voucher by ID
function getVoucherById($id) {
    global $conn;
    
    $id = intval($id);
    $sql = "SELECT * FROM vouchers WHERE MaVoucher = $id";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        echo json_encode(['success' => true, 'data' => $result->fetch_assoc()]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Voucher không tồn tại']);
    }
}

// Add new voucher
function addVoucher() {
    global $conn;
    
    $code = $conn->real_escape_string(strtoupper(trim($_POST['Code'])));
    $name = $conn->real_escape_string($_POST['TenVoucher']);
    $desc = $conn->real_escape_string($_POST['MoTa'] ?? '');
    $type = $conn->real_escape_string($_POST['LoaiGiam']);
    $value = floatval($_POST['GiaTri']);
    $maxDiscount = !empty($_POST['GiamToiDa']) ? floatval($_POST['GiamToiDa']) : 'NULL';
    $minOrder = floatval($_POST['DonToiThieu'] ?? 0);
    $quantity = !empty($_POST['SoLuong']) ? intval($_POST['SoLuong']) : 'NULL';
    $startDate = $conn->real_escape_string($_POST['NgayBatDau']);
    $endDate = $conn->real_escape_string($_POST['NgayKetThuc']);
    $status = intval($_POST['TrangThai'] ?? 1);
    
    // Check if code already exists
    $checkSql = "SELECT MaVoucher FROM vouchers WHERE Code = '$code'";
    $checkResult = $conn->query($checkSql);
    if ($checkResult && $checkResult->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Mã voucher đã tồn tại']);
        return;
    }
    
    // Validate discount type and value
    if ($type === 'percent' && $value > 100) {
        echo json_encode(['success' => false, 'message' => 'Giảm giá phần trăm không thể vượt quá 100%']);
        return;
    }
    
    $maxDiscountVal = $maxDiscount === 'NULL' ? 'NULL' : $maxDiscount;
    $quantityVal = $quantity === 'NULL' ? 'NULL' : $quantity;
    
    $sql = "INSERT INTO vouchers (Code, TenVoucher, MoTa, LoaiGiam, GiaTri, GiamToiDa, DonToiThieu, SoLuong, NgayBatDau, NgayKetThuc, TrangThai)
            VALUES ('$code', '$name', '$desc', '$type', $value, $maxDiscountVal, $minOrder, $quantityVal, '$startDate', '$endDate', $status)";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Thêm voucher thành công', 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $conn->error]);
    }
}

// Update voucher
function updateVoucher() {
    global $conn;
    
    $id = intval($_POST['MaVoucher']);
    $code = $conn->real_escape_string(strtoupper(trim($_POST['Code'])));
    $name = $conn->real_escape_string($_POST['TenVoucher']);
    $desc = $conn->real_escape_string($_POST['MoTa'] ?? '');
    $type = $conn->real_escape_string($_POST['LoaiGiam']);
    $value = floatval($_POST['GiaTri']);
    $maxDiscount = !empty($_POST['GiamToiDa']) ? floatval($_POST['GiamToiDa']) : 'NULL';
    $minOrder = floatval($_POST['DonToiThieu'] ?? 0);
    $quantity = !empty($_POST['SoLuong']) ? intval($_POST['SoLuong']) : 'NULL';
    $startDate = $conn->real_escape_string($_POST['NgayBatDau']);
    $endDate = $conn->real_escape_string($_POST['NgayKetThuc']);
    $status = intval($_POST['TrangThai'] ?? 1);
    
    // Check if code already exists for different voucher
    $checkSql = "SELECT MaVoucher FROM vouchers WHERE Code = '$code' AND MaVoucher != $id";
    $checkResult = $conn->query($checkSql);
    if ($checkResult && $checkResult->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Mã voucher đã tồn tại']);
        return;
    }
    
    // Validate discount type and value
    if ($type === 'percent' && $value > 100) {
        echo json_encode(['success' => false, 'message' => 'Giảm giá phần trăm không thể vượt quá 100%']);
        return;
    }
    
    $maxDiscountVal = $maxDiscount === 'NULL' ? 'NULL' : $maxDiscount;
    $quantityVal = $quantity === 'NULL' ? 'NULL' : $quantity;
    
    $sql = "UPDATE vouchers SET 
                Code = '$code',
                TenVoucher = '$name',
                MoTa = '$desc',
                LoaiGiam = '$type',
                GiaTri = $value,
                GiamToiDa = $maxDiscountVal,
                DonToiThieu = $minOrder,
                SoLuong = $quantityVal,
                NgayBatDau = '$startDate',
                NgayKetThuc = '$endDate',
                TrangThai = $status
            WHERE MaVoucher = $id";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Cập nhật voucher thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $conn->error]);
    }
}

// Delete voucher
function deleteVoucher() {
    global $conn;
    
    $id = intval($_POST['MaVoucher']);
    
    // Check if voucher has been used
    $checkSql = "SELECT DaSuDung FROM vouchers WHERE MaVoucher = $id";
    $checkResult = $conn->query($checkSql);
    if ($checkResult && $checkResult->num_rows > 0) {
        $voucher = $checkResult->fetch_assoc();
        if ($voucher['DaSuDung'] > 0) {
            echo json_encode(['success' => false, 'message' => 'Không thể xóa voucher đã được sử dụng']);
            return;
        }
    }
    
    $sql = "DELETE FROM vouchers WHERE MaVoucher = $id";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Xóa voucher thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $conn->error]);
    }
}

// Toggle voucher status
function toggleVoucherStatus() {
    global $conn;
    
    $id = intval($_POST['MaVoucher']);
    $status = intval($_POST['TrangThai']);
    
    $sql = "UPDATE vouchers SET TrangThai = $status WHERE MaVoucher = $id";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Cập nhật trạng thái thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $conn->error]);
    }
}

// Validate voucher (for checkout)
function validateVoucher() {
    global $conn;
    
    $code = $conn->real_escape_string(strtoupper(trim($_POST['code'] ?? $_GET['code'])));
    $orderTotal = floatval($_POST['total'] ?? $_GET['total'] ?? 0);
    
    $sql = "SELECT * FROM vouchers WHERE Code = '$code' AND TrangThai = 1";
    $result = $conn->query($sql);
    
    if (!$result || $result->num_rows == 0) {
        echo json_encode(['success' => false, 'message' => 'Mã voucher không tồn tại hoặc không hoạt động']);
        return;
    }
    
    $voucher = $result->fetch_assoc();
    $now = new DateTime();
    $startDate = new DateTime($voucher['NgayBatDau']);
    $endDate = new DateTime($voucher['NgayKetThuc']);
    
    // Check date validity
    if ($now < $startDate) {
        echo json_encode(['success' => false, 'message' => 'Voucher chưa đến thời gian sử dụng']);
        return;
    }
    
    if ($now > $endDate) {
        echo json_encode(['success' => false, 'message' => 'Voucher đã hết hạn']);
        return;
    }
    
    // Check quantity
    if ($voucher['SoLuong'] !== null && $voucher['DaSuDung'] >= $voucher['SoLuong']) {
        echo json_encode(['success' => false, 'message' => 'Voucher đã hết lượt sử dụng']);
        return;
    }
    
    // Check minimum order
    if ($orderTotal < $voucher['DonToiThieu']) {
        echo json_encode([
            'success' => false, 
            'message' => 'Đơn hàng chưa đạt giá trị tối thiểu ' . number_format($voucher['DonToiThieu']) . 'đ'
        ]);
        return;
    }
    
    // Calculate discount
    $discount = 0;
    if ($voucher['LoaiGiam'] === 'percent') {
        $discount = $orderTotal * ($voucher['GiaTri'] / 100);
        if ($voucher['GiamToiDa'] !== null && $discount > $voucher['GiamToiDa']) {
            $discount = $voucher['GiamToiDa'];
        }
    } else {
        $discount = $voucher['GiaTri'];
    }
    
    // Make sure discount doesn't exceed order total
    if ($discount > $orderTotal) {
        $discount = $orderTotal;
    }
    
    echo json_encode([
        'success' => true,
        'voucher' => [
            'code' => $voucher['Code'],
            'name' => $voucher['TenVoucher'],
            'type' => $voucher['LoaiGiam'],
            'value' => $voucher['GiaTri'],
            'maxDiscount' => $voucher['GiamToiDa']
        ],
        'discount' => $discount,
        'message' => 'Áp dụng voucher thành công! Giảm ' . number_format($discount) . 'đ'
    ]);
}

// Apply voucher (increase usage count)
function applyVoucher() {
    global $conn;
    
    $code = $conn->real_escape_string(strtoupper(trim($_POST['code'])));
    
    $sql = "UPDATE vouchers SET DaSuDung = DaSuDung + 1 WHERE Code = '$code'";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Đã áp dụng voucher']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $conn->error]);
    }
}
?>

