// DEPRECATED: Pagination variables - no longer using pagination
// var SoLuongTrangHienThi = 4;
// var ProductsPerPage = 4;
var DanhSachSanPham = [];
var DataCompany = [];
var CurrentFilters = [];

window.onload = function() {
    console.log('Trangchu.js loaded - Starting initialization');
    khoiTao();

    // autocomplete cho khung tim kiem
    // autocomplete(document.getElementById('search-box'), list_products);

    // Tags - disabled for new header
    // var tags = ["Samsung", "iPhone", "Coolpad", "Oppo", "Mobi"];
    // for (var t of tags) addTags(t, "Index.php?search=" + t);

    // =================== web 2 tìm nâng cao ================
    // Thêm hình vào banner
    setupBanner();

    // Không còn dùng loại sản phẩm cũ
    // setupCompany();

    // Setup dropdown toggles
    setupDropdowns();
    
    // Initialize Samsung style filters
    initSamsungFilters();

    // ==================== End ===========================

    // Chỉ hiển thị product series (không còn sản phẩm cũ)
    var filters = getFilterFromURL();
    console.log('Filters from URL:', filters);
    hienThiTatCaSanPham();

    // Các filter cũ - bỏ vì không còn dùng
    // addPricesRange, addPromotion, addStarFilter, addSortFilter đã bị disable
};

// ============================== web2 ===========================
function hienThiTatCaSanPham(priceFilters) {
    $('.contain-khungSanPham').html('');
    // Xóa tất cả sản phẩm series hiện tại trong #products trước khi load lại
    $('#products .sanPham').remove();
    
    // Chỉ load product series (không còn sản phẩm cũ)
    loadProductSeries(priceFilters);
}

function hienThiKhungSanPhamMacDinh() {
    // Deprecated - now showing all products
    hienThiTatCaSanPham();
}

// ========== LOAD PRODUCT SERIES ===============
function loadProductSeries(priceFilters) {
    console.log('Loading product series with filters:', priceFilters);
    $.ajax({
        type: "POST",
        url: "Api/ProcessProductSeriesGet.php",
        dataType: "json",
        data: {
            request: "getallseries"
        },
        success: function(response) {
            console.log('Series response:', response);
            if (response.success && response.data.length > 0) {
                console.log('Found ' + response.data.length + ' series');
                // Load chi tiết của từng series và thêm vào danh sách sản phẩm
                response.data.forEach(function(series) {
                    loadAndDisplaySeries(series.id, priceFilters);
                });
            } else {
                console.log('No series found or error:', response);
                // Hiển thị thông báo không có sản phẩm
                alertNotHaveProduct(false);
            }
        },
        error: function(xhr, status, error) {
            console.log('Error loading product series:', error);
            console.log('XHR:', xhr);
            console.log('Status:', status);
        }
    });
}

function loadAndDisplaySeries(seriesId, priceFilters) {
    console.log('Loading series detail for ID:', seriesId);
    $.ajax({
        type: "POST",
        url: "Api/ProcessProductSeriesGet.php",
        dataType: "json",
        data: {
            request: "getseriesdetail",
            series_id: seriesId
        },
        success: function(response) {
            console.log('Series detail response:', response);
            if (response.success && response.data) {
                // Kiểm tra filter giá trước khi hiển thị
                var displayPrice = response.data.display_price ? parseInt(response.data.display_price) : 0;
                
                // Nếu có filter giá, kiểm tra xem sản phẩm có nằm trong khoảng giá không
                if (priceFilters && priceFilters.length > 0) {
                    var matchesFilter = false;
                    for (var i = 0; i < priceFilters.length; i++) {
                        var parts = priceFilters[i].split('-');
                        var minPrice = parseInt(parts[0]);
                        var maxPrice = parseInt(parts[1]);
                        
                        // Kiểm tra giá nằm trong khoảng
                        if (maxPrice === 0) {
                            // Trên X đồng
                            if (displayPrice >= minPrice) {
                                matchesFilter = true;
                                break;
                            }
                        } else if (minPrice === 0) {
                            // Dưới X đồng
                            if (displayPrice <= maxPrice) {
                                matchesFilter = true;
                                break;
                            }
                        } else {
                            // Trong khoảng min-max
                            if (displayPrice >= minPrice && displayPrice <= maxPrice) {
                                matchesFilter = true;
                                break;
                            }
                        }
                    }
                    
                    if (!matchesFilter) {
                        console.log('Series ' + response.data.series_name + ' (giá: ' + displayPrice + ') không khớp filter');
                        return; // Không hiển thị sản phẩm này
                    }
                }
                
                // Chỉ tạo 1 product card cho cả series (không loop variants)
                addSeriesToWeb(response.data);
            }
        },
        error: function(xhr, status, error) {
            console.log('Error loading series detail:', error);
        }
    });
}

