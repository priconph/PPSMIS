<?php
	//echo "test";
	if(is_ajax()) {
		if(isset($_POST["action"]) && !empty($_POST["action"])) {
			$action = $_POST["action"];
			switch($action) {
				case "addNewProduct" 					: addNewProduct(); break;
				case "addNewItem" 						: addNewItem(); break;
				case "addNewProductWithrawal" 			: addNewProductWithrawal(); break;
				case "addNewProductWithrawal2" 			: addNewProductWithrawal2(); break;	
				case "addTotal" 						: addTotal(); break;			
				case "updateItemList" 					: updateItemList(); break;	
				case "updateBalance" 					: updateBalance(); break;
				case "deleteResinRecord" 				: deleteResinRecord(); break;
				case "getLastID" 						: getLastID(); break;
				case "deleteAllProductInfo" 			: deleteAllProductInfo(); break;
				case "ImportWarehouseData" 				: ImportWarehouseData(); break;
				case "load_transfer_slip_transactions" 	: load_transfer_slip_transactions(); break;
				case "save_new_transfer_slip" 			: save_new_transfer_slip(); break;
				case "update_transfer_slip" 			: update_transfer_slip(); break;
				case "acknowledge" 						: acknowledge(); break;	


				case "display_resin_summary1" 						: display_resin_summary1(); break;
				case "display_resin_summary3" 						: display_resin_summary3(); break;
					
							
			}
		}
	}

	function is_ajax() {
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}
	
	function addNewProduct() {
		require_once('ppswarehouse-oop.php');
		$return = $_POST;
		$result = "";
		// $result = PPSW::getInstance()->addNewProduct($return['Classification'],$return['ProductLine'],$return["PartNumber"],$return["MaterialType"],$return["Model"],$return["Balance"],$return["LastUpdate"],$return["Username"],$return["Supplier"],$return["Cost"]); //04082024 by Nessa
		$result = PPSW::getInstance()->addNewProduct($return['Classification'],$return['ProductLine'],$return["PartNumber"],$return["MaterialType"],$return["Model"],$return["Balance"],$return["LastUpdate"],$return["Username"],$return["Supplier"],$return["Cost"],$return["Factory"]);
		$return["result"] = json_encode($result);
		echo json_encode($return);
	}

	function addNewItem() {
		require_once('ppswarehouse-oop.php');
		$return = $_POST;
		$result = "";
		$result = PPSW::getInstance()->addNewItem($return["partcode"],$return['ProductLine'],$return["partname"],$return["Model"]);
		$return["result"] = json_encode($result);
		echo json_encode($return);
	}

	function addNewProductWithrawal() {
		require_once('ppswarehouse-oop.php');
		$return = $_POST;
		$result = "";
		$result = PPSW::getInstance()->addNewProductWithrawal($return["fkid"],$return["Date"],$return["Price"],$return["InvoiceNo"],$return["TransferSlipNo"],$return["BOH"],$return["IN"],$return["OUT"],$return["EOH"],$return["RecordedBy"],$return["Remarks"],$return["inspection_class"],$return["Lot_number"],$return["LastUpdate"],$return["Username"],$return["AcknowledgedBy"]);
		$result2 = PPSW::getInstance()->addTotal($return["fkid"],$return["BOH"],$return["IN"],$return["OUT"],$return["EOH"],$return["LastUpdate"]);
		$result3 = PPSW::getInstance()->updateBalancePerTransaction($return["fkid"],$return["EOH"]); // CHAN 08-13-2024

		$return["result"] = json_encode($result);
		$return["result2"] = json_encode($result2);
		$return["result3"] = json_encode($result3); // CHAN 08-13-2024
	}
	
	function addNewProductWithrawal2() {
		require_once('ppswarehouse-oop.php');
		$return = $_POST;
		$result = "";
		$result = PPSW::getInstance()->addNewProductWithrawal2($return["Date"],$return["Price"],$return["InvoiceNo"],$return["TransferSlipNo"],$return["BOH"],$return["IN"],$return["OUT"],$return["EOH"],$return["RecordedBy"],$return["Remarks"],$return["LastUpdate"],$return["Username"]);
		//$return['last_id'] = $result;
		$return["result"] = json_encode($result);
		echo json_encode($return);
	}
	
	function updateBalance() {
		require_once('ppswarehouse-oop.php');
		$return = $_POST;
		$result = "";
		// $result = PPSW::getInstance()->updateBalance($return['id'],$return['Classification'],$return['ProductLine'],$return['PartNumber'],$return['MaterialType'],$return['Model'],$return['Balance'],$return['Cost'],$return['Supplier'],$return["LastUpdate"],$return["Username"]);
		$result = PPSW::getInstance()->updateBalance($return['id'],$return['Classification'],$return['ProductLine'],$return['PartNumber'],$return['MaterialType'],$return['Model'],$return['Balance'],$return['Cost'],$return['Factory'],$return['Supplier'],$return["LastUpdate"],$return["Username"]); // 04082024 by Nessa
		$return["result"] = json_encode($result);
		echo json_encode($return);
	}

	function updateItemList() {
		require_once('ppswarehouse-oop.php');
		$return = $_POST;
		$result = "";
		// $result = PPSW::getInstance()->updateItem($return['pkid_itemlist'],$return['Classification'],$return['partcode'],$return['ProductLine'],$return['partname'],$return['Model']);
		$result = PPSW::getInstance()->updateItem($return['pkid_itemlist'],$return['Classification'],$return['partcode'],$return['ProductLine'],$return['partname'],$return['Model'],$return['Factory']);
		$return["result"] = json_encode($result);
		echo json_encode($return);
	}
	function load_transfer_slip_transactions() {
		require_once('ppswarehouse-oop.php');
		$return = $_POST;
		$result = "";
		$result = PPSW::getInstance()->load_transfer_slip_transactions();
		$ctr = 0;
		while ($row = mysqli_fetch_array($result)){
			$return['pkid'][$ctr] = $row['pkid'];
			$return['PartNumber'][$ctr] = $row['PartNumber'];
			$return['MaterialType'][$ctr] = $row['MaterialType'];
			$return['Qty'][$ctr] = $row['Out'];
			$return['Remarks'][$ctr] = $row['Remarks'];
			$ctr++;
		}
		$return['ctr'] = $ctr;
		$return["result"] = json_encode($result);
		echo json_encode($return);
	}
	
	function deleteResinRecord() {
		require_once('ppswarehouse-oop.php');
		$return = $_POST;
		$result = "";
		$result = PPSW::getInstance()->deleteResinRecord($return['pkid']);
		$return["result"] = json_encode($result);
		echo json_encode($return);
	}
	
	function getLastID(){
		require_once('ppswarehouse-oop.php');
		$return = $_POST;
		$result = "";
		$result = PPSW::getInstance()->getLastID($return['PartNumber']);
		if($row = mysqli_fetch_array($result)){
			$return['lastID'] = $row['pkid'];
		}
		$return["result"] = json_encode($result);
		echo json_encode($return);
	}

	function acknowledge(){
		require_once('ppswarehouse-oop.php');
		$return = $_POST;
		$result = "";
		$result = PPSW::getInstance()->acknowledge($return['id'],$return['AcknowledgedBy']);
		$return["result"] = json_encode($result);
		echo json_encode($return);
	}

	function deleteAllProductInfo(){
		require_once('ppswarehouse-oop.php');
		$return = $_POST;
		$result = "";
		$result = PPSW::getInstance()->deleteAllProductInfo($return['PartNumber']);
		if($row = mysqli_fetch_array($result)){
			$return['lastID'] = $row['pkid'];
		}
		$return["result"] = json_encode($result);
		echo json_encode($return);
	}
	
	function save_new_transfer_slip(){
		require_once('ppswarehouse-oop.php');
		$return = $_POST;
		$result = "";
		$result = PPSW::getInstance()->save_new_transfer_slip($return['ProductLine'],$return['ControlNo'],$return['Date'],
															$return['IssuedBy'],$return['LastUpdate'],$return['Username']);
		$return["result"] = json_encode($result);
		echo json_encode($return);
	}
	
	function update_transfer_slip(){
		require_once('ppswarehouse-oop.php');
		$return = $_POST;
		$result = "";
		$result = PPSW::getInstance()->update_transfer_slip($return['pkid'],$return['Trans_Slip']);
		$return["result"] = json_encode($result);
		echo json_encode($return);
	}
	
	function ImportWarehouseData(){
		require_once('ppswarehouse-oop.php');
		$return = $_POST;
		$result = "";
		$result = PPSW::getInstance()->ImportWarehouseData($return['ProductLine'],$return['PartNumber'],$return['MaterialType'],
															$return['Supplier'],$return['Model'],$return['LastUpdate'],
															$return['Username']);
		$return["result"] = json_encode($result);
		echo json_encode($return);
	}
	
	//YPICS Database access
	function searchPoNum(){
		$return = $_POST;
		$result = "";
		require_once('ppswarehouse-oop.php');
		$YPICS = new YPICS();
		$result = $YPICS->searchPoNum($return['PONum']);
			while ( $row = mssql_fetch_array($result) ) {			
				$return["PONum"]  = $row['PONum'];
				$return["DeviceName"]  = $row['DeviceName'];	
				$return["ProdCode"]  = $row['ProdCode'];	
				$return["POQty"]  = $row['POQty'];
				$return["CUST"]  = $row['CUST'];
			}
		$return["result"] = json_encode($result);
		echo json_encode($return);
	}

	// 04042024
	function display_resin_summary1(){

		require_once('ppswarehouse-oop.php');
		
		$return 		= $_POST;
		$result 		= '';
		$array_fields 	= array('A.*, B.Classification Classification2, (d_BOH+c_EOH) EOH');
		$table 	   		= 'tbl_Warehouse A';
		$joins 	   		= 'LEFT JOIN tbl_Warehouse_Classification B
			ON A.Classification=B.id
			LEFT JOIN
			( SELECT fkid c_fkid, (SUM(`In`) - SUM(`Out`)) c_EOH FROM tbl_WarehouseTransaction GROUP BY fkid ORDER BY Date DESC, pkid DESC ) C
			ON A.id = C.c_fkid

			LEFT JOIN 
			(
				SELECT * FROM (
					SELECT fkid d_fkid, Boh d_BOH FROM tbl_WarehouseTransaction ORDER BY Date ASC, pkid ASC
				) AA
				GROUP BY d_fkid
			) D
			ON A.id = D.d_fkid';
		$sql_where 		= "WHERE (c_EOH IS NOT NULL or (year(LastUpdate) > 2019 and LastUpdate <> 42065)) AND A.Factory <> 3";
		$sql_order 		= 'ORDER BY A.Classification DESC';
		$sql_limit 		= '';
		$result 		= PPSW::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);

		if (!$result) {
			echo mysql_error();
		}

		$ctr = 0;

		while($row = mysqli_fetch_array($result)){
			$return['id'][$ctr] 				= $row['id'];
			$return['Classification'][$ctr] 	= $row['Classification2'];
			$return['ProductLine'][$ctr] 		= $row['ProductLine'];
			$return['PartNumber'][$ctr] 		= $row['PartNumber'];
			$return['MaterialType'][$ctr] 		= $row['MaterialType'];
			$return['EOH'][$ctr] 				= $row['EOH'];
			$return['Remarks'][$ctr] 			= $row['Remarks'];
			
			
			$ctr++;
		}

		$return['ctr']	  = $ctr;
		$return['result'] = json_encode($result);
		echo json_encode($return);

	}

	function display_resin_summary3(){

		require_once('ppswarehouse-oop.php');
		
		$return 		= $_POST;
		$result 		= '';
		$array_fields 	= array('A.*, B.Classification Classification2, (d_BOH+c_EOH) EOH');
		$table 	   		= 'tbl_Warehouse A';
		$joins 	   		= 'LEFT JOIN tbl_Warehouse_Classification B
			ON A.Classification=B.id
			LEFT JOIN
			( SELECT fkid c_fkid, (SUM(`In`) - SUM(`Out`)) c_EOH FROM tbl_WarehouseTransaction GROUP BY fkid ORDER BY Date DESC, pkid DESC ) C
			ON A.id = C.c_fkid

			LEFT JOIN 
			(
				SELECT * FROM (
					SELECT fkid d_fkid, Boh d_BOH FROM tbl_WarehouseTransaction ORDER BY Date ASC, pkid ASC
				) AA
				GROUP BY d_fkid
			) D
			ON A.id = D.d_fkid';
		$sql_where 		= "WHERE (c_EOH IS NOT NULL or (year(LastUpdate) > 2019 and LastUpdate <> 42065)) AND A.Factory ='3' ";
		$sql_order 		= 'ORDER BY A.Classification DESC';
		$sql_limit 		= '';
		$result 		= PPSW::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);

		if (!$result) {
			echo mysql_error();
		}

		$ctr = 0;

		while($row = mysqli_fetch_array($result)){
			$return['id'][$ctr] 				= $row['id'];
			$return['Classification'][$ctr] 	= $row['Classification2'];
			$return['ProductLine'][$ctr] 		= $row['ProductLine'];
			$return['PartNumber'][$ctr] 		= $row['PartNumber'];
			$return['MaterialType'][$ctr] 		= $row['MaterialType'];
			$return['EOH'][$ctr] 				= $row['EOH'];
			$return['Remarks'][$ctr] 			= $row['Remarks'];
			
			
			$ctr++;
		}

		$return['ctr']	  = $ctr;
		$return['result'] = json_encode($result);
		echo json_encode($return);

	}

?>