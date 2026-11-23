<?php
session_start();

// Thêm topnav vào trang (Samsung style - minimalist)
function addTopNav()
{
    // Top nav removed - clean minimal design
}

// Thêm header Samsung style
function addHeader()
{
    echo '        
	<div class="header-samsung">
        <div class="header-container">
            <!-- Logo text style Samsung + Menu bên trái -->
            <div class="header-left">
                <div class="header-logo">
                    <a href="Index.php" class="logo-text">3AM</a>
                </div>
                
                <!-- Menu chính -->
                <nav class="header-nav">
                    <ul class="nav-menu">
                        <li><a href="Index.php" class="nav-link">Trang chủ</a></li>
                        <li><a href="?category=phone" class="nav-link">Di động</a></li>
                        <li><a href="?category=laptop" class="nav-link">Laptop</a></li>
                        <li><a href="?category=watch" class="nav-link">Đồng hồ</a></li>
                        <li><a href="?category=accessories" class="nav-link">Phụ kiện</a></li>
                    </ul>
                </nav>
            </div>

            <!-- Tools bên phải -->
            <div class="header-tools">
                <!-- Search Bar (Samsung style) -->
                <div class="tool-search-bar">
                    <form method="get" action="Index.php" class="search-form-samsung">
                        <i class="fa fa-search search-icon"></i>
                        <input type="text" name="search" placeholder="Tìm kiếm" autocomplete="off">
                    </form>
                </div>

                <!-- Cart -->
                <div class="tool-cart">
                    <a href="Cart.php" class="cart-link">
                        <svg class="cart-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M6 6h15l-1.5 9h-12z"/>
                            <circle cx="9" cy="20" r="1.5"/>
                            <circle cx="18" cy="20" r="1.5"/>
                            <path d="M6 6L5 3H2"/>
                        </svg>
                        <span class="cart-badge cart-number"></span>
                    </a>
                </div>

                <!-- User Account -->
                <div class="tool-account">
                    <button class="account-toggle" onclick="checkTaiKhoan()" id="btnTaiKhoan">
                        <i class="fa fa-user"></i>
                    </button>
                    <div class="account-dropdown menuMember">
                        <div class="account-header" id="accountUserName">
                            <i class="fa fa-user"></i>
                            <span>Tài khoản</span>
                        </div>
                        <a href="Account.php">Tài khoản của tôi</a>
                        <a href="User.php">Đơn hàng của tôi</a>
                        <a href="User.php#rewards">My Rewards</a>
                        <a href="Warranty.php">Bảo hành & Sửa chữa</a>
                        <a onclick="checkDangXuat();" style="cursor: pointer;">Đăng xuất</a>
                    </div>
                    <!-- Login/Register dropdown (khi chưa đăng nhập) -->
                    <div class="account-dropdown auth-menu" id="authMenu" style="display: none;">
                        <a href="SignIn.php" class="auth-main-link">
                            <div class="account-header">
                                <span>Đăng nhập/Đăng ký</span>
                                <i class="fa fa-angle-right"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>';
}

