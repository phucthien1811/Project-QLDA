<aside class="adm-sidebar">
    <!-- LOGO -->
    <div class="adm-sidebar-header">
        <a href="Index.php" class="adm-logo">
            <i class="fa fa-mobile-alt"></i>
            3AM Store
        </a>
    </div>
    
    <!-- NAVIGATION -->
    <nav class="adm-nav">
        
        <!-- MAIN SECTION -->
        <div class="adm-nav-section">
            <div class="adm-nav-title">Main</div>
            <a href="Index.php" class="adm-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'Index.php' ? 'active' : ''; ?>">
                <i class="fa fa-tachometer-alt"></i>
                Dashboard
            </a>
        </div>
        
        <!-- MANAGEMENT SECTION -->
        <div class="adm-nav-section">
            <div class="adm-nav-title">Quản lý</div>
            
            <a href="Orders.php" class="adm-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'Orders.php' ? 'active' : ''; ?>">
                <i class="fa fa-clipboard-list"></i>
                Đơn hàng
            </a>
            
            <a href="Customers.php" class="adm-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'Customers.php' ? 'active' : ''; ?>">
                <i class="fa fa-users"></i>
                Tài khoản
            </a>
            
            <a href="Staff.php" class="adm-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'Staff.php' ? 'active' : ''; ?>">
                <i class="fa fa-user-tie"></i>
                Nhân viên
            </a>
            
            <?php 
            $currentPage = basename($_SERVER['PHP_SELF']);
            $isProductPage = ($currentPage == 'ProductList.php' || $currentPage == 'AddProductSeries.php');
            ?>
            
            <!-- PRODUCT MANAGEMENT DROPDOWN -->
            <a href="javascript:void(0)" class="adm-nav-item adm-nav-dropdown <?php echo $isProductPage ? 'open' : ''; ?>" onclick="toggleSubmenu(this)">
                <i class="fa fa-box-open"></i>
                Quản lí sản phẩm
                <i class="fa fa-chevron-down adm-dropdown-icon"></i>
            </a>
            <div class="adm-nav-submenu <?php echo $isProductPage ? 'show' : ''; ?>">
                <a href="ProductList.php" class="adm-nav-item <?php echo $currentPage == 'ProductList.php' ? 'active' : ''; ?>">
                    <i class="fa fa-list"></i>
                    Danh sách sản phẩm
                </a>
                <a href="AddProductSeries.php" class="adm-nav-item <?php echo $currentPage == 'AddProductSeries.php' ? 'active' : ''; ?>">
                    <i class="fa fa-plus-circle"></i>
                    Thêm sản phẩm
                </a>
            </div>
            
            <!-- SỬA CHỮA & BẢO HÀNH -->
            <a href="Repairs.php" class="adm-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'Repairs.php' ? 'active' : ''; ?>">
                <i class="fa fa-tools"></i>
                Sửa chữa & Bảo hành
            </a>
            
            <!-- 3AM CARE+ -->
            <a href="CarePlan.php" class="adm-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'CarePlan.php' ? 'active' : ''; ?>">
                <i class="fa fa-shield-alt"></i>
                3AM Care+
            </a>
            
            <!-- VOUCHERS -->
            <a href="Vouchers.php" class="adm-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'Vouchers.php' ? 'active' : ''; ?>">
                <i class="fa fa-ticket-alt"></i>
                Mã giảm giá
            </a>
        </div>
        
        <!-- SYSTEM SECTION -->
        <div class="adm-nav-section">
            <div class="adm-nav-title">Hệ Thống</div>
            <a href="ChatbotMemory.php" class="adm-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'ChatbotMemory.php' ? 'active' : ''; ?>">
                <i class="fa fa-comments"></i>
                Chatbot Memory
            </a>
            <a href="#" class="adm-nav-item">
                <i class="fa fa-cog"></i>
                Cài đặt
            </a>
        </div>
        
    </nav>
</aside>

<script>
function toggleSubmenu(element) {
    element.classList.toggle('open');
    var submenu = element.nextElementSibling;
    if (submenu && submenu.classList.contains('adm-nav-submenu')) {
        submenu.classList.toggle('show');
    }
}
</script>

