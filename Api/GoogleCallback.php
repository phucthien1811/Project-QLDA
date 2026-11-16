<?php
session_start();
require_once 'GoogleConfig.php';
require_once __DIR__ . '/../Backend/Database/DbDriver.php';

// Khởi tạo Google Client
$client = getGoogleClient();

// Kiểm tra code từ Google
if (isset($_GET['code'])) {
    try {
        // Lấy access token
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        
        if (isset($token['error'])) {
            throw new Exception($token['error_description']);
        }
        
        $client->setAccessToken($token);
        
        // Lấy thông tin người dùng
        $google_oauth = new Google_Service_Oauth2($client);
        $google_account_info = $google_oauth->userinfo->get();
        
        // Thông tin user từ Google
        $google_id = $google_account_info->id;
        $email = $google_account_info->email;
        $name = $google_account_info->name;
        $picture = $google_account_info->picture;
        
        // Kết nối database
        $db = new DB_driver();
        $db->connect();
        
        // Kiểm tra xem email đã tồn tại chưa
        $sql = "SELECT * FROM nguoidung WHERE Email = ?";
        $stmt = $db->__conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // User đã tồn tại - đăng nhập
            $user = $result->fetch_assoc();
            
            // Cập nhật Google ID nếu chưa có
            if (empty($user['google_id'])) {
                $update_sql = "UPDATE nguoidung SET google_id = ? WHERE Email = ?";
                $update_stmt = $db->__conn->prepare($update_sql);
                $update_stmt->bind_param("ss", $google_id, $email);
                $update_stmt->execute();
            }
            
            // Lưu thông tin vào session (giống với ProcessAccount.php)
            $_SESSION['currentUser'] = $user;
            
            // Chuyển về trang chủ
            header('Location: ../Index.php?login=success');
            exit();
            
        } else {
            // User chưa tồn tại - tạo tài khoản mới
            $username = explode('@', $email)[0]; // Lấy phần trước @ làm username
            $username = preg_replace('/[^a-zA-Z0-9]/', '', $username); // Loại bỏ ký tự đặc biệt
            
            // Kiểm tra username đã tồn tại chưa
            $check_username_sql = "SELECT * FROM nguoidung WHERE TaiKhoan = ?";
            $check_stmt = $db->__conn->prepare($check_username_sql);
            $check_stmt->bind_param("s", $username);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            // Nếu username đã tồn tại, thêm số ngẫu nhiên
            if ($check_result->num_rows > 0) {
                $username = $username . rand(100, 999);
            }
            
            // Tách họ và tên
            $nameParts = explode(' ', $name);
            $ten = array_pop($nameParts); // Lấy phần cuối làm tên
            $ho = implode(' ', $nameParts); // Phần còn lại là họ
            
            // Tạo tài khoản mới (MatKhau để NULL vì đăng nhập bằng Google)
            $insert_sql = "INSERT INTO nguoidung (Ho, Ten, SDT, Email, DiaChi, TaiKhoan, MatKhau, MaQuyen, TrangThai, google_id) 
                          VALUES (?, ?, '', ?, '', ?, NULL, 1, 1, ?)";
            $insert_stmt = $db->__conn->prepare($insert_sql);
            $insert_stmt->bind_param("sssss", $ho, $ten, $email, $username, $google_id);
            $insert_stmt->execute();
            
            $new_user_id = $db->__conn->insert_id;
            
            // Lấy thông tin user mới tạo
            $get_user_sql = "SELECT * FROM nguoidung WHERE MaND = ?";
            $get_user_stmt = $db->__conn->prepare($get_user_sql);
            $get_user_stmt->bind_param("i", $new_user_id);
            $get_user_stmt->execute();
            $new_user = $get_user_stmt->get_result()->fetch_assoc();
            
            // Lưu thông tin vào session (giống với ProcessAccount.php)
            $_SESSION['currentUser'] = $new_user;
            
            // Chuyển về trang chủ
            header('Location: ../Index.php?register=success');
            exit();
        }
        
    } catch (Exception $e) {
        // Xử lý lỗi
        echo "<script>alert('Lỗi đăng nhập: " . addslashes($e->getMessage()) . "'); window.location.href='../SignIn.php';</script>";
        exit();
    }
    
} else {
    // Không có code, chuyển về trang đăng nhập
    header('Location: ../SignIn.php?error=google_auth_failed');
    exit();
}
?>