// thêm home
function addHome()
{
    echo '
    <div class="banner">
        <!-- Swiper hero slider -->
        <div class="swiper hero-swiper">
            <div class="swiper-wrapper"></div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </div> <!-- End Banner -->
    
    <div class="smallbanner" style="width: 100%;"></div>

    <!-- 4 Small Promo Banners -->
    <div class="promo-banners-grid">
        <a href="#" class="promo-banner-item">
            <img data-src="img/smallBanners/sml1.jpg" class="lazyload" alt="Promo 1">
        </a>
        <a href="#" class="promo-banner-item">
            <img data-src="img/smallBanners/sml2.jpg" class="lazyload" alt="Promo 2">
        </a>
        <a href="#" class="promo-banner-item">
            <img data-src="img/smallBanners/sml3.jpg" class="lazyload" alt="Promo 3">
        </a>
        <a href="#" class="promo-banner-item">
            <img data-src="img/smallBanners/sml4.jpg" class="lazyload" alt="Promo 4">
        </a>
    </div>

    <div class="companysFilter">
        <button class="companysButton" onclick="setCompanysMenu()">
            <p>Hãng</p>
            <div id="iconOpenMenu">▷</div>
            <div id="iconCloseMenu" style="display: none;">▽</div>
        </button>
    </div>
    <div class="companyMenu group flexContain"></div>

    <!-- Samsung Style Category Tabs -->
    <div class="samsung-category-tabs">
        <div class="category-tabs-container">
            <a href="Index.php" class="category-tab active">Tất cả</a>
            <a href="?brand=iphone" class="category-tab">iPhone</a>
            <a href="?brand=samsung" class="category-tab">Samsung</a>
            <a href="?brand=realme" class="category-tab">Realme</a>
            <a href="?brand=oppo" class="category-tab">Oppo</a>
            <a href="?category=accessories" class="category-tab">Phụ kiện Điện thoại</a>
        </div>
    </div>

    <!-- Samsung Style Filter Bar -->
    <div class="samsung-filter-bar">
        <div class="filter-bar-left">
            <span class="filter-label">Bộ lọc</span>
            <span class="filter-divider">|</span>
            <span class="result-count-samsung"><span id="productCount">0</span> Kết quả</span>
        </div>
        <div class="filter-bar-right">
            <span class="sort-label">Sắp xếp</span>
            <div class="sort-dropdown-samsung">
                <button class="sort-toggle" onclick="toggleSortDropdown()">
                    <span id="currentSort">Theo Khuyến Nghị</span>
                    <i class="fa fa-angle-down"></i>
                </button>
                <div class="sort-dropdown-content" id="sortDropdownContent">
                    <a href="#" onclick="setSortOption(\'newest\', \'Mới Nhất\')">Mới Nhất</a>
                    <a href="#" onclick="setSortOption(\'popular\', \'Phổ Biến Nhất\')">Phổ Biến Nhất</a>
                    <a href="#" onclick="setSortOption(\'rating\', \'Highest Rated\')">Highest Rated</a>
                    <a href="#" onclick="setSortOption(\'recommended\', \'Theo Khuyến Nghị\')" class="active"><i class="fa fa-check"></i> Theo Khuyến Nghị</a>
                    <a href="#" onclick="setSortOption(\'price-desc\', \'Giá: từ cao đến thấp\')">Giá: từ cao đến thấp</a>
                    <a href="#" onclick="setSortOption(\'price-asc\', \'Giá: từ thấp đến cao\')">Giá: từ thấp đến cao</a>
                    <a href="#" onclick="setSortOption(\'stock\', \'Có Hàng Online\')">Có Hàng Online</a>
                    <a href="#" onclick="setSortOption(\'views\', \'Xem nhiều nhất\')">Xem nhiều nhất</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Samsung Style Filter Buttons -->
    <div class="samsung-filter-buttons">
        <div class="filter-btn-wrapper">
            <button class="filter-btn recommended active" onclick="toggleFilterBtn(this)">
                Recommended <i class="fa fa-angle-down"></i>
            </button>
        </div>
        
        <div class="filter-btn-wrapper">
            <div class="pricesRangeFilter dropdown-samsung">
                <button class="filter-btn" onclick="toggleDropdownSamsung(this)">
                    Khoảng Giá <i class="fa fa-angle-down"></i>
                </button>
                <div class="dropdown-content-samsung"></div>
            </div>
        </div>

        <div class="filter-btn-wrapper">
            <div class="promosFilter dropdown-samsung">
                <button class="filter-btn" onclick="toggleDropdownSamsung(this)">
                    Khuyến mãi <i class="fa fa-angle-down"></i>
                </button>
                <div class="dropdown-content-samsung"></div>
            </div>
        </div>

        <div class="filter-btn-wrapper">
            <div class="starFilter dropdown-samsung">
                <button class="filter-btn" onclick="toggleDropdownSamsung(this)">
                    Số lượng sao <i class="fa fa-angle-down"></i>
                </button>
                <div class="dropdown-content-samsung"></div>
            </div>
        </div>

        <div class="filter-btn-wrapper">
            <button class="filter-btn" onclick="toggleFilterBtn(this)">
                Mua Hàng Trực Tuyến <i class="fa fa-angle-down"></i>
            </button>
        </div>
        
        <!-- Clear All Button (hidden by default) -->
        <div class="filter-btn-wrapper" id="clearAllWrapper" style="display: none;">
            <button class="filter-btn-clear" onclick="clearAllSamsungFilters()">
                Xóa tất cả
            </button>
        </div>
    </div>
    
    <!-- Samsung Active Filter Tags -->
    <div class="samsung-filter-tags" id="samsungFilterTags" style="display: none;">
        <!-- Filter tags will be inserted here by JS -->
    </div>

    <!-- Old filters (hidden, keep for JS compatibility) -->
    <div class="timNangCao" style="display: none;">
        <div class="flexContain">
            <div class="pricesRangeFilter-old dropdown">
                <button class="dropbtn">Khoảng Giá</button>
                <div class="dropdown-content"></div>
            </div>

            <div class="promosFilter-old dropdown">
                <button class="dropbtn">Khuyến mãi</button>
                <div class="dropdown-content"></div>
            </div>

            <div class="starFilter-old dropdown">
                <button class="dropbtn">Số lượng sao</button>
                <div class="dropdown-content"></div>
            </div>

            <div class="sortFilter dropdown">
                <button class="dropbtn">Sắp xếp</button>
                <div class="dropdown-content"></div>
            </div>            
        </div>
        
        <!-- Active filters display -->
        <div class="filter-tags" id="activeFilters" style="display:none;">
            <!-- Active filter tags will be inserted here by JS -->
        </div>

        <!-- Result count -->
        <div class="result-count" id="resultCount" style="display:none;">
            Tìm thấy <strong>0</strong> sản phẩm
        </div>
    </div>

    <div class="choosedFilter flexContain"></div> <!-- Những bộ lọc đã chọn -->

    <!-- Mặc định mới vào trang sẽ ẩn đi, nế có filter thì mới hiện lên -->
    <div class="contain-products" style="display:none">
    <!-- Removed filterName div with product count -->
    <div class="loader" style="display: none"></div>

    <div id="khongCoSanPham">
        <i class="fa fa-times-circle"></i>
        Không có sản phẩm nào
    </div> <!-- End Khong co san pham -->

    <ul id="products" class="products-grid">
    </ul><!-- End products -->

    <div class="pagination"></div>
    </div>

    <!-- Div hiển thị khung sp hot, khuyến mãi, mới ra mắt ... -->
    <div class="contain-khungSanPham"></div>';
}

