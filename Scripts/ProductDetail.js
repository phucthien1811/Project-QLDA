var nameProduct, maProduct; // Tên sản phẩm trong trang này, 
// // là biến toàn cục để có thể dùng ở bát cứ đâu trong trang
// // không cần tính toán lấy tên từ url nhiều lần

// Gallery variables
var currentImageIndex = 0;
var productImages = [];
var galleryAutoSlideInterval = null;
var autoSlideDelay = 3000; // 3 seconds

// Product selection variables
var selectedVariantId = null;
var selectedStorageId = null;
var selectedColorId = null;
var productVariants = [];
var currentProduct = null;
var productColors = [];

// Care Plan variables
var selectedCarePlanId = 0;
var selectedCarePlanPrice = 0;

window.onload = function() {
    khoiTao();

    // thêm tags (từ khóa) vào khung tìm kiếm
    var tags = ["Samsung", "iPhone", "Huawei", "Oppo", "Mobi"];
    for (var t of tags) addTags(t, "Index.php?search=" + t, true);

    phanTichURL_Web2();
    
    // Initialize scroll effects
    initScrollEffects();
    
    // Initialize sticky product header
    initStickyProductHeader();
    
    // Care Plans sẽ được load khi người dùng chọn variant
}

// ==================== Sticky Product Header ====================
function initStickyProductHeader() {
    var productHeader = document.getElementById('productHeader');
    if (!productHeader) return;
    
    var header = document.querySelector('header');
    var headerHeight = header ? header.offsetHeight : 0;
    
    window.addEventListener('scroll', function() {
        var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        // When at top (within 50px), show as normal
        if (scrollTop < 50) {
            productHeader.classList.add('at-top');
            productHeader.style.top = '0';
        } else {
            // When scrolled down, keep it sticky at top
            productHeader.classList.remove('at-top');
            productHeader.style.top = '0';
        }
    });
}