function addSeriesToWeb(seriesData) {
    console.log('Adding series to web:', seriesData.series_name);
    
    // Sử dụng giá display_price từ series (giá đại diện)
    var displayPrice = seriesData.display_price ? parseInt(seriesData.display_price) : 0;
    
    // Nếu không có display_price, tính giá thấp nhất từ tất cả variants
    if (!displayPrice && seriesData.variants && seriesData.variants.length > 0) {
        var minPrice = 999999999;
        seriesData.variants.forEach(function(variant) {
            if (variant.storage_options && variant.storage_options.length > 0) {
                variant.storage_options.forEach(function(storage) {
                    var p = parseFloat(storage.price);
                    if (p < minPrice) minPrice = p;
                });
            }
        });
        displayPrice = minPrice;
    }
    
    // Lấy ảnh đại diện của series
    var productImage = seriesData.main_image || 'img/products/default.jpg';
    
    // Tạo rating stars
    var rating = "";
    for (var i = 1; i <= 5; i++) {
        rating += `<i class="fa fa-star"></i>`;
    }
    rating += `<span>0 đánh giá</span>`;
    
    // Format giá
    var giaTri = parseInt(displayPrice);
    var pricediv = `<strong>` + giaTri.toLocaleString() + `&#8363;</strong>`;
    
    // Tạo link chi tiết
    var chitietSeries = 'ProductDetail.php?slug=' + seriesData.slug;
    
    // HTML giống hệt addToWeb
    var newLi =
        `<li class="sanPham product-card" data-aos="fade-up">
            <a href="` + chitietSeries + `" class="product-card-link">
                <div class="product-card-image">
                    <img data-src="` + productImage + `" class="lazyload" alt="` + seriesData.series_name + `">
                </div>
                <div class="product-card-content">
                    <h3 class="product-card-title">` + seriesData.series_name + `</h3>
                    <div class="product-card-price">
                        ` + pricediv + `
                    </div>
                    <div class="product-card-rating">
                        ` + rating + `
                    </div>
                </div>
            </a>
            <div class="product-card-footer">
                <button class="btn-action btn-buy-now" onclick="window.location.href='` + chitietSeries + `'">Mua ngay</button>
                <a href="` + chitietSeries + `" class="btn-action btn-learn-more">Tìm hiểu thêm</a>
            </div>
        </li>`;
    
    // Thêm vào DOM
    var products = document.getElementById('products');
    if (products) {
        products.innerHTML += newLi;
        console.log('Added series product card');
        try { if (window.AOS) window.AOS.refresh(); } catch (e) { }
    }
    
    // Update result count (both old and Samsung style)
    var currentCount = $('#products .sanPham').length;
    var resultCountStrong = document.querySelector('#resultCount strong');
    if (resultCountStrong) {
        resultCountStrong.textContent = currentCount;
    }
    updateProductCountSamsung(currentCount);
    if (currentCount > 0) {
        var resultCount = document.getElementById('resultCount');
        if (resultCount) resultCount.style.display = '';
        var containProducts = document.querySelector('.contain-products');
        if (containProducts) containProducts.style.display = '';
    }
}

// Setup dropdown toggles for filters
function setupDropdowns() {
    var dropdowns = document.querySelectorAll('.timNangCao .dropdown');
    
    dropdowns.forEach(function(dropdown) {
        var button = dropdown.querySelector('.dropbtn');
        
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            
            // Close all other dropdowns
            dropdowns.forEach(function(otherDropdown) {
                if (otherDropdown !== dropdown) {
                    otherDropdown.classList.remove('show');
                }
            });
            
            // Toggle current dropdown
            dropdown.classList.toggle('show');
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.timNangCao .dropdown')) {
            dropdowns.forEach(function(dropdown) {
                dropdown.classList.remove('show');
            });
        }
    });
}

function setupBanner() {
    $.ajax({
        type: "POST",
        url: "Api/ProcessImage.php",
        dataType: "json",
        timeout: 1500, // sau 1.5 giây mà không phản hồi thì dừng => hiện lỗi
        data: {
            request: "getallbanners",
        },
        success: function(data, status, xhr) {
            for (var url of data) {
                var realPath = url.split('../').join('');
                addBanner(realPath, realPath);
            }
            // If Swiper markup exists, init Swiper; else fall back to existing owl-carousel
            if (document.querySelector('.hero-swiper .swiper-wrapper')) {
                // ensure images are ready, then initialize
                setTimeout(function() {
                    if (window.heroSwiper) {
                        window.heroSwiper.destroy(true, true);
                    }
                    window.heroSwiper = new Swiper('.hero-swiper', {
                        slidesPerView: 1,
                        spaceBetween: 0,
                        loop: true,
                        centeredSlides: false,
                        autoplay: {
                            delay: 3500,
                            disableOnInteraction: false
                        },
                        pagination: { el: '.swiper-pagination', clickable: true },
                        navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
                        breakpoints: {
                            600: { slidesPerView: 1 },
                            1000: { slidesPerView: 1 }
                        }
                    });
                }, 50);
            } else {
                $('.owl-carousel').owlCarousel({
                    items: 1,
                    margin: 0,
                    center: false,
                    loop: true,
                    smartSpeed: 450,
                    nav: false,

                    autoplay: true,
                    autoplayTimeout: 3500,

                    responsive: {
                        0: { items: 1 },
                        600: { items: 1 },
                        1000: { items: 1 }
                    }
                });
            }
        },
        error: function() {
            Swal.fire({
                type: "error",
                title: "Lỗi lấy dữ liệu hình ảnh banners (Home.js > setUpBanner)",
                html: e.responseText
            });
        }
    });

    $.ajax({
        type: "POST",
        url: "Api/ProcessImage.php",
        dataType: "json",
        timeout: 1500, // sau 1.5 giây mà không phản hồi thì dừng => hiện lỗi
        data: {
            request: "getsmallbanner",
        },
        success: function(data, status, xhr) {
            for (var url of data) {
                var realPath = url.split('../').join('');
                addSmallBanner(realPath);
            }
        },
        error: function() {
            Swal.fire({
                type: "error",
                title: "Lỗi lấy dữ liệu hình ảnh small banners (Home.js > setUpBanner)",
                html: e.responseText
            });
        }
    });
}

