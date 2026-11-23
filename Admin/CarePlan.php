<?php
// Admin authentication check - restrict access to admin users only
require_once 'includes/AuthCheck.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý 3AM Care+ - Admin Dashboard</title>
    <link rel="shortcut icon" href="/Project-PHP/img/favicon.ico?v=2">
    <link rel="shortcut icon" type="image/x-icon" href="/Project-PHP/img/favicon.ico?v=2">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Admin Dashboard CSS -->
    <link rel="stylesheet" href="../Styles/Admin/AdminDashboard.css">
    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        .care-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .care-badge.active {
            background: #d4edda;
            color: #155724;
        }
        .care-badge.inactive {
            background: #f8d7da;
            color: #721c24;
        }
        .care-badge.protection {
            background: #cce5ff;
            color: #004085;
        }
        .care-badge.warranty {
            background: #fff3cd;
            color: #856404;
        }
        .price-display {
            font-weight: 600;
            color: #e74c3c;
        }
        .original-price {
            text-decoration: line-through;
            color: #999;
            font-size: 13px;
        }
        .discount-badge {
            background: #e74c3c;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            margin-left: 5px;
        }
        .action-btn {
            padding: 6px 12px;
            border-radius: 4px;
            border: 1px solid transparent;
            cursor: pointer;
            margin: 0 3px;
            transition: all 0.2s;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .action-btn:hover {
            opacity: 0.9;
        }
        .btn-edit {
            background: white;
            color: #3498db;
            border-color: #3498db;
        }
        .btn-edit:hover {
            background: #3498db;
            color: white;
        }
        .btn-delete {
            background: white;
            color: #e74c3c;
            border-color: #e74c3c;
        }
        .btn-delete:hover {
            background: #e74c3c;
            color: white;
        }
        .btn-toggle {
            background: white;
            color: #2ecc71;
            border-color: #2ecc71;
        }
        .btn-toggle:hover {
            background: #2ecc71;
            color: white;
        }
        .btn-toggle.off {
            color: #95a5a6;
            border-color: #95a5a6;
        }
        .btn-toggle.off:hover {
            background: #95a5a6;
            color: white;
        }
        .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .table-header {
            background: #f8f9fa;
            color: #333;
            padding: 20px;
            border-bottom: 1px solid #dee2e6;
        }
        .adm-table {
            width: 100%;
            border-collapse: collapse;
        }
        .adm-table th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        .adm-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        .adm-table td:last-child {
            white-space: nowrap;
        }
        .adm-table tr:hover {
            background: #f8f9fa;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        .empty-state i {
            font-size: 60px;
            color: #ddd;
            margin-bottom: 20px;
        }
        .modal-header {
            background: #f8f9fa;
            color: #333;
            border-bottom: 1px solid #dee2e6;
        }
        .modal-header .btn-close {
            opacity: 1;
        }
        .form-label {
            font-weight: 600;
            color: #333;
        }
        .form-label i {
            margin-right: 5px;
            color: #667eea;
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
                    <i class="fas fa-shield-alt me-2"></i>
                    Quản lý 3AM Care+
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
                
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="adm-stat-card">
                            <div class="adm-stat-header">
                                <span class="adm-stat-title">Tổng gói</span>
                                <div class="adm-stat-icon" style="background: #667eea; color: white;">
                                    <i class="fa fa-shield-alt"></i>
                                </div>
                            </div>
                            <div class="adm-stat-value" id="totalPlans">0</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="adm-stat-card">
                            <div class="adm-stat-header">
                                <span class="adm-stat-title">Đang hoạt động</span>
                                <div class="adm-stat-icon" style="background: #2ecc71; color: white;">
                                    <i class="fa fa-check-circle"></i>
                                </div>
                            </div>
                            <div class="adm-stat-value" id="activePlans">0</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="adm-stat-card">
                            <div class="adm-stat-header">
                                <span class="adm-stat-title">Gói bảo vệ</span>
                                <div class="adm-stat-icon" style="background: #3498db; color: white;">
                                    <i class="fa fa-umbrella"></i>
                                </div>
                            </div>
                            <div class="adm-stat-value" id="protectionPlans">0</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="adm-stat-card">
                            <div class="adm-stat-header">
                                <span class="adm-stat-title">Gói gia hạn</span>
                                <div class="adm-stat-icon" style="background: #e74c3c; color: white;">
                                    <i class="fa fa-calendar-plus"></i>
                                </div>
                            </div>
                            <div class="adm-stat-value" id="warrantyPlans">0</div>
                        </div>
                    </div>
                </div>
                
                <!-- Table Section -->
                <div class="table-container">
                    <div class="table-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0"><i class="fas fa-list me-2"></i>Danh sách gói 3AM Care+</h4>
                            <small>Quản lý các gói bảo hành và bảo vệ thiết bị</small>
                        </div>
                        <button class="btn btn-light" onclick="openAddModal()">
                            <i class="fas fa-plus me-2"></i>Thêm gói mới
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="adm-table">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="25%">Tên gói</th>
                                    <th width="10%">Loại</th>
                                    <th width="10%">Thời hạn</th>
                                    <th width="15%">Giá gốc</th>
                                    <th width="15%">Giá sau giảm</th>
                                    <th width="8%">Trạng thái</th>
                                    <th width="12%">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="carePlanTableBody">
                                <tr>
                                    <td colspan="8" class="empty-state">
                                        <i class="fas fa-spinner fa-spin"></i>
                                        <p>Đang tải dữ liệu...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
    <!-- Modal Thêm/Sửa -->
    <div class="modal fade" id="carePlanModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fas fa-plus-circle me-2"></i>Thêm gói 3AM Care+ mới
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="carePlanForm">
                        <input type="hidden" id="planId" name="id">
                        
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-tag"></i>Tên gói <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="planName" name="name" 
                                       placeholder="VD: 3AM Care+ (Rơi vỡ vào nước) 1 năm" required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-clock"></i>Thời hạn
                                </label>
                                <select class="form-select" id="planDuration" name="duration">
                                    <option value="6 tháng">6 tháng</option>
                                    <option value="1 năm" selected>1 năm</option>
                                    <option value="2 năm">2 năm</option>
                                    <option value="3 năm">3 năm</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-align-left"></i>Mô tả
                            </label>
                            <textarea class="form-control" id="planDescription" name="description" rows="3"
                                      placeholder="Nhập mô tả chi tiết về gói bảo hành..."></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-layer-group"></i>Loại gói
                                </label>
                                <select class="form-select" id="planType" name="plan_type">
                                    <option value="protection">Bảo vệ (Rơi vỡ, vào nước)</option>
                                    <option value="warranty">Gia hạn bảo hành</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-dollar-sign"></i>Giá gốc (VNĐ) <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="originalPrice" name="original_price" 
                                       placeholder="VD: 1099000" required min="0">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-percentage"></i>Giảm giá (%)
                                </label>
                                <input type="number" class="form-control" id="discountPercent" name="discount_percent" 
                                       placeholder="VD: 25" min="0" max="100" value="0">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-tags"></i>Giá sau giảm (VNĐ)
                                </label>
                                <input type="text" class="form-control" id="finalPrice" readonly 
                                       style="background: #e9ecef; font-weight: 600; color: #e74c3c;">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-sort"></i>Thứ tự hiển thị
                                </label>
                                <input type="number" class="form-control" id="sortOrder" name="sort_order" 
                                       placeholder="0" value="0" min="0">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-toggle-on"></i>Trạng thái
                                </label>
                                <select class="form-select" id="isActive" name="is_active">
                                    <option value="1">Đang hoạt động</option>
                                    <option value="0">Vô hiệu hóa</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Hủy
                    </button>
                    <button type="button" class="btn btn-primary" onclick="saveCarePlan()">
                        <i class="fas fa-save me-1"></i>Lưu
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // API URL
        const API_URL = '../Api/ProcessCarePlan.php';
        
        // Modal instance
        let carePlanModal;
        
        // Khởi tạo
        document.addEventListener('DOMContentLoaded', function() {
            carePlanModal = new bootstrap.Modal(document.getElementById('carePlanModal'));
            loadCarePlans();
            
            // Tính giá sau giảm khi thay đổi giá gốc hoặc % giảm
            document.getElementById('originalPrice').addEventListener('input', calculateFinalPrice);
            document.getElementById('discountPercent').addEventListener('input', calculateFinalPrice);
        });
        
        // Tính giá sau giảm
        function calculateFinalPrice() {
            const originalPrice = parseFloat(document.getElementById('originalPrice').value) || 0;
            const discountPercent = parseFloat(document.getElementById('discountPercent').value) || 0;
            const finalPrice = originalPrice * (100 - discountPercent) / 100;
            document.getElementById('finalPrice').value = formatCurrency(finalPrice);
        }
        
        // Format tiền tệ
        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount) + ' ₫';
        }
        
        // Tải danh sách care plans
        async function loadCarePlans() {
            try {
                const response = await fetch(`${API_URL}?action=get_all`);
                const result = await response.json();
                
                if (result.success) {
                    renderCarePlans(result.data);
                    updateStats(result.data);
                } else {
                    showError(result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('carePlanTableBody').innerHTML = `
                    <tr>
                        <td colspan="8" class="empty-state">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p>Lỗi kết nối. Vui lòng kiểm tra database và thử lại.</p>
                            <button class="btn btn-primary mt-2" onclick="loadCarePlans()">
                                <i class="fas fa-redo me-1"></i>Thử lại
                            </button>
                        </td>
                    </tr>
                `;
            }
        }
        
        // Render danh sách care plans
        function renderCarePlans(plans) {
            const tbody = document.getElementById('carePlanTableBody');
            
            if (!plans || plans.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="empty-state">
                            <i class="fas fa-shield-alt"></i>
                            <p>Chưa có gói 3AM Care+ nào</p>
                            <button class="btn btn-primary mt-2" onclick="openAddModal()">
                                <i class="fas fa-plus me-1"></i>Thêm gói đầu tiên
                            </button>
                        </td>
                    </tr>
                `;
                return;
            }
            
            let html = '';
            plans.forEach((plan, index) => {
                const typeLabel = plan.plan_type === 'protection' ? 
                    '<span class="care-badge protection">Bảo vệ</span>' : 
                    '<span class="care-badge warranty">Gia hạn BH</span>';
                    
                const statusLabel = plan.is_active == 1 ? 
                    '<span class="care-badge active">Hoạt động</span>' : 
                    '<span class="care-badge inactive">Vô hiệu</span>';
                
                const toggleClass = plan.is_active == 1 ? '' : 'off';
                const toggleIcon = plan.is_active == 1 ? 'fa-toggle-on' : 'fa-toggle-off';
                
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>
                            <strong>${plan.name}</strong>
                            ${plan.description ? `<br><small class="text-muted">${plan.description}</small>` : ''}
                        </td>
                        <td>${typeLabel}</td>
                        <td>${plan.duration || '-'}</td>
                        <td>
                            <span class="original-price">${formatCurrency(plan.original_price)}</span>
                            ${plan.discount_percent > 0 ? `<span class="discount-badge">-${plan.discount_percent}%</span>` : ''}
                        </td>
                        <td class="price-display">${formatCurrency(plan.final_price)}</td>
                        <td>${statusLabel}</td>
                        <td>
                            <button class="action-btn btn-edit" onclick="editCarePlan(${plan.id})" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn btn-toggle ${toggleClass}" onclick="toggleStatus(${plan.id})" title="Bật/Tắt">
                                <i class="fas ${toggleIcon}"></i>
                            </button>
                            <button class="action-btn btn-delete" onclick="deleteCarePlan(${plan.id})" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = html;
        }
        
        // Cập nhật thống kê
        function updateStats(plans) {
            document.getElementById('totalPlans').textContent = plans.length;
            document.getElementById('activePlans').textContent = plans.filter(p => p.is_active == 1).length;
            document.getElementById('protectionPlans').textContent = plans.filter(p => p.plan_type === 'protection').length;
            document.getElementById('warrantyPlans').textContent = plans.filter(p => p.plan_type === 'warranty').length;
        }
        
        // Mở modal thêm mới
        function openAddModal() {
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus-circle me-2"></i>Thêm gói 3AM Care+ mới';
            document.getElementById('carePlanForm').reset();
            document.getElementById('planId').value = '';
            document.getElementById('finalPrice').value = '';
            carePlanModal.show();
        }
        
        // Sửa care plan
        async function editCarePlan(id) {
            try {
                const response = await fetch(`${API_URL}?action=get_by_id&id=${id}`);
                const result = await response.json();
                
                if (result.success) {
                    const plan = result.data;
                    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Sửa gói 3AM Care+';
                    document.getElementById('planId').value = plan.id;
                    document.getElementById('planName').value = plan.name;
                    document.getElementById('planDescription').value = plan.description || '';
                    document.getElementById('planDuration').value = plan.duration;
                    document.getElementById('planType').value = plan.plan_type;
                    document.getElementById('originalPrice').value = plan.original_price;
                    document.getElementById('discountPercent').value = plan.discount_percent;
                    document.getElementById('sortOrder').value = plan.sort_order;
                    document.getElementById('isActive').value = plan.is_active;
                    calculateFinalPrice();
                    carePlanModal.show();
                } else {
                    showError(result.message);
                }
            } catch (error) {
                showError('Không thể tải thông tin gói bảo hành');
            }
        }
        
        // Lưu care plan
        async function saveCarePlan() {
            const form = document.getElementById('carePlanForm');
            const formData = new FormData(form);
            
            const planId = document.getElementById('planId').value;
            formData.append('action', planId ? 'update' : 'add');
            
            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.success) {
                    carePlanModal.hide();
                    loadCarePlans();
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công!',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    showError(result.message);
                }
            } catch (error) {
                showError('Không thể lưu gói bảo hành');
            }
        }
        
        // Xóa care plan
        function deleteCarePlan(id) {
            Swal.fire({
                title: 'Xác nhận xóa?',
                text: 'Bạn có chắc chắn muốn xóa gói bảo hành này?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#95a5a6',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch(`${API_URL}?action=delete&id=${id}`);
                        const data = await response.json();
                        
                        if (data.success) {
                            loadCarePlans();
                            Swal.fire({
                                icon: 'success',
                                title: 'Đã xóa!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            showError(data.message);
                        }
                    } catch (error) {
                        showError('Không thể xóa gói bảo hành');
                    }
                }
            });
        }
        
        // Toggle trạng thái
        async function toggleStatus(id) {
            try {
                const response = await fetch(`${API_URL}?action=toggle_status&id=${id}`);
                const result = await response.json();
                
                if (result.success) {
                    loadCarePlans();
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công!',
                        text: result.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    showError(result.message);
                }
            } catch (error) {
                showError('Không thể cập nhật trạng thái');
            }
        }
        
        // Hiển thị lỗi
        function showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi!',
                text: message
            });
        }
    </script>
</body>
</html>




