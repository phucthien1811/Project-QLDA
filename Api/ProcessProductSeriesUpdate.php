<?php
/**
 * File xử lý cập nhật sản phẩm đa phiên bản (Product Series)
 * Nhận dữ liệu từ form edit và update vào database
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

// Lấy series ID cần update
$seriesId = isset($_POST['series_id']) ? intval($_POST['series_id']) : 0;
if ($seriesId <= 0) {
    die(json_encode(['success' => false, 'message' => 'ID sản phẩm không hợp lệ']));
}

// Bắt đầu transaction
mysqli_begin_transaction($conn);

try {
    // ==========================================================================
    // BƯỚC 1: XỬ LÝ UPLOAD ẢNH SERIES (NẾU CÓ)
    // ==========================================================================
    $seriesImagePath = null;
    
    if (isset($_FILES['series_image']) && $_FILES['series_image']['error'] === UPLOAD_ERR_OK) {
        $seriesImagePath = uploadImage($_FILES['series_image'], 'series');
        if ($seriesImagePath === false) {
            throw new Exception('Lỗi upload ảnh series');
        }
    }
    
    // ==========================================================================
    // BƯỚC 2: CẬP NHẬT THÔNG TIN SERIES
    // ==========================================================================
    $seriesName = mysqli_real_escape_string($conn, trim($_POST['series_name']));
    $displayPrice = isset($_POST['display_price']) ? intval($_POST['display_price']) : 0;
    
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
    
    // Build UPDATE query
    $updateParts = [];
    $updateParts[] = "series_name = '$seriesName'";
    $updateParts[] = "display_price = $displayPrice";
    $updateParts[] = "specs_json = " . ($specsJson ? "'$specsJson'" : "NULL");
    
    if ($seriesImagePath) {
        $updateParts[] = "main_image = '$seriesImagePath'";
    }
    
    $sqlUpdateSeries = "UPDATE product_series SET " . implode(", ", $updateParts) . " WHERE id = $seriesId";
    
    if (!mysqli_query($conn, $sqlUpdateSeries)) {
        throw new Exception('Lỗi cập nhật series: ' . mysqli_error($conn));
    }
    
    // ==========================================================================
    // BƯỚC 2.5: CẬP NHẬT SERIES CARE PLANS
    // ==========================================================================
    // Xóa care plans cũ
    mysqli_query($conn, "DELETE FROM series_careplans WHERE series_id = $seriesId");
    
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
    // BƯỚC 2.6: CẬP NHẬT SERIES OFFERS (ƯU ĐÃI ĐỘC QUYỀN)
    // ==========================================================================
    // Xóa offers cũ
    mysqli_query($conn, "DELETE FROM series_offers WHERE series_id = $seriesId");
    
    $offerCount = 0;
    if (isset($_POST['series_offers']) && is_array($_POST['series_offers'])) {
        foreach ($_POST['series_offers'] as $ofIndex => $ofData) {
            $badge = isset($ofData['badge']) ? mysqli_real_escape_string($conn, trim($ofData['badge'])) : '';
            $title = isset($ofData['title']) ? mysqli_real_escape_string($conn, trim($ofData['title'])) : '';
            $description = isset($ofData['description']) ? mysqli_real_escape_string($conn, trim($ofData['description'])) : '';
            $linkText = isset($ofData['link_text']) ? mysqli_real_escape_string($conn, trim($ofData['link_text'])) : '';
            $linkUrl = isset($ofData['link_url']) ? mysqli_real_escape_string($conn, trim($ofData['link_url'])) : '';
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
    
    // Lấy danh sách variant cũ để xóa
    $existingVariants = [];
    $result = mysqli_query($conn, "SELECT id FROM product_variants WHERE series_id = $seriesId");
    while ($row = mysqli_fetch_assoc($result)) {
        $existingVariants[] = $row['id'];
    }
    
    $variantCount = 0;
    $storageCount = 0;
    $promoCount = 0;
    $updatedVariantIds = [];
    
    foreach ($_POST['variants'] as $variantIndex => $variantData) {
        $modelName = isset($variantData['name']) ? mysqli_real_escape_string($conn, trim($variantData['name'])) : '';
        $screenSize = isset($variantData['screen_size']) ? mysqli_real_escape_string($conn, trim($variantData['screen_size'])) : '';
        
        // Xử lý upload ảnh variant (có thể nhiều ảnh)
        $variantImages = [];
        $hasNewImages = false;
        
        if (isset($_FILES['variants']['name'][$variantIndex]['images'])) {
            $imageFiles = $_FILES['variants'];
            
            foreach ($imageFiles['name'][$variantIndex]['images'] as $key => $fileName) {
                if ($imageFiles['error'][$variantIndex]['images'][$key] === UPLOAD_ERR_OK) {
                    $tmpName = $imageFiles['tmp_name'][$variantIndex]['images'][$key];
                    $fileSize = $imageFiles['size'][$variantIndex]['images'][$key];
                    
                    $imagePath = uploadImageFromTemp($tmpName, $fileName, $fileSize);
                    if ($imagePath !== false) {
                        $variantImages[] = $imagePath;
                        $hasNewImages = true;
                    }
                }
            }
        }
        
        // Nếu không có ảnh mới, giữ lại ảnh cũ từ hidden field
        if (!$hasNewImages && isset($variantData['existing_images'])) {
            $existingImages = json_decode($variantData['existing_images'], true);
            if (is_array($existingImages) && !empty($existingImages)) {
                $variantImages = $existingImages;
            }
        }
        
        // Kiểm tra xem variant đã tồn tại hay là mới
        $variantId = isset($variantData['id']) ? intval($variantData['id']) : 0;
        
        if ($variantId > 0 && in_array($variantId, $existingVariants)) {
            // UPDATE variant hiện có
            $updateVariantParts = [];
            $updateVariantParts[] = "variant_name = '$modelName'";
            $updateVariantParts[] = "screen_size = " . ($screenSize ? "'$screenSize'" : "NULL");
            
            // Luôn cập nhật ảnh (có thể là ảnh mới hoặc ảnh cũ được giữ lại)
            if (!empty($variantImages)) {
                $variantImagesJson = mysqli_real_escape_string($conn, json_encode($variantImages));
                $updateVariantParts[] = "images = '$variantImagesJson'";
            }
            
            $sqlUpdateVariant = "UPDATE product_variants SET " . implode(", ", $updateVariantParts) . " WHERE id = $variantId";
            
            if (!mysqli_query($conn, $sqlUpdateVariant)) {
                throw new Exception('Lỗi update variant: ' . mysqli_error($conn));
            }
            
            $updatedVariantIds[] = $variantId;
        } else {
            // INSERT variant mới
            $variantImagesJson = mysqli_real_escape_string($conn, json_encode($variantImages));
            
            $sqlVariant = "INSERT INTO product_variants (series_id, variant_name, screen_size, specs, images) 
                           VALUES ($seriesId, '$modelName', " . 
                           ($screenSize ? "'$screenSize'" : "NULL") . ", '{}', '$variantImagesJson')";
            
            if (!mysqli_query($conn, $sqlVariant)) {
                throw new Exception('Lỗi insert variant: ' . mysqli_error($conn));
            }
            
            $variantId = mysqli_insert_id($conn);
            $updatedVariantIds[] = $variantId;
        }
        
        $variantCount++;
        
        // ======================================================================
        // BƯỚC 4: XỬ LÝ TÙY CHỌN DUNG LƯỢNG
        // ======================================================================
        
        // Lấy storage options cũ
        $existingStorages = [];
        $result = mysqli_query($conn, "SELECT id FROM variant_storage_options WHERE variant_id = $variantId");
        while ($row = mysqli_fetch_assoc($result)) {
            $existingStorages[] = $row['id'];
        }
        
        $updatedStorageIds = [];
        
        if (isset($variantData['storage']) && is_array($variantData['storage'])) {
            foreach ($variantData['storage'] as $storageData) {
                $storage = mysqli_real_escape_string($conn, trim($storageData['storage']));
                $ram = isset($storageData['ram']) ? mysqli_real_escape_string($conn, trim($storageData['ram'])) : '';
                $price = floatval($storageData['price']);
                $installmentPrice = isset($storageData['installment_price']) ? floatval($storageData['installment_price']) : 0;
                $installmentMonths = isset($storageData['installment_months']) ? intval($storageData['installment_months']) : 12;
                $interestRate = isset($storageData['interest_rate']) ? floatval($storageData['interest_rate']) : 0;
                
                $storageId = isset($storageData['id']) ? intval($storageData['id']) : 0;
                
                if ($storageId > 0 && in_array($storageId, $existingStorages)) {
                    // UPDATE storage
                    $sqlUpdateStorage = "UPDATE variant_storage_options SET 
                                        storage = '$storage', 
                                        ram = " . ($ram ? "'$ram'" : "NULL") . ",
                                        price = $price,
                                        installment_price = $installmentPrice,
                                        installment_months = $installmentMonths,
                                        interest_rate = $interestRate
                                        WHERE id = $storageId";
                    
                    if (!mysqli_query($conn, $sqlUpdateStorage)) {
                        throw new Exception('Lỗi update storage option: ' . mysqli_error($conn));
                    }
                    
                    $updatedStorageIds[] = $storageId;
                } else {
                    // INSERT storage mới
                    $sqlStorage = "INSERT INTO variant_storage_options 
                                   (variant_id, storage, ram, price, installment_price, installment_months, interest_rate) 
                                   VALUES ($variantId, '$storage', " . 
                                   ($ram ? "'$ram'" : "NULL") . ", $price, $installmentPrice, $installmentMonths, $interestRate)";
                    
                    if (!mysqli_query($conn, $sqlStorage)) {
                        throw new Exception('Lỗi insert storage option: ' . mysqli_error($conn));
                    }
                    
                    $storageId = mysqli_insert_id($conn);
                    $updatedStorageIds[] = $storageId;
                }
                
                $storageCount++;
                
                // ==============================================================
                // XỬ LÝ MÀU SẮC CHO STORAGE OPTION NÀY
                // ==============================================================
                // Xóa colors cũ của storage này
                mysqli_query($conn, "DELETE FROM variant_colors WHERE storage_option_id = $storageId");
                
                if (isset($storageData['colors']) && is_array($storageData['colors'])) {
                    foreach ($storageData['colors'] as $colorData) {
                        $colorName = mysqli_real_escape_string($conn, trim($colorData['color_name']));
                        $colorCode = mysqli_real_escape_string($conn, trim($colorData['color_code']));
                        $colorStock = intval($colorData['color_stock']);
                        
                        $sqlColor = "INSERT INTO variant_colors 
                                     (storage_option_id, color_name, color_code, stock_quantity) 
                                     VALUES ($storageId, '$colorName', '$colorCode', $colorStock)";
                        
                        if (!mysqli_query($conn, $sqlColor)) {
                            throw new Exception('Lỗi insert color: ' . mysqli_error($conn));
                        }
                    }
                }
            }
        }
        
        // Xóa storage options không còn được sử dụng
        foreach ($existingStorages as $oldStorageId) {
            if (!in_array($oldStorageId, $updatedStorageIds)) {
                mysqli_query($conn, "DELETE FROM variant_colors WHERE storage_option_id = $oldStorageId");
                mysqli_query($conn, "DELETE FROM variant_storage_options WHERE id = $oldStorageId");
            }
        }
        
        // ======================================================================
        // BƯỚC 5: XỬ LÝ CHƯƠNG TRÌNH KHUYẾN MÃI
        // ======================================================================
        // Xóa promotions cũ
        mysqli_query($conn, "DELETE FROM variant_promotions WHERE variant_id = $variantId");
        
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
    }
    
    // Xóa variants không còn được sử dụng
    foreach ($existingVariants as $oldVariantId) {
        if (!in_array($oldVariantId, $updatedVariantIds)) {
            // Xóa tất cả dữ liệu liên quan
            mysqli_query($conn, "DELETE FROM variant_promotions WHERE variant_id = $oldVariantId");
            mysqli_query($conn, "DELETE FROM variant_careplans WHERE variant_id = $oldVariantId");
            
            // Xóa colors và storage
            $result = mysqli_query($conn, "SELECT id FROM variant_storage_options WHERE variant_id = $oldVariantId");
            while ($row = mysqli_fetch_assoc($result)) {
                mysqli_query($conn, "DELETE FROM variant_colors WHERE storage_option_id = " . $row['id']);
            }
            mysqli_query($conn, "DELETE FROM variant_storage_options WHERE variant_id = $oldVariantId");
            
            // Xóa variant
            mysqli_query($conn, "DELETE FROM product_variants WHERE id = $oldVariantId");
        }
    }
    
    // Commit transaction
    mysqli_commit($conn);
    
    // Trả về kết quả thành công
    echo json_encode([
        'success' => true,
        'message' => 'Cập nhật sản phẩm thành công!',
        'data' => [
            'series_id' => $seriesId,
            'series_name' => $seriesName,
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
    
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $imageFileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array($imageFileType, $allowedTypes)) {
        return false;
    }
    
    if ($file['size'] > 5000000) {
        return false;
    }
    
    $newFileName = uniqid() . '_' . time() . '.' . $imageFileType;
    $targetFile = $targetDir . $newFileName;
    
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