// chọn hãng - DEPRECATED: không còn dùng loại sản phẩm cũ
function setupCompany() {
    console.log('setupCompany() deprecated - no longer using old product categories');
    return; // Skip - no longer using old product system
}

function addProductsFromList(list, filters) {
    DanhSachSanPham = list; // lưu danh sách hiện thời

    if (list.length == 0) {
        alertNotHaveProduct(false); // nếu length = 0 thì hiện ko có sản phẩm
        updateProductCountSamsung(0);
        return;
    } else {
        alertNotHaveProduct(true);
    }

    // Display all products without pagination
    for (var p of list) {
        addToWeb(p);
    }

    // Update Samsung style product count
    var totalCount = $('#products .sanPham').length;
    updateProductCountSamsung(totalCount);

    // Hide pagination
    $('.pagination').hide();
}

// DEPRECATED: No longer using pagination - showing all products
// function chuyenTrang(vitriTrang) {
//     $("#products li.sanPham").remove();
//     pushState(createFilters('page', vitriTrang));
//     var sanPhamDu = DanhSachSanPham.length % ProductsPerPage;
//     var soTrang = parseInt(DanhSachSanPham.length / ProductsPerPage) + (sanPhamDu ? 1 : 0);
//     var trangHienTai = parseInt(vitriTrang < soTrang ? vitriTrang : soTrang);
//     themNutPhanTrang(soTrang, trangHienTai);
//     var start = ProductsPerPage * (trangHienTai - 1);
//     var temp = copyObject(DanhSachSanPham);
//     temp = temp.splice(start, ProductsPerPage);
//     for (var p of temp) {
//         addToWeb(p);
//     }
// }

// DEPRECATED: không còn dùng sản phẩm cũ
function filtersAjax(filters, callback) {
    console.log('filtersAjax() deprecated - no longer using old product system. Filters were:', filters);
    // Không làm gì cả - sản phẩm đã được load bởi loadProductSeries()
    return;
}

// DEPRECATED: không còn dùng sản phẩm cũ
function ajaxThemSanPham(p, onSuccess, onFail) {
    console.log('ajaxThemSanPham() deprecated - no longer using old product system');
    return;
}

function addToWeb(p, ele, returnString) {
    // Chuyển star sang dạng tag html
    var rating = "";
    if (p.SoDanhGia >= 0) {
        for (var i = 1; i <= 5; i++) {
            if (i <= p.SoSao) {
                rating += `<i class="fa fa-star"></i>`
            } else {
                rating += `<i class="fa fa-star-o"></i>`
            }
        }
        rating += `<span>` + p.SoDanhGia + ` đánh giá</span>`;
    }

    // Chuyển giá tiền sang dạng tag html
    var giaTri = parseInt(p.DonGia);
    var giaTrikhuyenMai = parseInt(p.KM.GiaTriKM);
    var giaTriSauKM = giaTri - giaTrikhuyenMai;

    var pricediv, khuyenmaidiv;

    if (p.KM.LoaiKM == "GiaReOnline") {

        khuyenmaidiv = promoToWeb(p.KM.LoaiKM, giaTriSauKM);
        pricediv = `<strong>` + giaTriSauKM.toLocaleString() + `&#8363;</strong>
                <span>` + giaTri.toLocaleString() + `&#8363;</span>`;
    } else {

        khuyenmaidiv = promoToWeb(p.KM.LoaiKM, giaTrikhuyenMai);
        pricediv = `<strong>` + giaTri.toLocaleString() + `&#8363;</strong>`;
    }

    // tách theo dấu ' ' vào gắn lại bằng dấu '-', code này giúp bỏ hết khoảng trắng và thay vào bằng dấu '-'.
    // Tạo link tới chi tiết sản phẩm, chuyển tất cả ' ' thành '-'
    var chitietSp = 'ProductDetail.php?' + p.MaSP;

    // Samsung-style product card - Semantic HTML fixed
    var newLi =
        `<li class="sanPham product-card" data-aos="fade-up">
            <a href="` + chitietSp + `" class="product-card-link">
                <div class="product-card-image">
                    <img data-src="` + p.HinhAnh + `" class="lazyload" alt="` + (p.TenSP || '') + `">
                </div>
                <div class="product-card-content">
                    <h3 class="product-card-title">` + p.TenSP + `</h3>
                    <div class="product-card-price">
                        ` + pricediv + `
                    </div>
                    <div class="product-card-rating">
                        ` + rating + `
                    </div>
                </div>
            </a>
            <div class="product-card-footer">
                <button class="btn-action btn-buy-now" onclick="return themVaoGioHang('` + p.MaSP + `', '` + p.TenSP + `');">Mua ngay</button>
                <a href="` + chitietSp + `" class="btn-action btn-learn-more">Tìm hiểu thêm</a>
            </div>
        </li>`;

    if (returnString) return newLi;

    // Thêm tag <li> vừa tạo vào <ul> homeproduct (mặc định) , hoặc tag ele truyền vào
    var products = ele || document.getElementById('products');
    products.innerHTML += newLi;
    // If AOS is loaded, refresh to bind animations for dynamically added elements
    try { if (window.AOS) window.AOS.refresh(); } catch (e) { }
}

// =========== Đọc dữ liệu từ url ============
function getFilterFromURL() { // tách và trả về mảng bộ lọc trên url
    var fullLocation = window.location.href;
    fullLocation = decodeURIComponent(fullLocation);
    var dauHoi = fullLocation.split('?'); // tách theo dấu ?

    if (dauHoi[1]) {
        var dauVa = dauHoi[1].split('&');
        return dauVa;
    }

    return [];
}

