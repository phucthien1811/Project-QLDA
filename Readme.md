# 🛍️ 3AM Store — Website mua bán hỗ trợ sửa chữa thiết bị điện thoại - Học phần: Quản lý dự án công nghệ thông tin - Nhóm 1


---


## 🌟 Giới thiệu dự án

**3AM Store** là website thương mại điện tử chuyên bán sửa chữa điện thoại, được xây dựng bằng PHP thuần (không framework) kết hợp MySQL. Dự án mô phỏng đầy đủ quy trình mua sắm trực tuyến từ duyệt sản phẩm, đặt hàng, thanh toán đến dịch vụ hậu mãi (bảo hành, sửa chữa), cùng hệ thống quản trị nội bộ đầy đủ chức năng.

---

## ✨ Tính năng

### 👤 Khách hàng

| Tính năng | Mô tả |
|-----------|-------|
| 🛒 Duyệt & Tìm kiếm | Xem danh sách sản phẩm, lọc theo dòng máy và khoảng giá |
| 📱 Chi tiết sản phẩm | Chọn phiên bản, dung lượng, màu sắc — giá cập nhật theo thời gian thực |
| 🛍️ Giỏ hàng | Thêm/sửa/xóa sản phẩm, lưu trữ qua localStorage |
| 💳 Thanh toán | Đặt hàng COD hoặc thanh toán MoMo, hỗ trợ nhập mã voucher |
| 👤 Tài khoản | Đăng ký/Đăng nhập bằng Email hoặc Google, quản lý hồ sơ cá nhân |
| 📦 Theo dõi đơn hàng | Xem lịch sử và trạng thái đơn hàng |
| 🔧 Bảo hành & Sửa chữa | Đặt lịch sửa chữa, theo dõi phiếu sửa chữa |
| 💬 Chatbot AI | Tư vấn sản phẩm tự động, chuyển nhân viên khi cần |
| ⭐ Đánh giá | Đánh giá sản phẩm đã mua |

### 🔧 Quản trị (Admin/Staff)

| Tính năng | Mô tả |
|-----------|-------|
| 📊 Dashboard | Thống kê doanh thu, đơn hàng, khách hàng; biểu đồ Chart.js |
| 📱 Quản lý sản phẩm | Thêm/sửa/xóa Series sản phẩm với đầy đủ phiên bản, màu sắc, giá |
| 📋 Quản lý đơn hàng | Xem danh sách, cập nhật trạng thái quy trình (5 bước) |
| 👥 Quản lý khách hàng | Xem danh sách, lịch sử mua hàng |
| 👨‍💼 Quản lý nhân viên | Tạo tài khoản staff, phân quyền |
| 🎫 Quản lý voucher | Tạo mã giảm giá, quản lý thời hạn và lượt dùng |
| 🔧 Quản lý sửa chữa | Xử lý phiếu sửa chữa, cập nhật kết quả |
| 💬 Quản lý Chatbot | Xem lịch sử hội thoại, trả lời trực tiếp trong chế độ CSKH |

---

## 🛠️ Công nghệ sử dụng

### Backend
- **PHP 8.0+** — Logic xử lý, API endpoints (không dùng framework)
- **MySQL / MariaDB** — Cơ sở dữ liệu quan hệ
- **Composer** — Quản lý thư viện PHP
- **google/apiclient** — Tích hợp Google OAuth 2.0

### Frontend
- **Bootstrap 5.3** — UI framework responsive
- **jQuery 3.x** — DOM manipulation, AJAX
- **Chart.js** — Biểu đồ thống kê admin
- **Owl Carousel** — Banner slider trang chủ
- **ion.rangeSlider** — Bộ lọc giá khoảng
- **SweetAlert2** — Hộp thoại thông báo đẹp

### Tích hợp bên thứ ba
- **MoMo Payment API** — Thanh toán ví điện tử (Sandbox)
- **Google Gemini API** — AI Chatbot (gemini-2.5-flash)
- **Google OAuth 2.0** — Đăng nhập bằng tài khoản Google

---

## 💻 Yêu cầu hệ thống

| Phần mềm | Phiên bản tối thiểu | Ghi chú |
|----------|---------------------|---------|
| XAMPP | 8.0+ | Bao gồm Apache + MySQL + PHP |
| PHP | 8.0+ | Cần extension: mysqli, curl, json, mbstring |
| MySQL | 5.7+ / MariaDB 10.4+ | |
| Composer | 2.0+ | Để cài google/apiclient |
| Trình duyệt | Chrome 100+, Firefox 100+, Edge 100+ | |

---

## 🚀 Hướng dẫn cài đặt

### Bước 1: Clone repository

```bash
cd C:\xampp\htdocs
git clone https://github.com/<your-username>/Project-CNW.git
cd Project-CNW
```

### Bước 2: Cài đặt Composer dependencies

```bash
composer install
```

> Lệnh này sẽ tải thư viện `google/apiclient` vào thư mục `vendor/`.

### Bước 3: Khởi động XAMPP

1. Mở **XAMPP Control Panel**
2. Nhấn **Start** cho **Apache** và **MySQL**
3. Đảm bảo cả hai dịch vụ hiển thị màu **xanh lá**

### Bước 4: Tạo Database

**Cách 1 — phpMyAdmin (khuyến nghị):**
1. Truy cập: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Tạo database mới tên `web2`, charset `utf8mb4`, collation `utf8mb4_unicode_ci`
3. Chọn database `web2` → tab **Import** → chọn file `sql/web2.sql` → **Go**

