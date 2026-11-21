// ======================= WARRANTY PAGE JS =======================

var currentUser = null;
var myProducts = [];
var currentStep = 1;
var selectedProduct = null;

window.onload = function() {
    khoiTao();
    initWarrantyTabs();
    initDatePickers();
    initTrackOptions();
    initBookingForm();
    loadUserAndProducts();
    
    // Load repair history on page load (default tab is history)
    loadRepairHistory();
}

// Initialize tabs
function initWarrantyTabs() {
    var tabLinks = document.querySelectorAll('.warranty-tab-link');
    tabLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            var tabName = this.getAttribute('data-tab');
            switchWarrantyTab(tabName);
        });
    });
    
    // Check URL hash for tab
    var hash = window.location.hash.replace('#', '');
    if (hash && ['history', 'products', 'booking'].includes(hash)) {
        switchWarrantyTab(hash);
    }
}

// Switch tab
function switchWarrantyTab(tabName) {
    // Update links
    document.querySelectorAll('.warranty-tab-link').forEach(function(link) {
        link.classList.remove('active');
        if (link.getAttribute('data-tab') === tabName) {
            link.classList.add('active');
        }
    });
    
    // Update content
    document.querySelectorAll('.warranty-tab-content').forEach(function(content) {
        content.classList.remove('active');
    });
    var targetTab = document.getElementById('tab-' + tabName);
    if (targetTab) {
        targetTab.classList.add('active');
    }
    
    // Update URL hash
    history.replaceState(null, null, '#' + tabName);
    
    // Load data if needed
    if (tabName === 'products' && myProducts.length === 0) {
        loadMyProducts();
    }
    
    // Load repair history when switching to history tab
    if (tabName === 'history') {
        loadRepairHistory();
    }
}

// Initialize date pickers
function initDatePickers() {
    var today = new Date();
    var threeYearsAgo = new Date();
    threeYearsAgo.setFullYear(today.getFullYear() - 3);
    
    var dateFrom = document.getElementById('dateFrom');
    var dateTo = document.getElementById('dateTo');
    
    if (dateFrom) {
        dateFrom.value = formatDateForInput(threeYearsAgo);
    }
    if (dateTo) {
        dateTo.value = formatDateForInput(today);
    }
    
    // Set min date for booking
    var bookingDate = document.getElementById('bookingDate');
    if (bookingDate) {
        var tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        bookingDate.min = formatDateForInput(tomorrow);
    }
}

// Format date for input
function formatDateForInput(date) {
    var year = date.getFullYear();
    var month = String(date.getMonth() + 1).padStart(2, '0');
    var day = String(date.getDate()).padStart(2, '0');
    return year + '-' + month + '-' + day;
}

// Initialize track options
function initTrackOptions() {
    var trackBtns = document.querySelectorAll('.track-btn');
    var trackInput = document.getElementById('trackInput');
    
    trackBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            trackBtns.forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');
            
            var type = this.getAttribute('data-type');
            if (trackInput) {
                if (type === 'email') {
                    trackInput.type = 'email';
                    trackInput.placeholder = 'Nhập email của bạn';
                } else {
                    trackInput.type = 'tel';
                    trackInput.placeholder = 'Nhập số điện thoại của bạn';
                }
            }
        });
    });
}

// Search repair by contact
function searchRepairByContact() {
    var trackInput = document.getElementById('trackInput');
    if (!trackInput || !trackInput.value.trim()) {
        Swal.fire('Thông báo', 'Vui lòng nhập email hoặc số điện thoại', 'warning');
        return;
    }
    
    // TODO: Call API to search repair history
    Swal.fire('Thông báo', 'Không tìm thấy lịch sử sửa chữa với thông tin này', 'info');
}

// Initialize booking form
function initBookingForm() {
    var form = document.getElementById('repairBookingForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            submitRepairBooking();
        });
    }
    
    // Product select items click
    document.addEventListener('click', function(e) {
        if (e.target.closest('.product-select-item')) {
            var item = e.target.closest('.product-select-item');
            selectProduct(item);
        }
    });
}

// Select product
function selectProduct(item) {
    document.querySelectorAll('.product-select-item').forEach(function(p) {
        p.classList.remove('selected');
    });
    item.classList.add('selected');
    selectedProduct = item.getAttribute('data-product-id');
}

