<?php
// Admin authentication check - restrict access to admin users only
require_once 'includes/AuthCheck.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xử lý Đơn hàng - Admin Dashboard</title>
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
        #admin-dashboard-app .order-id {
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
        #admin-dashboard-app .status-confirmed { background: #d1ecf1; color: #0c5460; }
        #admin-dashboard-app .status-processing { background: #e2d5f1; color: #5a3d8a; }
        #admin-dashboard-app .status-shipping { background: #cce5ff; color: #004085; }
        #admin-dashboard-app .status-completed { background: #d4edda; color: #155724; }
        #admin-dashboard-app .status-cancelled { background: #f8d7da; color: #721c24; }
        
        #admin-dashboard-app .payment-badge {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
        }
        
        #admin-dashboard-app .payment-paid { background: #d4edda; color: #155724; }
        #admin-dashboard-app .payment-unpaid { background: #fff3cd; color: #856404; }
        
        #admin-dashboard-app .method-badge {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }
        
        #admin-dashboard-app .method-momo { background: #af206b; color: #fff; }
        #admin-dashboard-app .method-cod { background: #333; color: #fff; }
        
        #admin-dashboard-app .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        
        #admin-dashboard-app .stat-card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
            transition: all 0.2s;
            border: 2px solid transparent;
        }
        
        #admin-dashboard-app .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        #admin-dashboard-app .stat-card.active {
            border-color: #333;
        }
        
        #admin-dashboard-app .stat-icon {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        
        #admin-dashboard-app .stat-icon.all { background: #e3f2fd; color: #1976d2; }
        #admin-dashboard-app .stat-icon.pending { background: #fff3cd; color: #856404; }
        #admin-dashboard-app .stat-icon.confirmed { background: #d1ecf1; color: #0c5460; }
        #admin-dashboard-app .stat-icon.processing { background: #e2d5f1; color: #5a3d8a; }
        #admin-dashboard-app .stat-icon.shipping { background: #cce5ff; color: #004085; }
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
        
        #admin-dashboard-app .order-total {
            font-weight: 700;
            color: #333;
            font-size: 15px;
        }
        
        #admin-dashboard-app .order-date {
            font-size: 13px;
            color: #666;
        }
        
        /* Modal styles */
        #admin-dashboard-app .detail-modal .modal-dialog {
            max-width: 650px;
        }
        
        #admin-dashboard-app .detail-modal .modal-content {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        }
        
        #admin-dashboard-app .detail-modal .modal-header {
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            padding: 16px 20px;
        }
        
        #admin-dashboard-app .detail-modal .modal-title {
            font-weight: 700;
            color: #333;
            font-size: 18px;
        }
        
        #admin-dashboard-app .detail-modal .modal-body {
            padding: 24px;
        }
        
        #admin-dashboard-app .detail-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding: 16px;
            background: #f8f9fa;
            border-radius: 10px;
            border: 1px solid #e9ecef;
        }
        
        #admin-dashboard-app .detail-section {
            margin-bottom: 24px;
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            overflow: hidden;
        }
        
        #admin-dashboard-app .detail-section-title {
            font-size: 13px;
            font-weight: 700;
            color: #555;
            padding: 12px 16px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }
        
        #admin-dashboard-app .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 16px;
            border-bottom: 1px solid #f5f5f5;
        }
        
        #admin-dashboard-app .detail-row:last-child {
            border-bottom: none;
        }
        
        #admin-dashboard-app .detail-label {
            color: #777;
            font-size: 14px;
        }
        
        #admin-dashboard-app .detail-value {
            font-weight: 600;
            color: #333;
            text-align: right;
            font-size: 14px;
        }
        
        #admin-dashboard-app .product-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.2s;
        }
        
        #admin-dashboard-app .product-item:last-of-type {
            border-bottom: none;
        }
        
        #admin-dashboard-app .product-item:hover {
            background: #fafafa;
        }
        
        #admin-dashboard-app .product-image {
            width: 55px;
            height: 55px;
            border-radius: 8px;
            object-fit: cover;
            background: #f5f5f5;
            border: 1px solid #eee;
        }
        
        #admin-dashboard-app .product-info {
            flex: 1;
        }
        
        #admin-dashboard-app .product-name {
            font-weight: 600;
            color: #333;
            font-size: 14px;
            margin-bottom: 4px;
        }
        
        #admin-dashboard-app .product-qty {
            font-size: 12px;
            color: #888;
        }
        
        #admin-dashboard-app .product-careplan {
            font-size: 12px;
            color: #1428a0;
            background: #f0f3ff;
            padding: 3px 8px;
            border-radius: 4px;
            display: inline-block;
            margin: 4px 0;
        }
        
        #admin-dashboard-app .product-careplan i {
            margin-right: 4px;
        }
        
        #admin-dashboard-app .product-price {
            font-weight: 700;
            color: #333;
            font-size: 14px;
        }
        
        #admin-dashboard-app .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            margin: 0;
        }
        
        #admin-dashboard-app .total-row .label {
            font-weight: 600;
            font-size: 14px;
            color: #333;
        }
        
        #admin-dashboard-app .total-row .value {
            font-size: 20px;
            font-weight: 700;
            color: #333;
        }
        
        /* Status update buttons */
        #admin-dashboard-app .status-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            justify-content: center;
        }
        
        #admin-dashboard-app .btn-status {
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        #admin-dashboard-app .btn-status:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        #admin-dashboard-app .btn-status.confirm { background: #27ae60; color: #fff; }
        #admin-dashboard-app .btn-status.confirm:hover { background: #219a52; }
        #admin-dashboard-app .btn-status.process { background: #8e44ad; color: #fff; }
        #admin-dashboard-app .btn-status.process:hover { background: #7d3c98; }
        #admin-dashboard-app .btn-status.ship { background: #3498db; color: #fff; }
        #admin-dashboard-app .btn-status.ship:hover { background: #2980b9; }
        #admin-dashboard-app .btn-status.complete { background: #27ae60; color: #fff; }
        #admin-dashboard-app .btn-status.complete:hover { background: #219a52; }
        #admin-dashboard-app .btn-status.cancel { background: #fff; color: #e74c3c; border: 1px solid #e74c3c; }
        #admin-dashboard-app .btn-status.cancel:hover { background: #e74c3c; color: #fff; }
        #admin-dashboard-app .btn-status.paid { background: #f39c12; color: #fff; }
        #admin-dashboard-app .btn-status.paid:hover { background: #d68910; }
        
        #admin-dashboard-app .filter-row {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
            flex-wrap: nowrap;
            align-items: center;
        }
        
        #admin-dashboard-app .search-box {
            flex: 2;
            min-width: 0;
        }
        
        #admin-dashboard-app .filter-select {
            flex: 1;
            min-width: 0;
        }
        
        #admin-dashboard-app .table th {
            font-size: 12px;
            text-transform: uppercase;
            color: #888;
            font-weight: 600;
            letter-spacing: 0.5px;
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
                    <i class="fas fa-clipboard-list me-2"></i>
                    Xử lý Đơn hàng
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
                
                <!-- STATS CARDS -->
                <div class="stats-cards">
                    <div class="stat-card active" onclick="filterByStatus('all')" data-status="all">
                        <div class="stat-icon all">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="statAll">0</h3>
                            <p>Tất cả</p>
                        </div>
                    </div>
                    <div class="stat-card" onclick="filterByStatus(1)" data-status="1">
                        <div class="stat-icon pending">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="statPending">0</h3>
                            <p>Chờ xử lý</p>
                        </div>
                    </div>
                    <div class="stat-card" onclick="filterByStatus(2)" data-status="2">
                        <div class="stat-icon confirmed">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="statConfirmed">0</h3>
                            <p>Đã xác nhận</p>
                        </div>
                    </div>
                    <div class="stat-card" onclick="filterByStatus(3)" data-status="3">
                        <div class="stat-icon processing">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="statProcessing">0</h3>
                            <p>Đã lên đơn</p>
                        </div>
                    </div>
                    <div class="stat-card" onclick="filterByStatus(4)" data-status="4">
                        <div class="stat-icon shipping">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="statShipping">0</h3>
                            <p>Đang giao</p>
                        </div>
                    </div>
                    <div class="stat-card" onclick="filterByStatus(5)" data-status="5">
                        <div class="stat-icon completed">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="statCompleted">0</h3>
                            <p>Hoàn thành</p>
                        </div>
                    </div>
                    <div class="stat-card" onclick="filterByStatus(0)" data-status="0">
                        <div class="stat-icon cancelled">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="statCancelled">0</h3>
                            <p>Đã hủy</p>
                        </div>
                    </div>
                </div>
                
                <!-- FILTER ROW -->
                <div class="filter-row">
                    <div class="search-box">
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchInput" placeholder="Tìm theo mã đơn, tên, SĐT...">
                            <button class="btn btn-dark" type="button" onclick="searchOrders()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <select class="form-select filter-select" id="paymentFilter" onchange="applyFilters()">
                        <option value="">Tất cả thanh toán</option>
                        <option value="paid">Đã thanh toán</option>
                        <option value="unpaid">Chưa thanh toán</option>
                    </select>
                    <select class="form-select filter-select" id="methodFilter" onchange="applyFilters()">
                        <option value="">Tất cả phương thức</option>
                        <option value="momo">MoMo</option>
                        <option value="cod">COD</option>
                    </select>
                </div>
                
                <!-- TABLE -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Mã ĐH</th>
                                <th>Khách hàng</th>
                                <th>Ngày đặt</th>
                                <th>Phương thức</th>
                                <th>Thanh toán</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="ordersTableBody">
                            <tr>
                                <td colspan="8">
                                    <div class="text-center py-5">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2 text-muted">Đang tải dữ liệu...</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>
        
        <!-- ORDER DETAIL MODAL - Inside admin-dashboard-app -->
        <div class="modal fade detail-modal" id="orderDetailModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Chi tiết Đơn hàng <span id="modalOrderId"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="orderDetailBody">
                        <!-- Content loaded dynamically -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        var ordersData = [];
        var currentFilter = 'all';
        var detailModal = null;
        
        document.addEventListener('DOMContentLoaded', function() {
            detailModal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
            loadOrders();
            
            document.getElementById('searchInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') searchOrders();
            });
        });
        
        // Load orders
        function loadOrders() {
            $.ajax({
                url: '../Api/ProcessOrderAdmin.php',
                method: 'POST',
                data: { request: 'getall' },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        ordersData = response.data;
                        updateStats();
                        displayOrders(ordersData);
                    } else {
                        showError('Không thể tải dữ liệu');
                    }
                },
                error: function(xhr) {
                    console.log('Error:', xhr.responseText);
                    showError('Lỗi kết nối server');
                }
            });
        }
        
        // Update stats
        function updateStats() {
            document.getElementById('statAll').textContent = ordersData.length;
            document.getElementById('statPending').textContent = ordersData.filter(o => o.TrangThai == 1).length;
            document.getElementById('statConfirmed').textContent = ordersData.filter(o => o.TrangThai == 2).length;
            document.getElementById('statProcessing').textContent = ordersData.filter(o => o.TrangThai == 3).length;
            document.getElementById('statShipping').textContent = ordersData.filter(o => o.TrangThai == 4).length;
            document.getElementById('statCompleted').textContent = ordersData.filter(o => o.TrangThai == 5).length;
            document.getElementById('statCancelled').textContent = ordersData.filter(o => o.TrangThai == 0).length;
        }
        
        // Filter by status
        function filterByStatus(status) {
            currentFilter = status;
            
            // Update active card
            document.querySelectorAll('.stat-card').forEach(card => {
                card.classList.remove('active');
                if (card.dataset.status == status) {
                    card.classList.add('active');
                }
            });
            
            applyFilters();
        }
        
        // Apply all filters
        function applyFilters() {
            var filtered = ordersData;
            
            // Status filter
            if (currentFilter !== 'all') {
                filtered = filtered.filter(o => o.TrangThai == currentFilter);
            }
            
            // Payment filter
            var paymentFilter = document.getElementById('paymentFilter').value;
            if (paymentFilter === 'paid') {
                filtered = filtered.filter(o => o.DaThanhToan == 1);
            } else if (paymentFilter === 'unpaid') {
                filtered = filtered.filter(o => o.DaThanhToan == 0);
            }
            
            // Method filter
            var methodFilter = document.getElementById('methodFilter').value;
            if (methodFilter) {
                filtered = filtered.filter(o => o.PhuongThucTT === methodFilter);
            }
            
            // Search filter
            var keyword = document.getElementById('searchInput').value.toLowerCase().trim();
            if (keyword) {
                filtered = filtered.filter(o => 
                    o.MaHD.toString().includes(keyword) ||
                    o.NguoiNhan.toLowerCase().includes(keyword) ||
                    o.SDT.includes(keyword)
                );
            }
            
            displayOrders(filtered);
        }
        
        // Search orders
        function searchOrders() {
            applyFilters();
        }
        
        // Display orders
        function displayOrders(orders) {
            var tbody = document.getElementById('ordersTableBody');
            
            if (!orders || orders.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8">
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                <h5>Không có đơn hàng nào</h5>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }
            
            var html = '';
            orders.forEach(function(o) {
                var statusInfo = getStatusInfo(o.TrangThai);
                var paymentBadge = o.DaThanhToan == 1 
                    ? '<span class="payment-badge payment-paid"><i class="fas fa-check"></i> Đã TT</span>'
                    : '<span class="payment-badge payment-unpaid"><i class="fas fa-clock"></i> Chưa TT</span>';
                var methodBadge = o.PhuongThucTT === 'momo'
                    ? '<span class="method-badge method-momo">MoMo</span>'
                    : '<span class="method-badge method-cod">COD</span>';
                
                html += `
                    <tr>
                        <td><span class="order-id">#${o.MaHD}</span></td>
                        <td>
                            <div class="customer-info">
                                <div class="customer-name">${o.NguoiNhan}</div>
                                <div class="customer-phone">${o.SDT}</div>
                            </div>
                        </td>
                        <td><span class="order-date">${formatDate(o.NgayLap)}</span></td>
                        <td>${methodBadge}</td>
                        <td>${paymentBadge}</td>
                        <td><span class="order-total">${formatPrice(o.TongTien)}</span></td>
                        <td>
                            <span class="status-badge ${statusInfo.class}">
                                <i class="fas ${statusInfo.icon}"></i>
                                ${statusInfo.text}
                            </span>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-info btn-sm action-btn" onclick="viewOrder(${o.MaHD})" title="Chi tiết">
                                <i class="fas fa-eye"></i>
                            </button>
                            ${getActionButtons(o)}
                        </td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = html;
        }
        
        // Get status info
        function getStatusInfo(status) {
            switch(parseInt(status)) {
                case 1: return { text: 'Chờ xử lý', class: 'status-pending', icon: 'fa-clock' };
                case 2: return { text: 'Đã xác nhận', class: 'status-confirmed', icon: 'fa-check-circle' };
                case 3: return { text: 'Đã lên đơn', class: 'status-processing', icon: 'fa-box' };
                case 4: return { text: 'Đang giao', class: 'status-shipping', icon: 'fa-truck' };
                case 5: return { text: 'Hoàn thành', class: 'status-completed', icon: 'fa-check-double' };
                case 0: return { text: 'Đã hủy', class: 'status-cancelled', icon: 'fa-times-circle' };
                default: return { text: 'Không rõ', class: 'status-pending', icon: 'fa-question' };
            }
        }
        
        // Get action buttons based on order status
        function getActionButtons(order) {
            var status = parseInt(order.TrangThai);
            
            // Đã hoàn thành hoặc đã hủy - không có nút
            if (status == 5 || status == 0) {
                return '<span class="text-muted">-</span>';
            }
            
            // Lấy trạng thái tiếp theo
            var nextStatus = status + 1;
            var nextText = '';
            switch(nextStatus) {
                case 2: nextText = 'Xác nhận'; break;
                case 3: nextText = 'Lên đơn'; break;
                case 4: nextText = 'Giao hàng'; break;
                case 5: nextText = 'Hoàn thành'; break;
            }
            
            return `
                <button class="btn btn-success btn-sm action-btn" onclick="nextStep(${order.MaHD}, ${nextStatus})" title="${nextText}">
                    <i class="fas fa-check"></i>
                </button>
                <button class="btn btn-danger btn-sm action-btn" onclick="cancelOrder(${order.MaHD})" title="Hủy đơn">
                    <i class="fas fa-times"></i>
                </button>
            `;
        }
        
        // Next step - chuyển sang trạng thái tiếp theo
        function nextStep(orderId, nextStatus) {
            var nextText = '';
            switch(nextStatus) {
                case 2: nextText = 'Xác nhận đơn hàng'; break;
                case 3: nextText = 'Lên đơn'; break;
                case 4: nextText = 'Giao hàng'; break;
                case 5: nextText = 'Hoàn thành'; break;
            }
            
            Swal.fire({
                title: nextText + '?',
                text: 'Đơn hàng #' + orderId,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Xác nhận',
                cancelButtonText: 'Hủy',
                confirmButtonColor: '#28a745'
            }).then((result) => {
                if (result.isConfirmed) {
                    updateStatus(orderId, nextStatus);
                }
            });
        }

        // View order detail
        function viewOrder(orderId) {
            var order = ordersData.find(o => o.MaHD == orderId);
            if (!order) return;
            
            document.getElementById('modalOrderId').textContent = '#' + order.MaHD;
            
            // Load order details
            $.ajax({
                url: '../Api/ProcessOrderAdmin.php',
                method: 'POST',
                data: { request: 'getdetail', MaHD: orderId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        renderOrderDetail(order, response.data);
                        detailModal.show();
                    }
                }
            });
        }
        
        // Render order detail
        function renderOrderDetail(order, products) {
            var statusInfo = getStatusInfo(order.TrangThai);
            var paymentStatus = order.DaThanhToan == 1 
                ? '<span class="payment-badge payment-paid"><i class="fas fa-check"></i> Đã thanh toán</span>'
                : '<span class="payment-badge payment-unpaid"><i class="fas fa-clock"></i> Chưa thanh toán</span>';
            var methodText = order.PhuongThucTT === 'momo' ? 'Ví MoMo' : 'Thanh toán khi nhận hàng (COD)';
            
            var productsHtml = '';
            if (products && products.length > 0) {
                products.forEach(function(p) {
                    // Kiểm tra care plan từ SeriesConfig
                    var carePlanHtml = '';
                    if (p.SeriesConfig) {
                        try {
                            var config = JSON.parse(p.SeriesConfig);
                            if (config.carePlanId && config.carePlanId > 0 && config.carePlanPrice) {
                                carePlanHtml = `<div class="product-careplan"><i class="fas fa-shield-alt"></i> 3AM Care+ (+${formatPrice(config.carePlanPrice)})</div>`;
                            }
                        } catch(e) {}
                    }
                    
                    productsHtml += `
                        <div class="product-item">
                            <div class="product-info">
                                <div class="product-name">${p.TenSP}</div>
                                ${carePlanHtml}
                                <div class="product-qty">Số lượng: ${p.SoLuong}</div>
                            </div>
                            <div class="product-price">${formatPrice(p.DonGia * p.SoLuong)}</div>
                        </div>
                    `;
                });
            }
            
            // Status action buttons - đơn giản hơn
            var statusActions = '';
            var status = parseInt(order.TrangThai);
            
            if (status != 0 && status != 5) {
                var nextStatus = status + 1;
                var nextText = '';
                var nextIcon = '';
                switch(nextStatus) {
                    case 2: nextText = 'Xác nhận đơn'; nextIcon = 'fa-check'; break;
                    case 3: nextText = 'Lên đơn'; nextIcon = 'fa-box'; break;
                    case 4: nextText = 'Giao hàng'; nextIcon = 'fa-truck'; break;
                    case 5: nextText = 'Hoàn thành'; nextIcon = 'fa-check-double'; break;
                }
                
                statusActions = `
                    <div class="status-actions">
                        <button class="btn-status confirm" onclick="nextStep(${order.MaHD}, ${nextStatus})">
                            <i class="fas ${nextIcon}"></i> ${nextText}
                        </button>
                        ${order.DaThanhToan == 0 ? `<button class="btn-status paid" onclick="markAsPaid(${order.MaHD})"><i class="fas fa-money-bill"></i> Đã thanh toán</button>` : ''}
                        <button class="btn-status cancel" onclick="cancelOrder(${order.MaHD})">
                            <i class="fas fa-times"></i> Hủy đơn
                        </button>
                    </div>
                `;
            }
            
            var html = `
                <div class="detail-header">
                    <div>
                        <span class="status-badge ${statusInfo.class}">
                            <i class="fas ${statusInfo.icon}"></i>
                            ${statusInfo.text}
                        </span>
                    </div>
                    <div>${paymentStatus}</div>
                </div>
                
                <div class="detail-section">
                    <div class="detail-section-title">Thông tin giao hàng</div>
                    <div class="detail-row">
                        <span class="detail-label">Người nhận:</span>
                        <span class="detail-value">${order.NguoiNhan}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Số điện thoại:</span>
                        <span class="detail-value">${order.SDT}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Địa chỉ:</span>
                        <span class="detail-value">${order.DiaChi}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Phương thức TT:</span>
                        <span class="detail-value">${methodText}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Ngày đặt:</span>
                        <span class="detail-value">${formatDate(order.NgayLap)}</span>
                    </div>
                </div>
                
                <div class="detail-section">
                    <div class="detail-section-title">Sản phẩm</div>
                    ${productsHtml}
                    
                    <div class="total-row">
                        <span class="label">Tổng thanh toán</span>
                        <span class="value">${formatPrice(order.TongTien)}</span>
                    </div>
                </div>
                
                ${statusActions}
            `;
            
            document.getElementById('orderDetailBody').innerHTML = html;
        }
        
        // Update order status
        function updateStatus(orderId, newStatus) {
            $.ajax({
                url: '../Api/ProcessOrderAdmin.php',
                method: 'POST',
                data: { request: 'updatestatus', MaHD: orderId, TrangThai: newStatus },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        detailModal.hide();
                        loadOrders();
                    } else {
                        Swal.fire('Lỗi', response.message, 'error');
                    }
                }
            });
        }
        
        // Mark as paid
        function markAsPaid(orderId) {
            Swal.fire({
                title: 'Xác nhận đã thanh toán?',
                text: 'Đơn hàng #' + orderId + ' sẽ được đánh dấu là đã thanh toán',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Xác nhận',
                cancelButtonText: 'Hủy',
                confirmButtonColor: '#28a745'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../Api/ProcessOrderAdmin.php',
                        method: 'POST',
                        data: { request: 'markpaid', MaHD: orderId },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Thành công',
                                    text: 'Đã cập nhật thanh toán',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                detailModal.hide();
                                loadOrders();
                            }
                        }
                    });
                }
            });
        }
        
        // Cancel order
        function cancelOrder(orderId) {
            Swal.fire({
                title: 'Hủy đơn hàng?',
                text: 'Bạn có chắc muốn hủy đơn hàng #' + orderId + '?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hủy đơn',
                cancelButtonText: 'Không',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    updateStatus(orderId, 0);
                }
            });
        }
        
        // Format price
        function formatPrice(price) {
            return parseInt(price).toLocaleString('vi-VN') + ' đ';
        }
        
        // Format date
        function formatDate(dateStr) {
            var d = new Date(dateStr);
            return d.toLocaleString('vi-VN', {
                hour: '2-digit',
                minute: '2-digit',
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        }
        
        // Show error
        function showError(message) {
            document.getElementById('ordersTableBody').innerHTML = `
                <tr>
                    <td colspan="8">
                        <div class="text-center py-5 text-danger">
                            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                            <h5>${message}</h5>
                        </div>
                    </td>
                </tr>
            `;
        }
    </script>
</body>
</html>




