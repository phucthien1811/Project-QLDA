<?php
require_once('../Backend/Database/DbClasses.php');
session_start();

header('Content-Type: application/json; charset=utf-8');

// Set timezone Việt Nam
date_default_timezone_set('Asia/Ho_Chi_Minh');

/**
 * ============================================================================
 * LOCK MONITOR - GIẢI THÍCH CƠ CHẾ KHÓA DATABASE
 * ============================================================================
 * 
 * Vấn đề Race Condition:
 * - Khi 2 admin cùng lên đơn cho 2 đơn hàng khác nhau mà cùng mua 1 sản phẩm
 * - Nếu không có lock, cả 2 có thể đọc được tồn kho = 1, và cả 2 đều trừ đi
 * - Kết quả: tồn kho = -1 (lỗi!)
 * 
 * Giải pháp: Sử dụng Transaction + Row-Level Locking (SELECT ... FOR UPDATE)
 * 
 * Cách hoạt động:
 * 1. BEGIN TRANSACTION - Bắt đầu giao dịch
 * 2. SELECT ... FOR UPDATE - Khóa dòng dữ liệu, các request khác phải CHỜ
 * 3. Kiểm tra số lượng tồn kho
 * 4. UPDATE giảm tồn kho
 * 5. COMMIT - Hoàn thành, giải phóng lock
 * 
 * Nếu có lỗi: ROLLBACK - Hoàn tác tất cả thay đổi
 * ============================================================================
 */

/**
 * Hàm giảm tồn kho khi lên đơn - CÓ LOCK
 * Sử dụng Transaction và SELECT FOR UPDATE để đảm bảo thread-safe
 */
function reduceStockForOrder($maHD, $db) {
    $conn = $db->__conn;
    
    // Lấy chi tiết đơn hàng
    $sql = "SELECT * FROM chitiethoadon WHERE MaHD = $maHD";
    $items = $db->get_list($sql);
    
    $errors = [];
    
    foreach ($items as $item) {
        $soLuong = intval($item['SoLuong']);
        $seriesConfig = $item['SeriesConfig'];
        
        if ($seriesConfig) {
            $config = json_decode($seriesConfig, true);
            $colorId = isset($config['colorId']) ? intval($config['colorId']) : 0;
            $storageId = isset($config['storageId']) ? intval($config['storageId']) : 0;
            
            if ($colorId > 0) {
                // ========== BẮT ĐẦU TRANSACTION VỚI LOCK ==========
                mysqli_begin_transaction($conn);
                
                try {
                    // BƯỚC 1: Khóa dòng với FOR UPDATE - các request khác phải CHỜ
                    $lockSql = "SELECT id, stock_quantity FROM variant_colors 
                                WHERE id = $colorId FOR UPDATE";
                    $lockResult = mysqli_query($conn, $lockSql);
                    $stockRow = mysqli_fetch_assoc($lockResult);
                    
                    if (!$stockRow) {
                        throw new Exception("Không tìm thấy màu sắc ID: $colorId");
                    }
                    
                    $currentStock = intval($stockRow['stock_quantity']);
                    
                    // BƯỚC 2: Kiểm tra đủ hàng không
                    if ($currentStock < $soLuong) {
                        throw new Exception("Không đủ hàng. Tồn kho: $currentStock, Yêu cầu: $soLuong");
                    }
                    
                    // BƯỚC 3: Giảm tồn kho
                    $newStock = $currentStock - $soLuong;
                    $updateSql = "UPDATE variant_colors SET stock_quantity = $newStock WHERE id = $colorId";
                    
                    if (!mysqli_query($conn, $updateSql)) {
                        throw new Exception("Lỗi cập nhật tồn kho: " . mysqli_error($conn));
                    }
                    
                    // BƯỚC 4: Commit - hoàn thành và giải phóng lock
                    mysqli_commit($conn);
                    
                } catch (Exception $e) {
                    // Rollback nếu có lỗi
                    mysqli_rollback($conn);
                    $errors[] = $e->getMessage();
                }
                // ========== KẾT THÚC TRANSACTION ==========
                
            } elseif ($storageId > 0) {
                // Tương tự cho storage option
                mysqli_begin_transaction($conn);
                
                try {
                    $lockSql = "SELECT id, stock_quantity FROM variant_storage_options 
                                WHERE id = $storageId FOR UPDATE";
                    $lockResult = mysqli_query($conn, $lockSql);
                    $stockRow = mysqli_fetch_assoc($lockResult);
                    
                    if (!$stockRow) {
                        throw new Exception("Không tìm thấy storage ID: $storageId");
                    }
                    
                    $currentStock = intval($stockRow['stock_quantity']);
                    
                    if ($currentStock < $soLuong) {
                        throw new Exception("Không đủ hàng. Tồn kho: $currentStock, Yêu cầu: $soLuong");
                    }
                    
                    $newStock = $currentStock - $soLuong;
                    $updateSql = "UPDATE variant_storage_options SET stock_quantity = $newStock WHERE id = $storageId";
                    
                    if (!mysqli_query($conn, $updateSql)) {
                        throw new Exception("Lỗi cập nhật tồn kho: " . mysqli_error($conn));
                    }
                    
                    mysqli_commit($conn);
                    
                } catch (Exception $e) {
                    mysqli_rollback($conn);
                    $errors[] = $e->getMessage();
                }
            }
        }
    }
    
    return $errors;
}

