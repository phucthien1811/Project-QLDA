<!DOCTYPE html>
<html lang="vi">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">

	<title>Bảo hành & Sửa chữa - 3AM Store</title>
	<link rel="icon" type="image/x-icon" href="/Project-PHP/img/favicon.ico?v=2">
    <link rel="shortcut icon" type="image/x-icon" href="/Project-PHP/img/favicon.ico?v=2">

	<!-- Load font awesome icons -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" crossorigin="anonymous">

	<!-- Google Fonts - Inter -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
	<link rel="stylesheet" href="Styles/Warranty.css">
	<link rel="stylesheet" href="Styles/Footer.css">
	<!-- js -->
	<script src="Scripts/Common.js?v=<?php echo time(); ?>"></script>
	<script src="Scripts/SamsungHeader.js?v=<?php echo time(); ?>"></script>
	<script src="Scripts/Warranty.js?v=<?php echo time(); ?>"></script>

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

		<div class="warranty-container">
			<!-- Header -->
			<div class="warranty-header">
				<h1 class="warranty-title">Bảo hành & Sửa chữa</h1>
				<p class="warranty-subtitle">Tra cứu thông tin bảo hành và đặt lịch sửa chữa sản phẩm</p>
			</div>

			<!-- Tabs Navigation -->
			<div class="warranty-tabs-nav">
				<a href="#" class="warranty-tab-link active" data-tab="history">Lịch sử sửa chữa</a>
				<a href="#" class="warranty-tab-link" data-tab="products">Sản phẩm của tôi</a>
				<a href="#" class="warranty-tab-link" data-tab="booking">Đặt hẹn sửa chữa</a>
			</div>

			<!-- Tab: Lịch sử sửa chữa -->
			<div class="warranty-tab-content active" id="tab-history">
				<div class="warranty-section">
					<h2 class="section-title-warranty">Sửa chữa đã hoàn tất</h2>
					
					<!-- Date Range Filter -->
					<div class="date-filter-section">
						<p class="date-filter-label">Chọn phạm vi ngày</p>
						<div class="date-range-picker">
							<div class="date-input-group">
								<input type="date" id="dateFrom" class="date-input">
								<i class="fa fa-calendar"></i>
							</div>
							<span class="date-separator">~</span>
							<div class="date-input-group">
								<input type="date" id="dateTo" class="date-input">
								<i class="fa fa-calendar"></i>
							</div>
						</div>
						<p class="date-note">* Bạn có thể xem lịch sử sửa chữa lâu hơn 3 năm bằng cách chọn phạm vị ngày mong muốn.</p>
					</div>

					<!-- Repair History List -->
					<div class="repair-history-card">
						<div class="empty-state" id="repairHistoryEmpty">
							<div class="empty-icon">
								<i class="fa fa-smile-o"></i>
							</div>
							<h3>Không tìm thấy lịch sử sửa chữa.</h3>
							<p>Bạn có thể kiểm tra lịch sử sửa chữa của mình theo số phiếu sửa chữa<br>hoặc gửi yêu cầu sửa chữa mới dưới đây.</p>
						</div>
						<div class="repair-history-list" id="repairHistoryList" style="display: none;">
							<!-- Repair history items loaded here -->
						</div>
					</div>

					<!-- Track Repair Section -->
					<div class="track-repair-section">
						<h3>Theo dõi sửa chữa của tôi</h3>
						<p>Đánh dấu ở đây nếu quý vị cần trợ giúp tìm sửa chữa.</p>
						<div class="track-options">
							<button class="track-btn active" data-type="email">Email</button>
							<button class="track-btn" data-type="phone">Điện thoại</button>
						</div>
						<div class="track-input-group" id="trackInputGroup">
							<input type="email" id="trackInput" placeholder="Nhập email của bạn" class="track-input">
							<button class="track-search-btn" onclick="searchRepairByContact()">
								<i class="fa fa-search"></i>
							</button>
						</div>
					</div>
				</div>
			</div>

			<!-- Tab: Sản phẩm của tôi -->
			<div class="warranty-tab-content" id="tab-products">
				<div class="warranty-section">
					<h2 class="section-title-warranty">Sản phẩm đã mua tại 3AM Store</h2>
					<p class="section-subtitle-warranty">Danh sách các sản phẩm của bạn kèm thông tin bảo hành</p>

					<div class="my-products-list" id="myProductsList">
						<div class="loading-state">
							<i class="fa fa-spinner fa-spin"></i>
							<span>Đang tải danh sách sản phẩm...</span>
						</div>
					</div>

					<div class="empty-state" id="productsEmpty" style="display: none;">
						<div class="empty-icon">
							<i class="fa fa-mobile"></i>
						</div>
						<h3>Chưa có sản phẩm nào</h3>
						<p>Bạn chưa mua sản phẩm nào tại 3AM Store.<br>Hãy mua sắm để được hỗ trợ bảo hành!</p>
						<a href="index.php" class="btn-shop-now">Mua sắm ngay</a>
					</div>
				</div>
			</div>

			<!-- Tab: Đặt hẹn sửa chữa -->
			<div class="warranty-tab-content" id="tab-booking">
				<div class="warranty-section">
					<h2 class="section-title-warranty">Đặt hẹn sửa chữa</h2>
					<p class="section-subtitle-warranty">Bắt đầu bằng cách đặt yêu cầu sửa chữa cho các sản phẩm đã đăng ký của bạn trên trang Thiết bị của tôi.</p>

					<!-- Booking Form -->
					<div class="booking-form-card">
						<form id="repairBookingForm">
							<!-- Step 1: Select Product -->
							<div class="form-step active" id="step1">
								<h3 class="step-title"><span class="step-number">1</span> Chọn sản phẩm cần sửa chữa</h3>
								<div class="product-select-list" id="productSelectList">
									<!-- Products loaded here -->
								</div>
							</div>

							<!-- Step 2: Describe Issue -->
							<div class="form-step" id="step2">
								<h3 class="step-title"><span class="step-number">2</span> Mô tả vấn đề</h3>
								<div class="issue-categories">
									<label class="issue-option">
										<input type="radio" name="issueType" value="screen">
										<span class="issue-label">
											<i class="fa fa-mobile"></i>
											Màn hình
										</span>
									</label>
									<label class="issue-option">
										<input type="radio" name="issueType" value="battery">
										<span class="issue-label">
											<i class="fa fa-battery-full"></i>
											Pin
										</span>
									</label>
									<label class="issue-option">
										<input type="radio" name="issueType" value="camera">
										<span class="issue-label">
											<i class="fa fa-camera"></i>
											Camera
										</span>
									</label>
									<label class="issue-option">
										<input type="radio" name="issueType" value="speaker">
										<span class="issue-label">
											<i class="fa fa-volume-up"></i>
											Loa/Mic
										</span>
									</label>
									<label class="issue-option">
										<input type="radio" name="issueType" value="software">
										<span class="issue-label">
											<i class="fa fa-cog"></i>
											Phần mềm
										</span>
									</label>
									<label class="issue-option">
										<input type="radio" name="issueType" value="other">
										<span class="issue-label">
											<i class="fa fa-question-circle"></i>
											Khác
										</span>
									</label>
								</div>
								<div class="issue-description">
									<label>Mô tả chi tiết vấn đề:</label>
									<textarea id="issueDescription" rows="4" placeholder="Vui lòng mô tả chi tiết tình trạng máy của bạn..."></textarea>
								</div>
							</div>

							<!-- Step 3: Choose Date & Time -->
							<div class="form-step" id="step3">
								<h3 class="step-title"><span class="step-number">3</span> Chọn ngày & giờ hẹn</h3>
								<div class="datetime-picker">
									<div class="date-picker-group">
										<label>Ngày hẹn:</label>
										<input type="date" id="bookingDate" class="form-input" min="">
									</div>
									<div class="time-picker-group">
										<label>Giờ hẹn:</label>
										<select id="bookingTime" class="form-input">
											<option value="">-- Chọn giờ --</option>
											<option value="08:00">08:00 - 09:00</option>
											<option value="09:00">09:00 - 10:00</option>
											<option value="10:00">10:00 - 11:00</option>
											<option value="11:00">11:00 - 12:00</option>
											<option value="14:00">14:00 - 15:00</option>
											<option value="15:00">15:00 - 16:00</option>
											<option value="16:00">16:00 - 17:00</option>
											<option value="17:00">17:00 - 18:00</option>
										</select>
									</div>
								</div>
							</div>

							<!-- Step 4: Contact Info -->
							<div class="form-step" id="step4">
								<h3 class="step-title"><span class="step-number">4</span> Thông tin liên hệ</h3>
								<div class="contact-form">
									<div class="form-row">
										<div class="form-group">
											<label>Họ và tên:</label>
											<input type="text" id="contactName" class="form-input" placeholder="Nhập họ tên">
										</div>
										<div class="form-group">
											<label>Số điện thoại:</label>
											<input type="tel" id="contactPhone" class="form-input" placeholder="Nhập số điện thoại">
										</div>
									</div>
									<div class="form-group">
										<label>Email:</label>
										<input type="email" id="contactEmail" class="form-input" placeholder="Nhập email">
									</div>
									<div class="form-group">
										<label>Ghi chú thêm (tùy chọn):</label>
										<textarea id="contactNote" rows="3" class="form-input" placeholder="Ghi chú thêm nếu có..."></textarea>
									</div>
								</div>
							</div>

							<!-- Form Actions -->
							<div class="form-actions">
								<button type="button" class="btn-prev" id="btnPrev" onclick="prevStep()" style="display: none;">
									<i class="fa fa-arrow-left"></i> Quay lại
								</button>
								<button type="button" class="btn-next" id="btnNext" onclick="nextStep()">
									Tiếp tục <i class="fa fa-arrow-right"></i>
								</button>
								<button type="submit" class="btn-submit" id="btnSubmit" style="display: none;">
									<i class="fa fa-check"></i> Đặt lịch hẹn
								</button>
							</div>
						</form>
					</div>

					<!-- Quick Support -->
					<div class="quick-support-section">
						<div class="support-card">
							<div class="support-icon">
								<i class="fa fa-phone"></i>
							</div>
							<div class="support-info">
								<h4>Hỗ trợ qua điện thoại</h4>
								<p>Hotline: <strong>1900 1234</strong></p>
								<p class="support-hours">8:00 - 21:00 (Thứ 2 - CN)</p>
							</div>
						</div>
						<div class="support-card">
							<div class="support-icon">
								<i class="fa fa-comments"></i>
							</div>
							<div class="support-info">
								<h4>Chat trực tuyến</h4>
								<p>Nhận hỗ trợ ngay lập tức</p>
								<button class="btn-chat" onclick="openChat()">Bắt đầu chat</button>
							</div>
						</div>
						<div class="support-card">
							<div class="support-icon">
								<i class="fa fa-map-marker"></i>
							</div>
							<div class="support-info">
								<h4>Trung tâm bảo hành</h4>
								<p>123 Nguyễn Văn A, Q.1, TP.HCM</p>
								<a href="#" class="btn-directions">Xem bản đồ</a>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>

	</section>

	<?php addContainTaiKhoan(); ?>

	<div class="footer">
		<?php addFooter(); ?>
	</div>

	<i class="fa fa-arrow-up" id="goto-top-page" onclick="gotoTop()"></i>
</body>

</html>




