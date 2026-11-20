var TotalPrice = 0;
window.onload = function() {
    khoiTao();

    // thêm tags (từ khóa) vào khung tìm kiếm
    var tags = ["Samsung", "iPhone", "Huawei", "Oppo", "Mobi"];
    for (var t of tags) addTags(t, "Index.php?search=" + t)

    var listGioHang = getListGioHang();
    getListFromDB(listGioHang);
    
    console.log("=== ĐANG GẮN EVENT HANDLER ===");
    
    // Thêm event handler cho button xác nhận thanh toán
    $(document).on('click', '#btnXacNhan', function(e) {
        console.log("=== BUTTON CLICKED ===");
        e.preventDefault();
        e.stopPropagation();
        xacNhanThanhToan();
    });
    
    console.log("=== ĐÃ GẮN EVENT HANDLER XONG ===");
}

function getListFromDB(list) {
    console.log("=== getListFromDB called ===");
    console.log("Cart list:", list);
    
    if (!list || !list.length) {
        console.log("Cart is empty or null");
        addProductToTable(list);
        return;
    };

    // Chỉ hỗ trợ series products (sản phẩm cũ đã bị xóa)
    var seriesProducts = [];
    
    for (var p of list) {
        console.log("Processing item:", p);
        if (p.isSeries) {
            console.log("Series product found:", p.masp);
            seriesProducts.push(p);
        }
        // Bỏ qua sản phẩm cũ (không có isSeries flag)
    }
    
    console.log("Series products:", seriesProducts.length);

    var allProducts = [];
    var pendingRequests = 0;

    // Xử lý series products only
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
                    for (var item of data.data) {
                        // Tìm cart item tương ứng
                        for (var g of seriesProducts) {
                            if (g.masp === item.productId) {
                                item.SoLuongTrongGio = g.soLuong;
                                item.isSeries = true;
                                item.seriesConfig = g.seriesConfig;
                                break;
                            }
                        }
                    }
                    allProducts = allProducts.concat(data.data);
                }
                pendingRequests--;
                if (pendingRequests === 0) {
                    addProductToTable(allProducts);
                }
            },
            error: function(e) {
                console.log("Error loading series products:", e.responseText);
                pendingRequests--;
                if (pendingRequests === 0) {
                    addProductToTable(allProducts);
                }
            }
        });
    }

    // Nếu không có request nào
    if (pendingRequests === 0) {
        addProductToTable([]);
    }
}

