// ======================= CHECKOUT PAGE =======================

var listProduct = [];
var currentUser = null;
var appliedVoucher = null; // Voucher đã áp dụng
var voucherDiscount = 0;   // Số tiền giảm từ voucher

// Initialize on page load
window.onload = function() {
    console.log("Checkout page loaded");
    khoiTao();
}

function khoiTao() {
    console.log("khoiTao() called");
    khoiTaoGioHang();
    loadUserInfo();
    initVoucherEvents();
}

// Load cart from localStorage
function khoiTaoGioHang() {
    var cart = localStorage.getItem('giohang');
    console.log('Raw cart from localStorage:', cart);
    if (!cart) {
        displayOrderItems();
        calculateTotal();
        return;
    }
    
    var listGioHang = JSON.parse(cart);
    if (!listGioHang || listGioHang.length === 0) {
        displayOrderItems();
        calculateTotal();
        return;
    }
    
    // Chỉ hỗ trợ series products (sản phẩm cũ đã bị xóa)
    var seriesProducts = [];
    
    for (var i = 0; i < listGioHang.length; i++) {
        if (listGioHang[i].isSeries) {
            seriesProducts.push(listGioHang[i]);
        }
        // Bỏ qua sản phẩm cũ (không có isSeries flag)
    }
    
    var allProducts = [];
    var pendingRequests = 0;
    
    // Load series products only
    if (seriesProducts.length > 0) {
        pendingRequests++;
        
        $.ajax({
            type: "POST",
            url: "Api/ProcessProductSeriesGet.php",
            dataType: "json",
            data: {
                request: "getcartitems",
                items: JSON.stringify(seriesProducts.map(function(p) {
                    return {
                        seriesId: p.seriesConfig.seriesId,
                        variantId: p.seriesConfig.variantId,
                        storageId: p.seriesConfig.storageId,
                        colorId: p.seriesConfig.colorId,
                        quantity: p.soLuong,
                        productId: p.masp
                    };
                }))
            },
            success: function(data) {
                if (data.success && data.data) {
                    for (var i = 0; i < data.data.length; i++) {
                        var item = data.data[i];
                        // Merge quantity
                        for (var j = 0; j < seriesProducts.length; j++) {
                            if (seriesProducts[j].masp === item.productId) {
                                item.SoLuongTrongGio = seriesProducts[j].soLuong;
                                item.isSeries = true;
                                item.seriesConfig = seriesProducts[j].seriesConfig;
                                break;
                            }
                        }
                    }
                    allProducts = allProducts.concat(data.data);
                }
                pendingRequests--;
                if (pendingRequests === 0) {
                    listProduct = allProducts;
                    displayOrderItems();
                    calculateTotal();
                }
            },
            error: function(e) {
                console.log("Error loading series products:", e);
                pendingRequests--;
                if (pendingRequests === 0) {
                    listProduct = allProducts;
                    displayOrderItems();
                    calculateTotal();
                }
            }
        });
    }
    
    // If no requests, still display
    if (pendingRequests === 0) {
        listProduct = allProducts;
        displayOrderItems();
        calculateTotal();
    }
}

// Load user info
function loadUserInfo() {
    getCurrentUser(function(user) {
        console.log('User data:', user);
        currentUser = user;
        if (user) {
            // Hide login modal if user is logged in
            var modal = document.querySelector('.containTaikhoan');
            if (modal) {
                modal.style.display = 'none';
            }
            
            var email = user.Email || user.email || '';
            var hovaten = ((user.Ho || user.ho || '') + ' ' + (user.Ten || user.ten || '')).trim();
            var sdt = user.SDT || user.sdt || '';
            
            document.getElementById('email').value = email;
            document.getElementById('hovaten').value = hovaten;
            document.getElementById('sdt').value = sdt;
            document.getElementById('userEmail').textContent = hovaten || email || 'Guest';
            
            // Load saved address from localStorage
            loadSavedAddress();
        } else {
            // Show login modal if not logged in
            console.log('No user logged in');
            // Still try to load saved address for guest
            loadSavedAddress();
        }
    }, function(error) {
        console.log('Error loading user:', error);
        loadSavedAddress();
    });
}

// Load saved address from localStorage
function loadSavedAddress() {
    var savedAddress = localStorage.getItem('savedAddress');
    if (savedAddress) {
        try {
            var address = JSON.parse(savedAddress);
            if (address.sonha) document.getElementById('sonha').value = address.sonha;
            if (address.duongpho) document.getElementById('duongpho').value = address.duongpho;
            if (address.tinh) document.getElementById('tinh').value = address.tinh;
            if (address.phuong) document.getElementById('phuong').value = address.phuong;
            if (address.khupho) document.getElementById('khupho').value = address.khupho;
            
            // Auto check "save address" checkbox
            var saveAddressCheckbox = document.getElementById('saveAddress');
            if (saveAddressCheckbox) {
                saveAddressCheckbox.checked = true;
            }
            
            console.log('Loaded saved address:', address);
        } catch (e) {
            console.log('Error loading saved address:', e);
        }
    }
}

// Save address to localStorage
function saveAddressToStorage() {
    var address = {
        sonha: document.getElementById('sonha').value.trim(),
        duongpho: document.getElementById('duongpho').value.trim(),
        tinh: document.getElementById('tinh').value.trim(),
        phuong: document.getElementById('phuong').value.trim(),
        khupho: document.getElementById('khupho').value.trim()
    };
    localStorage.setItem('savedAddress', JSON.stringify(address));
    console.log('Address saved:', address);
}