// Thêm sản phẩm vào các khung sản phẩm
function addKhungSanPham(tenKhung, color, filters, len) {
    // convert color to code
    var gradient = `background-image: linear-gradient(120deg, ` + color[0] + ` 0%, ` + color[1] + ` 50%, ` + color[0] + ` 100%);`
    var borderColor = `border-color: ` + color[0];
    var borderA = ` border-left: 2px solid ` + color[0] + `; border-right: 2px solid ` + color[0] + `;`;

    // mở tag
    var s = `<div class="khungSanPham" style="` + borderColor + `">
                <h3 class="tenKhung" style="` + gradient + `">* ` + tenKhung + ` *</h3>
                <div class="listSpTrongKhung flexContain" data-tenkhung="` + tenKhung + `">
                    <div class="loader"></div>
                </div>
                <a class="xemTatCa" onclick='filtersAjax(`+JSON.stringify(filters)+`)' style="` + borderA + `" data-tenkhung="` + tenKhung + `">
                </a>
              </div> <hr>`;


    // thêm khung vào contain-khung
    document.getElementsByClassName('contain-khungSanPham')[0].innerHTML += s;

    // lấy dữ liệu cho vào khung
    filtersAjax(filters, (data) => {
        // thêm các <li> (sản phẩm) vào tag
        var s1 = "";
        var spResult = data;
        if (spResult.length < len) len = spResult.length;

        for (var i = 0; i < len; i++) {
            s1 += addToWeb(spResult[i], null, true);
            // truyền vào 'true' để trả về chuỗi rồi gán vào s
        }

        $("div.listSpTrongKhung[data-tenkhung='" + tenKhung + "']").html(s1);
        $("a.xemTatCa[data-tenkhung='" + tenKhung + "']").html("Xem tất cả " + spResult.length + " sản phẩm");
    })
}

// Nút phân trang
// DEPRECATED: No longer using pagination buttons
// function themNutPhanTrang(soTrang, trangHienTai) {
//     var divPhanTrang = document.getElementsByClassName('pagination')[0];
//     divPhanTrang.innerHTML = "";
//     if (trangHienTai > 1) {
//         divPhanTrang.innerHTML += `<a onclick="chuyenTrang(1)"><i class="fa fa-angle-double-left"></i></a>`;
//         divPhanTrang.innerHTML += `<a onclick="chuyenTrang(` + (trangHienTai - 1) + `)"><i class="fa fa-angle-left"></i></a>`;
//     }
//     if (soTrang > 1) {
//         for (var i = trangHienTai - (SoLuongTrangHienThi - 2); i <= trangHienTai + (SoLuongTrangHienThi - 2); i++) {
//             if (i == trangHienTai) {
//                 divPhanTrang.innerHTML += `<a href="javascript:;" class="current">` + i + `</a>`
//             } else if (i >= 1 && i <= soTrang) {
//                 divPhanTrang.innerHTML += `<a onclick="chuyenTrang(` + i + `)">` + i + `</a>`
//             }
//         }
//     }
//     if (trangHienTai < soTrang) {
//         divPhanTrang.innerHTML += `<a onclick="chuyenTrang(` + (trangHienTai + 1) + `)"><i class="fa fa-angle-right"></i></a>`;
//         divPhanTrang.innerHTML += `<a onclick="chuyenTrang(` + (soTrang) + `)"><i class="fa fa-angle-double-right"></i></a>`;
//     }
// }

function pushState(filters) {
    var str = "Index.php?";
    var fsort = "";
    for(var f of filters) {
        if(f.split('=')[0] != 'sort') {
            str += f + "&";
        } else {
            fsort = f;
        }
    }
    if(fsort != '') {
        str += fsort;
    } else if(str.indexOf("&") >= 0) {
        str = str.slice(0, str.length - 1); // loại bỏ "&" dư thừa
    }

    history.pushState("", "", str);
}

// ========== LỌC ===============
function createFilters(type, value) {
    var newFilters = [];

    var chuaCo = true;
    for (var f of CurrentFilters) {
        var fSplit = f.split('=');
        var fType = fSplit[0];
        var fValue = fSplit[1];

        if (fType == type) {
            newFilters.push(type + "=" + value);
            chuaCo = false;
        } else {
            newFilters.push(f);
        }
    }

    if (chuaCo) {
        newFilters.push(type + "=" + value);
    }

    return newFilters;
}

function craeteRemoveFilters(type) {
    var newFilters = [];

    for (var f of CurrentFilters) {
        var fSplit = f.split('=');
        var fType = fSplit[0];
        var fValue = fSplit[1];

        if (fType == type) {
            // không thêm cái cần remove
        } else {
            newFilters.push(f);
        }
    }

    return newFilters;
}

function removeAllFilters() {
    CurrentFilters = [];
    pushState([]);
    $(".choosedFilter").html("").css("display", "none");
    // Load all product series
    hienThiTatCaSanPham();
}

// Thêm bộ lọc đã chọn vào html
function addChoosedFilter(type, textInside) {
    var divChoosedFilter = document.getElementsByClassName('choosedFilter')[0];
    divChoosedFilter.innerHTML += (`<a onclick="filtersAjax(craeteRemoveFilters('`+type+`'))">
        <h3>` + textInside + ` <i class="fa fa-close"></i></h3>
        </a>`);
}

