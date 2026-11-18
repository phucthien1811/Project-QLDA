<!DOCTYPE html>
<html lang="vi">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">

	<title>Tài khoản của tôi - Thế giới điện thoại</title>
	<link rel="icon" type="image/x-icon" href="/Project-PHP/img/favicon.ico?v=2">
    <link rel="shortcut icon" type="image/x-icon" href="/Project-PHP/img/favicon.ico?v=2">

	<!-- Load font awesome icons -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" crossorigin="anonymous">

	<!-- Jquery -->
	<script src="Lib/JQuery/Jquery.min.js"></script>

	<!-- Bootstrap -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>

	<!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

	<!-- our files -->
	<link rel="stylesheet" href="Styles/Base.css">
	<link rel="stylesheet" href="Styles/SamsungHeader.css">
	<link rel="stylesheet" href="Styles/Account.css?v=<?php echo time(); ?>">
	<link rel="stylesheet" href="Styles/Footer.css">

	<script src="Scripts/Common.js?v=<?php echo time(); ?>"></script>
	<script src="Scripts/SamsungHeader.js?v=<?php echo time(); ?>"></script>
	<script src="Scripts/Account.js?v=<?php echo time(); ?>"></script>

	<?php require_once "Api/HtmlRenderer.php"; ?>
</head>

