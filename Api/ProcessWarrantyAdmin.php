<?php
// Admin API for Repair/Warranty Management
session_start();
header('Content-Type: application/json; charset=utf-8');

// Check admin authentication
if (!isset($_SESSION['currentUser']) || $_SESSION['currentUser']['MaQuyen'] < 2) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Database connection using DB_driver class
require_once __DIR__ . "/../Backend/Database/DbDriver.php";
$db = new DB_driver();
$db->connect();
$conn = $db->__conn;

// Get request type from GET or POST
$request = isset($_GET['request']) ? $_GET['request'] : (isset($_POST['request']) ? $_POST['request'] : '');

switch ($request) {
    case 'getallrepairs':
        getAllRepairs();
        break;
    case 'updaterepair':
        updateRepair();
        break;
    case 'deleterepair':
        deleteRepair();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

/**
 * Get all repairs with optional date filter
 */
function getAllRepairs() {
    global $conn;
    
    $dateFilter = isset($_GET['dateFilter']) ? $_GET['dateFilter'] : 'all';
    
    // Build date condition
    $dateCondition = '';
    switch ($dateFilter) {
        case 'today':
            $dateCondition = "AND DATE(NgayTao) = CURDATE()";
            break;
        case 'week':
            $dateCondition = "AND NgayTao >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $dateCondition = "AND NgayTao >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            break;
        default:
            $dateCondition = '';
    }
    
    $sql = "SELECT p.*, n.Ho, n.Ten, n.Email as UserEmail
            FROM phieusuachua p
            LEFT JOIN nguoidung n ON p.MaND = n.MaND
            WHERE 1=1 $dateCondition
            ORDER BY p.NgayTao DESC";
    
    $result = $conn->query($sql);
    
    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        return;
    }
    
    $repairs = [];
    while ($row = $result->fetch_assoc()) {
        $repairs[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $repairs]);
}

/**
 * Update repair status, cost, and result
 */
function updateRepair() {
    global $conn;
    
    $repairId = isset($_POST['repairId']) ? intval($_POST['repairId']) : 0;
    $status = isset($_POST['status']) ? intval($_POST['status']) : 1;
    $cost = isset($_POST['cost']) ? floatval($_POST['cost']) : 0;
    $result = isset($_POST['result']) ? trim($_POST['result']) : '';
    $returnDate = isset($_POST['returnDate']) && !empty($_POST['returnDate']) ? $_POST['returnDate'] : null;
    
    if ($repairId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid repair ID']);
        return;
    }
    
    // Validate status
    if ($status < 1 || $status > 4) {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        return;
    }
    
    // Update with NgayHenTra
    if ($returnDate) {
        $sql = "UPDATE phieusuachua SET 
                TrangThai = ?,
                ChiPhiSuaChua = ?,
                KetQuaSuaChua = ?,
                NgayHenTra = ?,
                NgayCapNhat = NOW()
                WHERE MaPhieu = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("idssi", $status, $cost, $result, $returnDate, $repairId);
    } else {
        $sql = "UPDATE phieusuachua SET 
                TrangThai = ?,
                ChiPhiSuaChua = ?,
                KetQuaSuaChua = ?,
                NgayCapNhat = NOW()
                WHERE MaPhieu = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("idsi", $status, $cost, $result, $repairId);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Update failed: ' . $stmt->error]);
    }
    
    $stmt->close();
}

/**
 * Delete a repair record
 */
function deleteRepair() {
    global $conn;
    
    $repairId = isset($_POST['repairId']) ? intval($_POST['repairId']) : 0;
    
    if ($repairId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid repair ID']);
        return;
    }
    
    $sql = "DELETE FROM phieusuachua WHERE MaPhieu = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $repairId);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Repair not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Delete failed: ' . $stmt->error]);
    }
    
    $stmt->close();
}
?>