// Thêm nhiều bộ lọc cùng lúc 
function addAllChoosedFilter(filters) {
    // Lưu bộ lọc mới
    CurrentFilters = filters;

    if (filters.length) {
        // xóa tất cả bộ lọc cũ và thêm nút "Xóa bộ lọc"
        $(".choosedFilter").html(`<a onclick="removeAllFilters()"><h3>Xóa bộ lọc <i class="fa fa-close"></i></h3></a>`);
        $(".choosedFilter").css("display", "");

        for (var f of filters) {
            var data = f.split('=');
            var type = data[0];
            var value = data[1];

            switch (type) {
                case 'company':
                    var tenHang = "";
                    for (var c of DataCompany) {
                        if (c.MaLSP == value) {
                            tenHang = c.TenLSP;
                        }
                    }
                    addChoosedFilter('company', "Hãng " + tenHang);
                    break;

                case 'search':
                    addChoosedFilter('search', searchToString(value));
                    break;
                case 'price':
                    var prices = value.split('-');
                    addChoosedFilter('price', priceToString(prices[0], prices[1]));
                    break;
                case 'promo':
                    addChoosedFilter('promo', promoToString(value));
                    break;
                case 'star':
                    addChoosedFilter('star', starToString(value));
                    break;
                case 'sort':
                    var sorts = value.split('-');
                    var sortBy = sortToString(sorts[0]);
                    var kieuSapXep = (sorts[1] == 'asc' ? 'tăng dần' : 'giảm dần');
                    addChoosedFilter('sort', sortBy + kieuSapXep);
                    break;
                default:
                    // statements_def
                    break;
            }
        }
    }
}

// Thông báo nếu không có sản phẩm
function alertNotHaveProduct(coSanPham) {
    var thongbao = document.getElementById('khongCoSanPham');
    if (!coSanPham) {
        thongbao.style.width = "auto";
        thongbao.style.opacity = "1";
        thongbao.style.margin = "auto"; // Căn giữa
        thongbao.style.transitionDuration = "1s"; // hiện ra từ từ

    } else {
        thongbao.style.width = "0";
        thongbao.style.opacity = "0";
        thongbao.style.margin = "0";
        thongbao.style.transitionDuration = "0s"; // Ngay lâp tức biến mất
    }
}

// ========== Lọc TRONG TRANG ============
// Hiển thị Sản phẩm
function showLi(li) {
    li.style.opacity = 1;
    li.style.width = "239px";
    li.style.borderWidth = "1px";
}
// Ẩn sản phẩm
function hideLi(li) {
    li.style.width = 0;
    li.style.opacity = 0;
    li.style.borderWidth = "0";
}

// Lấy mảng sản phẩm trong trang hiện tại (ở dạng tag html)
function getLiArray() {
    var ul = document.getElementById('products');
    var listLi = ul.getElementsByTagName('li');
    return listLi;
}

// lọc theo tên
function getNameFromLi(li) {
    var a = li.getElementsByTagName('a')[0];
    var h3 = a.getElementsByTagName('h3')[0];
    var name = h3.innerHTML;
    return name;
}

// DEPRECATED: In-page filtering by name - removed search bar
// function filterProductsName(ele) {
//     var filter = ele.value.toUpperCase();
//     var listLi = getLiArray();
//     var coSanPham = false;
//     var soLuong = 0;
//     for (var i = 0; i < listLi.length; i++) {
//         if (getNameFromLi(listLi[i]).toUpperCase().indexOf(filter) > -1 &&
//             soLuong < ProductsPerPage) {
//             showLi(listLi[i]);
//             coSanPham = true;
//             soLuong++;
//         } else {
//             hideLi(listLi[i]);
//         }
//     }
//     alertNotHaveProduct(coSanPham);
// }

// ================= Hàm khác ==================

// Thêm banner
function addBanner(img, link) {
    // Add version query to bypass cache
    var imgWithVersion = img + '?v=' + new Date().getTime();
    // If using Swiper, append slides to .swiper-wrapper; otherwise append to owl-carousel
    var swiperWrapper = document.querySelector('.hero-swiper .swiper-wrapper');
    if (swiperWrapper) {
        var slide = `<div class="swiper-slide"><a href="` + link + `"><img data-src="` + imgWithVersion + `" class="lazyload" alt=""></a></div>`;
        swiperWrapper.innerHTML += slide;
    } else {
        var newDiv = `<div class='item'><img src=` + imgWithVersion + `></div>`;
        var banner = document.getElementsByClassName('owl-carousel')[0];
        if (banner) banner.innerHTML += newDiv;
    }
}

function addSmallBanner(img) {
    var newimg = `<img data-src="` + img + `" class="lazyload" style="width:100%;">`;
    var smallbanner = document.getElementsByClassName('smallbanner')[0];
    if (smallbanner) smallbanner.innerHTML += newimg;
}

// Thêm hãng sản xuất - DEPRECATED
function addCompany(img, nameCompany) {
    var khung_hangSanXuat = document.getElementsByClassName('companyMenu')[0];
    if (!khung_hangSanXuat) return; // Element không tồn tại, bỏ qua
    
    // Add version query to bypass cache
    var imgWithVersion = img + '?v=' + new Date().getTime();
    var new_tag = `<button onclick="filtersAjax(['company=` + nameCompany + `'])"><img src=` + imgWithVersion + `></button>`;
    khung_hangSanXuat.innerHTML += new_tag;
}

