<?php
// File xử lý CRUD cho Ưu đãi độc quyền (Series Offers)
require_once __DIR__ . "/../Backend/Database/DbBusiness.php";

header('Content-Type: application/json; charset=utf-8');

$db = new DB_business();
$db->setTable("series_offers", "id");

// Lấy action từ request
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

switch ($action) {
    case 'get_all':
        getAllOffers();
        break;
    case 'get_by_series':
        getOffersBySeries();
        break;
    case 'get_by_id':
        getOfferById();
        break;
    case 'add':
        addOffer();
        break;
    case 'update':
        updateOffer();
        break;
    case 'delete':
        deleteOffer();
        break;
    case 'toggle_status':
        toggleStatus();
        break;
    default:
        echo json_encode([
            'success' => false,
            'message' => 'Action không hợp lệ'
        ]);
}

// Hàm lấy tất cả offers
function getAllOffers() {
    global $db;
    
    try {
        $sql = "SELECT o.*, s.series_name 
                FROM series_offers o 
                LEFT JOIN product_series s ON o.series_id = s.id 
                ORDER BY o.series_id ASC, o.sort_order ASC, o.id DESC";
        $result = $db->get_list($sql);
        
        echo json_encode([
            'success' => true,
            'data' => $result ? $result : []
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi: ' . $e->getMessage()
        ]);
    }
}

// Hàm lấy offers theo series_id
function getOffersBySeries() {
    global $db;
    
    $series_id = isset($_GET['series_id']) ? intval($_GET['series_id']) : 0;
    
    if ($series_id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Series ID không hợp lệ',
            'data' => []
        ]);
        return;
    }
    
    try {
        $sql = "SELECT * FROM series_offers 
                WHERE series_id = $series_id AND is_active = 1 
                ORDER BY sort_order ASC, id ASC";
        $result = $db->get_list($sql);
        
        echo json_encode([
            'success' => true,
            'data' => $result ? $result : []
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi: ' . $e->getMessage(),
            'data' => []
        ]);
    }
}

// Hàm lấy offer theo id
function getOfferById() {
    global $db;
    
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'ID không hợp lệ'
        ]);
        return;
    }
    
    try {
        $result = $db->select_by_id("*", $id);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'data' => $result
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Không tìm thấy ưu đãi'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi: ' . $e->getMessage()
        ]);
    }
}

// Hàm thêm offer mới
function addOffer() {
    global $db;
    
    $series_id = isset($_POST['series_id']) ? intval($_POST['series_id']) : 0;
    $badge = isset($_POST['badge']) ? trim($_POST['badge']) : '';
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $link_text = isset($_POST['link_text']) ? trim($_POST['link_text']) : '';
    $link_url = isset($_POST['link_url']) ? trim($_POST['link_url']) : '';
    $sort_order = isset($_POST['sort_order']) ? intval($_POST['sort_order']) : 0;
    
    // Validate
    if ($series_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng chọn series']);
        return;
    }
    if (empty($badge)) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng nhập badge']);
        return;
    }
    if (empty($title)) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng nhập tiêu đề']);
        return;
    }
    
    try {
        $data = [
            'series_id' => $series_id,
            'badge' => $badge,
            'title' => $title,
            'description' => $description,
            'link_text' => $link_text,
            'link_url' => $link_url,
            'sort_order' => $sort_order,
            'is_active' => 1
        ];
        
        $result = $db->insert($data);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Thêm ưu đãi thành công',
                'id' => $result
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi khi thêm ưu đãi'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi: ' . $e->getMessage()
        ]);
    }
}

// Hàm cập nhật offer
function updateOffer() {
    global $db;
    
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $series_id = isset($_POST['series_id']) ? intval($_POST['series_id']) : 0;
    $badge = isset($_POST['badge']) ? trim($_POST['badge']) : '';
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $link_text = isset($_POST['link_text']) ? trim($_POST['link_text']) : '';
    $link_url = isset($_POST['link_url']) ? trim($_POST['link_url']) : '';
    $sort_order = isset($_POST['sort_order']) ? intval($_POST['sort_order']) : 0;
    
    // Validate
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
        return;
    }
    if ($series_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng chọn series']);
        return;
    }
    if (empty($badge)) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng nhập badge']);
        return;
    }
    if (empty($title)) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng nhập tiêu đề']);
        return;
    }
    
    try {
        $data = [
            'series_id' => $series_id,
            'badge' => $badge,
            'title' => $title,
            'description' => $description,
            'link_text' => $link_text,
            'link_url' => $link_url,
            'sort_order' => $sort_order
        ];
        
        $result = $db->update($data, $id);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Cập nhật ưu đãi thành công'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi khi cập nhật ưu đãi'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi: ' . $e->getMessage()
        ]);
    }
}

// Hàm xóa offer
function deleteOffer() {
    global $db;
    
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
        return;
    }
    
    try {
        $result = $db->delete($id);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Xóa ưu đãi thành công'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi khi xóa ưu đãi'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi: ' . $e->getMessage()
        ]);
    }
}

// Hàm toggle trạng thái
function toggleStatus() {
    global $db;
    
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
        return;
    }
    
    try {
        // Lấy trạng thái hiện tại
        $current = $db->select_by_id("is_active", $id);
        if (!$current) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy ưu đãi']);
            return;
        }
        
        $newStatus = $current['is_active'] == 1 ? 0 : 1;
        $result = $db->update(['is_active' => $newStatus], $id);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => $newStatus == 1 ? 'Đã kích hoạt ưu đãi' : 'Đã tắt ưu đãi',
                'new_status' => $newStatus
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi khi cập nhật trạng thái'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi: ' . $e->getMessage()
        ]);
    }
}
?>

