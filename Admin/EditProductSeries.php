<?php
// Admin authentication check - restrict access to admin users only
require_once 'includes/AuthCheck.php';

// Get series ID from URL
$seriesId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($seriesId <= 0) {
    header('Location: ProductList.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh Sửa Sản Phẩm - Admin Dashboard</title>
    <link rel="shortcut icon" href="/Project-PHP/img/favicon.ico?v=2">
    <link rel="shortcut icon" type="image/x-icon" href="/Project-PHP/img/favicon.ico?v=2">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Admin Dashboard CSS -->
    <link rel="stylesheet" href="../Styles/Admin/AdminDashboard.css">
    <!-- Product Series Form CSS -->
    <link rel="stylesheet" href="../Styles/Admin/ProductSeriesForm.css">
         
</head>
<body>
    <!-- ISOLATED ADMIN DASHBOARD APP -->
    <div id="admin-dashboard-app">
        
        <!-- SIDEBAR -->
        <?php include 'includes/Sidebar.php'; ?>
        
        <!-- MAIN CONTENT -->
        <div class="adm-main-content">
            
            <!-- HEADER -->
            <div class="adm-header">
                <h1 class="adm-header-title">
                    <i class="fas fa-edit me-2"></i>
                    Chỉnh Sửa Sản Phẩm
                </h1>
                <div class="adm-user-menu">
                    <a href="ProductList.php" class="btn btn-outline-secondary me-3">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                    </a>
                </div>
            </div>
            
            <!-- CONTENT -->
            <div class="adm-content">
                
                <!-- Loading indicator -->
                <div id="loadingIndicator" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-3">Đang tải dữ liệu sản phẩm...</p>
                </div>

                <form id="productForm" method="POST" enctype="multipart/form-data" style="display:none;">
                    <input type="hidden" name="series_id" id="series_id" value="<?php echo $seriesId; ?>">
                    <input type="hidden" name="action" value="update">
                    
                    <!-- PHẦN 1: THÔNG TIN SERIES -->
                    <div class="mb-4">
                        <div class="section-header">
                            <h3 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Thông Tin Hiển Thị
                            </h3>
                        </div>
                        
                        <div class="adm-card">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-tag me-1"></i>
                                        Tên Sản Phẩm <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="series_name" id="series_name" 
                                           placeholder="VD: iPhone 16 Pro Max" required>
                                    <div class="form-text">Tên sản phẩm sẽ hiển thị trên trang chủ</div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-dollar-sign me-1"></i>
                                        Giá Hiển Thị <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" name="display_price" id="display_price" 
                                           placeholder="VD: 27000000" required>
                                    <div class="form-text">Giá hiển thị ở trang chủ (VNĐ)</div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-image me-1"></i>
                                        Ảnh Đại Diện
                                    </label>
                                    <input type="file" class="form-control" name="series_image" id="series_image" accept="image/*">
                                    <div class="form-text">Để trống nếu không muốn thay đổi ảnh</div>
                                    <div id="series_image_preview" class="mt-2"></div>
                                </div>
                                
                                <div class="col-12 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-microchip me-1"></i>
                                        Thông Số Kỹ Thuật (JSON)
                                    </label>
                                    <textarea class="form-control" name="specs_json" id="specs_json" rows="10"></textarea>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Nhập thông số dạng JSON. Key là tên thông số, Value là giá trị.
                                        <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="formatSpecsJson()">
                                            <i class="fas fa-check me-1"></i>Kiểm tra JSON
                                        </button>
                                    </div>
                                    <div id="specs_json_preview" class="mt-2"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PHẦN 1.5: 3AM CARE+ CHO SERIES -->
                    <div class="mb-4">
                        <div class="section-header d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">
                                <i class="fas fa-shield-alt me-2"></i>
                                3AM Care+ 
                            </h3>
                            <div class="btn-group">
                                <button type="button" class="btn" id="addAllCarePlansBtn">
                                    <i class="fas fa-layer-group me-2"></i>
                                    Thêm Tất Cả
                                </button>
                                <button type="button" class="btn" id="addSeriesCarePlanBtn">
                                    <i class="fas fa-plus-circle me-2"></i>
                                    Thêm Gói Care+
                                </button>
                            </div>
                        </div>
                        
                        <div class="adm-card">
                            <p class="text-muted mb-3">
                                <i class="fas fa-info-circle me-1"></i>
                                Chọn các gói bảo hành 3AM Care+ áp dụng cho toàn bộ sản phẩm
                            </p>
                            <div id="seriesCarePlansContainer">
                                <!-- Care plans will be loaded here -->
                            </div>
                        </div>
                    </div>

                    <!-- PHẦN 1.6: ƯU ĐÃI ĐỘC QUYỀN CHO SERIES -->
                    <div class="mb-4">
                        <div class="section-header d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">
                                <i class="fas fa-gift me-2"></i>
                                Ưu Đãi Độc Quyền
                            </h3>
                            <div class="btn-group">
                                <button type="button" class="btn" id="addDefaultOffersBtn">
                                    <i class="fas fa-magic me-2"></i>
                                    Thêm Mẫu Mặc Định
                                </button>
                                <button type="button" class="btn" id="addSeriesOfferBtn">
                                    <i class="fas fa-plus-circle me-2"></i>
                                    Thêm Ưu Đãi
                                </button>
                            </div>
                        </div>
                        
                        <div class="adm-card">
                            <p class="text-muted mb-3">
                                <i class="fas fa-info-circle me-1"></i>
                                Thêm các ưu đãi độc quyền hiển thị trên trang chi tiết sản phẩm
                            </p>
                            <div id="seriesOffersContainer">
                                <!-- Offers will be loaded here -->
                            </div>
                        </div>
                    </div>

                    <!-- PHẦN 2: CÁC PHIÊN BẢN -->
                    <div class="mb-4">
                        <div class="section-header d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">
                                <i class="fas fa-layer-group me-2"></i>
                                Các Phiên Bản của Sản Phẩm
                            </h3>
                            <button type="button" class="btn btn-light" id="addVariantBtn">
                                <i class="fas fa-plus-circle me-2"></i>
                                Thêm Phiên Bản Mới
                            </button>
                        </div>
                        
                        <div id="variantsContainer">
                            <!-- Variant blocks will be loaded here -->
                        </div>
                    </div>

                    <!-- PHẦN 3: NÚT SUBMIT -->
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-save me-2"></i>
                            Cập Nhật Sản Phẩm
                        </button>
                        <a href="ProductList.php" class="btn btn-secondary btn-lg px-5 ms-3">
                            <i class="fas fa-times me-2"></i>
                            Hủy Bỏ
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- TEMPLATES HTML (ẨN) -->
    <!-- Template Variant -->
    <template id="variantTemplate">
        <div class="variant-block" data-variant-index="0">
            <div class="variant-header">
                <h4 class="variant-title">Phiên Bản <span class="variant-number">1</span></h4>
                <button type="button" class="btn btn-danger btn-sm delete-variant-btn">
                    <i class="fas fa-trash"></i> Xóa Phiên Bản
                </button>
            </div>
            <div class="variant-body">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Tên Phiên bản *</label>
                        <input type="text" class="form-control" name="variants[0][name]" required
                               placeholder="VD: iPhone 16 Pro Max 256GB">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Màn Hình</label>
                        <input type="text" class="form-control" name="variants[0][screen_size]"
                               placeholder='6.9"'>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Hình ảnh mô tả</label>
                        <input type="file" class="form-control variant-images" name="variants[0][images][]" 
                               accept="image/*" multiple>
                        <small class="text-muted">Có thể chọn nhiều hình ảnh</small>
                        <div class="variant-images-preview mt-2" style="display:flex;gap:8px;flex-wrap:wrap;"></div>
                    </div>
                </div>
                
                <!-- Storage Options Container -->
                <div class="storage-options-section mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Tùy Chọn Dung Lượng</h5>
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-secondary btn-sm import-storage-json-btn" title="Import từ JSON">
                                <i class="fas fa-file-import"></i> Import JSON
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm add-storage-btn">
                                <i class="fas fa-plus"></i> Thêm Tùy Chọn
                            </button>
                        </div>
                    </div>
                    <div class="storage-options-container">
                        <!-- Storage options will be added here -->
                    </div>
                </div>
                
                <!-- Promotions Container -->
                <div class="promotions-section mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Chương Trình Khuyến Mãi</h5>
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-secondary btn-sm add-default-promos-btn">
                                <i class="fas fa-magic"></i> Thêm Mẫu
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm add-promo-btn">
                                <i class="fas fa-gift"></i> Thêm Khuyến Mãi
                            </button>
                        </div>
                    </div>
                    <div class="promotions-container">
                        <!-- Promotions will be added here -->
                    </div>
                </div>
            </div>
        </div>
    </template>
    
    <!-- Template Storage Option -->
    <template id="storageTemplate">
        <div class="storage-option-row">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Dung Lượng *</label>
                    <input type="text" class="form-control" 
                           name="variants[0][storage][0][storage]" required
                           placeholder="256GB">
                </div>
                <div class="col-md-2">
                    <label class="form-label">RAM</label>
                    <input type="text" class="form-control" 
                           name="variants[0][storage][0][ram]"
                           placeholder="8GB">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Giá Bán (₫) *</label>
                    <input type="number" class="form-control storage-price" 
                           name="variants[0][storage][0][price]" required
                           placeholder="27000000">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Trả Góp/tháng</label>
                    <input type="number" class="form-control" 
                           name="variants[0][storage][0][installment_price]"
                           placeholder="3000000">
                </div>
                <div class="col-md-1">
                    <label class="form-label">Kỳ hạn</label>
                    <input type="number" class="form-control" 
                           name="variants[0][storage][0][installment_months]"
                           value="12">
                </div>
                <div class="col-md-1">
                    <label class="form-label">Lãi suất %</label>
                    <input type="number" class="form-control" 
                           name="variants[0][storage][0][interest_rate]"
                           step="0.01" value="0">
                </div>
                <div class="col-md-1">
                    <label class="form-label">KM</label>
                    <button type="button" class="btn btn-primary btn-sm w-100 add-color-btn" title="Thêm Màu Sắc">
                        <i class="fas fa-palette"></i>
                    </button>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm w-100 delete-storage-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            
            <!-- Colors Container -->
            <div class="colors-container" style="margin-top: 15px; padding-left: 20px; border-left: 3px solid #0d6efd;">
                <!-- Color options will be added here -->
            </div>
        </div>
    </template>
    
    <!-- Template Color Option -->
    <template id="colorTemplate">
        <div class="color-option-row">
            <div class="row g-2 align-items-center" style="margin-top: 8px; padding: 10px; background: #f8f9fa; border-radius: 8px;">
                <div class="col-md-4">
                    <label class="form-label mb-1" style="font-size: 13px;">Tên Màu *</label>
                    <input type="text" class="form-control form-control-sm" 
                           name="variants[0][storage][0][colors][0][color_name]" required
                           placeholder="VD: Titan Tự Nhiên">
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1" style="font-size: 13px;">Mã Màu *</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text color-preview-box" style="width:38px;background:#000000;border:1px solid #ccc;"></span>
                        <input type="text" class="form-control form-control-sm color-code-input" 
                               name="variants[0][storage][0][colors][0][color_code]" 
                               value="#000000" placeholder="#000000" maxlength="7"
                               pattern="^#[0-9A-Fa-f]{6}$" title="Nhập mã màu hex, VD: #FF5733">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1" style="font-size: 13px;">Tồn Kho *</label>
                    <input type="number" class="form-control form-control-sm" 
                           name="variants[0][storage][0][colors][0][color_stock]" required
                           min="0" value="0" placeholder="0">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm delete-color-btn" style="margin-top: 20px;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </template>
    
    <!-- Template Promotion -->
    <template id="promoTemplate">
        <div class="promo-row">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Loại KM *</label>
                    <select class="form-select" name="variants[0][promos][0][type]" required>
                        <option value="GiamGia">Giảm Giá</option>
                        <option value="TraGop">Trả Góp 0%</option>
                        <option value="TangPhuKien">Tặng Phụ Kiện</option>
                        <option value="BaoHanh">Bảo Hành Mở Rộng</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Mô Tả *</label>
                    <input type="text" class="form-control" 
                           name="variants[0][promos][0][description]" required
                           placeholder="VD: Giảm 10% qua VNPay">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Giá Trị (nếu có)</label>
                    <input type="number" class="form-control" 
                           name="variants[0][promos][0][value]"
                           placeholder="1000000">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm w-100 delete-promo-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </template>
    
    <!-- Template CarePlan Option cho Series -->
    <template id="seriesCareplanTemplate">
        <div class="careplan-row" style="background: #e8f4fd; padding: 15px; border-radius: 8px; margin-bottom: 10px; border-left: 4px solid #0d6efd;">
            <div class="row g-3 align-items-center">
                <div class="col-md-8">
                    <label class="form-label">
                        <i class="fas fa-shield-alt me-1"></i>
                        Chọn Gói 3AM Care+ *
                    </label>
                    <select class="form-select series-careplan-select" name="series_careplans[0][careplan_id]" required>
                        <option value="">-- Chọn gói Care+ --</option>
                        <!-- Options will be loaded dynamically -->
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Giá (tự động)</label>
                    <input type="text" class="form-control series-careplan-price-display" readonly 
                           style="background: #f8f9fa; font-weight: 600; color: #e74c3c;">
                </div>
                <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-danger btn-sm delete-series-careplan-btn" style="margin-top: 24px;">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </template>
    
    <!-- Template Offer cho Series -->
    <template id="seriesOfferTemplate">
        <div class="offer-row" style="background: #fff8e1; padding: 15px; border-radius: 8px; margin-bottom: 10px; border-left: 4px solid #ffc107;">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="fas fa-tag me-1"></i>
                        Badge (nhãn) *
                    </label>
                    <input type="text" class="form-control" name="series_offers[0][badge]" 
                           placeholder="VD: Ưu đãi độc quyền" required>
                </div>
                <div class="col-md-9">
                    <label class="form-label">
                        <i class="fas fa-heading me-1"></i>
                        Tiêu đề *
                    </label>
                    <input type="text" class="form-control" name="series_offers[0][title]" 
                           placeholder="VD: Tặng vé concert Anh Trai Say Hi" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label">
                        <i class="fas fa-align-left me-1"></i>
                        Mô tả (không bắt buộc)
                    </label>
                    <input type="text" class="form-control" name="series_offers[0][description]" 
                           placeholder="VD: Từ 24/11 đến 10/12, mua sản phẩm nhận ngay vé concert">
                </div>
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="fas fa-link me-1"></i>
                        Text link (nếu có)
                    </label>
                    <input type="text" class="form-control" name="series_offers[0][link_text]" 
                           placeholder="VD: Tìm hiểu ngay">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm w-100 delete-series-offer-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </template>
    
    <!-- Product Series Form JS -->
    <script src="../Scripts/ProductSeriesForm.js"></script>
    
    <!-- Edit Mode Script -->
    <script>
        var isEditMode = true;
        var editSeriesId = <?php echo $seriesId; ?>;
        
        $(document).ready(function() {
            // Override form submit for edit mode - MUST BE FIRST before loading data
            $('#productForm').off('submit').on('submit', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Đang cập nhật...',
                    html: 'Vui lòng đợi trong giây lát',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                const formData = new FormData(this);
                
                $.ajax({
                    type: 'POST',
                    url: '../Api/ProcessProductSeriesUpdate.php',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: 'Sản phẩm đã được cập nhật',
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                window.location.href = 'ProductList.php';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi!',
                                text: response.message || 'Có lỗi xảy ra khi cập nhật sản phẩm',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);
                        console.log('Response:', xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi kết nối!',
                            text: 'Không thể kết nối đến server. Vui lòng thử lại.',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            });
            
            // Load product data for editing
            loadProductData(editSeriesId);
        });
        
        function loadProductData(seriesId) {
            $.ajax({
                url: '../Api/ProcessProductSeriesGet.php',
                method: 'POST',
                data: {
                    request: 'getseriesdetail',
                    series_id: seriesId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        populateForm(response.data);
                        $('#loadingIndicator').hide();
                        $('#productForm').show();
                    } else {
                        Swal.fire('Lỗi', response.message || 'Không thể tải dữ liệu sản phẩm', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Lỗi', 'Không thể kết nối server', 'error');
                }
            });
        }
        
        function populateForm(data) {
            // Fill series info
            $('#series_name').val(data.series_name);
            $('#display_price').val(data.display_price);
            
            // Show current image
            if (data.main_image) {
                $('#series_image_preview').html(`
                    <div class="mb-2">
                        <small class="text-muted">Ảnh hiện tại:</small><br>
                        <img src="../${data.main_image}" style="max-width:100px;max-height:100px;border-radius:4px;border:1px solid #ddd;">
                    </div>
                `);
            }
            
            // Fill specs JSON
            if (data.specs_json) {
                try {
                    const specs = typeof data.specs_json === 'string' ? JSON.parse(data.specs_json) : data.specs_json;
                    $('#specs_json').val(JSON.stringify(specs, null, 2));
                } catch(e) {
                    $('#specs_json').val(data.specs_json);
                }
            }
            
            // Clear default variant and load existing variants
            $('#variantsContainer').empty();
            
            if (data.variants && data.variants.length > 0) {
                data.variants.forEach(function(variant, vIndex) {
                    addVariantWithData(variant, vIndex);
                });
                // Đồng bộ variantIndex để thêm variant mới hoạt động đúng
                variantIndex = data.variants.length;
            }
            
            // Load care plans
            loadExistingCarePlans(data.id);
            
            // Load offers
            loadExistingOffers(data.id);
        }
        
        function addVariantWithData(variant, vIndex) {
            // Clone template
            const template = $('#variantTemplate').prop('content');
            const $variantBlock = $(template).clone();
            
            // Update index
            $variantBlock.find('.variant-block').attr('data-variant-index', vIndex);
            $variantBlock.find('.variant-number').text(vIndex + 1);
            
            // Update name attributes
            $variantBlock.find('[name]').each(function() {
                let name = $(this).attr('name');
                name = name.replace('[0]', `[${vIndex}]`);
                $(this).attr('name', name);
            });
            
            // Fill variant data
            $variantBlock.find('input[name*="[name]"]').val(variant.variant_name || '');
            $variantBlock.find('input[name*="[screen_size]"]').val(variant.screen_size || '');
            
            // Show existing images and add hidden field to preserve them
            if (variant.images && variant.images.length > 0) {
                let imagesHtml = '<div class="mt-2"><small class="text-muted">Ảnh hiện tại:</small><div style="display:flex;gap:5px;flex-wrap:wrap;margin-top:5px;">';
                variant.images.forEach(function(img, i) {
                    imagesHtml += `<img src="../${img}" style="width:50px;height:50px;object-fit:cover;border-radius:4px;border:1px solid #ddd;">`;
                });
                imagesHtml += '</div></div>';
                $variantBlock.find('.variant-images-preview').html(imagesHtml);
                
                // Add hidden field to preserve existing images
                $variantBlock.find('.variant-body').prepend(`<input type="hidden" name="variants[${vIndex}][existing_images]" value='${JSON.stringify(variant.images)}'>`);
            }
            
            // Add hidden field for variant ID (for update)
            $variantBlock.find('.variant-body').prepend(`<input type="hidden" name="variants[${vIndex}][id]" value="${variant.id}">`);
            
            $('#variantsContainer').append($variantBlock);
            
            // Add storage options
            const $storageContainer = $variantBlock.find('.storage-options-container');
            
            if (variant.storage_options && variant.storage_options.length > 0) {
                variant.storage_options.forEach(function(storage, sIndex) {
                    addStorageWithData(vIndex, sIndex, storage, $storageContainer);
                });
            }
            
            // Add promotions
            const $promoContainer = $variantBlock.find('.promotions-container');
            if (variant.promotions && variant.promotions.length > 0) {
                variant.promotions.forEach(function(promo, pIndex) {
                    addPromoWithData(vIndex, pIndex, promo, $promoContainer);
                });
            }
        }
        
        function addStorageWithData(vIndex, sIndex, storage, $container) {
            const template = $('#storageTemplate').prop('content');
            if (!template) {
                return;
            }
            const $storageRow = $(template).clone();
            
            // Update name attributes
            $storageRow.find('[name]').each(function() {
                let name = $(this).attr('name');
                name = name.replace('[0][storage][0]', `[${vIndex}][storage][${sIndex}]`);
                $(this).attr('name', name);
            });
            
            // Fill data
            $storageRow.find('input[name*="[storage]"]:not([name*="[colors]"])').first().val(storage.storage || '');
            $storageRow.find('input[name*="[ram]"]').val(storage.ram || '');
            $storageRow.find('input[name*="[price]"]').val(storage.price || 0);
            $storageRow.find('input[name*="[installment_price]"]').val(storage.installment_price || '');
            $storageRow.find('input[name*="[installment_months]"]').val(storage.installment_months || 12);
            $storageRow.find('input[name*="[interest_rate]"]').val(storage.interest_rate || 0);
            
            // Add hidden field for storage ID
            $storageRow.find('.storage-option-row').prepend(`<input type="hidden" name="variants[${vIndex}][storage][${sIndex}][id]" value="${storage.id}">`);
            
            $container.append($storageRow);
            
            // Add colors
            const $colorsContainer = $container.find('.storage-option-row').last().find('.colors-container');
            if (storage.colors && storage.colors.length > 0) {
                storage.colors.forEach(function(color, cIndex) {
                    addColorWithData(vIndex, sIndex, cIndex, color, $colorsContainer);
                });
            }
        }
        
        function addColorWithData(vIndex, sIndex, cIndex, color, $container) {
            const template = $('#colorTemplate').prop('content');
            const $colorRow = $(template).clone();
            
            // Update name attributes
            $colorRow.find('[name]').each(function() {
                let name = $(this).attr('name');
                name = name.replace('[0][storage][0][colors][0]', `[${vIndex}][storage][${sIndex}][colors][${cIndex}]`);
                $(this).attr('name', name);
            });
            
            // Fill data
            const colorCode = color.color_code || '#000000';
            $colorRow.find('input[name*="[color_name]"]').val(color.color_name || '');
            $colorRow.find('input[name*="[color_code]"]').val(colorCode);
            $colorRow.find('input[name*="[color_stock]"]').val(color.stock_quantity || 0);
            $colorRow.find('.color-preview-box').css('background', colorCode);
            
            // Add hidden field for color ID
            $colorRow.find('.color-option-row').prepend(`<input type="hidden" name="variants[${vIndex}][storage][${sIndex}][colors][${cIndex}][id]" value="${color.id}">`);
            
            $container.append($colorRow);
        }
        
        function addPromoWithData(vIndex, pIndex, promo, $container) {
            const template = $('#promoTemplate').prop('content');
            const $promoRow = $(template).clone();
            
            // Update name attributes
            $promoRow.find('[name]').each(function() {
                let name = $(this).attr('name');
                name = name.replace('[0][promos][0]', `[${vIndex}][promos][${pIndex}]`);
                $(this).attr('name', name);
            });
            
            // Fill data
            $promoRow.find('select[name*="[type]"]').val(promo.promo_type || 'GiamGia');
            $promoRow.find('input[name*="[description]"]').val(promo.description || '');
            $promoRow.find('input[name*="[value]"]').val(promo.value || '');
            
            $container.append($promoRow);
        }
        
        function loadExistingCarePlans(seriesId) {
            $.ajax({
                url: '../Api/ProcessProductSeriesGet.php',
                method: 'POST',
                data: {
                    request: 'getseriescareplans',
                    series_id: seriesId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data) {
                        response.data.forEach(function(cp, index) {
                            addSeriesCarePlanWithData(cp, index);
                        });
                    }
                }
            });
        }
        
        function addSeriesCarePlanWithData(careplan, index) {
            const template = document.getElementById('seriesCareplanTemplate');
            const $careplanRow = $(template.content.cloneNode(true));
            
            // Update name attributes
            $careplanRow.find('[name]').each(function() {
                let name = $(this).attr('name');
                name = name.replace('[0]', `[${index}]`);
                $(this).attr('name', name);
            });
            
            // Populate select and set value
            const $select = $careplanRow.find('.series-careplan-select');
            $select.empty();
            $select.append('<option value="">-- Chọn gói Care+ --</option>');
            
            if (typeof carePlansData !== 'undefined' && carePlansData.length > 0) {
                carePlansData.forEach(function(plan) {
                    const priceText = formatCurrency(plan.final_price);
                    const typeLabel = plan.plan_type === 'protection' ? '🛡️' : '📅';
                    const selected = plan.id == careplan.careplan_id ? 'selected' : '';
                    $select.append(`<option value="${plan.id}" data-price="${plan.final_price}" ${selected}>${typeLabel} ${plan.name} - ${priceText}</option>`);
                });
            }
            
            // Set price display
            $careplanRow.find('.series-careplan-price-display').val(formatCurrency(careplan.plan_price || 0));
            
            $('#seriesCarePlansContainer').append($careplanRow);
        }
        
        function loadExistingOffers(seriesId) {
            $.ajax({
                url: '../Api/ProcessOffers.php?action=get_by_series&series_id=' + seriesId,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data) {
                        response.data.forEach(function(offer, index) {
                            addSeriesOfferWithData(offer, index);
                        });
                    }
                }
            });
        }
        
        function addSeriesOfferWithData(offer, index) {
            const template = document.getElementById('seriesOfferTemplate');
            const $offerRow = $(template.content.cloneNode(true));
            
            // Update name attributes
            $offerRow.find('[name]').each(function() {
                let name = $(this).attr('name');
                name = name.replace('[0]', `[${index}]`);
                $(this).attr('name', name);
            });
            
            // Fill data
            $offerRow.find('input[name*="[badge]"]').val(offer.badge || '');
            $offerRow.find('input[name*="[title]"]').val(offer.title || '');
            $offerRow.find('input[name*="[description]"]').val(offer.description || '');
            $offerRow.find('input[name*="[link_text]"]').val(offer.link_text || '');
            
            $('#seriesOffersContainer').append($offerRow);
        }
        
        function formatSpecsJson() {
            const textarea = document.getElementById('specs_json');
            const preview = document.getElementById('specs_json_preview');
            
            try {
                const specs = JSON.parse(textarea.value);
                textarea.value = JSON.stringify(specs, null, 2);
                
                let html = '<div class="alert alert-success mb-2"><i class="fas fa-check-circle me-1"></i> JSON hợp lệ!</div>';
                html += '<table class="table table-sm table-bordered">';
                html += '<thead class="table-light"><tr><th>Thông số</th><th>Giá trị</th></tr></thead><tbody>';
                
                for (const [key, value] of Object.entries(specs)) {
                    html += `<tr><td><strong>${key}</strong></td><td>${value}</td></tr>`;
                }
                
                html += '</tbody></table>';
                preview.innerHTML = html;
                
            } catch (e) {
                preview.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-1"></i> JSON không hợp lệ: ${e.message}</div>`;
            }
        }
    </script>
    
    <!-- Modal Import JSON Storage -->
    <div class="modal fade" id="importStorageJsonModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background:#f8f9fa;border-bottom:2px solid #dee2e6;">
                    <h5 class="modal-title" style="font-weight:700;color:#2c3e50;font-size:15px;"><i class="fas fa-file-import me-2" style="color:#5a6c7d;"></i>Import Tùy Chọn Dung Lượng từ JSON</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i>
                        <strong>Cấu trúc JSON mẫu:</strong>
                        <button type="button" class="btn btn-sm btn-outline-primary float-end" onclick="copySampleStorageJson()">
                            <i class="fas fa-copy"></i> Copy mẫu
                        </button>
                    </div>
                    <pre id="sampleStorageJson" style="background: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 12px; max-height: 200px; overflow-y: auto;">[
  {
    "storage": "256GB",
    "ram": "8GB",
    "price": 27000000,
    "installment_price": 3000000,
    "installment_months": 12,
    "interest_rate": 0,
    "colors": [
      {"name": "Titan Tự Nhiên", "code": "#C4B8A6", "stock": 10},
      {"name": "Titan Đen", "code": "#1D1D1F", "stock": 15}
    ]
  },
  {
    "storage": "512GB",
    "ram": "8GB",
    "price": 32000000,
    "installment_price": 3500000,
    "colors": [
      {"name": "Titan Tự Nhiên", "code": "#C4B8A6", "stock": 8}
    ]
  }
]</pre>
                    <div class="mb-3">
                        <label class="form-label"><strong>Paste JSON của bạn:</strong></label>
                        <textarea class="form-control" id="storageJsonInput" rows="10" placeholder="Paste JSON vào đây..."></textarea>
                    </div>
                    <div id="storageJsonPreview"></div>
                </div>
                <div class="modal-footer" style="background:#f8f9fa;border-top:1px solid #dee2e6;">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="previewStorageJson()">
                        <i class="fas fa-eye"></i> Xem trước
                    </button>
                    <button type="button" class="btn btn-dark" onclick="applyStorageJson()">
                        <i class="fas fa-check"></i> Áp dụng
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        var currentImportVariantIndex = null;
        
        function copySampleStorageJson() {
            const sample = document.getElementById('sampleStorageJson').textContent;
            navigator.clipboard.writeText(sample).then(() => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Đã copy JSON mẫu!',
                    showConfirmButton: false,
                    timer: 1500
                });
            });
        }
        
        function previewStorageJson() {
            const jsonText = document.getElementById('storageJsonInput').value.trim();
            const preview = document.getElementById('storageJsonPreview');
            
            if (!jsonText) {
                preview.innerHTML = '<div class="alert alert-warning">Vui lòng nhập JSON!</div>';
                return;
            }
            
            try {
                const data = JSON.parse(jsonText);
                
                if (!Array.isArray(data)) {
                    throw new Error('JSON phải là một mảng []');
                }
                
                let html = '<div class="alert alert-success"><i class="fas fa-check-circle me-1"></i> JSON hợp lệ! Tìm thấy ' + data.length + ' tùy chọn dung lượng.</div>';
                html += '<div class="table-responsive"><table class="table table-sm table-bordered">';
                html += '<thead class="table-light"><tr><th>Dung lượng</th><th>RAM</th><th>Giá</th><th>Màu sắc</th></tr></thead><tbody>';
                
                data.forEach((item, i) => {
                    const colors = item.colors ? item.colors.map(c => `<span style="display:inline-block;width:16px;height:16px;background:${c.code};border-radius:50%;border:1px solid #ccc;margin-right:3px;" title="${c.name}"></span>`).join('') : '-';
                    html += `<tr><td>${item.storage || '-'}</td><td>${item.ram || '-'}</td><td>${(item.price || 0).toLocaleString()}₫</td><td>${colors}</td></tr>`;
                });
                
                html += '</tbody></table></div>';
                preview.innerHTML = html;
                
            } catch (e) {
                preview.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-1"></i> Lỗi: ${e.message}</div>`;
            }
        }
        
        function applyStorageJson() {
            const jsonText = document.getElementById('storageJsonInput').value.trim();
            
            if (!jsonText) {
                Swal.fire('Lỗi', 'Vui lòng nhập JSON!', 'error');
                return;
            }
            
            try {
                const data = JSON.parse(jsonText);
                
                if (!Array.isArray(data)) {
                    throw new Error('JSON phải là một mảng []');
                }
                
                // Import storage options
                importStorageOptionsFromJson(currentImportVariantIndex, data);
                
                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('importStorageJsonModal')).hide();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Đã import thành công!',
                    text: `Đã thêm ${data.length} tùy chọn dung lượng`,
                    timer: 2000,
                    showConfirmButton: false
                });
                
            } catch (e) {
                Swal.fire('Lỗi JSON', e.message, 'error');
            }
        }
    </script>
</body>
</html>




