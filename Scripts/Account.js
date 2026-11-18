// ======================= MY ACCOUNT PAGE =======================

var currentUser = null;
var originalUserData = null;

window.onload = function() {
    khoiTao();
    loadUserAccount();
    initTabs();
    initForms();
    initPasswordValidation();
}

// ===== LOAD USER DATA =====
function loadUserAccount() {
    getCurrentUser(function(user) {
        if (user) {
            currentUser = user;
            originalUserData = {...user};
            displayUserProfile(user);
            loadUserStats();
            loadAddresses();
        } else {
            showNotLoggedInAccount();
        }
    }, function(error) {
        console.log('Error loading user:', error);
        showNotLoggedInAccount();
    });
}

// Display user profile in sidebar
function displayUserProfile(user) {
    var fullName = ((user.Ho || '') + ' ' + (user.Ten || '')).trim() || 'Khách hàng';
    var email = user.Email || user.email || '';
    
    document.getElementById('userFullName').textContent = fullName;
    document.getElementById('userEmail').textContent = email;
    
    // Fill form fields
    document.getElementById('inputHo').value = user.Ho || '';
    document.getElementById('inputTen').value = user.Ten || '';
    document.getElementById('inputGioiTinh').value = user.GioiTinh || '';
    document.getElementById('inputNgaySinh').value = user.NgaySinh || '';
    document.getElementById('inputEmail').value = user.Email || '';
    document.getElementById('inputSDT').value = user.SDT || '';
    document.getElementById('inputDiaChi').value = user.DiaChi || '';
    
    // Security info
    document.getElementById('securityUsername').textContent = user.TaiKhoan || '-';
    
    // Google status
    if (user.google_id) {
        document.getElementById('googleStatus').textContent = 'Đã liên kết';
        document.getElementById('googleBadge').className = 'security-status linked';
        document.getElementById('googleBadge').innerHTML = '<i class="fas fa-check-circle"></i> Đã liên kết';
    }
}

// Show not logged in message
function showNotLoggedInAccount() {
    document.querySelector('.account-content').innerHTML = `
        <div class="not-logged-in-account">
            <i class="fas fa-user-lock"></i>
            <h3>Bạn chưa đăng nhập</h3>
            <p>Vui lòng đăng nhập để quản lý tài khoản của bạn</p>
            <a href="SignIn.php" class="btn-login-account">
                <i class="fas fa-sign-in-alt"></i> Đăng nhập ngay
            </a>
        </div>
    `;
}

// ===== TAB NAVIGATION =====
function initTabs() {
    var navItems = document.querySelectorAll('.account-nav .nav-item[data-tab]');
    
    navItems.forEach(function(item) {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            var tabId = this.getAttribute('data-tab');
            switchTab(tabId, this);
        });
    });
}

function switchTab(tabId, navItem) {
    // Remove active class from all nav items
    document.querySelectorAll('.account-nav .nav-item').forEach(function(item) {
        item.classList.remove('active');
    });
    
    // Add active class to clicked nav item
    navItem.classList.add('active');
    
    // Hide all tab contents
    document.querySelectorAll('.tab-content-account').forEach(function(tab) {
        tab.classList.remove('active');
    });
    
    // Show selected tab
    var selectedTab = document.getElementById('tab-' + tabId);
    if (selectedTab) {
        selectedTab.classList.add('active');
        
        // Load orders when switching to orders tab
        if (tabId === 'orders') {
            loadMyOrders();
        }
    }
}

// ===== FORMS =====
function initForms() {
    // Profile form
    var profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveProfile();
        });
    }
    
    // Password form
    var passwordForm = document.getElementById('passwordForm');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            changePassword();
        });
    }
    
    // Address form
    var addressForm = document.getElementById('addressForm');
    if (addressForm) {
        addressForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveAddress();
        });
    }
}

