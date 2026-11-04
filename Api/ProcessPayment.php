<?php
	
	require_once ("../Backend/Database/DbClasses.php");
	session_start();
	
	// Set timezone Việt Nam
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	
	// Set JSON header
	header('Content-Type: application/json; charset=utf-8');

	// Log để debug
	function logData($data, $filename = 'checkout_log.txt') {
		$logDir = __DIR__ . '/../logs/';
		if (!is_dir($logDir)) mkdir($logDir, 0777, true);
		file_put_contents($logDir . $filename, date('Y-m-d H:i:s') . " - " . print_r($data, true) . "\n", FILE_APPEND);
	}

	// Xử lý đặt hàng từ trang checkout mới
	if (isset($_POST['email']) && isset($_POST['hovaten']) && isset($_POST['listProduct'])) {
		
		$email = $_POST['email'];
		$hovaten = $_POST['hovaten'];
		$sdt = $_POST['sdt'];
		$diachi = $_POST['diachi'];
		$ghichu = isset($_POST['ghichu']) ? $_POST['ghichu'] : '';
		$paymentMethod = isset($_POST['paymentMethod']) ? $_POST['paymentMethod'] : 'cod';
		$listProduct = json_decode($_POST['listProduct'], true);
		
		// Log dữ liệu nhận được
		logData(['POST' => $_POST, 'listProduct' => $listProduct]);
		
		// Lấy mã người dùng từ session
		$maNguoiDung = isset($_SESSION['currentUser']) ? $_SESSION['currentUser']['MaND'] : 0;
		
		// Tính tổng tiền (bao gồm care plan)
		$tongTien = 0;
		foreach ($listProduct as $sp) {
			$donGia = 0;
			// Kiểm tra nhiều trường hợp giá
			if (isset($sp['display_price'])) {
				$donGia = floatval($sp['display_price']);
			} elseif (isset($sp['finalPrice'])) {
				$donGia = floatval($sp['finalPrice']);
			} elseif (isset($sp['price'])) {
				$donGia = floatval($sp['price']);
			} elseif (isset($sp['DonGia'])) {
				$donGia = floatval($sp['DonGia']);
			}
			
			// Cộng thêm care plan nếu có
			if (isset($sp['seriesConfig']) && isset($sp['seriesConfig']['carePlanPrice'])) {
				$donGia += floatval($sp['seriesConfig']['carePlanPrice']);
			}
			
			$soLuong = isset($sp['SoLuongTrongGio']) ? intval($sp['SoLuongTrongGio']) : 1;
			$tongTien += $donGia * $soLuong;
		}
		
		// Xử lý voucher discount
		$voucherCode = isset($_POST['voucherCode']) ? $_POST['voucherCode'] : '';
		$voucherDiscount = isset($_POST['voucherDiscount']) ? floatval($_POST['voucherDiscount']) : 0;
		
		// Trừ voucher discount khỏi tổng tiền
		if ($voucherDiscount > 0) {
			$tongTien = $tongTien - $voucherDiscount;
			if ($tongTien < 0) $tongTien = 0;
		}
		
		// Thêm hóa đơn
		$hoadonBUS = new HoaDonBUS();
		$chitiethdBUS = new ChiTietHoaDonBUS();
		
		// MoMo = đã thanh toán, COD = chưa thanh toán
		$daThanhToan = ($paymentMethod === 'momo') ? 1 : 0;
		
		$hoadonBUS->add_new(array(
			"MaND" => $maNguoiDung,
			"NgayLap" => date('Y-m-d H:i:s'),
			"NguoiNhan" => $hovaten,
			"SDT" => $sdt,
			"DiaChi" => $diachi,
			"PhuongThucTT" => $paymentMethod,
			"DaThanhToan" => $daThanhToan,
			"TongTien" => $tongTien,
			"TrangThai" => 1
		));
		
		// Lấy mã hóa đơn vừa tạo
		$hoadonMaxID = $hoadonBUS->get_list("SELECT * FROM hoadon ORDER BY MaHD DESC LIMIT 0, 1");
		$mahd = $hoadonMaxID[0]["MaHD"];
		
		// Thêm chi tiết hóa đơn
		foreach ($listProduct as $sp) {
			$maSP = 0;
			$donGia = 0;
			$soLuong = isset($sp['SoLuongTrongGio']) ? intval($sp['SoLuongTrongGio']) : 1;
			$tenSP = '';
			$hinhAnh = '';
			$seriesConfig = null;
			
			// Kiểm tra xem có phải sản phẩm series không
			$isSeries = isset($sp['isSeries']) && $sp['isSeries'];
			
			if ($isSeries) {
				// Sản phẩm series - MaSP = 0, lưu thông tin vào các trường khác
				$maSP = 0;
				$tenSP = isset($sp['TenSP']) ? $sp['TenSP'] : (isset($sp['seriesName']) ? $sp['seriesName'] : '');
				if (isset($sp['configName'])) {
					$tenSP .= ' - ' . $sp['configName'];
				}
				if (isset($sp['colorName'])) {
					$tenSP .= ' - ' . $sp['colorName'];
				}
				// Thêm thông tin care plan vào tên nếu có
				if (isset($sp['seriesConfig']) && isset($sp['seriesConfig']['carePlanId']) && $sp['seriesConfig']['carePlanId'] > 0) {
					$carePlanPrice = isset($sp['seriesConfig']['carePlanPrice']) ? floatval($sp['seriesConfig']['carePlanPrice']) : 0;
					$tenSP .= ' + 3AM Care+ (+' . number_format($carePlanPrice, 0, ',', '.') . ' ₫)';
				}
				$hinhAnh = isset($sp['HinhAnh']) ? $sp['HinhAnh'] : (isset($sp['image']) ? $sp['image'] : '');
				$seriesConfig = isset($sp['seriesConfig']) ? json_encode($sp['seriesConfig']) : null;
			} else {
				// Sản phẩm thường
				$maSP = isset($sp['MaSP']) ? intval($sp['MaSP']) : 0;
				$tenSP = isset($sp['TenSP']) ? $sp['TenSP'] : '';
				$hinhAnh = isset($sp['HinhAnh']) ? $sp['HinhAnh'] : '';
			}
			
			// Lấy đơn giá
			if (isset($sp['display_price'])) {
				$donGia = floatval($sp['display_price']);
			} elseif (isset($sp['finalPrice'])) {
				$donGia = floatval($sp['finalPrice']);
			} elseif (isset($sp['price'])) {
				$donGia = floatval($sp['price']);
			} elseif (isset($sp['DonGia'])) {
				$donGia = floatval($sp['DonGia']);
			}
			
			// Cộng thêm care plan vào đơn giá nếu có
			if (isset($sp['seriesConfig']) && isset($sp['seriesConfig']['carePlanPrice'])) {
				$donGia += floatval($sp['seriesConfig']['carePlanPrice']);
			}
			
			logData(['Saving product' => ['maSP' => $maSP, 'tenSP' => $tenSP, 'donGia' => $donGia, 'soLuong' => $soLuong, 'hinhAnh' => $hinhAnh, 'isSeries' => $isSeries]]);
			
			// Insert trực tiếp với các trường mới
			$db = new DB_driver();
			$insertData = array(
				'MaHD' => $mahd,
				'MaSP' => $maSP,
				'SoLuong' => $soLuong,
				'DonGia' => $donGia,
				'TenSP' => $tenSP,
				'HinhAnh' => $hinhAnh,
				'SeriesConfig' => $seriesConfig
			);
			$result = $db->insert('chitiethoadon', $insertData);
			logData(['Insert result' => $result, 'Data' => $insertData]);
		}
		
		die(json_encode(['success' => true, 'message' => 'Đặt hàng thành công', 'orderId' => $mahd]));
	}

	// Không còn xử lý sản phẩm cũ
	if(!isset($_POST['request']) && !isset($_GET['request'])) die();
?>
