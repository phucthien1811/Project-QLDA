<?php
/**
 * File xử lý lấy dữ liệu sản phẩm đa phiên bản
 */

header('Content-Type: application/json; charset=utf-8');

// Include file kết nối database
require_once('../Backend/Database/DbDriver.php');

$db = new DB_driver();
$db->connect();
$conn = $db->__conn;

if (!isset($_POST['request'])) {
    die(json_encode(['success' => false, 'message' => 'Invalid request']));
}

$request = $_POST['request'];

// =============================================================================
// LẤY TẤT CẢ SERIES
// =============================================================================
if ($request === 'getallseries') {
    $sql = "SELECT * FROM product_series ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);
    
    $series = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $series[] = $row;
    }
    
    die(json_encode(['success' => true, 'data' => $series]));
}

// =============================================================================
// LẤY CHI TIẾT SERIES VÀ CÁC VARIANTS
// =============================================================================
if ($request === 'getseriesdetail') {
    $seriesId = isset($_POST['series_id']) ? intval($_POST['series_id']) : 0;
    
    if ($seriesId <= 0) {
        die(json_encode(['success' => false, 'message' => 'Invalid series ID']));
    }
    
    // Lấy thông tin series
    $sql = "SELECT * FROM product_series WHERE id = $seriesId";
    $result = mysqli_query($conn, $sql);
    $series = mysqli_fetch_assoc($result);
    
    if (!$series) {
        die(json_encode(['success' => false, 'message' => 'Series not found']));
    }
    
    // Lấy các variants của series
    $sql = "SELECT * FROM product_variants WHERE series_id = $seriesId";
    $result = mysqli_query($conn, $sql);
    
    $variants = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $variantId = $row['id'];
        
        // Decode JSON
        $row['specs'] = json_decode($row['specs'], true);
        $row['images'] = json_decode($row['images'], true);
        
        // Lấy storage options
        $sqlStorage = "SELECT * FROM variant_storage_options WHERE variant_id = $variantId ORDER BY price ASC";
        $resultStorage = mysqli_query($conn, $sqlStorage);
        $storageOptions = [];
        while ($storage = mysqli_fetch_assoc($resultStorage)) {
            $storageId = $storage['id'];
            
            // Lấy colors cho storage option này
            $sqlColors = "SELECT * FROM variant_colors WHERE storage_option_id = $storageId";
            $resultColors = mysqli_query($conn, $sqlColors);
            $colors = [];
            while ($color = mysqli_fetch_assoc($resultColors)) {
                $colors[] = $color;
            }
            $storage['colors'] = $colors;
            
            $storageOptions[] = $storage;
        }
        $row['storage_options'] = $storageOptions;
        
        // Lấy promotions
        $sqlPromo = "SELECT * FROM variant_promotions WHERE variant_id = $variantId";
        $resultPromo = mysqli_query($conn, $sqlPromo);
        $promotions = [];
        while ($promo = mysqli_fetch_assoc($resultPromo)) {
            $promotions[] = $promo;
        }
        $row['promotions'] = $promotions;
        
        $variants[] = $row;
    }
    
    $series['variants'] = $variants;
    
    die(json_encode(['success' => true, 'data' => $series]));
}

// =============================================================================
// LẤY SERIES THEO SLUG
// =============================================================================
if ($request === 'getseriesbyslug') {
    $slug = isset($_POST['slug']) ? mysqli_real_escape_string($conn, $_POST['slug']) : '';
    
    if (empty($slug)) {
        die(json_encode(['success' => false, 'message' => 'Invalid slug']));
    }
    
    // Lấy thông tin series
    $sql = "SELECT * FROM product_series WHERE slug = '$slug'";
    $result = mysqli_query($conn, $sql);
    $series = mysqli_fetch_assoc($result);
    
    if (!$series) {
        die(json_encode(['success' => false, 'message' => 'Series not found']));
    }
    
    $seriesId = $series['id'];
    
    // Lấy các variants của series
    $sql = "SELECT * FROM product_variants WHERE series_id = $seriesId";
    $result = mysqli_query($conn, $sql);
    
    $variants = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $variantId = $row['id'];
        
        // Decode JSON
        $row['specs'] = json_decode($row['specs'], true);
        $row['images'] = json_decode($row['images'], true);
        
        // Lấy storage options
        $sqlStorage = "SELECT * FROM variant_storage_options WHERE variant_id = $variantId ORDER BY price ASC";
        $resultStorage = mysqli_query($conn, $sqlStorage);
        $storageOptions = [];
        while ($storage = mysqli_fetch_assoc($resultStorage)) {
            $storageId = $storage['id'];
            
            // Lấy colors cho storage option này
            $sqlColors = "SELECT * FROM variant_colors WHERE storage_option_id = $storageId";
            $resultColors = mysqli_query($conn, $sqlColors);
            $colors = [];
            while ($color = mysqli_fetch_assoc($resultColors)) {
                $colors[] = $color;
            }
            $storage['colors'] = $colors;
            
            $storageOptions[] = $storage;
        }
        $row['storage_options'] = $storageOptions;
        
        // Lấy promotions
        $sqlPromo = "SELECT * FROM variant_promotions WHERE variant_id = $variantId";
        $resultPromo = mysqli_query($conn, $sqlPromo);
        $promotions = [];
        while ($promo = mysqli_fetch_assoc($resultPromo)) {
            $promotions[] = $promo;
        }
        $row['promotions'] = $promotions;
        
        $variants[] = $row;
    }
    
    $series['variants'] = $variants;
    
    die(json_encode(['success' => true, 'data' => $series]));
}