// Display order items
function displayOrderItems() {
    var container = document.getElementById('orderItems');
    var cartCount = document.querySelector('.cart-count');
    
    console.log('displayOrderItems called, listProduct:', listProduct);
    
    if (!listProduct || listProduct.length === 0) {
        container.innerHTML = '<div style="text-align:center; padding:20px; color:#999;">Giỏ hàng trống</div>';
        if (cartCount) cartCount.textContent = '0';
        return;
    }
    
    if (cartCount) cartCount.textContent = listProduct.length;
    
    var html = '';
    for (var i = 0; i < listProduct.length; i++) {
        var p = listProduct[i];
        console.log('Product:', p);
        
        var price = 0;
        var discount = 0;
        var tensp = '';
        var hinhanh = '';
        var configInfo = '';
        
        if (p.isSeries) {
            // Series product
            tensp = p.seriesName || p.TenSP;
            hinhanh = p.image || p.HinhAnh || 'img/products/default.png';
            
            // Giá gốc sản phẩm
            var basePrice = Number(p.price || 0);
            
            // Thêm care plan price nếu có
            var carePlanPrice = 0;
            if (p.seriesConfig && p.seriesConfig.carePlanId && p.seriesConfig.carePlanId > 0) {
                carePlanPrice = Number(p.seriesConfig.carePlanPrice || 0);
            }
            price = basePrice + carePlanPrice;
            
            // Config info
            if (p.configName) {
                configInfo = ' - ' + p.configName;
            }
            if (p.colorName) {
                configInfo += ' - ' + p.colorName;
            }
            // Hiển thị care plan
            if (carePlanPrice > 0) {
                configInfo += '<br><span style="color:#1428a0;font-size:12px;">+ 3AM Care+ (+' + numToString(carePlanPrice) + ' ₫)</span>';
            }
        } else {
            // Regular product
            tensp = p.TenSP || 'Sản phẩm';
            hinhanh = p.HinhAnh || '';
            price = Number(p.DonGia || 0);
            
            // Handle KM object safely
            if (p.KM && p.KM.GiaTriKM) {
                discount = Number(p.KM.GiaTriKM);
            } else if (p.km && p.km.giatrikm) {
                discount = Number(p.km.giatrikm);
            }
        }
        
        var finalPrice = price - discount;
        var quantity = Number(p.SoLuongTrongGio || 1);
        var total = finalPrice * quantity;
        
        html += `
            <div class="order-item">
                <img src="${hinhanh}" alt="${tensp}" class="order-item-image">
                <div class="order-item-info">
                    <div class="order-item-name">${tensp}${configInfo}</div>
                    <div class="order-item-details">Kèm ${quantity}</div>
                </div>
                <div class="order-item-price">
                    ${discount > 0 ? '<div class="order-item-original-price">' + numToString(price) + ' VNĐ</div>' : ''}
                    <div class="order-item-final-price">${numToString(total)} VNĐ</div>
                </div>
            </div>
        `;
    }
    
    container.innerHTML = html;
    console.log('Order items rendered');
}

// Calculate totals
function calculateTotal() {
    var subtotal = 0;
    
    console.log('calculateTotal called');
    
    for (var i = 0; i < listProduct.length; i++) {
        var p = listProduct[i];
        var price = 0;
        var discount = 0;
        
        if (p.isSeries) {
            // Series product - bao gồm care plan
            var basePrice = Number(p.price || 0);
            var carePlanPrice = 0;
            if (p.seriesConfig && p.seriesConfig.carePlanId && p.seriesConfig.carePlanId > 0) {
                carePlanPrice = Number(p.seriesConfig.carePlanPrice || 0);
            }
            price = basePrice + carePlanPrice;
        } else {
            // Regular product
            price = Number(p.DonGia || 0);
            
            if (p.KM && p.KM.GiaTriKM) {
                discount = Number(p.KM.GiaTriKM);
            } else if (p.km && p.km.giatrikm) {
                discount = Number(p.km.giatrikm);
            }
        }
        
        var finalPrice = price - discount;
        var quantity = Number(p.SoLuongTrongGio || 1);
        subtotal += finalPrice * quantity;
    }
    
    // Apply voucher discount
    var total = subtotal - voucherDiscount;
    if (total < 0) total = 0;
    
    var subtotalEl = document.getElementById('subtotal');
    var totalEl = document.getElementById('totalPrice');
    
    if (subtotalEl) subtotalEl.textContent = numToString(subtotal) + ' VNĐ';
    if (totalEl) totalEl.textContent = numToString(total) + ' VNĐ';
    
    // Update payment totals
    updatePaymentTotals(total);
    
    console.log('Totals calculated - Subtotal:', subtotal, 'Voucher:', voucherDiscount, 'Total:', total);
    
    return { subtotal: subtotal, total: total };
}

// Update payment totals in payment section
function updatePaymentTotals(total) {
    var paymentQRTotal = document.getElementById('paymentQRTotal');
    var paymentTotalAmount = document.getElementById('paymentTotalAmount');
    
    if (paymentQRTotal) paymentQRTotal.textContent = numToString(total);
    if (paymentTotalAmount) paymentTotalAmount.textContent = numToString(total);
}

// ======================= VOUCHER FUNCTIONS =======================

// Initialize voucher events
function initVoucherEvents() {
    var applyBtn = document.querySelector('.btn-apply-promo');
    var promoInput = document.getElementById('promoCode');
    
    if (applyBtn) {
        applyBtn.addEventListener('click', applyVoucher);
    }
    
    if (promoInput) {
        promoInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                applyVoucher();
            }
        });
    }
}

