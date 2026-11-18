<?php
/**
 * Admin Authentication Check
 * Include this file at the top of all admin pages to restrict access
 * Only users with MaQuyen >= 2 (Admin/Staff) can access
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['currentUser'])) {
    // Not logged in - redirect to login page
    header('Location: ../SignIn.php?redirect=admin&error=not_logged_in');
    exit();
}

// Check if user has admin role (MaQuyen >= 2)
$currentUser = $_SESSION['currentUser'];
$userRole = isset($currentUser['MaQuyen']) ? intval($currentUser['MaQuyen']) : 1;

if ($userRole < 2) {
    // User doesn't have admin privileges - show access denied page
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Không có quyền truy cập - 3AM Store</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }
            .access-denied-container {
                background: white;
                border-radius: 20px;
                padding: 50px;
                text-align: center;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                max-width: 500px;
                width: 90%;
            }
            .access-denied-icon {
                width: 120px;
                height: 120px;
                background: linear-gradient(135deg, #ff6b6b 0%, #ee5a5a 100%);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 30px;
                animation: pulse 2s infinite;
            }
            .access-denied-icon i {
                font-size: 50px;
                color: white;
            }
            @keyframes pulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.05); }
            }
            .access-denied-title {
                font-size: 28px;
                font-weight: 700;
                color: #333;
                margin-bottom: 15px;
            }
            .access-denied-message {
                color: #666;
                font-size: 16px;
                line-height: 1.6;
                margin-bottom: 30px;
            }
            .btn-home {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border: none;
                padding: 12px 30px;
                border-radius: 30px;
                font-size: 16px;
                font-weight: 600;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                transition: all 0.3s ease;
            }
            .btn-home:hover {
                color: white;
                transform: translateY(-3px);
                box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            }
            .user-info {
                background: #f8f9fa;
                border-radius: 10px;
                padding: 15px;
                margin-bottom: 25px;
                font-size: 14px;
                color: #666;
            }
            .user-info strong {
                color: #333;
            }
        </style>
    </head>
    <body>
        <div class="access-denied-container">
            <div class="access-denied-icon">
                <i class="fas fa-lock"></i>
            </div>
            <h1 class="access-denied-title">Không có quyền truy cập</h1>
            <p class="access-denied-message">
                Xin lỗi, bạn không có quyền truy cập vào trang quản trị.<br>
                Trang này chỉ dành cho quản trị viên và nhân viên.
            </p>
            <div class="user-info">
                <i class="fas fa-user me-2"></i>
                Đăng nhập với tài khoản: <strong><?php echo htmlspecialchars($currentUser['TaiKhoan'] ?? 'Unknown'); ?></strong>
            </div>
            <a href="../Index.php" class="btn-home">
                <i class="fas fa-home"></i>
                Về trang chủ
            </a>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// User is authenticated and has admin privileges - continue loading the page
?>


