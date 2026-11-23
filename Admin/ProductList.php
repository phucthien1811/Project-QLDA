<?php
// Admin authentication check - restrict access to admin users only
require_once 'includes/AuthCheck.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List - Admin Dashboard</title>
    <link rel="shortcut icon" href="/Project-PHP/img/favicon.ico?v=2">
    <link rel="shortcut icon" type="image/x-icon" href="/Project-PHP/img/favicon.ico?v=2">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Admin Dashboard CSS -->
    <link rel="stylesheet" href="../Styles/Admin/AdminDashboard.css">
    
    <style>
        #admin-dashboard-app .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        #admin-dashboard-app .action-btn {
            padding: 6px 12px;
            font-size: 14px;
            margin: 0 3px;
        }
        
        #admin-dashboard-app .series-name {
            font-weight: 600;
            color: #000;
        }
        
        #admin-dashboard-app .variant-count {
            display: inline-block;
            background: #e9ecef;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin-left: 8px;
        }
        
        #admin-dashboard-app .price-display {
            font-weight: 600;
            color: #dc3545;
        }
        
        #admin-dashboard-app .search-box {
            max-width: 400px;
            margin-bottom: 20px;
        }
        
        #admin-dashboard-app .loading-spinner {
            text-align: center;
            padding: 40px;
        }
        
        #admin-dashboard-app .no-products {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        
        #admin-dashboard-app .no-products i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.3;
        }
        
        /* Stock Modal Styles */
        .stock-modal .modal-dialog {
            max-width: 900px;
        }
        
        .stock-variant-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #1428a0;
        }
        
        .stock-variant-title {
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 10px;
            color: #1428a0;
        }
        
        .stock-table {
            width: 100%;
            font-size: 14px;
        }
        
        .stock-table th {
            background: #e9ecef;
            padding: 8px 12px;
            font-weight: 600;
        }
        
        .stock-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .stock-low {
            color: #dc3545;
            font-weight: 600;
        }
        
        .stock-ok {
            color: #28a745;
            font-weight: 600;
        }
        
        .stock-out {
            color: #dc3545;
            background: #fff5f5;
        }
        
        .color-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .color-dot {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 1px solid #ddd;
            display: inline-block;
        }
        
        .stock-edit-btn {
            padding: 2px 8px;
            font-size: 12px;
        }
    </style>
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
                    <i class="fas fa-box-open me-2"></i>
                    Product List
                </h1>
                <div class="adm-user-menu">
                    <button class="adm-notification-btn">
                        <i class="fa fa-bell"></i>
                    </button>
                    <button class="adm-user-btn">
                        <i class="fa fa-user-circle"></i>
                    </button>
                </div>
            </div>
            
            <!-- CONTENT -->
            <div class="adm-content">
                
                <!-- TOOLBAR -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="search-box">
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchInput" placeholder="Tìm kiếm sản phẩm...">
                            <button class="btn btn-primary" type="button">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <a href="AddProductSeries.php" class="btn btn-primary">
                        <i class="fa fa-plus-circle me-2"></i>
                        Thêm Sản Phẩm Mới
                    </a>
                </div>
                
                <!-- LOADING -->
                <div id="loadingSpinner" class="loading-spinner">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Đang tải danh sách sản phẩm...</p>
                </div>
                
                <!-- NO PRODUCTS -->
                <div id="noProducts" class="no-products" style="display: none;">
                    <i class="fa fa-box-open"></i>
                    <h4>Chưa có sản phẩm nào</h4>
                    <p>Hãy thêm sản phẩm series đầu tiên của bạn</p>
                    <a href="AddProductSeries.php" class="btn btn-primary mt-3">
                        <i class="fa fa-plus-circle me-2"></i>
                        Thêm Sản Phẩm
                    </a>
                </div>
                
                <!-- PRODUCTS TABLE -->
                <div id="productsTable" class="adm-card" style="display: none;">
                    <div class="table-responsive">
                        <table class="adm-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Hình Ảnh</th>
                                    <th>Tên Sản Phẩm</th>
                                    <th>Slug</th>
                                    <th>Giá Hiển Thị</th>
                                    <th>Số Phiên Bản</th>
                                    <th>Ngày Tạo</th>
                                    <th>Hành Động</th>
                                </tr>
                            </thead>
                            <tbody id="productTableBody">
                                <!-- Products will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
    <!-- STOCK DETAIL MODAL -->
    <div class="modal fade stock-modal" id="stockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-warehouse me-2"></i>
                        Chi tiết tồn kho: <span id="stockModalTitle"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="stockModalBody">
                    <div class="text-center py-4">
                        <div class="spinner-border" role="status"></div>
                        <p class="mt-2">Đang tải...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <a href="#" id="viewProductLink" target="_blank" class="btn btn-primary">
                        <i class="fa fa-external-link-alt me-1"></i> Xem trang sản phẩm
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Product List Script -->
    <script>
        $(document).ready(function() {
            loadProducts();
            
            // Search functionality
            $('#searchInput').on('keyup', function() {
                const searchTerm = $(this).val().toLowerCase();
                $('#productTableBody tr').each(function() {
                    const text = $(this).text().toLowerCase();
                    $(this).toggle(text.indexOf(searchTerm) > -1);
                });
            });
        });
        
        function loadProducts() {
            $.ajax({
                url: '../Api/ProcessProductSeriesGet.php',
                method: 'POST',
                data: { request: 'getallseries' },
                dataType: 'json',
                success: function(response) {
                    $('#loadingSpinner').hide();
                    
                    if (response.success && response.data.length > 0) {
                        displayProducts(response.data);
                        $('#productsTable').show();
                    } else {
                        $('#noProducts').show();
                    }
                },
                error: function(xhr, status, error) {
                    $('#loadingSpinner').hide();
                    console.error('Error loading products:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Không thể tải danh sách sản phẩm'
                    });
                }
            });
        }
        
        function displayProducts(products) {
            const tbody = $('#productTableBody');
            tbody.empty();
            
            products.forEach(product => {
                // Get variant count via AJAX
                getVariantCount(product.id, function(variantCount) {
                    const imageUrl = product.main_image ? `../img/products/${product.main_image}` : 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="60" height="60"%3E%3Crect fill="%23ddd" width="60" height="60"/%3E%3Ctext fill="%23999" x="50%25" y="50%25" dominant-baseline="middle" text-anchor="middle" font-family="sans-serif" font-size="14"%3ENo Image%3C/text%3E%3C/svg%3E';
                    const displayPrice = product.display_price ? formatPrice(product.display_price) : 'Chưa đặt';
                    const createdDate = formatDate(product.created_at);
                    
                    const row = `
                        <tr data-id="${product.id}">
                            <td>${product.id}</td>
                            <td>
                                <img src="${imageUrl}" alt="${product.series_name}" class="product-image" 
                                     onerror="handleImageError(this)">
                            </td>
                            <td>
                                <div class="series-name">${product.series_name}</div>
                                ${product.description ? '<small class="text-muted">' + truncateText(product.description, 50) + '</small>' : ''}
                            </td>
                            <td><code>${product.slug}</code></td>
                            <td><span class="price-display">${displayPrice}</span></td>
                            <td><span class="variant-count">${variantCount} phiên bản</span></td>
                            <td>${createdDate}</td>
                            <td>
                                <button class="btn btn-sm btn-primary action-btn" onclick="viewProduct(${product.id})" title="Xem chi tiết">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning action-btn" onclick="editProduct(${product.id})" title="Sửa">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger action-btn" onclick="deleteProduct(${product.id}, '${product.series_name}')" title="Xóa">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            });
        }
        
        function getVariantCount(seriesId, callback) {
            $.ajax({
                url: '../Api/ProcessProductSeriesGet.php',
                method: 'POST',
                data: { 
                    request: 'getseriesdetail',
                    series_id: seriesId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data.variants) {
                        callback(response.data.variants.length);
                    } else {
                        callback(0);
                    }
                },
                error: function() {
                    callback(0);
                }
            });
        }
        
        function viewProduct(seriesId) {
            // Hiện modal và load thông tin tồn kho
            const modal = new bootstrap.Modal(document.getElementById('stockModal'));
            modal.show();
            
            // Reset modal content
            $('#stockModalTitle').text('...');
            $('#stockModalBody').html('<div class="text-center py-4"><div class="spinner-border" role="status"></div><p class="mt-2">Đang tải...</p></div>');
            
            // Load stock data
            $.ajax({
                url: '../Api/ProcessProductSeriesGet.php',
                method: 'POST',
                data: { 
                    request: 'getseriesdetail',
                    series_id: seriesId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data) {
                        renderStockModal(response.data);
                    } else {
                        $('#stockModalBody').html('<div class="alert alert-danger">Không thể tải thông tin sản phẩm</div>');
                    }
                },
                error: function() {
                    $('#stockModalBody').html('<div class="alert alert-danger">Lỗi kết nối server</div>');
                }
            });
        }
        
        function renderStockModal(seriesData) {
            $('#stockModalTitle').text(seriesData.series_name);
            $('#viewProductLink').attr('href', `../ProductDetail.php?slug=${seriesData.slug}`);
            
            let html = '';
            let totalStock = 0;
            
            if (seriesData.variants && seriesData.variants.length > 0) {
                seriesData.variants.forEach(variant => {
                    html += `
                        <div class="stock-variant-card">
                            <div class="stock-variant-title">
                                <i class="fa fa-mobile-alt me-2"></i>${variant.variant_name}
                            </div>
                    `;
                    
                    if (variant.storage_options && variant.storage_options.length > 0) {
                        html += `
                            <table class="stock-table">
                                <thead>
                                    <tr>
                                        <th>Dung lượng</th>
                                        <th>RAM</th>
                                        <th>Màu sắc</th>
                                        <th>Tồn kho</th>
                                        <th>Giá</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;
                        
                        variant.storage_options.forEach(storage => {
                            if (storage.colors && storage.colors.length > 0) {
                                storage.colors.forEach((color, colorIndex) => {
                                    const stockClass = color.stock_quantity <= 0 ? 'stock-out' : (color.stock_quantity <= 5 ? 'stock-low' : 'stock-ok');
                                    totalStock += parseInt(color.stock_quantity) || 0;
                                    
                                    html += `
                                        <tr class="${color.stock_quantity <= 0 ? 'stock-out' : ''}">
                                            ${colorIndex === 0 ? `<td rowspan="${storage.colors.length}">${storage.storage}</td>` : ''}
                                            ${colorIndex === 0 ? `<td rowspan="${storage.colors.length}">${storage.ram || '-'}</td>` : ''}
                                            <td>
                                                <span class="color-badge">
                                                    <span class="color-dot" style="background-color: ${color.color_code}"></span>
                                                    ${color.color_name}
                                                </span>
                                            </td>
                                            <td class="${stockClass}">
                                                ${color.stock_quantity} 
                                                ${color.stock_quantity <= 0 ? '<i class="fa fa-exclamation-triangle ms-1"></i>' : ''}
                                            </td>
                                            ${colorIndex === 0 ? `<td rowspan="${storage.colors.length}">${formatPrice(storage.price)}</td>` : ''}
                                        </tr>
                                    `;
                                });
                            } else {
                                // Không có màu, hiển thị stock của storage
                                const stockClass = storage.stock_quantity <= 0 ? 'stock-out' : (storage.stock_quantity <= 5 ? 'stock-low' : 'stock-ok');
                                totalStock += parseInt(storage.stock_quantity) || 0;
                                
                                html += `
                                    <tr class="${storage.stock_quantity <= 0 ? 'stock-out' : ''}">
                                        <td>${storage.storage}</td>
                                        <td>${storage.ram || '-'}</td>
                                        <td><em>Mặc định</em></td>
                                        <td class="${stockClass}">
                                            ${storage.stock_quantity || 0}
                                            ${storage.stock_quantity <= 0 ? '<i class="fa fa-exclamation-triangle ms-1"></i>' : ''}
                                        </td>
                                        <td>${formatPrice(storage.price)}</td>
                                    </tr>
                                `;
                            }
                        });
                        
                        html += '</tbody></table>';
                    } else {
                        html += '<p class="text-muted mb-0"><i class="fa fa-info-circle me-1"></i>Chưa có tùy chọn dung lượng</p>';
                    }
                    
                    html += '</div>';
                });
                
                // Tổng kết
                html = `
                    <div class="alert alert-info mb-3">
                        <i class="fa fa-info-circle me-2"></i>
                        <strong>Tổng tồn kho:</strong> ${totalStock} sản phẩm
                    </div>
                ` + html;
                
            } else {
                html = '<div class="alert alert-warning">Chưa có phiên bản nào</div>';
            }
            
            $('#stockModalBody').html(html);
        }
        
        function editProduct(seriesId) {
            window.location.href = 'EditProductSeries.php?id=' + seriesId;
        }
        
        function deleteProduct(seriesId, seriesName) {
            Swal.fire({
                title: 'Xác nhận xóa?',
                html: `Bạn có chắc muốn xóa sản phẩm <strong>${seriesName}</strong>?<br>Hành động này không thể hoàn tác!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('Deleting series ID:', seriesId); // Debug
                    $.ajax({
                        url: '../Api/ProcessProductSeriesDelete.php',
                        method: 'POST',
                        data: { series_id: seriesId },
                        dataType: 'json',
                        success: function(response) {
                            console.log('Delete response:', response); // Debug
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Đã xóa!',
                                    text: 'Sản phẩm đã được xóa thành công',
                                    timer: 1500
                                }).then(() => {
                                    $(`tr[data-id="${seriesId}"]`).fadeOut(300, function() {
                                        $(this).remove();
                                        if ($('#productTableBody tr').length === 0) {
                                            $('#productsTable').hide();
                                            $('#noProducts').show();
                                        }
                                    });
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Lỗi',
                                    text: response.message || 'Không thể xóa sản phẩm'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', xhr, status, error); // Debug
                            console.error('Response Text:', xhr.responseText); // Debug
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: 'Không thể kết nối đến server'
                            });
                        }
                    });
                }
            });
        }
        
        function formatPrice(price) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(price);
        }
        
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        
        function truncateText(text, maxLength) {
            if (text.length <= maxLength) return text;
            return text.substring(0, maxLength) + '...';
        }
        
        function handleImageError(img) {
            // Prevent infinite loop by checking if already tried placeholder
            if (!img.dataset.errorHandled) {
                img.dataset.errorHandled = 'true';
                // Use inline SVG placeholder
                img.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="60" height="60"%3E%3Crect fill="%23e9ecef" width="60" height="60"/%3E%3Ctext fill="%23999" x="50%25" y="50%25" dominant-baseline="middle" text-anchor="middle" font-family="sans-serif" font-size="12"%3ENo Image%3C/text%3E%3C/svg%3E';
            }
        }
        
        // Dropdown toggle functionality
        document.querySelectorAll('#admin-dashboard-app .adm-nav-dropdown').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                this.classList.toggle('open');
                const submenu = this.nextElementSibling;
                if (submenu && submenu.classList.contains('adm-nav-submenu')) {
                    submenu.classList.toggle('show');
                }
            });
        });
        
        // Set active menu item
        const currentPage = window.location.pathname.split('/').pop();
        document.querySelectorAll('#admin-dashboard-app .adm-nav-item').forEach(item => {
            if (item.getAttribute('href') === currentPage) {
                item.classList.add('active');
                const parentDropdown = item.closest('.adm-nav-submenu');
                if (parentDropdown) {
                    parentDropdown.classList.add('show');
                    const dropdownToggle = parentDropdown.previousElementSibling;
                    if (dropdownToggle) {
                        dropdownToggle.classList.add('open');
                    }
                }
            }
        });
    </script>
</body>
</html>