// Load user and products
function loadUserAndProducts() {
    getCurrentUser(function(user) {
        if (user) {
            currentUser = user;
            prefillContactInfo(user);
            loadMyProducts();
        } else {
            showNotLoggedIn();
        }
    }, function(error) {
        console.log('Error loading user:', error);
        showNotLoggedIn();
    });
}

// Prefill contact info
function prefillContactInfo(user) {
    var hovaten = ((user.Ho || '') + ' ' + (user.Ten || '')).trim();
    
    var nameInput = document.getElementById('contactName');
    var phoneInput = document.getElementById('contactPhone');
    var emailInput = document.getElementById('contactEmail');
    
    if (nameInput && hovaten) nameInput.value = hovaten;
    if (phoneInput && user.SDT) phoneInput.value = user.SDT;
    if (emailInput && user.Email) emailInput.value = user.Email;
}

// Show not logged in
function showNotLoggedIn() {
    var productsEmpty = document.getElementById('productsEmpty');
    var productsList = document.getElementById('myProductsList');
    
    if (productsList) productsList.innerHTML = '';
    if (productsEmpty) {
        productsEmpty.style.display = 'block';
        productsEmpty.innerHTML = `
            <div class="empty-icon">
                <i class="fa fa-user-times"></i>
            </div>
            <h3>Bạn chưa đăng nhập</h3>
            <p>Vui lòng đăng nhập để xem sản phẩm và đặt lịch bảo hành</p>
            <a href="SignIn.php" class="btn-shop-now">Đăng nhập ngay</a>
        `;
    }
}

// Load my products (from completed orders)
function loadMyProducts() {
    var productsList = document.getElementById('myProductsList');
    var productsEmpty = document.getElementById('productsEmpty');
    var productSelectList = document.getElementById('productSelectList');
    
    if (productsList) {
        productsList.innerHTML = `
            <div class="loading-state">
                <i class="fa fa-spinner fa-spin"></i>
                <span>Đang tải danh sách sản phẩm...</span>
            </div>
        `;
    }
    
    $.ajax({
        type: "GET",
        url: "Api/ProcessWarranty.php",
        data: { request: 'getmyproducts' },
        dataType: 'json',
        success: function(response) {
            console.log('Products response:', response);
            if (response.success && response.data && response.data.length > 0) {
                myProducts = response.data;
                displayMyProducts(response.data);
                displayProductSelectList(response.data);
                if (productsEmpty) productsEmpty.style.display = 'none';
            } else {
                if (productsList) productsList.innerHTML = '';
                if (productsEmpty) productsEmpty.style.display = 'block';
                if (productSelectList) {
                    productSelectList.innerHTML = `
                        <div class="empty-state">
                            <p>Bạn chưa có sản phẩm nào. Hãy mua sắm để được hỗ trợ bảo hành!</p>
                        </div>
                    `;
                }
            }
        },
        error: function(e) {
            console.log('Error loading products:', e);
            if (productsList) productsList.innerHTML = '';
            if (productsEmpty) productsEmpty.style.display = 'block';
        }
    });
}

// Display my products
function displayMyProducts(products) {
    var productsList = document.getElementById('myProductsList');
    if (!productsList) return;
    
    var html = '';
    products.forEach(function(product) {
        var warrantyStatus = getWarrantyStatus(product.NgayMua, product.ThoiGianBaoHanh || 12);
        
        html += `
            <div class="product-warranty-card">
                <div class="product-warranty-image">
                    <img src="${product.HinhAnh || 'img/products/default.png'}" alt="${product.TenSP}">
                </div>
                <div class="product-warranty-info">
                    <div class="product-warranty-name">${product.TenSP}</div>
                    <div class="product-warranty-details">Ngày mua: ${formatDate(product.NgayMua)}</div>
                    <div class="product-warranty-details">Mã đơn hàng: #${product.MaDH}</div>
                    <div class="product-warranty-details">Thời hạn BH: ${product.ThoiGianBaoHanh || 12} tháng</div>
                    <span class="warranty-status ${warrantyStatus.class}">
                        <i class="fa ${warrantyStatus.icon}"></i>
                        ${warrantyStatus.text}
                    </span>
                </div>
                <div class="product-warranty-actions">
                    <button class="btn-repair-request" onclick="requestRepair('${product.MaSP}', '${product.TenSP}')">
                        Yêu cầu sửa chữa
                    </button>
                    <button class="btn-warranty-detail" onclick="viewWarrantyDetail('${product.MaSP}')">
                        Chi tiết bảo hành
                    </button>
                </div>
            </div>
        `;
    });
    
    productsList.innerHTML = html;
}

