<!DOCTYPE html>
<html lang="vi">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="icon" type="image/x-icon" href="/Project-PHP/img/favicon.ico?v=2">
    <link rel="shortcut icon" type="image/x-icon" href="/Project-PHP/img/favicon.ico?v=2">

	<title>Thế giới điện thoại</title>

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
	<link rel="stylesheet" href="Styles/Account.css">
	<link rel="stylesheet" href="Styles/SamsungCart.css">
	<link rel="stylesheet" href="Styles/SwalCustom.css">
	<link rel="stylesheet" href="Styles/Footer.css">
	<!-- js -->
	<script src="Scripts/Common.js?v=<?php echo time(); ?>"></script>
	<script src="Scripts/SamsungHeader.js?v=<?php echo time(); ?>"></script>
	<script src="Scripts/Cart.js?v=<?php echo time(); ?>"></script>

	<?php require_once "Api/HtmlRenderer.php"; ?>

</head>

<body>
	<?php addTopNav(); ?>

	<section style="min-height: 85vh; background: #f8f9fa;">
		<?php addHeader(); ?>

		<div class="listSanPham"></div>

		<div class="modal fade" id="exampleModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				    <div class="modal-header">
				        <h5 class="modal-title" id="exampleModalLabel" >Nhập thông tin thanh toán</h5>
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close" >
				          <span aria-hidden="true">&times;</span>
				        </button>
				    </div>
				    <form id="formThanhToan" action="" method="post">
						<div class="modal-body" id="thongtinthanhtoan"></div>
						<div class="modal-footer">
				        	<button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
				        	<button type="button" class="btn btn-primary" id="btnXacNhan">Xác nhận</button>
				      	</div>
				    </form>
		    	</div>
		  	</div>
		</div>

	</section> <!-- End Section -->

	<?php addContainTaiKhoan(); ?>

	<div class="footer">
		<?php addFooter(); ?>
	</div>

	<i class="fa fa-arrow-up" id="goto-top-page" onclick="gotoTop()"></i>
	<i class="fa fa-arrow-down" id="goto-bot-page" onclick="gotoBot()"></i>
</body>

</html>



