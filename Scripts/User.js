// ======================= MY ORDERS PAGE =======================

var currentUser = null;
var ordersData = [];
var rewardsData = null;

window.onload = function() {
    khoiTao();
    initTabs();
    loadUserAndOrders();
    
    // Always load rewards data on page load
    loadRewardsData();
}

// Initialize tabs
function initTabs() {
    var tabLinks = document.querySelectorAll('.tab-link');
    tabLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            var tabName = this.getAttribute('data-tab');
            switchTab(tabName);
        });
    });
    
    // Initialize tier tabs in benefits section
    var tierTabs = document.querySelectorAll('.tier-tab');
    tierTabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            tierTabs.forEach(function(t) { t.classList.remove('active'); });
            this.classList.add('active');
            updateBenefitsPercent(this.getAttribute('data-tier'));
        });
    });
    
    // Check URL hash for tab
    if (window.location.hash === '#orders') {
        switchTab('orders');
    } else {
        // Default to rewards tab
        switchTab('rewards');
    }
}

// Switch tab
function switchTab(tabName) {
    // Update links
    document.querySelectorAll('.tab-link').forEach(function(link) {
        link.classList.remove('active');
        if (link.getAttribute('data-tab') === tabName) {
            link.classList.add('active');
        }
    });
    
    // Update content
    document.querySelectorAll('.tab-content').forEach(function(content) {
        content.classList.remove('active');
    });
    var targetTab = document.getElementById('tab-' + tabName);
    if (targetTab) {
        targetTab.classList.add('active');
    }
    
    // Load data based on tab
    if (tabName === 'rewards' && !rewardsData) {
        loadRewardsData();
    }
    
    // Update URL hash without scrolling
    history.replaceState(null, null, tabName === 'rewards' ? '#rewards' : '#orders');
}

// Update benefits percent based on tier
function updateBenefitsPercent(tier) {
    var percentMap = { 'blue': '1%', 'gold': '3%', 'platinum': '5%' };
    var percentEl = document.querySelector('.benefit-percent');
    if (percentEl) {
        percentEl.textContent = percentMap[tier] || '1%';
    }
}

// Load user info and orders
function loadUserAndOrders() {
    getCurrentUser(function(user) {
        if (user) {
            currentUser = user;
            displayUserInfo(user);
            loadOrders();
        } else {
            showNotLoggedIn();
        }
    }, function(error) {
        console.log('Error loading user:', error);
        showNotLoggedIn();
    });
}

// Display user info in sidebar
function displayUserInfo(user) {
    var hovaten = ((user.Ho || '') + ' ' + (user.Ten || '')).trim();
    var email = user.Email || user.email || '';
    
    document.getElementById('userDetails').innerHTML = `
        <h3>${hovaten || 'Khách hàng'}</h3>
        <p>${email}</p>
    `;
}

// Show not logged in message
function showNotLoggedIn() {
    document.querySelector('.orders-content').innerHTML = `
        <div class="not-logged-in">
            <i class="fa fa-user-times"></i>
            <h3>Bạn chưa đăng nhập</h3>
            <p>Vui lòng đăng nhập để xem đơn hàng của bạn</p>
            <a href="SignIn.php" class="btn-login">Đăng nhập ngay</a>
        </div>
    `;
}

// Load orders from server
function loadOrders() {
    $.ajax({
        type: "GET",
        url: "Api/ProcessOrder.php",
        data: { request: 'getmyorders' },
        dataType: 'json',
        success: function(response) {
            console.log('Orders response:', response);
            if (response.success && response.data) {
                ordersData = response.data;
                displayOrders(response.data);
                updateStats(response.data);
            } else {
                displayEmptyOrders();
            }
        },
        error: function(e) {
            console.log('Error loading orders:', e);
            displayEmptyOrders();
        }
    });
}

// Update order statistics
function updateStats(orders) {
    var total = orders.length;
    var completed = orders.filter(o => o.TrangThai == 5).length;
    var pending = orders.filter(o => o.TrangThai == 1 || o.TrangThai == 2 || o.TrangThai == 3 || o.TrangThai == 4).length;
    
    document.getElementById('totalOrders').textContent = total;
    document.getElementById('completedOrders').textContent = completed;
    document.getElementById('pendingOrders').textContent = pending;
}

// Display empty orders message
function displayEmptyOrders() {
    document.getElementById('ordersList').innerHTML = `
        <div class="empty-orders">
            <i class="fa fa-shopping-bag"></i>
            <h3>Chưa có đơn hàng nào</h3>
            <p>Hãy mua sắm để có đơn hàng đầu tiên của bạn!</p>
            <a href="Index.php" class="btn-shop">Mua sắm ngay</a>
        </div>
    `;
    
    document.getElementById('totalOrders').textContent = '0';
    document.getElementById('completedOrders').textContent = '0';
    document.getElementById('pendingOrders').textContent = '0';
}