function scrollToConfig() {
    var stickyContainer = document.getElementById('stickyContainer');
    if (stickyContainer) {
        stickyContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// ==================== Gallery Functions ====================
function initGallery(images) {
    productImages = images;
    currentImageIndex = 0;
    updateGallery();
    updatePagination();
    startAutoSlide();
}

function startAutoSlide() {
    // Clear existing interval if any
    if (galleryAutoSlideInterval) {
        clearInterval(galleryAutoSlideInterval);
    }
    
    // Only start auto-slide if there are multiple images
    if (productImages.length > 1) {
        galleryAutoSlideInterval = setInterval(function() {
            nextImage();
        }, autoSlideDelay);
    }
}

function stopAutoSlide() {
    if (galleryAutoSlideInterval) {
        clearInterval(galleryAutoSlideInterval);
        galleryAutoSlideInterval = null;
    }
}

function resetAutoSlide() {
    stopAutoSlide();
    startAutoSlide();
}

function updateGallery() {
    var mainImg = document.getElementById('mainImage');
    if (mainImg && productImages.length > 0) {
        mainImg.src = productImages[currentImageIndex];
    }
}

function updatePagination() {
    var pagination = document.querySelector('.gallery-pagination');
    if (!pagination) return;
    
    pagination.innerHTML = '';
    for (var i = 0; i < productImages.length; i++) {
        var dot = document.createElement('span');
        dot.className = 'dot' + (i === currentImageIndex ? ' active' : '');
        dot.onclick = (function(index) {
            return function() { selectImage(index); };
        })(i);
        pagination.appendChild(dot);
    }
}

function nextImage() {
    if (productImages.length === 0) return;
    currentImageIndex = (currentImageIndex + 1) % productImages.length;
    updateGallery();
    updatePagination();
    resetAutoSlide(); // Reset timer when manually navigating
}

function prevImage() {
    if (productImages.length === 0) return;
    currentImageIndex = (currentImageIndex - 1 + productImages.length) % productImages.length;
    updateGallery();
    updatePagination();
    resetAutoSlide(); // Reset timer when manually navigating
}

function selectImage(index) {
    currentImageIndex = index;
    updateGallery();
    updatePagination();
    resetAutoSlide(); // Reset timer when manually selecting
}

// ======================= Web 2 =======================
function phanTichURL_Web2() {
    const urlParams = new URLSearchParams(window.location.search);
    var slug = urlParams.get('slug'); // Check for series product first
    maProduct = urlParams.get('masp'); // lấy giá trị của tham số masp
    
    // If it's a series product, load series detail
    if (slug) {
        console.log("Đang load series product với slug:", slug);
        loadSeriesProduct(slug);
        return;
    }
    
    // DEPRECATED: Không còn hỗ trợ sản phẩm cũ (không có slug)
    // Nếu không có slug, hiển thị thông báo và redirect về trang chủ
    if (!slug) {
        console.log("Sản phẩm cũ không còn được hỗ trợ - redirecting to homepage");
        Swal.fire({
            icon: 'warning',
            title: 'Sản phẩm không tồn tại',
            text: 'Sản phẩm này không còn tồn tại. Bạn sẽ được chuyển về trang chủ.',
            timer: 3000,
            showConfirmButton: false
        }).then(function() {
            window.location.href = 'Index.php';
        });
        return;
    }
}

// Load series product detail
function loadSeriesProduct(slug) {
    console.log('Loading series with slug:', slug);
    $.ajax({
        type: "POST",
        url: "Api/ProcessProductSeriesGet.php",
        dataType: "json",
        data: {
            request: "getseriesbyslug",
            slug: slug
        },
        success: function(response) {
            console.log('API Response:', response);
            if (response.success && response.data) {
                addSeriesChiTietToWeb(response.data);
            } else {
                alert("Không tìm thấy sản phẩm series");
                console.error('Error:', response.message);
            }
        },
        error: function(e) {
            console.error("Error loading series:", e);
            alert("Lỗi khi tải thông tin sản phẩm");
        }
    });
}

// Display series product detail
function addSeriesChiTietToWeb(seriesData) {
    console.log('Series Data:', seriesData);
    console.log('Series Name:', seriesData.series_name);
    
    var divChiTiet = document.getElementsByClassName('chitietSanpham')[0];
    if (!divChiTiet) return;
    
    // Save series info to window for cart
    window.currentSeriesId = seriesData.id;
    window.currentSeriesName = seriesData.series_name;
    
    // Update page title
    document.title = seriesData.series_name + ' - Thế giới điện thoại';
    
    // KHÔNG tự động chọn variant - để người dùng tự chọn
    var firstVariant = seriesData.variants && seriesData.variants.length > 0 ? seriesData.variants[0] : null;
    if (!firstVariant) {
        alert("Sản phẩm không có biến thể nào");
        return;
    }
    
    // Tính giá mặc định để hiển thị "Từ XX₫" (giá thấp nhất)
    var minPrice = 999999999;
    seriesData.variants.forEach(function(v) {
        if (v.storage_options && v.storage_options.length > 0) {
            v.storage_options.forEach(function(s) {
                var totalPrice = parseFloat(s.price) || 0;
                if (totalPrice < minPrice && totalPrice > 0) minPrice = totalPrice;
            });
        }
    });
    
    var price = minPrice;
    var installmentPrice = Math.round(price / 12);
    
    // Collect ALL images from ALL variants for gallery
    var allImages = [];
    seriesData.variants.forEach(function(variant) {
        if (variant.images && variant.images.length > 0) {
            variant.images.forEach(function(img) {
                allImages.push(img);
            });
        }
    });
    // Fallback to main image if no variant images
    if (allImages.length === 0 && seriesData.main_image) {
        allImages.push(seriesData.main_image);
    }
    
    // Build variant cards HTML (dùng cấu trúc giao diện cũ)
    var variantsHtml = seriesData.variants.map(function(variant, index) {
        // Tính giá hiển thị: lấy giá thấp nhất từ storage options
        var displayPrice = 0;
        
        // Lấy giá từ storage option thấp nhất
        if (variant.storage_options && variant.storage_options.length > 0) {
            var prices = variant.storage_options.map(s => parseFloat(s.price) || 0).filter(p => p > 0);
            displayPrice = prices.length > 0 ? Math.min(...prices) : 0;
        }
        
        // Tính giá trả góp
        var installmentPrice = Math.round(displayPrice / 12);
        
        // Lấy thông tin màn hình
        var screenInfo = variant.screen_size || 'Màn hình LTPO Super Retina XDR OLED';
        
        // Lấy promotion nếu có
        var promoText = '';
        if (variant.promotions && variant.promotions.length > 0) {
            promoText = variant.promotions.map(p => p.description).join(' • ');
        }
        
        return `
            <div class="variant-item" data-variant-id="${variant.id}" onclick="selectSeriesVariant(${variant.id})">
                <div class="variant-item-content">
                    <div class="variant-info">
                        <h3 class="variant-name">${variant.variant_name}</h3>
                        <p class="variant-screen">${screenInfo}</p>
                    </div>
                    <div class="variant-pricing">
                        <span class="variant-installment">${installmentPrice.toLocaleString()} ₫/tháng với lãi suất 0% hoặc</span>
                        <span class="variant-price">Từ ${displayPrice.toLocaleString()} ₫</span>
                    </div>
                </div>
                ${promoText ? `<div class="variant-trade-link">${promoText}</div>` : ''}
            </div>
        `;
    }).join('');
    
    // Build storage options HTML (dùng cấu trúc giao diện cũ)
    var storageHtml = firstVariant.storage_options.map(function(storage, index) {
        return `
            <div class="storage-item${index === 0 ? ' active' : ''}" data-storage-id="${storage.id}" onclick="selectSeriesStorage(${storage.id}, ${storage.price}, ${storage.installment_price || 0})">
                <div class="storage-info">
                    <span class="storage-capacity">${storage.storage} / ${storage.ram}</span>
                    <span class="storage-price">${parseInt(storage.price).toLocaleString()}₫</span>
                </div>
            </div>
        `;
    }).join('');
    
    // Create HTML structure (giống y hệt HtmlRenderer.php)
    divChiTiet.innerHTML = `
        <!-- Sticky Product Header Bar -->
        <div class="product-header" id="productHeader">
            <div class="product-header-content">
                <div class="product-header-left">
                    <span class="badge-new">Sản phẩm mới</span>
                    <h1 class="product-title" id="stickyProductTitle">${seriesData.series_name}</h1>
                </div>
                <div class="product-header-right">
                    <div class="sticky-price">
                        <p class="sticky-installment" id="stickyInstallment">Từ ${installmentPrice.toLocaleString()} ₫/tháng với lãi suất 0% hoặc</p>
                        <div class="sticky-total" id="stickyTotal">${price.toLocaleString()} ₫</div>
                    </div>
                    <div class="sticky-actions">
                        <a href="#" class="sticky-action-link">
                            <i class="fa fa-truck"></i>
                            <span>Giao hàng miễn phí</span>
                        </a>
                        <a href="#" class="sticky-action-link">
                            <i class="fa fa-shopping-bag"></i>
                            <span>Lấy tại cửa hàng</span>
                        </a>
                        <button class="sticky-btn-buy" onclick="scrollToConfig()">Mua ngay</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sticky Two Column Layout -->
        <div class="sticky-container" id="stickyContainer">
            <div class="sticky-layout">
                <!-- Left Column: Sticky Gallery -->
                <div class="sticky-gallery">
                    <div class="gallery-main">
                        <button class="gallery-nav prev" onclick="prevImage()">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <div class="gallery-images">
                            <img id="mainImage" src="${allImages[0]}" alt="${seriesData.series_name}" class="gallery-main-image">
                        </div>
                        <button class="gallery-nav next" onclick="nextImage()">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                    </div>
                    <div class="gallery-pagination"></div>
                </div>

                <!-- Right Column: Scrollable Options -->
                <div class="options-column">
                    <!-- Device Variants Section -->
                    <div class="config-section" data-section="variants">
                        <h2 class="section-title">Thiết bị</h2>
                        <p class="section-subtitle">Vui lòng chọn thiết bị</p>
                        <div class="variant-list" id="variantList">
                            ${variantsHtml}
                        </div>
                    </div>
                    
                    <!-- Storage Options Section - ẨN ban đầu cho đến khi chọn variant -->
                    <div class="config-section" data-section="storage" id="storageSection" style="display: none;">
                        <h2 class="section-title">Dung lượng</h2>
                        <p class="section-subtitle">Chọn dung lượng phù hợp</p>
                        <div class="storage-list" id="storageList">
                            ${storageHtml}
                        </div>
                    </div>
                    
                    <!-- Color Options Section - ẨN ban đầu cho đến khi chọn storage -->
                    <div class="config-section" data-section="colors" id="colorSection" style="display: none;">
                        <h2 class="section-title">Chọn màu sắc</h2>
                        <p class="section-subtitle">Màu sắc bạn yêu thích</p>
                        <div class="color-list" id="colorList">
                            <!-- Colors sẽ được load động khi chọn storage -->
                        </div>
                    </div>
                    
                    <!-- 3AM Care+ Section - ẨN cho đến khi chọn màu -->
                    <div class="config-section" data-section="careplan" id="carePlanSection" style="display: none;">
                        <h2 class="section-title">3AM Care+</h2>
                        <p class="section-subtitle">Bảo vệ thiết bị của bạn từ rơi vỡ hoặc rơi vào nước</p>
                        <div class="care-plans-list" id="carePlansList">
                            <!-- Care plans sẽ được load động -->
                        </div>
                    </div>
                    
                    <!-- Summary & Action Buttons -->
                    <div class="config-section" id="productActionsBottom" style="display: none;">
                        <div class="selected-summary">
                            <div class="summary-row">
                                <span class="summary-label">Cấu hình:</span>
                                <span class="summary-value" id="selectedConfigText">Chưa chọn</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Màu sắc:</span>
                                <span class="summary-value" id="selectedColorText">Chưa chọn</span>
                            </div>
                            <div class="summary-row" id="carePlanRow" style="display: none;">
                                <span class="summary-label">3AM Care+:</span>
                                <span class="summary-value" id="selectedCarePlanText">Không</span>
                            </div>
                            <div class="summary-row summary-total">
                                <span class="summary-label">Tổng tiền:</span>
                                <span class="summary-price" id="totalPriceText">0 ₫</span>
                            </div>
                        </div>
                        <button class="btn-add-to-cart" onclick="addSeriesProductToCart()">
                            <i class="fa fa-shopping-cart"></i>
                            Thêm vào giỏ hàng
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Ưu Đãi Độc Quyền Section -->
        <div class="marketing-section exclusive-offers" style="display: none;">
            <h2 class="marketing-title">Ưu đãi độc quyền</h2>
            <div class="offers-grid" id="offersGrid">
                <!-- Offers sẽ được load động -->
            </div>
        </div>
        
        <!-- Thông Số Kỹ Thuật Section -->
        <div class="marketing-section tech-specs-section" id="techSpecsSection" style="display: none;">
            <h2 class="marketing-title">Thông số kỹ thuật</h2>
            <div class="specs-table-large" id="specsTableContainer">
                <!-- Specs sẽ được render động -->
            </div>
        </div>
        
        <!-- Đánh Giá Sản Phẩm Section -->
        <div class="marketing-section reviews-section">
            <h2 class="marketing-title">Đánh giá sản phẩm</h2>
            
            <div class="reviews-container">
                <!-- Cột trái: Điểm tổng và phân bố sao -->
                <div class="reviews-summary">
                    <div class="reviews-score">
                        <div class="score-number" id="avgScore">5.0</div>
                        <div class="score-stars">
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                        </div>
                        <div class="score-count"><span id="totalReviews">0</span> đánh giá</div>
                    </div>
                    
                    <div class="reviews-filter">
                        <p class="filter-title">Kết xuất nhanh xếp loại</p>
                        <p class="filter-subtitle">Chọn một dòng dưới đây để lọc đánh giá.</p>
                        
                        <div class="rating-bars">
                            <div class="rating-bar-row" data-star="5">
                                <span class="bar-label">5 sao</span>
                                <div class="bar-track">
                                    <div class="bar-fill" id="bar5" style="width: 100%;"></div>
                                </div>
                                <span class="bar-count" id="count5">0</span>
                            </div>
                            <div class="rating-bar-row" data-star="4">
                                <span class="bar-label">4 sao</span>
                                <div class="bar-track">
                                    <div class="bar-fill" id="bar4" style="width: 0%;"></div>
                                </div>
                                <span class="bar-count" id="count4">0</span>
                            </div>
                            <div class="rating-bar-row" data-star="3">
                                <span class="bar-label">3 sao</span>
                                <div class="bar-track">
                                    <div class="bar-fill" id="bar3" style="width: 0%;"></div>
                                </div>
                                <span class="bar-count" id="count3">0</span>
                            </div>
                            <div class="rating-bar-row" data-star="2">
                                <span class="bar-label">2 sao</span>
                                <div class="bar-track">
                                    <div class="bar-fill" id="bar2" style="width: 0%;"></div>
                                </div>
                                <span class="bar-count" id="count2">0</span>
                            </div>
                            <div class="rating-bar-row" data-star="1">
                                <span class="bar-label">1 sao</span>
                                <div class="bar-track">
                                    <div class="bar-fill" id="bar1" style="width: 0%;"></div>
                                </div>
                                <span class="bar-count" id="count1">0</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Cột giữa: Xếp hạng tổng thể -->
                <div class="reviews-overall">
                    <h3 class="overall-title">Xếp hạng tổng thể</h3>
                    <p class="overall-subtitle">Đánh giá sản phẩm này</p>
                    
                    <div class="overall-stars" id="ratingStars">
                        <i class="fa fa-star-o" data-rating="1"></i>
                        <i class="fa fa-star-o" data-rating="2"></i>
                        <i class="fa fa-star-o" data-rating="3"></i>
                        <i class="fa fa-star-o" data-rating="4"></i>
                        <i class="fa fa-star-o" data-rating="5"></i>
                    </div>
                    
                    <p class="overall-note">Thêm đánh giá sẽ yêu cầu một email hợp lệ để xác minh</p>
                </div>
                
                <!-- Cột phải: Xếp loại chi tiết -->
                <div class="reviews-categories">
                    <h3 class="categories-title">Xếp loại khách hàng trung bình</h3>
                    
                    <div class="category-row">
                        <span class="category-label">Tính năng</span>
                        <div class="category-stars">
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                        </div>
                        <span class="category-score">5.0</span>
                    </div>
                    <div class="category-row">
                        <span class="category-label">Hiệu năng</span>
                        <div class="category-stars">
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star-half-o"></i>
                        </div>
                        <span class="category-score">4.8</span>
                    </div>
                    <div class="category-row">
                        <span class="category-label">Thiết kế</span>
                        <div class="category-stars">
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                        </div>
                        <span class="category-score">5.0</span>
                    </div>
                </div>
            </div>
            
            <!-- Form đánh giá -->
            <div class="review-form-section">
                <div class="review-input-row">
                    <div class="review-stars-input" id="reviewStarsInput">
                        <i class="fa fa-star" data-rating="1"></i>
                        <i class="fa fa-star" data-rating="2"></i>
                        <i class="fa fa-star" data-rating="3"></i>
                        <i class="fa fa-star" data-rating="4"></i>
                        <i class="fa fa-star" data-rating="5"></i>
                    </div>
                    <span class="review-count-label"><span id="userReviewCount">0</span> đánh giá</span>
                </div>
            </div>
            
            <!-- Danh sách đánh giá -->
            <div class="reviews-list" id="reviewsList">
                <!-- Reviews sẽ được load động -->
            </div>
        </div>
    `;
    
    // Initialize gallery with ALL images from ALL variants
    initGallery(allImages);
    
    // Store series data globally - KHÔNG set selectedVariantId và selectedStorageId
    window.currentSeriesData = seriesData;
    window.currentSeriesId = seriesData.id; // Lưu series ID
    window.selectedVariantId = null; // Chưa chọn variant nào
    window.selectedStorageId = null; // Chưa chọn storage nào
    window.currentPrice = minPrice; // Giá thấp nhất
    window.currentInstallmentPrice = installmentPrice;
    
    nameProduct = seriesData.series_name;
    
    // Initialize scroll effects for series product
    setTimeout(function() {
        initScrollEffects();
    }, 100);
    
    // Load care plans cho series này
    loadCarePlansForProduct(seriesData.id);
    
    // Load offers cho series này
    loadOffersForProduct(seriesData.id);
    
    // Render thông số kỹ thuật nếu có
    if (seriesData.specs_json) {
        renderTechSpecs(seriesData.specs_json);
    }
    
    // Init review stars interaction
    initReviewStars();
}

// Select series variant
function selectSeriesVariant(variantId) {
    var seriesData = window.currentSeriesData;
    if (!seriesData) return;
    
    var variant = seriesData.variants.find(v => v.id == variantId);
    if (!variant) return;
    
    window.selectedVariantId = variantId;
    window.currentVariantBasePrice = parseFloat(variant.base_price) || 0;
    
    // Update active variant
    document.querySelectorAll('.variant-item').forEach(el => el.classList.remove('active'));
    document.querySelector(`.variant-item[data-variant-id="${variantId}"]`).classList.add('active');
    
    // Cập nhật gallery với ảnh của variant được chọn
    var variantImages = variant.images || [seriesData.main_image];
    initGallery(variantImages);
    
    // Apply image effect when selecting variant
    var mainImage = document.getElementById('mainImage');
    if (mainImage) {
        applyImageEffect('variants', mainImage);
    }
    
    // Update storage options - Samsung style với trả góp
    var storageHtml = variant.storage_options.map(function(storage, index) {
        var totalPrice = parseFloat(storage.price) || 0;
        var colorsJson = JSON.stringify(storage.colors || []).replace(/"/g, '&quot;');
        var storageStock = parseInt(storage.stock_quantity) || 0;
        
        // Tính giá trả góp
        var installmentMonths = parseInt(storage.installment_months) || 12;
        var interestRate = parseFloat(storage.interest_rate) || 0;
        var installmentPrice = storage.installment_price > 0 
            ? parseInt(storage.installment_price) 
            : Math.round(totalPrice / installmentMonths);
        
        // Format giá
        var priceFormatted = parseInt(totalPrice).toLocaleString('vi-VN');
        var installmentFormatted = installmentPrice.toLocaleString('vi-VN');
        
        // Tạo text hiển thị trả góp
        var installmentText = interestRate === 0 
            ? `${installmentFormatted} ₫/tháng với lãi suất 0% hoặc`
            : `${installmentFormatted} ₫/tháng (${installmentMonths} tháng) hoặc`;
        
        // Check có promotion không
        var hasPromoClass = storage.promotion_text ? 'has-promo' : '';
        
        return `
            <div class="storage-item ${hasPromoClass}" data-storage-id="${storage.id}" 
                 data-colors='${JSON.stringify(storage.colors || [])}' 
                 data-stock="${storageStock}"
                 onclick="selectSeriesStorageWrapper(this, ${storage.id}, ${totalPrice}, ${storage.installment_price || 0}, 0, ${storageStock})">
                <div class="storage-info">
                    <div class="storage-capacity">${storage.storage} ${storage.ram ? '| ' + storage.ram : ''}</div>
                </div>
                <div class="storage-pricing">
                    <span class="storage-installment">${installmentText}</span>
                    <span class="storage-price">${priceFormatted} ₫</span>
                </div>
                ${storage.promotion_text ? 
                    `<div class="storage-promo-badge">${storage.promotion_text}</div>` : ''}
            </div>
        `;
    }).join('');
    
    document.getElementById('storageList').innerHTML = storageHtml;
    
    // Show storage section
    document.getElementById('storageSection').style.display = 'block';
    
    // Ẩn color section và nút add to cart khi chuyển variant
    document.getElementById('colorSection').style.display = 'none';
    document.getElementById('productActionsBottom').style.display = 'none';
    
    // KHÔNG tự động chọn storage - để người dùng tự chọn
    // Reset selected storage và color khi chuyển variant
    window.selectedStorageId = null;
}

// Wrapper function to handle colors from data attribute
function selectSeriesStorageWrapper(element, storageId, totalPrice, installmentPrice, additionalPrice, storageStock) {
    const colors = JSON.parse(element.getAttribute('data-colors') || '[]');
    const storageName = element.querySelector('.storage-capacity').textContent;
    selectSeriesStorage(storageId, totalPrice, installmentPrice, additionalPrice, colors, storageName, storageStock);
}

// Select series storage option
function selectSeriesStorage(storageId, totalPrice, installmentPrice, additionalPrice, colors, storageName, storageStock) {
    window.selectedStorageId = storageId;
    window.selectedColorId = null; // Reset color khi chọn storage khác
    window.currentColorStock = null; // Reset color stock
    window.currentPrice = parseInt(totalPrice);
    window.currentStorageAdditionalPrice = parseInt(additionalPrice || 0);
    window.currentInstallmentPrice = installmentPrice ? parseInt(installmentPrice) : Math.round(window.currentPrice / 12);
    window.currentColors = colors || [];
    window.currentStorageName = storageName || '';
    window.currentStorageStock = parseInt(storageStock) || 0; // Lưu tồn kho của storage
    
    // Update active storage
    document.querySelectorAll('.storage-item').forEach(el => el.classList.remove('active'));
    document.querySelector(`.storage-item[data-storage-id="${storageId}"]`).classList.add('active');
    
    // Apply image effect when selecting storage
    var mainImage = document.getElementById('mainImage');
    if (mainImage) {
        applyImageEffect('storage', mainImage);
    }
    
    // Update pricing display
    document.getElementById('stickyInstallment').textContent = 
        'Từ ' + window.currentInstallmentPrice.toLocaleString() + ' ₫/tháng với lãi suất 0% hoặc';
    document.getElementById('stickyTotal').textContent = window.currentPrice.toLocaleString() + ' ₫';
    
    // Update summary config text
    document.getElementById('selectedConfigText').textContent = storageName || 'Đã chọn';
    document.getElementById('totalPriceText').textContent = window.currentPrice.toLocaleString() + ' ₫';
    
    // Show color section và load colors
    const colorSection = document.getElementById('colorSection');
    const colorList = document.getElementById('colorList');
    
    if (colors && colors.length > 0) {
        colorSection.style.display = 'block';
        
        // Generate color HTML
        let colorsHtml = '';
        colors.forEach(color => {
            colorsHtml += `
                <div class="color-item" data-color-id="${color.id}" 
                     onclick="selectSeriesColor(${color.id}, ${color.stock_quantity}, '${color.color_name}')">
                    <div class="color-circle" style="background-color: ${color.color_code};" 
                         title="${color.color_name}"></div>
                    <span class="color-name">${color.color_name}</span>
                </div>
            `;
        });
        
        colorList.innerHTML = colorsHtml;
        document.getElementById('productActionsBottom').style.display = 'none'; // Ẩn nút cho đến khi chọn màu
    } else {
        // Không có màu nào, ẩn section
        colorSection.style.display = 'none';
        document.getElementById('productActionsBottom').style.display = 'none';
    }
}

// Chọn màu sắc
function selectSeriesColor(colorId, stockQuantity, colorName) {
    window.selectedColorId = colorId;
    window.currentColorName = colorName;
    window.currentColorStock = stockQuantity; // Lưu tồn kho của màu đã chọn
    
    // Update active color
    document.querySelectorAll('.color-item').forEach(el => el.classList.remove('active'));
    document.querySelector(`.color-item[data-color-id="${colorId}"]`).classList.add('active');
    
    // Apply flash effect when selecting color
    var mainImage = document.getElementById('mainImage');
    if (mainImage) {
        // Remove any existing effect classes
        mainImage.classList.remove('zoom-in', 'zoom-out', 'rotate-slight', 'flash');
        
        // Force reflow to restart animation
        void mainImage.offsetWidth;
        
        // Add flash effect
        mainImage.classList.add('flash');
        
        // Remove flash class after animation completes
        setTimeout(function() {
            mainImage.classList.remove('flash');
            // Then apply rotate effect
            applyImageEffect('colors', mainImage);
        }, 500);
    }
    
    // Update summary color text
    document.getElementById('selectedColorText').textContent = colorName;
    
    // Hiển thị 3AM Care+ section sau khi chọn màu
    showCarePlanSection();
    
    // Hiện nút Thêm vào giỏ hàng với summary
    document.getElementById('productActionsBottom').style.display = 'block';
}

// Add series product to cart
function addSeriesProductToCart() {
    if (!window.selectedVariantId || !window.selectedStorageId) {
        Swal.fire({
            icon: 'warning',
            title: 'Chưa chọn cấu hình',
            text: 'Vui lòng chọn thiết bị và dung lượng trước khi thêm vào giỏ hàng!',
            confirmButtonColor: '#0d6efd'
        });
        return;
    }
    
    // Kiểm tra màu sắc nếu có
    if (window.currentColors && window.currentColors.length > 0 && !window.selectedColorId) {
        Swal.fire({
            icon: 'warning',
            title: 'Chưa chọn màu sắc',
            text: 'Vui lòng chọn màu sắc cho sản phẩm!',
            confirmButtonColor: '#0d6efd'
        });
        return;
    }
    
    // ========== KIỂM TRA TỒN KHO ==========
    var currentStock = window.currentColorStock || window.currentStorageStock || 0;
    if (currentStock <= 0) {
        Swal.fire({
            icon: 'error',
            title: 'Sản phẩm hết hàng',
            html: '<div style="text-align:center;">' +
                  '<i class="fa fa-box-open" style="font-size:48px;color:#dc3545;margin-bottom:15px;"></i>' +
                  '<p>Rất tiếc, sản phẩm <strong>' + (window.currentColorName || window.currentStorageName) + '</strong> hiện đã hết hàng.</p>' +
                  '<p style="color:#666;font-size:14px;">Vui lòng chọn màu sắc/cấu hình khác hoặc quay lại sau.</p>' +
                  '</div>',
            confirmButtonText: 'Đã hiểu',
            confirmButtonColor: '#dc3545'
        });
        return;
    }
    // ========================================
    
    // Create a unique product identifier for series product (bao gồm care plan)
    var carePlanSuffix = selectedCarePlanId > 0 ? '_cp' + selectedCarePlanId : '';
    var seriesProductId = 'series_' + window.currentSeriesId + '_v' + window.selectedVariantId + '_s' + window.selectedStorageId + '_c' + (window.selectedColorId || '0') + carePlanSuffix;
    
    // Get product name with full config
    var fullProductName = window.currentSeriesName + ' - ' + window.currentStorageName;
    if (window.currentColorName) {
        fullProductName += ' - ' + window.currentColorName;
    }
    
    // Tính tổng giá bao gồm care plan
    var basePrice = window.currentPrice || 0;
    var carePlanPrice = selectedCarePlanPrice || 0;
    var totalPrice = basePrice + carePlanPrice;
    
    // Add to cart
    getCurrentUser((user) => {
        if(user && user.TrangThai == 0) {
            Swal.fire({
                title: 'Tài Khoản Bị Khóa!',
                text: 'Tài khoản của bạn hiện đang bị khóa nên không thể thêm hàng!',
                icon: 'error',
                confirmButtonText: 'Trở về'
            });
            return;
        }
        
        // Add to cart with series product info bao gồm care plan
        addToGioHangSeries(seriesProductId, {
            seriesId: window.currentSeriesId,
            variantId: window.selectedVariantId,
            storageId: window.selectedStorageId,
            colorId: window.selectedColorId,
            price: totalPrice,
            basePrice: basePrice,
            carePlanId: selectedCarePlanId || 0,
            carePlanPrice: carePlanPrice,
            name: fullProductName
        });
        
        animateCartNumber();
        
        var carePlanText = selectedCarePlanId > 0 ? ' + 3AM Care+' : '';
        Swal.fire({
            toast: true,
            position: 'bottom-end',
            icon: 'success',
            html: 'Đã thêm <strong>' + fullProductName + carePlanText + '</strong> vào giỏ.',
            showConfirmButton: true,
            timer: 5000
        });
        
    }, (error) => {
        console.log(error.responseText);
    });
}


function addChiTietToWeb(p) {
    var divChiTiet = document.getElementsByClassName('chitietSanpham')[0];
    
    // Save current product data
    currentProduct = p;

    // Đổi title
    document.title = p.TenSP + ' - Thế giới điện thoại';

    // Cập nhật tên sản phẩm ở sticky header
    var stickyProductTitle = document.getElementById('stickyProductTitle');
    if (stickyProductTitle) {
        stickyProductTitle.textContent = p.TenSP;
    }

    // Cập nhật thông tin trả góp
    var giaTri = parseInt(p.DonGia);
    var giaTrikhuyenMai = parseInt(p.KM.GiaTriKM);
    var giaTriSauKM = giaTri - giaTrikhuyenMai;
    var tienTraGop = Math.round(giaTriSauKM / 12);
    
    // Update sticky header pricing
    var stickyInstallment = document.getElementById('stickyInstallment');
    if (stickyInstallment) {
        stickyInstallment.textContent = 'Từ ' + tienTraGop.toLocaleString() + ' ₫/tháng với lãi suất 0% hoặc';
    }
    
    var stickyTotal = document.getElementById('stickyTotal');
    if (stickyTotal) {
        stickyTotal.textContent = giaTriSauKM.toLocaleString() + ' ₫';
    }

    // Khởi tạo gallery với ảnh sản phẩm
    var images = [p.HinhAnh];
    initGallery(images);

    // Initialize product variants
    initializeProductVariants(p);

    // Cập nhật phần cũ (giữ lại để tương thích)
    var area_price = divChiTiet.querySelector('.area_price');
    if (area_price) {
        if (p.KM.LoaiKM == "GiaReOnline") {
            area_price.innerHTML = `<strong>` + giaTriSauKM.toLocaleString() + `&#8363;</strong>
                    <span>` + giaTri.toLocaleString() + `&#8363;</span>`;
            area_price.innerHTML += promoToWeb(p.KM.LoaiKM, giaTriSauKM);
        } else {
            var ship = document.querySelector('.ship');
            if (ship) ship.style.display = '';
            
            var khuyenmaidiv = promoToWeb(p.KM.LoaiKM, giaTrikhuyenMai);
            area_price.innerHTML = `<strong>` + giaTri.toLocaleString() + `&#8363;</strong>` + khuyenmaidiv;
        }
    }

    // Cập nhật chi tiết khuyến mãi
    var detailPromo = document.getElementById('detailPromo');
    if (detailPromo) {
        detailPromo.innerHTML = getDetailPromo(p);
    }

    // Cập nhật thông số
    var info = document.getElementsByClassName('info')[0];
    if (info) {
        var s = addThongSo('Màn hình', p.ManHinh);
        s += addThongSo('Hệ điều hành', p.HDH);
        s += addThongSo('Camara sau', p.CamSau);
        s += addThongSo('Camara trước', p.CamTruoc);
        s += addThongSo('CPU', p.CPU);
        s += addThongSo('RAM', p.Ram);
        s += addThongSo('Bộ nhớ trong', p.Rom);
        s += addThongSo('Thẻ nhớ', p.SDCard);
        s += addThongSo('Dung lượng pin', p.Pin);
        info.innerHTML = s;
    }

    // Test bình luận
    refreshBinhLuan();
}

// ==================== Product Variants and Storage Functions ====================
function initializeProductVariants(product) {
    // Tạo dữ liệu demo cho variants dựa trên sản phẩm hiện tại
    productVariants = generateVariantsFromProduct(product);
    
    // Initialize colors
    productColors = generateProductColors(product);
    
    // Render variants
    renderVariants();
}

function generateProductColors(product) {
    // Tạo dữ liệu màu sắc demo
    var colors = [
        { id: 'black', name: 'Đen', hex: '#1a1a1a', image: product.HinhAnh },
        { id: 'white', name: 'Trắng', hex: '#f5f5f5', image: product.HinhAnh },
        { id: 'blue', name: 'Xanh Dương', hex: '#1e40af', image: product.HinhAnh },
        { id: 'pink', name: 'Hồng', hex: '#ec4899', image: product.HinhAnh },
        { id: 'gold', name: 'Vàng', hex: '#d4af37', image: product.HinhAnh }
    ];
    return colors;
}

function initScrollEffects() {
    var optionsColumn = document.querySelector('.options-column');
    if (!optionsColumn) return;
    
    var mainImage = document.getElementById('mainImage');
    if (!mainImage) return;
    
    var currentSection = -1;
    
    // Remove existing scroll listener if any
    if (window.scrollEffectHandler) {
        window.removeEventListener('scroll', window.scrollEffectHandler);
    }
    
    window.scrollEffectHandler = function() {
        // Get visible sections dynamically (re-query each time to handle hidden sections)
        var sections = document.querySelectorAll('.config-section');
        var visibleSections = [];
        
        sections.forEach(function(section) {
            if (section.style.display !== 'none' && section.offsetParent !== null) {
                visibleSections.push(section);
            }
        });
        
        if (visibleSections.length === 0) return;
        
        visibleSections.forEach(function(section, index) {
            var rect = section.getBoundingClientRect();
            var windowHeight = window.innerHeight;
            
            // Check if section is in viewport
            if (rect.top < windowHeight / 2 && rect.bottom > windowHeight / 2) {
                if (currentSection !== index) {
                    currentSection = index;
                    var sectionType = section.getAttribute('data-section') || '';
                    applyImageEffect(sectionType, mainImage);
                }
            }
        });
    };
    
    window.addEventListener('scroll', window.scrollEffectHandler);
    
    // Trigger once on init
    window.scrollEffectHandler();
}

function applyImageEffect(sectionType, imageElement) {
    // Remove all effect classes
    imageElement.classList.remove('zoom-in', 'zoom-out', 'rotate-slight');
    
    // Apply effect based on section type
    setTimeout(function() {
        switch(sectionType) {
            case 'variants': // Variants/Device section
                imageElement.classList.add('zoom-out');
                break;
            case 'storage': // Storage section
                imageElement.classList.add('zoom-in');
                break;
            case 'colors': // Colors section
                imageElement.classList.add('rotate-slight');
                break;
            default:
                // No effect for unknown sections
                break;
        }
    }, 50);
}

function generateVariantsFromProduct(product) {
    // Tạo 3 variants demo dựa trên sản phẩm
    var basePrice = parseInt(product.DonGia);
    var baseName = product.TenSP;
    var screenSize = product.ManHinh ? product.ManHinh.split(',')[0] : '6.7 inch';
    
    var variants = [];
    
    // Variant 1: Plus (giá cao hơn 40%)
    variants.push({
        id: 'variant_1',
        productId: product.MaSP,
        model_name: baseName + ' Plus',
        screen_size: screenSize,
        base_price: Math.round(basePrice * 1.4),
        promotions: ['Thu cũ đổi mới hỗ trợ 2 triệu'],
        storage_options: [
            {
                id: 'storage_1_1',
                storage: '128GB',
                ram: '8GB',
                price: Math.round(basePrice * 1.4),
                promotion_text: ''
            },
            {
                id: 'storage_1_2',
                storage: '256GB',
                ram: '8GB',
                price: Math.round(basePrice * 1.5),
                promotion_text: 'Ưu đãi 1 triệu'
            },
            {
                id: 'storage_1_3',
                storage: '512GB',
                ram: '12GB',
                price: Math.round(basePrice * 1.7),
                promotion_text: ''
            }
        ]
    });
    
    // Variant 2: Standard (giá gốc)
    variants.push({
        id: 'variant_2',
        productId: product.MaSP,
        model_name: baseName,
        screen_size: screenSize,
        base_price: basePrice,
        promotions: ['Thu cũ đổi mới hỗ trợ 1 triệu'],
        storage_options: [
            {
                id: 'storage_2_1',
                storage: '128GB',
                ram: '6GB',
                price: basePrice,
                promotion_text: ''
            },
            {
                id: 'storage_2_2',
                storage: '256GB',
                ram: '8GB',
                price: Math.round(basePrice * 1.15),
                promotion_text: 'Ưu đãi 500 nghìn'
            }
        ]
    });
    
    // Variant 3: Lite/FE (giá thấp hơn 20%)
    variants.push({
        id: 'variant_3',
        productId: product.MaSP,
        model_name: baseName + ' Lite',
        screen_size: screenSize,
        base_price: Math.round(basePrice * 0.8),
        promotions: ['Miễn phí giao hàng'],
        storage_options: [
            {
                id: 'storage_3_1',
                storage: '64GB',
                ram: '4GB',
                price: Math.round(basePrice * 0.75),
                promotion_text: ''
            },
            {
                id: 'storage_3_2',
                storage: '128GB',
                ram: '6GB',
                price: Math.round(basePrice * 0.8),
                promotion_text: 'Ưu đãi 300 nghìn'
            }
        ]
    });
    
    return variants;
}

function renderVariants() {
    var variantList = document.getElementById('variantList');
    if (!variantList) return;
    
    variantList.innerHTML = '';
    
    productVariants.forEach(function(variant, index) {
        var installmentPrice = Math.round(variant.base_price / 12);
        
        var variantHtml = `
            <div class="variant-item" data-variant-id="${variant.id}" onclick="selectVariant('${variant.id}')">
                <div class="variant-item-content">
                    <div class="variant-info">
                        <h3 class="variant-name">${variant.model_name}</h3>
                        <p class="variant-screen">Màn hình ${variant.screen_size}</p>
                    </div>
                    <div class="variant-pricing">
                        <span class="variant-installment">${installmentPrice.toLocaleString()} ₫/tháng với lãi suất 0% hoặc</span>
                        <span class="variant-price">${variant.base_price.toLocaleString()} ₫</span>
                    </div>
                </div>
                ${variant.promotions && variant.promotions.length > 0 ? 
                    `<div class="variant-trade-link">${variant.promotions.join(' • ')}</div>` : ''}
            </div>
        `;
        
        variantList.innerHTML += variantHtml;
    });
}

function selectVariant(variantId) {
    selectedVariantId = variantId;
    selectedStorageId = null; // Reset storage selection
    
    // Update UI - remove active class from all variants
    var allVariants = document.querySelectorAll('.variant-item');
    allVariants.forEach(function(item) {
        item.classList.remove('active');
    });
    
    // Add active class to selected variant
    var selectedVariant = document.querySelector('[data-variant-id="' + variantId + '"]');
    if (selectedVariant) {
        selectedVariant.classList.add('active');
    }
    
    // Load storage options for this variant
    var variant = productVariants.find(function(v) { return v.id === variantId; });
    if (variant) {
        renderStorageOptions(variant);
        
        // Show storage section with animation
        var storageSection = document.getElementById('storageSection');
        if (storageSection) {
            storageSection.style.display = 'block';
        }
    }
    
    // Hide action buttons until storage is selected
    var actionsBottom = document.getElementById('productActionsBottom');
    if (actionsBottom) {
        actionsBottom.style.display = 'none';
    }
}

function renderStorageOptions(variant) {
    var storageList = document.getElementById('storageList');
    if (!storageList) return;
    
    storageList.innerHTML = '';
    
    variant.storage_options.forEach(function(storage) {
        // Tính giá trả góp: ưu tiên installment_price từ DB, nếu không có thì tính từ giá bán
        var installmentMonths = parseInt(storage.installment_months) || 12;
        var interestRate = parseFloat(storage.interest_rate) || 0;
        var installmentPrice = storage.installment_price > 0 
            ? parseInt(storage.installment_price) 
            : Math.round(storage.price / installmentMonths);
        
        // Format giá
        var priceFormatted = parseInt(storage.price).toLocaleString('vi-VN');
        var installmentFormatted = installmentPrice.toLocaleString('vi-VN');
        
        // Tạo text hiển thị trả góp
        var installmentText = interestRate === 0 
            ? `${installmentFormatted} ₫/tháng với lãi suất 0% hoặc`
            : `${installmentFormatted} ₫/tháng (${installmentMonths} tháng) hoặc`;
        
        // Check có promotion không để thêm class
        var hasPromoClass = storage.promotion_text ? 'has-promo' : '';
        
        var storageHtml = `
            <div class="storage-item ${hasPromoClass}" data-storage-id="${storage.id}" onclick="selectStorage('${storage.id}')">
                <div class="storage-info">
                    <div class="storage-capacity">${storage.storage} ${storage.ram ? '| ' + storage.ram : ''}</div>
                </div>
                <div class="storage-pricing">
                    <span class="storage-installment">${installmentText}</span>
                    <span class="storage-price">${priceFormatted} ₫</span>
                </div>
                ${storage.promotion_text ? 
                    `<div class="storage-promo-badge">${storage.promotion_text}</div>` : ''}
            </div>
        `;
        
        storageList.innerHTML += storageHtml;
    });
}

function selectStorage(storageId) {
    selectedStorageId = storageId;
    
    // Update UI - remove active class from all storage items
    var allStorage = document.querySelectorAll('.storage-item');
    allStorage.forEach(function(item) {
        item.classList.remove('active');
    });
    
    // Add active class to selected storage
    var selectedStorage = document.querySelector('[data-storage-id="' + storageId + '"]');
    if (selectedStorage) {
        selectedStorage.classList.add('active');
    }
    
    // Show color section
    showColorSection();
    
    // Update summary
    updateSelectedSummary();
}

function showColorSection() {
    var colorSection = document.getElementById('colorSection');
    if (colorSection) {
        colorSection.style.display = 'block';
        renderColors();
    }
}

function renderColors() {
    var colorList = document.getElementById('colorList');
    if (!colorList) return;
    
    colorList.innerHTML = '';
    
    productColors.forEach(function(color) {
        var colorHtml = `
            <div class="color-item" data-color-id="${color.id}" onclick="selectColor('${color.id}')">
                <div class="color-swatch" style="background-color: ${color.hex}"></div>
                <div class="color-name">${color.name}</div>
            </div>
        `;
        colorList.innerHTML += colorHtml;
    });
}

function selectColor(colorId) {
    selectedColorId = colorId;
    
    // Update UI - remove active class from all colors
    var allColors = document.querySelectorAll('.color-item');
    allColors.forEach(function(item) {
        item.classList.remove('active');
    });
    
    // Add active class to selected color
    var selectedColorItem = document.querySelector('[data-color-id="' + colorId + '"]');
    if (selectedColorItem) {
        selectedColorItem.classList.add('active');
    }
    
    // Update main image with color
    var color = productColors.find(function(c) { return c.id === colorId; });
    if (color && color.image) {
        var mainImage = document.getElementById('mainImage');
        if (mainImage) {
            // Remove any existing effect classes
            mainImage.classList.remove('zoom-in', 'zoom-out', 'rotate-slight', 'flash');
            
            // Force reflow to restart animation
            void mainImage.offsetWidth;
            
            // Add flash effect
            mainImage.classList.add('flash');
            
            // Change image after flash starts
            setTimeout(function() {
                mainImage.src = color.image;
            }, 150);
            
            // Remove flash class after animation completes, then apply rotate
            setTimeout(function() {
                mainImage.classList.remove('flash');
                applyImageEffect('colors', mainImage);
            }, 500);
        }
    }
    
    // Update summary and show action buttons
    updateSelectedSummary();
    
    // Show action buttons
    var actionsBottom = document.getElementById('productActionsBottom');
    if (actionsBottom) {
        actionsBottom.style.display = 'block';
    }
}

function updateSelectedSummary() {
    if (!selectedVariantId || !selectedStorageId) return;
    
    var variant = productVariants.find(function(v) { return v.id === selectedVariantId; });
    if (!variant) return;
    
    var storage = variant.storage_options.find(function(s) { return s.id === selectedStorageId; });
    if (!storage) return;
    
    // Update config text
    var configValue = document.getElementById('selectedConfig');
    if (configValue) {
        configValue.textContent = variant.model_name + ' - ' + storage.storage + ' | ' + storage.ram;
    }
    
    // Update color text
    if (selectedColorId) {
        var color = productColors.find(function(c) { return c.id === selectedColorId; });
        var colorValue = document.getElementById('selectedColor');
        if (colorValue && color) {
            colorValue.textContent = color.name;
        }
    }
    
    // Lưu giá sản phẩm để tính tổng với care plan
    window.currentProductPrice = storage.price;
    
    // Update price - gọi hàm updateTotalPrice để tính cả care plan
    updateTotalPrice();
    
    // Update sticky header price (tổng bao gồm care plan)
    var stickyTotal = document.getElementById('stickyTotal');
    if (stickyTotal) {
        var totalWithCarePlan = storage.price + (selectedCarePlanPrice || 0);
        stickyTotal.textContent = totalWithCarePlan.toLocaleString() + ' ₫';
    }
    
    var stickyInstallment = document.getElementById('stickyInstallment');
    if (stickyInstallment) {
        var totalWithCarePlan = storage.price + (selectedCarePlanPrice || 0);
        var installmentPrice = Math.round(totalWithCarePlan / 12);
        stickyInstallment.textContent = 'Từ ' + installmentPrice.toLocaleString() + ' ₫/tháng với lãi suất 0% hoặc';
    }
}

function addToCartWithConfig() {
    if (!selectedVariantId || !selectedStorageId) {
        Swal.fire({
            title: 'Chưa chọn đủ!',
            text: 'Vui lòng chọn phiên bản và dung lượng trước khi thêm vào giỏ hàng',
            type: 'warning',
            confirmButtonText: 'Đã hiểu'
        });
        return;
    }
    
    if (!selectedColorId) {
        Swal.fire({
            title: 'Chưa chọn màu!',
            text: 'Vui lòng chọn màu sắc cho sản phẩm',
            type: 'warning',
            confirmButtonText: 'Đã hiểu'
        });
        return;
    }
    
    var variant = productVariants.find(function(v) { return v.id === selectedVariantId; });
    if (!variant) return;
    
    var storage = variant.storage_options.find(function(s) { return s.id === selectedStorageId; });
    if (!storage) return;
    
    var color = productColors.find(function(c) { return c.id === selectedColorId; });
    if (!color) return;
    
    // Create product name with full configuration
    var fullProductName = variant.model_name + ' (' + storage.storage + ' - ' + storage.ram + ') - ' + color.name;
    
    // Tính tổng giá bao gồm care plan
    var totalPrice = storage.price + (selectedCarePlanPrice || 0);
    
    // Build config object với care plan
    var config = {
        variantId: selectedVariantId,
        storageId: selectedStorageId,
        colorId: selectedColorId,
        colorName: color.name,
        storageName: storage.storage,
        ramName: storage.ram,
        productName: fullProductName,
        basePrice: storage.price,
        carePlanId: selectedCarePlanId || 0,
        carePlanPrice: selectedCarePlanPrice || 0,
        totalPrice: totalPrice
    };
    
    // Add to cart with config
    themVaoGioHangWithCarePlan(variant.productId, fullProductName, config);
}

// Hàm thêm vào giỏ hàng với care plan
function themVaoGioHangWithCarePlan(masp, tensp, config) {
    getCurrentUser((user) => {
        if(user && user.TrangThai == 0) {
            Swal.fire({
                title: 'Tài Khoản Bị Khóa!',
                text: 'Tài khoản của bạn hiện đang bị khóa nên không thể thêm hàng!',
                type: 'error',
                grow: 'row',
                confirmButtonText: 'Trở về',
                footer: '<a href>Liên hệ với Admin</a>'
            });
            return;
        } else {
            addToGioHangWithCarePlan(masp, config);
            animateCartNumber();
            
            var carePlanText = config.carePlanId > 0 ? ' + 3AM Care+' : '';
            Swal.fire({
                toast: true,
                position: 'bottom-end',
                type: 'success',
                html: ' Đã thêm <strong>' + tensp + carePlanText + '</strong> vào giỏ.',
                showConfirmButton: true,
                timer: 5000
            })
        }
    }, (error) => {
       console.log(error.responseText)
    });
    return false;
}

// Hàm thêm vào localStorage với care plan
function addToGioHangWithCarePlan(masp, config) {
    getCurrentUser((data) => {
        if (!data) {
            Swal.fire({
                icon: 'warning',
                title: 'Chưa đăng nhập',
                text: 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng',
                confirmButtonText: 'Đăng nhập',
                showCancelButton: true,
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.value) {
                    window.location.href = 'SignIn.php';
                }
            });
            return;
        }
        
        var currentList = getListGioHang();
        if(!currentList) {
            currentList = [];
        }

        // Tạo unique ID với care plan
        var uniqueId = masp + '_care' + (config.carePlanId || 0);
        
        var daCo = false;
        for(var sp of currentList) {
            if(sp.uniqueId == uniqueId) {
                sp.soLuong++;
                daCo = true;
                break;
            }
        }

        if(!daCo) {
            currentList.push({
                masp: masp,
                uniqueId: uniqueId,
                soLuong: 1,
                isSeries: true,
                seriesConfig: config
            })
        }

        setListGioHang(currentList);
        
        var cartNumber = document.getElementsByClassName('cart-number')[0];
        if (cartNumber) {
            var count = getSoLuongGioHang();
            cartNumber.innerHTML = count > 0 ? count : '';
            cartNumber.setAttribute('data-count', count);
        }
    });
}

function checkGuiBinhLuan() {
    getCurrentUser((user) => {
        if(user == null) {
            Swal.fire({
                title: 'Xin chào!',
                text: 'Bạn cần đăng nhập để binh luận',
                type: 'error',
                grow: 'row',
                confirmButtonText: 'Đăng nhập',
                cancelButtonText: 'Trở về',
                showCancelButton: true
            }).then((result) => {
                if (result.value) {
                    showTaiKhoan(true);
                }
            })
        } else {
            guiBinhLuan(user);
        }

    }, (error) => {
        Swal.fire({
            title: 'Lỗi!',
            text: 'Có lỗi khi đăng đánh giá',
            type: 'error',
            grow: 'row'
        })
    })
}

function guiBinhLuan(nguoidung) {
    var soSao = $("input[name='star']:checked").val();
    var binhLuan = $("#inpBinhLuan").val();

    if(!soSao) {
        Swal.fire({
            title: 'Thiếu!',
            text: 'Bạn vui lòng đánh số sao',
            type: 'warning',
            grow: 'row'
        })
        return;
    }

    if(!binhLuan) {
        Swal.fire({
            title: 'Thiếu!',
            text: 'Bạn vui lòng để lại bình luận',
            type: 'warning',
            grow: 'row'
        })
        $("#inpBinhLuan")[0].focus();
        return;
    }


    $.ajax({
        type: "POST",
        url: "Api/ProcessReview.php",
        dataType: "json",
        timeout: 1500, // sau 1.5 giây mà không phản hồi thì dừng => hiện lỗi
        data: {
            request: "thembinhluan",
            masp: maProduct,
            mand: nguoidung.MaND,
            sosao: soSao,
            binhluan: binhLuan,
            thoigian: new Date().toMysqlFormat()
        },
        success: function(data, status, xhr) {
            $("#inpBinhLuan").val("");
            refreshBinhLuan();
        },
        error: function(e) {
            console.log(e);
        }
    })
}

function refreshBinhLuan() {
    $.ajax({
        type: "POST",
        url: "Api/ProcessReview.php",
        dataType: "json",
        timeout: 1500, // sau 1.5 giây mà không phản hồi thì dừng => hiện lỗi
        data: {
            request: "getbinhluan",
            masp: maProduct
        },
        success: function(data, status, xhr) {
            var div = document.getElementsByClassName("comment-content")[0];
            div.innerHTML = "";
            for(var b of data) {
                div.innerHTML += createComment(b.ND.TaiKhoan, b.BinhLuan, getRateStar(b.SoSao), b.NgayLap);
            }
        },
        error: function(e) {
            console.log(e);
        }
    })
}

// =====================================================

function getRateStar(num) {
    var result = "";
    for (var i = 1; i <= 5; i++) {
        if (i <= num) {
            result += `<i class="fa fa-star"></i>`
        } else {
            result += `<i class="fa fa-star-o"></i>`
        }
    }
    return result;
}

// Chi tiết khuyến mãi
function getDetailPromo(sp) {
    switch (sp.KM.LoaiKM) {
        case 'tragop':
            var span = `<span style="font-weight: bold"> lãi suất ` + sp.KM.GiaTriKM + `% </span>`;
            return `Khách hàng có thể mua trả góp sản phẩm với ` + span + `với thời hạn 6 tháng kể từ khi mua hàng.`;

        case 'giamgia':
            var span = `<span style="font-weight: bold">` + Number(sp.KM.GiaTriKM).toLocaleString() + `</span>`;
            return `Khách hàng sẽ được giảm ` + span + `₫ khi tới mua trực tiếp tại cửa hàng`;

        case 'moiramat':
            return `Khách hàng sẽ được thử máy miễn phí tại cửa hàng. Có thể đổi trả lỗi trong vòng 2 tháng.`;

        case 'giareonline':
            var del = Number(p.DonGia) - Number(p.KM.GiaTriKM);
            var span = `<span style="font-weight: bold">` + numToString(del) + `</span>`;
            return `Sản phẩm sẽ được giảm ` + span + `₫ khi mua hàng online bằng thẻ VPBank hoặc tin nhắn SMS`;

        default:
            var span = `<span style="font-weight: bold">61 xe Wave Alpha</span>`;
            return `Cơ hội trúng ` + span + ` khi trả góp Home Credit`;
    }
}

function addThongSo(ten, giatri) {
    return `<li>
                <p>` + ten + `</p>
                <div>` + giatri + `</div>
            </li>`;
}

function createComment(name, value, star, time) {
    return `<div class="comment">
                <i class="fa fa-user-circle"> </i>
                <h4>` + name + `<span> `+ star +`</span></h4>
                <p>` + value + `</p>
                <span class="time">` + time + `</span>
            </div>`;
}

// ==================== 3AM Care Plans ====================

// Hiển thị section 3AM Care+ sau khi chọn màu
function showCarePlanSection() {
    var carePlanSection = document.getElementById('carePlanSection');
    if (!carePlanSection) return;
    
    // Load care plans nếu chưa load
    if (window.currentSeriesId) {
        loadCarePlansInline(window.currentSeriesId);
        carePlanSection.style.display = 'block';
    }
}

// Load care plans vào inline section
function loadCarePlansInline(seriesId) {
    if (!seriesId) {
        $('#carePlansList').html('');
        return;
    }
    
    $.ajax({
        url: 'Api/ProcessCarePlan.php?action=get_by_series&series_id=' + seriesId,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data && response.data.length > 0) {
                renderCarePlansInline(response.data);
            } else {
                // Chỉ hiện option "Không, cảm ơn"
                var html = '<div class="care-plan-item selected" data-careplan-id="0" data-price="0" onclick="selectCarePlanInline(this)">' +
                            '<div class="care-plan-info"><span class="care-plan-name">Không, cảm ơn</span></div>' +
                          '</div>';
                $('#carePlansList').html(html);
            }
        },
        error: function() {
            console.log('Không thể load care plans');
            $('#carePlansList').html('');
        }
    });
}