/**
 * Hàm hoàn trả tồn kho khi hủy đơn - CÓ LOCK
 */
function restoreStockForOrder($maHD, $db) {
    $conn = $db->__conn;
    
    $sql = "SELECT * FROM chitiethoadon WHERE MaHD = $maHD";
    $items = $db->get_list($sql);
    
    foreach ($items as $item) {
        $soLuong = intval($item['SoLuong']);
        $seriesConfig = $item['SeriesConfig'];
        
        if ($seriesConfig) {
            $config = json_decode($seriesConfig, true);
            $colorId = isset($config['colorId']) ? intval($config['colorId']) : 0;
            $storageId = isset($config['storageId']) ? intval($config['storageId']) : 0;
            
            if ($colorId > 0) {
                // Transaction với lock để hoàn trả an toàn
                mysqli_begin_transaction($conn);
                
                try {
                    // Lock dòng trước khi update
                    $lockSql = "SELECT id, stock_quantity FROM variant_colors 
                                WHERE id = $colorId FOR UPDATE";
                    $lockResult = mysqli_query($conn, $lockSql);
                    $stockRow = mysqli_fetch_assoc($lockResult);
                    
                    if ($stockRow) {
                        $newStock = intval($stockRow['stock_quantity']) + $soLuong;
                        $updateSql = "UPDATE variant_colors SET stock_quantity = $newStock WHERE id = $colorId";
                        mysqli_query($conn, $updateSql);
                    }
                    
                    mysqli_commit($conn);
                } catch (Exception $e) {
                    mysqli_rollback($conn);
                }
                
            } elseif ($storageId > 0) {
                mysqli_begin_transaction($conn);
                
                try {
                    $lockSql = "SELECT id, stock_quantity FROM variant_storage_options 
                                WHERE id = $storageId FOR UPDATE";
                    $lockResult = mysqli_query($conn, $lockSql);
                    $stockRow = mysqli_fetch_assoc($lockResult);
                    
                    if ($stockRow) {
                        $newStock = intval($stockRow['stock_quantity']) + $soLuong;
                        $updateSql = "UPDATE variant_storage_options SET stock_quantity = $newStock WHERE id = $storageId";
                        mysqli_query($conn, $updateSql);
                    }
                    
                    mysqli_commit($conn);
                } catch (Exception $e) {
                    mysqli_rollback($conn);
                }
            }
        }
    }
}

$request = isset($_POST['request']) ? $_POST['request'] : '';

