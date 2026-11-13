<?php
// File xử lý CRUD cho 3AM Care+
require_once __DIR__ . "/../Backend/Database/DbBusiness.php";

header('Content-Type: application/json; charset=utf-8');

$db = new DB_business();
$db->setTable("careplan", "id");

// Lấy action từ request
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

switch ($action) {
    case 'get_all':
        getAllCarePlans();
        break;
    case 'get_by_id':
        getCarePlanById();
        break;
    case 'get_by_variant':
        getCarePlansByVariant();
        break;
    case 'get_by_series':
        getCarePlansBySeries();
        break;
    case 'add':
        addCarePlan();
        break;
    case 'update':
        updateCarePlan();
        break;
    case 'delete':
        deleteCarePlan();
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

// Hàm lấy tất cả care plans
function getAllCarePlans() {
    global $db;
    
    try {
        $sql = "SELECT * FROM careplan ORDER BY sort_order ASC, id DESC";
        $result = $db->get_list($sql);
        
        echo json_encode([
            'success' => true,
            'data' => $result
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi: ' . $e->getMessage()
        ]);
    }
}

// Hàm lấy care plans theo variant_id (deprecated - dùng get_by_series)
function getCarePlansByVariant() {
    global $db;
    
    $variant_id = isset($_GET['variant_id']) ? intval($_GET['variant_id']) : 0;
    
    if ($variant_id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Variant ID không hợp lệ',
            'data' => []
        ]);
        return;
    }
    
    try {
        $sql = "SELECT c.* FROM careplan c 
                INNER JOIN variant_careplans vc ON c.id = vc.careplan_id 
                WHERE vc.variant_id = $variant_id AND c.is_active = 1 
                ORDER BY vc.sort_order ASC, c.sort_order ASC";
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

// Hàm lấy care plans theo series_id
function getCarePlansBySeries() {
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
        $sql = "SELECT c.* FROM careplan c 
                INNER JOIN series_careplans sc ON c.id = sc.careplan_id 
                WHERE sc.series_id = $series_id AND c.is_active = 1 
                ORDER BY sc.sort_order ASC, c.sort_order ASC";
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

// Hàm lấy care plan theo id
function getCarePlanById() {
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
                'message' => 'Không tìm thấy gói bảo hành'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi: ' . $e->getMessage()
        ]);
    }
}

// Hàm thêm care plan mới
function addCarePlan() {
    global $db;
    
    // Lấy dữ liệu từ POST
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $original_price = isset($_POST['original_price']) ? floatval($_POST['original_price']) : 0;
    $discount_percent = isset($_POST['discount_percent']) ? intval($_POST['discount_percent']) : 0;
    $duration = isset($_POST['duration']) ? trim($_POST['duration']) : '';
    $plan_type = isset($_POST['plan_type']) ? trim($_POST['plan_type']) : 'protection';
    $sort_order = isset($_POST['sort_order']) ? intval($_POST['sort_order']) : 0;
    $is_active = isset($_POST['is_active']) ? intval($_POST['is_active']) : 1;
    
    // Validate
    if (empty($name)) {
        echo json_encode([
            'success' => false,
            'message' => 'Tên gói bảo hành không được để trống'
        ]);
        return;
    }
    
    if ($original_price <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Giá gốc phải lớn hơn 0'
        ]);
        return;
    }
    
    // Tính giá sau giảm
    $final_price = $original_price * (100 - $discount_percent) / 100;
    
    // Chuẩn bị data
    $data = [
        'name' => $name,
        'description' => $description,
        'original_price' => $original_price,
        'discount_percent' => $discount_percent,
        'final_price' => $final_price,
        'duration' => $duration,
        'plan_type' => $plan_type,
        'sort_order' => $sort_order,
        'is_active' => $is_active,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    try {
        $result = $db->add_new($data);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Thêm gói bảo hành thành công'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Không thể thêm gói bảo hành'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi: ' . $e->getMessage()
        ]);
    }
}

// Hàm cập nhật care plan
function updateCarePlan() {
    global $db;
    
    // Lấy dữ liệu từ POST
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $original_price = isset($_POST['original_price']) ? floatval($_POST['original_price']) : 0;
    $discount_percent = isset($_POST['discount_percent']) ? intval($_POST['discount_percent']) : 0;
    $duration = isset($_POST['duration']) ? trim($_POST['duration']) : '';
    $plan_type = isset($_POST['plan_type']) ? trim($_POST['plan_type']) : 'protection';
    $sort_order = isset($_POST['sort_order']) ? intval($_POST['sort_order']) : 0;
    $is_active = isset($_POST['is_active']) ? intval($_POST['is_active']) : 1;
    
    // Validate
    if ($id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'ID không hợp lệ'
        ]);
        return;
    }
    
    if (empty($name)) {
        echo json_encode([
            'success' => false,
            'message' => 'Tên gói bảo hành không được để trống'
        ]);
        return;
    }
    
    if ($original_price <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Giá gốc phải lớn hơn 0'
        ]);
        return;
    }
    
    // Tính giá sau giảm
    $final_price = $original_price * (100 - $discount_percent) / 100;
    
    // Chuẩn bị data
    $data = [
        'name' => $name,
        'description' => $description,
        'original_price' => $original_price,
        'discount_percent' => $discount_percent,
        'final_price' => $final_price,
        'duration' => $duration,
        'plan_type' => $plan_type,
        'sort_order' => $sort_order,
        'is_active' => $is_active,
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    try {
        $result = $db->update_by_id($data, $id);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Cập nhật gói bảo hành thành công'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Không thể cập nhật gói bảo hành'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi: ' . $e->getMessage()
        ]);
    }
}

// Hàm xóa care plan
function deleteCarePlan() {
    global $db;
    
    $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    
    if ($id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'ID không hợp lệ'
        ]);
        return;
    }
    
    try {
        $result = $db->delete_by_id($id);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Xóa gói bảo hành thành công'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Không thể xóa gói bảo hành'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi: ' . $e->getMessage()
        ]);
    }
}

// Hàm bật/tắt trạng thái
function toggleStatus() {
    global $db;
    
    $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    
    if ($id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'ID không hợp lệ'
        ]);
        return;
    }
    
    try {
        // Lấy trạng thái hiện tại
        $current = $db->select_by_id("is_active", $id);
        
        if (!$current) {
            echo json_encode([
                'success' => false,
                'message' => 'Không tìm thấy gói bảo hành'
            ]);
            return;
        }
        
        $new_status = $current['is_active'] == 1 ? 0 : 1;
        
        $result = $db->update_by_id([
            'is_active' => $new_status,
            'updated_at' => date('Y-m-d H:i:s')
        ], $id);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => $new_status == 1 ? 'Đã kích hoạt gói bảo hành' : 'Đã vô hiệu hóa gói bảo hành',
                'new_status' => $new_status
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Không thể cập nhật trạng thái'
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