function renderCarePlansInline(plans) {
    var html = '';
    plans.forEach(function(plan) {
        var priceFormatted = new Intl.NumberFormat('vi-VN').format(plan.final_price) + ' ₫';
        var discountText = plan.discount_percent > 0 ? ' - Giảm ' + plan.discount_percent + '%' : '';
        
        html += '<div class="care-plan-item" data-careplan-id="' + plan.id + '" data-price="' + plan.final_price + '" data-name="' + plan.name + '" onclick="selectCarePlanInline(this)">' +
                    '<div class="care-plan-info">' +
                        '<span class="care-plan-name">' + plan.name + discountText + '</span>' +
                    '</div>' +
                    '<div class="care-plan-price">' + priceFormatted + '</div>' +
                '</div>';
    });
    
    // Thêm option "Không, cảm ơn" - được chọn mặc định
    html += '<div class="care-plan-item selected" data-careplan-id="0" data-price="0" onclick="selectCarePlanInline(this)">' +
                '<div class="care-plan-info"><span class="care-plan-name">Không, cảm ơn</span></div>' +
            '</div>';
    
    $('#carePlansList').html(html);
}

function selectCarePlanInline(element) {
    // Remove selected class từ tất cả
    $('.care-plan-item').removeClass('selected');
    // Add selected class cho item được chọn
    $(element).addClass('selected');
    
    // Lưu thông tin
    selectedCarePlanId = parseInt($(element).data('careplan-id')) || 0;
    selectedCarePlanPrice = parseFloat($(element).data('price')) || 0;
    var carePlanName = $(element).data('name') || 'Không';
    
    // Cập nhật summary
    var carePlanRow = document.getElementById('carePlanRow');
    var carePlanText = document.getElementById('selectedCarePlanText');
    if (carePlanRow && carePlanText) {
        if (selectedCarePlanId > 0) {
            carePlanRow.style.display = 'flex';
            carePlanText.textContent = carePlanName + ' (+' + new Intl.NumberFormat('vi-VN').format(selectedCarePlanPrice) + ' ₫)';
        } else {
            carePlanRow.style.display = 'none';
            carePlanText.textContent = 'Không';
        }
    }
    
    // Cập nhật tổng tiền
    updateTotalPrice();
}