// Thêm chi tiết sản phẩm
function addChiTietSanPham()
{
    echo '
    <div class="chitietSanpham">
        <!-- Sticky Product Header Bar -->
        <div class="product-header" id="productHeader">
            <div class="product-header-content">
                <div class="product-header-left">
                    <span class="badge-new">Sản phẩm mới</span>
                    <h1 class="product-title" id="stickyProductTitle">Galaxy S25 FE</h1>
                </div>
                <div class="product-header-right">
                    <div class="sticky-price">
                        <p class="sticky-installment" id="stickyInstallment">Từ 90.833 ₫/tháng với lãi suất 0% hoặc</p>
                        <div class="sticky-total" id="stickyTotal">16.690.000 ₫</div>
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
                            <img id="mainImage" src="" alt="Product Image" class="gallery-main-image">
                        </div>
                        <button class="gallery-nav next" onclick="nextImage()">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                    </div>
                    <div class="gallery-pagination">
                        <span class="dot active" onclick="selectImage(0)"></span>
                        <span class="dot" onclick="selectImage(1)"></span>
                    </div>
                </div>

                <!-- Right Column: Scrollable Options -->
                <div class="options-column">
                    <!-- Device Variants Section -->
                    <div class="config-section" data-section="variants">
                        <h2 class="section-title">Thiết bị</h2>
                        <p class="section-subtitle">Vui lòng chọn thiết bị</p>
                        
                        <div class="variant-list" id="variantList">
                            <!-- Variants will be loaded dynamically -->
                        </div>
                    </div>
                    
                    <!-- Storage Options Section -->
                    <div class="config-section" data-section="storage" id="storageSection" style="display: none;">
                        <h2 class="section-title">Chọn dung lượng bộ nhớ</h2>
                        <div class="storage-list" id="storageList">
                            <!-- Storage options will be loaded dynamically -->
                        </div>
                    </div>
                    
                    <!-- Color Selection Section -->
                    <div class="config-section" data-section="colors" id="colorSection" style="display: none;">
                        <h2 class="section-title">Chọn màu sắc</h2>
                        <p class="section-subtitle">Màu sắc bạn yêu thích</p>
                        <div class="color-list" id="colorList">
                            <!-- Color options will be loaded dynamically -->
                        </div>
                    </div>
                    
                    <!-- Summary & Action Buttons -->
                    <div class="config-section" id="productActionsBottom" style="display: none;">
                        <div class="selected-summary">
                            <div class="summary-row">
                                <span class="summary-label">Cấu hình:</span>
                                <span class="summary-value" id="selectedConfig">Chưa chọn</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Màu sắc:</span>
                                <span class="summary-value" id="selectedColor">Chưa chọn</span>
                            </div>
                            <div class="summary-row summary-total">
                                <span class="summary-label">Tổng tiền:</span>
                                <span class="summary-price" id="totalPrice">0 ₫</span>
                            </div>
                        </div>
                        <button class="btn-add-to-cart" onclick="addToCartWithConfig()">
                            <i class="fa fa-shopping-cart"></i>
                            Thêm vào giỏ hàng
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 3AM Care+ Section -->
        <div class="marketing-section samsung-care-section carePlus" style="display: none;">
            <h2 class="care-title">3AM Care+</h2>
            <p class="care-subtitle">Bảo vệ toàn diện thiết bị của bạn từ các rủi ro như rơi vỡ hoặc rơi vào nước tối đa lên đến 2 năm</p>
            
            <div class="care-plans-grid" id="carePlansGrid">
                <!-- Care plans will be loaded dynamically -->
            </div>
        </div>
        
        <!-- Exclusive Offers Section -->
        <div class="marketing-section exclusive-offers" style="display: none;">
            <h2 class="marketing-title">Ưu đãi độc quyền</h2>
            <div class="offers-grid" id="offersGrid">
                <!-- Offers will be loaded dynamically -->
            </div>
        </div>
        
        <!-- Technical Specifications Section -->
        <div class="marketing-section tech-specs-section">
            <h2 class="marketing-title">Thông số kỹ thuật</h2>
            
            <div class="specs-table-large">
                <div class="specs-row">
                    <div class="specs-label">Màn hình</div>
                    <div class="specs-value">LTPO Super Retina XDR OLED, 6.9 inch, 120Hz ProMot</div>
                </div>
                
                <div class="specs-row">
                    <div class="specs-label">Hệ điều hành</div>
                    <div class="specs-value">iOS 18</div>
                </div>
                
                <div class="specs-row">
                    <div class="specs-label">Camera sau</div>
                    <div class="specs-value">48MP Chính + 48MP Siêu rộng + 12MP Tele 5x</div>
                </div>
                
                <div class="specs-row">
                    <div class="specs-label">Camera trước</div>
                    <div class="specs-value">12MP, f/1.9, Auto Focus</div>
                </div>
                
                <div class="specs-row">
                    <div class="specs-label">CPU</div>
                    <div class="specs-value">Apple A18 Pro (6 nhân CPU, 6 nhân GPU)</div>
                </div>
                
                <div class="specs-row">
                    <div class="specs-label">RAM</div>
                    <div class="specs-value">8 GB</div>
                </div>
                
                <div class="specs-row">
                    <div class="specs-label">Bộ nhớ trong</div>
                    <div class="specs-value">256GB / 512GB / 1TB</div>
                </div>
                
                <div class="specs-row">
                    <div class="specs-label">Thẻ nhớ</div>
                    <div class="specs-value">Không hỗ trợ</div>
                </div>
                
                <div class="specs-row">
                    <div class="specs-label">Dung lượng pin</div>
                    <div class="specs-value">4676 mAh</div>
                </div>
            </div>
        </div>
        
        <!-- Old sections below - will be redesigned later -->
        <div class="old-sections" style="display: none;">
            <div class="price_sale">
                <div class="area_price"> </div>
                <div class="ship" style="display: none;">
                    <i class="fa fa-clock-o"></i>
                    <div>NHẬN HÀNG TRONG 1 GIỜ</div>
                </div>
                <div class="area_promo">
                    <strong>khuyến mãi</strong>
                    <div class="promo">
                        <i class="fa fa-check-circle"></i>
                        <div id="detailPromo"> </div>
                    </div>
                </div>
                <div class="policy">
                    <div>
                        <i class="fa fa-archive"></i>
                        <p>Trong hộp có: Sạc, Tai nghe, Sách hướng dẫn, Cây lấy sim, Ốp lưng </p>
                    </div>
                    <div>
                        <i class="fa fa-star"></i>
                        <p>Bảo hành chính hãng 12 tháng.</p>
                    </div>
                    <div class="last">
                        <i class="fa fa-retweet"></i>
                        <p>1 đổi 1 trong 1 tháng nếu lỗi, đổi sản phẩm tại nhà trong 1 ngày.</p>
                    </div>
                </div>
                <div class="area_order">
                    <!-- nameProduct là biến toàn cục được khởi tạo giá trị trong phanTich_URL_chiTietSanPham -->
                    <a class="buy_now" onclick="themVaoGioHang(maProduct, nameProduct);">
                        <h3><i class="fa fa-plus"></i> Thêm vào giỏ hàng</h3>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Samsung Style Reviews Section -->
        <div class="samsung-reviews">
            <h2 class="reviews-title">Đánh giá sản phẩm</h2>
            
            <!-- Rating Overview -->
            <div class="rating-overview">
                <div class="rating-summary">
                    <div class="average-rating">
                        <div class="rating-number">5.0</div>
                        <div class="rating-stars">
                            <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                        </div>
                        <div class="rating-count">5 đánh giá</div>
                    </div>
                    
                    <div class="rating-breakdown">
                        <div class="rating-row">
                            <span>Kết xuất nhanh xếp loại</span>
                        </div>
                        <div class="rating-row">
                            <span>Chọn một dòng dưới đây để lọc đánh giá.</span>
                        </div>
                        <div class="rating-bar-item">
                            <span class="rating-label">5 sao</span>
                            <div class="rating-bar">
                                <div class="rating-fill" style="width: 100%"></div>
                            </div>
                            <span class="rating-count-text">5</span>
                        </div>
                        <div class="rating-bar-item">
                            <span class="rating-label">4 sao</span>
                            <div class="rating-bar">
                                <div class="rating-fill" style="width: 0%"></div>
                            </div>
                            <span class="rating-count-text">0</span>
                        </div>
                        <div class="rating-bar-item">
                            <span class="rating-label">3 sao</span>
                            <div class="rating-bar">
                                <div class="rating-fill" style="width: 0%"></div>
                            </div>
                            <span class="rating-count-text">0</span>
                        </div>
                        <div class="rating-bar-item">
                            <span class="rating-label">2 sao</span>
                            <div class="rating-bar">
                                <div class="rating-fill" style="width: 0%"></div>
                            </div>
                            <span class="rating-count-text">0</span>
                        </div>
                        <div class="rating-bar-item">
                            <span class="rating-label">1 sao</span>
                            <div class="rating-bar">
                                <div class="rating-fill" style="width: 0%"></div>
                            </div>
                            <span class="rating-count-text">0</span>
                        </div>
                    </div>
                    
                    <div class="rating-categories">
                        <div class="rating-cat-title">Xếp hạng tổng thể</div>
                        <div class="rating-cat-item">
                            <span>Đánh giá sản phẩm này</span>
                        </div>
                        <div class="rating-stars-select">
                            <i class="fa fa-star-o" data-rating="1"></i>
                            <i class="fa fa-star-o" data-rating="2"></i>
                            <i class="fa fa-star-o" data-rating="3"></i>
                            <i class="fa fa-star-o" data-rating="4"></i>
                            <i class="fa fa-star-o" data-rating="5"></i>
                        </div>
                        <div class="rating-note">Thêm đánh giá sẽ yêu cầu một email hợp lệ để xác minh</div>
                    </div>
                    
                    <div class="rating-filter">
                        <div class="filter-title">Xếp loại khách hàng trung bình</div>
                        <div class="filter-items">
                            <div class="filter-item">
                                <span>Tính năng</span>
                                <div class="filter-stars">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </div>
                                <span class="filter-score">5.0</span>
                            </div>
                            <div class="filter-item">
                                <span>Hiệu năng</span>
                                <div class="filter-stars">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i>
                                </div>
                                <span class="filter-score">4.8</span>
                            </div>
                            <div class="filter-item">
                                <span>Thiết kế</span>
                                <div class="filter-stars">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </div>
                                <span class="filter-score">5.0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Write Review Form - Hidden (chỉ hiện khi đã mua hàng) -->
            <div class="write-review" style="display: none;">
                <div class="stars-input">
                    <input class="star star-5" id="star-5" value="5" type="radio" name="star"/>
                    <label class="star star-5" for="star-5"></label>
                    <input class="star star-4" id="star-4" value="4" type="radio" name="star"/>
                    <label class="star star-4" for="star-4"></label>
                    <input class="star star-3" id="star-3" value="3" type="radio" name="star"/>
                    <label class="star star-3" for="star-3"></label>
                    <input class="star star-2" id="star-2" value="2" type="radio" name="star"/>
                    <label class="star star-2" for="star-2"></label>
                    <input class="star star-1" id="star-1" value="1" type="radio" name="star"/>
                    <label class="star star-1" for="star-1"></label>
                </div>
                <textarea maxlength="250" id="inpBinhLuan" placeholder="Viết suy nghĩ của bạn vào đây..."></textarea>
                <button id="btnBinhLuan" onclick="checkGuiBinhLuan()">GỬI BÌNH LUẬN</button>
            </div>
            
            <!-- Reviews List -->
            <div class="reviews-list">
                <div class="reviews-header">
                    <div class="reviews-stars-display">
                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                        <span>0 đánh giá</span>
                    </div>
                </div>
                <div class="comment-content">
                </div>
            </div>
        </div>
    </div>';
}

