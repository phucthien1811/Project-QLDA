<!DOCTYPE html>
<html lang="vi">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">

	<title>Trang của tôi - Thế giới điện thoại</title>
	<link rel="icon" type="image/x-icon" href="/Project-PHP/img/favicon.ico?v=2">
    <link rel="shortcut icon" type="image/x-icon" href="/Project-PHP/img/favicon.ico?v=2">

	<!-- Load font awesome icons -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" crossorigin="anonymous">

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
	<link rel="stylesheet" href="Styles/User.css">
	<link rel="stylesheet" href="Styles/Footer.css">
	<!-- js -->
	<script src="Scripts/Common.js?v=<?php echo time(); ?>"></script>
	<script src="Scripts/SamsungHeader.js?v=<?php echo time(); ?>"></script>
	<script src="Scripts/User.js?v=<?php echo time(); ?>"></script>

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

		<div class="mypage-container">
			<!-- Header giống Samsung -->
			<div class="mypage-header">
				<h1 class="mypage-title">Trang của tôi</h1>
			</div>

			<!-- Tabs Navigation giống Samsung -->
			<div class="mypage-tabs-nav">
				<a href="#" class="tab-link" data-tab="orders">Đơn hàng của tôi</a>
				<a href="#" class="tab-link active" data-tab="rewards">My Rewards</a>
			</div>

			<!-- Tab: My Rewards (Default) -->
			<div class="tab-content active" id="tab-rewards">
				<div class="rewards-wrapper">
					<!-- Points & Rank Cards -->
					<div class="rewards-top-cards">
						<div class="rewards-card points-card">
							<h3 class="card-title">Điểm thưởng của tôi</h3>
							<div class="points-display">
								<span class="points-value" id="myPoints">0</span>
								<span class="points-unit">P</span>
							</div>
							<p class="points-equivalent">(Tương đương <span id="pointsVnd">0</span> VND)</p>
							<p class="points-expiry">Không có điểm sẽ hết hạn trong tháng này.</p>
							<div class="points-info">
								<span>Chi tiêu cho lần mua hàng tiếp theo của bạn để tiết kiệm thêm.</span>
								<i class="fa fa-info-circle"></i>
							</div>
						</div>

						<div class="rewards-card rank-card">
							<h3 class="card-title">Hạng của tôi</h3>
							<div class="rank-display">
								<span class="rank-name" id="myRank">Blue</span>
							</div>
							<div class="rank-progress-container">
								<div class="rank-progress-bar">
									<div class="rank-progress" id="rankProgress" style="width: 0%"></div>
									<div class="rank-marker start">
										<span class="marker-dot blue"></span>
										<span class="marker-label">0 VND</span>
									</div>
									<div class="rank-marker end">
										<span class="marker-dot gold"></span>
										<span class="marker-label">45.000.000 VND</span>
									</div>
								</div>
							</div>
							<p class="rank-next">
								<span id="amountToGold">45.000.000</span> VND cho đến <span class="gold-text">Gold</span>
							</p>
							<p class="rank-benefits">Tận hưởng quyền lợi hội viên <span id="rankBenefitName">Blue</span> của bạn! <i class="fa fa-info-circle"></i></p>
							<p class="rank-since">Kể từ: <span id="memberSince">--</span></p>
						</div>
					</div>

					<!-- Tiers Info -->
					<div class="rewards-tiers-section">
						<h2 class="section-title">Điểm qua về Hạng Thành viên và Điểm thưởng</h2>
						<div class="tiers-grid">
							<div class="tier-card blue active">
								<div class="tier-header">
									<span class="tier-name">Blue</span>
									<span class="tier-percent">1%</span>
								</div>
								<div class="tier-info">
									<p>Tổng số tiền mua</p>
									<p class="tier-threshold">Hạng Bắt đầu</p>
								</div>
							</div>
							<div class="tier-card gold">
								<div class="tier-header">
									<span class="tier-name">Gold</span>
									<span class="tier-percent">3%</span>
								</div>
								<div class="tier-info">
									<p>Tổng số tiền mua</p>
									<p class="tier-threshold">45.000.000 VND</p>
								</div>
							</div>
							<div class="tier-card platinum">
								<div class="tier-header">
									<span class="tier-name">Platinum</span>
									<span class="tier-percent">5%</span>
								</div>
								<div class="tier-info">
									<p>Tổng số tiền mua</p>
									<p class="tier-threshold">110.000.000 VND</p>
								</div>
							</div>
						</div>
						<p class="tiers-note">* Tỷ lệ phần trăm điểm thưởng nêu trên (%) Chỉ áp dụng cho việc mua hàng tại 3AM Store.</p>
					</div>

					<!-- Benefits Section -->
					<div class="rewards-benefits-section">
						<h2 class="section-title">Tận hưởng ưu đãi dành cho bạn</h2>
						<p class="section-subtitle">Xem chi tiết ưu đãi thành viên của bạn ngay bên dưới</p>
						
						<!-- Tier filter tabs -->
						<div class="benefits-tier-tabs">
							<button class="tier-tab active" data-tier="blue">Blue</button>
							<button class="tier-tab" data-tier="gold">Gold</button>
							<button class="tier-tab" data-tier="platinum">Platinum</button>
						</div>
						
						<div class="benefits-grid">
							<div class="benefit-card">
								<div class="benefit-icon">
									<i class="fa fa-file-text-o"></i>
								</div>
								<h4>Điểm Rewards</h4>
								<p>Tích lũy <span class="benefit-percent">1%</span> giá trị đơn hàng hợp lệ khi mua sắm trên 3AM Store</p>
							</div>
							<div class="benefit-card">
								<div class="benefit-icon">
									<i class="fa fa-users"></i>
								</div>
								<h4>Ưu đãi Điểm Rewards</h4>
								<p>Nhận thêm Điểm Rewards ưu đãi trong thời gian khuyến mãi</p>
							</div>
							<div class="benefit-card">
								<div class="benefit-icon">
									<i class="fa fa-hand-pointer-o"></i>
								</div>
								<h4>Voucher khuyến khích thăng hạng</h4>
								<p>Voucher 500K cho hóa đơn từ 1 triệu. Chỉ áp dụng vào những thời điểm được thông báo trước</p>
							</div>
						</div>
						
						<p class="benefits-note">* Các quyền lợi điểm thưởng được đề cập ở trên chỉ áp dụng trên 3AM Store. Không áp dụng đối với chương trình bán hàng cho nhân viên đối tác, B2B, SMB.</p>
					</div>

					<!-- How to earn -->
					<div class="rewards-howto-section">
						<h2 class="section-title">Làm thế nào để có điểm 3AM Rewards?</h2>
						<div class="howto-illustration">
							<div class="howto-icon">
								<i class="fa fa-mobile"></i>
								<div class="howto-badge">
									<i class="fa fa-percent"></i>
								</div>
							</div>
						</div>
						<p class="howto-text">Đơn giản chỉ cần đăng nhập tài khoản 3AM trước khi thanh toán mỗi khi mua sắm tại 3AM Store.</p>
						<p class="howto-text">Sau đó, bạn sẽ nhận điểm Rewards cho tất cả đơn hàng được xác nhận thành công và hợp lệ.</p>
					</div>
				</div>
			</div>

			<!-- Tab: Đơn hàng của tôi -->
			<div class="tab-content" id="tab-orders">
				<div class="orders-content">
				<!-- Sidebar - Thông tin người dùng -->
				<div class="orders-sidebar">
					<div class="user-info-card">
						<div class="user-avatar">
							<i class="fa fa-user"></i>
						</div>
						<div class="user-details" id="userDetails">
							<!-- User info loaded here -->
						</div>
					</div>

					<div class="orders-stats">
						<div class="stat-item">
							<span class="stat-number" id="totalOrders">0</span>
							<span class="stat-label">Đơn hàng</span>
						</div>
						<div class="stat-item">
							<span class="stat-number" id="completedOrders">0</span>
							<span class="stat-label">Hoàn thành</span>
						</div>
						<div class="stat-item">
							<span class="stat-number" id="pendingOrders">0</span>
							<span class="stat-label">Đang xử lý</span>
						</div>
					</div>
				</div>

				<!-- Main content - Danh sách đơn hàng -->
				<div class="orders-main">
					<div class="orders-list" id="ordersList">
						<!-- Orders loaded here -->
						<div class="loading-orders">
							<i class="fa fa-spinner fa-spin"></i>
							<span>Đang tải đơn hàng...</span>
						</div>
					</div>
				</div>
				</div>
			</div>

		</div>

	</section>

	<!-- Modal Chi tiết đơn hàng -->
	<div class="order-detail-modal" id="orderDetailModal">
		<div class="order-detail-content">
			<div class="order-detail-header">
				<h2>Chi tiết đơn hàng <span id="modalOrderId"></span></h2>
				<button class="modal-close-btn" onclick="closeOrderDetail()">&times;</button>
			</div>
			<div class="order-detail-body" id="orderDetailBody">
				<!-- Order detail loaded here -->
			</div>
		</div>
	</div>

	<?php addContainTaiKhoan(); ?>

	<div class="footer">
		<?php addFooter(); ?>
	</div>

	<i class="fa fa-arrow-up" id="goto-top-page" onclick="gotoTop()"></i>
</body>

</html>