// Display product select list for booking
function displayProductSelectList(products) {
    var productSelectList = document.getElementById('productSelectList');
    if (!productSelectList) return;
    
    var html = '';
    products.forEach(function(product, index) {
        var warrantyStatus = getWarrantyStatus(product.NgayMua, product.ThoiGianBaoHanh || 12);
        
        html += `
            <label class="product-select-item ${index === 0 ? 'selected' : ''}" data-product-id="${product.MaSP}">
                <input type="radio" name="selectedProduct" value="${product.MaSP}" ${index === 0 ? 'checked' : ''}>
                <div class="product-select-check"></div>
                <div class="product-select-image">
                    <img src="${product.HinhAnh || 'img/products/default.png'}" alt="${product.TenSP}">
                </div>
                <div class="product-select-info">
                    <div class="product-select-name">${product.TenSP}</div>
                    <div class="product-select-warranty">Bảo hành: ${warrantyStatus.text} | Mua: ${formatDate(product.NgayMua)}</div>
                </div>
            </label>
        `;
    });
    
    productSelectList.innerHTML = html;
    
    // Auto select first product
    if (products.length > 0) {
        selectedProduct = products[0].MaSP;
    }
}

// Get warranty status
function getWarrantyStatus(purchaseDate, warrantyMonths) {
    var purchase = new Date(purchaseDate);
    var expiryDate = new Date(purchase);
    expiryDate.setMonth(expiryDate.getMonth() + warrantyMonths);
    
    var today = new Date();
    var daysLeft = Math.ceil((expiryDate - today) / (1000 * 60 * 60 * 24));
    
    if (daysLeft < 0) {
        return {
            class: 'expired',
            icon: 'fa-times-circle',
            text: 'Hết hạn bảo hành'
        };
    } else if (daysLeft <= 30) {
        return {
            class: 'expiring-soon',
            icon: 'fa-exclamation-circle',
            text: 'Sắp hết hạn (' + daysLeft + ' ngày)'
        };
    } else {
        return {
            class: 'active',
            icon: 'fa-check-circle',
            text: 'Còn bảo hành (đến ' + formatDate(expiryDate.toISOString()) + ')'
        };
    }
}