**Cách 2 — Command Line:**
```bash
cd C:\xampp\mysql\bin
.\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS web2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
.\mysql.exe -u root web2 < "C:\xampp\htdocs\Project-CNW\sql\web2.sql"
```

### Bước 5: Kiểm tra cấu hình Database

Mở file `Backend/Database/DbDriver.php`, xác nhận thông tin kết nối:

```php
$localhost = "localhost";   // Host MySQL
$user      = "root";        // Username (mặc định XAMPP)
$pass      = "";            // Password (mặc định XAMPP để trống)
$DbName    = "web2";        // Tên database vừa tạo
```

### Bước 6: Cấu hình Google OAuth *(tùy chọn)*

Nếu muốn sử dụng tính năng **Đăng nhập bằng Google**:

1. Tạo project và credentials tại [Google Cloud Console](https://console.cloud.google.com/apis/credentials)
2. Thêm Authorized redirect URI: `http://localhost/Project-CNW/Api/GoogleCallback.php`
3. Copy file cấu hình mẫu:
   ```bash
   copy Api\GoogleConfigExample.php Api\GoogleConfig.php
   ```
4. Mở `Api/GoogleConfig.php` và điền thông tin:
   ```php
   define('GOOGLE_CLIENT_ID',     'your-client-id.apps.googleusercontent.com');
   define('GOOGLE_CLIENT_SECRET', 'your-client-secret');
   define('GOOGLE_REDIRECT_URI',  'http://localhost/Project-CNW/Api/GoogleCallback.php');
   ```

### Bước 7: Cấu hình AI Chatbot *(tùy chọn)*

Nếu muốn sử dụng tính năng **Chatbot AI (Gemini)**:

1. Lấy API key tại [Google AI Studio](https://aistudio.google.com/app/apikey)
2. Copy file cấu hình mẫu:
   ```bash
   copy Api\ConfigExample.php Api\Config.php
   ```
3. Mở `Api/Config.php` và điền:
   ```php
   define('GEMINI_API_KEY', 'your-gemini-api-key-here');
   ```

### Bước 8: Cấu hình MoMo Payment *(tùy chọn)*

Dự án sử dụng **MoMo Sandbox** (môi trường test) với credentials mặc định trong `Api/MomoPayment.php`. Không cần cấu hình thêm để test chức năng thanh toán.

### Bước 9: Chạy ứng dụng

Mở trình duyệt và truy cập:





## 👤 Tài khoản mặc định

| Vai trò | Email | Mật khẩu | Quyền |
|---------|-------|----------|-------|
| Admin | `admin@3am.com` | `admin` | Toàn quyền hệ thống |
| Nhân viên | `staff@3am.com` | `staff123` | Quản lý đơn, sửa chữa |
| Khách hàng | Tự đăng ký | — | Mua hàng, xem đơn |

> ⚠️ **Lưu ý bảo mật:** Thay đổi mật khẩu mặc định trước khi deploy lên môi trường production.

---

## 🛠️ Xử lý lỗi thường gặp

### ❌ Lỗi kết nối database (`Lỗi kết nối`)

**Nguyên nhân:** MySQL chưa start hoặc sai thông tin kết nối.

**Giải pháp:**
1. Kiểm tra MySQL đã **Start** trong XAMPP Control Panel
2. Mở `Backend/Database/DbDriver.php`, xác nhận `$user`, `$pass`, `$DbName`
3. Đảm bảo database `web2` đã được tạo và import SQL

---

### ❌ Lỗi 404 Not Found

**Giải pháp:**
1. Kiểm tra project nằm đúng thư mục `C:\xampp\htdocs\Project-CNW`
2. Kiểm tra Apache đã **Start** trong XAMPP
3. Xác nhận file `.htaccess` tồn tại trong thư mục gốc

---

### ❌ Lỗi Composer (`composer: command not found`)

**Giải pháp:**
1. Tải Composer tại [https://getcomposer.org/download/](https://getcomposer.org/download/)
2. Cài đặt và thêm vào PATH hệ thống
3. Mở terminal mới, chạy lại `composer install`

---

### ❌ Chatbot báo lỗi API key

**Giải pháp:**
1. Đảm bảo file `Api/Config.php` đã được tạo (copy từ `ConfigExample.php`)
2. Kiểm tra `GEMINI_API_KEY` đã được điền đúng
3. Xem log tại `logs/chatbot_debug.log` để biết chi tiết lỗi

---

### ❌ Lỗi hiển thị tiếng Việt bị vỡ ký tự

**Giải pháp:**
1. Đảm bảo database charset là `utf8mb4`, collation `utf8mb4_unicode_ci`
2. Kiểm tra đầu file PHP có `header('Content-Type: text/html; charset=utf-8')`
3. Nếu dùng phpMyAdmin, đặt charset `utf8mb4` khi import SQL

---

## 📝 Ghi chú phát triển

- Thư mục `vendor/` **không được commit** — chạy `composer install` sau khi clone
- Các file config nhạy cảm (`Api/Config.php`, `Api/GoogleConfig.php`) **không được commit** — đã gitignore, tạo từ file Example
- Log files được lưu trong `logs/` — **không cần commit** (đã gitignore)
- Dự án sử dụng MoMo **Sandbox** — không cần tài khoản thật để test



## 📄 License

Dự án được phát triển cho mục đích học tập trong khuôn khổ môn **Quản lý Dự án Thông tin**, Trường Đại học ... .  
Không sử dụng cho mục đích thương mại.
