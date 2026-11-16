<!DOCTYPE html>
<html lang="vi">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">

	<title>Đăng nhập - 3AM Store</title>
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
	<script src="Scripts/SignIn.js?v=<?php echo time(); ?>"></script>
</head>

<body>
	<div class="auth-container">
		<div class="auth-box">
			<div class="auth-header">
				<a href="Index.php" class="back-btn">
					<i class="fa fa-arrow-left"></i>
				</a>
				<h1>Một tài khoản. Mọi thiết bị.</h1>
				<p class="subtitle">Chỉ dành cho bạn.</p>
				<p class="login-prompt">Đăng nhập để bắt đầu</p>
			</div>

			<form id="loginForm" class="auth-form">
				<div class="form-group">
					<label for="username">Tên tài khoản</label>
					<input type="text" id="username" name="username" placeholder="Nhập tên tài khoản" required>
				</div>

				<div class="form-group">
					<label for="password">Mật khẩu</label>
					<input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
				</div>

				<div class="remember-me">
					<input type="checkbox" id="rememberMe" name="rememberMe">
					<label for="rememberMe">Nhớ tài khoản của tôi</label>
				</div>

				<button type="submit" class="btn-submit">Đăng nhập</button>

				<div class="form-links">
					<a href="#" class="link">Quên mật khẩu?</a>
					<a href="SignUp.php" class="link">Tạo tài khoản</a>
				</div>

				<div class="divider">
					<span>HOẶC</span>
				</div>

				<button type="button" class="btn-google" onclick="loginWithGoogle()">
					<i class="fa fa-google"></i>
					Đăng nhập bằng Google
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




