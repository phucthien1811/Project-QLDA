<?php
// Admin authentication check - restrict access to admin users only
require_once 'includes/AuthCheck.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa chữa & Bảo hành - Admin Dashboard</title>
    <link rel="shortcut icon" href="/Project-PHP/img/favicon.ico?v=2">
    <link rel="shortcut icon" type="image/x-icon" href="/Project-PHP/img/favicon.ico?v=2">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Admin Dashboard CSS -->
    <link rel="stylesheet" href="../Styles/Admin/AdminDashboard.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        /* ========== REPAIR PAGE SPECIFIC STYLES ========== */
        #admin-dashboard-app .repair-id {
            font-weight: 700;
            color: #333;
        }
        
        #admin-dashboard-app .action-btn {
            padding: 6px 12px;
            font-size: 14px;
            margin: 0 2px;
        }
        
        #admin-dashboard-app .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        #admin-dashboard-app .status-pending { background: #fff3cd; color: #856404; }
        #admin-dashboard-app .status-inprogress { background: #cce5ff; color: #004085; }
        #admin-dashboard-app .status-completed { background: #d4edda; color: #155724; }
        #admin-dashboard-app .status-cancelled { background: #f8d7da; color: #721c24; }
        
        #admin-dashboard-app .issue-badge {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
            background: #e9ecef;
            color: #495057;
        }
        
        #admin-dashboard-app .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        
        #admin-dashboard-app .stat-card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        #admin-dashboard-app .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        #admin-dashboard-app .stat-card.active {
            border-color: #333;
            border-width: 2px;
        }
        
        #admin-dashboard-app .stat-icon {
            width: 45px;
            height: 45px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        
        #admin-dashboard-app .stat-icon.all { background: #e3f2fd; color: #1976d2; }
        #admin-dashboard-app .stat-icon.pending { background: #fff3cd; color: #856404; }
        #admin-dashboard-app .stat-icon.inprogress { background: #cce5ff; color: #004085; }
        #admin-dashboard-app .stat-icon.completed { background: #d4edda; color: #155724; }
        #admin-dashboard-app .stat-icon.cancelled { background: #f8d7da; color: #721c24; }
        
        #admin-dashboard-app .stat-info h3 {
            font-size: 22px;
            font-weight: 700;
            margin: 0;
            color: #333;
        }
        
        #admin-dashboard-app .stat-info p {
            margin: 0;
            color: #888;
            font-size: 12px;
        }
        
        #admin-dashboard-app .customer-info {
            line-height: 1.4;
        }
        
        #admin-dashboard-app .customer-name {
            font-weight: 600;
            color: #333;
        }
        
        #admin-dashboard-app .customer-phone {
            font-size: 13px;
            color: #888;
        }
        
        #admin-dashboard-app .product-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        #admin-dashboard-app .product-img {
            width: 50px;
            height: 50px;
            object-fit: contain;
            border-radius: 6px;
            background: #f8f9fa;
        }
        
        #admin-dashboard-app .product-name {
            font-weight: 500;
            color: #333;
            font-size: 13px;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        #admin-dashboard-app .appointment-info {
            font-size: 13px;
        }
        
        #admin-dashboard-app .appointment-date {
            font-weight: 600;
            color: #333;
        }
        
        #admin-dashboard-app .appointment-time {
            color: #666;
        }
        
        /* Modal styles */
        #admin-dashboard-app .detail-modal .modal-dialog {
            max-width: 700px;
        }
        
        #admin-dashboard-app .detail-modal .modal-content {
            border: none;
            border-radius: 8px;
            overflow: hidden;
        }
        
        #admin-dashboard-app .detail-modal .modal-header {
            background: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
            padding: 16px 20px;
        }
        
        #admin-dashboard-app .detail-modal .modal-title {
            font-weight: 700;
            color: #000;
            font-size: 18px;
        }
        
        #admin-dashboard-app .detail-modal .modal-body {
            padding: 24px;
            max-height: 70vh;
            overflow-y: auto;
        }
        
        #admin-dashboard-app .detail-modal .modal-footer {
            background: #f8f9fa;
            border-top: 1px solid #e0e0e0;
            padding: 12px 20px;
        }
        
        #admin-dashboard-app .detail-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 14px 16px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        
        #admin-dashboard-app .detail-section {
            margin-bottom: 16px;
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
        }
        
        #admin-dashboard-app .detail-section:last-child {
            margin-bottom: 0;
        }
        
        #admin-dashboard-app .detail-section-title {
            background: #f8f9fa;
            padding: 10px 16px;
            font-weight: 600;
            font-size: 13px;
            color: #000;
            border-bottom: 1px solid #e0e0e0;
        }
        
        #admin-dashboard-app .detail-section-content {
            padding: 14px 16px;
        }
        
        #admin-dashboard-app .detail-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        #admin-dashboard-app .detail-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        
        #admin-dashboard-app .detail-row:first-child {
            padding-top: 0;
        }
        
        #admin-dashboard-app .detail-label {
            width: 120px;
            color: #666;
            font-size: 13px;
            flex-shrink: 0;
        }
        
        #admin-dashboard-app .detail-value {
            flex: 1;
            color: #333;
            font-size: 13px;
            font-weight: 500;
        }
        
        #admin-dashboard-app .status-select {
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #ddd;
            font-size: 14px;
            min-width: 180px;
        }
        
        #admin-dashboard-app .product-detail-card {
            display: flex;
            gap: 16px;
            align-items: center;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        #admin-dashboard-app .product-detail-img {
            width: 70px;
            height: 70px;
            min-width: 70px;
            object-fit: contain;
            border-radius: 8px;
            background: #fff;
            border: 1px solid #e0e0e0;
        }
        
        #admin-dashboard-app .product-detail-info {
            flex: 1;
            min-width: 0;
        }
        
        #admin-dashboard-app .product-detail-info h5 {
            margin: 0 0 6px 0;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            word-break: break-word;
        }
        
        #admin-dashboard-app .product-detail-info p {
            margin: 0;
            font-size: 13px;
            color: #666;
        }
        
        #admin-dashboard-app .issue-description {
            background: #fff8e1;
            padding: 10px 12px;
            border-radius: 6px;
            border-left: 3px solid #ffc107;
            margin-top: 10px;
            font-size: 13px;
        }
        
        #admin-dashboard-app .cost-input {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        #admin-dashboard-app .cost-input input {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 13px;
            max-width: 200px;
        }
        
        #admin-dashboard-app .cost-input span {
            font-size: 13px;
            color: #666;
        }
        
        #admin-dashboard-app .result-textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 13px;
            min-height: 70px;
            resize: vertical;
        }
        
        /* Page header styles - match other admin pages */
        #admin-dashboard-app .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        
        #admin-dashboard-app .page-desc {
            color: #666;
            font-size: 14px;
            margin: 0;
        }
        
        #admin-dashboard-app .header-search {
            position: relative;
            width: 300px;
        }
        
        #admin-dashboard-app .header-search input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            background: #fff;
        }
        
        #admin-dashboard-app .header-search i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
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
                <h1 class="adm-header-title">Sửa chữa & Bảo hành</h1>
                <div class="adm-user-menu">
                    <div class="header-search">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Tìm kiếm phiếu..." id="searchInput">
                    </div>
                    <button class="adm-user-btn">
                        <i class="fa fa-user-circle"></i>
                    </button>
                </div>
            </div>
            
            <!-- CONTENT -->
            <div class="adm-content">
                
                <!-- PAGE HEADER -->
                <div class="page-header">
                    <div>
                        <p class="page-desc">Quản lý các phiếu yêu cầu sửa chữa và bảo hành từ khách hàng</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary" onclick="loadRepairs()">
                            <i class="fas fa-sync-alt"></i> Làm mới
                        </button>
                    </div>
                </div>
                
                <!-- STATISTICS CARDS -->
                <div class="stats-cards">
                    <div class="stat-card active" data-filter="all" onclick="filterByStatus('all')">
                        <div class="stat-icon all">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="statAll">0</h3>
                            <p>Tất cả</p>
                        </div>
                    </div>
                    <div class="stat-card" data-filter="1" onclick="filterByStatus('1')">
                        <div class="stat-icon pending">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="statPending">0</h3>
                            <p>Chờ xác nhận</p>
                        </div>
                    </div>
                    <div class="stat-card" data-filter="2" onclick="filterByStatus('2')">
                        <div class="stat-icon inprogress">
                            <i class="fas fa-wrench"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="statInProgress">0</h3>
                            <p>Đang sửa chữa</p>
                        </div>
                    </div>
                    <div class="stat-card" data-filter="3" onclick="filterByStatus('3')">
                        <div class="stat-icon completed">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="statCompleted">0</h3>
                            <p>Hoàn tất</p>
                        </div>
                    </div>
                    <div class="stat-card" data-filter="4" onclick="filterByStatus('4')">
                        <div class="stat-icon cancelled">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="statCancelled">0</h3>
                            <p>Đã hủy</p>
                        </div>
                    </div>
                </div>
                
                <!-- REPAIRS TABLE -->
                <div class="adm-card">
                    <div class="adm-card-header d-flex justify-content-between align-items-center">
                        <span>Danh sách phiếu sửa chữa</span>
                        <div class="d-flex gap-2">
                            <select class="form-select form-select-sm" style="width: auto;" id="dateFilter" onchange="loadRepairs()">
                                <option value="all">Tất cả thời gian</option>
                                <option value="today">Hôm nay</option>
                                <option value="week">7 ngày qua</option>
                                <option value="month" selected>30 ngày qua</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="adm-table">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">Mã phiếu</th>
                                    <th>Sản phẩm</th>
                                    <th>Khách hàng</th>
                                    <th>Loại sự cố</th>
                                    <th>Lịch hẹn</th>
                                    <th>Trạng thái</th>
                                    <th style="width: 120px;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="repairsTableBody">
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="spinner-border text-secondary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- REPAIR DETAIL MODAL -->
        <div class="modal fade detail-modal" id="repairDetailModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Chi tiết phiếu sửa chữa <span id="modalRepairId"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="repairDetailBody">
                        <!-- Detail content loaded here -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-primary" onclick="saveRepairChanges()">
                            <i class="fas fa-save"></i> Lưu thay đổi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Global variables
        var allRepairs = [];
        var currentFilter = 'all';
        var currentRepairId = null;
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadRepairs();
            
            // Search functionality
            document.getElementById('searchInput').addEventListener('input', function() {
                filterRepairs();
            });
        });
        
        // Load repairs from server
        function loadRepairs() {
            var dateFilter = document.getElementById('dateFilter').value;
            
            $.ajax({
                url: '../Api/ProcessWarrantyAdmin.php',
                type: 'GET',
                data: { 
                    request: 'getallrepairs',
                    dateFilter: dateFilter
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        allRepairs = response.data;
                        updateStats(allRepairs);
                        filterRepairs();
                    } else {
                        showError('Không thể tải dữ liệu: ' + response.message);
                    }
                },
                error: function() {
                    showError('Lỗi kết nối server');
                }
            });
        }
        
        // Update statistics
        function updateStats(repairs) {
            var stats = {
                all: repairs.length,
                pending: 0,
                inprogress: 0,
                completed: 0,
                cancelled: 0
            };
            
            repairs.forEach(function(r) {
                switch(parseInt(r.TrangThai)) {
                    case 1: stats.pending++; break;
                    case 2: stats.inprogress++; break;
                    case 3: stats.completed++; break;
                    case 4: stats.cancelled++; break;
                }
            });
            
            document.getElementById('statAll').textContent = stats.all;
            document.getElementById('statPending').textContent = stats.pending;
            document.getElementById('statInProgress').textContent = stats.inprogress;
            document.getElementById('statCompleted').textContent = stats.completed;
            document.getElementById('statCancelled').textContent = stats.cancelled;
        }
        
        // Filter by status
        function filterByStatus(status) {
            currentFilter = status;
            
            // Update active card
            document.querySelectorAll('.stat-card').forEach(function(card) {
                card.classList.remove('active');
                if (card.dataset.filter === status) {
                    card.classList.add('active');
                }
            });
            
            filterRepairs();
        }
        
        // Filter repairs
        function filterRepairs() {
            var searchTerm = document.getElementById('searchInput').value.toLowerCase();
            
            var filtered = allRepairs.filter(function(r) {
                // Status filter
                if (currentFilter !== 'all' && r.TrangThai != currentFilter) {
                    return false;
                }
                
                // Search filter
                if (searchTerm) {
                    var searchFields = [
                        r.MaPhieu.toString(),
                        r.TenSP || '',
                        r.HoTen || '',
                        r.SDT || '',
                        r.LoaiSuCo || ''
                    ].join(' ').toLowerCase();
                    
                    if (searchFields.indexOf(searchTerm) === -1) {
                        return false;
                    }
                }
                
                return true;
            });
            
            renderRepairsTable(filtered);
        }
        
        // Render repairs table
        function renderRepairsTable(repairs) {
            var tbody = document.getElementById('repairsTableBody');
            
            if (repairs.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted">Không có phiếu sửa chữa nào</td></tr>';
                return;
            }
            
            var html = '';
            repairs.forEach(function(r) {
                var statusClass = getStatusClass(r.TrangThai);
                var statusText = getStatusText(r.TrangThai);
                var statusIcon = getStatusIcon(r.TrangThai);
                
                html += `
                    <tr>
                        <td><span class="repair-id">#${r.MaPhieu}</span></td>
                        <td>
                            <div class="product-info">
                                <img src="${r.HinhAnhSP || '../img/products/default.png'}" class="product-img" onerror="this.src='../img/products/default.png'">
                                <span class="product-name" title="${r.TenSP || 'Sản phẩm'}">${r.TenSP || 'Sản phẩm'}</span>
                            </div>
                        </td>
                        <td>
                            <div class="customer-info">
                                <div class="customer-name">${r.HoTen}</div>
                                <div class="customer-phone">${r.SDT}</div>
                            </div>
                        </td>
                        <td><span class="issue-badge">${r.LoaiSuCo}</span></td>
                        <td>
                            <div class="appointment-info">
                                <div class="appointment-date">${formatDate(r.NgayHen)}</div>
                                <div class="appointment-time">${r.GioHen}</div>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge status-${statusClass}">
                                <i class="fas ${statusIcon}"></i>
                                ${statusText}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary action-btn" onclick="viewRepairDetail(${r.MaPhieu})" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger action-btn" onclick="deleteRepair(${r.MaPhieu})" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = html;
        }
        
        // Get status class
        function getStatusClass(status) {
            switch(parseInt(status)) {
                case 1: return 'pending';
                case 2: return 'inprogress';
                case 3: return 'completed';
                case 4: return 'cancelled';
                default: return 'pending';
            }
        }
        
        // Get status text
        function getStatusText(status) {
            switch(parseInt(status)) {
                case 1: return 'Chờ xác nhận';
                case 2: return 'Đang sửa chữa';
                case 3: return 'Hoàn tất';
                case 4: return 'Đã hủy';
                default: return 'Không xác định';
            }
        }
        
        // Get status icon
        function getStatusIcon(status) {
            switch(parseInt(status)) {
                case 1: return 'fa-clock';
                case 2: return 'fa-wrench';
                case 3: return 'fa-check-circle';
                case 4: return 'fa-times-circle';
                default: return 'fa-question-circle';
            }
        }
        
        // Format date
        function formatDate(dateStr) {
            if (!dateStr) return '--';
            var date = new Date(dateStr);
            return date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
        }
        
        // Format currency
        function formatCurrency(amount) {
            return parseInt(amount || 0).toLocaleString('vi-VN') + ' VND';
        }
        
        // View repair detail
        function viewRepairDetail(repairId) {
            currentRepairId = repairId;
            var repair = allRepairs.find(function(r) { return r.MaPhieu == repairId; });
            
            if (!repair) {
                showError('Không tìm thấy phiếu sửa chữa');
                return;
            }
            
            document.getElementById('modalRepairId').textContent = '#' + repair.MaPhieu;
            
            var html = `
                <div class="detail-header">
                    <div>
                        <strong>Ngày tạo:</strong> ${formatDate(repair.NgayTao)}
                    </div>
                    <div>
                        <select class="status-select" id="repairStatus">
                            <option value="1" ${repair.TrangThai == 1 ? 'selected' : ''}>Chờ xác nhận</option>
                            <option value="2" ${repair.TrangThai == 2 ? 'selected' : ''}>Đang sửa chữa</option>
                            <option value="3" ${repair.TrangThai == 3 ? 'selected' : ''}>Hoàn tất</option>
                            <option value="4" ${repair.TrangThai == 4 ? 'selected' : ''}>Đã hủy</option>
                        </select>
                    </div>
                </div>
                
                <div class="detail-section">
                    <div class="detail-section-title"><i class="fas fa-mobile-alt me-2"></i>Sản phẩm</div>
                    <div class="detail-section-content">
                        <div class="product-detail-card">
                            <img src="${repair.HinhAnhSP || '../img/products/default.png'}" class="product-detail-img" onerror="this.src='../img/products/default.png'">
                            <div class="product-detail-info">
                                <h5>${repair.TenSP || 'Sản phẩm'}</h5>
                                <p><i class="fas fa-tag me-1"></i>Loại sự cố: <strong>${repair.LoaiSuCo}</strong></p>
                            </div>
                        </div>
                        ${repair.MoTa ? `<div class="issue-description"><strong>Mô tả:</strong> ${repair.MoTa}</div>` : ''}
                    </div>
                </div>
                
                <div class="detail-section">
                    <div class="detail-section-title"><i class="fas fa-user me-2"></i>Thông tin khách hàng</div>
                    <div class="detail-section-content">
                        <div class="detail-row">
                            <span class="detail-label">Họ tên:</span>
                            <span class="detail-value">${repair.HoTen}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Số điện thoại:</span>
                            <span class="detail-value">${repair.SDT}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Email:</span>
                            <span class="detail-value">${repair.Email || '--'}</span>
                        </div>
                        ${repair.GhiChu ? `
                        <div class="detail-row">
                            <span class="detail-label">Ghi chú:</span>
                            <span class="detail-value">${repair.GhiChu}</span>
                        </div>
                        ` : ''}
                    </div>
                </div>
                
                <div class="detail-section">
                    <div class="detail-section-title"><i class="fas fa-calendar-alt me-2"></i>Lịch hẹn</div>
                    <div class="detail-section-content">
                        <div class="detail-row">
                            <span class="detail-label">Ngày hẹn:</span>
                            <span class="detail-value">${formatDate(repair.NgayHen)}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Giờ hẹn:</span>
                            <span class="detail-value">${repair.GioHen}</span>
                        </div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <div class="detail-section-title"><i class="fas fa-tools me-2"></i>Kết quả sửa chữa</div>
                    <div class="detail-section-content">
                        <div class="detail-row">
                            <span class="detail-label">Chi phí:</span>
                            <span class="detail-value">
                                <div class="cost-input">
                                    <input type="number" id="repairCost" value="${repair.ChiPhiSuaChua || 0}" placeholder="Nhập chi phí">
                                    <span>VND</span>
                                </div>
                            </span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Ngày hẹn trả:</span>
                            <span class="detail-value">
                                <input type="date" id="returnDate" value="${repair.NgayHenTra || ''}" class="form-control" style="max-width: 200px;">
                            </span>
                        </div>
                        <div class="detail-row" style="flex-direction: column; gap: 10px;">
                            <span class="detail-label">Kết quả/Ghi chú NV:</span>
                            <textarea class="result-textarea" id="repairResult" placeholder="Nhập kết quả sửa chữa hoặc ghi chú...">${repair.KetQuaSuaChua || ''}</textarea>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('repairDetailBody').innerHTML = html;
            
            var modal = new bootstrap.Modal(document.getElementById('repairDetailModal'));
            modal.show();
        }
        
        // Save repair changes
        function saveRepairChanges() {
            if (!currentRepairId) return;
            
            var status = document.getElementById('repairStatus').value;
            var cost = document.getElementById('repairCost').value;
            var result = document.getElementById('repairResult').value;
            var returnDate = document.getElementById('returnDate').value;
            
            $.ajax({
                url: '../Api/ProcessWarrantyAdmin.php',
                type: 'POST',
                data: {
                    request: 'updaterepair',
                    repairId: currentRepairId,
                    status: status,
                    cost: cost,
                    result: result,
                    returnDate: returnDate
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: 'Đã cập nhật phiếu sửa chữa',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        
                        bootstrap.Modal.getInstance(document.getElementById('repairDetailModal')).hide();
                        loadRepairs();
                    } else {
                        showError(response.message || 'Lỗi cập nhật');
                    }
                },
                error: function() {
                    showError('Lỗi kết nối server');
                }
            });
        }
        
        // Delete repair
        function deleteRepair(repairId) {
            Swal.fire({
                title: 'Xác nhận xóa?',
                text: 'Bạn có chắc muốn xóa phiếu sửa chữa #' + repairId + '?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#000',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../Api/ProcessWarrantyAdmin.php',
                        type: 'POST',
                        data: {
                            request: 'deleterepair',
                            repairId: repairId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Đã xóa',
                                    text: 'Phiếu sửa chữa đã được xóa',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                loadRepairs();
                            } else {
                                showError(response.message || 'Lỗi xóa');
                            }
                        },
                        error: function() {
                            showError('Lỗi kết nối server');
                        }
                    });
                }
            });
        }
        
        // Show error
        function showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: message
            });
        }
    </script>
</body>
</html>