// Thêm chọn mức giá - DEPRECATED: không còn dùng filter cũ
function addPricesRange(min, max) {
    var element = document.getElementsByClassName('pricesRangeFilter')[0];
    if (!element) return; // Element không tồn tại, bỏ qua
    var dropdown = element.getElementsByClassName('dropdown-content')[0];
    if (!dropdown) return;
    
    var text = priceToString(min, max);
    var value = min + "-" + max;
    var checkbox = `
        <label>
            <input type="checkbox" value="${value}" onchange="handlePriceFilter(this, '${value}')">
            ${text}
        </label>
    `;
    dropdown.innerHTML += checkbox;
}

// Handle price filter checkbox
function handlePriceFilter(checkbox, value) {
    console.log('handlePriceFilter deprecated - no longer using old filters');
}

// Thêm chọn khuyến mãi - DEPRECATED
function addPromotion(name) {
    var element = document.getElementsByClassName('promosFilter')[0];
    if (!element) return;
    var dropdown = element.getElementsByClassName('dropdown-content')[0];
    if (!dropdown) return;
    
    var text = promoToString(name);
    var promo = `<a onclick="filtersAjax(createFilters('promo', '` + name + `'))">` + text + `</a>`;
    dropdown.innerHTML += promo;
}

// Thêm chọn số lượng sao - DEPRECATED
function addStarFilter(value) {
    var element = document.getElementsByClassName('starFilter')[0];
    if (!element) return;
    var dropdown = element.getElementsByClassName('dropdown-content')[0];
    if (!dropdown) return;
    
    var text = starToString(value);
    var star = `<a onclick="filtersAjax(createFilters('star', '` + value + `'))">` + text + `</a>`;
    dropdown.innerHTML += star;
}

// Thêm chọn sắp xếp theo giá - DEPRECATED
function addSortFilter(type, nameFilter, text) {
    var element = document.getElementsByClassName('sortFilter')[0];
    if (!element) return;
    var dropdown = element.getElementsByClassName('dropdown-content')[0];
    if (!dropdown) return;
    
    var sortTag = `<a onclick="filtersAjax(createFilters('sort', '` + (nameFilter + "-" + type) + `'))">` + text + `</a>`;
    dropdown.innerHTML += sortTag;
}

//  =================== ToString ====================
function searchToString(value) {
    return "Tìm '" + value + "'";
}

// Chuyển mức giá về dạng chuỗi tiếng việt
function priceToString(min, max) {
    if (min == 0) return 'Below ' + (max / 1000).toLocaleString() + '.000 ₫';
    if (max == 0) return 'Above ' + (min / 1000).toLocaleString() + '.000 ₫';
    return (min / 1000).toLocaleString() + ' ₫ - ' + (max / 1000).toLocaleString() + '.000 ₫';
}

// Chuyển khuyến mãi vễ dạng chuỗi tiếng việt
function promoToString(name) {
    switch (name) {
        case 'tragop':
            return 'Trả góp';
        case 'giamgia':
            return 'Giảm giá';
        case 'giareonline':
            return 'Giá rẻ online';
        case 'moiramat':
            return 'Mới ra mắt';
        case 'Nothing':
            return 'Không khuyến mãi';
    }
}

// Chuyển số sao về dạng chuỗi tiếng việt
function starToString(star) {
    return 'Từ ' + star + ' sao trở lên';
}

// Chuyển các loại sắp xếp về dạng chuỗi tiếng việt
function sortToString(sortBy) {
    switch (sortBy) {
        case 'DonGia':
            return 'Giá ';
        case 'SoSao':
            return 'Sao ';
        case 'SoDanhGia':
            return 'Đánh giá ';
        case 'TenSP':
            return 'Tên ';
        default:
            return '';
    }
}

// Hàm Test, chưa sử dụng
function hideSanPhamKhongThuoc(list) {
    var allLi = getLiArray();
    for (var i = 0; i < allLi.length; i++) {
        var hide = true;
        for (var j = 0; j < list.length; j++) {
            if (getNameFromLi(allLi[i]) == list[j].name) {
                hide = false;
                break;
            }
        }
        if (hide) hideLi(allLi[i]);
    }
}

//companysMenu responsive
function setCompanysMenu() {
    var content = document.getElementsByClassName("companyMenu")[0];
    if (content.style.maxHeight) {
        content.style.maxHeight = null;
        document.getElementById("iconOpenMenu").style.display = "block";
        document.getElementById("iconCloseMenu").style.display = "none";
    } else {
        content.style.maxHeight = content.scrollHeight + "px";
        document.getElementById("iconOpenMenu").style.display = "none";
        document.getElementById("iconCloseMenu").style.display = "block";
    }
}

// ==================== SAMSUNG STYLE FILTER FUNCTIONS ====================

// Initialize Samsung style filters
function initSamsungFilters() {
    // Add price range filters
    addPricesRangeSamsung(0, 4000000);
    addPricesRangeSamsung(4000000, 7000000);
    addPricesRangeSamsung(7000000, 15000000);
    addPricesRangeSamsung(15000000, 25000000);
    addPricesRangeSamsung(25000000, 0);
    
    // Add promotion filters
    addPromotionSamsung('giamgia', 'Giảm giá');
    addPromotionSamsung('tragop', 'Trả góp 0%');
    addPromotionSamsung('moiramat', 'Mới ra mắt');
    addPromotionSamsung('giareonline', 'Giá rẻ online');
    
    // Add star filters
    addStarFilterSamsung(5);
    addStarFilterSamsung(4);
    addStarFilterSamsung(3);
    addStarFilterSamsung(2);
    addStarFilterSamsung(1);
}