// Cập nhật tổng tiền bao gồm care plan
function updateTotalPrice() {
    // Dùng window.currentPrice (được set khi chọn storage/color)
    var productPrice = window.currentPrice || 0;
    var carePlanPrice = selectedCarePlanPrice || 0;
    var total = productPrice + carePlanPrice;
    
    // Update summary totalPrice
    var totalPriceEl = document.getElementById('totalPrice');
    if (totalPriceEl) {
        totalPriceEl.textContent = new Intl.NumberFormat('vi-VN').format(total) + ' ₫';
    }
    
    // Update totalPriceText if exists
    var totalPriceText = document.getElementById('totalPriceText');
    if (totalPriceText) {
        totalPriceText.textContent = new Intl.NumberFormat('vi-VN').format(total) + ' ₫';
    }
    
    // Update sticky header price
    var stickyTotal = document.getElementById('stickyTotal');
    if (stickyTotal) {
        stickyTotal.textContent = new Intl.NumberFormat('vi-VN').format(total) + ' ₫';
    }
    
    // Update sticky installment
    var stickyInstallment = document.getElementById('stickyInstallment');
    if (stickyInstallment) {
        var installmentPrice = Math.round(total / 12);
        stickyInstallment.textContent = 'Từ ' + new Intl.NumberFormat('vi-VN').format(installmentPrice) + ' ₫/tháng với lãi suất 0% hoặc';
    }
}