switch($request) {
    
    case 'getall':
        // Lấy tất cả đơn hàng
        $db = new DB_driver();
        $sql = "SELECT h.*, n.Ho, n.Ten, n.Email 
                FROM hoadon h 
                LEFT JOIN nguoidung n ON h.MaND = n.MaND 
                ORDER BY h.MaHD DESC";
        $result = $db->get_list($sql);
        
        die(json_encode([
            'success' => true,
            'data' => $result
        ]));
        break;
    
    case 'getdetail':
        // Lấy chi tiết đơn hàng (sản phẩm)
        $maHD = isset($_POST['MaHD']) ? intval($_POST['MaHD']) : 0;
        
        if (!$maHD) {
            die(json_encode(['success' => false, 'message' => 'Thiếu mã đơn hàng']));
        }
        
        $db = new DB_driver();
        $sql = "SELECT ct.* FROM chitiethoadon ct WHERE ct.MaHD = $maHD";
        $result = $db->get_list($sql);
        
        die(json_encode([
            'success' => true,
            'data' => $result
        ]));
        break;
    
    case 'updatestatus':
        // Cập nhật trạng thái đơn hàng
        $maHD = isset($_POST['MaHD']) ? intval($_POST['MaHD']) : 0;
        $trangThai = isset($_POST['TrangThai']) ? intval($_POST['TrangThai']) : 1;
        
        if (!$maHD) {
            die(json_encode(['success' => false, 'message' => 'Thiếu mã đơn hàng']));
        }
        
        $db = new DB_driver();
        
        // Lấy trạng thái hiện tại của đơn hàng
        $currentOrder = $db->get_list("SELECT TrangThai FROM hoadon WHERE MaHD = $maHD");
        $currentStatus = $currentOrder ? intval($currentOrder[0]['TrangThai']) : 0;
        
        // Nếu chuyển sang trạng thái 3 (Đã lên đơn) và trước đó chưa phải 3, 4, 5 -> giảm tồn kho
        if ($trangThai == 3 && $currentStatus < 3) {
            $stockErrors = reduceStockForOrder($maHD, $db);
            
            // Nếu có lỗi (không đủ hàng), không cho lên đơn
            if (!empty($stockErrors)) {
                die(json_encode([
                    'success' => false, 
                    'message' => 'Không thể lên đơn: ' . implode(', ', $stockErrors)
                ]));
            }
        }
        
        // Nếu hủy đơn hàng (đã lên đơn rồi) -> hoàn trả tồn kho
        if ($trangThai == 0 && $currentStatus >= 3) {
            restoreStockForOrder($maHD, $db);
        }
        
        $result = $db->update('hoadon', ['TrangThai' => $trangThai], "MaHD = $maHD");
        
        // Nếu hoàn thành và là COD thì tự động đánh dấu đã thanh toán
        if ($trangThai == 5) {
            $order = $db->get_list("SELECT PhuongThucTT FROM hoadon WHERE MaHD = $maHD");
            if ($order && $order[0]['PhuongThucTT'] == 'cod') {
                $db->update('hoadon', ['DaThanhToan' => 1], "MaHD = $maHD");
            }
        }
        
        $statusText = getStatusText($trangThai);
        die(json_encode([
            'success' => true,
            'message' => "Đã cập nhật: $statusText"
        ]));
        break;
    
    case 'markpaid':
        // Đánh dấu đã thanh toán
        $maHD = isset($_POST['MaHD']) ? intval($_POST['MaHD']) : 0;
        
        if (!$maHD) {
            die(json_encode(['success' => false, 'message' => 'Thiếu mã đơn hàng']));
        }
        
        $db = new DB_driver();
        $result = $db->update('hoadon', ['DaThanhToan' => 1], "MaHD = $maHD");
        
        die(json_encode([
            'success' => true,
            'message' => 'Đã cập nhật thanh toán'
        ]));
        break;
    
    case 'delete':
        // Xóa đơn hàng
        $maHD = isset($_POST['MaHD']) ? intval($_POST['MaHD']) : 0;
        
        if (!$maHD) {
            die(json_encode(['success' => false, 'message' => 'Thiếu mã đơn hàng']));
        }
        
        $db = new DB_driver();
        
        // Xóa chi tiết trước
        $db->remove('chitiethoadon', "MaHD = $maHD");
        // Xóa đơn hàng
        $db->remove('hoadon', "MaHD = $maHD");
        
        die(json_encode([
            'success' => true,
            'message' => 'Đã xóa đơn hàng'
        ]));
        break;
    
    case 'getstats':
        // Lấy thống kê
        $db = new DB_driver();
        
        $stats = [
            'total' => 0,
            'pending' => 0,
            'confirmed' => 0,
            'processing' => 0,
            'shipping' => 0,
            'completed' => 0,
            'cancelled' => 0,
            'revenue' => 0
        ];
        
        // Total orders by status
        $sql = "SELECT TrangThai, COUNT(*) as count FROM hoadon GROUP BY TrangThai";
        $result = $db->get_list($sql);
        
        foreach ($result as $row) {
            $stats['total'] += $row['count'];
            switch($row['TrangThai']) {
                case 1: $stats['pending'] = $row['count']; break;
                case 2: $stats['confirmed'] = $row['count']; break;
                case 3: $stats['processing'] = $row['count']; break;
                case 4: $stats['shipping'] = $row['count']; break;
                case 5: $stats['completed'] = $row['count']; break;
                case 0: $stats['cancelled'] = $row['count']; break;
            }
        }
        
        // Revenue from completed orders
        $sql = "SELECT SUM(TongTien) as total FROM hoadon WHERE TrangThai = 5";
        $result = $db->get_list($sql);
        $stats['revenue'] = $result[0]['total'] ?: 0;
        
        die(json_encode([
            'success' => true,
            'data' => $stats
        ]));
        break;
    
    default:
        die(json_encode(['success' => false, 'message' => 'Invalid request']));
}

// Helper function
function getStatusText($status) {
    switch($status) {
        case 0: return 'Đã hủy';
        case 1: return 'Chờ xử lý';
        case 2: return 'Đã xác nhận';
        case 3: return 'Đã lên đơn';
        case 4: return 'Đang giao';
        case 5: return 'Hoàn thành';
        default: return 'Không rõ';
    }
}
?>

