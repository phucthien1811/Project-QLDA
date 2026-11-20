<!DOCTYPE html>
<html lang="vi">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="icon" type="image/x-icon" href="/Project-PHP/img/favicon.ico?v=2">
    <link rel="shortcut icon" type="image/x-icon" href="/Project-PHP/img/favicon.ico?v=2">

	<title>Thanh toán - Thế giới điện thoại</title>

	<!-- Load font awesome icons -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css">

	<!-- Jquery -->
	<script src="Lib/JQuery/Jquery.min.js"></script>

	<!-- Bootstrap -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>

	<!-- Sweet Alert -->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>

	<!-- our files -->
	<!-- css -->
	<link rel="stylesheet" href="Styles/Base.css">
	<link rel="stylesheet" href="Styles/SamsungHeader.css">
	<link rel="stylesheet" href="Styles/CheckoutPage.css">
	<link rel="stylesheet" href="Styles/Footer.css">
	<!-- js -->
	<script src="Scripts/Common.js?v=<?php echo time(); ?>"></script>
	<script src="Scripts/SamsungHeader.js?v=<?php echo time(); ?>"></script>
	<script src="Scripts/Checkout.js?v=<?php echo time(); ?>"></script>

	<?php require_once "Api/HtmlRenderer.php"; ?>

</head>