function loadCarePlansForProduct(seriesId) {
    // Nếu không có seriesId, ẩn section care plans
    if (!seriesId) {
        $('#carePlansGrid').html('');
        return;
    }
    
    $.ajax({
        url: 'Api/ProcessCarePlan.php?action=get_by_series&series_id=' + seriesId,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data && response.data.length > 0) {
                renderCarePlans(response.data);
                // KHÔNG hiển thị section cũ nữa - đã chuyển sang inline
                // Section .carePlus giữ ẩn, chỉ dùng carePlanSection inline
            } else {
                // Ẩn section nếu không có care plans
                var carePlusSection = document.querySelector('.carePlus');
                if (carePlusSection) {
                    carePlusSection.style.display = 'none';
                }
                $('#carePlansGrid').html('');
            }
        },
        error: function() {
            console.log('Không thể load care plans');
            $('#carePlansGrid').html('');
        }
    });
}

function renderCarePlans(plans) {
    var html = '';
    plans.forEach(function(plan) {
        var priceFormatted = new Intl.NumberFormat('vi-VN').format(plan.final_price) + ' ₫';
        var discountText = plan.discount_percent > 0 ? ' - Giảm ' + plan.discount_percent + '%' : '';
        
        html += '<div class="care-plan-card" data-careplan-id="' + plan.id + '" data-price="' + plan.final_price + '" onclick="selectCarePlan(this)">' +
                    '<h3 class="care-plan-title">' + plan.name + discountText + '</h3>' +
                    '<div class="care-plan-price">' + priceFormatted + '</div>' +
                '</div>';
    });
    
    // Thêm option "Không, cảm ơn"
    html += '<div class="care-plan-card care-no-thanks" data-careplan-id="0" onclick="selectCarePlan(this)">' +
                '<h3 class="care-plan-title">Không, cảm ơn</h3>' +
            '</div>';
    
    $('#carePlansGrid').html(html);
}

