<?php
// Admin authentication check - restrict access to admin users only
require_once 'includes/AuthCheck.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Voucher - Admin Dashboard</title>
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
        .voucher-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .voucher-badge.active {
            background: #d4edda;
            color: #155724;
        }
        .voucher-badge.inactive {
            background: #f8d7da;
            color: #721c24;
        }
        .voucher-badge.expired {
            background: #e2e3e5;
            color: #383d41;
        }
        .voucher-badge.percent {
            background: #cce5ff;
            color: #004085;
        }
        .voucher-badge.fixed {
            background: #fff3cd;
            color: #856404;
        }
        .voucher-code {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            background: #f8f9fa;
            padding: 4px 10px;
            border-radius: 6px;
            border: 1px dashed #dee2e6;
            color: #e74c3c;
            letter-spacing: 1px;
        }
        .price-display {
            font-weight: 600;
            color: #e74c3c;
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
        .btn-copy {
            background: white;
            color: #9b59b6;
            border-color: #9b59b6;
        }
        .btn-copy:hover {
            background: #9b59b6;
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
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #dee2e6;
        }
        .adm-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        .adm-table td:last-child {
            white-space: nowrap;
        }
        .adm-table tr:hover {
            background: #f8f9fa;
        }
        .usage-info {
            font-size: 12px;
            color: #666;
        }
        .usage-bar {
            width: 100px;
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
            margin-top: 4px;
        }
        .usage-bar-fill {
            height: 100%;
            background: #667eea;
            border-radius: 3px;
            transition: width 0.3s ease;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .stat-card .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }
        .stat-card .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #333;
        }
        .stat-card .stat-label {
            color: #888;
            font-size: 14px;
        }
        .stat-card.total .stat-icon { background: #e8f4fd; color: #2196f3; }
        .stat-card.active .stat-icon { background: #e8f5e9; color: #4caf50; }
        .stat-card.used .stat-icon { background: #fff3e0; color: #ff9800; }
        .stat-card.expired .stat-icon { background: #fce4ec; color: #e91e63; }
        
        /* Modal styles */
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
        .input-group-text {
            background: #f8f9fa;
        }
        .date-range {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
    </style>
</head>
<body>
    <div id="admin-dashboard-app">
        
        <!-- SIDEBAR -->
        <?php include 'includes/Sidebar.php'; ?>
        
        <!-- MAIN CONTENT -->
        <div class="adm-main-content">
            
            <!-- HEADER -->
            <div class="adm-header">
                <h1 class="adm-header-title">Quản lý Voucher</h1>
                <div class="adm-user-menu">
                    <button class="btn btn-primary" onclick="openAddModal()">
                        <i class="fa fa-plus me-2"></i>Thêm Voucher
                    </button>
                </div>
            </div>
            
            <!-- CONTENT -->
            <div class="adm-content">
                
                <!-- STATS -->
                <div class="stats-grid">
                    <div class="stat-card total">
                        <div class="stat-icon"><i class="fa fa-ticket-alt"></i></div>
                        <div class="stat-value" id="statTotal">0</div>
                        <div class="stat-label">Tổng voucher</div>
                    </div>
                    <div class="stat-card active">
                        <div class="stat-icon"><i class="fa fa-check-circle"></i></div>
                        <div class="stat-value" id="statActive">0</div>
                        <div class="stat-label">Đang hoạt động</div>
                    </div>
                    <div class="stat-card used">
                        <div class="stat-icon"><i class="fa fa-shopping-cart"></i></div>
                        <div class="stat-value" id="statUsed">0</div>
                        <div class="stat-label">Lượt sử dụng</div>
                    </div>
                    <div class="stat-card expired">
                        <div class="stat-icon"><i class="fa fa-clock"></i></div>
                        <div class="stat-value" id="statExpired">0</div>
                        <div class="stat-label">Hết hạn</div>
                    </div>
                </div>
                
                <!-- FILTERS -->
                <div class="table-container mb-4">
                    <div class="p-3 border-bottom">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-search"></i></span>
                                    <input type="text" class="form-control" id="searchInput" placeholder="Tìm theo mã hoặc tên voucher...">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" id="filterStatus">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="active">Hoạt động</option>
                                    <option value="inactive">Tạm dừng</option>
                                    <option value="expired">Hết hạn</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" id="filterType">
                                    <option value="">Tất cả loại</option>
                                    <option value="percent">Giảm %</option>
                                    <option value="fixed">Giảm tiền</option>
                                </select>
                            </div>
                            <div class="col-md-4 text-end">
                                <button class="btn btn-outline-secondary" onclick="loadVouchers()">
                                    <i class="fa fa-refresh me-1"></i>Làm mới
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- VOUCHERS TABLE -->
                <div class="table-container">
                    <div class="table-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1"><i class="fa fa-ticket-alt me-2"></i>Danh sách Voucher</h5>
                            <small class="opacity-75">Quản lý mã giảm giá cho khách hàng</small>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="adm-table">
                            <thead>
                                <tr>
                                    <th>Mã Voucher</th>
                                    <th>Tên / Mô tả</th>
                                    <th>Loại giảm</th>
                                    <th>Giá trị</th>
                                    <th>Điều kiện</th>
                                    <th>Sử dụng</th>
                                    <th>Thời gian</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="vouchersTableBody">
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status"></div>
                                        <p class="mt-2 mb-0">Đang tải dữ liệu...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>
        </div>
    </div>

    <!-- ADD/EDIT VOUCHER MODAL -->
    <div class="modal fade" id="voucherModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fa fa-ticket-alt me-2"></i>Thêm Voucher mới
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="voucherForm">
                        <input type="hidden" id="voucherId" value="">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Mã Voucher <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control text-uppercase" id="voucherCode" 
                                           placeholder="VD: SALE50K" maxlength="50" required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="generateCode()">
                                        <i class="fa fa-random"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Chỉ chữ cái, số và dấu gạch dưới</small>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Tên Voucher <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="voucherName" 
                                       placeholder="VD: Giảm giá mùa hè" required>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Mô tả</label>
                                <textarea class="form-control" id="voucherDesc" rows="2" 
                                          placeholder="Mô tả chi tiết về voucher..."></textarea>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Loại giảm giá <span class="text-danger">*</span></label>
                                <select class="form-select" id="discountType" onchange="updateDiscountUI()">
                                    <option value="percent">Giảm theo %</option>
                                    <option value="fixed">Giảm số tiền cố định</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Giá trị giảm <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="discountValue" 
                                           min="0" step="1000" required>
                                    <span class="input-group-text" id="discountUnit">%</span>
                                </div>
                            </div>
                            
                            <div class="col-md-4" id="maxDiscountGroup">
                                <label class="form-label">Giảm tối đa</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="maxDiscount" 
                                           min="0" step="10000" placeholder="Không giới hạn">
                                    <span class="input-group-text">đ</span>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Đơn hàng tối thiểu</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="minOrder" 
                                           min="0" step="10000" value="0">
                                    <span class="input-group-text">đ</span>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Số lượng voucher</label>
                                <input type="number" class="form-control" id="quantity" 
                                       min="1" placeholder="Không giới hạn">
                                <small class="text-muted">Để trống nếu không giới hạn</small>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="startDate" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="endDate" required>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="voucherStatus" checked>
                                    <label class="form-check-label" for="voucherStatus">Kích hoạt voucher</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" onclick="saveVoucher()">
                        <i class="fa fa-save me-1"></i>Lưu Voucher
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        var voucherModal;
        var vouchersData = [];
        
        document.addEventListener('DOMContentLoaded', function() {
            voucherModal = new bootstrap.Modal(document.getElementById('voucherModal'));
            loadVouchers();
            
            // Search & filter events
            document.getElementById('searchInput').addEventListener('input', filterVouchers);
            document.getElementById('filterStatus').addEventListener('change', filterVouchers);
            document.getElementById('filterType').addEventListener('change', filterVouchers);
        });
        
        // Load vouchers from server
        function loadVouchers() {
            $.ajax({
                url: '../Api/ProcessVoucher.php',
                type: 'GET',
                data: { request: 'getAll' },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        vouchersData = response.data;
                        renderVouchers(vouchersData);
                        updateStats(vouchersData);
                    } else {
                        showError('Không thể tải dữ liệu voucher');
                    }
                },
                error: function() {
                    showError('Lỗi kết nối server');
                }
            });
        }
        
        // Render vouchers table
        function renderVouchers(vouchers) {
            var tbody = document.getElementById('vouchersTableBody');
            
            if (!vouchers || vouchers.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="fa fa-ticket-alt fa-3x text-muted mb-3 d-block"></i>
                            <p class="text-muted mb-0">Chưa có voucher nào</p>
                        </td>
                    </tr>
                `;
                return;
            }
            
            var html = '';
            vouchers.forEach(function(v) {
                var statusClass = getStatusClass(v);
                var statusText = getStatusText(v);
                var usagePercent = v.SoLuong ? Math.round((v.DaSuDung / v.SoLuong) * 100) : 0;
                
                html += `
                    <tr>
                        <td>
                            <span class="voucher-code">${v.Code}</span>
                            <button class="btn btn-sm btn-link p-0 ms-2" onclick="copyCode('${v.Code}')" title="Copy">
                                <i class="fa fa-copy"></i>
                            </button>
                        </td>
                        <td>
                            <strong>${v.TenVoucher}</strong>
                            <br><small class="text-muted">${v.MoTa || '-'}</small>
                        </td>
                        <td>
                            <span class="voucher-badge ${v.LoaiGiam}">
                                ${v.LoaiGiam === 'percent' ? 'Giảm %' : 'Giảm tiền'}
                            </span>
                        </td>
                        <td>
                            <span class="price-display">
                                ${v.LoaiGiam === 'percent' ? v.GiaTri + '%' : formatPrice(v.GiaTri)}
                            </span>
                            ${v.LoaiGiam === 'percent' && v.GiamToiDa ? '<br><small class="text-muted">Tối đa: ' + formatPrice(v.GiamToiDa) + '</small>' : ''}
                        </td>
                        <td>
                            <small>Đơn từ: ${formatPrice(v.DonToiThieu)}</small>
                        </td>
                        <td>
                            <div class="usage-info">
                                ${v.DaSuDung}/${v.SoLuong || '∞'}
                            </div>
                            ${v.SoLuong ? `
                                <div class="usage-bar">
                                    <div class="usage-bar-fill" style="width: ${usagePercent}%"></div>
                                </div>
                            ` : ''}
                        </td>
                        <td>
                            <small>
                                <i class="fa fa-calendar me-1"></i>${formatDate(v.NgayBatDau)}<br>
                                <i class="fa fa-calendar-times me-1"></i>${formatDate(v.NgayKetThuc)}
                            </small>
                        </td>
                        <td>
                            <span class="voucher-badge ${statusClass}">${statusText}</span>
                        </td>
                        <td>
                            <button class="action-btn btn-edit" onclick="editVoucher(${v.MaVoucher})" title="Sửa">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="action-btn btn-toggle ${v.TrangThai == 1 ? '' : 'off'}" 
                                    onclick="toggleVoucher(${v.MaVoucher}, ${v.TrangThai})" 
                                    title="${v.TrangThai == 1 ? 'Tạm dừng' : 'Kích hoạt'}">
                                <i class="fa fa-${v.TrangThai == 1 ? 'pause' : 'play'}"></i>
                            </button>
                            <button class="action-btn btn-delete" onclick="deleteVoucher(${v.MaVoucher})" title="Xóa">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = html;
        }
        
        // Update statistics
        function updateStats(vouchers) {
            var now = new Date();
            var total = vouchers.length;
            var active = 0;
            var used = 0;
            var expired = 0;
            
            vouchers.forEach(function(v) {
                var endDate = new Date(v.NgayKetThuc);
                if (endDate < now) {
                    expired++;
                } else if (v.TrangThai == 1) {
                    active++;
                }
                used += parseInt(v.DaSuDung) || 0;
            });
            
            document.getElementById('statTotal').textContent = total;
            document.getElementById('statActive').textContent = active;
            document.getElementById('statUsed').textContent = used;
            document.getElementById('statExpired').textContent = expired;
        }
        
        // Filter vouchers
        function filterVouchers() {
            var search = document.getElementById('searchInput').value.toLowerCase();
            var status = document.getElementById('filterStatus').value;
            var type = document.getElementById('filterType').value;
            
            var filtered = vouchersData.filter(function(v) {
                var now = new Date();
                var endDate = new Date(v.NgayKetThuc);
                var vStatus = endDate < now ? 'expired' : (v.TrangThai == 1 ? 'active' : 'inactive');
                
                var matchSearch = !search || 
                    v.Code.toLowerCase().includes(search) || 
                    v.TenVoucher.toLowerCase().includes(search);
                var matchStatus = !status || vStatus === status;
                var matchType = !type || v.LoaiGiam === type;
                
                return matchSearch && matchStatus && matchType;
            });
            
            renderVouchers(filtered);
        }
        
        // Get status class
        function getStatusClass(v) {
            var now = new Date();
            var endDate = new Date(v.NgayKetThuc);
            if (endDate < now) return 'expired';
            return v.TrangThai == 1 ? 'active' : 'inactive';
        }
        
        // Get status text
        function getStatusText(v) {
            var now = new Date();
            var endDate = new Date(v.NgayKetThuc);
            if (endDate < now) return 'Hết hạn';
            return v.TrangThai == 1 ? 'Hoạt động' : 'Tạm dừng';
        }
        
        // Open add modal
        function openAddModal() {
            document.getElementById('modalTitle').innerHTML = '<i class="fa fa-ticket-alt me-2"></i>Thêm Voucher mới';
            document.getElementById('voucherForm').reset();
            document.getElementById('voucherId').value = '';
            document.getElementById('voucherStatus').checked = true;
            updateDiscountUI();
            
            // Set default dates
            var now = new Date();
            var nextMonth = new Date(now.getTime() + 30*24*60*60*1000);
            document.getElementById('startDate').value = formatDateTimeLocal(now);
            document.getElementById('endDate').value = formatDateTimeLocal(nextMonth);
            
            voucherModal.show();
        }
        
        // Edit voucher
        function editVoucher(id) {
            var voucher = vouchersData.find(v => v.MaVoucher == id);
            if (!voucher) return;
            
            document.getElementById('modalTitle').innerHTML = '<i class="fa fa-edit me-2"></i>Chỉnh sửa Voucher';
            document.getElementById('voucherId').value = voucher.MaVoucher;
            document.getElementById('voucherCode').value = voucher.Code;
            document.getElementById('voucherName').value = voucher.TenVoucher;
            document.getElementById('voucherDesc').value = voucher.MoTa || '';
            document.getElementById('discountType').value = voucher.LoaiGiam;
            document.getElementById('discountValue').value = voucher.GiaTri;
            document.getElementById('maxDiscount').value = voucher.GiamToiDa || '';
            document.getElementById('minOrder').value = voucher.DonToiThieu;
            document.getElementById('quantity').value = voucher.SoLuong || '';
            document.getElementById('startDate').value = formatDateTimeLocal(new Date(voucher.NgayBatDau));
            document.getElementById('endDate').value = formatDateTimeLocal(new Date(voucher.NgayKetThuc));
            document.getElementById('voucherStatus').checked = voucher.TrangThai == 1;
            
            updateDiscountUI();
            voucherModal.show();
        }
        
        // Save voucher
        function saveVoucher() {
            var id = document.getElementById('voucherId').value;
            var data = {
                request: id ? 'update' : 'add',
                MaVoucher: id,
                Code: document.getElementById('voucherCode').value.toUpperCase(),
                TenVoucher: document.getElementById('voucherName').value,
                MoTa: document.getElementById('voucherDesc').value,
                LoaiGiam: document.getElementById('discountType').value,
                GiaTri: document.getElementById('discountValue').value,
                GiamToiDa: document.getElementById('maxDiscount').value || null,
                DonToiThieu: document.getElementById('minOrder').value || 0,
                SoLuong: document.getElementById('quantity').value || null,
                NgayBatDau: document.getElementById('startDate').value,
                NgayKetThuc: document.getElementById('endDate').value,
                TrangThai: document.getElementById('voucherStatus').checked ? 1 : 0
            };
            
            // Validation
            if (!data.Code || !data.TenVoucher || !data.GiaTri || !data.NgayBatDau || !data.NgayKetThuc) {
                Swal.fire('Lỗi', 'Vui lòng điền đầy đủ thông tin bắt buộc', 'warning');
                return;
            }
            
            $.ajax({
                url: '../Api/ProcessVoucher.php',
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công!',
                            text: id ? 'Đã cập nhật voucher' : 'Đã thêm voucher mới',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        voucherModal.hide();
                        loadVouchers();
                    } else {
                        Swal.fire('Lỗi', response.message || 'Không thể lưu voucher', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Lỗi', 'Lỗi kết nối server', 'error');
                }
            });
        }
        
        // Toggle voucher status
        function toggleVoucher(id, currentStatus) {
            $.ajax({
                url: '../Api/ProcessVoucher.php',
                type: 'POST',
                data: { 
                    request: 'toggle', 
                    MaVoucher: id,
                    TrangThai: currentStatus == 1 ? 0 : 1
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        loadVouchers();
                    }
                }
            });
        }
        
        // Delete voucher
        function deleteVoucher(id) {
            Swal.fire({
                title: 'Xác nhận xóa?',
                text: 'Bạn có chắc muốn xóa voucher này?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#888',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../Api/ProcessVoucher.php',
                        type: 'POST',
                        data: { request: 'delete', MaVoucher: id },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Đã xóa!',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                loadVouchers();
                            } else {
                                Swal.fire('Lỗi', response.message || 'Không thể xóa', 'error');
                            }
                        }
                    });
                }
            });
        }
        
        // Generate random code
        function generateCode() {
            var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            var code = '';
            for (var i = 0; i < 8; i++) {
                code += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById('voucherCode').value = code;
        }
        
        // Copy voucher code
        function copyCode(code) {
            navigator.clipboard.writeText(code).then(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Đã copy!',
                    text: code,
                    timer: 1000,
                    showConfirmButton: false
                });
            });
        }
        
        // Update discount UI based on type
        function updateDiscountUI() {
            var type = document.getElementById('discountType').value;
            var maxGroup = document.getElementById('maxDiscountGroup');
            var unit = document.getElementById('discountUnit');
            
            if (type === 'percent') {
                unit.textContent = '%';
                maxGroup.style.display = 'block';
            } else {
                unit.textContent = 'đ';
                maxGroup.style.display = 'none';
            }
        }
        
        // Format helpers
        function formatPrice(price) {
            return parseInt(price).toLocaleString('vi-VN') + 'đ';
        }
        
        function formatDate(dateStr) {
            var d = new Date(dateStr);
            return d.toLocaleDateString('vi-VN');
        }
        
        function formatDateTimeLocal(date) {
            return date.toISOString().slice(0, 16);
        }
        
        function showError(msg) {
            document.getElementById('vouchersTableBody').innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-4 text-danger">
                        <i class="fa fa-exclamation-triangle fa-2x mb-2 d-block"></i>
                        ${msg}
                    </td>
                </tr>
            `;
        }
    </script>
</body>
</html>