// Apply voucher
function applyVoucher() {
    var promoInput = document.getElementById('promoCode');
    var code = promoInput ? promoInput.value.trim().toUpperCase() : '';
    
    if (!code) {
        Swal.fire({
            icon: 'warning',
            title: 'Chưa nhập mã',
            text: 'Vui lòng nhập mã voucher'
        });
        return;
    }
    
    // Calculate current subtotal (bao gồm care plan)
    var subtotal = 0;
    for (var i = 0; i < listProduct.length; i++) {
        var p = listProduct[i];
        var price = 0;
        var discount = 0;
        
        if (p.isSeries) {
            // Series product - bao gồm care plan
            var basePrice = Number(p.price || 0);
            var carePlanPrice = 0;
            if (p.seriesConfig && p.seriesConfig.carePlanId && p.seriesConfig.carePlanId > 0) {
                carePlanPrice = Number(p.seriesConfig.carePlanPrice || 0);
            }
            price = basePrice + carePlanPrice;
        } else {
            price = Number(p.DonGia || 0);
            if (p.KM && p.KM.GiaTriKM) discount = Number(p.KM.GiaTriKM);
            else if (p.km && p.km.giatrikm) discount = Number(p.km.giatrikm);
        }
        
        var finalPrice = price - discount;
        var quantity = Number(p.SoLuongTrongGio || 1);
        subtotal += finalPrice * quantity;
    }
    
    // Validate voucher via API
    $.ajax({
        type: 'POST',
        url: 'Api/ProcessVoucher.php',
        data: {
            request: 'validate',
            code: code,
            total: subtotal
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                appliedVoucher = response.voucher;
                voucherDiscount = response.discount;
                
                // Update UI
                showVoucherApplied(response.voucher, response.discount);
                calculateTotal();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Không thể áp dụng',
                    text: response.message
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: 'Không thể kết nối server'
            });
        }
    });
}

// Show voucher applied UI
function showVoucherApplied(voucher, discount) {
    var promoSection = document.querySelector('.promo-section');
    if (!promoSection) return;
    
    promoSection.innerHTML = `
        <div class="promo-code-title">Mã khuyến mãi</div>
        <div class="voucher-applied">
            <div class="voucher-applied-info">
                <i class="fa fa-check-circle" style="color: #28a745;"></i>
                <div class="voucher-applied-details">
                    <strong>${voucher.code}</strong>
                    <span>${voucher.name}</span>
                </div>
            </div>
            <button class="btn-remove-voucher" onclick="removeVoucher()">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="voucher-discount-row">
            <span>Giảm giá voucher</span>
            <span class="voucher-discount-value">-${numToString(discount)} VNĐ</span>
        </div>
    `;
    
    // Also update summary details
    updateSummaryWithVoucher(discount);
}

// Update summary section with voucher discount
function updateSummaryWithVoucher(discount) {
    var summaryDetails = document.querySelector('.summary-details');
    if (!summaryDetails) return;
    
    // Check if voucher row already exists
    var existingRow = summaryDetails.querySelector('.voucher-summary-row');
    if (existingRow) {
        existingRow.querySelector('.summary-value').textContent = '-' + numToString(discount) + ' ₫';
    } else {
        var voucherRow = document.createElement('div');
        voucherRow.className = 'summary-row voucher-summary-row';
        voucherRow.innerHTML = `
            <span style="color: #28a745;">Voucher giảm giá</span>
            <span class="summary-value" style="color: #28a745;">-${numToString(discount)} ₫</span>
        `;
        summaryDetails.appendChild(voucherRow);
    }
}

// Remove voucher
function removeVoucher() {
    appliedVoucher = null;
    voucherDiscount = 0;
    
    // Reset promo section
    var promoSection = document.querySelector('.promo-section');
    if (promoSection) {
        promoSection.innerHTML = `
            <div class="promo-code-title">Mã khuyến mãi</div>
            <div class="promo-code-input">
                <input type="text" id="promoCode" placeholder="Nhập Voucher hoặc Gift Code">
                <button class="btn-apply-promo">Áp dụng</button>
            </div>
            <a href="#" class="promo-link">Xem mã khuyến mãi</a>
        `;
        // Re-init events
        initVoucherEvents();
    }
    
    // Remove voucher row from summary
    var voucherRow = document.querySelector('.voucher-summary-row');
    if (voucherRow) voucherRow.remove();
    
    calculateTotal();
    
    Swal.fire({
        icon: 'info',
        title: 'Đã hủy voucher',
        timer: 1500,
        showConfirmButton: false
    });
}

// Toggle summary details
function toggleSummary() {
    var summaryExpand = document.querySelector('.summary-expand');
    summaryExpand.classList.toggle('active');
}

// Initialize provinces
function khoiTaoTinh() {
    var tinhSelect = document.getElementById('tinh');
    var provinces = [
        'Hà Nội', 'TP. Hồ Chí Minh', 'Đà Nẵng', 'Hải Phòng', 'Cần Thơ',
        'An Giang', 'Bà Rịa - Vũng Tàu', 'Bắc Giang', 'Bắc Kạn', 'Bạc Liêu',
        'Bắc Ninh', 'Bến Tre', 'Bình Định', 'Bình Dương', 'Bình Phước',
        'Bình Thuận', 'Cà Mau', 'Cao Bằng', 'Đắk Lắk', 'Đắk Nông',
        'Điện Biên', 'Đồng Nai', 'Đồng Tháp', 'Gia Lai', 'Hà Giang',
        'Hà Nam', 'Hà Tĩnh', 'Hải Dương', 'Hậu Giang', 'Hòa Bình',
        'Hưng Yên', 'Khánh Hòa', 'Kiên Giang', 'Kon Tum', 'Lai Châu',
        'Lâm Đồng', 'Lạng Sơn', 'Lào Cai', 'Long An', 'Nam Định',
        'Nghệ An', 'Ninh Bình', 'Ninh Thuận', 'Phú Thọ', 'Phú Yên',
        'Quảng Bình', 'Quảng Nam', 'Quảng Ngãi', 'Quảng Ninh', 'Quảng Trị',
        'Sóc Trăng', 'Sơn La', 'Tây Ninh', 'Thái Bình', 'Thái Nguyên',
        'Thanh Hóa', 'Thừa Thiên Huế', 'Tiền Giang', 'Trà Vinh', 'Tuyên Quang',
        'Vĩnh Long', 'Vĩnh Phúc', 'Yên Bái'
    ];
    
    provinces.forEach(function(province) {
        var option = document.createElement('option');
        option.value = province;
        option.textContent = province;
        tinhSelect.appendChild(option);
    });
}