// Format date
function formatDate(dateString) {
    if (!dateString) return '--';
    var date = new Date(dateString);
    return date.toLocaleDateString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

// Request repair
function requestRepair(productId, productName) {
    selectedProduct = productId;
    switchWarrantyTab('booking');
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
    
    // Select the product in list
    setTimeout(function() {
        var productItem = document.querySelector('.product-select-item[data-product-id="' + productId + '"]');
        if (productItem) {
            selectProduct(productItem);
        }
    }, 100);
}

// View warranty detail
function viewWarrantyDetail(productId) {
    var product = myProducts.find(function(p) { return p.MaSP == productId; });
    if (!product) return;
    
    var warrantyStatus = getWarrantyStatus(product.NgayMua, product.ThoiGianBaoHanh || 12);
    var purchase = new Date(product.NgayMua);
    var expiryDate = new Date(purchase);
    expiryDate.setMonth(expiryDate.getMonth() + (product.ThoiGianBaoHanh || 12));
    
    Swal.fire({
        title: 'Chi tiết bảo hành',
        html: `
            <div style="text-align: left; padding: 10px;">
                <p><strong>Sản phẩm:</strong> ${product.TenSP}</p>
                <p><strong>Mã sản phẩm:</strong> ${product.MaSP}</p>
                <p><strong>Ngày mua:</strong> ${formatDate(product.NgayMua)}</p>
                <p><strong>Thời hạn bảo hành:</strong> ${product.ThoiGianBaoHanh || 12} tháng</p>
                <p><strong>Ngày hết hạn:</strong> ${formatDate(expiryDate.toISOString())}</p>
                <p><strong>Trạng thái:</strong> <span style="color: ${warrantyStatus.class === 'active' ? '#2e7d32' : '#c62828'}">${warrantyStatus.text}</span></p>
                <hr>
                <p style="font-size: 12px; color: #666;">
                    * Bảo hành áp dụng cho các lỗi do nhà sản xuất.<br>
                    * Không bảo hành các trường hợp: rơi vỡ, vào nước, tự ý sửa chữa.
                </p>
            </div>
        `,
        confirmButtonText: 'Đóng',
        confirmButtonColor: '#1428a0'
    });
}

// Next step
function nextStep() {
    // Validate current step
    if (!validateStep(currentStep)) {
        return;
    }
    
    if (currentStep < 4) {
        document.getElementById('step' + currentStep).classList.remove('active');
        currentStep++;
        document.getElementById('step' + currentStep).classList.add('active');
        
        updateFormButtons();
    }
}

// Previous step
function prevStep() {
    if (currentStep > 1) {
        document.getElementById('step' + currentStep).classList.remove('active');
        currentStep--;
        document.getElementById('step' + currentStep).classList.add('active');
        
        updateFormButtons();
    }
}

// Update form buttons
function updateFormButtons() {
    var btnPrev = document.getElementById('btnPrev');
    var btnNext = document.getElementById('btnNext');
    var btnSubmit = document.getElementById('btnSubmit');
    
    if (btnPrev) btnPrev.style.display = currentStep > 1 ? 'flex' : 'none';
    if (btnNext) btnNext.style.display = currentStep < 4 ? 'flex' : 'none';
    if (btnSubmit) btnSubmit.style.display = currentStep === 4 ? 'flex' : 'none';
}

// Validate step
function validateStep(step) {
    switch(step) {
        case 1:
            if (!selectedProduct) {
                Swal.fire('Thông báo', 'Vui lòng chọn sản phẩm cần sửa chữa', 'warning');
                return false;
            }
            return true;
            
        case 2:
            var issueType = document.querySelector('input[name="issueType"]:checked');
            if (!issueType) {
                Swal.fire('Thông báo', 'Vui lòng chọn loại vấn đề', 'warning');
                return false;
            }
            return true;
            
        case 3:
            var bookingDate = document.getElementById('bookingDate').value;
            var bookingTime = document.getElementById('bookingTime').value;
            if (!bookingDate || !bookingTime) {
                Swal.fire('Thông báo', 'Vui lòng chọn ngày và giờ hẹn', 'warning');
                return false;
            }
            return true;
            
        case 4:
            var name = document.getElementById('contactName').value.trim();
            var phone = document.getElementById('contactPhone').value.trim();
            if (!name || !phone) {
                Swal.fire('Thông báo', 'Vui lòng nhập họ tên và số điện thoại', 'warning');
                return false;
            }
            return true;
    }
    return true;
}

// Submit repair booking
function submitRepairBooking() {
    if (!validateStep(4)) return;
    
    // Get selected product info
    var selectedProductItem = document.querySelector('.product-select-item.selected');
    var productName = '';
    var productImage = '';
    if (selectedProductItem) {
        var nameEl = selectedProductItem.querySelector('.product-select-name');
        var imgEl = selectedProductItem.querySelector('.product-select-image img');
        if (nameEl) productName = nameEl.textContent;
        if (imgEl) productImage = imgEl.src;
    }
    
    var formData = {
        request: 'createbooking',
        productId: selectedProduct || '',
        productName: productName,
        productImage: productImage,
        issueType: document.querySelector('input[name="issueType"]:checked').value,
        issueDescription: document.getElementById('issueDescription').value,
        bookingDate: document.getElementById('bookingDate').value,
        bookingTime: document.getElementById('bookingTime').value,
        contactName: document.getElementById('contactName').value,
        contactPhone: document.getElementById('contactPhone').value,
        contactEmail: document.getElementById('contactEmail').value,
        contactNote: document.getElementById('contactNote').value
    };
    
    $.ajax({
        type: "POST",
        url: "Api/ProcessWarranty.php",
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Đặt lịch thành công!',
                    html: `
                        <p>Mã phiếu sửa chữa: <strong>#${response.bookingId}</strong></p>
                        <p>Chúng tôi sẽ liên hệ xác nhận trong 24h.</p>
                    `,
                    confirmButtonText: 'Xem lịch sử',
                    confirmButtonColor: '#1428a0'
                }).then(function() {
                    // Reset form and go to history tab
                    resetBookingForm();
                    switchWarrantyTab('history');
                    loadRepairHistory();
                });
            } else {
                Swal.fire('Lỗi', response.message || 'Có lỗi xảy ra', 'error');
            }
        },
        error: function(e) {
            console.log('Error:', e);
            Swal.fire('Lỗi', 'Không thể kết nối server', 'error');
        }
    });
}