// Display orders list
function displayOrders(orders) {
    if (!orders || orders.length === 0) {
        displayEmptyOrders();
        return;
    }
    
    var html = '';
    
    orders.forEach(function(order) {
        var statusIcon = getStatusIcon(order.TrangThai);
        var productsHtml = '';
        var maxDisplay = 2;
        
        // Display first 2 products
        for (var i = 0; i < Math.min(order.SanPham.length, maxDisplay); i++) {
            var sp = order.SanPham[i];
            productsHtml += `
                <div class="order-product-item">
                    <div class="product-image">
                        <img src="${sp.HinhAnh || 'img/products/default.png'}" alt="${sp.TenSP}">
                    </div>
                    <div class="product-details">
                        <div class="product-name">${sp.TenSP}</div>
                        <div class="product-quantity">Số lượng: ${sp.SoLuong}</div>
                    </div>
                    <div class="product-price">${formatPrice(sp.DonGia * sp.SoLuong)}</div>
                </div>
            `;
        }
        
        // Show more indicator if there are more products
        if (order.SanPham.length > maxDisplay) {
            productsHtml += `
                <div class="more-products">
                    + ${order.SanPham.length - maxDisplay} sản phẩm khác
                </div>
            `;
        }
        
        html += `
            <div class="order-card" data-order-id="${order.MaHD}">
                <div class="order-card-header">
                    <div class="order-info">
                        <span class="order-id">Đơn hàng</span>
                        <span class="order-date"><i class="fa fa-calendar"></i> ${formatDate(order.NgayLap)}</span>
                    </div>
                    <span class="order-status ${order.TrangThaiClass}">
                        <i class="fa ${statusIcon}"></i>
                        ${order.TrangThaiText}
                    </span>
                </div>
                <div class="order-products">
                    ${productsHtml}
                </div>
                <div class="order-card-footer">
                    <div class="order-total">
                        Tổng tiền: <strong>${formatPrice(order.TongTien)}</strong>
                    </div>
                    <button class="btn-view-detail" onclick="viewOrderDetail(${order.MaHD})">
                        Xem chi tiết
                    </button>
                </div>
            </div>
        `;
    });
    
    document.getElementById('ordersList').innerHTML = html;
}

// Get status icon
function getStatusIcon(status) {
    switch(parseInt(status)) {
        case 1: return 'fa-clock-o';
        case 2: return 'fa-check';
        case 3: return 'fa-list-alt';
        case 4: return 'fa-truck';
        case 5: return 'fa-check-circle';
        case 0: return 'fa-times-circle';
        default: return 'fa-question-circle';
    }
}

// Format price
function formatPrice(price) {
    return parseInt(price).toLocaleString('vi-VN') + ' ₫';
}

