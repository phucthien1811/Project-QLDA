<?php
/**
 * File xử lý thêm sản phẩm đa phiên bản (Product Series)
 * Nhận dữ liệu từ form và lưu vào database
 */

// Bật hiển thị lỗi để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Header JSON
header('Content-Type: application/json; charset=utf-8');

// Chỉ cho phép POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['success' => false, 'message' => 'Chỉ chấp nhận POST request']));
}

// Include file kết nối database
require_once('../Backend/Database/DbDriver.php');

// Khởi tạo database connection
$db = new DB_driver();
$db->connect();
$conn = $db->__conn;

// Bắt đầu transaction
mysqli_begin_transaction($conn);

try {
    // ==========================================================================
    // BƯỚC 1: XỬ LÝ UPLOAD ẢNH SERIES
    // ==========================================================================
    $seriesImagePath = null;
    
    if (isset($_FILES['series_image']) && $_FILES['series_image']['error'] === UPLOAD_ERR_OK) {
        $seriesImagePath = uploadImage($_FILES['series_image'], 'series');
        if ($seriesImagePath === false) {
            throw new Exception('Lỗi upload ảnh series');
        }
    }
    
    // ==========================================================================
    // BƯỚC 2: LẤY THÔNG TIN SERIES VÀ INSERT VÀO DATABASE
    // ==========================================================================
    $seriesName = mysqli_real_escape_string($conn, trim($_POST['series_name']));
    $seriesDescription = isset($_POST['series_description']) ? mysqli_real_escape_string($conn, trim($_POST['series_description'])) : '';
    $displayPrice = isset($_POST['display_price']) ? intval($_POST['display_price']) : 0;
    $slug = createSlug($seriesName);
    
    // Lấy thông số kỹ thuật JSON
    $specsJson = '';
    if (isset($_POST['specs_json']) && !empty(trim($_POST['specs_json']))) {
        $specsJson = trim($_POST['specs_json']);
        // Validate JSON
        $decoded = json_decode($specsJson);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON thông số kỹ thuật không hợp lệ');
        }
        $specsJson = mysqli_real_escape_string($conn, $specsJson);
    }
    
    // Kiểm tra trùng slug
    $checkSlug = mysqli_query($conn, "SELECT id FROM product_series WHERE slug = '$slug'");
    if (mysqli_num_rows($checkSlug) > 0) {
        $slug = $slug . '-' . time();
    }
    
    // Insert series
    $sqlSeries = "INSERT INTO product_series (series_name, slug, description, specs_json, main_image, display_price) 
                  VALUES ('$seriesName', '$slug', '$seriesDescription', " . 
                  ($specsJson ? "'$specsJson'" : "NULL") . ", " .
                  ($seriesImagePath ? "'$seriesImagePath'" : "NULL") . ", $displayPrice)";
    
    if (!mysqli_query($conn, $sqlSeries)) {
        throw new Exception('Lỗi insert series: ' . mysqli_error($conn));
    }
    
    $seriesId = mysqli_insert_id($conn);
    
    // ==========================================================================
    // BƯỚC 2.5: LƯU SERIES CARE PLANS
    // ==========================================================================
    $carePlanCount = 0;
    if (isset($_POST['series_careplans']) && is_array($_POST['series_careplans'])) {
        foreach ($_POST['series_careplans'] as $cpIndex => $cpData) {
            $careplanId = isset($cpData['careplan_id']) ? intval($cpData['careplan_id']) : 0;
            
            if ($careplanId > 0) {
                $sqlCarePlan = "INSERT INTO series_careplans (series_id, careplan_id, sort_order) 
                                VALUES ($seriesId, $careplanId, $cpIndex)";
                if (!mysqli_query($conn, $sqlCarePlan)) {
                    throw new Exception('Lỗi insert series careplan: ' . mysqli_error($conn));
                }
                $carePlanCount++;
            }
        }
    }
    
    // ==========================================================================
    // BƯỚC 2.6: LƯU SERIES OFFERS (ƯU ĐÃI ĐỘC QUYỀN)
    // ==========================================================================
    $offerCount = 0;
    if (isset($_POST['series_offers']) && is_array($_POST['series_offers'])) {
        foreach ($_POST['series_offers'] as $ofIndex => $ofData) {
            $badge = isset($ofData['badge']) ? mysqli_real_escape_string($conn, trim($ofData['badge'])) : '';
            $title = isset($ofData['title']) ? mysqli_real_escape_string($conn, trim($ofData['title'])) : '';
            $description = isset($ofData['description']) ? mysqli_real_escape_string($conn, trim($ofData['description'])) : '';
            $linkText = isset($ofData['link_text']) ? mysqli_real_escape_string($conn, trim($ofData['link_text'])) : '';
            $linkUrl = isset($ofData['link_url']) ? mysqli_real_escape_string($conn, trim($ofData['link_url'])) : '';
            // Thứ tự tự động theo vị trí trong danh sách (bắt đầu từ 1)
            $sortOrder = $ofIndex + 1;
            
            if (!empty($badge) && !empty($title)) {
                $sqlOffer = "INSERT INTO series_offers (series_id, badge, title, description, link_text, link_url, sort_order) 
                             VALUES ($seriesId, '$badge', '$title', '$description', '$linkText', '$linkUrl', $sortOrder)";
                if (!mysqli_query($conn, $sqlOffer)) {
                    throw new Exception('Lỗi insert series offer: ' . mysqli_error($conn));
                }
                $offerCount++;
            }
        }
    }
    
    // ==========================================================================
    // BƯỚC 3: XỬ LÝ CÁC PHIÊN BẢN (VARIANTS)
    // ==========================================================================
    if (!isset($_POST['variants']) || !is_array($_POST['variants'])) {
        throw new Exception('Không có dữ liệu phiên bản');
    }
    
    $variantCount = 0;
    $storageCount = 0;
    $promoCount = 0;
    
    foreach ($_POST['variants'] as $variantIndex => $variantData) {
        // Lấy thông tin variant - sử dụng 'name' thay vì 'model_name'
        $modelName = isset($variantData['name']) ? mysqli_real_escape_string($conn, trim($variantData['name'])) : '';
        $screenSize = isset($variantData['screen_size']) ? mysqli_real_escape_string($conn, trim($variantData['screen_size'])) : '';
        $specs = isset($variantData['specs']) ? mysqli_real_escape_string($conn, trim($variantData['specs'])) : '';
        
        // Validate JSON specs
        if (!empty($specs)) {
            $specsDecoded = json_decode($specs);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $specs = '{}'; // Nếu không phải JSON hợp lệ, set về empty object
            }
        } else {
            $specs = '{}';
        }
        
        // Xử lý upload ảnh variant (có thể nhiều ảnh)
        $variantImages = [];
        if (isset($_FILES['variants']['name'][$variantIndex]['images'])) {
            $imageFiles = $_FILES['variants'];
            
            // Xử lý multiple file upload
            foreach ($imageFiles['name'][$variantIndex]['images'] as $key => $fileName) {
                if ($imageFiles['error'][$variantIndex]['images'][$key] === UPLOAD_ERR_OK) {
                    $tmpName = $imageFiles['tmp_name'][$variantIndex]['images'][$key];
                    $fileSize = $imageFiles['size'][$variantIndex]['images'][$key];
                    
                    $imagePath = uploadImageFromTemp($tmpName, $fileName, $fileSize);
                    if ($imagePath !== false) {
                        $variantImages[] = $imagePath;
                    }
                }
            }
        }
        
        $variantImagesJson = json_encode($variantImages);
        
        // Insert variant (không còn base_price)
        $sqlVariant = "INSERT INTO product_variants (series_id, variant_name, screen_size, specs, images) 
                       VALUES ($seriesId, '$modelName', " . 
                       ($screenSize ? "'$screenSize'" : "NULL") . ", '$specs', '$variantImagesJson')";
        
        if (!mysqli_query($conn, $sqlVariant)) {
            throw new Exception('Lỗi insert variant: ' . mysqli_error($conn));
        }
        
        $variantId = mysqli_insert_id($conn);
        $variantCount++;
        
        // ======================================================================
        // BƯỚC 4: XỬ LÝ TÙY CHỌN DUNG LƯỢNG
        // ======================================================================
        if (isset($variantData['storage']) && is_array($variantData['storage'])) {
            foreach ($variantData['storage'] as $storageData) {
                $storage = mysqli_real_escape_string($conn, trim($storageData['storage']));
                $ram = isset($storageData['ram']) ? mysqli_real_escape_string($conn, trim($storageData['ram'])) : '';
                $price = floatval($storageData['price']);
                $installmentPrice = isset($storageData['installment_price']) ? floatval($storageData['installment_price']) : 0;
                $installmentMonths = isset($storageData['installment_months']) ? intval($storageData['installment_months']) : 12;
                $interestRate = isset($storageData['interest_rate']) ? floatval($storageData['interest_rate']) : 0;
                $stock = isset($storageData['stock']) ? intval($storageData['stock']) : 0;
                $promoText = isset($storageData['promo_text']) ? mysqli_real_escape_string($conn, trim($storageData['promo_text'])) : '';
                
                $sqlStorage = "INSERT INTO variant_storage_options 
                               (variant_id, storage, ram, price, installment_price, installment_months, 
                                interest_rate, stock_quantity, promotion_text) 
                               VALUES ($variantId, '$storage', " . 
                               ($ram ? "'$ram'" : "NULL") . ", $price, $installmentPrice, $installmentMonths, 
                               $interestRate, $stock, " . 
                               ($promoText ? "'$promoText'" : "NULL") . ")";
                
                if (!mysqli_query($conn, $sqlStorage)) {
                    throw new Exception('Lỗi insert storage option: ' . mysqli_error($conn));
                }
                
                $storageOptionId = mysqli_insert_id($conn);
                $storageCount++;
                
                // ==============================================================
                // XỬ LÝ MÀU SẮC CHO STORAGE OPTION NÀY
                // ==============================================================
                if (isset($storageData['colors']) && is_array($storageData['colors'])) {
                    foreach ($storageData['colors'] as $colorData) {
                        $colorName = mysqli_real_escape_string($conn, trim($colorData['color_name']));
                        $colorCode = mysqli_real_escape_string($conn, trim($colorData['color_code']));
                        $colorStock = intval($colorData['color_stock']);
                        
                        $sqlColor = "INSERT INTO variant_colors 
                                     (storage_option_id, color_name, color_code, stock_quantity) 
                                     VALUES ($storageOptionId, '$colorName', '$colorCode', $colorStock)";
                        
                        if (!mysqli_query($conn, $sqlColor)) {
                            throw new Exception('Lỗi insert color: ' . mysqli_error($conn));
                        }
                    }
                }
            }
        }
        
        // ======================================================================
        // BƯỚC 5: XỬ LÝ CHƯƠNG TRÌNH KHUYẾN MÃI
        // ======================================================================
        if (isset($variantData['promos']) && is_array($variantData['promos'])) {
            foreach ($variantData['promos'] as $promoData) {
                $promoType = mysqli_real_escape_string($conn, trim($promoData['type']));
                $description = mysqli_real_escape_string($conn, trim($promoData['description']));
                $value = isset($promoData['value']) ? floatval($promoData['value']) : null;
                
                $sqlPromo = "INSERT INTO variant_promotions (variant_id, promotion_type, description, value) 
                             VALUES ($variantId, '$promoType', '$description', " . 
                             ($value ? $value : "NULL") . ")";
                
                if (!mysqli_query($conn, $sqlPromo)) {
                    throw new Exception('Lỗi insert promotion: ' . mysqli_error($conn));
                }
                $promoCount++;
            }
        }
        
        // ======================================================================
        // BƯỚC 6: XỬ LÝ 3AM CARE PLANS
        // ======================================================================
        $careplanCount = 0;
        if (isset($variantData['careplans']) && is_array($variantData['careplans'])) {
            foreach ($variantData['careplans'] as $careplanData) {
                $careplanId = intval($careplanData['careplan_id']);
                
                if ($careplanId > 0) {
                    $sortOrder = $careplanCount;
                    
                    $sqlCareplan = "INSERT INTO variant_careplans (variant_id, careplan_id, sort_order) 
                                    VALUES ($variantId, $careplanId, $sortOrder)";
                    
                    if (!mysqli_query($conn, $sqlCareplan)) {
                        throw new Exception('Lỗi insert careplan: ' . mysqli_error($conn));
                    }
                    $careplanCount++;
                }
            }
        }
    }
    
    // Commit transaction
    mysqli_commit($conn);
    
    // Trả về kết quả thành công
    echo json_encode([
        'success' => true,
        'message' => 'Thêm sản phẩm thành công!',
        'data' => [
            'series_id' => $seriesId,
            'series_name' => $seriesName,
            'slug' => $slug,
            'variants_count' => $variantCount,
            'storage_options_count' => $storageCount,
            'promotions_count' => $promoCount,
            'careplans_count' => $carePlanCount,
            'offers_count' => $offerCount
        ]
    ]);
    
} catch (Exception $e) {
    // Rollback nếu có lỗi
    mysqli_rollback($conn);
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Đóng kết nối
$db->dis_connect();

// =============================================================================
// CÁC HÀM HỖ TRỢ
// =============================================================================

/**
 * Upload ảnh và trả về đường dẫn
 */
function uploadImage($file, $folder = 'products') {
    $targetDir = "../img/$folder/";
    
    // Tạo thư mục nếu chưa tồn tại
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    // Kiểm tra file hợp lệ
    $imageFileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array($imageFileType, $allowedTypes)) {
        return false;
    }
    
    // Kiểm tra kích thước (max 5MB)
    if ($file['size'] > 5000000) {
        return false;
    }
    
    // Đổi tên file để tránh trùng
    $newFileName = uniqid() . '_' . time() . '.' . $imageFileType;
    $targetFile = $targetDir . $newFileName;
    
    // Di chuyển file
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        return "img/$folder/" . $newFileName;
    }
    
    return false;
}

/**
 * Upload ảnh từ temp name
 */
function uploadImageFromTemp($tmpName, $originalName, $fileSize) {
    $targetDir = "../img/products/";
    
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $imageFileType = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array($imageFileType, $allowedTypes)) {
        return false;
    }
    
    if ($fileSize > 5000000) {
        return false;
    }
    
    $newFileName = uniqid() . '_' . time() . '.' . $imageFileType;
    $targetFile = $targetDir . $newFileName;
    
    if (move_uploaded_file($tmpName, $targetFile)) {
        return "img/products/" . $newFileName;
    }
    
    return false;
}

/**
 * Tạo slug từ tên (bỏ dấu, chuyển thành chữ thường, thay khoảng trắng = gạch ngang)
 */
function createSlug($str) {
    // Chuyển về chữ thường
    $str = mb_strtolower($str, 'UTF-8');
    
    // Bỏ dấu tiếng Việt
    $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
    $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
    $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
    $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
    $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
    $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
    $str = preg_replace('/(đ)/', 'd', $str);
    
    // Xóa ký tự đặc biệt, chỉ giữ chữ, số, gạch ngang
    $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
    
    // Thay khoảng trắng bằng gạch ngang
    $str = preg_replace('/[\s-]+/', '-', $str);
    
    // Xóa gạch ngang ở đầu và cuối
    $str = trim($str, '-');
    
    return $str;
}