// Show delivery section (Section 2)
function showDeliverySection() {
    // Validate section 1 first
    var email = document.getElementById('email').value.trim();
    var hovaten = document.getElementById('hovaten').value.trim();
    var sdt = document.getElementById('sdt').value.trim();
    var sonha = document.getElementById('sonha').value.trim();
    var duongpho = document.getElementById('duongpho').value.trim();
    var tinh = document.getElementById('tinh').value.trim();
    var phuong = document.getElementById('phuong').value.trim();
    var khupho = document.getElementById('khupho').value.trim();
    
    if (!email || !hovaten || !sdt) {
        Swal.fire({
            type: 'warning',
            title: 'Thiếu thông tin',
            text: 'Vui lòng điền đầy đủ thông tin khách hàng'
        });
        return;
    }
    
    if (!sonha || !duongpho || !tinh || !phuong) {
        Swal.fire({
            type: 'warning',
            title: 'Thiếu thông tin',
            text: 'Vui lòng điền đầy đủ địa chỉ giao hàng'
        });
        return;
    }
    
    // Check agree terms
    var agreeTerms = document.getElementById('agreeTerms');
    if (agreeTerms && !agreeTerms.checked) {
        Swal.fire({
            type: 'warning',
            title: 'Chưa đồng ý điều khoản',
            text: 'Vui lòng đồng ý với Điều khoản & Điều kiện để tiếp tục'
        });
        return;
    }
    
    if (listProduct.length === 0) {
        Swal.fire({
            type: 'warning',
            title: 'Giỏ hàng trống',
            text: 'Không có sản phẩm để thanh toán'
        });
        return;
    }
    
    // Build address string
    var diachi = sonha + ', ' + duongpho;
    if (khupho) diachi += ', ' + khupho;
    diachi += ', ' + phuong + ', ' + tinh;
    
    // Update summary
    document.getElementById('summaryName').textContent = hovaten;
    document.getElementById('summaryAddress').textContent = diachi;
    
    // Hide form sections, show summary
    document.getElementById('section1Form').style.display = 'none';
    document.getElementById('section1Address').style.display = 'none';
    document.getElementById('section1Summary').style.display = 'block';
    
    // Show delivery section
    document.getElementById('deliverySection').style.display = 'block';
    
    // Load package product info
    loadPackageProduct();
    
    // Scroll to delivery section
    document.getElementById('deliverySection').scrollIntoView({ behavior: 'smooth' });
}

// Edit Section 1 (go back to form)
function editSection1() {
    // Show form sections, hide summary
    document.getElementById('section1Form').style.display = 'block';
    document.getElementById('section1Address').style.display = 'block';
    document.getElementById('section1Summary').style.display = 'none';
    
    // Hide delivery and payment sections
    document.getElementById('deliverySection').style.display = 'none';
    var paymentSection = document.getElementById('paymentSection');
    if (paymentSection) paymentSection.style.display = 'none';
    
    // Scroll to top
    document.getElementById('section1Form').scrollIntoView({ behavior: 'smooth' });
}

// Load package product for delivery section
function loadPackageProduct() {
    var container = document.getElementById('packageProduct');
    var packageTotal = document.getElementById('packageTotal');
    
    if (packageTotal) {
        packageTotal.textContent = listProduct.length;
    }
    
    if (listProduct.length > 0) {
        var product = listProduct[0];
        var productName = product.TenSP || product.seriesName || 'Sản phẩm';
        var variant = product.variantName || '';
        var color = product.colorName || '';
        var price = product.DonGia || product.price || 0;
        var image = product.HinhAnh || product.image || 'img/products/default.jpg';
        
        var html = `
            <div class="package-product-item">
                <div class="package-product-colors">
                    <img src="${image}" alt="${productName}">
                </div>
                <div class="package-product-details">
                    <div class="package-product-name">${productName}</div>
                    <div class="package-product-variant">${color || variant}</div>
                    <div class="package-product-price">
                        <span class="new-price">${parseInt(price).toLocaleString()} VND</span>
                    </div>
                </div>
            </div>
        `;
        container.innerHTML = html;
    }
}

// Show payment section (Section 3)
function showPaymentSection() {
    // Hide Section 2 form, show Section 2 summary
    document.getElementById('deliverySection').style.display = 'none';
    document.getElementById('section2Summary').style.display = 'block';
    
    // Populate Section 2 summary
    populateSection2Summary();
    
    // Show Section 3
    document.getElementById('paymentSection').style.display = 'block';
    
    // Update payment total amount
    updatePaymentTotal();
    
    document.getElementById('paymentSection').scrollIntoView({ behavior: 'smooth' });
}