// Add price range option to Samsung dropdown
function addPricesRangeSamsung(min, max) {
    var text = priceToStringSamsung(min, max);
    var value = min + "-" + max;
    var checkbox = `
        <label class="samsung-filter-option">
            <input type="checkbox" value="${value}" onchange="handlePriceFilterSamsung(this)">
            <span class="checkmark"></span>
            ${text}
        </label>
    `;
    
    var container = document.querySelector('.pricesRangeFilter .dropdown-content-samsung');
    if (container) {
        container.innerHTML += checkbox;
    }
}

// Add promotion option to Samsung dropdown
function addPromotionSamsung(value, text) {
    var checkbox = `
        <label class="samsung-filter-option">
            <input type="checkbox" value="${value}" onchange="handlePromoFilterSamsung(this)">
            <span class="checkmark"></span>
            ${text}
        </label>
    `;
    
    var container = document.querySelector('.promosFilter .dropdown-content-samsung');
    if (container) {
        container.innerHTML += checkbox;
    }
}

// Add star filter option to Samsung dropdown
function addStarFilterSamsung(star) {
    var stars = '';
    for (var i = 1; i <= 5; i++) {
        if (i <= star) {
            stars += '<i class="fa fa-star" style="color: #ffc107;"></i>';
        } else {
            stars += '<i class="fa fa-star-o" style="color: #ccc;"></i>';
        }
    }
    var text = stars + ' <span>từ ' + star + ' sao</span>';
    
    var checkbox = `
        <label class="samsung-filter-option star-option">
            <input type="checkbox" value="${star}" onchange="handleStarFilterSamsung(this)">
            <span class="checkmark"></span>
            ${text}
        </label>
    `;
    
    var container = document.querySelector('.starFilter .dropdown-content-samsung');
    if (container) {
        container.innerHTML += checkbox;
    }
}

// Price to string for Samsung style
function priceToStringSamsung(min, max) {
    if (min == 0) return 'Below ' + (max / 1000000).toFixed(0) + '.000.000 ₫';
    if (max == 0) return 'Above ' + (min / 1000000).toFixed(0) + '.000.000₫';
    return (min / 1000000).toFixed(0) + '.000.000 ₫ - ' + (max / 1000000).toFixed(0) + (max % 1000000 == 0 ? '.000.000' : '.999.000') + ' ₫';
}

// Handle price filter for Samsung style
function handlePriceFilterSamsung(checkbox) {
    updateSamsungFilters();
}

// Handle promotion filter for Samsung style
function handlePromoFilterSamsung(checkbox) {
    updateSamsungFilters();
}

// Handle star filter for Samsung style
function handleStarFilterSamsung(checkbox) {
    updateSamsungFilters();
}

// Collect all Samsung filters and apply
function updateSamsungFilters() {
    console.log('updateSamsungFilters() - applying price filters');
    
    // Thu thập các filter giá đã chọn
    var priceFilters = [];
    document.querySelectorAll('.pricesRangeFilter .dropdown-content-samsung input:checked').forEach(function(input) {
        priceFilters.push(input.value); // Format: "min-max"
    });
    
    console.log('Selected price filters:', priceFilters);
    
    // Load product series với filter giá
    hienThiTatCaSanPham(priceFilters.length > 0 ? priceFilters : null);
    
    // Update filter tags display
    updateSamsungFilterTags();
}

// Update Samsung filter tags display
function updateSamsungFilterTags() {
    var tagsContainer = document.getElementById('samsungFilterTags');
    var clearAllWrapper = document.getElementById('clearAllWrapper');
    if (!tagsContainer) return;
    
    var tags = [];
    
    // Get price tags
    document.querySelectorAll('.pricesRangeFilter .dropdown-content-samsung input:checked').forEach(function(input) {
        var value = input.value;
        var parts = value.split('-');
        var min = parseInt(parts[0]);
        var max = parseInt(parts[1]);
        var text = priceTagToString(min, max);
        tags.push({ type: 'price', value: value, text: text });
    });
    
    // Get promo tags
    document.querySelectorAll('.promosFilter .dropdown-content-samsung input:checked').forEach(function(input) {
        var text = promoTagToString(input.value);
        tags.push({ type: 'promo', value: input.value, text: text });
    });
    
    // Get star tags
    document.querySelectorAll('.starFilter .dropdown-content-samsung input:checked').forEach(function(input) {
        var text = 'Từ ' + input.value + ' sao';
        tags.push({ type: 'star', value: input.value, text: text });
    });
    
    // Build tags HTML
    if (tags.length > 0) {
        var html = '';
        tags.forEach(function(tag) {
            html += '<span class="samsung-filter-tag" data-type="' + tag.type + '" data-value="' + tag.value + '">';
            html += tag.text;
            html += ' <button class="tag-close" onclick="removeSamsungFilterTag(this)">&times;</button>';
            html += '</span>';
        });
        tagsContainer.innerHTML = html;
        tagsContainer.style.display = 'flex';
        if (clearAllWrapper) clearAllWrapper.style.display = 'block';
    } else {
        tagsContainer.innerHTML = '';
        tagsContainer.style.display = 'none';
        if (clearAllWrapper) clearAllWrapper.style.display = 'none';
    }
}

// Price tag to string
function priceTagToString(min, max) {
    if (min == 0) return 'Dưới ' + (max / 1000000).toFixed(0) + ' triệu ₫';
    if (max == 0) return 'Trên ' + (min / 1000000).toFixed(0) + ' triệu ₫';
    return (min / 1000000).toFixed(0) + ' - ' + (max / 1000000).toFixed(0) + ' triệu ₫';
}