// =============================================================================
// TÌM KIẾM SERIES
// =============================================================================
if ($request === 'searchseries') {
    $keyword = isset($_POST['keyword']) ? mysqli_real_escape_string($conn, $_POST['keyword']) : '';
    
    $sql = "SELECT * FROM product_series WHERE series_name LIKE '%$keyword%' OR description LIKE '%$keyword%' ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);
    
    $series = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Đếm số variants
        $seriesId = $row['id'];
        $sqlCount = "SELECT COUNT(*) as count FROM product_variants WHERE series_id = $seriesId";
        $resultCount = mysqli_query($conn, $sqlCount);
        $count = mysqli_fetch_assoc($resultCount);
        $row['variant_count'] = $count['count'];
        
        $series[] = $row;
    }
    
    die(json_encode(['success' => true, 'data' => $series]));
}

// =============================================================================
// LẤY THÔNG TIN CHO GIỎ HÀNG - CART ITEMS
// =============================================================================
if ($request === 'getcartitems') {
    $items = isset($_POST['items']) ? json_decode($_POST['items'], true) : [];
    
    if (empty($items)) {
        die(json_encode(['success' => true, 'data' => []]));
    }
    
    $cartData = [];
    
    foreach ($items as $item) {
        $seriesId = intval($item['seriesId']);
        $variantId = intval($item['variantId']);
        $storageId = intval($item['storageId']);
        $colorId = intval($item['colorId']);
        
        // Lấy thông tin series
        $sql = "SELECT series_name, main_image, slug FROM product_series WHERE id = $seriesId";
        $result = mysqli_query($conn, $sql);
        $series = mysqli_fetch_assoc($result);
        
        if (!$series) continue;
        
        // Lấy thông tin variant
        $sql = "SELECT variant_name, base_price FROM product_variants WHERE id = $variantId AND series_id = $seriesId";
        $result = mysqli_query($conn, $sql);
        $variant = mysqli_fetch_assoc($result);
        
        if (!$variant) continue;
        
        // Lấy thông tin storage
        $sql = "SELECT storage, ram, price FROM variant_storage_options WHERE id = $storageId AND variant_id = $variantId";
        $result = mysqli_query($conn, $sql);
        $storage = mysqli_fetch_assoc($result);
        
        if (!$storage) continue;
        
        // Lấy thông tin color
        $sql = "SELECT color_name, stock_quantity FROM variant_colors WHERE id = $colorId AND storage_option_id = $storageId";
        $result = mysqli_query($conn, $sql);
        $color = mysqli_fetch_assoc($result);
        
        if (!$color) continue;
        
        // Tính giá - cộng base_price của variant với price (additional) của storage
        $basePrice = floatval($variant['base_price']);
        $additionalPrice = floatval($storage['price']);
        $totalPrice = $basePrice + $additionalPrice;
        
        // Tên cấu hình
        $configName = $variant['variant_name'] . ' ' . $storage['storage'] . '/' . $storage['ram'];
        
        $cartData[] = [
            'productId' => $item['productId'],
            'MaSP' => $item['productId'],
            'TenSP' => $series['series_name'],
            'seriesName' => $series['series_name'],
            'HinhAnh' => $series['main_image'],
            'image' => $series['main_image'],
            'slug' => $series['slug'],
            'price' => $totalPrice,
            'DonGia' => $totalPrice,
            'configName' => $configName,
            'colorName' => $color['color_name'],
            'stockQuantity' => $color['stock_quantity'],
            'isSeries' => true,
            'seriesConfig' => [
                'seriesId' => $seriesId,
                'variantId' => $variantId,
                'storageId' => $storageId,
                'colorId' => $colorId,
                'price' => $totalPrice,
                'name' => $configName
            ]
        ];
    }
    
    die(json_encode(['success' => true, 'data' => $cartData]));
}

// =============================================================================
// LẤY SERIES CAREPLANS
// =============================================================================
if ($request === 'getseriescareplans') {
    $seriesId = isset($_POST['series_id']) ? intval($_POST['series_id']) : 0;
    
    if ($seriesId <= 0) {
        die(json_encode(['success' => false, 'message' => 'Invalid series ID']));
    }
    
    $sql = "SELECT sc.*, cp.name as plan_name, cp.price as plan_price, cp.final_price, cp.plan_type 
            FROM series_careplans sc 
            JOIN careplans cp ON sc.careplan_id = cp.id 
            WHERE sc.series_id = $seriesId 
            ORDER BY sc.sort_order ASC";
    $result = mysqli_query($conn, $sql);
    
    $careplans = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $careplans[] = $row;
    }
    
    die(json_encode(['success' => true, 'data' => $careplans]));
}

$db->dis_connect();