function selectCarePlan(element) {
    // Remove selected class từ tất cả
    $('.care-plan-card').removeClass('selected');
    // Add selected class cho card được chọn
    $(element).addClass('selected');
    
    // Lưu thông tin
    selectedCarePlanId = $(element).data('careplan-id') || 0;
    selectedCarePlanPrice = parseFloat($(element).data('price')) || 0;
    
    // Cập nhật tổng tiền nếu cần
    if (typeof updateTotalPrice === 'function') {
        updateTotalPrice();
    }
}

// ==================== Ưu Đãi Độc Quyền ====================
function loadOffersForProduct(seriesId) {
    if (!seriesId) {
        $('#offersGrid').html('');
        return;
    }
    
    $.ajax({
        url: 'Api/ProcessOffers.php?action=get_by_series&series_id=' + seriesId,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data && response.data.length > 0) {
                renderOffers(response.data);
                // Hiển thị section Ưu đãi nếu có dữ liệu
                var offersSection = document.querySelector('.exclusive-offers');
                if (offersSection) {
                    offersSection.style.display = 'block';
                }
            } else {
                // Ẩn section nếu không có offers
                var offersSection = document.querySelector('.exclusive-offers');
                if (offersSection) {
                    offersSection.style.display = 'none';
                }
                $('#offersGrid').html('');
            }
        },
        error: function() {
            console.log('Không thể load offers');
            $('#offersGrid').html('');
        }
    });
}