// Populate Section 2 Summary
function populateSection2Summary() {
    // Get product name from cart
    var productName = '';
    if (listProduct.length > 0) {
        productName = listProduct[0].TenSanPham || listProduct[0].name || 'Sản phẩm';
    }
    
    // Set package count
    document.getElementById('summaryPackageCurrent').textContent = '1';
    document.getElementById('summaryPackageTotal').textContent = '1';
    
    // Set product name
    document.getElementById('summaryProductName').textContent = productName;
    
    // Calculate delivery date (7 days from now)
    var deliveryDate = new Date();
    deliveryDate.setDate(deliveryDate.getDate() + 7);
    var options = { day: '2-digit', month: '2-digit', year: 'numeric' };
    var formattedDate = deliveryDate.toLocaleDateString('vi-VN', options).replace(/\//g, ' thg ');
    
    document.getElementById('summaryDeliveryDate').textContent = 'Bắt đầu giao hàng từ ' + formattedDate;
}

// Edit Section 2 - go back to delivery form
function editSection2() {
    document.getElementById('section2Summary').style.display = 'none';
    document.getElementById('paymentSection').style.display = 'none';
    document.getElementById('deliverySection').style.display = 'block';
    document.getElementById('deliverySection').scrollIntoView({ behavior: 'smooth' });
}

// Toggle payment accordion
function togglePaymentAccordion(id) {
    var item = document.getElementById(id);
    var allItems = document.querySelectorAll('.payment-accordion-item');
    
    // Close all other items
    allItems.forEach(function(el) {
        if (el.id !== id) {
            el.classList.remove('active');
        }
    });
    
    // Toggle current item
    item.classList.toggle('active');
    
    // If opening QR payment, uncheck COD
    if (id === 'paymentQR' && item.classList.contains('active')) {
        // Default select MoMo
        var momoRadio = document.querySelector('input[name="payment"][value="momo"]');
        if (momoRadio) momoRadio.checked = true;
    }
    
    // If opening Cash payment, set COD
    if (id === 'paymentCash' && item.classList.contains('active')) {
        // Clear QR payment selections
        var qrRadios = document.querySelectorAll('#paymentQR input[name="payment"]');
        qrRadios.forEach(function(r) { r.checked = false; });
    }
}

// Update payment total display
function updatePaymentTotal() {
    var subtotal = 0;
    for (var i = 0; i < listProduct.length; i++) {
        var p = listProduct[i];
        var price = 0;
        var discount = 0;
        
        if (p.isSeries) {
            // Series product - bao gồm care plan
            var basePrice = Number(p.price || 0);
            var carePlanPrice = 0;
            if (p.seriesConfig && p.seriesConfig.carePlanId && p.seriesConfig.carePlanId > 0) {
                carePlanPrice = Number(p.seriesConfig.carePlanPrice || 0);
            }
            price = basePrice + carePlanPrice;
        } else {
            price = Number(p.DonGia || 0);
            if (p.KM && p.KM.GiaTriKM) discount = Number(p.KM.GiaTriKM);
            else if (p.km && p.km.giatrikm) discount = Number(p.km.giatrikm);
        }
        
        var finalPrice = price - discount;
        var qty = Number(p.SoLuongTrongGio || 1);
        subtotal += finalPrice * qty;
    }
    
    // Apply voucher discount
    var total = subtotal - voucherDiscount;
    if (total < 0) total = 0;
    
    var formattedTotal = total.toLocaleString('vi-VN');
    
    // Update both COD and QR payment totals
    var totalEl = document.getElementById('paymentTotalAmount');
    if (totalEl) {
        totalEl.textContent = formattedTotal;
    }
    
    var qrTotalEl = document.getElementById('paymentQRTotal');
    if (qrTotalEl) {
        qrTotalEl.textContent = formattedTotal;
    }
}

// Submit QR Payment - Show MoMo modal
function xacNhanThanhToanQR() {
    // Check if terms are agreed
    var agreeTerms = document.getElementById('paymentQRAgreeTerms');
    if (!agreeTerms || !agreeTerms.checked) {
        Swal.fire({
            icon: 'warning',
            title: 'Chưa đồng ý điều khoản',
            text: 'Vui lòng đọc và đồng ý với Điều khoản & Điều kiện trước khi tiếp tục'
        });
        return;
    }
    
    // Show MoMo payment modal
    openMomoModal();
}

// Open MoMo Modal
function openMomoModal() {
    document.getElementById('momoModalOverlay').classList.add('active');
    document.body.style.overflow = 'hidden';
}

// Close MoMo Modal
function closeMomoModal() {
    document.getElementById('momoModalOverlay').classList.remove('active');
    document.body.style.overflow = '';
}

// Process MoMo Payment - Call MoMo API
function processMomoPayment() {
    // Calculate total (bao gồm voucher discount)
    var subtotal = 0;
    for (var i = 0; i < listProduct.length; i++) {
        var p = listProduct[i];
        var price = 0;
        var discount = 0;
        
        if (p.isSeries) {
            // Series product - bao gồm care plan
            var basePrice = Number(p.price || 0);
            var carePlanPrice = 0;
            if (p.seriesConfig && p.seriesConfig.carePlanId && p.seriesConfig.carePlanId > 0) {
                carePlanPrice = Number(p.seriesConfig.carePlanPrice || 0);
            }
            price = basePrice + carePlanPrice;
        } else {
            price = Number(p.DonGia || 0);
            if (p.KM && p.KM.GiaTriKM) discount = Number(p.KM.GiaTriKM);
            else if (p.km && p.km.giatrikm) discount = Number(p.km.giatrikm);
        }
        
        var finalPrice = price - discount;
        var qty = Number(p.SoLuongTrongGio || 1);
        subtotal += finalPrice * qty;
    }
    
    // Apply voucher discount
    var total = subtotal - voucherDiscount;
    if (total < 0) total = 0;
    
    // Get order info
    var hovaten = document.getElementById('hovaten').value.trim();
    var orderInfo = 'Thanh toan don hang - ' + hovaten;
    
    // Show loading
    Swal.fire({
        title: 'Đang tạo thanh toán MoMo...',
        text: 'Vui lòng đợi',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Call MoMo API
    $.ajax({
        url: 'Api/MomoPayment.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            amount: total,
            orderInfo: orderInfo,
            maHD: Date.now(),
            maNguoiDung: 1
        }),
        success: function(response) {
            Swal.close();
            
            if (typeof response === 'string') {
                response = JSON.parse(response);
            }
            
            if (response.success && response.payUrl) {
                // Close MoMo selection modal
                closeMomoModal();
                
                // Show QR Code modal with orderId for polling
                showMomoQRModal(response.payUrl, response.qrCodeUrl, total, response.orderId);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: response.message || 'Không thể tạo thanh toán MoMo'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Lỗi kết nối',
                text: 'Không thể kết nối đến server MoMo'
            });
        }
    });
}

// MoMo payment polling interval
var momoPollingInterval = null;