// Save profile
function saveProfile() {
    var ho = document.getElementById('inputHo').value.trim();
    var ten = document.getElementById('inputTen').value.trim();
    var gioitinh = document.getElementById('inputGioiTinh').value;
    var ngaysinh = document.getElementById('inputNgaySinh').value;
    var email = document.getElementById('inputEmail').value.trim();
    var sdt = document.getElementById('inputSDT').value.trim();
    var diachi = document.getElementById('inputDiaChi').value.trim();
    
    // Validation
    if (!ten) {
        Swal.fire({
            icon: 'warning',
            title: 'Thiếu thông tin',
            text: 'Vui lòng nhập tên của bạn'
        });
        return;
    }
    
    if (!email) {
        Swal.fire({
            icon: 'warning',
            title: 'Thiếu thông tin',
            text: 'Vui lòng nhập email của bạn'
        });
        return;
    }
    
    // Validate email format
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        Swal.fire({
            icon: 'warning',
            title: 'Email không hợp lệ',
            text: 'Vui lòng nhập đúng định dạng email'
        });
        return;
    }
    
    // Validate phone number
    if (sdt && !/^[0-9]{10,11}$/.test(sdt)) {
        Swal.fire({
            icon: 'warning',
            title: 'Số điện thoại không hợp lệ',
            text: 'Số điện thoại phải có 10-11 chữ số'
        });
        return;
    }
    
    // Show loading
    Swal.fire({
        title: 'Đang cập nhật...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Send update request
    $.ajax({
        type: "POST",
        url: "Api/ProcessAccount.php",
        data: {
            request: 'updateProfile',
            MaND: currentUser.MaND,
            Ho: ho,
            Ten: ten,
            GioiTinh: gioitinh,
            NgaySinh: ngaysinh,
            Email: email,
            SDT: sdt,
            DiaChi: diachi
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Update local user data
                currentUser.Ho = ho;
                currentUser.Ten = ten;
                currentUser.GioiTinh = gioitinh;
                currentUser.NgaySinh = ngaysinh;
                currentUser.Email = email;
                currentUser.SDT = sdt;
                currentUser.DiaChi = diachi;
                originalUserData = {...currentUser};
                
                // Update display
                displayUserProfile(currentUser);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: 'Thông tin của bạn đã được cập nhật',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: response.message || 'Không thể cập nhật thông tin'
                });
            }
        },
        error: function(xhr, status, error) {
            console.log('Update Profile Error:', {
                xhr: xhr,
                status: status,
                error: error,
                responseText: xhr.responseText,
                currentUser: currentUser
            });
            Swal.fire({
                icon: 'error',
                title: 'Lỗi kết nối',
                text: 'Vui lòng thử lại sau',
                footer: '<small>Chi tiết: ' + (xhr.responseText || error) + '</small>'
            });
        }
    });
}

// Reset profile form
function resetProfileForm() {
    if (originalUserData) {
        document.getElementById('inputHo').value = originalUserData.Ho || '';
        document.getElementById('inputTen').value = originalUserData.Ten || '';
        document.getElementById('inputGioiTinh').value = originalUserData.GioiTinh || '';
        document.getElementById('inputNgaySinh').value = originalUserData.NgaySinh || '';
        document.getElementById('inputEmail').value = originalUserData.Email || '';
        document.getElementById('inputSDT').value = originalUserData.SDT || '';
        document.getElementById('inputDiaChi').value = originalUserData.DiaChi || '';
    }
}

// ===== PASSWORD =====
function initPasswordValidation() {
    var newPasswordInput = document.getElementById('newPassword');
    if (newPasswordInput) {
        newPasswordInput.addEventListener('input', function() {
            validatePasswordStrength(this.value);
        });
    }
}

