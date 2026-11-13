<?php
/**
 * File xử lý xóa sản phẩm series
 */

header('Content-Type: application/json; charset=utf-8');

// Include file kết nối database
require_once('../Backend/Database/DbDriver.php');

$db = new DB_driver();
$db->connect();
$conn = $db->__conn;

if (!isset($_POST['series_id'])) {
    die(json_encode(['success' => false, 'message' => 'Series ID không hợp lệ']));
}

$seriesId = intval($_POST['series_id']);

if ($seriesId <= 0) {
    die(json_encode(['success' => false, 'message' => 'Series ID không hợp lệ']));
}

try {
    // Bắt đầu transaction
    mysqli_begin_transaction($conn);
    
    // 1. Lấy danh sách variant IDs
    $sql = "SELECT id FROM product_variants WHERE series_id = $seriesId";
    $result = mysqli_query($conn, $sql);
    $variantIds = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $variantIds[] = $row['id'];
    }
    
    // 2. Xóa storage options và promotions cho từng variant
    if (!empty($variantIds)) {
        $variantIdsStr = implode(',', $variantIds);
        
        // 2.1. Lấy danh sách storage option IDs
        $sql = "SELECT id FROM variant_storage_options WHERE variant_id IN ($variantIdsStr)";
        $result = mysqli_query($conn, $sql);
        $storageIds = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $storageIds[] = $row['id'];
        }
        
        // 2.2. Xóa colors (nếu có storage options)
        if (!empty($storageIds)) {
            $storageIdsStr = implode(',', $storageIds);
            $sql = "DELETE FROM variant_colors WHERE storage_option_id IN ($storageIdsStr)";
            mysqli_query($conn, $sql);
        }
        
        // 2.3. Xóa variant storage options
        $sql = "DELETE FROM variant_storage_options WHERE variant_id IN ($variantIdsStr)";
        mysqli_query($conn, $sql);
        
        // 2.4. Xóa variant promotions
        $sql = "DELETE FROM variant_promotions WHERE variant_id IN ($variantIdsStr)";
        mysqli_query($conn, $sql);
    }
    
    // 3. Xóa variants
    $sql = "DELETE FROM product_variants WHERE series_id = $seriesId";
    mysqli_query($conn, $sql);
    
    // 4. Xóa series
    $sql = "DELETE FROM product_series WHERE id = $seriesId";
    $deleteResult = mysqli_query($conn, $sql);
    
    if ($deleteResult) {
        // Commit transaction
        mysqli_commit($conn);
        
        echo json_encode([
            'success' => true,
            'message' => 'Xóa sản phẩm series thành công'
        ]);
    } else {
        // Rollback nếu có lỗi
        mysqli_rollback($conn);
        
        echo json_encode([
            'success' => false,
            'message' => 'Không thể xóa sản phẩm series: ' . mysqli_error($conn)
        ]);
    }
    
} catch (Exception $e) {
    // Rollback nếu có exception
    mysqli_rollback($conn);
    
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}

$db->dis_connect();
?>

