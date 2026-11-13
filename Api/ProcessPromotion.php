<?php
	require_once('../Backend/Database/DbClasses.php');

	if(!isset($_POST['request']) && !isset($_GET['request'])) die(null);

	switch ($_POST['request']) {
    	// lấy tất cả khuyến mãi
    	case 'getall':
				$dskm = (new KhuyenMaiBUS())->select_all();
		    	die (json_encode($dskm));
    		break;

        // lấy khuyến mãi theo id
        case 'theoID':
                $km = (new KhuyenMaiBUS())->select_by_id('*', $_POST['id']);
                die (json_encode($km));
            break;
    	default:
    		# code...
    		break;
    }

?>