function renderOffers(offers) {
    var html = '';
    offers.forEach(function(offer) {
        var linkHtml = '';
        if (offer.link_text) {
            var linkUrl = offer.link_url || '#';
            linkHtml = '<a href="' + linkUrl + '" class="offer-card-link">' + offer.link_text + '</a>';
        }
        
        var descHtml = '';
        if (offer.description) {
            descHtml = '<p class="offer-card-desc">' + offer.description + '</p>';
        }
        
        html += '<div class="offer-card">' +
                    '<div class="offer-badge">' + offer.badge + '</div>' +
                    '<h3 class="offer-card-title">' + offer.title + '</h3>' +
                    descHtml +
                    linkHtml +
                '</div>';
    });
    
    $('#offersGrid').html(html);
}

// ==================== Thông Số Kỹ Thuật ====================
function renderTechSpecs(specsJson) {
    var specs = null;
    
    // Parse JSON nếu là string
    if (typeof specsJson === 'string') {
        try {
            specs = JSON.parse(specsJson);
        } catch (e) {
            console.log('Lỗi parse specs JSON:', e);
            return;
        }
    } else {
        specs = specsJson;
    }
    
    if (!specs || Object.keys(specs).length === 0) {
        return;
    }
    
    var html = '<table class="specs-table">';
    
    for (var key in specs) {
        if (specs.hasOwnProperty(key)) {
            html += '<tr class="specs-row">' +
                        '<td class="specs-label">' + key + '</td>' +
                        '<td class="specs-value">' + specs[key] + '</td>' +
                    '</tr>';
        }
    }
    
    html += '</table>';
    
    $('#specsTableContainer').html(html);
    
    // Hiển thị section
    var specsSection = document.getElementById('techSpecsSection');
    if (specsSection) {
        specsSection.style.display = 'block';
    }
}