// Promo tag to string
function promoTagToString(value) {
    switch(value) {
        case 'giamgia': return 'Giảm giá';
        case 'tragop': return 'Trả góp 0%';
        case 'moiramat': return 'Mới ra mắt';
        case 'giareonline': return 'Giá rẻ online';
        default: return value;
    }
}

// Remove a single filter tag
function removeSamsungFilterTag(btn) {
    var tag = btn.closest('.samsung-filter-tag');
    var type = tag.dataset.type;
    var value = tag.dataset.value;
    
    // Find and uncheck the corresponding checkbox
    var selector = '';
    if (type === 'price') {
        selector = '.pricesRangeFilter .dropdown-content-samsung input[value="' + value + '"]';
    } else if (type === 'promo') {
        selector = '.promosFilter .dropdown-content-samsung input[value="' + value + '"]';
    } else if (type === 'star') {
        selector = '.starFilter .dropdown-content-samsung input[value="' + value + '"]';
    }
    
    var checkbox = document.querySelector(selector);
    if (checkbox) {
        checkbox.checked = false;
    }
    
    // Update filters
    updateSamsungFilters();
}

// Clear all Samsung filters
function clearAllSamsungFilters() {
    // Uncheck all checkboxes
    document.querySelectorAll('.dropdown-content-samsung input[type="checkbox"]').forEach(function(cb) {
        cb.checked = false;
    });
    
    // Update filters (this will also clear tags and hide clear button)
    updateSamsungFilters();
}

// Update filter button visual states based on selected options
function updateFilterButtonStates() {
    // Price filter
    var priceChecked = document.querySelectorAll('.pricesRangeFilter .dropdown-content-samsung input:checked').length;
    var priceBtn = document.querySelector('.pricesRangeFilter .filter-btn');
    if (priceBtn) {
        if (priceChecked > 0) {
            priceBtn.classList.add('active');
            priceBtn.innerHTML = 'Khoảng Giá (' + priceChecked + ') <i class="fa fa-angle-down"></i>';
        } else {
            priceBtn.classList.remove('active');
            priceBtn.innerHTML = 'Khoảng Giá <i class="fa fa-angle-down"></i>';
        }
    }
    
    // Promo filter
    var promoChecked = document.querySelectorAll('.promosFilter .dropdown-content-samsung input:checked').length;
    var promoBtn = document.querySelector('.promosFilter .filter-btn');
    if (promoBtn) {
        if (promoChecked > 0) {
            promoBtn.classList.add('active');
            promoBtn.innerHTML = 'Khuyến mãi (' + promoChecked + ') <i class="fa fa-angle-down"></i>';
        } else {
            promoBtn.classList.remove('active');
            promoBtn.innerHTML = 'Khuyến mãi <i class="fa fa-angle-down"></i>';
        }
    }
    
    // Star filter
    var starChecked = document.querySelectorAll('.starFilter .dropdown-content-samsung input:checked').length;
    var starBtn = document.querySelector('.starFilter .filter-btn');
    if (starBtn) {
        if (starChecked > 0) {
            starBtn.classList.add('active');
            starBtn.innerHTML = 'Số lượng sao (' + starChecked + ') <i class="fa fa-angle-down"></i>';
        } else {
            starBtn.classList.remove('active');
            starBtn.innerHTML = 'Số lượng sao <i class="fa fa-angle-down"></i>';
        }
    }
}

// Toggle Sort Dropdown
function toggleSortDropdown() {
    var dropdown = document.querySelector('.sort-dropdown-samsung');
    dropdown.classList.toggle('open');
    
    // Close when clicking outside
    document.addEventListener('click', function closeDropdown(e) {
        if (!dropdown.contains(e.target)) {
            dropdown.classList.remove('open');
            document.removeEventListener('click', closeDropdown);
        }
    });
}

// Set Sort Option
function setSortOption(value, text) {
    document.getElementById('currentSort').textContent = text;
    
    // Update active state
    var links = document.querySelectorAll('.sort-dropdown-content a');
    links.forEach(function(link) {
        link.classList.remove('active');
        link.innerHTML = link.textContent;
    });
    
    event.target.classList.add('active');
    event.target.innerHTML = '<i class="fa fa-check"></i> ' + text;
    
    // Close dropdown
    document.querySelector('.sort-dropdown-samsung').classList.remove('open');
    
    // Apply sort (you can add actual sorting logic here)
    console.log('Sort by:', value);
    
    event.preventDefault();
}

// Toggle Filter Button
function toggleFilterBtn(btn) {
    btn.classList.toggle('active');
}

// Toggle Dropdown Samsung
function toggleDropdownSamsung(btn) {
    var dropdown = btn.closest('.dropdown-samsung');
    var wasOpen = dropdown.classList.contains('open');
    
    // Close all other dropdowns
    document.querySelectorAll('.dropdown-samsung').forEach(function(d) {
        d.classList.remove('open');
    });
    
    if (!wasOpen) {
        dropdown.classList.add('open');
    }
    
    // Close when clicking outside
    document.addEventListener('click', function closeDropdown(e) {
        if (!dropdown.contains(e.target)) {
            dropdown.classList.remove('open');
            document.removeEventListener('click', closeDropdown);
        }
    });
}

// Update product count in Samsung filter bar
function updateProductCountSamsung(count) {
    var countEl = document.getElementById('productCount');
    if (countEl) {
        countEl.textContent = count;
    }
}
