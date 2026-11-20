<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thế giới điện thoại</title>
    <link rel="icon" type="image/x-icon" href="/Project-PHP/img/favicon.ico?v=2">
    <link rel="shortcut icon" type="image/x-icon" href="/Project-PHP/img/favicon.ico?v=2">

    <!-- Load font awesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" crossorigin="anonymous">

    <!-- Jquery -->
    <script src="Lib/JQuery/Jquery.min.js"></script>

    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>

    <!-- our files -->
    <!-- css -->
    <link rel="stylesheet" href="Styles/Base.css">
    <link rel="stylesheet" href="Styles/SamsungHeader.css">
    <link rel="stylesheet" href="Styles/Account.css">
    <link rel="stylesheet" href="Styles/ProductDetail.css">
    <link rel="stylesheet" href="Styles/Footer.css">
    <!-- js -->
    <script src="Scripts/Common.js?v=<?php echo time(); ?>"></script>
    <script src="Scripts/SamsungHeader.js?v=<?php echo time(); ?>"></script>
    <script src="Scripts/HeaderScroll.js?v=<?php echo time(); ?>"></script>
    <script src="Scripts/ProductDetail.js?v=<?php echo time(); ?>"></script>

    <?php 
        require_once "Api/HtmlRenderer.php";
    ?>
</head>

<body>

    <?php addTopNav(); ?>

    <section>
        <?php 
            addHeader(); 
            addChiTietSanPham();
        ?>
    </section>

    <?php addContainTaiKhoan(); ?>

    <div class="footer">
        <?php addFooter(); ?>
    </div>

    <i class="fa fa-arrow-up" id="goto-top-page" onclick="gotoTop()"></i>
    <i class="fa fa-arrow-down" id="goto-bot-page" onclick="gotoBot()"></i>

</body>

</html>



