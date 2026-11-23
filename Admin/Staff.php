<?php
// Admin authentication check - restrict access to admin users only
require_once 'includes/AuthCheck.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Nhân viên - Admin Dashboard</title>
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
        #admin-dashboard-app .staff-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        
        #admin-dashboard-app .search-box {
            max-width: 400px;
            margin-bottom: 20px;
        }
        
        #admin-dashboard-app .loading-spinner {
            text-align: center;
            padding: 40px;
        }
        
        #admin-dashboard-app .no-staff {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        
        #admin-dashboard-app .no-staff i {
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
        
        #admin-dashboard-app .stat-icon.total { background: #e8f4fd; color: #1976d2; }
        #admin-dashboard-app .stat-icon.active { background: #e8f5e9; color: #388e3c; }
        #admin-dashboard-app .stat-icon.inactive { background: #ffebee; color: #d32f2f; }
        
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
        
        #admin-dashboard-app .staff-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        #admin-dashboard-app .staff-name {
            font-weight: 600;
            color: #333;
        }
        
        #admin-dashboard-app .staff-email {
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
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
        }
        
        #admin-dashboard-app .btn-save {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 10px 24px;
            border-radius: 8px;
            border: none;
        }
        
        #admin-dashboard-app .btn-save:hover {
            opacity: 0.9;
            color: #fff;
        }
        
        #admin-dashboard-app .btn-cancel {
            background: #f8f9fa;
            color: #333;
            padding: 10px 24px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        
        #admin-dashboard-app .position-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            background: #fff3cd;
            color: #856404;
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
                    <i class="fas fa-user-tie me-2"></i>
                    Quản lý Nhân viên
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
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="totalStaff">0</h3>
                            <p>Tổng nhân viên</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon active">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="activeStaff">0</h3>
                            <p>Đang làm việc</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon inactive">
                            <i class="fas fa-user-times"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="inactiveStaff">0</h3>
                            <p>Đã nghỉ việc</p>
                        </div>
                    </div>
                </div>
                
                <!-- TOOLBAR -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="search-box">
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchInput" placeholder="Tìm kiếm nhân viên...">
                            <button class="btn btn-dark" type="button" onclick="searchStaff()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <button class="btn btn-dark" onclick="openAddModal()">
                        <i class="fas fa-plus me-2"></i>
                        Thêm Nhân viên
                    </button>
                </div>
                
                <!-- TABLE -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nhân viên</th>
                                <th>Số điện thoại</th>
                                <th>Địa chỉ</th>
                                <th>Trạng thái</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody id="staffTableBody">
                            <tr>
                                <td colspan="6">
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
    <div class="modal fade" id="staffModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Thêm Nhân viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="staffForm">
                        <input type="hidden" id="staffId">
                        
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
                                <label class="form-label">Trạng thái</label>
                                <select class="form-select" id="trangthai">
                                    <option value="1">Đang làm việc</option>
                                    <option value="0">Đã nghỉ việc</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-save" onclick="saveStaff()">
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
                    <h5 class="modal-title">Chi tiết Nhân viên</h5>
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
        var staffData = [];
        var staffModal = null;
        var viewModal = null;
        
        document.addEventListener('DOMContentLoaded', function() {
            staffModal = new bootstrap.Modal(document.getElementById('staffModal'));
            viewModal = new bootstrap.Modal(document.getElementById('viewModal'));
            loadStaff();
            
            // Search on Enter
            document.getElementById('searchInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') searchStaff();
            });
        });
        
        // Load staff (MaQuyen = 3)
        function loadStaff() {
            $.ajax({
                url: '../Api/ProcessUser.php',
                method: 'POST',
                data: { request: 'getall' },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Filter only staff (MaQuyen = 3)
                        staffData = response.data.filter(function(user) {
                            return user.MaQuyen == 3;
                        });
                        displayStaff(staffData);
                        updateStats(staffData);
                    } else {
                        showError('Không thể tải dữ liệu');
                    }
                },
                error: function() {
                    showError('Lỗi kết nối server');
                }
            });
        }
        
        // Display staff
        function displayStaff(staff) {
            var tbody = document.getElementById('staffTableBody');
            
            if (!staff || staff.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6">
                            <div class="no-staff">
                                <i class="fas fa-user-tie"></i>
                                <h4>Chưa có nhân viên nào</h4>
                                <p>Nhấn "Thêm Nhân viên" để thêm nhân viên mới</p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }
            
            var html = '';
            staff.forEach(function(s) {
                var initials = (s.Ho.charAt(0) + s.Ten.charAt(0)).toUpperCase();
                var fullName = s.Ho + ' ' + s.Ten;
                var statusClass = s.TrangThai == 1 ? 'status-active' : 'status-inactive';
                var statusText = s.TrangThai == 1 ? 'Đang làm việc' : 'Đã nghỉ việc';
                
                html += `
                    <tr>
                        <td><strong>${s.MaND}</strong></td>
                        <td>
                            <div class="staff-info">
                                <div class="staff-avatar">${initials}</div>
                                <div>
                                    <div class="staff-name">${fullName}</div>
                                    <div class="staff-email">${s.Email}</div>
                                </div>
                            </div>
                        </td>
                        <td>${s.SDT || '-'}</td>
                        <td>${s.DiaChi ? (s.DiaChi.length > 30 ? s.DiaChi.substring(0, 30) + '...' : s.DiaChi) : '-'}</td>
                        <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                        <td class="text-center">
                            <button class="btn btn-info action-btn" onclick="viewStaff(${s.MaND})" title="Xem">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning action-btn" onclick="editStaff(${s.MaND})" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger action-btn" onclick="deleteStaff(${s.MaND})" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = html;
        }
        
        // Update stats
        function updateStats(staff) {
            var total = staff.length;
            var active = staff.filter(s => s.TrangThai == 1).length;
            var inactive = staff.filter(s => s.TrangThai == 0).length;
            
            document.getElementById('totalStaff').textContent = total;
            document.getElementById('activeStaff').textContent = active;
            document.getElementById('inactiveStaff').textContent = inactive;
        }
        
        // Search staff
        function searchStaff() {
            var keyword = document.getElementById('searchInput').value.toLowerCase().trim();
            
            if (!keyword) {
                displayStaff(staffData);
                return;
            }
            
            var filtered = staffData.filter(function(s) {
                var fullName = (s.Ho + ' ' + s.Ten).toLowerCase();
                return fullName.includes(keyword) || 
                       s.Email.toLowerCase().includes(keyword) ||
                       (s.SDT && s.SDT.includes(keyword));
            });
            
            displayStaff(filtered);
        }
        
        // Open add modal
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Thêm Nhân viên';
            document.getElementById('staffForm').reset();
            document.getElementById('staffId').value = '';
            staffModal.show();
        }
        
        // View staff
        function viewStaff(id) {
            var staff = staffData.find(s => s.MaND == id);
            if (!staff) return;
            
            var statusText = staff.TrangThai == 1 ? 'Đang làm việc' : 'Đã nghỉ việc';
            
            var html = `
                <div class="text-center mb-4">
                    <div class="staff-avatar mx-auto" style="width: 80px; height: 80px; font-size: 28px;">
                        ${(staff.Ho.charAt(0) + staff.Ten.charAt(0)).toUpperCase()}
                    </div>
                    <h4 class="mt-3 mb-1">${staff.Ho} ${staff.Ten}</h4>
                    <p class="text-muted">${staff.Email}</p>
                    <span class="position-badge">Nhân viên</span>
                </div>
                
                <table class="table table-borderless">
                    <tr>
                        <td class="text-muted" width="40%">Mã nhân viên:</td>
                        <td><strong>${staff.MaND}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Giới tính:</td>
                        <td>${staff.GioiTinh || '-'}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Số điện thoại:</td>
                        <td>${staff.SDT || '-'}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Địa chỉ:</td>
                        <td>${staff.DiaChi || '-'}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tài khoản:</td>
                        <td>${staff.TaiKhoan || '-'}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Trạng thái:</td>
                        <td><span class="status-badge ${staff.TrangThai == 1 ? 'status-active' : 'status-inactive'}">${statusText}</span></td>
                    </tr>
                </table>
            `;
            
            document.getElementById('viewModalBody').innerHTML = html;
            viewModal.show();
        }
        
        // Edit staff
        function editStaff(id) {
            var staff = staffData.find(s => s.MaND == id);
            if (!staff) return;
            
            document.getElementById('modalTitle').textContent = 'Chỉnh sửa Nhân viên';
            document.getElementById('staffId').value = staff.MaND;
            document.getElementById('ho').value = staff.Ho;
            document.getElementById('ten').value = staff.Ten;
            document.getElementById('email').value = staff.Email;
            document.getElementById('sdt').value = staff.SDT || '';
            document.getElementById('gioitinh').value = staff.GioiTinh || 'Nam';
            document.getElementById('taikhoan').value = staff.TaiKhoan || '';
            document.getElementById('diachi').value = staff.DiaChi || '';
            document.getElementById('trangthai').value = staff.TrangThai;
            document.getElementById('matkhau').value = '';
            document.getElementById('matkhau2').value = '';
            
            staffModal.show();
        }
        
        // Save staff
        function saveStaff() {
            var id = document.getElementById('staffId').value;
            var ho = document.getElementById('ho').value.trim();
            var ten = document.getElementById('ten').value.trim();
            var email = document.getElementById('email').value.trim();
            var sdt = document.getElementById('sdt').value.trim();
            var gioitinh = document.getElementById('gioitinh').value;
            var taikhoan = document.getElementById('taikhoan').value.trim();
            var diachi = document.getElementById('diachi').value.trim();
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
                MaQuyen: 3, // Luôn là nhân viên
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
                        staffModal.hide();
                        Swal.fire('Thành công', response.message, 'success');
                        loadStaff();
                    } else {
                        Swal.fire('Lỗi', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Lỗi', 'Không thể kết nối server', 'error');
                }
            });
        }
        
        // Delete staff
        function deleteStaff(id) {
            var staff = staffData.find(s => s.MaND == id);
            if (!staff) return;
            
            Swal.fire({
                title: 'Xác nhận xóa?',
                html: `Bạn có chắc muốn xóa nhân viên <strong>${staff.Ho} ${staff.Ten}</strong>?`,
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
                                loadStaff();
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
            document.getElementById('staffTableBody').innerHTML = `
                <tr>
                    <td colspan="6">
                        <div class="no-staff">
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