// Reset booking form
function resetBookingForm() {
    currentStep = 1;
    selectedProduct = null;
    
    document.querySelectorAll('.form-step').forEach(function(step, index) {
        step.classList.remove('active');
        if (index === 0) step.classList.add('active');
    });
    
    document.querySelectorAll('input[name="issueType"]').forEach(function(input) {
        input.checked = false;
    });
    
    document.getElementById('issueDescription').value = '';
    document.getElementById('bookingDate').value = '';
    document.getElementById('bookingTime').value = '';
    document.getElementById('contactNote').value = '';
    
    updateFormButtons();
}

// Load repair history
function loadRepairHistory() {
    var dateFrom = document.getElementById('dateFrom').value;
    var dateTo = document.getElementById('dateTo').value;
    
    $.ajax({
        type: "GET",
        url: "Api/ProcessWarranty.php",
        data: { 
            request: 'getrepairhistory',
            dateFrom: dateFrom,
            dateTo: dateTo
        },
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data && response.data.length > 0) {
                displayRepairHistory(response.data);
            } else {
                document.getElementById('repairHistoryEmpty').style.display = 'block';
                document.getElementById('repairHistoryList').style.display = 'none';
            }
        },
        error: function(e) {
            console.log('Error loading repair history:', e);
        }
    });
}

// Display repair history
function displayRepairHistory(repairs) {
    var historyList = document.getElementById('repairHistoryList');
    var historyEmpty = document.getElementById('repairHistoryEmpty');
    
    if (!historyList) return;
    
    var html = '';
    repairs.forEach(function(repair) {
        var statusClass = getRepairStatusClass(repair.TrangThai);
        var hasResult = repair.TrangThai == 3 && (repair.ChiPhiSuaChua > 0 || repair.KetQuaSuaChua);
        var showReturnDate = repair.NgayHenTra && (repair.TrangThai == 2 || repair.TrangThai == 3);
        
        html += `
            <div class="repair-history-item">
                <div class="repair-item-product">
                    <div class="repair-item-image">
                        <img src="${repair.HinhAnh || 'img/products/default.png'}" alt="${repair.TenSP}">
                    </div>
                    <div class="repair-item-info">
                        <h4>${repair.TenSP}</h4>
                        <p>Mã phiếu: #${repair.MaPhieu}</p>
                        <p>Loại sự cố: ${repair.LoaiSuCo}</p>
                    </div>
                </div>
                <div class="repair-item-status">
                    <span class="repair-status-badge ${statusClass}">${repair.TrangThaiText}</span>
                    <div class="repair-item-date">Ngày tạo: ${formatDate(repair.NgayTao)}</div>
                    ${hasResult ? `<div class="repair-item-cost">Chi phí: ${formatCurrency(repair.ChiPhiSuaChua)}</div>` : ''}
                    ${showReturnDate ? `<div class="repair-item-return-date"><i class="fa fa-calendar-check-o"></i> Hẹn trả: ${formatDate(repair.NgayHenTra)}</div>` : ''}
                </div>
                <div class="repair-item-actions">
                    <button class="btn-view-detail" onclick='viewRepairDetail(${JSON.stringify(repair)})'>
                        Xem chi tiết
                    </button>
                </div>
            </div>
        `;
    });
    
    historyList.innerHTML = html;
    historyList.style.display = 'block';
    if (historyEmpty) historyEmpty.style.display = 'none';
}

// Format currency
function formatCurrency(amount) {
    if (!amount || amount == 0) return 'Miễn phí';
    return parseInt(amount).toLocaleString('vi-VN') + ' VND';
}

// Get repair status class
function getRepairStatusClass(status) {
    switch(parseInt(status)) {
        case 1: return 'pending';
        case 2: return 'in-progress';
        case 3: return 'completed';
        case 4: return 'cancelled';
        default: return 'pending';
    }
}