<body>
	<?php addTopNav(); ?>

	<section style="min-height: 85vh; background: #f8f9fa;">
		<?php addHeader(); ?>

		<style>
			.containTaikhoan {
				display: none !important;
				visibility: hidden !important;
				opacity: 0 !important;
				pointer-events: none !important;
			}
		</style>

		<div class="checkout-container">
			<div class="checkout-content">
				<!-- Left side - Customer info and delivery -->
				<div class="checkout-left">
					<!-- Section 1: Chi tiết khách hàng - Form -->
					<div class="checkout-section" id="section1Form">
						<h2 class="section-title">1. Chi tiết khách hàng</h2>
						
						<div class="form-group">
							<label>E-mail *</label>
							<input type="email" id="email" class="form-control" placeholder="email@example.com" required>
						</div>

						<div class="form-row">
							<div class="form-group col-md-6">
								<label>Họ và tên *</label>
								<input type="text" id="hovaten" class="form-control" required>
							</div>
							<div class="form-group col-md-6">
								<label>Số điện thoại *</label>
								<input type="tel" id="sdt" class="form-control" required>
							</div>
						</div>
					</div>

					<div class="checkout-section" id="section1Address">
						<h2 class="section-title">Địa chỉ giao hàng</h2>
						<p class="section-description">Chúng tôi đang trong quá trình cập nhật địa chỉ giao hàng theo đơn vị hành chính mới, vui lòng chọn địa chỉ giao hàng theo đơn vị hành chính trong thời gian cập nhật hiện có trong.</p>

						<h3 class="subsection-title">Địa chỉ mới</h3>

						<div class="form-row">
							<div class="form-group col-md-6">
								<label>Số nhà *</label>
								<input type="text" id="sonha" class="form-control" required>
							</div>
							<div class="form-group col-md-6">
								<label>Đường phố *</label>
								<input type="text" id="duongpho" class="form-control" required>
							</div>
						</div>

						<div class="form-row">
							<div class="form-group col-md-6">
								<label>Tỉnh/Thành phố *</label>
								<input type="text" id="tinh" class="form-control" placeholder="VD: TP. Hồ Chí Minh" required>
							</div>
							<div class="form-group col-md-6">
								<label>Phường/Xã/Đặc khu *</label>
								<input type="text" id="phuong" class="form-control" placeholder="VD: Phường 12" required>
							</div>
						</div>

						<div class="form-group">
							<label>Làng/Khu phố (tùy chọn)</label>
							<input type="text" id="khupho" class="form-control" placeholder="">
						</div>

						<div class="checkout-options">
							<label class="checkout-option">
								<input type="checkbox" id="saveAddress">
								<span>Lưu cho các lần mua sắm sau</span>
							</label>
							<label class="checkout-option">
								<input type="checkbox" id="receivePromo">
								<span>Tôi muốn nhận thông báo qua email về giảm giá và khuyến mãi</span>
							</label>
							<label class="checkout-option">
								<input type="checkbox" id="agreeTerms" required>
								<span>Tôi hiểu và đồng ý với <a href="#" class="link-highlight">Điều khoản & Điều kiện</a> cũng như <a href="#" class="link-highlight">Chính sách quyền riêng tư</a> của 3AM.*</span>
							</label>
						</div>

						<div class="checkout-notes">
							<p><em>Lưu ý: 3AM từ chối giải quyết các yêu cầu phát sinh trong việc kê khai thuế đối với hóa đơn từ 05 triệu đồng trở lên thanh toán bằng tiền mặt/ thẻ tín dụng cá nhân.</em></p>
							<p>3AM không hỗ trợ lưu trữ, đơn hàng sẽ tự động bị hủy nếu bạn không sắp xếp nhận hàng trong vòng 7 ngày kể từ ngày đơn hàng được bàn giao qua đơn vị vận chuyển.</p>
							<p>Đối với các đơn hàng ngoài khu vực Hà Nội và Hồ Chí Minh, 3AM sẽ liên hệ lắp đặt trong vòng 24h kể từ khi nhận hàng.</p>
							<p><em>Đối với đơn hàng bao gồm sản phẩm chính và sản phẩm được tặng hoặc mua chung với giá ưu đãi, khi hủy đơn hàng, khách hàng phải hoàn trả toàn bộ sản phẩm bao gồm quà tặng, sản phẩm đi kèm, kể cả phụ kiện. Đầy nguyên bản, nguyên tag, không một vết xước.</em></p>
							<p><em>Địa chỉ giao hàng không thể thay đổi sau khi đặt, vui lòng kiểm tra kỹ địa chỉ trước khi thanh toán. Trường hợp quý khách cần thay đổi địa chỉ giao hàng sau khi đặt hàng, vui lòng hủy đơn hàng cũ và đặt đơn hàng mới.</em></p>
						</div>

						<button type="button" class="btn-continue-delivery" onclick="showDeliverySection()">
							Tiếp tục đến các tùy chọn giao hàng
						</button>
					</div>

					<!-- Section 1: Summary (hiển thị sau khi hoàn thành) -->
					<div class="checkout-section section-summary" id="section1Summary" style="display: none;">
						<div class="summary-header">
							<h2 class="section-title">1. Chi tiết khách hàng</h2>
							<a href="#" class="edit-link" onclick="editSection1(); return false;">
								<i class="fa fa-pencil"></i> Chỉnh sửa
							</a>
						</div>
						<div class="summary-content">
							<p class="summary-name" id="summaryName"></p>
							<p class="summary-address" id="summaryAddress"></p>
						</div>
					</div>

					<!-- Section 2: Hình thức giao hàng -->
					<div class="checkout-section" id="deliverySection" style="display: none;">
						<h2 class="section-title">2. Hình thức giao hàng</h2>
						<p class="section-subtitle">Bạn muốn nhận đơn đặt hàng của mình như thế nào?</p>

						<div class="delivery-options">
							<label class="delivery-option selected">
								<input type="radio" name="deliveryMethod" value="shipping" checked>
								<div class="delivery-option-content">
									<i class="fa fa-truck"></i>
									<span>Vận chuyển</span>
								</div>
							</label>
							<label class="delivery-option">
								<input type="radio" name="deliveryMethod" value="pickup">
								<div class="delivery-option-content">
									<i class="fa fa-map-marker"></i>
									<span>Nhận tại cửa hàng</span>
								</div>
							</label>
						</div>

						<div class="package-info">
							<span class="package-label">Kiện hàng <span id="packageCurrent">1</span> trên <span id="packageTotal">1</span></span>
						</div>

						<div class="package-product" id="packageProduct">
							<!-- Product info will be loaded here -->
						</div>

						<div class="shipping-option-section">
							<p class="shipping-title">Chọn một tùy chọn giao hàng</p>
							<div class="shipping-option-box selected">
								<div class="shipping-option-left">
									<input type="radio" name="shippingMethod" value="standard" checked>
									<div class="shipping-info">
										<strong>Giao hàng tiêu chuẩn</strong>
										<p>Giao hàng tiêu chuẩn trong vòng 07 ngày kể từ ngày nhận tin nhắn đơn hàng xuất kho và bàn giao cho đơn vị vận chuyển</p>
										<a href="#" class="shipping-app-link">Theo dõi đơn hàng tiện lợi hơn khi tải 3AM Shop App</a>
									</div>
								</div>
								<div class="shipping-price">Miễn phí</div>
							</div>
						</div>

						<button type="button" class="btn-continue-payment" onclick="showPaymentSection()">
							Tiếp tục thanh toán
						</button>
					</div>

					<!-- Section 2: Summary (hiển thị sau khi hoàn thành) -->
					<div class="checkout-section section-summary" id="section2Summary" style="display: none;">
						<div class="summary-header">
							<h2 class="section-title">2. Hình thức giao hàng</h2>
							<a href="#" class="edit-link" onclick="editSection2(); return false;">
								<i class="fa fa-pencil"></i> Chỉnh sửa
							</a>
						</div>
						<div class="summary-content">
							<p class="package-label-summary">Kiện hàng <span id="summaryPackageCurrent">1</span> trên <span id="summaryPackageTotal">1</span></p>
							<p class="summary-product-name" id="summaryProductName"></p>
							<p class="summary-delivery-date" id="summaryDeliveryDate"></p>
						</div>
					</div>

					<div class="checkout-section" id="paymentSection" style="display: none;">
						<h2 class="section-title">3. Thanh toán</h2>
						<p class="section-subtitle">Chọn các phương thức thanh toán dưới đây</p>

						<!-- Thanh toán trả trước -->
						<h3 class="payment-category-title">Thanh toán trả trước (Ví điện tử/QR code)</h3>
						
						<div class="payment-accordion">
							<div class="payment-accordion-item" id="paymentQR">
								<div class="payment-accordion-header" onclick="togglePaymentAccordion('paymentQR')">
									<span class="payment-accordion-title">Thanh toán Ví điện tử / QR Pay</span>
									<div class="payment-accordion-icons">
										<img src="https://developers.momo.vn/v3/assets/images/icon-52bd5808cecdb1970e1aeec3c31a3ee1.png" alt="MoMo" height="24">
									</div>
									<i class="fa fa-angle-down accordion-arrow"></i>
								</div>
								<div class="payment-accordion-content">
									<p class="payment-info-text">Sau khi đọc và chấp thuận Điều khoản & Điều kiện mua hàng, vui lòng nhấn "Tiếp tục" để được chuyển hướng đến trang thanh toán trực tuyến và hoàn thành quy trình mua hàng một cách an toàn, bảo mật.</p>
									
									<p class="payment-total">Tổng cộng: <strong id="paymentQRTotal">0</strong> VND</p>
									
									<label class="checkout-option payment-terms">
										<input type="checkbox" id="paymentQRAgreeTerms" required>
										<span>Tôi hiểu và đồng ý với <a href="#" class="link-highlight">Điều khoản & Điều kiện</a> cũng như <a href="#" class="link-highlight">Chính sách quyền riêng tư</a> của 3AM.</span>
									</label>
									
									<button type="button" class="btn-submit-order btn-qr-payment" onclick="xacNhanThanhToanQR()">
										Tiếp tục
									</button>
								</div>
							</div>
						</div>

						<!-- Phương thức thanh toán tiền mặt -->
						<h3 class="payment-category-title">Phương thức thanh toán</h3>
						
						<div class="payment-accordion">
							<div class="payment-accordion-item" id="paymentCash">
								<div class="payment-accordion-header" onclick="togglePaymentAccordion('paymentCash')">
									<span class="payment-accordion-title">Thanh toán tiền mặt (COD)</span>
									<div class="payment-accordion-icons">
										<i class="fa fa-money cash-icon"></i>
									</div>
									<i class="fa fa-angle-down accordion-arrow"></i>
								</div>
								<div class="payment-accordion-content">
									<p class="payment-info-text">Sau khi đọc và đồng ý với Điều kiện & Điều khoản, vui lòng chọn Tiếp tục.</p>
									<p class="payment-info-text">Yêu cầu xác nhận sẽ được gửi đến số điện thoại và email. Vui lòng xác nhận bằng cách điền OTP.</p>
									<p class="payment-info-text">Nếu không xác nhận, đơn hàng sẽ không được hoàn thành.</p>
									
									<p class="payment-total">Tổng cộng: <strong id="paymentTotalAmount">0</strong> VND</p>
									
									<label class="checkout-option payment-terms">
										<input type="checkbox" id="paymentAgreeTerms" required>
										<span>Tôi hiểu và đồng ý với <a href="#" class="link-highlight">Điều khoản & Điều kiện</a> cũng như <a href="#" class="link-highlight">Chính sách quyền riêng tư</a> của 3AM.</span>
									</label>
									
									<button type="button" class="btn-submit-order" onclick="xacNhanDatHang()">
										Tiếp tục
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Right side - Order summary -->
				<div class="checkout-right">
					<div class="order-summary">
						<h2 class="summary-title">Xem đơn hàng</h2>
						
						<div class="cart-edit">
							<span>Bạn có <strong class="cart-count">0</strong> mặt hàng trong giỏ hàng của mình</span>
							<a href="Cart.php" class="edit-cart">
								<i class="fa fa-pencil"></i> Chỉnh sửa
							</a>
						</div>

						<div class="order-items" id="orderItems">
							<!-- Items will be loaded here -->
						</div>

						<div class="promo-section">
							<div class="promo-code-title">Mã khuyến mãi</div>
							<div class="promo-code-input">
								<input type="text" id="promoCode" placeholder="Nhập Voucher hoặc Gift Code">
								<button class="btn-apply-promo">Áp dụng</button>
							</div>
							<a href="#" class="promo-link">Xem mã khuyến mãi</a>
						</div>

						<div class="summary-details">
							<div class="summary-row">
								<span>Tạm tính</span>
								<span class="summary-value" id="subtotal">0 ₫</span>
							</div>
							<div class="summary-row">
								<span>Phí vận chuyển</span>
								<span class="summary-value">Miễn phí</span>
							</div>
						</div>

						<div class="total-section">
							<div class="total-row">
								<span class="total-label">Tổng cộng</span>
								<span class="total-value" id="totalPrice">0 ₫</span>
							</div>
							<div class="total-note-blue">Bạn đã đăng nhập với tư cách: <strong id="userEmail">Blue</strong></div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</section>

	<!-- MoMo Payment Modal -->
	<div class="payment-modal-overlay" id="momoModalOverlay" onclick="closeMomoModal()">
		<div class="payment-modal" onclick="event.stopPropagation()">
			<div class="payment-modal-header">
				<div class="payment-modal-logo">
					<img src="https://developers.momo.vn/v3/assets/images/icon-52bd5808cecdb1970e1aeec3c31a3ee1.png" alt="MoMo" height="32">
					<span>MoMo</span>
				</div>
				<button class="payment-modal-close" onclick="closeMomoModal()">&times;</button>
			</div>
			
			<div class="payment-modal-body">
				<div class="payment-method-list">
					<label class="payment-method-item selected">
						<input type="radio" name="momoMethod" value="momo" checked>
						<img src="https://developers.momo.vn/v3/assets/images/icon-52bd5808cecdb1970e1aeec3c31a3ee1.png" alt="MoMo" height="28">
						<span>MoMo</span>
						<span class="promo-badge">Khuyến mãi</span>
					</label>
				</div>
				
				<div class="payment-promo-note">
					<span class="promo-code">3AM2024</span> giảm ngay <strong>50K</strong> cho đơn hàng từ <strong>2 triệu</strong>
				</div>
				
				<button class="btn-payment-continue" onclick="processMomoPayment()">Tiếp theo</button>
				
				<div class="payment-note">
					<i class="fa fa-exclamation-triangle"></i>
					<div>
						<strong>Lưu ý:</strong><br>
						Thiết bị cần có ứng dụng MoMo để thanh toán
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- COD Order Confirmation Modal -->
	<div class="payment-modal-overlay" id="codConfirmModal" onclick="closeCodConfirmModal()">
		<div class="payment-modal cod-confirm-modal" onclick="event.stopPropagation()">
			<div class="cod-modal-header">
				<h2>Xác nhận đơn hàng</h2>
			</div>
			
			<div class="cod-modal-body">
				<p class="cod-instruction">
					Đơn hàng của bạn đã được tạo. Tin nhắn yêu cầu xác nhận đơn hàng đã được gửi đến số điện thoại. 
					Vui lòng xác nhận bằng cách nhập vào đường dẫn trong tin nhắn. 
					<strong>Nếu bạn không xác nhận, đơn hàng sẽ không được thực hiện.</strong>
				</p>
				
				<div class="cod-order-info">
					<label>Số đơn hàng</label>
					<p id="codOrderNumber">VN251202-00000000_000000000000</p>
				</div>
				
				<div class="cod-otp-section">
					<label>Mã OTP <span class="otp-hint">(8 kí tự)</span></label>
					<input type="text" id="codOtpInput" maxlength="8" placeholder="Nhập mã OTP">
				</div>
				
				<button class="btn-cod-confirm" onclick="confirmCodOrder()">XÁC NHẬN</button>
			</div>
		</div>
	</div>

	<div class="footer">
		<?php addFooter(); ?>
	</div>

	<?php addContainTaiKhoan(); ?>

	<i class="fa fa-arrow-up" id="goto-top-page" onclick="gotoTop()"></i>
	<i class="fa fa-arrow-down" id="goto-bot-page" onclick="gotoBot()"></i>
</body>

</html>