function validatePasswordStrength(password) {
    var strength = 0;
    var reqLength = document.getElementById('req-length');
    var reqUpper = document.getElementById('req-upper');
    var reqLower = document.getElementById('req-lower');
    var reqNumber = document.getElementById('req-number');
    
    // Check length
    if (password.length >= 6) {
        strength += 25;
        reqLength.classList.add('valid');
    } else {
        reqLength.classList.remove('valid');
    }
    
    // Check uppercase
    if (/[A-Z]/.test(password)) {
        strength += 25;
        reqUpper.classList.add('valid');
    } else {
        reqUpper.classList.remove('valid');
    }
    
    // Check lowercase
    if (/[a-z]/.test(password)) {
        strength += 25;
        reqLower.classList.add('valid');
    } else {
        reqLower.classList.remove('valid');
    }
    
    // Check number
    if (/[0-9]/.test(password)) {
        strength += 25;
        reqNumber.classList.add('valid');
    } else {
        reqNumber.classList.remove('valid');
    }
    
    // Update strength bar
    var strengthBar = document.querySelector('.strength-bar');
    var strengthText = document.querySelector('.strength-text');
    
    var color, text;
    if (strength <= 25) {
        color = '#e74c3c';
        text = 'Yếu';
    } else if (strength <= 50) {
        color = '#f39c12';
        text = 'Trung bình';
    } else if (strength <= 75) {
        color = '#3498db';
        text = 'Khá';
    } else {
        color = '#27ae60';
        text = 'Mạnh';
    }
    
    strengthBar.style.setProperty('--strength', strength + '%');
    strengthBar.style.setProperty('--color', color);
    strengthText.textContent = text;
    strengthText.style.setProperty('--color', color);
    strengthText.style.color = color;
}

function togglePassword(inputId) {
    var input = document.getElementById(inputId);
    var icon = input.parentElement.querySelector('.toggle-password i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function changePassword() {
    var currentPass = document.getElementById('currentPassword').value;
    var newPass = document.getElementById('newPassword').value;
    var confirmPass = document.getElementById('confirmPassword').value;
    
    // Validation
    if (!currentPass) {
        Swal.fire({
            icon: 'warning',
            title: 'Thiếu thông tin',
            text: 'Vui lòng nhập mật khẩu hiện tại'
        });
        return;
    }
    
    if (!newPass) {
        Swal.fire({
            icon: 'warning',
            title: 'Thiếu thông tin',
            text: 'Vui lòng nhập mật khẩu mới'
        });
        return;
    }
    
    if (newPass.length < 6) {
        Swal.fire({
            icon: 'warning',
            title: 'Mật khẩu yếu',
            text: 'Mật khẩu phải có ít nhất 6 ký tự'
        });
        return;
    }
    
    if (newPass !== confirmPass) {
        Swal.fire({
            icon: 'warning',
            title: 'Không khớp',
            text: 'Mật khẩu xác nhận không khớp'
        });
        return;
    }
    
    // Show loading
    Swal.fire({
        title: 'Đang cập nhật...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Send request
    $.ajax({
        type: "POST",
        url: "Api/ProcessAccount.php",
        data: {
            request: 'changePassword',
            MaND: currentUser.MaND,
            currentPassword: currentPass,
            newPassword: newPass
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: 'Mật khẩu của bạn đã được cập nhật',
                    timer: 2000,
                    showConfirmButton: false
                });
                
                // Clear form
                document.getElementById('currentPassword').value = '';
                document.getElementById('newPassword').value = '';
                document.getElementById('confirmPassword').value = '';
                
                // Reset validation UI
                document.querySelectorAll('.password-requirements li').forEach(function(li) {
                    li.classList.remove('valid');
                });
                document.querySelector('.strength-bar').style.setProperty('--strength', '0%');
                document.querySelector('.strength-text').textContent = '';
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: response.message || 'Mật khẩu hiện tại không đúng'
                });
            }
        },
        error: function(e) {
            console.log('Error:', e);
            Swal.fire({
                icon: 'error',
                title: 'Lỗi kết nối',
                text: 'Vui lòng thử lại sau'
            });
        }
    });
}

// ===== USER STATS =====
function loadUserStats() {
    $.ajax({
        type: "GET",
        url: "Api/ProcessOrder.php",
        data: { request: 'getmyorders' },
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data) {
                var orders = response.data;
                var totalOrders = orders.length;
                var totalSpent = orders.reduce(function(sum, order) {
                    return sum + parseInt(order.TongTien || 0);
                }, 0);
                
                document.getElementById('statOrders').textContent = totalOrders;
                document.getElementById('statSpent').textContent = formatPrice(totalSpent);
            }
        },
        error: function(e) {
            console.log('Error loading stats:', e);
        }
    });
}

