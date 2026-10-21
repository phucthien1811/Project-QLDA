-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th1 08, 2026 lúc 09:09 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `web2`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `careplan`
--

CREATE TABLE `careplan` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'T??n g??i b???o h??nh',
  `description` text DEFAULT NULL COMMENT 'M?? t??? chi ti???t',
  `original_price` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Gi?? g???c',
  `discount_percent` int(11) NOT NULL DEFAULT 0 COMMENT 'Ph???n tr??m gi???m gi??',
  `final_price` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Gi?? sau gi???m',
  `duration` varchar(50) DEFAULT NULL COMMENT 'Th???i h???n (6 th??ng, 1 n??m, 2 n??m...)',
  `plan_type` enum('protection','warranty') NOT NULL DEFAULT 'protection' COMMENT 'Lo???i g??i: protection=b???o v???, warranty=gia h???n b???o h??nh',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Th??? t??? hi???n th???',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Tr???ng th??i: 1=ho???t ?????ng, 0=v?? hi???u',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='B???ng l??u c??c g??i 3AM Care+';

--
-- Đang đổ dữ liệu cho bảng `careplan`
--

INSERT INTO `careplan` (`id`, `name`, `description`, `original_price`, `discount_percent`, `final_price`, `duration`, `plan_type`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(7, '3AM Care+ (Rơi vỡ, vào nước) 1 năm', 'Bảo vệ thiết bị khỏi rơi vỡ, vào nước trong 1 năm', 1099000.00, 25, 824250.00, '1 năm', 'protection', 2, 1, '2025-12-03 22:30:24', '2025-12-04 07:30:36'),
(8, '3AM Care+ (Rơi vỡ, vào nước) 6 tháng', 'Bảo vệ thiết bị khỏi rơi vỡ, vào nước trong 6 tháng', 699000.00, 25, 524250.00, '6 tháng', 'protection', 3, 1, '2025-12-03 22:30:24', '2025-12-04 07:30:45'),
(10, 'Gói Gia hạn Bảo hành 3AM 6 tháng', 'Gia hạn bảo hành chính hãng thêm 6 tháng', 499000.00, 20, 399200.00, '6 tháng', 'warranty', 5, 1, '2025-12-03 22:30:24', '2025-12-04 07:30:25'),
(12, 'Gói Gia hạn Bảo hành 3AM 1 năm', 'Gia hạn bảo hành chính hãng thêm 1 năm', 799000.00, 20, 639200.00, '1 năm', 'warranty', 5, 1, '2025-12-03 22:30:24', '2025-12-04 07:30:04');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitiethoadon`
--

CREATE TABLE `chitiethoadon` (
  `MaHD` int(11) NOT NULL,
  `MaSP` int(11) DEFAULT NULL,
  `SoLuong` int(11) NOT NULL,
  `DonGia` float NOT NULL,
  `TenSP` varchar(255) DEFAULT NULL,
  `HinhAnh` varchar(500) DEFAULT NULL,
  `SeriesConfig` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chitiethoadon`
--

INSERT INTO `chitiethoadon` (`MaHD`, `MaSP`, `SoLuong`, `DonGia`, `TenSP`, `HinhAnh`, `SeriesConfig`) VALUES
(40, 0, 1, 44444000, 'iPhone 20 - iPhone 20 VIP 128GB/8GB - Màu Hồng', 'img/series/692e970d86f09_1764661005.jpg', '{\"seriesId\":\"10\",\"variantId\":15,\"storageId\":19,\"colorId\":10,\"price\":44444000,\"name\":\"iPhone 20 - 128GB \\/ 8GB - M\\u00e0u H\\u1ed3ng\"}'),
(41, 0, 1, 2200000, 'iPhone 17 -  128GB/16GB - xanh lá', 'img/series/692e943d2aff0_1764660285.jpg', '{\"seriesId\":\"8\",\"variantId\":13,\"storageId\":16,\"colorId\":3,\"price\":2200000,\"name\":\"iPhone 17 - 128GB \\/ 16GB - xanh l\\u00e1\"}'),
(42, 0, 1, 200000, 'iPhone 17 -  128GB/8GB - Đen', 'img/series/692e943d2aff0_1764660285.jpg', '{\"seriesId\":\"8\",\"variantId\":13,\"storageId\":15,\"colorId\":1,\"price\":200000,\"name\":\"iPhone 17 - 128GB \\/ 8GB - \\u0110en\"}'),
(43, 0, 1, 222222, 'iPhone 17 - iPhone 17 Titanium 512GB/32GB - Hồng', 'img/series/693107db323b6_1764820955.jpg', '{\"seriesId\":\"13\",\"variantId\":18,\"storageId\":22,\"colorId\":15,\"price\":222222,\"name\":\"iPhone 17 - 512GB \\/ 32GB - H\\u1ed3ng\"}'),
(44, 0, 1, 34990000, 'iPhone 17 - iPhone 17 Pro 256GB/12GB - Bạc', 'img/series/69317addc694b_1764850397.jpg', '{\"seriesId\":\"18\",\"variantId\":23,\"storageId\":33,\"colorId\":37,\"price\":34990000,\"name\":\"iPhone 17 - 256GB \\/ 12GB - B\\u1ea1c\"}'),
(45, 0, 1, 40990000, 'iPhone 17 - iPhone 17 Pro 512GB/12GB - Cam Vũ Trụ', 'img/series/69317addc694b_1764850397.jpg', '{\"seriesId\":\"18\",\"variantId\":23,\"storageId\":34,\"colorId\":41,\"price\":40990000,\"name\":\"iPhone 17 - 512GB \\/ 12GB - Cam V\\u0169 Tr\\u1ee5\"}'),
(45, 0, 1, 34990000, 'iPhone 17 - iPhone 17 Pro 256GB/12GB - Xanh Đậm', 'img/series/69317addc694b_1764850397.jpg', '{\"seriesId\":\"18\",\"variantId\":23,\"storageId\":33,\"colorId\":39,\"price\":34990000,\"name\":\"iPhone 17 - 256GB \\/ 12GB - Xanh \\u0110\\u1eadm\"}'),
(46, 0, 1, 40990000, 'iPhone 17 - iPhone 17 Pro 512GB/12GB - Cam Vũ Trụ', 'img/series/69317addc694b_1764850397.jpg', '{\"seriesId\":\"18\",\"variantId\":23,\"storageId\":34,\"colorId\":41,\"price\":40990000,\"name\":\"iPhone 17 - 512GB \\/ 12GB - Cam V\\u0169 Tr\\u1ee5\"}'),
(46, 0, 1, 34990000, 'iPhone 17 - iPhone 17 Pro 256GB/12GB - Xanh Đậm', 'img/series/69317addc694b_1764850397.jpg', '{\"seriesId\":\"18\",\"variantId\":23,\"storageId\":33,\"colorId\":39,\"price\":34990000,\"name\":\"iPhone 17 - 256GB \\/ 12GB - Xanh \\u0110\\u1eadm\"}'),
(47, 0, 1, 40990000, 'iPhone 17 - iPhone 17 Pro 512GB/12GB - Cam Vũ Trụ', 'img/series/69317addc694b_1764850397.jpg', '{\"seriesId\":\"18\",\"variantId\":23,\"storageId\":34,\"colorId\":41,\"price\":40990000,\"name\":\"iPhone 17 - 512GB \\/ 12GB - Cam V\\u0169 Tr\\u1ee5\"}'),
(47, 0, 1, 34990000, 'iPhone 17 - iPhone 17 Pro 256GB/12GB - Xanh Đậm', 'img/series/69317addc694b_1764850397.jpg', '{\"seriesId\":\"18\",\"variantId\":23,\"storageId\":33,\"colorId\":39,\"price\":34990000,\"name\":\"iPhone 17 - 256GB \\/ 12GB - Xanh \\u0110\\u1eadm\"}'),
(48, 0, 1, 40990000, 'iPhone 17 - iPhone 17 Pro 512GB/12GB - Cam Vũ Trụ', 'img/series/69317addc694b_1764850397.jpg', '{\"seriesId\":\"18\",\"variantId\":23,\"storageId\":34,\"colorId\":41,\"price\":40990000,\"name\":\"iPhone 17 - 512GB \\/ 12GB - Cam V\\u0169 Tr\\u1ee5\"}'),
(48, 0, 1, 34990000, 'iPhone 17 - iPhone 17 Pro 256GB/12GB - Xanh Đậm', 'img/series/69317addc694b_1764850397.jpg', '{\"seriesId\":\"18\",\"variantId\":23,\"storageId\":33,\"colorId\":39,\"price\":34990000,\"name\":\"iPhone 17 - 256GB \\/ 12GB - Xanh \\u0110\\u1eadm\"}'),
(49, 0, 1, 40990000, 'iPhone 17 - iPhone 17 Pro 512GB/12GB - Cam Vũ Trụ', 'img/series/69317addc694b_1764850397.jpg', '{\"seriesId\":\"18\",\"variantId\":23,\"storageId\":34,\"colorId\":41,\"price\":40990000,\"name\":\"iPhone 17 - 512GB \\/ 12GB - Cam V\\u0169 Tr\\u1ee5\"}'),
(50, 0, 1, 34990000, 'iPhone 17 - iPhone 17 Pro 256GB/12GB - Cam Vũ Trụ', 'img/series/69317addc694b_1764850397.jpg', '{\"seriesId\":\"18\",\"variantId\":23,\"storageId\":33,\"colorId\":38,\"price\":36414250,\"basePrice\":34990000,\"carePlanId\":9,\"carePlanPrice\":1424250,\"name\":\"iPhone 17 - 256GB \\/ 12GB - Cam V\\u0169 Tr\\u1ee5\"}'),
(51, 0, 1, 43389200, 'iPhone 16 Pro Max - iPhone 16 Pro Max 1TB/8GB - Titan Tự Nhiên + 3AM Care+ (+399.200 ₫)', 'img/series/695e7ac63d7fe_1767799494.png', '{\"seriesId\":\"20\",\"variantId\":25,\"storageId\":40,\"colorId\":63,\"price\":43389200,\"basePrice\":42990000,\"carePlanId\":10,\"carePlanPrice\":399200,\"name\":\"iPhone 16 Pro Max - 1TB | 8GB - Titan T\\u1ef1 Nhi\\u00ean\"}');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danhgia`
--

CREATE TABLE `danhgia` (
  `MaSP` int(11) NOT NULL,
  `MaND` varchar(10) NOT NULL,
  `SoSao` int(11) NOT NULL,
  `BinhLuan` varchar(255) NOT NULL,
  `NgayLap` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hoadon`
--

CREATE TABLE `hoadon` (
  `MaHD` int(11) NOT NULL,
  `MaND` int(11) NOT NULL,
  `NgayLap` datetime NOT NULL,
  `NguoiNhan` varchar(50) NOT NULL,
  `SDT` varchar(20) NOT NULL,
  `DiaChi` varchar(100) NOT NULL,
  `PhuongThucTT` varchar(20) NOT NULL,
  `DaThanhToan` tinyint(1) NOT NULL DEFAULT 0,
  `TongTien` float NOT NULL,
  `TrangThai` varchar(70) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `hoadon`
--

INSERT INTO `hoadon` (`MaHD`, `MaND`, `NgayLap`, `NguoiNhan`, `SDT`, `DiaChi`, `PhuongThucTT`, `DaThanhToan`, `TongTien`, `TrangThai`) VALUES
(40, 13, '2025-12-03 11:36:40', 'Thiên Nguyễn', '0123456789', '999, CMT8, P12, TPHCM', 'cod', 0, 44444000, '1'),
(41, 13, '2025-12-03 18:01:48', 'Thiên Nguyễn', '0123456789', '999, CMT8, P12, TPHCM', 'momo', 0, 2200000, '1'),
(42, 13, '2025-12-03 18:06:40', 'Thiên Nguyễn', '0123456789', '999, CMT8, P12, TPHCM', 'momo', 1, 200000, '5'),
(43, 13, '2025-12-04 11:39:47', 'Thiên Nguyễn', '0123456789', '999, CMT8, P12, TPHCM', 'momo', 1, 222222, '5'),
(44, 13, '2025-12-04 19:17:37', 'Thiên Nguyễn', '0123456789', '999, CMT8, P12, TPHCM', 'cod', 1, 34990000, '5'),
(45, 7, '2025-12-28 16:05:54', 'Admin System', '0987654321', '999, CMT8, P12, TPHCM', 'cod', 1, 75980000, '5'),
(46, 7, '2025-12-28 16:06:41', 'Admin System', '0987654321', '999, CMT8, P12, TPHCM', 'cod', 0, 75980000, '2'),
(47, 7, '2025-12-28 16:31:01', 'Admin System', '0987654321', '999, CMT8, P12, TPHCM', 'cod', 1, 75980000, '1'),
(48, 7, '2025-12-28 16:34:32', 'Admin System', '0987654321', '999, CMT8, P12, TPHCM', 'cod', 0, 75980000, '2'),
(49, 13, '2026-01-05 16:39:18', 'Thiên Nguyễn', '0123456789', '999, CMT8, P12, TPHCM', 'cod', 1, 40990000, '5'),
(50, 13, '2026-01-07 16:49:37', 'Thiên Nguyễn', '0777999686', '999, CMT8, P12, TPHCM', 'cod', 0, 34990000, '0'),
(51, 13, '2026-01-08 08:48:30', 'Thiên Nguyễn', '0777999686', '999, CMT8, P12, TPHCM', 'momo', 1, 433892, '1');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khuyenmai`
--

CREATE TABLE `khuyenmai` (
  `MaKM` int(11) NOT NULL,
  `TenKM` varchar(100) NOT NULL,
  `LoaiKM` varchar(20) NOT NULL,
  `GiaTriKM` float NOT NULL,
  `NgayBD` datetime NOT NULL,
  `NgayKT` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `khuyenmai`
--

INSERT INTO `khuyenmai` (`MaKM`, `TenKM`, `LoaiKM`, `GiaTriKM`, `NgayBD`, `NgayKT`) VALUES
(1, 'Không khuyến mãi', 'Nothing', 0, '2019-04-08 00:00:00', '2022-04-17 00:00:00'),
(2, 'Giảm giá', 'GiamGia', 500000, '2019-05-01 00:00:00', '2019-05-31 00:00:00'),
(3, 'Giá rẻ online', 'GiaReOnline', 650000, '2019-05-01 00:00:00', '2019-05-31 00:00:00'),
(4, 'Trả góp', 'TraGop', 0, '2019-05-01 00:00:00', '2019-05-31 00:00:00'),
(5, 'Mới ra mắt', 'MoiRaMat', 0, '2019-05-01 00:00:00', '2019-05-31 00:00:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loaisanpham`
--

CREATE TABLE `loaisanpham` (
  `MaLSP` int(11) NOT NULL,
  `TenLSP` varchar(70) NOT NULL,
  `HinhAnh` varchar(200) NOT NULL,
  `Mota` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `loaisanpham`
--

INSERT INTO `loaisanpham` (`MaLSP`, `TenLSP`, `HinhAnh`, `Mota`) VALUES
(1, 'Apple', 'Apple.jpg', 'Các sản phẩm của Apple'),
(2, 'Coolpad', 'Coolpad.png', 'Các sản phẩm của coolpad'),
(3, 'HTC', 'HTC.jpg', 'Các sản phẩm đến từ HTC'),
(4, 'Itel', 'Itel.jpg', 'Các sản phẩm của Itel'),
(5, 'Mobell', 'Mobell.jpg', 'Các sản phẩm của mobell'),
(6, 'Vivo', 'Vivo.jpg', 'Các sản phẩm của Vivo'),
(7, 'Oppo', 'Oppo.jpg', 'Camara Selphi cuc chat tu Oppo'),
(8, 'SamSung', 'Samsung.jpg', 'Khuyen mai lon tu SamSung'),
(9, 'Phillips', 'Philips.jpg', 'Cac san pham tuyet dep tu Phillip'),
(10, 'Nokia', 'Nokia.jpg', 'Các sản phẩm đến từ thương hiệu Nokia'),
(11, 'Motorola', 'Motorola.jpg', 'Motorola is the best'),
(12, 'Mobiistar', 'Mobiistar.jpg', 'Các sản phẩm đến từ thương hiệu Mobiistar'),
(14, 'Xiaomi', 'Xiaomi.png', 'Các sản phẩm đến từ thương hiệu Xiaomi'),
(15, 'Realme', 'img/company/realme.jpg', 'Điện thoại Realme'),
(16, 'OnePlus', 'img/company/oneplus.jpg', 'Điện thoại OnePlus');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoidung`
--

CREATE TABLE `nguoidung` (
  `MaND` int(11) NOT NULL,
  `Ho` varchar(20) NOT NULL,
  `Ten` varchar(20) NOT NULL,
  `GioiTinh` varchar(10) NOT NULL,
  `NgaySinh` date DEFAULT NULL,
  `SDT` varchar(20) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `DiaChi` varchar(200) NOT NULL,
  `TaiKhoan` varchar(100) NOT NULL,
  `MatKhau` varchar(255) DEFAULT NULL,
  `MaQuyen` int(11) NOT NULL,
  `TrangThai` int(11) NOT NULL,
  `google_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoidung`
--

INSERT INTO `nguoidung` (`MaND`, `Ho`, `Ten`, `GioiTinh`, `NgaySinh`, `SDT`, `Email`, `DiaChi`, `TaiKhoan`, `MatKhau`, `MaQuyen`, `TrangThai`, `google_id`) VALUES
(7, 'Admin', 'System', '', NULL, '0987654321', 'admin@admin.com', '', 'admin', '21232f297a57a5a743894a0e4a801fc3', 2, 1, NULL),
(8, 'Nguyễn', 'Duy', 'Nam', NULL, '0123456789', 'Duy@gmail.com', 'Đường số 1 , Bình Chánh , Thành phố Hồ Chí Minh', 'Duyyy', '$2y$10$71HjKpELuV6sfocms7bDMu6UwDdTX6EZTHV7yGwu0w25jK2te9gBm', 3, 1, NULL),
(13, 'Thiên', 'Nguyễn', 'Nam', '2000-12-12', '0777999686', 'thientiy1811@gmail.com', '555 An Dương Vương , Quận 5, Hồ Chí Minh', 'thientiy1811', NULL, 1, 1, '112181714857421916336');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phanquyen`
--

CREATE TABLE `phanquyen` (
  `MaQuyen` int(11) NOT NULL,
  `TenQuyen` varchar(20) NOT NULL,
  `ChiTietQuyen` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `phanquyen`
--

INSERT INTO `phanquyen` (`MaQuyen`, `TenQuyen`, `ChiTietQuyen`) VALUES
(1, 'Customer', 'Khách hàng có tài khoản'),
(2, 'Admin', 'Quản trị viên - Toàn quyền'),
(3, 'Staff', 'Nhân viên - Quản lý đơn hàng');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieusuachua`
--

CREATE TABLE `phieusuachua` (
  `MaPhieu` int(11) NOT NULL,
  `MaND` int(11) NOT NULL,
  `MaSP` varchar(255) DEFAULT NULL,
  `TenSP` varchar(255) DEFAULT NULL,
  `HinhAnhSP` varchar(255) DEFAULT NULL,
  `LoaiSuCo` varchar(100) NOT NULL COMMENT 'Loß║íi sß╗▒ cß╗æ: M├án h├¼nh, Pin, Camera, Loa/Mic, Phß║ºn mß╗üm, Kh├íc',
  `MoTa` text DEFAULT NULL COMMENT 'M├┤ tß║ú chi tiß║┐t vß║Ñn ─æß╗ü',
  `NgayHen` date NOT NULL COMMENT 'Ng├áy hß║╣n sß╗¡a chß╗»a',
  `GioHen` varchar(20) NOT NULL COMMENT 'Giß╗Ø hß║╣n',
  `HoTen` varchar(100) NOT NULL COMMENT 'Hß╗ì t├¬n ngã░ß╗Øi li├¬n hß╗ç',
  `SDT` varchar(15) NOT NULL COMMENT 'Sß╗æ ─æiß╗çn thoß║íi',
  `Email` varchar(100) DEFAULT NULL,
  `GhiChu` text DEFAULT NULL COMMENT 'Ghi ch├║ th├¬m',
  `TrangThai` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1: Chß╗Ø x├íc nhß║¡n, 2: ─Éang sß╗¡a chß╗»a, 3: Ho├án tß║Ñt, 4: ─É├ú hß╗ºy',
  `NgayTao` datetime NOT NULL DEFAULT current_timestamp(),
  `NgayCapNhat` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `NhanVienXuLy` int(11) DEFAULT NULL COMMENT 'M├ú nh├ón vi├¬n xß╗¡ l├¢',
  `ChiPhiSuaChua` decimal(15,0) DEFAULT 0 COMMENT 'Chi ph├¡ sß╗¡a chß╗»a nß║┐u c├│',
  `KetQuaSuaChua` text DEFAULT NULL COMMENT 'Kß║┐t quß║ú sß╗¡a chß╗»a',
  `NgayHenTra` date DEFAULT NULL COMMENT 'NgÓy h?n tr? s?n ph?m sau khi s?a xong'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `phieusuachua`
--

INSERT INTO `phieusuachua` (`MaPhieu`, `MaND`, `MaSP`, `TenSP`, `HinhAnhSP`, `LoaiSuCo`, `MoTa`, `NgayHen`, `GioHen`, `HoTen`, `SDT`, `Email`, `GhiChu`, `TrangThai`, `NgayTao`, `NgayCapNhat`, `NhanVienXuLy`, `ChiPhiSuaChua`, `KetQuaSuaChua`, `NgayHenTra`) VALUES
(3, 13, '23', 'iPhone 17 - iPhone 17 Pro 512GB/12GB - Cam Vũ Trụ', 'http://localhost/Project-PHP/img/series/69317addc694b_1764850397.jpg', 'Pin', 'pin bị chai rồi', '2026-01-09', '08:00', 'Thiên Nguyễn', '0777999686', 'thientiy1811@gmail.com', '', 3, '2026-01-08 09:59:28', '2026-01-08 10:38:10', NULL, 100000, '', '2026-01-13'),
(4, 13, '18', 'iPhone 17 - iPhone 17 Titanium 512GB/32GB - Hồng', 'http://localhost/Project-PHP/img/series/693107db323b6_1764820955.jpg', 'Camera', 'HƯ CAMREA\n', '2026-01-10', '10:00', 'Thiên Nguyễn', '0777999686', 'thientiy1811@gmail.com', '', 1, '2026-01-08 10:30:57', NULL, NULL, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_series`
--

CREATE TABLE `product_series` (
  `id` int(11) NOT NULL,
  `series_name` varchar(255) NOT NULL COMMENT 'Tên series, VD: iPhone 16 Pro Max Series',
  `slug` varchar(255) NOT NULL COMMENT 'URL thân thiện, VD: iphone-16-pro-max-series',
  `description` text DEFAULT NULL COMMENT 'Mô tả chung về series',
  `specs_json` longtext DEFAULT NULL,
  `main_image` varchar(500) DEFAULT NULL COMMENT 'Đường dẫn ảnh đại diện chính',
  `display_price` decimal(15,2) DEFAULT NULL COMMENT 'Giß hi?n th? ? trang ch? (VNð)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_series`
--

INSERT INTO `product_series` (`id`, `series_name`, `slug`, `description`, `specs_json`, `main_image`, `display_price`, `created_at`, `updated_at`) VALUES
(20, 'iPhone 16 Pro Max', 'iphone-16-pro-max', '', '{\r\n  \"Màn hình\": \"Super Retina XDR OLED, 6.9 inch, 120Hz ProMotion\",\r\n  \"Độ phân giải\": \"2796 x 1290\",\r\n  \"Hệ điều hành\": \"iOS 18\",\r\n  \"CPU\": \"Apple A18 Pro\",\r\n  \"RAM\": \"8GB\",\r\n  \"Bộ nhớ trong\": \"256GB / 512GB / 1TB\",\r\n  \"Camera sau\": \"Camera chính 48MP + Siêu rộng 48MP + Tele 12MP (zoom quang 5x)\",\r\n  \"Camera trước\": \"12MP, Face ID\",\r\n  \"Quay video\": \"4K Dolby Vision, hỗ trợ quay ProRes\",\r\n  \"Pin\": \"Pin dung lượng lớn, hỗ trợ sạc nhanh\",\r\n  \"Sạc nhanh\": \"Có (USB-C)\",\r\n  \"Sạc không dây\": \"MagSafe\",\r\n  \"Cổng sạc\": \"USB Type-C\",\r\n  \"Kết nối\": \"5G, Wi-Fi, Bluetooth, NFC\",\r\n  \"SIM\": \"2 SIM (Nano SIM + eSIM) (tùy thị trường)\",\r\n  \"Chất liệu\": \"Khung Titan, mặt kính cường lực\",\r\n  \"Chống nước\": \"IP68\",\r\n  \"Bảo mật\": \"Face ID\"\r\n}', 'img/series/695e7ac63d7fe_1767799494.png', 31590000.00, '2026-01-07 15:24:54', NULL),
(21, 'Galaxy Z Flip 7', 'galaxy-z-flip-7', '', '{\r\n  \"Màn hình chính\": \"Dynamic AMOLED 2X, 6.7 inch, Full HD+, 120Hz\",\r\n  \"Màn hình phụ\": \"Super AMOLED, 3.4 inch (FlexWindow)\",\r\n  \"Hệ điều hành\": \"Android 15, One UI 7\",\r\n  \"CPU\": \"Snapdragon 8 Gen 3 for Galaxy\",\r\n  \"RAM\": \"12GB\",\r\n  \"Bộ nhớ trong\": \"256GB / 512GB\",\r\n  \"Camera sau\": \"50MP (chính) + 12MP (siêu rộng)\",\r\n  \"Camera trước\": \"10MP\",\r\n  \"Quay video\": \"4K@60fps\",\r\n  \"Pin\": \"4000 mAh\",\r\n  \"Sạc nhanh\": \"25W\",\r\n  \"Sạc không dây\": \"Có\",\r\n  \"Cổng sạc\": \"USB Type-C\",\r\n  \"Kết nối\": \"5G, Wi-Fi 6E, Bluetooth 5.3, NFC\",\r\n  \"SIM\": \"2 SIM (Nano SIM + eSIM)\",\r\n  \"Chất liệu\": \"Khung nhôm Armor Aluminum, kính cường lực\",\r\n  \"Chống nước\": \"IPX8\",\r\n  \"Bảo mật\": \"Vân tay cạnh bên\",\r\n  \"Trọng lượng\": \"187 g\"\r\n}', 'img/series/695e7f00802b8_1767800576.webp', 24090000.00, '2026-01-07 15:42:56', NULL),
(24, 'iPhone 17', 'iphone-17', '', '{\r\n  \"Màn hình\": \"OLED, 6.1 inch, 120Hz\",\r\n  \"Hệ điều hành\": \"iOS 18\",\r\n  \"CPU\": \"Apple A18\",\r\n  \"RAM\": \"8GB\",\r\n  \"Bộ nhớ trong\": \"256GB / 512GB\",\r\n  \"Camera sau\": \"Camera kép (chính + siêu rộng)\",\r\n  \"Camera trước\": \"12MP, Face ID\",\r\n  \"Quay video\": \"4K\",\r\n  \"Pin\": \"Dung lượng lớn, tối ưu cho cả ngày\",\r\n  \"Sạc nhanh\": \"Có\",\r\n  \"Sạc không dây\": \"MagSafe\",\r\n  \"Cổng sạc\": \"USB Type-C\",\r\n  \"Kết nối\": \"5G, Wi-Fi, Bluetooth, NFC\",\r\n  \"SIM\": \"Nano SIM + eSIM (tùy thị trường)\",\r\n  \"Chất liệu\": \"Khung kim loại, mặt kính cường lực\",\r\n  \"Chống nước\": \"IP68\",\r\n  \"Bảo mật\": \"Face ID\"\r\n}', 'img/series/695e89d7427f5_1767803351.webp', 24990000.00, '2026-01-07 16:29:11', NULL),
(25, 'iPhone 17 Pro Max', 'iphone-17-pro-max', '', '{\r\n  \"Màn hình\": \"Super Retina XDR OLED 6.9 inch, 120Hz ProMotion\",\r\n  \"Độ phân giải\": \"2868 x 1320 pixels\",\r\n  \"Hệ điều hành\": \"iOS 26\",\r\n  \"CPU\": \"Apple A19 Pro\",\r\n  \"RAM\": \"8GB\",\r\n  \"Bộ nhớ trong\": \"256GB / 512GB / 1TB / 2TB\",\r\n  \"Camera sau\": \"48MP (chính, OIS) + 48MP (siêu rộng) + 48MP (telephoto, zoom quang 8x)\",\r\n  \"Camera trước\": \"18MP, Center Stage\",\r\n  \"Quay video\": \"4K Dolby Vision, ProRes\",\r\n  \"Pin\": \"Dung lượng lớn, tối ưu cho sử dụng cường độ cao\",\r\n  \"Sạc nhanh\": \"Có\",\r\n  \"Sạc không dây\": \"MagSafe\",\r\n  \"Cổng sạc\": \"USB Type-C\",\r\n  \"Kết nối\": \"5G, Wi-Fi, Bluetooth, NFC\",\r\n  \"SIM\": \"Nano SIM + eSIM\",\r\n  \"Chất liệu\": \"Khung Titan, mặt kính cường lực\",\r\n  \"Chống nước\": \"IP68\",\r\n  \"Bảo mật\": \"Face ID\"\r\n}', 'img/series/695f2ade656dc_1767844574.webp', 37690000.00, '2026-01-08 03:56:14', NULL),
(26, 'OPPO Reno15 5G', 'oppo-reno15-5g', '', '{\r\n  \"Màn hình\": \"OLED 6.59 inch, FHD+\",\r\n  \"Tần số quét\": \"120Hz\",\r\n  \"Độ phân giải\": \"2412 x 1080 pixels\",\r\n  \"Tỉ lệ màn hình\": \"20:9\",\r\n  \"Hệ điều hành\": \"ColorOS 14 (Android 14)\",\r\n  \"CPU\": \"Qualcomm Snapdragon 7s Gen 2\",\r\n  \"GPU\": \"Adreno\",\r\n  \"RAM\": \"8GB / 12GB\",\r\n  \"Bộ nhớ trong\": \"128GB / 256GB\",\r\n  \"Camera sau chính\": \"50MP\",\r\n  \"Camera siêu rộng\": \"8MP\",\r\n  \"Camera macro\": \"2MP\",\r\n  \"Camera trước\": \"32MP\",\r\n  \"Quay video\": \"4K@30fps\",\r\n  \"Pin\": \"5000 mAh\",\r\n  \"Sạc nhanh\": \"80W SUPERVOOC\",\r\n  \"Bluetooth\": \"5.3\",\r\n  \"Kết nối\": \"5G, Wi-Fi 6, GPS, NFC\",\r\n  \"SIM\": \"2 Nano SIM\",\r\n  \"Cổng sạc\": \"USB Type-C\",\r\n  \"Kích thước\": \"160.2 x 74.3 x 7.8 mm\",\r\n  \"Trọng lượng\": \"184 g\",\r\n  \"Chống nước\": \"Không\",\r\n  \"Bảo mật\": \"Vân tay dưới màn hình\"\r\n}', 'img/series/695f2dd8e4385_1767845336.webp', 16990000.00, '2026-01-08 04:08:56', NULL),
(28, 'OPPO Find  X9', 'oppo-find-x9', '', '{\r\n  \"Màn hình\": \"AMOLED, 6.78 inch, 1.5K, 120Hz\",\r\n  \"Hệ điều hành\": \"Android 15, ColorOS\",\r\n  \"CPU\": \"MediaTek Dimensity 9500\",\r\n  \"GPU\": \"Immortalis-G720\",\r\n  \"RAM\": \"12GB / 16GB\",\r\n  \"Bộ nhớ trong\": \"256GB / 512GB\",\r\n  \"Camera sau\": \"Chính 50MP (OIS) + Góc siêu rộng 50MP + Telephoto 50MP\",\r\n  \"Camera trước\": \"32MP\",\r\n  \"Quay video\": \"4K@60fps\",\r\n  \"Pin\": \"5000mAh\",\r\n  \"Sạc nhanh\": \"80W SuperVOOC\",\r\n  \"Sạc không dây\": \"Có\",\r\n  \"Cổng sạc\": \"USB Type-C\",\r\n  \"Kết nối\": \"5G, Wi-Fi 7, Bluetooth 5.4, NFC\",\r\n  \"SIM\": \"2 Nano SIM hoặc eSIM\",\r\n  \"Chất liệu\": \"Khung kim loại, mặt kính\",\r\n  \"Chống nước\": \"IP68\",\r\n  \"Bảo mật\": \"Vân tay trong màn hình, Face Unlock\"\r\n}', 'img/series/695f5208551a8_1767854600.webp', 22990000.00, '2026-01-08 06:43:20', NULL),
(29, 'Galaxy S25 FE', 'galaxy-s25-fe', '', '{\r\n  \"Màn hình\": \"Dynamic AMOLED 2X, 6.7 inch, Full HD+ (1080 x 2400), 120Hz\",\r\n  \"Hệ điều hành\": \"Android 15, One UI 7\",\r\n  \"Camera sau\": \"50MP (chính) + 12MP (siêu rộng) + 8MP (telephoto 3x)\",\r\n  \"Camera trước\": \"10MP, f/2.4\",\r\n  \"CPU\": \"Exynos 2400e (4nm)\",\r\n  \"GPU\": \"Xclipse 940\",\r\n  \"RAM\": \"8 GB\",\r\n  \"Bộ nhớ trong\": \"128GB / 256GB\",\r\n  \"Thẻ nhớ\": \"Không hỗ trợ\",\r\n  \"Dung lượng pin\": \"4700 mAh\",\r\n  \"Sạc nhanh\": \"25W\",\r\n  \"Sạc không dây\": \"Có\",\r\n  \"Cổng sạc\": \"USB Type-C\",\r\n  \"Kết nối\": \"5G, Wi-Fi 6E, Bluetooth 5.3, NFC\",\r\n  \"SIM\": \"2 Nano SIM hoặc 1 Nano SIM + eSIM\",\r\n  \"Chống nước\": \"IP68\",\r\n  \"Chất liệu\": \"Khung kim loại, mặt kính cường lực\",\r\n  \"Bảo mật\": \"Vân tay trong màn hình, nhận diện khuôn mặt\",\r\n  \"Kích thước\": \"161.0 x 77.0 x 8.2 mm\",\r\n  \"Trọng lượng\": \"213 g\"\r\n}', 'img/series/695f530cf168d_1767854860.webp', 16990000.00, '2026-01-08 06:47:40', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int(11) NOT NULL,
  `series_id` int(11) NOT NULL COMMENT 'Khóa ngoại tới bảng product_series',
  `variant_name` varchar(255) NOT NULL COMMENT 'Tên phiên bản, VD: iPhone 16 Pro Max Plus',
  `screen_size` varchar(50) DEFAULT NULL COMMENT 'Kích thước màn hình, VD: 6.7 inch',
  `specs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Thông số kỹ thuật dạng JSON (chip, camera, pin, etc)' CHECK (json_valid(`specs`)),
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Mảng đường dẫn ảnh của phiên bản này' CHECK (json_valid(`images`)),
  `base_price` decimal(15,2) DEFAULT NULL COMMENT 'Giá cơ bản (có thể không dùng nếu giá theo dung lượng)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_variants`
--

INSERT INTO `product_variants` (`id`, `series_id`, `variant_name`, `screen_size`, `specs`, `images`, `base_price`, `created_at`, `updated_at`) VALUES
(25, 20, 'iPhone 16 Pro Max', 'Super Retina XDR OLED', '{}', '[\"img/products/695e7ac640501_1767799494.png\",\"img/products/695e7ac640806_1767799494.png\",\"img/products/695e7ac640b1a_1767799494.png\"]', NULL, '2026-01-07 15:24:54', NULL),
(26, 21, 'Galaxy Z Flip 7', 'Dynamic AMOLED 2X', '{}', '[\"img/products/695e7f00837be_1767800576.webp\",\"img/products/695e7f0083d4c_1767800576.webp\",\"img/products/695e7f0084137_1767800576.webp\",\"img/products/695e7f008447f_1767800576.webp\"]', NULL, '2026-01-07 15:42:56', NULL),
(29, 24, 'iPhone 17', 'OLED', '{}', '[\"img/products/695e89d745110_1767803351.webp\",\"img/products/695e89d74555a_1767803351.webp\",\"img/products/695e89d74592d_1767803351.webp\"]', NULL, '2026-01-07 16:29:11', NULL),
(30, 25, 'iPhone 17 Pro Max', 'Super Retina XDR OLED', '{}', '[\"img/products/695f2ade688b4_1767844574.webp\",\"img/products/695f2ade68bdf_1767844574.webp\",\"img/products/695f2ade68f6d_1767844574.webp\",\"img/products/695f2ade6934d_1767844574.webp\"]', NULL, '2026-01-08 03:56:14', NULL),
(31, 26, 'OPPO Reno15 5G', 'OLED 6.59 inch, FHD+', '{}', '[\"img/products/695f2dd8e676d_1767845336.webp\",\"img/products/695f2dd8e6a68_1767845336.webp\",\"img/products/695f2dd8e6d69_1767845336.webp\",\"img/products/695f2dd8e70e7_1767845336.webp\"]', NULL, '2026-01-08 04:08:56', NULL),
(33, 28, 'OPPO Find  X9', 'AMOLED, 6.78 inch, 1.5K, 120Hz', '{}', '[\"img/products/695f5208578c2_1767854600.webp\",\"img/products/695f520857bcd_1767854600.webp\",\"img/products/695f520857f35_1767854600.webp\",\"img/products/695f52085830e_1767854600.webp\",\"img/products/695f520858742_1767854600.webp\"]', NULL, '2026-01-08 06:43:20', NULL),
(34, 29, 'Galaxy S25 FE', 'Dynamic AMOLED 2X', '{}', '[\"img/products/695f530cf2f42_1767854860.webp\",\"img/products/695f530cf3648_1767854860.webp\",\"img/products/695f530cf3b02_1767854860.jpg\",\"img/products/695f530cf3e77_1767854860.jpg\"]', NULL, '2026-01-08 06:47:40', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sanpham`
--

CREATE TABLE `sanpham` (
  `MaSP` int(11) NOT NULL,
  `MaLSP` int(11) NOT NULL,
  `TenSP` varchar(70) NOT NULL,
  `DonGia` int(11) NOT NULL,
  `SoLuong` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `HinhAnh` varchar(200) NOT NULL,
  `MaKM` int(11) NOT NULL,
  `ManHinh` varchar(200) NOT NULL,
  `HDH` varchar(200) NOT NULL,
  `CamSau` varchar(200) NOT NULL,
  `CamTruoc` varchar(200) NOT NULL,
  `CPU` varchar(200) NOT NULL,
  `Ram` varchar(200) NOT NULL,
  `Rom` varchar(200) NOT NULL,
  `SDCard` varchar(200) NOT NULL,
  `Pin` varchar(200) NOT NULL,
  `SoSao` int(11) NOT NULL,
  `SoDanhGia` int(11) NOT NULL,
  `TrangThai` int(11) NOT NULL,
  `ThoiGianBaoHanh` int(11) DEFAULT 12 COMMENT 'Thß╗Øi gian bß║úo h├ánh (th├íng)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `series_careplans`
--

CREATE TABLE `series_careplans` (
  `id` int(11) NOT NULL,
  `series_id` int(11) NOT NULL,
  `careplan_id` int(11) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `series_careplans`
--

INSERT INTO `series_careplans` (`id`, `series_id`, `careplan_id`, `sort_order`, `created_at`) VALUES
(29, 20, 7, 0, '2026-01-07 15:24:54'),
(30, 20, 8, 1, '2026-01-07 15:24:54'),
(31, 20, 12, 2, '2026-01-07 15:24:54'),
(32, 20, 10, 3, '2026-01-07 15:24:54'),
(33, 21, 7, 0, '2026-01-07 15:42:56'),
(34, 21, 8, 1, '2026-01-07 15:42:56'),
(35, 21, 12, 2, '2026-01-07 15:42:56'),
(36, 21, 10, 3, '2026-01-07 15:42:56'),
(49, 24, 7, 0, '2026-01-07 16:29:11'),
(50, 24, 8, 1, '2026-01-07 16:29:11'),
(51, 24, 12, 2, '2026-01-07 16:29:11'),
(52, 24, 10, 3, '2026-01-07 16:29:11'),
(53, 25, 7, 0, '2026-01-08 03:56:14'),
(54, 25, 8, 1, '2026-01-08 03:56:14'),
(55, 25, 12, 2, '2026-01-08 03:56:14'),
(56, 25, 10, 3, '2026-01-08 03:56:14'),
(57, 26, 7, 0, '2026-01-08 04:08:56'),
(58, 26, 8, 1, '2026-01-08 04:08:56'),
(59, 26, 12, 2, '2026-01-08 04:08:56'),
(60, 26, 10, 3, '2026-01-08 04:08:56'),
(69, 28, 7, 0, '2026-01-08 06:43:20'),
(70, 28, 8, 1, '2026-01-08 06:43:20'),
(71, 28, 12, 2, '2026-01-08 06:43:20'),
(72, 28, 10, 3, '2026-01-08 06:43:20'),
(73, 29, 7, 0, '2026-01-08 06:47:40'),
(74, 29, 8, 1, '2026-01-08 06:47:40'),
(75, 29, 12, 2, '2026-01-08 06:47:40'),
(76, 29, 10, 3, '2026-01-08 06:47:40');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `series_offers`
--

CREATE TABLE `series_offers` (
  `id` int(11) NOT NULL,
  `series_id` int(11) NOT NULL,
  `badge` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `link_text` varchar(100) DEFAULT NULL,
  `link_url` varchar(500) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `series_offers`
--

INSERT INTO `series_offers` (`id`, `series_id`, `badge`, `title`, `description`, `link_text`, `link_url`, `sort_order`, `is_active`, `created_at`) VALUES
(17, 20, 'ƯU ĐÃI GOOGLE AI PRO', 'Nhận ngay ưu đãi Google AI Pro (Trị giá 3 Triệu)', '', 'Tìm hiểu thêm', '', 1, 1, '2026-01-07 15:24:54'),
(18, 20, 'ƯU ĐÃI ĐỘC QUYỀN', 'Nhập code CTHANG12 giảm trực tiếp đến 1.1 triệu', 'Áp dụng khi mua kèm phụ kiện', '', '', 2, 1, '2026-01-07 15:24:54'),
(19, 20, 'ƯU ĐÃI THANH TOÁN', 'Thanh toán trước giảm 1 triệu', 'Áp dụng thanh toán qua thẻ quốc tế/ ATM/ mã QR/ Ví điện tử', '', '', 3, 1, '2026-01-07 15:24:54'),
(20, 20, '3AM CARE+', 'Ưu đãi lên đến 25%', 'Chăm sóc thiết bị chuẩn chuyên gia Samsung.', 'Tìm hiểu', '', 4, 1, '2026-01-07 15:24:54'),
(21, 20, 'ƯU ĐÃI MUA KÈM', 'Thu cũ đổi mới hỗ trợ 1 triệu', 'Áp dụng cho một số sản phẩm nhất định', '', '', 5, 1, '2026-01-07 15:24:54'),
(27, 21, 'ƯU ĐÃI GOOGLE AI PRO', 'Nhận ngay ưu đãi Google AI Pro (Trị giá 3 Triệu)', '', 'Tìm hiểu thêm', '', 1, 1, '2026-01-07 15:42:56'),
(28, 21, 'ƯU ĐÃI ĐỘC QUYỀN', 'Nhập code CTHANG12 giảm trực tiếp đến 1.1 triệu', 'Áp dụng khi mua kèm phụ kiện', '', '', 2, 1, '2026-01-07 15:42:56'),
(29, 21, 'ƯU ĐÃI THANH TOÁN', 'Thanh toán trước giảm 1 triệu', 'Áp dụng thanh toán qua thẻ quốc tế/ ATM/ mã QR/ Ví điện tử', '', '', 3, 1, '2026-01-07 15:42:56'),
(30, 21, '3AM CARE+', 'Ưu đãi lên đến 25%', 'Chăm sóc thiết bị chuẩn chuyên gia Samsung.', 'Tìm hiểu', '', 4, 1, '2026-01-07 15:42:56'),
(31, 21, 'ƯU ĐÃI MUA KÈM', 'Thu cũ đổi mới hỗ trợ 1 triệu', 'Áp dụng cho một số sản phẩm nhất định', '', '', 5, 1, '2026-01-07 15:42:56'),
(57, 24, 'ƯU ĐÃI GOOGLE AI PRO', 'Nhận ngay ưu đãi Google AI Pro (Trị giá 3 Triệu)', '', 'Tìm hiểu thêm', '', 1, 1, '2026-01-07 16:29:11'),
(58, 24, 'ƯU ĐÃI ĐỘC QUYỀN', 'Nhập code CTHANG12 giảm trực tiếp đến 1.1 triệu', 'Áp dụng khi mua kèm phụ kiện', '', '', 2, 1, '2026-01-07 16:29:11'),
(59, 24, 'ƯU ĐÃI THANH TOÁN', 'Thanh toán trước giảm 1 triệu', 'Áp dụng thanh toán qua thẻ quốc tế/ ATM/ mã QR/ Ví điện tử', '', '', 3, 1, '2026-01-07 16:29:11'),
(60, 24, '3AM CARE+', 'Ưu đãi lên đến 25%', 'Chăm sóc thiết bị chuẩn chuyên gia Samsung.', 'Tìm hiểu', '', 4, 1, '2026-01-07 16:29:11'),
(61, 24, 'ƯU ĐÃI MUA KÈM', 'Thu cũ đổi mới hỗ trợ 1 triệu', 'Áp dụng cho một số sản phẩm nhất định', '', '', 5, 1, '2026-01-07 16:29:11'),
(62, 25, 'ƯU ĐÃI GOOGLE AI PRO', 'Nhận ngay ưu đãi Google AI Pro (Trị giá 3 Triệu)', '', 'Tìm hiểu thêm', '', 1, 1, '2026-01-08 03:56:14'),
(63, 25, 'ƯU ĐÃI ĐỘC QUYỀN', 'Nhập code CTHANG12 giảm trực tiếp đến 1.1 triệu', 'Áp dụng khi mua kèm phụ kiện', '', '', 2, 1, '2026-01-08 03:56:14'),
(64, 25, 'ƯU ĐÃI THANH TOÁN', 'Thanh toán trước giảm 1 triệu', 'Áp dụng thanh toán qua thẻ quốc tế/ ATM/ mã QR/ Ví điện tử', '', '', 3, 1, '2026-01-08 03:56:14'),
(65, 25, '3AM CARE+', 'Ưu đãi lên đến 25%', 'Chăm sóc thiết bị chuẩn chuyên gia Samsung.', 'Tìm hiểu', '', 4, 1, '2026-01-08 03:56:14'),
(66, 25, 'ƯU ĐÃI MUA KÈM', 'Thu cũ đổi mới hỗ trợ 1 triệu', 'Áp dụng cho một số sản phẩm nhất định', '', '', 5, 1, '2026-01-08 03:56:14'),
(67, 26, 'ƯU ĐÃI GOOGLE AI PRO', 'Nhận ngay ưu đãi Google AI Pro (Trị giá 3 Triệu)', '', 'Tìm hiểu thêm', '', 1, 1, '2026-01-08 04:08:56'),
(68, 26, 'ƯU ĐÃI ĐỘC QUYỀN', 'Nhập code CTHANG12 giảm trực tiếp đến 1.1 triệu', 'Áp dụng khi mua kèm phụ kiện', '', '', 2, 1, '2026-01-08 04:08:56'),
(69, 26, 'ƯU ĐÃI THANH TOÁN', 'Thanh toán trước giảm 1 triệu', 'Áp dụng thanh toán qua thẻ quốc tế/ ATM/ mã QR/ Ví điện tử', '', '', 3, 1, '2026-01-08 04:08:56'),
(70, 26, '3AM CARE+', 'Ưu đãi lên đến 25%', 'Chăm sóc thiết bị chuẩn chuyên gia Samsung.', 'Tìm hiểu', '', 4, 1, '2026-01-08 04:08:56'),
(71, 26, 'ƯU ĐÃI MUA KÈM', 'Thu cũ đổi mới hỗ trợ 1 triệu', 'Áp dụng cho một số sản phẩm nhất định', '', '', 5, 1, '2026-01-08 04:08:56'),
(82, 28, 'ƯU ĐÃI GOOGLE AI PRO', 'Nhận ngay ưu đãi Google AI Pro (Trị giá 3 Triệu)', '', 'Tìm hiểu thêm', '', 1, 1, '2026-01-08 06:43:20'),
(83, 28, 'ƯU ĐÃI ĐỘC QUYỀN', 'Nhập code CTHANG12 giảm trực tiếp đến 1.1 triệu', 'Áp dụng khi mua kèm phụ kiện', '', '', 2, 1, '2026-01-08 06:43:20'),
(84, 28, 'ƯU ĐÃI THANH TOÁN', 'Thanh toán trước giảm 1 triệu', 'Áp dụng thanh toán qua thẻ quốc tế/ ATM/ mã QR/ Ví điện tử', '', '', 3, 1, '2026-01-08 06:43:20'),
(85, 28, '3AM CARE+', 'Ưu đãi lên đến 25%', 'Chăm sóc thiết bị chuẩn chuyên gia Samsung.', 'Tìm hiểu', '', 4, 1, '2026-01-08 06:43:20'),
(86, 28, 'ƯU ĐÃI MUA KÈM', 'Thu cũ đổi mới hỗ trợ 1 triệu', 'Áp dụng cho một số sản phẩm nhất định', '', '', 5, 1, '2026-01-08 06:43:20'),
(87, 29, 'ƯU ĐÃI GOOGLE AI PRO', 'Nhận ngay ưu đãi Google AI Pro (Trị giá 3 Triệu)', '', 'Tìm hiểu thêm', '', 1, 1, '2026-01-08 06:47:40'),
(88, 29, 'ƯU ĐÃI ĐỘC QUYỀN', 'Nhập code CTHANG12 giảm trực tiếp đến 1.1 triệu', 'Áp dụng khi mua kèm phụ kiện', '', '', 2, 1, '2026-01-08 06:47:40'),
(89, 29, 'ƯU ĐÃI THANH TOÁN', 'Thanh toán trước giảm 1 triệu', 'Áp dụng thanh toán qua thẻ quốc tế/ ATM/ mã QR/ Ví điện tử', '', '', 3, 1, '2026-01-08 06:47:40'),
(90, 29, '3AM CARE+', 'Ưu đãi lên đến 25%', 'Chăm sóc thiết bị chuẩn chuyên gia Samsung.', 'Tìm hiểu', '', 4, 1, '2026-01-08 06:47:40'),
(91, 29, 'ƯU ĐÃI MUA KÈM', 'Thu cũ đổi mới hỗ trợ 1 triệu', 'Áp dụng cho một số sản phẩm nhất định', '', '', 5, 1, '2026-01-08 06:47:40');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `variant_careplans`
--

CREATE TABLE `variant_careplans` (
  `id` int(11) NOT NULL,
  `variant_id` int(11) NOT NULL,
  `careplan_id` int(11) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `variant_careplans`
--

INSERT INTO `variant_careplans` (`id`, `variant_id`, `careplan_id`, `sort_order`, `created_at`) VALUES
(1, 16, 7, 0, '2025-12-03 23:03:35'),
(2, 16, 10, 1, '2025-12-03 23:03:35');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `variant_colors`
--

CREATE TABLE `variant_colors` (
  `id` int(11) NOT NULL,
  `storage_option_id` int(11) NOT NULL,
  `color_name` varchar(100) NOT NULL,
  `color_code` varchar(20) NOT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `variant_colors`
--

INSERT INTO `variant_colors` (`id`, `storage_option_id`, `color_name`, `color_code`, `stock_quantity`, `created_at`, `updated_at`) VALUES
(52, 38, 'Titan Sa Mạc', '#C2A27A', 12, '2026-01-07 15:24:54', '2026-01-07 15:24:54'),
(53, 38, 'Titan Đen', '#1F1F1F', 15, '2026-01-07 15:24:54', '2026-01-07 15:24:54'),
(54, 38, 'Titan Trắng', '#E9E9E6', 10, '2026-01-07 15:24:54', '2026-01-07 15:24:54'),
(55, 38, 'Titan Tự Nhiên', '#B7B0A3', 8, '2026-01-07 15:24:54', '2026-01-07 15:24:54'),
(56, 39, 'Titan Sa Mạc', '#C2A27A', 10, '2026-01-07 15:24:54', '2026-01-07 15:24:54'),
(57, 39, 'Titan Đen', '#1F1F1F', 12, '2026-01-07 15:24:54', '2026-01-07 15:24:54'),
(58, 39, 'Titan Trắng', '#E9E9E6', 8, '2026-01-07 15:24:54', '2026-01-07 15:24:54'),
(59, 39, 'Titan Tự Nhiên', '#B7B0A3', 6, '2026-01-07 15:24:54', '2026-01-07 15:24:54'),
(60, 40, 'Titan Sa Mạc', '#C2A27A', 6, '2026-01-07 15:24:54', '2026-01-07 15:24:54'),
(61, 40, 'Titan Đen', '#1F1F1F', 8, '2026-01-07 15:24:54', '2026-01-07 15:24:54'),
(62, 40, 'Titan Trắng', '#E9E9E6', 5, '2026-01-07 15:24:54', '2026-01-07 15:24:54'),
(63, 40, 'Titan Tự Nhiên', '#B7B0A3', 4, '2026-01-07 15:24:54', '2026-01-07 15:24:54'),
(64, 41, 'Đỏ san hô', '#E07A6F', 10, '2026-01-07 15:42:56', '2026-01-07 15:42:56'),
(65, 41, 'Xanh bóng', '#2F4F6F', 12, '2026-01-07 15:42:56', '2026-01-07 15:42:56'),
(66, 41, 'Đen tuyền', '#1C1C1C', 15, '2026-01-07 15:42:56', '2026-01-07 15:42:56'),
(67, 42, 'Đỏ san hô', '#E07A6F', 8, '2026-01-07 15:42:56', '2026-01-07 15:42:56'),
(68, 42, 'Xanh bóng', '#2F4F6F', 10, '2026-01-07 15:42:56', '2026-01-07 15:42:56'),
(69, 42, 'Đen tuyền', '#1C1C1C', 12, '2026-01-07 15:42:56', '2026-01-07 15:42:56'),
(110, 51, 'Xanh Lá Xô Thơm', '#A7C8A1', 12, '2026-01-07 16:29:11', '2026-01-07 16:29:11'),
(111, 51, 'Đen', '#1C1C1C', 15, '2026-01-07 16:29:11', '2026-01-07 16:29:11'),
(112, 51, 'Tím Oải Hương', '#C7B6E6', 10, '2026-01-07 16:29:11', '2026-01-07 16:29:11'),
(113, 51, 'Trắng', '#F2F2F2', 10, '2026-01-07 16:29:11', '2026-01-07 16:29:11'),
(114, 51, 'Xanh Lam Khói', '#8FA6C6', 10, '2026-01-07 16:29:11', '2026-01-07 16:29:11'),
(115, 52, 'Trắng', '#F2F2F2', 8, '2026-01-07 16:29:11', '2026-01-07 16:29:11'),
(116, 52, 'Đen', '#1C1C1C', 10, '2026-01-07 16:29:11', '2026-01-07 16:29:11'),
(117, 52, 'Xanh Lam Khói', '#8FA6C6', 9, '2026-01-07 16:29:11', '2026-01-07 16:29:11'),
(118, 52, 'Tím Oải Hương', '#C7B6E6', 7, '2026-01-07 16:29:11', '2026-01-07 16:29:11'),
(119, 52, 'Xanh Lá Xô Thơm', '#A7C8A1', 7, '2026-01-07 16:29:11', '2026-01-07 16:29:11'),
(120, 53, 'Xanh đậm', '#1F2A44', 12, '2026-01-08 03:56:14', '2026-01-08 03:56:14'),
(121, 53, 'Cam Vũ Trụ', '#C96A2C', 10, '2026-01-08 03:56:14', '2026-01-08 03:56:14'),
(122, 53, 'Bạc', '#E5E5E5', 8, '2026-01-08 03:56:14', '2026-01-08 03:56:14'),
(123, 54, 'Xanh đậm', '#1F2A44', 10, '2026-01-08 03:56:14', '2026-01-08 03:56:14'),
(124, 54, 'Cam Vũ Trụ', '#C96A2C', 8, '2026-01-08 03:56:14', '2026-01-08 03:56:14'),
(125, 54, 'Bạc', '#E5E5E5', 6, '2026-01-08 03:56:14', '2026-01-08 03:56:14'),
(126, 55, 'Xanh đậm', '#1F2A44', 8, '2026-01-08 03:56:14', '2026-01-08 03:56:14'),
(127, 55, 'Cam Vũ Trụ', '#C96A2C', 6, '2026-01-08 03:56:14', '2026-01-08 03:56:14'),
(128, 55, 'Bạc', '#E5E5E5', 5, '2026-01-08 03:56:14', '2026-01-08 03:56:14'),
(129, 56, 'Xanh đậm', '#1F2A44', 5, '2026-01-08 03:56:14', '2026-01-08 03:56:14'),
(130, 56, 'Cam Vũ Trụ', '#C96A2C', 4, '2026-01-08 03:56:14', '2026-01-08 03:56:14'),
(131, 56, 'Bạc', '#E5E5E5', 3, '2026-01-08 03:56:14', '2026-01-08 03:56:14'),
(132, 57, 'Trắng', '#F2F2F2', 10, '2026-01-08 04:08:56', '2026-01-08 04:08:56'),
(133, 57, 'Xám đậm', '#3A3F45', 10, '2026-01-08 04:08:56', '2026-01-08 04:08:56'),
(146, 62, 'Xám', '#9E9E9E', 10, '2026-01-08 06:43:20', '2026-01-08 06:43:20'),
(147, 62, 'Đen', '#000000', 10, '2026-01-08 06:43:20', '2026-01-08 06:43:20'),
(148, 63, 'Đen', '#000000', 8, '2026-01-08 06:43:20', '2026-01-08 06:43:20'),
(149, 63, 'Đỏ', '#8B0000', 8, '2026-01-08 06:43:20', '2026-01-08 06:43:20'),
(150, 64, 'Đỏ', '#8B0000', 6, '2026-01-08 06:43:20', '2026-01-08 06:43:20'),
(151, 64, 'Xám', '#9E9E9E', 6, '2026-01-08 06:43:20', '2026-01-08 06:43:20'),
(152, 64, 'Trắng', '#FFFFFF', 6, '2026-01-08 06:43:20', '2026-01-08 06:43:20'),
(153, 65, 'Xanh navy', '#2F3A56', 20, '2026-01-08 06:47:41', '2026-01-08 06:47:41'),
(154, 65, 'Xanh da trời', '#A7C7E7', 18, '2026-01-08 06:47:41', '2026-01-08 06:47:41'),
(155, 65, 'Xám bạc', '#C9CCD6', 15, '2026-01-08 06:47:41', '2026-01-08 06:47:41'),
(156, 66, 'Xanh navy', '#2F3A56', 14, '2026-01-08 06:47:41', '2026-01-08 06:47:41'),
(157, 66, 'Xanh da trời', '#A7C7E7', 12, '2026-01-08 06:47:41', '2026-01-08 06:47:41'),
(158, 66, 'Xám bạc', '#C9CCD6', 10, '2026-01-08 06:47:41', '2026-01-08 06:47:41');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `variant_promotions`
--

CREATE TABLE `variant_promotions` (
  `id` int(11) NOT NULL,
  `variant_id` int(11) NOT NULL COMMENT 'Khóa ngoại tới bảng product_variants',
  `promotion_type` varchar(50) NOT NULL COMMENT 'Loại KM: trade_in, gift, discount, bundle',
  `description` text NOT NULL COMMENT 'Mô tả khuyến mãi để hiển thị',
  `value` decimal(15,2) DEFAULT NULL COMMENT 'Giá trị khuyến mãi (nếu có)',
  `valid_from` date DEFAULT NULL COMMENT 'Ngày bắt đầu KM',
  `valid_to` date DEFAULT NULL COMMENT 'Ngày kết thúc KM',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `variant_promotions`
--

INSERT INTO `variant_promotions` (`id`, `variant_id`, `promotion_type`, `description`, `value`, `valid_from`, `valid_to`, `created_at`, `updated_at`) VALUES
(15, 25, 'TangPhuKien', 'Tặng ốp lưng chính hãng trị giá 500.000đ', 500000.00, NULL, NULL, '2026-01-07 15:24:54', NULL),
(16, 26, 'TangPhuKien', 'Tặng ốp lưng chính hãng trị giá 500.000đ', 500000.00, NULL, NULL, '2026-01-07 15:42:56', NULL),
(20, 29, 'GiamGia', 'Giảm 10% khi thanh toán qua VNPay', NULL, NULL, NULL, '2026-01-07 16:29:11', NULL),
(21, 30, 'TangPhuKien', 'Tặng ốp lưng chính hãng trị giá 500.000đ', 500000.00, NULL, NULL, '2026-01-08 03:56:14', NULL),
(22, 31, 'TraGop', 'Trả góp 0% lãi suất qua thẻ tín dụng', NULL, NULL, NULL, '2026-01-08 04:08:56', NULL),
(25, 33, 'BaoHanh', 'Bảo hành mở rộng 12 tháng', NULL, NULL, NULL, '2026-01-08 06:43:20', NULL),
(26, 34, 'BaoHanh', 'Bảo hành mở rộng 12 tháng', NULL, NULL, NULL, '2026-01-08 06:47:41', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `variant_storage_options`
--

CREATE TABLE `variant_storage_options` (
  `id` int(11) NOT NULL,
  `variant_id` int(11) NOT NULL COMMENT 'Khóa ngoại tới bảng product_variants',
  `storage` varchar(50) NOT NULL COMMENT 'Dung lượng bộ nhớ trong, VD: 128GB',
  `ram` varchar(50) DEFAULT NULL COMMENT 'Dung lượng RAM, VD: 8GB',
  `price` decimal(15,2) NOT NULL COMMENT 'Giá bán (mua trọn gói)',
  `installment_price` decimal(15,2) DEFAULT NULL COMMENT 'Giá trả góp mỗi tháng',
  `installment_months` int(11) DEFAULT 12 COMMENT 'Số tháng trả góp',
  `interest_rate` decimal(5,2) DEFAULT 0.00 COMMENT 'Lãi suất (%), VD: 0 hoặc 5.5',
  `stock_quantity` int(11) DEFAULT 0 COMMENT 'Số lượng tồn kho',
  `promotion_text` varchar(255) DEFAULT NULL COMMENT 'Text khuyến mãi, VD: Ưu đãi 3 triệu',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `variant_storage_options`
--

INSERT INTO `variant_storage_options` (`id`, `variant_id`, `storage`, `ram`, `price`, `installment_price`, `installment_months`, `interest_rate`, `stock_quantity`, `promotion_text`, `created_at`, `updated_at`) VALUES
(38, 25, '256GB', '8GB', 31590000.00, 3000000.00, 12, 0.00, 0, NULL, '2026-01-07 15:24:54', NULL),
(39, 25, '512GB', '8GB', 37990000.00, 4000000.00, 12, 0.00, 0, NULL, '2026-01-07 15:24:54', NULL),
(40, 25, '1TB', '8GB', 42990000.00, 5000000.00, 12, 0.00, 0, NULL, '2026-01-07 15:24:54', NULL),
(41, 26, '256GB', '12GB', 24090000.00, 2500000.00, 12, 0.00, 0, NULL, '2026-01-07 15:42:56', NULL),
(42, 26, '512GB', '12GB', 27090000.00, 3000000.00, 12, 0.00, 0, NULL, '2026-01-07 15:42:56', NULL),
(51, 29, '256GB', '8GB', 24990000.00, 2500000.00, 12, 0.00, 0, NULL, '2026-01-07 16:29:11', NULL),
(52, 29, '512GB', '8GB', 30490000.00, 3000000.00, 12, 0.00, 0, NULL, '2026-01-07 16:29:11', NULL),
(53, 30, '256GB', '8GB', 37690000.00, 4000000.00, 12, 0.00, 0, NULL, '2026-01-08 03:56:14', NULL),
(54, 30, '512GB', '8GB', 43990000.00, 5000000.00, 12, 0.00, 0, NULL, '2026-01-08 03:56:14', NULL),
(55, 30, '1TB', '8GB', 50990000.00, 6000000.00, 12, 0.00, 0, NULL, '2026-01-08 03:56:14', NULL),
(56, 30, '2TB', '8GB', 62990000.00, 8000000.00, 12, 0.00, 0, NULL, '2026-01-08 03:56:14', NULL),
(57, 31, '256GB', '12GB', 16990000.00, 2000000.00, 12, 0.00, 0, NULL, '2026-01-08 04:08:56', NULL),
(62, 33, '256GB', '12GB', 22990000.00, 1908000.00, 12, 0.00, 0, NULL, '2026-01-08 06:43:20', NULL),
(63, 33, '512GB', '16GB', 26990000.00, 2250000.00, 12, 0.00, 0, NULL, '2026-01-08 06:43:20', NULL),
(64, 33, '512GB', '16GB', 32990000.00, 2750000.00, 12, 0.00, 0, NULL, '2026-01-08 06:43:20', NULL),
(65, 34, '128GB', '8GB', 16990000.00, 1500000.00, 12, 0.00, 0, NULL, '2026-01-08 06:47:41', NULL),
(66, 34, '256GB', '8GB', 18990000.00, 1800000.00, 12, 0.00, 0, NULL, '2026-01-08 06:47:41', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vouchers`
--

CREATE TABLE `vouchers` (
  `MaVoucher` int(11) NOT NULL,
  `Code` varchar(50) NOT NULL,
  `TenVoucher` varchar(255) NOT NULL,
  `MoTa` text DEFAULT NULL,
  `LoaiGiam` enum('percent','fixed') NOT NULL DEFAULT 'percent' COMMENT 'percent: gi???m %, fixed: gi???m s??? ti???n c??? ?????nh',
  `GiaTri` decimal(15,2) NOT NULL COMMENT 'Gi?? tr??? gi???m (% ho???c VND)',
  `GiamToiDa` decimal(15,2) DEFAULT NULL COMMENT 'Gi???m t???i ??a (ch??? ??p d???ng cho lo???i percent)',
  `DonToiThieu` decimal(15,2) DEFAULT 0.00 COMMENT '????n h??ng t???i thi???u ????? ??p d???ng',
  `SoLuong` int(11) DEFAULT NULL COMMENT 'S??? l?????ng voucher (NULL = kh??ng gi???i h???n)',
  `DaSuDung` int(11) DEFAULT 0 COMMENT 'S??? l???n ???? s??? d???ng',
  `NgayBatDau` datetime NOT NULL,
  `NgayKetThuc` datetime NOT NULL,
  `TrangThai` tinyint(1) DEFAULT 1 COMMENT '1: Ho???t ?????ng, 0: T???m d???ng',
  `NgayTao` datetime DEFAULT current_timestamp(),
  `NgayCapNhat` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `vouchers`
--

INSERT INTO `vouchers` (`MaVoucher`, `Code`, `TenVoucher`, `MoTa`, `LoaiGiam`, `GiaTri`, `GiamToiDa`, `DonToiThieu`, `SoLuong`, `DaSuDung`, `NgayBatDau`, `NgayKetThuc`, `TrangThai`, `NgayTao`, `NgayCapNhat`) VALUES
(1, 'WELCOME10', 'Chào mừng thành viên mới', 'Giảm 10% cho đơn hàng đầu tiên', 'percent', 10.00, 500000.00, 1000000.00, 100, 0, '2023-12-31 17:00:00', '2026-01-17 16:59:00', 1, '2025-12-28 13:59:14', '2025-12-28 14:00:51'),
(2, 'SALE99', 'Giảm 99%', 'Giảm ngay 50k cho đơn hàng từ 500k', 'percent', 99.00, NULL, 500000.00, 200, 1, '2023-12-31 10:00:00', '2026-01-17 09:59:00', 1, '2025-12-28 13:59:14', '2026-01-08 08:48:30'),
(4, 'FREESHIP', 'Miễn phí vận chuyển', 'Giảm 30K phí ship', 'fixed', 30000.00, NULL, 300000.00, NULL, 0, '2023-12-31 17:00:00', '2026-01-17 16:59:00', 1, '2025-12-28 13:59:14', '2025-12-28 14:05:54');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `careplan`
--
ALTER TABLE `careplan`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `chitiethoadon`
--
ALTER TABLE `chitiethoadon`
  ADD KEY `MaHD` (`MaHD`),
  ADD KEY `MaSP` (`MaSP`);

--
-- Chỉ mục cho bảng `danhgia`
--
ALTER TABLE `danhgia`
  ADD KEY `MaSP` (`MaSP`);

--
-- Chỉ mục cho bảng `hoadon`
--
ALTER TABLE `hoadon`
  ADD PRIMARY KEY (`MaHD`),
  ADD KEY `MaKH` (`MaND`);

--
-- Chỉ mục cho bảng `khuyenmai`
--
ALTER TABLE `khuyenmai`
  ADD PRIMARY KEY (`MaKM`);

--
-- Chỉ mục cho bảng `loaisanpham`
--
ALTER TABLE `loaisanpham`
  ADD PRIMARY KEY (`MaLSP`);

--
-- Chỉ mục cho bảng `nguoidung`
--
ALTER TABLE `nguoidung`
  ADD PRIMARY KEY (`MaND`),
  ADD KEY `MaQuyen` (`MaQuyen`),
  ADD KEY `idx_google_id` (`google_id`);

--
-- Chỉ mục cho bảng `phanquyen`
--
ALTER TABLE `phanquyen`
  ADD PRIMARY KEY (`MaQuyen`);

--
-- Chỉ mục cho bảng `phieusuachua`
--
ALTER TABLE `phieusuachua`
  ADD PRIMARY KEY (`MaPhieu`),
  ADD KEY `idx_mand` (`MaND`),
  ADD KEY `idx_masp` (`MaSP`),
  ADD KEY `idx_trangthai` (`TrangThai`),
  ADD KEY `idx_ngaytao` (`NgayTao`);

--
-- Chỉ mục cho bảng `product_series`
--
ALTER TABLE `product_series`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_series_name` (`series_name`),
  ADD KEY `idx_series_created` (`created_at`);

--
-- Chỉ mục cho bảng `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_variant_series` (`series_id`),
  ADD KEY `idx_variant_name` (`variant_name`),
  ADD KEY `idx_variant_created` (`created_at`);

--
-- Chỉ mục cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  ADD PRIMARY KEY (`MaSP`),
  ADD KEY `LoaiSP` (`MaLSP`),
  ADD KEY `MaKM` (`MaKM`);

--
-- Chỉ mục cho bảng `series_careplans`
--
ALTER TABLE `series_careplans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_series_careplan` (`series_id`,`careplan_id`),
  ADD KEY `careplan_id` (`careplan_id`);

--
-- Chỉ mục cho bảng `series_offers`
--
ALTER TABLE `series_offers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `series_id` (`series_id`);

--
-- Chỉ mục cho bảng `variant_careplans`
--
ALTER TABLE `variant_careplans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `careplan_id` (`careplan_id`);

--
-- Chỉ mục cho bảng `variant_colors`
--
ALTER TABLE `variant_colors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_color_storage` (`storage_option_id`),
  ADD KEY `idx_color_name` (`color_name`),
  ADD KEY `idx_color_stock` (`stock_quantity`);

--
-- Chỉ mục cho bảng `variant_promotions`
--
ALTER TABLE `variant_promotions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_promo_variant` (`variant_id`),
  ADD KEY `idx_promo_type` (`promotion_type`),
  ADD KEY `idx_promotion_dates` (`valid_from`,`valid_to`);

--
-- Chỉ mục cho bảng `variant_storage_options`
--
ALTER TABLE `variant_storage_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_storage_variant` (`variant_id`),
  ADD KEY `idx_storage` (`storage`),
  ADD KEY `idx_price` (`price`),
  ADD KEY `idx_stock_quantity` (`stock_quantity`);

--
-- Chỉ mục cho bảng `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`MaVoucher`),
  ADD UNIQUE KEY `Code` (`Code`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `careplan`
--
ALTER TABLE `careplan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `hoadon`
--
ALTER TABLE `hoadon`
  MODIFY `MaHD` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT cho bảng `khuyenmai`
--
ALTER TABLE `khuyenmai`
  MODIFY `MaKM` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `loaisanpham`
--
ALTER TABLE `loaisanpham`
  MODIFY `MaLSP` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `nguoidung`
--
ALTER TABLE `nguoidung`
  MODIFY `MaND` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `phanquyen`
--
ALTER TABLE `phanquyen`
  MODIFY `MaQuyen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `phieusuachua`
--
ALTER TABLE `phieusuachua`
  MODIFY `MaPhieu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `product_series`
--
ALTER TABLE `product_series`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT cho bảng `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  MODIFY `MaSP` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT cho bảng `series_careplans`
--
ALTER TABLE `series_careplans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT cho bảng `series_offers`
--
ALTER TABLE `series_offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT cho bảng `variant_careplans`
--
ALTER TABLE `variant_careplans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `variant_colors`
--
ALTER TABLE `variant_colors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=159;

--
-- AUTO_INCREMENT cho bảng `variant_promotions`
--
ALTER TABLE `variant_promotions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT cho bảng `variant_storage_options`
--
ALTER TABLE `variant_storage_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT cho bảng `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `MaVoucher` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `chitiethoadon`
--
ALTER TABLE `chitiethoadon`
  ADD CONSTRAINT `chitiethoadon_ibfk_2` FOREIGN KEY (`MaHD`) REFERENCES `hoadon` (`MaHD`);

--
-- Các ràng buộc cho bảng `danhgia`
--
ALTER TABLE `danhgia`
  ADD CONSTRAINT `danhgia_ibfk_1` FOREIGN KEY (`MaSP`) REFERENCES `sanpham` (`MaSP`);

--
-- Các ràng buộc cho bảng `hoadon`
--
ALTER TABLE `hoadon`
  ADD CONSTRAINT `hoadon_ibfk_1` FOREIGN KEY (`MaND`) REFERENCES `nguoidung` (`MaND`);

--
-- Các ràng buộc cho bảng `nguoidung`
--
ALTER TABLE `nguoidung`
  ADD CONSTRAINT `nguoidung_ibfk_1` FOREIGN KEY (`MaQuyen`) REFERENCES `phanquyen` (`MaQuyen`);

--
-- Các ràng buộc cho bảng `phieusuachua`
--
ALTER TABLE `phieusuachua`
  ADD CONSTRAINT `fk_phieusuachua_nguoidung` FOREIGN KEY (`MaND`) REFERENCES `nguoidung` (`MaND`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `fk_variant_series` FOREIGN KEY (`series_id`) REFERENCES `product_series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  ADD CONSTRAINT `sanpham_ibfk_1` FOREIGN KEY (`MaLSP`) REFERENCES `loaisanpham` (`MaLSP`),
  ADD CONSTRAINT `sanpham_ibfk_2` FOREIGN KEY (`MaKM`) REFERENCES `khuyenmai` (`MaKM`);

--
-- Các ràng buộc cho bảng `series_careplans`
--
ALTER TABLE `series_careplans`
  ADD CONSTRAINT `series_careplans_ibfk_1` FOREIGN KEY (`series_id`) REFERENCES `product_series` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `series_careplans_ibfk_2` FOREIGN KEY (`careplan_id`) REFERENCES `careplan` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `series_offers`
--
ALTER TABLE `series_offers`
  ADD CONSTRAINT `series_offers_ibfk_1` FOREIGN KEY (`series_id`) REFERENCES `product_series` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `variant_careplans`
--
ALTER TABLE `variant_careplans`
  ADD CONSTRAINT `variant_careplans_ibfk_1` FOREIGN KEY (`careplan_id`) REFERENCES `careplan` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `variant_colors`
--
ALTER TABLE `variant_colors`
  ADD CONSTRAINT `fk_color_storage` FOREIGN KEY (`storage_option_id`) REFERENCES `variant_storage_options` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `variant_promotions`
--
ALTER TABLE `variant_promotions`
  ADD CONSTRAINT `fk_promo_variant` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `variant_storage_options`
--
ALTER TABLE `variant_storage_options`
  ADD CONSTRAINT `fk_storage_variant` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
