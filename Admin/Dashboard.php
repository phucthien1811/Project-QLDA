<?php
// Admin authentication check - restrict access to admin users only
require_once 'Includes/AuthCheck.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Smartphone Store</title>
    <link rel="icon" type="image/x-icon" href="/Project-PHP/img/favicon.ico?v=2">
    <link rel="shortcut icon" type="image/x-icon" href="/Project-PHP/img/favicon.ico?v=2">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Admin Dashboard CSS -->
    <link rel="stylesheet" href="../Styles/Admin/AdminDashboard.css">
</head>
<body>
    <!-- ISOLATED ADMIN DASHBOARD APP -->
    <div id="admin-dashboard-app">
        
        <!-- SIDEBAR -->
        <?php include 'Includes/Sidebar.php'; ?>
        
        <!-- MAIN CONTENT -->
        <div class="adm-main-content">
            
            <!-- HEADER -->
            <div class="adm-header">
                <h1 class="adm-header-title">Dashboard</h1>
                <div class="adm-user-menu">
                    <button class="adm-notification-btn" style="position: relative;">
                        <i class="fa fa-bell"></i>
                        <span id="pendingOrdersBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;">0</span>
                    </button>
                    <button class="adm-user-btn">
                        <i class="fa fa-user-circle"></i>
                    </button>
                </div>
            </div>
            
            <!-- CONTENT -->
            <div class="adm-content">
                
                <!-- STATS CARDS -->
                <div class="adm-stats-grid">
                    <div class="adm-stat-card">
                        <div class="adm-stat-header">
                            <span class="adm-stat-title">Tổng doanh thu</span>
                            <div class="adm-stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <i class="fa fa-dollar-sign"></i>
                            </div>
                        </div>
                        <div class="adm-stat-value" id="totalRevenue">
                            <span class="placeholder-glow"><span class="placeholder col-8"></span></span>
                        </div>
                        <div class="adm-stat-change" id="monthRevenue">
                            <i class="fa fa-calendar"></i> Tháng này: ...
                        </div>
                    </div>
                    
                    <div class="adm-stat-card">
                        <div class="adm-stat-header">
                            <span class="adm-stat-title">Tổng đơn hàng</span>
                            <div class="adm-stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <i class="fa fa-shopping-cart"></i>
                            </div>
                        </div>
                        <div class="adm-stat-value" id="totalOrders">
                            <span class="placeholder-glow"><span class="placeholder col-6"></span></span>
                        </div>
                        <div class="adm-stat-change" id="pendingOrders">
                            <i class="fa fa-clock text-warning"></i> Chờ xử lý: ...
                        </div>
                    </div>
                    
                    <div class="adm-stat-card">
                        <div class="adm-stat-header">
                            <span class="adm-stat-title">Tổng khách hàng</span>
                            <div class="adm-stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <i class="fa fa-users"></i>
                            </div>
                        </div>
                        <div class="adm-stat-value" id="totalCustomers">
                            <span class="placeholder-glow"><span class="placeholder col-6"></span></span>
                        </div>
                        <div class="adm-stat-change" id="newCustomers">
                            <i class="fa fa-user-plus text-success"></i> Mới tháng này: ...
                        </div>
                    </div>
                    
                    <div class="adm-stat-card">
                        <div class="adm-stat-header">
                            <span class="adm-stat-title">Sản phẩm</span>
                            <div class="adm-stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                <i class="fa fa-box"></i>
                            </div>
                        </div>
                        <div class="adm-stat-value" id="totalProducts">
                            <span class="placeholder-glow"><span class="placeholder col-6"></span></span>
                        </div>
                        <div class="adm-stat-change" id="inStockProducts">
                            <i class="fa fa-check-circle text-success"></i> Còn hàng: ...
                        </div>
                    </div>
                </div>
                
                <!-- CHARTS -->
                <div class="adm-charts-grid">
                    <div class="adm-card">
                        <div class="adm-card-header d-flex justify-content-between align-items-center">
                            <span>Biểu đồ doanh thu</span>
                            <select id="chartPeriod" class="form-select form-select-sm" style="width: auto;">
                                <option value="7days">7 ngày qua</option>
                                <option value="30days">30 ngày qua</option>
                                <option value="12months">12 tháng qua</option>
                            </select>
                        </div>
                        <div style="padding: 20px; height: 300px;">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                    
                    <div class="adm-card">
                        <div class="adm-card-header">Trạng thái đơn hàng</div>
                        <div style="padding: 20px; height: 300px;">
                            <canvas id="orderStatusChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- SECOND ROW: Recent Orders + Top Products -->
                <div class="adm-charts-grid">
                    <!-- RECENT ORDERS TABLE -->
                    <div class="adm-card">
                        <div class="adm-card-header d-flex justify-content-between align-items-center">
                            <span>Đơn hàng gần đây</span>
                            <a href="Orders.php" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                        </div>
                        <div class="table-responsive">
                            <table class="adm-table">
                                <thead>
                                    <tr>
                                        <th>Mã ĐH</th>
                                        <th>Khách hàng</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày</th>
                                    </tr>
                                </thead>
                                <tbody id="recentOrdersBody">
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <div class="spinner-border spinner-border-sm" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- TOP PRODUCTS -->
                    <div class="adm-card">
                        <div class="adm-card-header d-flex justify-content-between align-items-center">
                            <span>Sản phẩm bán chạy</span>
                            <a href="ProductList.php" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                        </div>
                        <div class="table-responsive">
                            <table class="adm-table">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Đã bán</th>
                                        <th>Doanh thu</th>
                                    </tr>
                                </thead>
                                <tbody id="topProductsBody">
                                    <tr>
                                        <td colspan="3" class="text-center">
                                            <div class="spinner-border spinner-border-sm" role="status">
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
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Dashboard JS -->
    <script>
        // Format số tiền VNĐ
        function formatMoney(amount) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
        }
        
        // Format ngày
        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('vi-VN');
        }
        
        // Lấy tên trạng thái
        function getStatusInfo(status) {
            const statusMap = {
                0: { text: 'Đã hủy', class: 'adm-badge-cancelled' },
                1: { text: 'Chờ xử lý', class: 'adm-badge-pending' },
                2: { text: 'Đã xác nhận', class: 'adm-badge-info' },
                3: { text: 'Đã lên đơn', class: 'adm-badge-primary' },
                4: { text: 'Đang giao', class: 'adm-badge-warning' },
                5: { text: 'Hoàn thành', class: 'adm-badge-success' }
            };
            return statusMap[status] || { text: 'Không xác định', class: 'adm-badge-secondary' };
        }
        
        // Biến lưu biểu đồ
        let revenueChart = null;
        let orderStatusChart = null;
        
        // Load thống kê tổng quan
        async function loadStats() {
            try {
                const response = await fetch('../Api/ProcessDashboard.php?action=getstats');
                const result = await response.json();
                
                if (result.success) {
                    const data = result.data;
                    
                    // Cập nhật doanh thu
                    document.getElementById('totalRevenue').textContent = formatMoney(data.revenue.total);
                    document.getElementById('monthRevenue').innerHTML = 
                        `<i class="fa fa-calendar"></i> Tháng này: ${formatMoney(data.revenue.month)}`;
                    
                    // Cập nhật đơn hàng
                    document.getElementById('totalOrders').textContent = data.orders.total.toLocaleString('vi-VN');
                    document.getElementById('pendingOrders').innerHTML = 
                        `<i class="fa fa-clock text-warning"></i> Chờ xử lý: ${data.orders.pending}`;
                    document.getElementById('pendingOrdersBadge').textContent = data.orders.pending;
                    
                    // Cập nhật khách hàng
                    document.getElementById('totalCustomers').textContent = data.customers.total.toLocaleString('vi-VN');
                    document.getElementById('newCustomers').innerHTML = 
                        `<i class="fa fa-user-plus text-success"></i> Mới tháng này: ${data.customers.newThisMonth}`;
                    
                    // Cập nhật sản phẩm (bao gồm cả series)
                    document.getElementById('totalProducts').textContent = data.products.total.toLocaleString('vi-VN');
                    document.getElementById('inStockProducts').innerHTML = 
                        `<i class="fa fa-check-circle text-success"></i> Còn hàng: ${data.products.inStock} | Series: ${data.products.series || 0}`;
                }
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }
        
        // Load biểu đồ doanh thu
        async function loadRevenueChart(period = '7days') {
            try {
                const response = await fetch(`../Api/ProcessDashboard.php?action=getrevenuechart&period=${period}`);
                const result = await response.json();
                
                if (result.success) {
                    const labels = result.data.map(item => {
                        if (period === '12months') {
                            return item.date;
                        }
                        const date = new Date(item.date);
                        return date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' });
                    });
                    const values = result.data.map(item => item.revenue);
                    
                    const ctx = document.getElementById('revenueChart').getContext('2d');
                    
                    if (revenueChart) {
                        revenueChart.destroy();
                    }
                    
                    revenueChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Doanh thu',
                                data: values,
                                borderColor: '#667eea',
                                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: '#667eea',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return formatMoney(context.raw);
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            if (value >= 1000000) {
                                                return (value / 1000000).toFixed(1) + 'M';
                                            } else if (value >= 1000) {
                                                return (value / 1000).toFixed(0) + 'K';
                                            }
                                            return value;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            } catch (error) {
                console.error('Error loading revenue chart:', error);
            }
        }
        
        // Load biểu đồ trạng thái đơn hàng
        async function loadOrderStatusChart() {
            try {
                const response = await fetch('../Api/ProcessDashboard.php?action=getorderstatus');
                const result = await response.json();
                
                if (result.success) {
                    const labels = result.data.map(item => item.status);
                    const values = result.data.map(item => item.count);
                    const colors = result.data.map(item => item.color);
                    
                    const ctx = document.getElementById('orderStatusChart').getContext('2d');
                    
                    if (orderStatusChart) {
                        orderStatusChart.destroy();
                    }
                    
                    orderStatusChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: values,
                                backgroundColor: colors,
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right',
                                    labels: {
                                        padding: 15,
                                        usePointStyle: true
                                    }
                                }
                            }
                        }
                    });
                }
            } catch (error) {
                console.error('Error loading order status chart:', error);
            }
        }
        
        // Load đơn hàng gần đây
        async function loadRecentOrders() {
            try {
                const response = await fetch('../Api/ProcessDashboard.php?action=getrecentorders&limit=5');
                const result = await response.json();
                
                const tbody = document.getElementById('recentOrdersBody');
                
                if (result.success && result.data.length > 0) {
                    tbody.innerHTML = result.data.map(order => {
                        const statusInfo = getStatusInfo(order.status);
                        return `
                            <tr>
                                <td><strong>#${order.id}</strong></td>
                                <td>${order.customer}</td>
                                <td>${formatMoney(order.amount)}</td>
                                <td><span class="adm-badge ${statusInfo.class}">${statusInfo.text}</span></td>
                                <td>${formatDate(order.date)}</td>
                            </tr>
                        `;
                    }).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Chưa có đơn hàng nào</td></tr>';
                }
            } catch (error) {
                console.error('Error loading recent orders:', error);
            }
        }
        
        // Load sản phẩm bán chạy
        async function loadTopProducts() {
            try {
                const response = await fetch('../Api/ProcessDashboard.php?action=gettopproducts&limit=5');
                const result = await response.json();
                
                const tbody = document.getElementById('topProductsBody');
                
                if (result.success && result.data.length > 0) {
                    tbody.innerHTML = result.data.map(product => {
                        return `
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="../${product.image}" 
                                             alt="${product.name}" 
                                             style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px; margin-right: 10px;"
                                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(product.name)}&background=667eea&color=fff&size=40'">
                                        <span class="text-truncate" style="max-width: 150px;" title="${product.name}">
                                            ${product.name}
                                        </span>
                                    </div>
                                </td>
                                <td><span class="badge bg-primary">${product.sold}</span></td>
                                <td>${formatMoney(product.revenue)}</td>
                            </tr>
                        `;
                    }).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">Chưa có dữ liệu</td></tr>';
                }
            } catch (error) {
                console.error('Error loading top products:', error);
            }
        }
        
        // Event listener cho dropdown chọn khoảng thời gian
        document.getElementById('chartPeriod').addEventListener('change', function() {
            loadRevenueChart(this.value);
        });
        
        // Dropdown toggle functionality
        document.querySelectorAll('#admin-dashboard-app .adm-nav-dropdown').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Toggle current dropdown
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
            }
        });
        
        // Load tất cả dữ liệu khi trang được tải
        document.addEventListener('DOMContentLoaded', function() {
            loadStats();
            loadRevenueChart('7days');
            loadOrderStatusChart();
            loadRecentOrders();
            loadTopProducts();
            
            // Auto refresh mỗi 5 phút
            setInterval(() => {
                loadStats();
                loadRecentOrders();
            }, 300000);
        });
    </script>
</body>
</html>