<body>
	<?php addTopNav(); ?>

	<section class="account-page-wrapper" style="min-height: 85vh; background: #f8f9fa;">
		<?php addHeader(); ?>

		<style>
			.containTaikhoan {
				display: none !important;
				visibility: hidden !important;
			}
		</style>

		<div class="account-container">
			<div class="account-header">
				<h1 class="account-title">Tài khoản của tôi</h1>
				<p class="account-subtitle">Quản lý thông tin cá nhân và bảo mật tài khoản</p>
			</div>

			<div class="account-content">
				<!-- Sidebar -->
				<div class="account-sidebar">
					<div class="user-profile-card">
						<div class="user-avatar-large" id="userAvatar">
							<i class="fas fa-user"></i>
						</div>
						<div class="user-name" id="userFullName">Đang tải...</div>
						<div class="user-email" id="userEmail">...</div>
						<div class="user-member-since">
							<i class="fas fa-calendar-alt"></i>
							<span id="memberSince">Thành viên</span>
						</div>
					</div>

					<nav class="account-nav">
						<a href="#" class="nav-item active" data-tab="profile">
							<i class="fas fa-user-circle"></i>
							<span>Thông tin cá nhân</span>
						</a>
						<a href="#" class="nav-item" data-tab="orders">
							<i class="fas fa-shopping-bag"></i>
							<span>Đơn hàng của tôi</span>
							<span class="nav-badge" id="ordersBadge"></span>
						</a>						<a href="User.php#rewards" class="nav-item">
							<i class="fas fa-star"></i>
							<span>My Rewards</span>
						</a>						<a href="#" class="nav-item" data-tab="security">
							<i class="fas fa-shield-alt"></i>
							<span>Bảo mật & Mật khẩu</span>
						</a>
						<a href="#" class="nav-item" data-tab="addresses">
							<i class="fas fa-map-marker-alt"></i>
							<span>Sổ địa chỉ</span>
						</a>
						<a href="#" class="nav-item nav-logout" onclick="logoutAccount()">
							<i class="fas fa-sign-out-alt"></i>
							<span>Đăng xuất</span>
						</a>
					</nav>
				</div>

				<!-- Main Content -->
				<div class="account-main">
					<!-- Tab: Profile -->
					<div class="tab-content-account active" id="tab-profile">
						<div class="content-card">
							<div class="card-header">
								<h2><i class="fas fa-user-edit"></i> Thông tin cá nhân</h2>
								<p>Cập nhật thông tin của bạn để chúng tôi phục vụ bạn tốt hơn</p>
							</div>
							<div class="card-body">
								<form id="profileForm" class="profile-form">
									<div class="form-row-account">
										<div class="form-group-account">
											<label><i class="fas fa-user"></i> Họ</label>
										<input type="text" id="inputHo" placeholder="Nhập họ của bạn" spellcheck="false">
										</div>
										<div class="form-group-account">
											<label><i class="fas fa-user"></i> Tên</label>
										<input type="text" id="inputTen" placeholder="Nhập tên của bạn" spellcheck="false">
										</div>
									</div>

									<div class="form-row-account">
										<div class="form-group-account">
											<label><i class="fas fa-venus-mars"></i> Giới tính</label>
											<select id="inputGioiTinh">
												<option value="">Chọn giới tính</option>
												<option value="Nam">Nam</option>
												<option value="Nữ">Nữ</option>
												<option value="Khác">Khác</option>
											</select>
										</div>
										<div class="form-group-account">
											<label><i class="fas fa-birthday-cake"></i> Ngày sinh</label>
											<input type="date" id="inputNgaySinh">
										</div>
									</div>

									<div class="form-row-account">
										<div class="form-group-account">
											<label><i class="fas fa-envelope"></i> Email</label>
										<input type="email" id="inputEmail" placeholder="example@email.com" spellcheck="false">
										</div>
										<div class="form-group-account">
											<label><i class="fas fa-phone"></i> Số điện thoại</label>
										<input type="tel" id="inputSDT" placeholder="0xxx xxx xxx" spellcheck="false">
										</div>
									</div>

									<div class="form-group-account full-width">
										<label><i class="fas fa-map-marker-alt"></i> Địa chỉ</label>
										<textarea id="inputDiaChi" rows="2" placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố" spellcheck="false"></textarea>
									</div>

									<div class="form-actions-account">
										<button type="button" class="btn-secondary-account" onclick="resetProfileForm()">
											<i class="fas fa-undo"></i> Hoàn tác
										</button>
										<button type="submit" class="btn-primary-account">
											<i class="fas fa-save"></i> Lưu thay đổi
										</button>
									</div>
								</form>
							</div>
						</div>

						<!-- Quick Stats -->
						<div class="quick-stats">
							<div class="stat-box">
								<div class="stat-icon orders">
									<i class="fas fa-shopping-bag"></i>
								</div>
								<div class="stat-info">
									<span class="stat-number" id="statOrders">0</span>
									<span class="stat-label">Đơn hàng</span>
								</div>
							</div>
							<div class="stat-box">
								<div class="stat-icon spent">
									<i class="fas fa-wallet"></i>
								</div>
								<div class="stat-info">
									<span class="stat-number" id="statSpent">0đ</span>
									<span class="stat-label">Đã chi tiêu</span>
								</div>
							</div>
							<div class="stat-box">
								<div class="stat-icon wishlist">
									<i class="fas fa-heart"></i>
								</div>
								<div class="stat-info">
									<span class="stat-number" id="statWishlist">0</span>
									<span class="stat-label">Yêu thích</span>
								</div>
							</div>
						</div>
					</div>

					<!-- Tab: Security -->
					<div class="tab-content-account" id="tab-security">
						<div class="content-card">
							<div class="card-header">
								<h2><i class="fas fa-lock"></i> Đổi mật khẩu</h2>
								<p>Đảm bảo tài khoản của bạn được bảo mật với mật khẩu mạnh</p>
							</div>
							<div class="card-body">
								<form id="passwordForm" class="password-form">
									<div class="form-group-account">
										<label><i class="fas fa-key"></i> Mật khẩu hiện tại</label>
										<div class="password-input-wrapper">
											<input type="password" id="currentPassword" placeholder="Nhập mật khẩu hiện tại">
											<button type="button" class="toggle-password" onclick="togglePassword('currentPassword')">
												<i class="fas fa-eye"></i>
											</button>
										</div>
									</div>

									<div class="form-group-account">
										<label><i class="fas fa-lock"></i> Mật khẩu mới</label>
										<div class="password-input-wrapper">
											<input type="password" id="newPassword" placeholder="Nhập mật khẩu mới">
											<button type="button" class="toggle-password" onclick="togglePassword('newPassword')">
												<i class="fas fa-eye"></i>
											</button>
										</div>
										<div class="password-strength" id="passwordStrength">
											<div class="strength-bar"></div>
											<span class="strength-text"></span>
										</div>
									</div>

									<div class="form-group-account">
										<label><i class="fas fa-check-circle"></i> Xác nhận mật khẩu mới</label>
										<div class="password-input-wrapper">
											<input type="password" id="confirmPassword" placeholder="Nhập lại mật khẩu mới">
											<button type="button" class="toggle-password" onclick="togglePassword('confirmPassword')">
												<i class="fas fa-eye"></i>
											</button>
										</div>
									</div>

									<div class="password-requirements">
										<h4><i class="fas fa-info-circle"></i> Yêu cầu mật khẩu:</h4>
										<ul>
											<li id="req-length"><i class="fas fa-circle"></i> Ít nhất 6 ký tự</li>
											<li id="req-upper"><i class="fas fa-circle"></i> Có chữ hoa</li>
											<li id="req-lower"><i class="fas fa-circle"></i> Có chữ thường</li>
											<li id="req-number"><i class="fas fa-circle"></i> Có số</li>
										</ul>
									</div>

									<div class="form-actions-account">
										<button type="submit" class="btn-primary-account btn-full">
											<i class="fas fa-sync-alt"></i> Cập nhật mật khẩu
										</button>
									</div>
								</form>
							</div>
						</div>

						<!-- Account Security Info -->
						<div class="content-card security-info">
							<div class="card-header">
								<h2><i class="fas fa-shield-alt"></i> Bảo mật tài khoản</h2>
							</div>
							<div class="card-body">
								<div class="security-item">
									<div class="security-icon">
										<i class="fas fa-user-check"></i>
									</div>
									<div class="security-details">
										<h4>Tài khoản</h4>
										<p id="securityUsername">-</p>
									</div>
									<div class="security-status verified">
										<i class="fas fa-check-circle"></i> Đã xác thực
									</div>
								</div>
								<div class="security-item">
									<div class="security-icon google">
										<i class="fab fa-google"></i>
									</div>
									<div class="security-details">
										<h4>Đăng nhập Google</h4>
										<p id="googleStatus">Chưa liên kết</p>
									</div>
									<div class="security-status" id="googleBadge">
										<i class="fas fa-times-circle"></i> Chưa liên kết
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Tab: Orders -->
					<div class="tab-content-account" id="tab-orders">
						<div class="content-card">
							<div class="card-header">
								<h2><i class="fas fa-shopping-bag"></i> Đơn hàng của tôi</h2>
								<p>Theo dõi và quản lý các đơn hàng của bạn</p>
							</div>
							<div class="card-body">
								<!-- Order Filters -->
								<div class="order-filters">
									<button class="filter-btn active" data-filter="all">Tất cả</button>
									<button class="filter-btn" data-filter="pending">Chờ xác nhận</button>
									<button class="filter-btn" data-filter="processing">Đang xử lý</button>
									<button class="filter-btn" data-filter="shipping">Đang giao</button>
									<button class="filter-btn" data-filter="completed">Hoàn thành</button>
									<button class="filter-btn" data-filter="cancelled">Đã hủy</button>
								</div>

								<!-- Orders List -->
								<div class="orders-list" id="ordersList">
									<div class="loading-orders">
										<i class="fas fa-spinner fa-spin"></i>
										<p>Đang tải đơn hàng...</p>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Tab: Addresses -->
					<div class="tab-content-account" id="tab-addresses">
						<div class="content-card">
							<div class="card-header">
								<h2><i class="fas fa-map-marked-alt"></i> Sổ địa chỉ</h2>
								<p>Quản lý địa chỉ giao hàng của bạn</p>
								<button class="btn-add-address" onclick="showAddAddressModal()">
									<i class="fas fa-plus"></i> Thêm địa chỉ mới
								</button>
							</div>
							<div class="card-body">
								<div class="addresses-list" id="addressesList">
									<!-- Address items loaded here -->
									<div class="empty-addresses">
										<i class="fas fa-map-marker-alt"></i>
										<h3>Chưa có địa chỉ nào</h3>
										<p>Thêm địa chỉ để đặt hàng nhanh hơn</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</section>

	<!-- Modal Add Address -->
	<div class="modal-overlay" id="addressModal">
		<div class="modal-content">
			<div class="modal-header-account">
				<h3><i class="fas fa-map-marker-alt"></i> Thêm địa chỉ mới</h3>
				<button class="modal-close" onclick="closeAddressModal()">&times;</button>
			</div>
			<div class="modal-body-account">
				<form id="addressForm">
					<div class="form-group-account">
						<label>Tên người nhận</label>
						<input type="text" id="addrName" placeholder="Họ và tên người nhận">
					</div>
					<div class="form-group-account">
						<label>Số điện thoại</label>
						<input type="tel" id="addrPhone" placeholder="Số điện thoại liên hệ">
					</div>
					<div class="form-group-account">
						<label>Địa chỉ chi tiết</label>
						<textarea id="addrAddress" rows="3" placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố"></textarea>
					</div>
					<div class="form-check">
						<input type="checkbox" id="addrDefault">
						<label for="addrDefault">Đặt làm địa chỉ mặc định</label>
					</div>
					<div class="form-actions-account">
						<button type="button" class="btn-secondary-account" onclick="closeAddressModal()">Hủy</button>
						<button type="submit" class="btn-primary-account">Lưu địa chỉ</button>
					</div>
				</form>
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