// ===== ADDRESSES =====
var userAddresses = [];

function loadAddresses() {
    // For now, just use the main address from user profile
    var addressesList = document.getElementById('addressesList');
    
    if (currentUser && currentUser.DiaChi) {
        userAddresses = [{
            id: 1,
            name: (currentUser.Ho || '') + ' ' + (currentUser.Ten || ''),
            phone: currentUser.SDT || '',
            address: currentUser.DiaChi,
            isDefault: true
        }];
        
        displayAddresses();
    } else {
        addressesList.innerHTML = `
            <div class="empty-addresses">
                <i class="fas fa-map-marker-alt"></i>
                <h3>Chưa có địa chỉ nào</h3>
                <p>Thêm địa chỉ để đặt hàng nhanh hơn</p>
            </div>
        `;
    }
}

function displayAddresses() {
    var addressesList = document.getElementById('addressesList');
    
    if (userAddresses.length === 0) {
        addressesList.innerHTML = `
            <div class="empty-addresses">
                <i class="fas fa-map-marker-alt"></i>
                <h3>Chưa có địa chỉ nào</h3>
                <p>Thêm địa chỉ để đặt hàng nhanh hơn</p>
            </div>
        `;
        return;
    }
    
    var html = '';
    userAddresses.forEach(function(addr) {
        html += `
            <div class="address-card ${addr.isDefault ? 'default' : ''}">
                <div class="address-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="address-info">
                    <h4>
                        ${addr.name}
                        ${addr.isDefault ? '<span class="default-badge">Mặc định</span>' : ''}
                    </h4>
                    <div class="phone"><i class="fas fa-phone"></i> ${addr.phone || 'Chưa có'}</div>
                    <div class="address-text">${addr.address}</div>
                </div>
                <div class="address-actions">
                    <button onclick="editAddress(${addr.id})" title="Sửa">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-delete" onclick="deleteAddress(${addr.id})" title="Xóa">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
    });
    
    addressesList.innerHTML = html;
}

function showAddAddressModal() {
    document.getElementById('addressModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeAddressModal() {
    document.getElementById('addressModal').classList.remove('active');
    document.body.style.overflow = '';
    
    // Clear form
    document.getElementById('addrName').value = '';
    document.getElementById('addrPhone').value = '';
    document.getElementById('addrAddress').value = '';
    document.getElementById('addrDefault').checked = false;
}

function saveAddress() {
    var name = document.getElementById('addrName').value.trim();
    var phone = document.getElementById('addrPhone').value.trim();
    var address = document.getElementById('addrAddress').value.trim();
    var isDefault = document.getElementById('addrDefault').checked;
    
    if (!name || !address) {
        Swal.fire({
            icon: 'warning',
            title: 'Thiếu thông tin',
            text: 'Vui lòng nhập đầy đủ thông tin'
        });
        return;
    }
    
    // Add to local array (in real app, save to server)
    var newAddress = {
        id: userAddresses.length + 1,
        name: name,
        phone: phone,
        address: address,
        isDefault: isDefault
    };
    
    if (isDefault) {
        userAddresses.forEach(function(addr) {
            addr.isDefault = false;
        });
    }
    
    userAddresses.push(newAddress);
    displayAddresses();
    closeAddressModal();
    
    Swal.fire({
        icon: 'success',
        title: 'Thành công!',
        text: 'Đã thêm địa chỉ mới',
        timer: 1500,
        showConfirmButton: false
    });
}

function editAddress(id) {
    // Navigate to profile tab and focus on address field
    var profileNavItem = document.querySelector('.account-nav .nav-item[data-tab="profile"]');
    switchTab('profile', profileNavItem);
    
    setTimeout(function() {
        document.getElementById('inputDiaChi').focus();
    }, 300);
}

function deleteAddress(id) {
    Swal.fire({
        title: 'Xác nhận xóa?',
        text: 'Bạn có chắc muốn xóa địa chỉ này?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74c3c',
        cancelButtonColor: '#888',
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            userAddresses = userAddresses.filter(function(addr) {
                return addr.id !== id;
            });
            displayAddresses();
            
            Swal.fire({
                icon: 'success',
                title: 'Đã xóa',
                timer: 1500,
                showConfirmButton: false
            });
        }
    });
}

// ===== LOGOUT =====
function logoutAccount() {
    Swal.fire({
        title: 'Đăng xuất?',
        text: 'Bạn có chắc muốn đăng xuất?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#1a1a2e',
        cancelButtonColor: '#888',
        confirmButtonText: 'Đăng xuất',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: "Api/ProcessAccount.php",
                data: { request: 'dangxuat' },
                success: function(response) {
                    window.location.href = 'Index.php';
                }
            });
        }
    });
}

// ===== HELPER FUNCTIONS =====
function formatPrice(price) {
    return parseInt(price).toLocaleString('vi-VN') + 'đ';
}

// Close modal on outside click
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        closeAddressModal();
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAddressModal();
        closeOrderDetailModal();
    }
});

// ======================= ORDERS MANAGEMENT =======================

var allOrders = [];
var currentOrderFilter = 'all';

// Load orders
function loadMyOrders() {
    var ordersList = document.getElementById('ordersList');
    if (!ordersList) return;

    ordersList.innerHTML = `
        <div class="loading-orders">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Đang tải đơn hàng...</p>
        </div>
    `;

    $.ajax({
        url: 'Api/ProcessAccount.php',
        type: 'GET',
        data: { request: 'getMyOrders' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                allOrders = response.data;
                renderOrders(allOrders);
                updateOrdersBadge(allOrders);
                initOrderFilters();
            } else {
                ordersList.innerHTML = `
                    <div class="empty-orders">
                        <i class="fas fa-shopping-bag"></i>
                        <h3>Không có đơn hàng nào</h3>
                        <p>Bạn chưa có đơn hàng nào. Hãy mua sắm ngay!</p>
                        <a href="Index.php" class="btn-shop-now">Mua sắm ngay</a>
                    </div>
                `;
            }
        },
        error: function() {
            ordersList.innerHTML = `
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Không thể tải đơn hàng. Vui lòng thử lại.</p>
                </div>
            `;
        }
    });
}

// Render orders list
function renderOrders(orders) {
    var ordersList = document.getElementById('ordersList');
    if (!ordersList) return;

    if (!orders || orders.length === 0) {
        ordersList.innerHTML = `
            <div class="empty-orders">
                <i class="fas fa-shopping-bag"></i>
                <h3>Không có đơn hàng nào</h3>
                <p>Bạn chưa có đơn hàng nào trong mục này</p>
            </div>
        `;
        return;
    }

    var html = '';
    orders.forEach(function(order) {
        var productImage = order.HinhAnhDau || 'img/products/default.png';
        
        html += `
            <div class="order-card" data-status="${order.TrangThaiClass}">
                <div class="order-header">
                    <div class="order-info">
                        <span class="order-id">Đơn hàng #${order.MaHD}</span>
                        <span class="order-date">${order.NgayLapFormatted}</span>
                    </div>
                    <span class="order-status status-${order.TrangThaiClass}">${order.TrangThaiText}</span>
                </div>
                <div class="order-body">
                    <div class="order-product-preview">
                        <img src="${productImage}" alt="Product" onerror="this.src='img/products/default.png'">
                        <div class="order-product-info">
                            <span class="product-count">${order.SoSanPham} sản phẩm</span>
                            <span class="order-receiver">${order.NguoiNhan}</span>
                        </div>
                    </div>
                    <div class="order-total">
                        <span class="total-label">Tổng tiền</span>
                        <span class="total-value">${order.TongTienFormatted}</span>
                    </div>
                </div>
                <div class="order-footer">
                    <div class="order-payment">
                        <i class="fas fa-${order.PhuongThucTT === 'momo' ? 'wallet' : 'money-bill-wave'}"></i>
                        <span>${order.PhuongThucTT === 'momo' ? 'MoMo' : 'COD'}</span>
                        ${order.DaThanhToan == 1 ? '<span class="paid-badge">Đã thanh toán</span>' : '<span class="unpaid-badge">Chưa thanh toán</span>'}
                    </div>
                    <div class="order-actions">
                        <button class="btn-order-detail" onclick="viewOrderDetail(${order.MaHD})">
                            <i class="fas fa-eye"></i> Chi tiết
                        </button>
                        ${order.TrangThai == 1 ? `
                            <button class="btn-cancel-order" onclick="cancelOrder(${order.MaHD})">
                                <i class="fas fa-times"></i> Hủy đơn
                            </button>
                        ` : ''}
                        ${order.TrangThai == 5 ? `
                            <button class="btn-reorder" onclick="reorder(${order.MaHD})">
                                <i class="fas fa-redo"></i> Mua lại
                            </button>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    });

    ordersList.innerHTML = html;
}

// Update orders badge
function updateOrdersBadge(orders) {
    var badge = document.getElementById('ordersBadge');
    if (badge) {
        var pendingCount = orders.filter(o => o.TrangThai == 1 || o.TrangThai == 2 || o.TrangThai == 3 || o.TrangThai == 4).length;
        if (pendingCount > 0) {
            badge.textContent = pendingCount;
            badge.style.display = 'inline-flex';
        } else {
            badge.style.display = 'none';
        }
    }
}

// Init order filters
function initOrderFilters() {
    var filterBtns = document.querySelectorAll('.filter-btn');
    filterBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            var filter = this.getAttribute('data-filter');
            filterOrders(filter);
        });
    });
}

// Filter orders
function filterOrders(filter) {
    currentOrderFilter = filter;
    var filtered = [];

    if (filter === 'all') {
        filtered = allOrders;
    } else if (filter === 'pending') {
        filtered = allOrders.filter(o => o.TrangThai == 1);
    } else if (filter === 'processing') {
        filtered = allOrders.filter(o => o.TrangThai == 2 || o.TrangThai == 3);
    } else if (filter === 'shipping') {
        filtered = allOrders.filter(o => o.TrangThai == 4);
    } else if (filter === 'completed') {
        filtered = allOrders.filter(o => o.TrangThai == 5);
    } else if (filter === 'cancelled') {
        filtered = allOrders.filter(o => o.TrangThai == 0);
    }

    renderOrders(filtered);
}

// View order detail
function viewOrderDetail(maHD) {
    $.ajax({
        url: 'Api/ProcessAccount.php',
        type: 'GET',
        data: { request: 'getOrderDetail', MaHD: maHD },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showOrderDetailModal(response.order, response.products);
            } else {
                Swal.fire('Lỗi', response.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Lỗi', 'Không thể tải chi tiết đơn hàng', 'error');
        }
    });
}

// Show order detail modal
function showOrderDetailModal(order, products) {
    var productsHtml = '';
    products.forEach(function(p) {
        productsHtml += `
            <div class="detail-product-item">
                <img src="${p.HinhAnh || 'img/products/default.png'}" alt="${p.TenSP}" onerror="this.src='img/products/default.png'">
                <div class="detail-product-info">
                    <h4>${p.TenSP}</h4>
                    <div class="detail-product-meta">
                        <span class="quantity">x${p.SoLuong}</span>
                        <span class="price">${p.DonGiaFormatted}</span>
                    </div>
                </div>
                <div class="detail-product-total">${p.ThanhTienFormatted}</div>
            </div>
        `;
    });

    var modalHtml = `
        <div class="modal-overlay order-detail-modal" id="orderDetailModal">
            <div class="modal-content modal-lg">
                <div class="modal-header-account">
                    <h3><i class="fas fa-receipt"></i> Chi tiết đơn hàng #${order.MaHD}</h3>
                    <button class="modal-close" onclick="closeOrderDetailModal()">&times;</button>
                </div>
                <div class="modal-body-account">
                    <div class="order-detail-status">
                        <span class="status-badge status-${order.TrangThaiClass}">${order.TrangThaiText}</span>
                        <span class="order-detail-date">${order.NgayLapFormatted}</span>
                    </div>

                    <div class="order-detail-section">
                        <h4><i class="fas fa-user"></i> Thông tin người nhận</h4>
                        <div class="detail-info-grid">
                            <div class="detail-info-item">
                                <label>Người nhận:</label>
                                <span>${order.NguoiNhan}</span>
                            </div>
                            <div class="detail-info-item">
                                <label>Số điện thoại:</label>
                                <span>${order.SDT}</span>
                            </div>
                            <div class="detail-info-item full-width">
                                <label>Địa chỉ:</label>
                                <span>${order.DiaChi}</span>
                            </div>
                        </div>
                    </div>

                    <div class="order-detail-section">
                        <h4><i class="fas fa-box"></i> Sản phẩm đã đặt</h4>
                        <div class="detail-products-list">
                            ${productsHtml}
                        </div>
                    </div>

                    <div class="order-detail-summary">
                        <div class="summary-row">
                            <span>Tạm tính:</span>
                            <span>${order.TongTienFormatted}</span>
                        </div>
                        <div class="summary-row">
                            <span>Phí vận chuyển:</span>
                            <span>Miễn phí</span>
                        </div>
                        <div class="summary-row total">
                            <span>Tổng cộng:</span>
                            <span>${order.TongTienFormatted}</span>
                        </div>
                        <div class="summary-row payment-method">
                            <span>Phương thức thanh toán:</span>
                            <span>
                                <i class="fas fa-${order.PhuongThucTT === 'momo' ? 'wallet' : 'money-bill-wave'}"></i>
                                ${order.PhuongThucTT === 'momo' ? 'Ví MoMo' : 'Thanh toán khi nhận hàng (COD)'}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer-account">
                    <button class="btn-secondary-account" onclick="closeOrderDetailModal()">Đóng</button>
                    ${order.TrangThai == 5 ? `
                        <button class="btn-primary-account" onclick="reorder(${order.MaHD}); closeOrderDetailModal();">
                            <i class="fas fa-redo"></i> Mua lại
                        </button>
                    ` : ''}
                </div>
            </div>
        </div>
    `;

    // Remove existing modal
    var existingModal = document.getElementById('orderDetailModal');
    if (existingModal) existingModal.remove();

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    setTimeout(function() {
        document.getElementById('orderDetailModal').classList.add('show');
    }, 10);
}

// Close order detail modal
function closeOrderDetailModal() {
    var modal = document.getElementById('orderDetailModal');
    if (modal) {
        modal.classList.remove('show');
        setTimeout(function() {
            modal.remove();
        }, 300);
    }
}

// Cancel order
function cancelOrder(maHD) {
    Swal.fire({
        title: 'Xác nhận hủy đơn?',
        text: 'Bạn có chắc muốn hủy đơn hàng này?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74c3c',
        cancelButtonColor: '#888',
        confirmButtonText: 'Hủy đơn',
        cancelButtonText: 'Không'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'Api/ProcessOrderAdmin.php',
                type: 'POST',
                data: { request: 'updatestatus', MaHD: maHD, TrangThai: 0 },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Đã hủy!', 'Đơn hàng đã được hủy.', 'success');
                        loadMyOrders();
                    } else {
                        Swal.fire('Lỗi', response.message || 'Không thể hủy đơn hàng', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Lỗi', 'Không thể hủy đơn hàng', 'error');
                }
            });
        }
    });
}

// Reorder
function reorder(maHD) {
    Swal.fire({
        title: 'Mua lại đơn hàng?',
        text: 'Các sản phẩm sẽ được thêm vào giỏ hàng',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Thêm vào giỏ',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            // TODO: Implement reorder logic
            Swal.fire('Thông báo', 'Tính năng đang được phát triển', 'info');
        }
    });
}