// View repair detail
function viewRepairDetail(repair) {
    var statusClass = getRepairStatusClass(repair.TrangThai);
    var hasResult = repair.ChiPhiSuaChua > 0 || repair.KetQuaSuaChua;
    
    var html = `
        <div style="text-align: left; padding: 10px 0;">
            <div style="display: flex; gap: 15px; align-items: center; margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 10px;">
                <img src="${repair.HinhAnh || 'img/products/default.png'}" style="width: 60px; height: 60px; object-fit: contain; border-radius: 8px; background: #fff;">
                <div>
                    <h4 style="margin: 0 0 5px 0; font-size: 15px;">${repair.TenSP}</h4>
                    <p style="margin: 0; font-size: 13px; color: #666;">Mã phiếu: #${repair.MaPhieu}</p>
                </div>
            </div>
            
            <div style="margin-bottom: 15px;">
                <p style="margin: 8px 0;"><strong>Loại sự cố:</strong> ${repair.LoaiSuCo}</p>
                ${repair.MoTa ? `<p style="margin: 8px 0;"><strong>Mô tả:</strong> ${repair.MoTa}</p>` : ''}
                <p style="margin: 8px 0;"><strong>Ngày hẹn:</strong> ${formatDate(repair.NgayHen)} - ${repair.GioHen}</p>
                <p style="margin: 8px 0;"><strong>Trạng thái:</strong> <span class="repair-status-badge ${statusClass}" style="padding: 4px 12px; border-radius: 15px; font-size: 12px;">${repair.TrangThaiText}</span></p>
            </div>
    `;
    
    // Show result section if completed
    if (repair.TrangThai == 3 || hasResult) {
        html += `
            <hr style="margin: 15px 0; border-color: #eee;">
            <div style="background: #e8f5e9; padding: 15px; border-radius: 10px; border-left: 4px solid #4caf50;">
                <h5 style="margin: 0 0 10px 0; color: #2e7d32;"><i class="fa fa-check-circle"></i> Kết quả sửa chữa</h5>
                <p style="margin: 8px 0;"><strong>Chi phí:</strong> <span style="color: #1976d2; font-weight: 600;">${formatCurrency(repair.ChiPhiSuaChua)}</span></p>
                ${repair.NgayHenTra ? `<p style="margin: 8px 0;"><strong>Ngày hẹn trả:</strong> <span style="color: #e65100; font-weight: 600;">${formatDate(repair.NgayHenTra)}</span></p>` : ''}
                ${repair.KetQuaSuaChua ? `<p style="margin: 8px 0;"><strong>Ghi chú từ kỹ thuật viên:</strong><br><span style="color: #333;">${repair.KetQuaSuaChua}</span></p>` : ''}
                ${repair.NgayCapNhat ? `<p style="margin: 8px 0; font-size: 12px; color: #666;"><em>Cập nhật lần cuối: ${formatDate(repair.NgayCapNhat)}</em></p>` : ''}
            </div>
        `;
    } else if (repair.TrangThai == 4) {
        html += `
            <hr style="margin: 15px 0; border-color: #eee;">
            <div style="background: #ffebee; padding: 15px; border-radius: 10px; border-left: 4px solid #f44336;">
                <h5 style="margin: 0 0 10px 0; color: #c62828;"><i class="fa fa-times-circle"></i> Phiếu đã bị hủy</h5>
                ${repair.KetQuaSuaChua ? `<p style="margin: 8px 0;"><strong>Lý do:</strong> ${repair.KetQuaSuaChua}</p>` : ''}
            </div>
        `;
    } else if (repair.TrangThai == 2) {
        html += `
            <hr style="margin: 15px 0; border-color: #eee;">
            <div style="background: #e3f2fd; padding: 15px; border-radius: 10px; border-left: 4px solid #2196f3;">
                <h5 style="margin: 0 0 5px 0; color: #1565c0;"><i class="fa fa-wrench"></i> Đang sửa chữa</h5>
                <p style="margin: 0; font-size: 13px;">Sản phẩm của bạn đang được kỹ thuật viên xử lý. Chúng tôi sẽ thông báo khi hoàn tất.</p>
                ${repair.NgayHenTra ? `<p style="margin: 8px 0;"><strong>Dự kiến trả:</strong> <span style="color: #e65100; font-weight: 600;">${formatDate(repair.NgayHenTra)}</span></p>` : ''}
            </div>
        `;
    }
    
    html += '</div>';
    
    Swal.fire({
        title: 'Chi tiết phiếu sửa chữa',
        html: html,
        width: 500,
        confirmButtonText: 'Đóng',
        confirmButtonColor: '#1428a0'
    });
}

// Open chat
function openChat() {
    // Open chatbot if exists
    var chatbotToggle = document.querySelector('.chatbot-toggle');
    if (chatbotToggle) {
        chatbotToggle.click();
    } else {
        Swal.fire('Thông báo', 'Chat trực tuyến hiện chưa khả dụng', 'info');
    }
}

