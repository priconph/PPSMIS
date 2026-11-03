<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	
	if(is_ajax()) {
		if(isset($_POST["action"]) && !empty($_POST["action"])) {
			$action = $_POST["action"];
			switch($action) {
				case "session_check" 			: session_check(); 				break;
				case "rl_get_data_for_update"	: rl_get_data_for_update(); 	break;
				case "rl_update_data"			: rl_update_data(); 			break;
				case "get_qrcode_data"			: get_qrcode_data(); 			break; // 01042024
				case "get_qrcode_issuance_data"	: get_qrcode_issuance_data(); 	break; // 01042024


			}
		}
	}
	
	function is_ajax() {
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}
	
	function session_check(){
		session_start();
		$return = $_POST;
		$result = "";
		$return["username"] = $_SESSION['username'];
		$return["result"] = json_encode($result);
		echo json_encode($return);
	}

	function rl_get_data_for_update(){		
		require_once( './ppswarehouse-oop.php');
		$result = PPSW::getInstance()->rl_get_data_for_update($_POST['pkid']);
		if($row = mysqli_fetch_array($result)){
			$return['Price']		 	= $row['Price'];
			$return['InvoiceNo'] 		= $row['InvoiceNo'];
			$return['TransferSlipNo']	= $row['TransferSlipNo'];
			$return['Remarks'] 			= $row['Remarks'];
			$return['inspection_class'] = $row['inspection_class'];
			$return['Lot_number'] 		= $row['Lot_number'];
		}
		$return["result"] = json_encode($result);
		echo json_encode($return);
	}

	function rl_update_data(){
		require_once( './ppswarehouse-oop.php');
		$result = PPSW::getInstance()->rl_update_data($_POST['txt_rl_pkid'],$_POST['txt_rl_price'],$_POST['txt_rl_invoice_no'],$_POST['txt_rl_transfer_slip_no'],$_POST['txt_rl_remarks'],$_POST['txt_rl_inspection_class'],$_POST['txt_rl_lot_no']);
		$return["result"] = json_encode($result);
		echo json_encode($return);
	}

	function get_qrcode_data(){
		require_once('ppswarehouse-oop.php');
		include './format/phpqrcode/qrlib.php'; 
	
		$return = $_POST;
		$result = "";		
		$text = "";		
		$text = "";
		$result = PPSW::getInstance()->get_qrcode_data($return['pkid']);
		if($row = mysqli_fetch_array($result)){			
			$return['pkid'] 			= $row['pkid'];
			$return['InvoiceNo'] 		= $row['InvoiceNo'];
			$return['Date'] 			= $row['Date'];
			$return['PartNumber'] 		= $row['PartNumber'];
			$return['MaterialType'] 	= $row['MaterialType'];
			$return['Model'] 			= $row['Model'];
			$return['Lot_number'] 		= $row['Lot_number']; // new lot #
			$return['Remarks'] 			= $row['Remarks']; // old lot #
			$text = $row['Lot_number'];
			// print_r($return);
			// exit;

			ob_start(); 
				QRcode::png($text);
				$result_qr_content_in_png = ob_get_contents();
			ob_end_clean();

			header("Content-type: text/html");
			
			$result_qr_content_in_base64 =  base64_encode($result_qr_content_in_png);

			$return['qrcode']			= $result_qr_content_in_base64; 
		}

		$return["result"] = json_encode($result);
		echo json_encode($return);
	}

	function get_qrcode_issuance_data(){
		require_once('ppswarehouse-oop.php');
		include './format/phpqrcode/qrlib.php'; 
	
		$return = $_POST;
		$result = "";		
		$text = "";		
		$text = "";
		$result = PPSW::getInstance()->get_qrcode_issuance_data($return['pkid']);
		$ctr = 0;
		while($row = mysqli_fetch_array($result)){			
			$return['fk_issuance'][$ctr]	= $row['fk_issuance'];
			$return['im_remarks'][$ctr] 	= $row['im_remarks']; // Lot #
			$return['issuanceqtym'][$ctr] 	= $row['issuanceqtym'];
			$return['partcode'][$ctr] 		= $row['partcode'];
			$return['partname'][$ctr] 		= $row['partname'];

			$text = $row['im_remarks']." | ".$row['issuanceqtym']." | ".$row['partcode']." | ".$row['partname']; // Lot #
			// print_r($return);
			// exit;

			

			ob_start(); 
				QRcode::png($text);
				$result_qr_content_in_png = ob_get_contents();
			ob_end_clean();

			header("Content-type: text/html");
			
			$result_qr_content_in_base64 =  base64_encode($result_qr_content_in_png);

			$return['qrcode'][$ctr]			= $result_qr_content_in_base64; 
			$ctr++;

		}

		$return['ctr']	  = $ctr;
		$return["result"] = json_encode($result);
		echo json_encode($return);
	}
?>