// Show MoMo QR Code Modal
function showMomoQRModal(payUrl, qrCodeUrl, amount, orderId) {
    // Create QR modal if not exists
    var qrModal = document.getElementById('momoQRModal');
    if (!qrModal) {
        var modalHtml = `
        <div class="payment-modal-overlay" id="momoQRModal">
            <div class="payment-modal momo-qr-modal" onclick="event.stopPropagation()">
                <div class="payment-modal-header">
                    <div class="payment-modal-logo">
                        <img src="https://developers.momo.vn/v3/assets/images/icon-52bd5808cecdb1970e1aeec3c31a3ee1.png" alt="MoMo" height="32">
                        <span>Thanh toán MoMo</span>
                    </div>
                    <button class="payment-modal-close" onclick="closeMomoQRModal()">&times;</button>
                </div>
                
                <!-- QR Code View -->
                <div class="payment-modal-body momo-qr-body" id="momoQRView">
                    <div class="qr-instruction">Quét mã QR bằng ứng dụng MoMo để thanh toán</div>
                    <div class="qr-code-container" id="momoQRCode"></div>
                    <div class="qr-amount">Số tiền: <strong id="momoAmount">0</strong> VND</div>
                    <div class="qr-status-checking">
                        <div class="spinner-border"></div>
                        <span>Đang chờ thanh toán...</span>
                    </div>
                </div>
                
                <!-- Success View -->
                <div class="payment-modal-body momo-success-body" id="momoSuccessView" style="display: none;">
                    <div class="success-checkmark">
                        <svg class="checkmark-svg" viewBox="0 0 100 100">
                            <!-- Circle -->
                            <circle class="checkmark-circle" cx="50" cy="50" r="45" fill="none" stroke="#e0e0e0" stroke-width="4"/>
                            <circle class="checkmark-circle-progress" cx="50" cy="50" r="45" fill="none" stroke="#4CAF50" stroke-width="4"/>
                            <!-- Checkmark -->
                            <path class="checkmark-check" d="M30 50 L45 65 L70 35" fill="none" stroke="#e0e0e0" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path class="checkmark-check-progress" d="M30 50 L45 65 L70 35" fill="none" stroke="#4CAF50" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="success-text">Thành công: giao dịch hoàn tất</div>
                    <div class="success-amount">Số tiền: <strong id="momoSuccessAmount">0</strong> VND</div>
                    <button class="btn-success-continue" onclick="momoPaymentComplete()">Tiếp tục</button>
                </div>
            </div>
        </div>`;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        qrModal = document.getElementById('momoQRModal');
    }
    
    // Reset views
    document.getElementById('momoQRView').style.display = 'block';
    document.getElementById('momoSuccessView').style.display = 'none';
    
    // Update amount
    document.getElementById('momoAmount').textContent = amount.toLocaleString('vi-VN');
    document.getElementById('momoSuccessAmount').textContent = amount.toLocaleString('vi-VN');
    
    // Generate QR Code
    var qrContainer = document.getElementById('momoQRCode');
    var qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(payUrl);
    qrContainer.innerHTML = '<img src="' + qrUrl + '" alt="MoMo QR Code" />';
    
    // Show modal
    qrModal.classList.add('active');
    document.body.style.overflow = 'hidden';
    
    // Start polling for payment status
    startMomoPolling(orderId, amount);
}

// Start polling MoMo payment status
function startMomoPolling(orderId, amount) {
    // Clear any existing interval
    if (momoPollingInterval) {
        clearInterval(momoPollingInterval);
    }
    
    // Poll every 3 seconds
    momoPollingInterval = setInterval(function() {
        checkMomoPaymentStatus(orderId, amount);
    }, 3000);
}

// Check MoMo payment status
function checkMomoPaymentStatus(orderId, amount) {
    $.ajax({
        url: 'Api/MomoCheckStatus.php',
        method: 'GET',
        data: { orderId: orderId },
        success: function(response) {
            if (typeof response === 'string') {
                response = JSON.parse(response);
            }
            
            if (response.success && response.isPaid) {
                // Payment successful!
                clearInterval(momoPollingInterval);
                showMomoSuccess(amount);
            }
        },
        error: function() {
            // Silently fail, will retry on next poll
        }
    });
}

// Show MoMo success view with animation
function showMomoSuccess(amount) {
    document.getElementById('momoQRView').style.display = 'none';
    document.getElementById('momoSuccessView').style.display = 'block';
    document.getElementById('momoSuccessAmount').textContent = amount.toLocaleString('vi-VN');
    
    // Trigger animation after a small delay
    setTimeout(function() {
        document.querySelector('.success-checkmark').classList.add('active');
    }, 100);
}