/*// add hình
function addSmallImg(img) {
    var newDiv = `<div class='item'>
                        <a>
                            <img src=` + img + ` onclick="changepic(this.src)">
                        </a>
                    </div>`;
    var banner = document.getElementsByClassName('owl-carousel')[0];
    banner.innerHTML += newDiv;
}

// đóng mở xem hình
function opencertain() {
    document.getElementById("overlaycertainimg").style.transform = "scale(1)";
}

function closecertain() {
    document.getElementById("overlaycertainimg").style.transform = "scale(0)";
}

// đổi hình trong chế độ xem hình
function changepic(src) {
    document.getElementById("bigimg").src = src;
}*/

// ==================== Review Stars Interaction ====================
function initReviewStars() {
    // Overall rating stars (middle column)
    const overallStars = document.querySelectorAll('#ratingStars i');
    overallStars.forEach((star, index) => {
        star.addEventListener('mouseenter', () => {
            overallStars.forEach((s, i) => {
                if (i <= index) {
                    s.classList.remove('fa-star-o');
                    s.classList.add('fa-star', 'active');
                } else {
                    s.classList.remove('fa-star', 'active');
                    s.classList.add('fa-star-o');
                }
            });
        });
        
        star.addEventListener('click', () => {
            const rating = index + 1;
            // Lưu rating đã chọn
            star.parentElement.dataset.selectedRating = rating;
            // TODO: Khi có backend, sẽ mở form đánh giá
            Swal.fire({
                icon: 'info',
                title: 'Đánh giá ' + rating + ' sao',
                text: 'Tính năng đánh giá sẽ sớm được cập nhật!',
                confirmButtonColor: '#1428a0'
            });
        });
    });
    
    // Reset stars khi mouse leave
    const ratingStarsContainer = document.getElementById('ratingStars');
    if (ratingStarsContainer) {
        ratingStarsContainer.addEventListener('mouseleave', () => {
            const selectedRating = parseInt(ratingStarsContainer.dataset.selectedRating) || 0;
            overallStars.forEach((s, i) => {
                if (i < selectedRating) {
                    s.classList.remove('fa-star-o');
                    s.classList.add('fa-star', 'active');
                } else {
                    s.classList.remove('fa-star', 'active');
                    s.classList.add('fa-star-o');
                }
            });
        });
    }
    
    // Review input stars (bottom section)
    const reviewStars = document.querySelectorAll('#reviewStarsInput i');
    reviewStars.forEach((star, index) => {
        star.addEventListener('mouseenter', () => {
            reviewStars.forEach((s, i) => {
                s.style.opacity = i <= index ? '1' : '0.4';
            });
        });
        
        star.addEventListener('click', () => {
            const rating = index + 1;
            // TODO: Khi có backend, sẽ mở form đánh giá
            Swal.fire({
                icon: 'info',
                title: 'Đánh giá ' + rating + ' sao',
                text: 'Tính năng đánh giá sẽ sớm được cập nhật!',
                confirmButtonColor: '#1428a0'
            });
        });
    });
    
    const reviewStarsContainer = document.getElementById('reviewStarsInput');
    if (reviewStarsContainer) {
        reviewStarsContainer.addEventListener('mouseleave', () => {
            reviewStars.forEach(s => s.style.opacity = '1');
        });
    }
    
    // Rating bar click filter (placeholder)
    const ratingBars = document.querySelectorAll('.rating-bar-row');
    ratingBars.forEach(bar => {
        bar.addEventListener('click', () => {
            const star = bar.dataset.star;
            ratingBars.forEach(b => b.classList.remove('active'));
            bar.classList.add('active');
            // TODO: Filter reviews by star rating
            console.log('Filter by', star, 'stars');
        });
    });
}
