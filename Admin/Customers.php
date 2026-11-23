<?php
// Admin authentication check - restrict access to admin users only
require_once 'includes/AuthCheck.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Tài khoản - Admin Dashboard</title>
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
        #admin-dashboard-app .customer-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #333;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }
        
        #admin-dashboard-app .action-btn {
            padding: 6px 12px;
            font-size: 14px;
            margin: 0 3px;
        }
        
        #admin-dashboard-app .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        #admin-dashboard-app .status-active {
            background: #d4edda;
            color: #155724;
        }
        
        #admin-dashboard-app .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        
        #admin-dashboard-app .role-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        #admin-dashboard-app .role-admin {
            background: #cce5ff;
            color: #004085;
        }
        
        #admin-dashboard-app .role-user {
            background: #e2e3e5;
            color: #383d41;
        }
        
        #admin-dashboard-app .role-staff {
            background: #fff3cd;
            color: #856404;
        }
        
        #admin-dashboard-app .search-box {
            max-width: 400px;
            margin-bottom: 20px;
        }
        
        #admin-dashboard-app .loading-spinner {
            text-align: center;
            padding: 40px;
        }
        
        #admin-dashboard-app .no-customers {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        
        #admin-dashboard-app .no-customers i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.3;
        }
        
        #admin-dashboard-app .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        #admin-dashboard-app .stat-card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        #admin-dashboard-app .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        #admin-dashboard-app .stat-icon.total { background: #e3f2fd; color: #1976d2; }
        #admin-dashboard-app .stat-icon.active { background: #e8f5e9; color: #388e3c; }
        #admin-dashboard-app .stat-icon.inactive { background: #ffebee; color: #d32f2f; }
        #admin-dashboard-app .stat-icon.admin { background: #fff3e0; color: #f57c00; }
        
        #admin-dashboard-app .stat-info h3 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            color: #333;
        }
        
        #admin-dashboard-app .stat-info p {
            margin: 0;
            color: #888;
            font-size: 13px;
        }
        
        #admin-dashboard-app .customer-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        #admin-dashboard-app .customer-name {
            font-weight: 600;
            color: #333;
        }
        
        #admin-dashboard-app .customer-email {
            font-size: 13px;
            color: #888;
        }
        
        /* Modal styles */
        #admin-dashboard-app .modal-header {
            border-bottom: 1px solid #eee;
            padding: 20px 24px;
        }
        
        #admin-dashboard-app .modal-body {
            padding: 24px;
        }
        
        #admin-dashboard-app .modal-footer {
            border-top: 1px solid #eee;
            padding: 16px 24px;
        }
        
        #admin-dashboard-app .form-label {
            font-weight: 500;
            color: #333;
            margin-bottom: 6px;
        }
        
        #admin-dashboard-app .form-control, 
        #admin-dashboard-app .form-select {
            border-radius: 8px;
            padding: 10px 14px;
            border: 1px solid #ddd;
        }
        
        #admin-dashboard-app .form-control:focus,
        #admin-dashboard-app .form-select:focus {
            border-color: #333;
            box-shadow: 0 0 0 3px rgba(0,0,0,0.1);
        }
        
        #admin-dashboard-app .btn-save {
            background: #333;
            color: #fff;
            padding: 10px 24px;
            border-radius: 8px;
            border: none;
        }
        
        #admin-dashboard-app .btn-save:hover {
            background: #555;
        }
        
        #admin-dashboard-app .btn-cancel {
            background: #f8f9fa;
            color: #333;
            padding: 10px 24px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        
        #admin-dashboard-app .google-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 11px;
            color: #666;
        }
        
        #admin-dashboard-app .google-badge i {
            color: #4285f4;
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
                    <i class="fas fa-users me-2"></i>
                    Quản lý Tài khoản
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
                    <div class="stat-card">
                        <div class="stat-icon total">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="totalCustomers">0</h3>
                            <p>Tổng tài khoản</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon active">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="activeCustomers">0</h3>
                            <p>Đang hoạt động</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon inactive">
                            <i class="fas fa-user-times"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="inactiveCustomers">0</h3>
                            <p>Đã khóa</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon admin">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="adminCount">0</h3>
                            <p>Quản trị viên</p>
                        </div>
                    </div>
                </div>
                
                <!-- TOOLBAR -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="search-box">
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchInput" placeholder="Tìm kiếm tài khoản...">
                            <button class="btn btn-dark" type="button" onclick="searchCustomers()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <button class="btn btn-dark" onclick="openAddModal()">
                        <i class="fas fa-plus me-2"></i>
                        Thêm Tài khoản
                    </button>
                </div>
                
                <!-- TABLE -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Tài khoản</th>
                                <th>Số điện thoại</th>
                                <th>Địa chỉ</th>
                                <th>Quyền</th>
                                <th>Trạng thái</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody id="customersTableBody">
                            <tr>
                                <td colspan="7">
                                    <div class="loading-spinner">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2">Đang tải dữ liệu...</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>
    </div>
    
    <!-- ADD/EDIT MODAL -->
    <div class="modal fade" id="customerModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Thêm Tài khoản</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="customerForm">
                        <input type="hidden" id="customerId">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Họ <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="ho" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="ten" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" class="form-control" id="sdt">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Giới tính</label>
                                <select class="form-select" id="gioitinh">
                                    <option value="Nam">Nam</option>
                                    <option value="Nữ">Nữ</option>
                                    <option value="Khác">Khác</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tài khoản</label>
                                <input type="text" class="form-control" id="taikhoan">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <input type="text" class="form-control" id="diachi">
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Mật khẩu <span class="text-muted">(để trống nếu không đổi)</span></label>
                                <input type="password" class="form-control" id="matkhau">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Xác nhận mật khẩu</label>
                                <input type="password" class="form-control" id="matkhau2">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Quyền</label>
                                <select class="form-select" id="maquyen">
                                    <option value="1">Khách hàng</option>
                                    <option value="2">Quản trị viên</option>
                                    <option value="3">Nhân viên</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Trạng thái</label>
                                <select class="form-select" id="trangthai">
                                    <option value="1">Hoạt động</option>
                                    <option value="0">Khóa</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-save" onclick="saveCustomer()">
                        <i class="fas fa-save me-2"></i>Lưu
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- VIEW MODAL -->
    <div class="modal fade" id="viewModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết Tài khoản</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="viewModalBody">
                    <!-- Content loaded dynamically -->
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
        var customersData = [];
        var customerModal = null;
        var viewModal = null;
        
        document.addEventListener('DOMContentLoaded', function() {
            customerModal = new bootstrap.Modal(document.getElementById('customerModal'));
            viewModal = new bootstrap.Modal(document.getElementById('viewModal'));
            loadCustomers();
            
            // Search on Enter
            document.getElementById('searchInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') searchCustomers();
            });
        });
        
        // Load customers
        function loadCustomers() {
            $.ajax({
                url: '../Api/ProcessUser.php',
                method: 'POST',
                data: { request: 'getall' },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        customersData = response.data;
                        displayCustomers(customersData);
                        updateStats(customersData);
                    } else {
                        showError('Không thể tải dữ liệu');
                    }
                },
                error: function() {
                    showError('Lỗi kết nối server');
                }
            });
        }
        
        // Display customers
        function displayCustomers(customers) {
            var tbody = document.getElementById('customersTableBody');
            
            if (!customers || customers.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7">
                            <div class="no-customers">
                                <i class="fas fa-users"></i>
                                <h4>Chưa có tài khoản nào</h4>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }
            
            var html = '';
            customers.forEach(function(c) {
                var initials = (c.Ho.charAt(0) + c.Ten.charAt(0)).toUpperCase();
                var fullName = c.Ho + ' ' + c.Ten;
                var roleClass = getRoleClass(c.MaQuyen);
                var roleText = getRoleText(c.MaQuyen);
                var statusClass = c.TrangThai == 1 ? 'status-active' : 'status-inactive';
                var statusText = c.TrangThai == 1 ? 'Hoạt động' : 'Đã khóa';
                var googleBadge = c.google_id ? '<span class="google-badge"><i class="fab fa-google"></i> Google</span>' : '';
                
                html += `
                    <tr>
                        <td><strong>${c.MaND}</strong></td>
                        <td>
                            <div class="customer-info">
                                <div class="customer-avatar">${initials}</div>
                                <div>
                                    <div class="customer-name">${fullName} ${googleBadge}</div>
                                    <div class="customer-email">${c.Email}</div>
                                </div>
                            </div>
                        </td>
                        <td>${c.SDT || '-'}</td>
                        <td>${c.DiaChi ? (c.DiaChi.length > 30 ? c.DiaChi.substring(0, 30) + '...' : c.DiaChi) : '-'}</td>
                        <td><span class="role-badge ${roleClass}">${roleText}</span></td>
                        <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                        <td class="text-center">
                            <button class="btn btn-info action-btn" onclick="viewCustomer(${c.MaND})" title="Xem">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning action-btn" onclick="editCustomer(${c.MaND})" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger action-btn" onclick="deleteCustomer(${c.MaND})" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = html;
        }
        
        // Update stats
        function updateStats(customers) {
            var total = customers.length;
            var active = customers.filter(c => c.TrangThai == 1).length;
            var inactive = customers.filter(c => c.TrangThai == 0).length;
            var admins = customers.filter(c => c.MaQuyen == 2).length;
            
            document.getElementById('totalCustomers').textContent = total;
            document.getElementById('activeCustomers').textContent = active;
            document.getElementById('inactiveCustomers').textContent = inactive;
            document.getElementById('adminCount').textContent = admins;
        }
        
        // Get role class
        function getRoleClass(role) {
            switch(parseInt(role)) {
                case 2: return 'role-admin';
                case 3: return 'role-staff';
                default: return 'role-user';
            }
        }
        
        // Get role text
        function getRoleText(role) {
            switch(parseInt(role)) {
                case 2: return 'Quản trị viên';
                case 3: return 'Nhân viên';
                default: return 'Khách hàng';
            }
        }
        
        // Search customers
        function searchCustomers() {
            var keyword = document.getElementById('searchInput').value.toLowerCase().trim();
            
            if (!keyword) {
                displayCustomers(customersData);
                return;
            }
            
            var filtered = customersData.filter(function(c) {
                var fullName = (c.Ho + ' ' + c.Ten).toLowerCase();
                return fullName.includes(keyword) || 
                       c.Email.toLowerCase().includes(keyword) ||
                       (c.SDT && c.SDT.includes(keyword));
            });
            
            displayCustomers(filtered);
        }
        
        // Open add modal
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Thêm Tài khoản';
            document.getElementById('customerForm').reset();
            document.getElementById('customerId').value = '';
            customerModal.show();
        }
        
        // View customer
        function viewCustomer(id) {
            var customer = customersData.find(c => c.MaND == id);
            if (!customer) return;
            
            var roleText = getRoleText(customer.MaQuyen);
            var statusText = customer.TrangThai == 1 ? 'Hoạt động' : 'Đã khóa';
            var googleInfo = customer.google_id ? '<span class="google-badge"><i class="fab fa-google"></i> Đăng nhập bằng Google</span>' : 'Tài khoản thường';
            
            var html = `
                <div class="text-center mb-4">
                    <div class="customer-avatar mx-auto" style="width: 80px; height: 80px; font-size: 28px;">
                        ${(customer.Ho.charAt(0) + customer.Ten.charAt(0)).toUpperCase()}
                    </div>
                    <h4 class="mt-3 mb-1">${customer.Ho} ${customer.Ten}</h4>
                    <p class="text-muted">${customer.Email}</p>
                    <div>${googleInfo}</div>
                </div>
                
                <table class="table table-borderless">
                    <tr>
                        <td class="text-muted" width="40%">Mã khách hàng:</td>
                        <td><strong>${customer.MaND}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Giới tính:</td>
                        <td>${customer.GioiTinh || '-'}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Số điện thoại:</td>
                        <td>${customer.SDT || '-'}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Địa chỉ:</td>
                        <td>${customer.DiaChi || '-'}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tài khoản:</td>
                        <td>${customer.TaiKhoan || '-'}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Quyền:</td>
                        <td><span class="role-badge ${getRoleClass(customer.MaQuyen)}">${roleText}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Trạng thái:</td>
                        <td><span class="status-badge ${customer.TrangThai == 1 ? 'status-active' : 'status-inactive'}">${statusText}</span></td>
                    </tr>
                </table>
            `;
            
            document.getElementById('viewModalBody').innerHTML = html;
            viewModal.show();
        }
        
        // Edit customer
        function editCustomer(id) {
            var customer = customersData.find(c => c.MaND == id);
            if (!customer) return;
            
            document.getElementById('modalTitle').textContent = 'Chỉnh sửa Tài khoản';
            document.getElementById('customerId').value = customer.MaND;
            document.getElementById('ho').value = customer.Ho;
            document.getElementById('ten').value = customer.Ten;
            document.getElementById('email').value = customer.Email;
            document.getElementById('sdt').value = customer.SDT || '';
            document.getElementById('gioitinh').value = customer.GioiTinh || 'Nam';
            document.getElementById('taikhoan').value = customer.TaiKhoan || '';
            document.getElementById('diachi').value = customer.DiaChi || '';
            document.getElementById('maquyen').value = customer.MaQuyen;
            document.getElementById('trangthai').value = customer.TrangThai;
            document.getElementById('matkhau').value = '';
            document.getElementById('matkhau2').value = '';
            
            customerModal.show();
        }
        
        // Save customer
        function saveCustomer() {
            var id = document.getElementById('customerId').value;
            var ho = document.getElementById('ho').value.trim();
            var ten = document.getElementById('ten').value.trim();
            var email = document.getElementById('email').value.trim();
            var sdt = document.getElementById('sdt').value.trim();
            var gioitinh = document.getElementById('gioitinh').value;
            var taikhoan = document.getElementById('taikhoan').value.trim();
            var diachi = document.getElementById('diachi').value.trim();
            var maquyen = document.getElementById('maquyen').value;
            var trangthai = document.getElementById('trangthai').value;
            var matkhau = document.getElementById('matkhau').value;
            var matkhau2 = document.getElementById('matkhau2').value;
            
            // Validate
            if (!ho || !ten || !email) {
                Swal.fire('Lỗi', 'Vui lòng điền đầy đủ thông tin bắt buộc', 'warning');
                return;
            }
            
            if (matkhau && matkhau !== matkhau2) {
                Swal.fire('Lỗi', 'Mật khẩu xác nhận không khớp', 'warning');
                return;
            }
            
            var data = {
                request: id ? 'update' : 'add',
                MaND: id,
                Ho: ho,
                Ten: ten,
                Email: email,
                SDT: sdt,
                GioiTinh: gioitinh,
                TaiKhoan: taikhoan || email,
                DiaChi: diachi,
                MaQuyen: maquyen,
                TrangThai: trangthai
            };
            
            if (matkhau) {
                data.MatKhau = matkhau;
            }
            
            $.ajax({
                url: '../Api/ProcessUser.php',
                method: 'POST',
                data: data,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        customerModal.hide();
                        Swal.fire('Thành công', response.message, 'success');
                        loadCustomers();
                    } else {
                        Swal.fire('Lỗi', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Lỗi', 'Không thể kết nối server', 'error');
                }
            });
        }
        
        // Delete customer
        function deleteCustomer(id) {
            var customer = customersData.find(c => c.MaND == id);
            if (!customer) return;
            
            Swal.fire({
                title: 'Xác nhận xóa?',
                html: `Bạn có chắc muốn xóa tài khoản <strong>${customer.Ho} ${customer.Ten}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../Api/ProcessUser.php',
                        method: 'POST',
                        data: { request: 'delete', MaND: id },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Đã xóa', response.message, 'success');
                                loadCustomers();
                            } else {
                                Swal.fire('Lỗi', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Lỗi', 'Không thể kết nối server', 'error');
                        }
                    });
                }
            });
        }
        
        // Show error
        function showError(message) {
            document.getElementById('customersTableBody').innerHTML = `
                <tr>
                    <td colspan="7">
                        <div class="no-customers">
                            <i class="fas fa-exclamation-triangle"></i>
                            <h4>${message}</h4>
                        </div>
                    </td>
                </tr>
            `;
        }
    </script>
</body>
</html>