// MoMo payment complete - close modal and proceed
function momoPaymentComplete() {
    closeMomoQRModal();
    
    // Lấy thông tin đơn hàng
    var email = document.getElementById('email').value.trim();
    var hovaten = document.getElementById('hovaten').value.trim();
    var sdt = document.getElementById('sdt').value.trim();
    var sonha = document.getElementById('sonha').value.trim();
    var duongpho = document.getElementById('duongpho').value.trim();
    var tinh = document.getElementById('tinh').value.trim();
    var phuong = document.getElementById('phuong').value.trim();
    var khupho = document.getElementById('khupho').value.trim();
    var ghichu = document.getElementById('ghichu') ? document.getElementById('ghichu').value.trim() : '';
    
    var diachi = sonha + ', ' + duongpho + ', ' + phuong + ', ' + tinh;
    if (khupho) diachi = sonha + ', ' + duongpho + ', ' + khupho + ', ' + phuong + ', ' + tinh;
    
    // Show loading
    Swal.fire({
        title: 'Đang lưu đơn hàng...',
        text: 'Vui lòng đợi',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Lưu đơn hàng vào database
    $.ajax({
        url: 'Api/ProcessPayment.php',
        method: 'POST',
        data: {
            email: email,
            hovaten: hovaten,
            sdt: sdt,
            diachi: diachi,
            ghichu: ghichu,
            paymentMethod: 'momo',
            listProduct: JSON.stringify(listProduct),
            voucherCode: appliedVoucher ? appliedVoucher.code : '',
            voucherDiscount: voucherDiscount || 0
        },
        success: function(response) {
            console.log('MoMo order saved:', response);
            
            // Apply voucher usage if voucher was used
            if (appliedVoucher && appliedVoucher.code) {
                applyVoucherUsage(appliedVoucher.code);
            }
            
            Swal.fire({
                icon: 'success',
                title: 'Đặt hàng thành công!',
                text: 'Cảm ơn bạn đã mua hàng. Đơn hàng của bạn đang được xử lý.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#333'
            }).then(() => {
                // Clear cart
                localStorage.removeItem('giohang');
                localStorage.removeItem('listSanPham');
                // Redirect to home
                window.location.href = 'Index.php';
            });
        },
        error: function(xhr, status, error) {
            console.log('MoMo order save error:', xhr, status, error);
            Swal.fire({
                icon: 'warning',
                title: 'Thanh toán thành công',
                text: 'Đã thanh toán nhưng không thể lưu đơn hàng. Vui lòng liên hệ hỗ trợ.',
                confirmButtonText: 'OK'
            }).then(() => {
                localStorage.removeItem('giohang');
                window.location.href = 'Index.php';
            });
        }
    });
}

// Close MoMo QR Modal
function closeMomoQRModal() {
    // Stop polling
    if (momoPollingInterval) {
        clearInterval(momoPollingInterval);
        momoPollingInterval = null;
    }
    
    var qrModal = document.getElementById('momoQRModal');
    if (qrModal) {
        qrModal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Submit order with specific payment method
function xacNhanDatHangWithMethod(paymentMethod) {
    // Validate form
    var email = document.getElementById('email').value.trim();
    var hovaten = document.getElementById('hovaten').value.trim();
    var sdt = document.getElementById('sdt').value.trim();
    var sonha = document.getElementById('sonha').value.trim();
    var duongpho = document.getElementById('duongpho').value.trim();
    var tinh = document.getElementById('tinh').value.trim();
    var phuong = document.getElementById('phuong').value.trim();
    var khupho = document.getElementById('khupho').value.trim();
    
    if (!email || !hovaten || !sdt) {
        Swal.fire({
            icon: 'warning',
            title: 'Thiếu thông tin',
            text: 'Vui lòng điền đầy đủ thông tin khách hàng'
        });
        return;
    }
    
    if (listProduct.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Giỏ hàng trống',
            text: 'Không có sản phẩm để thanh toán'
        });
        return;
    }
    
    // Get address
    var ghichu = document.getElementById('ghichu') ? document.getElementById('ghichu').value.trim() : '';
    var diachi = sonha + ', ' + duongpho + ', ' + phuong + ', ' + tinh;
    if (khupho) diachi = sonha + ', ' + duongpho + ', ' + khupho + ', ' + phuong + ', ' + tinh;
    
    // Prepare order data
    var orderData = {
        email: email,
        hovaten: hovaten,
        sdt: sdt,
        diachi: diachi,
        ghichu: ghichu,
        paymentMethod: paymentMethod,
        products: listProduct,
        voucherCode: appliedVoucher ? appliedVoucher.code : null,
        voucherDiscount: voucherDiscount
    };
    
    // Save address if checkbox is checked
    var saveAddressCheckbox = document.getElementById('saveAddress');
    if (saveAddressCheckbox && saveAddressCheckbox.checked) {
        saveAddressToStorage();
    }
    
    // Show confirmation
    Swal.fire({
        icon: 'question',
        title: 'Xác nhận đặt hàng',
        text: 'Bạn có chắc chắn muốn đặt hàng?',
        showCancelButton: true,
        confirmButtonText: 'Đặt hàng',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.value) {
            submitOrder(orderData);
        }
    });
}

// Handle delivery option selection
document.addEventListener('DOMContentLoaded', function() {
    var deliveryOptions = document.querySelectorAll('.delivery-option');
    deliveryOptions.forEach(function(option) {
        option.addEventListener('click', function() {
            deliveryOptions.forEach(function(o) { o.classList.remove('selected'); });
            this.classList.add('selected');
            this.querySelector('input[type="radio"]').checked = true;
        });
    });
});

// Store pending order data for COD confirmation
var pendingCodOrderData = null;

// Generate order number for COD
function generateCodOrderNumber() {
    var now = new Date();
    var dateStr = now.getFullYear().toString().slice(-2) +
                  ('0' + (now.getMonth() + 1)).slice(-2) +
                  ('0' + now.getDate()).slice(-2);
    var random1 = Math.floor(Math.random() * 100000000).toString().padStart(8, '0');
    var timestamp = Date.now().toString();
    return 'VN' + dateStr + '-' + random1 + '_' + timestamp;
}

// Show COD confirmation modal
function showCodConfirmModal(orderNumber) {
    document.getElementById('codOrderNumber').textContent = orderNumber;
    document.getElementById('codOtpInput').value = '';
    document.getElementById('codConfirmModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

// Close COD confirmation modal
function closeCodConfirmModal() {
    document.getElementById('codConfirmModal').classList.remove('active');
    document.body.style.overflow = '';
    pendingCodOrderData = null;
}

// Confirm COD order with OTP
function confirmCodOrder() {
    var otpInput = document.getElementById('codOtpInput').value.trim();
    
    // Validate OTP - default is 00000000
    if (otpInput.length !== 8) {
        Swal.fire({
            icon: 'warning',
            title: 'Mã OTP không hợp lệ',
            text: 'Vui lòng nhập mã OTP 8 ký tự'
        });
        return;
    }
    
    if (otpInput !== '00000000') {
        Swal.fire({
            icon: 'error',
            title: 'Mã OTP không đúng',
            text: 'Vui lòng kiểm tra lại mã OTP'
        });
        return;
    }
    
    // Close modal
    document.getElementById('codConfirmModal').classList.remove('active');
    document.body.style.overflow = '';
    
    // Show loading
    Swal.fire({
        title: 'Đang xử lý đơn hàng...',
        text: 'Vui lòng đợi',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Submit order
    if (pendingCodOrderData) {
        // Send to server
        $.ajax({
            url: 'Api/ProcessPayment.php',
            method: 'POST',
            dataType: 'json',
            data: {
                email: pendingCodOrderData.email,
                hovaten: pendingCodOrderData.hovaten,
                sdt: pendingCodOrderData.sdt,
                diachi: pendingCodOrderData.diachi,
                ghichu: pendingCodOrderData.ghichu,
                paymentMethod: pendingCodOrderData.paymentMethod,
                listProduct: JSON.stringify(pendingCodOrderData.products),
                voucherCode: pendingCodOrderData.voucherCode || '',
                voucherDiscount: pendingCodOrderData.voucherDiscount || 0
            },
            success: function(response) {
                console.log('Order response:', response);
                
                // Save voucher code before clearing
                var usedVoucherCode = pendingCodOrderData ? pendingCodOrderData.voucherCode : null;
                
                // Apply voucher usage if voucher was used
                if (usedVoucherCode) {
                    applyVoucherUsage(usedVoucherCode);
                }
                
                // Clear pending data
                pendingCodOrderData = null;
                
                Swal.fire({
                    icon: 'success',
                    title: 'Đặt hàng thành công!',
                    text: 'Cảm ơn bạn đã mua hàng. Đơn hàng của bạn đang được xử lý.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                }).then(() => {
                    // Clear cart
                    localStorage.removeItem('giohang');
                    localStorage.removeItem('listSanPham');
                    // Redirect to home
                    window.location.href = 'Index.php';
                });
            },
            error: function(xhr, status, error) {
                console.log('Order error:', xhr, status, error);
                pendingCodOrderData = null;
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Không thể đặt hàng. Vui lòng thử lại sau.'
                });
            }
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: 'Không tìm thấy thông tin đơn hàng. Vui lòng thử lại.'
        });
    }
}

// Confirm order - show COD modal
function xacNhanDatHang() {
    // Validate form
    var email = document.getElementById('email').value.trim();
    var hovaten = document.getElementById('hovaten').value.trim();
    var sdt = document.getElementById('sdt').value.trim();
    var sonha = document.getElementById('sonha').value.trim();
    var duongpho = document.getElementById('duongpho').value.trim();
    var tinh = document.getElementById('tinh').value.trim();
    var phuong = document.getElementById('phuong').value.trim();
    var khupho = document.getElementById('khupho').value.trim();
    
    if (!email || !hovaten || !sdt) {
        Swal.fire({
            type: 'warning',
            title: 'Thiếu thông tin',
            text: 'Vui lòng điền đầy đủ thông tin khách hàng'
        });
        return;
    }
    
    if (!sonha || !duongpho || !tinh || !phuong) {
        Swal.fire({
            type: 'warning',
            title: 'Thiếu thông tin',
            text: 'Vui lòng điền đầy đủ địa chỉ giao hàng'
        });
        return;
    }
    
    if (listProduct.length === 0) {
        Swal.fire({
            type: 'warning',
            title: 'Giỏ hàng trống',
            text: 'Không có sản phẩm để thanh toán'
        });
        return;
    }
    
    // Check if terms are agreed
    var agreeTerms = document.getElementById('paymentAgreeTerms');
    if (agreeTerms && !agreeTerms.checked) {
        Swal.fire({
            icon: 'warning',
            title: 'Chưa đồng ý điều khoản',
            text: 'Vui lòng đồng ý với Điều khoản & Điều kiện trước khi tiếp tục'
        });
        return;
    }
    
    // Get address
    var ghichu = document.getElementById('ghichu') ? document.getElementById('ghichu').value.trim() : '';
    var diachi = sonha + ', ' + duongpho + ', ' + phuong + ', ' + tinh;
    if (khupho) diachi = sonha + ', ' + duongpho + ', ' + khupho + ', ' + phuong + ', ' + tinh;
    
    // Generate order number
    var orderNumber = generateCodOrderNumber();
    
    // Prepare order data
    pendingCodOrderData = {
        email: email,
        hovaten: hovaten,
        sdt: sdt,
        diachi: diachi,
        ghichu: ghichu,
        paymentMethod: 'cod',
        products: listProduct,
        orderNumber: orderNumber,
        voucherCode: appliedVoucher ? appliedVoucher.code : null,
        voucherDiscount: voucherDiscount
    };
    
    // Save address if checkbox is checked
    var saveAddressCheckbox = document.getElementById('saveAddress');
    if (saveAddressCheckbox && saveAddressCheckbox.checked) {
        saveAddressToStorage();
    }
    
    // Show COD confirmation modal with OTP input
    showCodConfirmModal(orderNumber);
}

// Submit order to server
function submitOrder(orderData) {
    // Show loading
    Swal.fire({
        title: 'Đang xử lý...',
        text: 'Vui lòng đợi',
        allowOutsideClick: false,
        onBeforeOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Send to server (using existing ProcessPayment.php)
    $.ajax({
        url: 'Api/ProcessPayment.php',
        method: 'POST',
        data: {
            email: orderData.email,
            hovaten: orderData.hovaten,
            sdt: orderData.sdt,
            diachi: orderData.diachi,
            ghichu: orderData.ghichu,
            paymentMethod: orderData.paymentMethod,
            listProduct: JSON.stringify(orderData.products),
            voucherCode: orderData.voucherCode || '',
            voucherDiscount: orderData.voucherDiscount || 0
        },
        success: function(response) {
            // Apply voucher usage if voucher was used
            if (orderData.voucherCode) {
                applyVoucherUsage(orderData.voucherCode);
            }
            
            Swal.fire({
                type: 'success',
                title: 'Đặt hàng thành công!',
                text: 'Cảm ơn bạn đã mua hàng. Đơn hàng của bạn đang được xử lý.',
                confirmButtonText: 'OK'
            }).then(() => {
                // Clear cart
                localStorage.removeItem('listSanPham');
                // Redirect to home
                window.location.href = 'Index.php';
            });
        },
        error: function() {
            Swal.fire({
                type: 'error',
                title: 'Lỗi',
                text: 'Không thể đặt hàng. Vui lòng thử lại sau.'
            });
        }
    });
}
// Apply voucher usage (increment usage count)
function applyVoucherUsage(voucherCode) {
    $.ajax({
        url: 'Api/ProcessVoucher.php',
        method: 'POST',
        data: {
            request: 'apply',
            code: voucherCode
        },
        success: function(response) {
            console.log('Voucher usage updated:', response);
        },
        error: function(err) {
            console.log('Error updating voucher usage:', err);
        }
    });
}
