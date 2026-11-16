<!DOCTYPE html>
<html lang="vi">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">

	<title>Tạo tài khoản - 3AM Store</title>
	<link rel="icon" type="image/x-icon" href="/Project-PHP/img/favicon.ico?v=2">
    <link rel="shortcut icon" type="image/x-icon" href="/Project-PHP/img/favicon.ico?v=2">

	<!-- Load font awesome icons -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" crossorigin="anonymous">

	<!-- Samsung Inter Font -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

	<!-- Jquery -->
	<script src="Lib/JQuery/Jquery.min.js"></script>

	<!-- Sweet Alert -->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>

	<!-- CSS -->
	<link rel="stylesheet" href="Styles/SignIn.css">

	<!-- JS -->
	<script src="Scripts/Common.js?v=<?php echo time(); ?>"></script>
	<script src="Scripts/SignUp.js?v=<?php echo time(); ?>"></script>
</head>

<body>
	<div class="auth-container">
		<div class="auth-box register-box">
			<div class="auth-header">
				<a href="Index.php" class="back-btn">
					<i class="fa fa-arrow-left"></i>
				</a>
				<h1>Tạo tài khoản 3AM Store</h1>
				<p class="login-prompt">Vui lòng điền thông tin để tạo tài khoản</p>
			</div>

			<form id="registerForm" class="auth-form">
				<!-- Privacy Policy Checkbox -->
				<div class="privacy-checkbox">
					<input type="checkbox" id="termsAndConditions" name="termsAndConditions" required>
					<label for="termsAndConditions">
						Bật Dịch vụ tùy chỉnh (tùy chọn)
						<div class="privacy-desc">
								Nhận nội dung và đề xuất được tùy chỉnh dựa trên sở thích và cách bạn sử dụng dịch vụ của chúng tôi. 
							Bạn có thể thay đổi cách sử dụng dữ liệu của mình thông qua <a href="#" class="link-inline">Quản lý dữ liệu</a>.
						</div>
					</label>
				</div>

				<div class="privacy-checkbox">
					<input type="checkbox" id="marketingConsent" name="marketingConsent">
					<label for="marketingConsent">
						Nhận tin tức và ưu đãi đặc biệt (tùy chọn)
						<div class="privacy-desc">
								Nhận quảng cáo có liên quan hơn dựa trên dữ liệu tài khoản 3AM của bạn, bao gồm cả dữ liệu bạn đã cấp phép cho Dịch vụ tùy chỉnh.
							</div>
						</label>
					</div>

					<div class="privacy-checkbox">
						<input type="checkbox" id="personalizedAds" name="personalizedAds">
						<label for="personalizedAds">
							Cài đặt quảng cáo được cá nhân hóa (tùy chọn)
							<div class="privacy-desc">
								Nhận quảng cáo có liên quan hơn dựa trên dữ liệu tài khoản 3AM của bạn, bao gồm cả dữ liệu bạn đã cấp phép cho Dịch vụ tùy chỉnh.
						</div>
					</label>
				</div>

				<div class="form-row">
					<div class="form-group half">
						<label for="firstName">Họ <span class="required">*</span></label>
						<input type="text" id="firstName" name="firstName" placeholder="Nhập họ" required>
					</div>
					<div class="form-group half">
						<label for="lastName">Tên <span class="required">*</span></label>
						<input type="text" id="lastName" name="lastName" placeholder="Nhập tên" required>
					</div>
				</div>

				<div class="form-group">
					<label for="email">Email <span class="required">*</span></label>
					<input type="email" id="email" name="email" placeholder="Nhập địa chỉ email" required>
				</div>

				<div class="form-group">
					<label for="phone">Số điện thoại <span class="required">*</span></label>
					<input type="tel" id="phone" name="phone" placeholder="Nhập số điện thoại" required>
				</div>

				<div class="form-group">
					<label for="password">Mật khẩu <span class="required">*</span></label>
					<input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
					<small class="hint">Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường và số</small>
				</div>

				<div class="form-group">
					<label for="confirmPassword">Xác nhận mật khẩu <span class="required">*</span></label>
					<input type="password" id="confirmPassword" name="confirmPassword" placeholder="Nhập lại mật khẩu" required>
				</div>

				<div class="form-group">
					<label for="dob">Ngày sinh <span class="required">*</span></label>
					<input type="date" id="dob" name="dob" required>
				</div>

				<div class="form-group">
					<label for="address">Địa chỉ</label>
					<input type="text" id="address" name="address" placeholder="Nhập địa chỉ">
				</div>

				<button type="submit" class="btn-submit">Tạo tài khoản</button>

				<div class="form-links center">
					<p>Đã có tài khoản? <a href="SignIn.php" class="link">Đăng nhập ngay</a></p>
				</div>

				<div class="divider">
					<span>HOẶC</span>
				</div>

				<button type="button" class="btn-google" onclick="registerWithGoogle()">
					<i class="fa fa-google"></i>
					Đăng ký bằng Google
				</button>
			</form>
		</div>

		<footer class="auth-footer">
			<p>Chính sách bảo mật | Điều khoản và Điều kiện</p>
			<p>© 2025 3AM Store. All Rights Reserved.</p>
		</footer>
	</div>
</body>

</html>




