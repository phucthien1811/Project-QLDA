<?php
    require_once('../Backend/Database/DbClasses.php');
    session_start();
    
    // Set timezone Việt Nam
    date_default_timezone_set('Asia/Ho_Chi_Minh');

    // Hàm lấy text và màu trạng thái
    function getTrangThaiInfo($trangThai) {
        switch($trangThai) {
            case 1:
                return ['text' => 'Đang xử lý', 'color' => '#f0ad4e', 'class' => 'pending'];
            case 2:
                return ['text' => 'Đã xác nhận', 'color' => '#5bc0de', 'class' => 'confirmed'];
            case 3:
                return ['text' => 'Đã lên đơn', 'color' => '#9b59b6', 'class' => 'processing'];
            case 4:
                return ['text' => 'Đang giao', 'color' => '#3498db', 'class' => 'shipping'];
            case 5:
                return ['text' => 'Đã giao thành công', 'color' => '#27ae60', 'class' => 'completed'];
            case 0:
                return ['text' => 'Đã hủy', 'color' => '#e74c3c', 'class' => 'cancelled'];
            default:
                return ['text' => 'Không xác định', 'color' => '#95a5a6', 'class' => 'unknown'];
        }
    }

    if(!isset($_POST['request']) && !isset($_GET['request'])) die(null);

    $request = isset($_POST['request']) ? $_POST['request'] : $_GET['request'];

    switch ($request) {
        case 'getmyorders':
            header('Content-Type: application/json; charset=utf-8');
            
            if (!isset($_SESSION['currentUser'])) {
                die(json_encode(['success' => false, 'message' => 'Chưa đăng nhập']));
            }
            
            $manguoidung = $_SESSION['currentUser']['MaND'];
            
            $sql = "SELECT * FROM hoadon WHERE MaND = $manguoidung ORDER BY NgayLap DESC";
            $dsdh = (new DB_driver())->get_list($sql);
            
            $result = [];
            foreach($dsdh as $dh) {
                // Lấy chi tiết đơn hàng - chỉ dùng series products
                $sqlChitiet = "SELECT ct.* FROM chitiethoadon ct WHERE ct.MaHD = " . $dh['MaHD'];
                $chitiet = (new DB_driver())->get_list($sqlChitiet);
                
                $sanphamList = [];
                foreach($chitiet as $ct) {
                    $sanphamList[] = [
                        'MaSP' => $ct['MaSP'],
                        'TenSP' => $ct['TenSP'] ?? 'Sản phẩm',
                        'HinhAnh' => $ct['HinhAnh'] ?? '',
                        'SoLuong' => $ct['SoLuong'],
                        'DonGia' => $ct['DonGia'],
                        'SeriesConfig' => $ct['SeriesConfig']
                    ];
                }
                
                $trangThaiInfo = getTrangThaiInfo($dh['TrangThai']);
                
                // Trạng thái thanh toán
                $daThanhToan = isset($dh['DaThanhToan']) ? intval($dh['DaThanhToan']) : 0;
                $thanhToanText = $daThanhToan ? 'Đã thanh toán' : 'Chưa thanh toán';
                $thanhToanClass = $daThanhToan ? 'paid' : 'unpaid';
                
                $result[] = [
                    'MaHD' => $dh['MaHD'],
                    'NgayLap' => $dh['NgayLap'],
                    'NguoiNhan' => $dh['NguoiNhan'],
                    'SDT' => $dh['SDT'],
                    'DiaChi' => $dh['DiaChi'],
                    'PhuongThucTT' => $dh['PhuongThucTT'],
                    'DaThanhToan' => $daThanhToan,
                    'ThanhToanText' => $thanhToanText,
                    'ThanhToanClass' => $thanhToanClass,
                    'TongTien' => $dh['TongTien'],
                    'TrangThai' => $dh['TrangThai'],
                    'TrangThaiText' => $trangThaiInfo['text'],
                    'TrangThaiColor' => $trangThaiInfo['color'],
                    'TrangThaiClass' => $trangThaiInfo['class'],
                    'SanPham' => $sanphamList
                ];
            }
            
            die(json_encode(['success' => true, 'data' => $result]));
            break;

    	case 'getall':
				$donhang = (new HoaDonBUS())->select_all();
                $ctdonhang = (new ChiTietHoaDonBUS())->select_all();
		    	die (json_encode($donhang));
    		break;

		case 'getdetail':
				$mahd = $_POST['mahd'];
				// Lấy chi tiết đơn hàng (không cần join với sanpham cũ)
				$sql = "SELECT ct.* FROM chitiethoadon ct WHERE ct.MaHD = $mahd";
				$result = (new DB_driver())->get_list($sql);
				die (json_encode($result));
			break;

		case 'getfulldetail':
				$mahd = $_POST['mahd'];
				
				// Lấy thông tin hóa đơn
				$hoadon = (new HoaDonBUS())->select_by_id('*', $mahd);
				
				// Lấy chi tiết sản phẩm (không cần join với sanpham cũ)
				$sql = "SELECT ct.* FROM chitiethoadon ct WHERE ct.MaHD = $mahd";
				$chitiet = (new DB_driver())->get_list($sql);
				
				$result = array(
					"hoadon" => $hoadon,
					"chitiet" => $chitiet
				);
				
				die (json_encode($result));
			break;

		case 'updatestatus':
				$mahd = $_POST['mahd'];
				$trangthai = $_POST['trangthai'];
				
				$hd = (new HoaDonBUS())->select_by_id('*', $mahd);
				$hd['TrangThai'] = $trangthai;
				
				$result = (new HoaDonBUS())->update_by_id($hd, $mahd);
				die (json_encode($result));
			break;

		default:
	    		# code...
	    		break;
    }
?>