// Thêm footer
function addFooter()
{
    echo '
    <!-- ============== Alert Box ============= -->
    <div id="alert">
        <span id="closebtn">&otimes;</span>
    </div>

    <!-- ============== Footer ============= -->
    <footer class="samsung-footer">
        <div class="footer-container">
            <div class="footer-links">
                <div class="footer-left">
                    <h4>Việt Nam/Vietnamese</h4>
                    <h4>Trợ Giúp Truy Cập</h4>
                    <h4>Bảo Mật</h4>
                    <h4>Pháp Lý</h4>
                </div>
                <div class="footer-social">
                    <span>Kết Nối với Chúng Tôi</span>
                    <div class="social-icons">
                        <a href="#" aria-label="Facebook"><i class="fa fa-facebook"></i></a>
                        <a href="#" aria-label="YouTube"><i class="fa fa-youtube-play"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fa fa-instagram"></i></a>
                        <a href="#" aria-label="Zalo">Zalo</a>
                    </div>
                </div>
            </div>

        </div>
    </footer>';
}

// Thêm contain Taikhoan
function addContainTaiKhoan()
{
    echo '
	<div class="containTaikhoan">
    <span class="close" onclick="showTaiKhoan(false);">&times;</span> 
        <div class=" taikhoan">
            <ul class="tab-group">
                <li class="tab active"><a href="#login">Đăng nhập</a></li>
                <li class="tab"><a href="#signup">Đăng kí</a></li>
            </ul> <!-- /tab group -->
            <div class="tab-content">
                <div id="login">
                    <h1>Chào mừng bạn trở lại!</h1>
                    <!-- <form onsubmit="return logIn(this);"> -->
                    <form action="" method="post" name="formDangNhap" onsubmit="return checkDangNhap();">
                        <div class="field-wrap">
                            <label>
                                Tên đăng nhập<span class="req">*</span>
                            </label>
                            <input name="username" type="text" id="username" required autocomplete="off" />
                        </div> <!-- /user name -->
                        <div class="field-wrap">
                            <label>
                                Mật khẩu<span class="req">*</span>
                            </label>
                            <input name="pass" type="password" id="pass" required autocomplete="off" />
                        </div> <!-- pass -->
                        <p class="forgot"><a href="#">Quên mật khẩu?</a></p>
                        <button type="submit" class="button button-block" />Tiếp tục</button>
                    </form> <!-- /form -->
                </div> <!-- /log in -->
                <div id="signup">
                    <h1>Đăng kí miễn phí</h1>
                    <!-- <form onsubmit="return signUp(this);"> -->
                    <form action="" method="post" name="formDangKy" onsubmit="return checkDangKy();">
                        <div class="top-row">
                            <div class="field-wrap">
                                <label>
                                    Họ<span class="req">*</span>
                                </label>
                                <input name="ho" type="text" id="ho" required autocomplete="off" />
                            </div>
                            <div class="field-wrap">
                                <label>
                                    Tên<span class="req">*</span>
                                </label>
                                <input name="ten" id="ten" type="text" required autocomplete="off" />
                            </div>
                        </div> <!-- / ho ten -->
                        <div class="top-row">
                            <div class="field-wrap">
                                <label>
                                    Điện thoại<span class="req">*</span>
                                </label>
                                <input name="sdt" id="sdt" type="text" pattern="\d*" minlength="10" maxlength="12" required autocomplete="off" />
                            </div> <!-- /sdt -->
                            <div class="field-wrap">
                                <label>
                                    Email<span class="req">*</span>
                                </label>
                                <input name="email" id="email" type="email" required autocomplete="off" />
                            </div> <!-- /email -->
                        </div>
                        <div class="field-wrap">
                            <label>
                                Địa chỉ<span class="req">*</span>
                            </label>
                            <input name="diachi" id="diachi" type="text" required autocomplete="off" />
                        </div> <!-- /user name -->
                        <div class="field-wrap">
                            <label>
                                Tên đăng nhập<span class="req">*</span>
                            </label>
                            <input name="newUser" id="newUser" type="text" required autocomplete="off" />
                        </div> <!-- /user name -->
                        <div class="field-wrap">
                            <label>
                                Mật khẩu<span class="req">*</span>
                            </label>
                            <input name="newPass" id="newPass" type="password" required autocomplete="off" />
                        </div> <!-- /pass -->
                        <button type="submit" class="button button-block" />Tạo tài khoản</button>
                    </form> <!-- /form -->
                </div> <!-- /sign up -->
            </div><!-- tab-content -->
        </div> <!-- /taikhoan -->
    </div>
';
}

// Thêm plc (phần giới thiệu trước footer)
function addPlc()
{
    echo '
    <div class="plc">
        <section>
            <ul class="flexContain">
                <li>Giao hàng hỏa tốc trong 1 giờ</li>
                <li>Thanh toán linh hoạt: tiền mặt, visa / master, trả góp</li>
                <li>Trải nghiệm sản phẩm tại nhà</li>
                <li>Lỗi đổi tại nhà trong 1 ngày</li>
                <li>Hỗ trợ suốt thời gian sử dụng.
                    <br>Hotline:
                    <a href="tel:12345678" style="color: #288ad6;">1234.5678</a>
                </li>
            </ul>
        </section>
    </div>';
}