function addProductToTable(listProduct) {
    var container = document.getElementsByClassName('listSanPham')[0];

    // Empty cart
    if (!listProduct || listProduct.length == 0) {
        container.innerHTML = `
            <div class="cart-empty">
                <i class="fa fa-shopping-cart"></i>
                <h2>Giỏ hàng đang trống</h2>
                <p>Chào mừng trở lại! Giỏ hàng của bạn hiện đang trống. Khám phá các sản phẩm mới và thêm vào giỏ hàng ngay.</p>
                <a href="Index.php" class="btn-continue-shopping">Tiếp tục mua sắm</a>
            </div>
            <div class="recommended-products">
                <h2>Bạn cũng có thể thích</h2>
                <div class="recommended-list" id="recommendedList">
                    <div class="loading">Đang tải sản phẩm...</div>
                </div>
            </div>
        `;
        loadRecommendedProducts();
        return;
    }

    var totalPrice = 0;
    var itemsHTML = '';

    // Generate cart items
    for (var i = 0; i < listProduct.length; i++) {
        var p = listProduct[i];
        var masp = p.MaSP || p.productId;
        var soluongSp = p.SoLuongTrongGio;
        var price = 0;
        var thanhtien = 0;
        var tensp = '';
        var hinhanh = '';
        var configInfo = '';

        if (p.isSeries) {
            // Series product
            tensp = p.seriesName || p.TenSP;
            hinhanh = p.image || p.HinhAnh || 'img/products/default.png';
            
            // Lấy giá sản phẩm gốc
            var basePrice = Number(p.price || 0);
            
            // Lấy thông tin care plan từ seriesConfig
            var carePlanInfo = '';
            var carePlanPrice = 0;
            if (p.seriesConfig && p.seriesConfig.carePlanId && p.seriesConfig.carePlanId > 0) {
                carePlanPrice = Number(p.seriesConfig.carePlanPrice || 0);
                carePlanInfo = `
                    <div class="cart-item-config cart-careplan">
                        <span class="config-label">3AM Care+:</span> +${numToString(carePlanPrice)} ₫
                    </div>
                `;
            }
            
            // Tổng giá = giá sản phẩm + care plan
            price = basePrice + carePlanPrice;
            thanhtien = price * soluongSp;
            
            // Tạo thông tin cấu hình
            configInfo = `
                <div class="cart-item-config">
                    <span class="config-label">Cấu hình:</span> ${p.configName || ''}
                </div>
                <div class="cart-item-config">
                    <span class="config-label">Màu sắc:</span> ${p.colorName || ''}
                </div>
                ${carePlanInfo}
            `;
        } else {
            // Regular product
            tensp = p.TenSP;
            hinhanh = p.HinhAnh;
            price = Number(p.DonGia) - Number(p.KM.GiaTriKM);
            thanhtien = price * soluongSp;
        }

        itemsHTML += `
            <div class="cart-item">
                <img class="cart-item-image" src="${hinhanh}" alt="${tensp}">
                
                <div class="cart-item-info">
                    <a href="${p.isSeries ? 'ProductDetail.php?slug=' + (p.slug || '') : 'ProductDetail.php?' + masp}" class="cart-item-name" title="Xem chi tiết">
                        ${tensp}
                    </a>
                    ${p.isSeries ? '' : '<div class="cart-item-sku">Mã SP: ' + masp + '</div>'}
                    ${configInfo}
                    <div class="cart-item-stock">Còn hàng</div>
                </div>
                
                <div class="cart-item-price">${numToString(price)} ₫</div>
                
                <div class="cart-item-quantity">
                    <button onclick="giamSoLuong('${masp}')" ${soluongSp <= 1 ? 'disabled' : ''}>
                        <i class="fa fa-minus"></i>
                    </button>
                    <input type="number" value="${soluongSp}" min="1" max="99" 
                           onchange="capNhatSoLuongFromInput(this, '${masp}')">
                    <button onclick="tangSoLuong('${masp}')">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
                
                <div class="cart-item-total">${numToString(thanhtien)} ₫</div>
                
                <div class="cart-item-remove">
                    <button onclick="xoaSanPhamTrongGioHang('${masp}', '${tensp}')">
                        <i class="fa fa-trash-o"></i>
                    </button>
                </div>
            </div>
        `;

        totalPrice += thanhtien;
    }

    TotalPrice = totalPrice;

    // Render cart layout
    container.innerHTML = `
        <div class="cart-container">
            <div class="cart-header">
                <h1>Giỏ hàng</h1>
                <div class="cart-item-count">${listProduct.length} sản phẩm</div>
            </div>
            
            <div class="cart-content">
                <div class="cart-items">
                    ${itemsHTML}
                </div>
                
                <div class="cart-summary">
                    <h2>Bản tóm tắt</h2>
                    
                    <div class="promo-code">
                        <div class="promo-code-title">Mã giảm giá</div>
                        <div class="promo-code-input">
                            <input type="text" placeholder="Nhập mã giảm giá">
                            <button>Áp dụng</button>
                        </div>
                    </div>
                    
                    <div class="summary-row">
                        <span class="summary-row-label">Tạm tính (${listProduct.length} sản phẩm)</span>
                        <span class="summary-row-value">${numToString(totalPrice)} ₫</span>
                    </div>
                    
                    <div class="summary-row">
                        <span class="summary-row-label">Phí vận chuyển</span>
                        <span class="summary-row-value">Miễn phí</span>
                    </div>
                    
                    <div class="summary-divider"></div>
                    
                    <div class="summary-total">
                        <span class="summary-total-label">Tổng cộng</span>
                        <span class="summary-total-value">${numToString(totalPrice)} ₫</span>
                    </div>
                    
                    <div class="summary-note">
                        (Đã bao gồm VAT nếu có)
                    </div>
                    
                    <div class="cart-actions">
                        <button class="btn-checkout" onclick="window.location.href='Checkout.php'">
                            Thanh Toán
                        </button>
                        <button class="btn-clear" onclick="xoaHet()">
                            Xóa tất cả
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function xoaSanPhamTrongGioHang(masp, tensp) {

    Swal.fire({
        title: "Xóa mặt hàng khỏi giỏ hàng?",
        html: "Bạn sắp xóa <b>" + tensp + "</b> khỏi giỏ hàng của mình. Bạn có chắc chắn muốn tiếp tục với lựa chọn này không?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: 'Đúng',
        cancelButtonText: 'Không',
        confirmButtonColor: '#0066ff',
        cancelButtonColor: '#fff',
        customClass: {
            confirmButton: 'swal2-confirm-custom',
            cancelButton: 'swal2-cancel-custom'
        }

    }).then((result) => {
        if (result.value) {
            var listProduct = getListGioHang();

            for (var i = 0; i < listProduct.length; i++) {
                if (listProduct[i].masp == masp) {
                    listProduct.splice(i, 1);
                    break;
                }
            }

            capNhatMoiThu(listProduct);
        }
    });
}

function thanhToan() {
    var listProduct = getListGioHang();
    if (!listProduct.length) {
        Swal.fire({
            type: 'info',
            title: "Rỗng",
            grow: 'row',
            text: 'Không có mặt hàng nào để thanh toán.'
        });
        return;
    }

    getCurrentUser((user) => {
        if (user == null) {
            Swal.fire({
                title: 'Xin chào!',
                text: 'Bạn cần đăng nhập để mua hàng',
                type: 'info',
                grow: 'row',
                confirmButtonText: 'Đăng nhập',
                cancelButtonText: 'Trở về',
                showCancelButton: true
            }).then((result) => {
                if (result.value) {
                    showTaiKhoan(true);
                }
            })

        } else if (user.TrangThai == 0) {
            Swal.fire({
                title: 'Tài Khoản Bị Khóa!',
                text: 'Tài khoản của bạn hiện đang bị khóa nên không thể mua hàng!',
                type: 'error',
                grow: 'row',
                confirmButtonText: 'Trở về',
                footer: '<a href>Liên hệ với Admin</a>'
            });
        } else {
        	UserHienTai = user;  // biến toàn cục
        	htmlThanhToan(user);
        }

    }, (error) => {
        console.log(error.responseText);
    });
}

function htmlThanhToan(userHienTai) {
	console.log('abc')

	$("#thongtinthanhtoan").html(`
		<div class="form-group">
		    <p>Tổng tiền : <h2>` + TotalPrice.toLocaleString() + `đ </h2></p>
		</div>
		<div class="form-group">
		    <label for="inputTen">Tên người nhận</label>
		    <input class="form-control input-sm" id="inputTen" required type="text" value="` + (userHienTai.Ho + " " + userHienTai.Ten) + `">
		</div>
		<div class="form-group">
		    <label for="inputSDT">SDT người nhận</label>
		    <input class="form-control input-sm" id="inputSDT" required type="text" pattern="\\d*" minlength="10" maxlength="12" value="` + userHienTai.SDT + `">
		</div>
		<div class="form-group">
		    <label for="inputDiaChi">Địa chỉ giao hàng</label>
		    <input class="form-control input-sm" id="inputDiaChi" required type="text" value="` + userHienTai.DiaChi + `">
		</div>
		<div class="form-group">
		    <select class="browser-default custom-select" id="selectHinhThucTT">
		      <option value="" disabled selected>Hình thức thanh toán</option>
			  <option value="Trực tiếp khi nhận hàng">Trực tiếp khi nhận hàng</option>
			  <option value="Qua thẻ ngân hàng">Qua thẻ ngân hàng</option>
			</select>
		</div>
	`);
}

function xacNhanThanhToan() {
	console.log("=== BẮT ĐẦU xacNhanThanhToan ===");
	console.log("UserHienTai:", UserHienTai);
	
	// Kiểm tra đăng nhập
	if (!UserHienTai || !UserHienTai.MaND) {
		Swal.fire({
			type: 'warning',
			title: 'Chưa đăng nhập',
			text: 'Vui lòng đăng nhập để đặt hàng!'
		});
		return false;
	}
	
	// Kiểm tra xem đang xử lý không
	if (window.isProcessingPayment) {
		console.log("Đang xử lý thanh toán, vui lòng chờ...");
		return false;
	}
	
	// Validate form
	var ten = $("#inputTen").val();
	var sdt = $("#inputSDT").val();
	var diaChi = $("#inputDiaChi").val();
	var phuongThucTT = $("#selectHinhThucTT").val();
	
	console.log("Form values:", {ten, sdt, diaChi, phuongThucTT});
	
	if (!ten || !sdt || !diaChi || !phuongThucTT) {
		Swal.fire({
			type: 'warning',
			title: 'Thiếu thông tin',
			text: 'Vui lòng điền đầy đủ thông tin!'
		});
		return false;
	}
	
	// Set flag đang xử lý
	window.isProcessingPayment = true;
	
	// Disable button submit
	$('#btnXacNhan').prop('disabled', true).text('Đang xử lý...');
	
	var dulieu = {
		maNguoiDung: UserHienTai.MaND,
		tenNguoiNhan: ten,
		sdtNguoiNhan: sdt,
		diaChiNguoiNhan: diaChi,
		phuongThucTT: phuongThucTT,
		dssp: getListGioHang(),
		tongTien: TotalPrice,
		ngayLap: new Date().toMysqlFormat()
	}
	
	console.log("Gửi đơn hàng:", dulieu);
	
	// Hiển thị loading
	Swal.fire({
		title: 'Đang xử lý...',
		text: 'Vui lòng chờ trong giây lát',
		allowOutsideClick: false,
		allowEscapeKey: false,
		showConfirmButton: false,
		onBeforeOpen: () => {
			Swal.showLoading();
		}
	});

	$.ajax({
		type: "POST",
		url: "Api/ProcessPayment.php",
		dataType: "json",
		data: {
			request: "themdonhang",
			dulieu: dulieu
		},
		success: function(data) {
			console.log("Đặt hàng thành công:", data);
			
			// Reset flag
			window.isProcessingPayment = false;
			
			// Enable button lại
			$('#btnXacNhan').prop('disabled', false).text('Xác nhận');
			
			// Xóa giỏ hàng NGAY LẬP TỨC
			capNhatMoiThu([]);
			
			// Đóng modal thanh toán trước
			$('#exampleModal').modal('hide');
			
			// Delay 500ms rồi mới hiện thông báo để thấy giỏ hàng clear
			setTimeout(function() {
				Swal.fire({
					type: 'success',
					title: 'Đặt hàng thành công!',
					text: 'Cảm ơn bạn đã mua hàng. Chúng tôi sẽ liên hệ với bạn sớm nhất.',
					confirmButtonText: 'OK'
				});
			}, 500);
		},
		error: function(e) {
			console.error("Lỗi đặt hàng:", e.responseText);
			
			// Reset flag
			window.isProcessingPayment = false;
			
			// Enable button lại
			$('#btnXacNhan').prop('disabled', false).text('Xác nhận');
			
			Swal.fire({
				type: 'error',
				title: 'Lỗi đặt hàng!',
				text: 'Đã có lỗi xảy ra. Vui lòng thử lại.',
				html: e.responseText
			});
		}
	});

	return false;
}

function xoaHet() {
    var listProduct = getListGioHang();

    if (listProduct.length) {
        Swal.fire({
            title: 'Xóa mặt hàng khỏi giỏ hàng?',
            html: 'Bạn sắp xóa <b>tất cả sản phẩm</b> khỏi giỏ hàng của mình. Bạn có chắc chắn muốn tiếp tục với lựa chọn này không?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Đúng',
            cancelButtonText: 'Không',
            confirmButtonColor: '#0066ff',
            cancelButtonColor: '#fff',
            customClass: {
                confirmButton: 'swal2-confirm-custom',
                cancelButton: 'swal2-cancel-custom'
            }
        }).then((result) => {
            if (result.value) {
                listProduct = [];
                capNhatMoiThu(listProduct);
            }
        })
    }
}

// Cập nhật số lượng lúc nhập số lượng vào input
function capNhatSoLuongFromInput(inp, masp) {
    var soLuongMoi = Number(inp.value);
    if (!soLuongMoi || soLuongMoi <= 0) soLuongMoi = 1;

    var listProduct = getListGioHang();

    for (var p of listProduct) {
        if (p.masp == masp && p.soLuong > 0) {
            p.soLuong = soLuongMoi;
        }
    }

    capNhatMoiThu(listProduct);
}

function tangSoLuong(masp) {
    var listProduct = getListGioHang();

    for (var p of listProduct) {
        if (p.masp == masp) {
            p.soLuong++;
        }
    }

    capNhatMoiThu(listProduct);
}

function giamSoLuong(masp) {
    var listProduct = getListGioHang();

    for (var p of listProduct) {
        if (p.masp == masp && p.soLuong > 1) {
            p.soLuong--;
        }
    }

    capNhatMoiThu(listProduct);
}

function loadRecommendedProducts() {
    // Load recommended products từ product_series
    $.ajax({
        type: "POST",
        url: "Api/ProcessProductSeriesGet.php",
        dataType: "json",
        data: {
            request: "getallseries"
        },
        success: function(response) {
            console.log("Recommended products from series:", response);
            if (response.success && response.data) {
                // Chuyển đổi format để phù hợp với display function
                var products = response.data.map(function(series) {
                    return {
                        MaSP: 'series_' + series.id,
                        TenSP: series.series_name,
                        Hinh: series.series_image,
                        DonGia: series.display_price || 0,
                        GiaGiam: series.display_price || 0,
                        slug: series.slug
                    };
                });
                displayRecommendedProducts(products.slice(0, 9)); // Limit 9
            } else {
                $('#recommendedList').html('<p>Không có sản phẩm gợi ý</p>');
            }
        },
        error: function(xhr, status, error) {
            console.error("Error loading recommended products:", xhr.responseText);
            $('#recommendedList').html('<p>Không thể tải sản phẩm gợi ý</p>');
        }
    });
}

let currentRecommendedPage = 0;
let recommendedProducts = [];
const itemsPerPage = 3;

function displayRecommendedProducts(products) {
    console.log("Displaying products:", products);
    
    if (!products || products.length === 0) {
        document.getElementById('recommendedList').innerHTML = '<p>Không có sản phẩm gợi ý</p>';
        return;
    }

    recommendedProducts = products;
    currentRecommendedPage = 0;
    renderRecommendedPage();
}

function renderRecommendedPage() {
    const totalPages = Math.ceil(recommendedProducts.length / itemsPerPage);
    const startIndex = currentRecommendedPage * itemsPerPage;
    const endIndex = Math.min(startIndex + itemsPerPage, recommendedProducts.length);
    const productsToShow = recommendedProducts.slice(startIndex, endIndex);
    
    let html = '<div class="recommended-carousel">';
    html += '<button class="carousel-btn prev" onclick="prevRecommendedPage()" ' + 
            (currentRecommendedPage === 0 ? 'disabled' : '') + '>‹</button>';
    
    html += '<div class="recommended-grid">';
    productsToShow.forEach(function(product) {
        const price = Number(product.GiaGiam || product.DonGia);
        const originalPrice = Number(product.DonGia);
        const imageUrl = product.Hinh || product.HinhAnh;
        
        // Sử dụng slug cho series products, fallback về masp cho legacy
        const productUrl = product.slug 
            ? `ProductDetail.php?slug=${product.slug}`
            : `ProductDetail.php?masp=${product.MaSP}`;
        
        html += `
            <div class="recommended-item">
                <a href="${productUrl}">
                    <img src="${imageUrl}" alt="${product.TenSP}" onerror="this.src='img/products/default.jpg'">
                    <h3>${product.TenSP}</h3>
                    <div class="price-box">
                        <span class="current-price">${price.toLocaleString('vi-VN')} ₫</span>
                        ${originalPrice > price ? `
                            <span class="original-price">${originalPrice.toLocaleString('vi-VN')} ₫</span>
                            <span class="discount-badge">tiết kiệm ${(originalPrice - price).toLocaleString('vi-VN')} ₫</span>
                        ` : ''}
                    </div>
                </a>
            </div>
        `;
    });
    html += '</div>';
    
    html += '<button class="carousel-btn next" onclick="nextRecommendedPage()" ' + 
            (currentRecommendedPage >= totalPages - 1 ? 'disabled' : '') + '>›</button>';
    html += '</div>';
    
    // Thêm dots indicator
    html += '<div class="carousel-dots">';
    for (let i = 0; i < totalPages; i++) {
        html += `<span class="dot ${i === currentRecommendedPage ? 'active' : ''}" onclick="goToRecommendedPage(${i})"></span>`;
    }
    html += '</div>';
    
    document.getElementById('recommendedList').innerHTML = html;
}

function nextRecommendedPage() {
    const totalPages = Math.ceil(recommendedProducts.length / itemsPerPage);
    if (currentRecommendedPage < totalPages - 1) {
        currentRecommendedPage++;
        renderRecommendedPage();
    }
}

function prevRecommendedPage() {
    if (currentRecommendedPage > 0) {
        currentRecommendedPage--;
        renderRecommendedPage();
    }
}

function goToRecommendedPage(page) {
    currentRecommendedPage = page;
    renderRecommendedPage();
}

function addToCart(masp) {
    // Tìm sản phẩm trong danh sách để lấy tên
    const product = recommendedProducts.find(p => p.MaSP == masp);
    const tensp = product ? product.TenSP : 'sản phẩm';
    
    themVaoGioHang(masp, tensp);
    
    // Reload sau khi thêm để cập nhật số lượng giỏ hàng
    setTimeout(() => {
        location.reload();
    }, 2000);
}

function capNhatMoiThu(list) { // Mọi thứ
    console.log("=== CẬP NHẬT GIỎ HÀNG ===");
    console.log("Danh sách mới:", list);
    console.log("Số lượng:", list.length);
    
    // Lưu vào localStorage TRƯỚC
    setListGioHang(list);
    
    // Sau đó mới animate (để getSoLuongGioHang() lấy số mới)
    animateCartNumber();

    // cập nhật danh sách sản phẩm ở table
    getListFromDB(list);
}