// Format date
function formatDate(dateString) {
    if (!dateString) return '';
    var date = new Date(dateString);
    return date.toLocaleDateString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// View order detail
function viewOrderDetail(orderId) {
    var order = ordersData.find(o => o.MaHD == orderId);
    if (!order) return;
    
    var productsHtml = '';
    order.SanPham.forEach(function(sp) {
        productsHtml += `
            <div class="detail-product-item">
                <div class="detail-product-image">
                    <img src="${sp.HinhAnh || 'img/products/default.png'}" alt="${sp.TenSP}">
                </div>
                <div class="detail-product-info">
                    <div class="detail-product-name">${sp.TenSP}</div>
                    <div class="detail-product-meta">Số lượng: ${sp.SoLuong} × ${formatPrice(sp.DonGia)}</div>
                </div>
                <div class="detail-product-price">${formatPrice(sp.DonGia * sp.SoLuong)}</div>
            </div>
        `;
    });
    
    var statusIcon = getStatusIcon(order.TrangThai);
    
    // Hiển thị trạng thái thanh toán
    var paymentStatusHtml = '';
    if (order.DaThanhToan == 1) {
        paymentStatusHtml = '<span class="payment-status paid"><i class="fa fa-check-circle"></i> Đã thanh toán</span>';
    } else {
        paymentStatusHtml = '<span class="payment-status unpaid"><i class="fa fa-clock-o"></i> Chưa thanh toán</span>';
    }
    
    // Hiển thị phương thức thanh toán đẹp hơn
    var paymentMethodText = order.PhuongThucTT === 'momo' ? 'Ví MoMo' : 'Thanh toán khi nhận hàng (COD)';
    
    document.getElementById('modalOrderId').textContent = '';
    document.getElementById('orderDetailBody').innerHTML = `
        <div class="detail-section">
            <div class="detail-section-title">Trạng thái đơn hàng</div>
            <div style="text-align: center; padding: 10px 0;">
                <span class="order-status ${order.TrangThaiClass}" style="font-size: 15px; padding: 10px 20px;">
                    <i class="fa ${statusIcon}"></i>
                    ${order.TrangThaiText}
                </span>
            </div>
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
                <span class="detail-value">${paymentMethodText}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Thanh toán:</span>
                <span class="detail-value">${paymentStatusHtml}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Ngày đặt:</span>
                <span class="detail-value">${formatDate(order.NgayLap)}</span>
            </div>
        </div>
        
        <div class="detail-section">
            <div class="detail-section-title">Sản phẩm</div>
            <div class="detail-product-list">
                ${productsHtml}
            </div>
            
            <div class="detail-total-row">
                <span class="detail-total-label">Tổng thanh toán</span>
                <span class="detail-total-value">${formatPrice(order.TongTien)}</span>
            </div>
        </div>
    `;
    
    document.getElementById('orderDetailModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

// Close order detail modal
function closeOrderDetail() {
    document.getElementById('orderDetailModal').classList.remove('active');
    document.body.style.overflow = '';
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.id === 'orderDetailModal') {
        closeOrderDetail();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeOrderDetail();
    }
});

// ======================= MY REWARDS FUNCTIONS =======================

// Load rewards data
function loadRewardsData() {
    if (!currentUser) {
        console.log('User not logged in, cannot load rewards');
        return;
    }
    
    $.ajax({
        type: "GET",
        url: "Api/ProcessRewards.php",
        data: { request: 'getmyrewards' },
        dataType: 'json',
        success: function(response) {
            console.log('Rewards response:', response);
            if (response.success) {
                rewardsData = response.data;
                displayRewardsData(response.data);
            }
        },
        error: function(e) {
            console.log('Error loading rewards:', e);
            // Show default values
            displayRewardsData({
                points: 0,
                totalSpent: 0,
                rank: 'Blue',
                memberSince: new Date().toISOString()
            });
        }
    });
}

// Display rewards data
function displayRewardsData(data) {
    // Points
    var points = data.points || 0;
    var myPointsEl = document.getElementById('myPoints');
    var pointsVndEl = document.getElementById('pointsVnd');
    
    if (myPointsEl) myPointsEl.textContent = points.toLocaleString('vi-VN');
    if (pointsVndEl) pointsVndEl.textContent = points.toLocaleString('vi-VN');
    
    // Rank
    var totalSpent = data.totalSpent || 0;
    var rank = calculateRank(totalSpent);
    
    var myRankEl = document.getElementById('myRank');
    if (myRankEl) {
        myRankEl.textContent = rank.name;
        myRankEl.className = 'rank-name ' + rank.class;
    }
    
    var rankBenefitEl = document.getElementById('rankBenefitName');
    if (rankBenefitEl) rankBenefitEl.textContent = rank.name;
    
    // Progress bar
    var progress = calculateProgress(totalSpent);
    var rankProgressEl = document.getElementById('rankProgress');
    var amountToGoldEl = document.getElementById('amountToGold');
    
    if (rankProgressEl) rankProgressEl.style.width = progress.percent + '%';
    if (amountToGoldEl) amountToGoldEl.textContent = formatCurrency(progress.amountToNext);
    
    // Update next rank text
    var rankNextEl = document.querySelector('.rank-next');
    if (rankNextEl) {
        if (rank.name === 'Platinum') {
            rankNextEl.innerHTML = 'Bạn đã đạt hạng cao nhất!';
        } else {
            var nextRankName = rank.name === 'Blue' ? 'Gold' : 'Platinum';
            var nextRankClass = rank.name === 'Blue' ? 'gold-text' : 'platinum-text';
            rankNextEl.innerHTML = formatCurrency(progress.amountToNext) + ' VND cho đến <span class="' + nextRankClass + '">' + nextRankName + '</span>';
        }
    }
    
    // Member since
    if (data.memberSince) {
        var date = new Date(data.memberSince);
        var memberSinceEl = document.getElementById('memberSince');
        if (memberSinceEl) {
            memberSinceEl.textContent = date.toLocaleDateString('vi-VN', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        }
    }
    
    // Update tier cards
    updateTierCards(rank.name);
}

// Calculate rank based on total spent
function calculateRank(totalSpent) {
    if (totalSpent >= 110000000) {
        return { name: 'Platinum', class: 'platinum', percent: 5 };
    } else if (totalSpent >= 45000000) {
        return { name: 'Gold', class: 'gold', percent: 3 };
    } else {
        return { name: 'Blue', class: 'blue', percent: 1 };
    }
}

// Calculate progress to next rank
function calculateProgress(totalSpent) {
    var result = {
        percent: 0,
        amountToNext: 0
    };
    
    if (totalSpent >= 110000000) {
        // Platinum - max rank
        result.percent = 100;
        result.amountToNext = 0;
    } else if (totalSpent >= 45000000) {
        // Gold - progress to Platinum
        var progressInTier = totalSpent - 45000000;
        var tierRange = 110000000 - 45000000;
        result.percent = (progressInTier / tierRange) * 100;
        result.amountToNext = 110000000 - totalSpent;
    } else {
        // Blue - progress to Gold
        result.percent = (totalSpent / 45000000) * 100;
        result.amountToNext = 45000000 - totalSpent;
    }
    
    return result;
}

// Update tier cards active state
function updateTierCards(rankName) {
    document.querySelectorAll('.tier-card').forEach(function(card) {
        card.classList.remove('active');
    });
    
    var activeCard = document.querySelector('.tier-card.' + rankName.toLowerCase());
    if (activeCard) {
        activeCard.classList.add('active');
    }
}

// Format currency
function formatCurrency(amount) {
    return parseInt(amount).toLocaleString('vi-VN');
}
