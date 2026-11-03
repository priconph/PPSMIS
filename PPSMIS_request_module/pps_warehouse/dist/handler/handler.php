<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if( !function_exists('is_ajax') ){
	function is_ajax(){
		return isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest';
	}
}


if(is_ajax()){
	if( isset($_POST['action']) && !empty($_POST['action']) ){
		switch ( $_POST['action'] ) {//switch case, in using ajax an action must be set in order to choose the specific function to use

			case 'select_item_list'											:			select_item_list(); break;
			case 'select_destination_list'									:			select_destination_list(); break;
			case 'select_current_user_and_datetime'							:			select_current_user_and_datetime(); break;
			case 'check_session_expired'									:			check_session_expired(); break;

			case 'insert_request'											:			insert_request(); break;
			case 'cancel_request'											:			cancel_request(); break;
			case 'acknowledgement'											:			acknowledgement(); break;
			case 'select_request_details' 									:			select_request_details(); break;
			case 'select_request_details_f3' 								:			select_request_details_f3(); break;
			case 'select_request_details_multiple' 							:			select_request_details_multiple(); break;
			case 'select_available_balance'									:			select_available_balance(); break;

			case 'insert_issue'												:			insert_issue(); break;
			case 'insert_issue_multiple'									:			insert_issue_multiple(); break;
			
			case 'check_material_lot_number_fifo'							:			check_material_lot_number_fifo(); break; // FIFO 
			case 'get_data_by_lot_no'										:			get_data_by_lot_no(); break;


			default: break;
		}
	}
}


function select_current_user_and_datetime(){
	require_once('../../../dist/handler/functions.php'); //OOP Path
	$return['username'] = isset($_SESSION['username'])?$_SESSION['username']:'';
	$return['datetime'] = datetime_convert( date('Y-m-d H:i:s') );

	echo json_encode($return);
}

function check_session_expired(){
	$return['username'] = isset($_SESSION['username'])?$_SESSION['username']:'';
	echo json_encode($return);
}


//PHP
function generate_new_ctrl_no(){
	require_once('../class/oop.php'); //OOP Path
	$result 		= "";
	$array_fields 	= array('control_number');
	$table 	   		= 'tbl_request';
	$joins 	   		= '';
	$sql_where 		= 'WHERE deleted = 0';
	$sql_order 		= 'ORDER BY pkid DESC';//should be the same as the server_side order
	$sql_limit 		= 'LIMIT 0,1';
	$result 		= db_pps::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
	$ctrl_date 		= '';
	$number 		= 0;
	while($row = mysqli_fetch_array($result)){
		 $ctrl_date  = substr($row['control_number'], -8, 4);
   		 $number   = (int)substr($row['control_number'], -3);//PPS-0000-000
	}

	if($ctrl_date != date('ym')){//continue the prev ctrl no, not 001
		$number = 0;
	}
	$number ++;

	$ctrl_no = "PPS-".date('ym').'-'.str_pad($number, 3, 0, STR_PAD_LEFT);

	return $ctrl_no;
}

function select_fullname($username){
	require_once('../class/oop-rapid.php'); //OOP Path
	$result 		= "";
	$array_fields 	= array('name');
	$table 	   		= 'tbl_useraccounts';
	$joins 	   		= '';
	$sql_where 		= 'WHERE username = "'.$username.'"';
	$sql_order 		= 'ORDER BY id DESC';//should be the same as the server_side order
	$sql_limit 		= 'LIMIT 0,1';
	$result 		= DB_RAPID::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
	$fullname = '---';
	while($row = mysqli_fetch_array($result)){
		$fullname = $row['name'];
	}
	return $fullname;
	exit();
}

//-PHP

//---------
function select_item_list(){
	require_once('../class/oop.php'); //OOP Path
	$return 		= $_POST;
	$result 		= "";
	$array_fields 	= array('*');
	$table 	   		= '
		(
			SELECT
				t1.pkid_itemlist,
				t1.partcode,
				t1.partname,
				t1.Factory
			FROM tbl_itemList t1
			ORDER BY t1.pkid_itemlist ASC
		) t_all
	';
	$joins 	   		= '';
	$sql_where 		= 'WHERE (partcode LIKE "%'.$return['keyword'].'%" OR partname LIKE "%'.$return['keyword'].'%")';
	$sql_order 		= '';
	$sql_limit 		= 'LIMIT 0,10';
	$result 		= db_pps::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
	$ctr 			= 0;
	while($row = mysqli_fetch_array($result)){
		$return['pkid_itemlist'][$ctr] 			= $row['pkid_itemlist'];
		$return['partcode'][$ctr] 				= $row['partname'];
		$return['partname'][$ctr] 				= $row['partcode'];
		$return['Factory'][$ctr] 				= $row['Factory'];
		$ctr ++;
	}

	$return['ctr'] 		= $ctr;
	$return["result"] 	= json_encode($result); //return the serialize values
	echo json_encode($return);
	exit();
}

function select_destination_list(){
	require_once('../class/oop.php'); //OOP Path
	$return 		= $_POST;
	$result 		= "";
	$array_fields 	= array('*');
	$table 	   		= '
		(
			SELECT
				t1.pkid,
				t1.destination
			FROM tbl_request t1
			GROUP BY t1.destination
			ORDER BY t1.destination ASC
		) t_all
	';
	$joins 	   		= '';
	$sql_where 		= 'WHERE destination LIKE "%'.$return['keyword'].'%"';
	$sql_order 		= '';
	$sql_limit 		= 'LIMIT 0,10';
	$result 		= db_pps::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
	$ctr 			= 0;
	while($row = mysqli_fetch_array($result)){
		$return['destination'][$ctr] 			= $row['destination'];
		$ctr ++;
	}

	$return['ctr'] 		= $ctr;
	$return["result"] 	= json_encode($result); //return the serialize values
	echo json_encode($return);
	exit();
}

function insert_request(){
	require_once('../class/oop.php');
	$return 			= $_POST;
	if( $return['pkid'] ){
		update_request();
	}
	$result 			= "";
	$table 				= 'tbl_request';
	$array_fields 		= array(
		'created_on',
		'created_by',
		'control_number',
		'fk_itemlist',
		'qty',
		'destination',
		'r_remarks'
	);
	$array_values 		= array(
		date('Y-m-d H:i:s', strtotime( $return['txt_req_on'] ) ),
		$return['txt_req_by'],
		generate_new_ctrl_no(),
		$return['fkid'],
		$return['txt_req_qty'],
		$return['txt_req_destination'],
		$return['txt_req_remarks']
	);
	$result 			= db_pps::getInstance()->insert_query($table,$array_fields,$array_values);
	$return['result'] 	= json_encode($result);
	echo json_encode($return);
	exit();
}

function update_request(){
	require_once('../class/oop.php');
	$return 			= $_POST;
	$result 			= "";
	$table 				= "tbl_request";
	$array_fields 		= array(
		'updated_by',
		'fk_itemlist',
		'qty',
		'destination',
		'r_remarks'
	);
	$array_values 		= array(
		isset($_SESSION['username'])?$_SESSION['username']:'',
		$return['fkid'],
		$return['txt_req_qty'],
		$return['txt_req_destination'],
		$return['txt_req_remarks']
	);
	$sql_where 			= "WHERE pkid = '".$return['pkid']."'";
	$result 			= db_pps::getInstance()->update_query_with_other_condition($table,$array_fields,$array_values,$sql_where);
	$return['result'] 	= json_encode($result);
	echo json_encode($return);
	exit();
}

function select_request_details(){
	require_once('../../../dist/handler/functions.php'); //OOP Path
	require_once('../class/oop.php');
	$return 			= $_POST;
	$result 			= "";
	$array_fields 		= array("*");
	$table 				= "
		(
			SELECT 	a.pkid,
					a.created_on,
					a.created_by,
					a.control_number,
					a.fk_itemlist,
					a.qty,
					a.destination,
					a.fk_issuance,
					a.r_remarks,
					a.i_remarks,
					a.cancelled,
					b.partcode,
					b.partname,
					b.Model,
					c.issuanceqty,
					c.issuancedate,
					c.issuedby
			FROM tbl_request a
			LEFT JOIN tbl_itemList b
			ON a.fk_itemlist = b.pkid_itemlist
			LEFT JOIN tbl_issuance c
			ON a.fk_issuance  = c.pkid_issuance
			WHERE a.deleted = 0 and a.created_on = year(curdate())
			ORDER BY pkid DESC
		) tbl1
	";
	$joins 				= "";
	$sql_where 			= "WHERE pkid = '".$return['select_pkid']."'";
	$sql_order 			= "";
	$sql_limit 			= "";
	$result 			= db_pps::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
	$ctr 				= 0;
	while($row = mysqli_fetch_array($result)){
		$return['pkid'][$ctr] 					= $row['pkid'];
		$return['created_on'][$ctr] 			= datetime_convert($row['created_on']);
		$return['created_by'][$ctr] 			= $row['created_by'];
		$return['control_number'][$ctr] 		= $row['control_number'];
		$return['fk_itemlist'][$ctr] 			= $row['fk_itemlist'];
		$return['qty'][$ctr] 					= $row['qty'];
		$return['destination'][$ctr] 			= $row['destination'];
		$return['fk_issuance'][$ctr] 			= $row['fk_issuance'];
		$return['r_remarks'][$ctr] 				= $row['r_remarks'];
		$return['i_remarks'][$ctr] 				= $row['i_remarks'];

		$return['partcode'][$ctr] 				= $row['partname'];
		$return['partname'][$ctr] 				= $row['partcode'];

		$return['issuanceqty'][$ctr] 			= $row['issuanceqty'];
		$return['issuedby'][$ctr] 				= $row['issuedby'];
		$return['issuancedate'][$ctr] 			= datetime_convert($row['issuancedate']);

		$status = '<i class="fa fa-clock fa-lg text-secondary" title="Pending"></i> Pending';
		if( $row['issuanceqty'] ){
			if( $row['qty'] == $row['issuanceqty'] ){
				$status = '<i class="fa fa-check-circle fa-lg text-success" title="Served"></i> Served';
			}
			elseif( $row['qty'] < $row['issuanceqty'] ){
				$status = '<i class="fa fa-exclamation-circle fa-lg text-danger" title="Excess"></i> Excess';
			}
			elseif( $row['qty'] > $row['issuanceqty'] ){
				// $status = '<i class="fa fa-exclamation-circle fa-lg text-warning" title="Incomplete"></i> Incomplete';
			}
		}
		if( $row['cancelled'] == 1 ){
			$status = '<i class="fa fa-ban fa-lg text-danger status_cancelled" title="Cancelled"></i> Cancelled';
		}
		$return['status'][$ctr] 				= $status;

		$ctr ++;
	}
	$return['ctr'] 		= $ctr;
	$return['result'] 	= json_encode($result);
	echo json_encode($return);
	exit();
}

function select_request_details_f3(){
	require_once('../../../dist/handler/functions.php'); //OOP Path
	require_once('../class/oop.php');
	$return 			= $_POST;
	$result 			= "";
	$array_fields 		= array("*");
	$table 				= "
		(
			SELECT 	a.pkid,
					a.created_on,
					a.created_by,
					a.control_number,
					a.fk_itemlist,
					a.qty,
					a.destination,
					a.fk_issuance,
					a.r_remarks,
					a.i_remarks,
					a.cancelled,
					b.partcode,
					b.partname,
					b.Model,
					c.issuanceqty,
					c.issuancedate,
					c.issuedby,
					d.Factory
			FROM tbl_request a
			LEFT JOIN tbl_itemList b ON a.fk_itemlist = b.pkid_itemlist
			LEFT JOIN tbl_issuance c ON a.fk_issuance  = c.pkid_issuance
			LEFT JOIN tbl_Warehouse d ON b.partcode  = d.PartNumber
			WHERE a.deleted = 0 and 
			MONTH(a.created_on) = MONTH(CURRENT_DATE()) and
			YEAR(a.created_on) = YEAR(CURRENT_DATE()) and 
			d.Factory = '3'
			ORDER BY pkid DESC
		) tbl1
	";
	$joins 				= "";
	$sql_where 			= "WHERE pkid = '".$return['select_pkid']."'";
	$sql_order 			= "";
	$sql_limit 			= "";
	$result 			= db_pps::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
	$ctr 				= 0;
	while($row = mysqli_fetch_array($result)){
		$return['pkid'][$ctr] 					= $row['pkid'];
		$return['created_on'][$ctr] 			= datetime_convert($row['created_on']);
		$return['created_by'][$ctr] 			= $row['created_by'];
		$return['control_number'][$ctr] 		= $row['control_number'];
		$return['fk_itemlist'][$ctr] 			= $row['fk_itemlist'];
		$return['qty'][$ctr] 					= $row['qty'];
		$return['destination'][$ctr] 			= $row['destination'];
		$return['fk_issuance'][$ctr] 			= $row['fk_issuance'];
		$return['r_remarks'][$ctr] 				= $row['r_remarks'];
		$return['i_remarks'][$ctr] 				= $row['i_remarks'];

		$return['partcode'][$ctr] 				= $row['partname'];
		$return['partname'][$ctr] 				= $row['partcode'];

		$return['issuanceqty'][$ctr] 			= $row['issuanceqty'];
		$return['issuedby'][$ctr] 				= $row['issuedby'];
		$return['issuancedate'][$ctr] 			= datetime_convert($row['issuancedate']);

		$status = '<i class="fa fa-clock fa-lg text-secondary" title="Pending"></i> Pending';
		if( $row['issuanceqty'] ){
			if( $row['qty'] == $row['issuanceqty'] ){
				$status = '<i class="fa fa-check-circle fa-lg text-success" title="Served"></i> Served';
			}
			elseif( $row['qty'] < $row['issuanceqty'] ){
				$status = '<i class="fa fa-exclamation-circle fa-lg text-danger" title="Excess"></i> Excess';
			}
			elseif( $row['qty'] > $row['issuanceqty'] ){
				// $status = '<i class="fa fa-exclamation-circle fa-lg text-warning" title="Incomplete"></i> Incomplete';
			}
		}
		if( $row['cancelled'] == 1 ){
			$status = '<i class="fa fa-ban fa-lg text-danger status_cancelled" title="Cancelled"></i> Cancelled';
		}
		$return['status'][$ctr] 				= $status;

		$ctr ++;
	}
	$return['ctr'] 		= $ctr;
	$return['result'] 	= json_encode($result);
	echo json_encode($return);
	exit();
}

function select_request_details_multiple(){
	require_once('../../../dist/handler/functions.php'); //OOP Path
	require_once('../class/oop.php');
	$return 			= $_POST;
	$result 			= "";
	$array_fields 		= array("*");
	$table 				= "
		(
			SELECT 	a.pkid,
					a.created_on,
					a.created_by,
					a.control_number,
					a.fk_itemlist,
					a.qty,
					a.destination,
					a.fk_issuance,
					a.r_remarks,
					a.i_remarks,
					a.cancelled,
					b.partcode,
					b.partname,
					b.Model,
					c.issuanceqty,
					c.issuancedate,
					c.issuedby,
					d.issuanceqtym,
					d.im_remarks,
					d.opt_remarks
			FROM tbl_request a
			LEFT JOIN tbl_itemList b
			ON a.fk_itemlist = b.pkid_itemlist
			LEFT JOIN tbl_issuance c
			ON a.fk_issuance  = c.pkid_issuance
			LEFT JOIN tbl_issuance_breakdown d
			ON c.pkid_issuance  = d.fk_issuance
			WHERE a.deleted = 0
			ORDER BY pkid DESC
		) tbl1
	";
	$joins 				= "";
	$sql_where 			= "WHERE pkid = '".$return['select_pkid']."'";
	$sql_order 			= "";
	$sql_limit 			= "";
	$result 			= db_pps::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
	$ctr 				= 0;
	while($row = mysqli_fetch_array($result)){
		$return['pkid'][$ctr] 					= $row['pkid'];
		$return['created_on'][$ctr] 			= datetime_convert($row['created_on']);
		$return['created_by'][$ctr] 			= $row['created_by'];
		$return['control_number'][$ctr] 		= $row['control_number'];
		$return['fk_itemlist'][$ctr] 			= $row['fk_itemlist'];
		$return['qty'][$ctr] 					= $row['qty'];
		$return['destination'][$ctr] 			= $row['destination'];
		$return['fk_issuance'][$ctr] 			= $row['fk_issuance'];
		$return['r_remarks'][$ctr] 				= $row['r_remarks'];
		$return['i_remarks'][$ctr] 				= $row['i_remarks'];

		$return['partcode'][$ctr] 				= $row['partname'];
		$return['partname'][$ctr] 				= $row['partcode'];

		$return['issuanceqty']					= $row['issuanceqty'];
		$return['issuedby'][$ctr] 				= $row['issuedby'];
		$return['issuancedate'][$ctr] 			= datetime_convert($row['issuancedate']);

		$return['issuanceqtym'][$ctr] 			= $row['issuanceqtym'];
		$return['im_remarks'][$ctr] 			= $row['im_remarks'];
		$return['opt_remarks'][$ctr] 			= $row['opt_remarks'];

		$status = '<i class="fa fa-clock fa-lg text-secondary" title="Pending"></i> Pending';
		if( $row['issuanceqty'] ){
			if( $row['qty'] == $row['issuanceqty'] ){
				$status = '<i class="fa fa-check-circle fa-lg text-success" title="Served"></i> Served';
			}
			elseif( $row['qty'] < $row['issuanceqty'] ){
				$status = '<i class="fa fa-exclamation-circle fa-lg text-danger" title="Excess"></i> Excess';
			}
			elseif( $row['qty'] > $row['issuanceqty'] ){
				// $status = '<i class="fa fa-exclamation-circle fa-lg text-warning" title="Incomplete"></i> Incomplete';
			}
		}
		if( $row['cancelled'] == 1 ){
			$status = '<i class="fa fa-ban fa-lg text-danger status_cancelled" title="Cancelled"></i> Cancelled';
		}

		$return['status'][$ctr] 				= $status;

		$ctr ++;
	}
	$return['ctr'] 		= $ctr;
	$return['result'] 	= json_encode($result);
	echo json_encode($return);
	exit();
}

// updated by Nessa 07252024
function select_available_balance(){
	require_once('../class/oop.php');
	$return 		= $_POST;
	$result 		= "";
	$array_fields 	= array("*");
	// $table 			= "tbl_WarehouseTransaction";
	$table 			= "tbl_Warehouse";
	$joins 			= "";
	// $sql_where 		= "WHERE fkid = '".$return['fkid']."'";
	$sql_where 		= "WHERE id = '".$return['fkid']."'";
	// $sql_order 		= "ORDER BY pkid DESC";
	$sql_order 		= "";
	// $sql_limit 		= "LIMIT 0,1";
	$sql_limit 		= "";
	$result 		= db_pps::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
	$ctr 			= 0;
	while ($row = mysqli_fetch_array($result)) {
		// $return['remainingqty'][$ctr] = $row['Eoh'];
		$return['remainingqty'][$ctr] = $row['Balance'];
		$ctr++;
	}
	$return['ctr'] 		= $ctr;
	$return['result'] 	= json_encode($result);
	echo json_encode($return);
	exit();
}

function insert_issue(){
	require_once('../class/oop.php');
	$return 			= $_POST;
	$result 			= "";
	$table 				= 'tbl_issuance';
	$table2				= 'tbl_Warehouse_TranSlip';
	$table3				= 'tbl_WarehouseTransaction';
	$array_fields 		= array(
		'fkid_itemlist',
		'issuanceqty',
		'ctrlno',
		'destination',
		'requestor',
		'issuedby',
		'issuancedate'
	);

	$array_fields2 		= array(
		'ProductLine',		
		'ControlNo',
		'IssuedBy',
		'Username',
		'Date'
	);

	$array_fields3 		= array(
		'fkid',
		'Date',		
		'TransferSlipNo',
		'tbl_WarehouseTransaction.Boh',
		'tbl_WarehouseTransaction.Out',
		'tbl_WarehouseTransaction.Eoh',
		'RecordedBy',
		'Remarks',
		'LastUpdate',
		'Username'
	);

	$array_values 		= array(
		$return['fkid'],
		$return['txt_issue_qty'],
		$return['span_ctrl_no'],
		$return['txt_req_destination'],
		$return['txt_req_by'],
		$return['txt_issue_by'],
		date('Y-m-d H:i:s', strtotime( $return['txt_issue_on'] ) )
	);

	$array_values2 		= array(
		"PPS Resin",
		$return['span_ctrl_no'],
		$return['txt_issue_by'],
		$_SESSION['username'],
		// date('Y-m-d', strtotime( $return['txt_issue_on'] ) )
		date('Y-m-d H:i:s', strtotime( $return['txt_issue_on'] ) ) //added by Nessa 04162024

	);


	$array_values3 		= array(
		$return['fkid'],
		date('Y-m-d H:i:s', strtotime( $return['txt_issue_on'] ) ),
		$return['span_ctrl_no'],
		$return['txt_issue_allowed_qty'],
		$return['txt_issue_qty'],
		$return['txt_issue_allowed_qty'] - $return['txt_issue_qty'],
		$_SESSION['username'],
		$return['txt_issue_remarks'],
		date('Y-m-d H-i-s', strtotime( $return['txt_issue_on'] ) ),
		$_SESSION['username']


	);

	$last_id 			= db_pps::getInstance()->insert_query_get_last_id($table,$array_fields,$array_values);	
	$result				= db_pps::getInstance()->insert_query2($table2,$array_fields2,$array_values2);
	$result				= db_pps::getInstance()->insert_query3($table3,$array_fields3,$array_values3);
	$return['result'] 	= json_encode($result);
	$return['result'] 	= json_encode($result);
	update_request_issue($return['pkid'],$last_id);
	// $return['result'] 	= json_encode($result);
	// echo json_encode($return);
	// exit();
}


function insert_issue_multiple(){
	require_once('../class/oop.php');
	$return 			= $_POST;
	$result 			= "";
	$table 				= 'tbl_issuance';
	$table2				= 'tbl_Warehouse_TranSlip';
	$table3				= 'tbl_WarehouseTransaction';
	$array_fields 		= array(
		'fkid_itemlist',
		'issuanceqty',
		'ctrlno',
		'destination',
		'requestor',
		'issuedby',
		'issuancedate'
	);
	$array_values		= array(
		$return['fkid'],
		$return['txt_total_issue_qty'],
		$return['span_ctrl_no'],
		$return['txt_req_destination'],
		$return['txt_req_by'],
		$return['txt_issue_by_multi'],
		date('Y-m-d H:i:s', strtotime( $return['txt_issue_on_multi'] ) )
	);

	$array_fields2 		= array(
		'ProductLine',		
		'ControlNo',
		'IssuedBy',
		'Username',
		'Date'
	);
	$array_values2 		= array(
		"PPS Resin",
		$return['span_ctrl_no'],
		$return['txt_issue_by_multi'],
		$_SESSION['username'],
		// date('Y-m-d', strtotime( $return['txt_issue_on_multi'] ) )
		date('Y-m-d H:i:s', strtotime( $return['txt_issue_on_multi'] ) ) //added by Nessa 04162024
	);

	$array_fields3 		= array(
		'fkid',
		'Date',		
		'TransferSlipNo',
		'tbl_WarehouseTransaction.Boh',
		'tbl_WarehouseTransaction.Out',
		'tbl_WarehouseTransaction.Eoh',
		'RecordedBy',
		'Lot_number',
		'Remarks',
		'LastUpdate',
		'Username'
	);

	// $issueqtym = 0;
	$issueqtym = "";
	if (isset($return['txt_multi_issue_qty'])){
		$issueqtym = implode(',', $return['txt_multi_issue_qty']);

		// return $issueqtym;
	};

	$boh = $return['txt_issue_allowed_qty'];
	$multissue = count($return['txt_multi_issue_qty']);
	for ($i = 0; $i < $multissue; $i++) {

	$eoh = $boh - $return['txt_multi_issue_qty'][$i];

	$array_values3 		= array(
		$return['fkid'],
		date('Y-m-d H:i:s', strtotime( $return['txt_issue_on_multi'] ) ),
		$return['span_ctrl_no'],
		$boh,
		$return['txt_multi_issue_qty'][$i],
		$eoh,
		$_SESSION['username'],
		$return['txt_multi_issue_remarks'][$i],
		$return['txt_opt_remarks'][$i],
		date('Y-m-d H-i-s', strtotime( $return['txt_issue_on_multi'] ) ),
		$_SESSION['username']
		);
		$result	= db_pps::getInstance()->insert_query3($table3,$array_fields3,$array_values3);
		$boh = $boh - $return['txt_multi_issue_qty'][$i];
	};

	$last_id 			= db_pps::getInstance()->insert_query_get_last_id($table,$array_fields,$array_values);	
	$result				= db_pps::getInstance()->insert_query2($table2,$array_fields2,$array_values2);
	
	$return['result'] 	= json_encode($result);
	$return['result'] 	= json_encode($result);
	$updatereq = update_request_issue_multiple($return['pkid'],$last_id);
	$issuebreakdown = issuance_breakdown($return['pkid'],$last_id);
	$return['updatereq'] 		= json_encode($updatereq);
	$return['issuebreakdown'] 	= json_encode($issuebreakdown);
	echo json_encode($return);
	exit();
}

function update_request_issue($pkid, $pkid_issuance){
	require_once('../class/oop.php');
	$return 			= $_POST;
	$result 			= "";
	$table 				= "tbl_request";
	$array_fields 		= array(
		'updated_by',
		'fk_issuance',
		'i_remarks',
		'opt_remakrs',
	);
	$array_values 		= array(
		isset($_SESSION['username'])?$_SESSION['username']:'',
		$pkid_issuance,
		$return['txt_issue_remarks'],
		$return['txt_opt_remarks'],
	);
	$sql_where 			= "WHERE pkid = '".$return['pkid']."'";
	$result 			= db_pps::getInstance()->update_query_with_other_condition($table,$array_fields,$array_values,$sql_where);
	$return['result'] 	= json_encode($result);	
	//echo json_encode($return);
	//exit();
}


function update_request_issue_multiple($pkid, $pkid_issuance){
	require_once('../class/oop.php');
	$return 			= $_POST;
	$result 			= "";
	$table 				= "tbl_request";
	// echo "implode ".implode(',', $return['txt_multi_issue_remarks']);
	$array_fields 		= array(
		'updated_by',
		'fk_issuance',
		'i_remarks',
		'opt_remarks',
	);
	$array_values 		= array(
		isset($_SESSION['username'])?$_SESSION['username']:'',
		$pkid_issuance,
		implode(',', $return['txt_multi_issue_remarks']),
		implode(',', $return['txt_opt_remarks']),
	);
	$sql_where 			= "WHERE pkid = '".$return['pkid']."'";
	$result 			= db_pps::getInstance()->update_query_with_other_condition($table,$array_fields,$array_values,$sql_where);
	$return['result'] 	= json_encode($result);	
	
	return 1;
	//echo json_encode($return);
	//exit();
}

function issuance_breakdown($pkid, $pkid_issuance){
	require_once('../class/oop.php');
	$return 			= $_POST;
	$result 			= "";
	$table 				= "tbl_issuance_breakdown";
	$array_fields 		= array(
		'fk_issuance',
		'fk_itemlist',
		'issuanceqtym',
		'ctrlno',
		'destination',
		'im_remarks',
		'opt_remarks',
		'requestor',
		'issuedby',
		'issuancedate'
	);

	$multissue = count($return['txt_multi_issue_qty']);
	for ($i = 0; $i < $multissue; $i++) {

	$array_values 		= array(
		$pkid_issuance,
		$return['fkid'],
		$return['txt_multi_issue_qty'][$i],
		$return['span_ctrl_no'],
		$return['txt_req_destination'],
		$return['txt_multi_issue_remarks'][$i],
		$return['txt_opt_remarks'][$i],
		$return['txt_req_by'],
		$return['txt_issue_by_multi'],
		date('Y-m-d H:i:s', strtotime( $return['txt_issue_on_multi'] ) )
	);
		$last_id = db_pps::getInstance()->insert_query_get_last_id($table,$array_fields,$array_values);	
		$array_values =  array();
		
	};
	return 1;
	//echo json_encode($return);
	//exit();
}

function cancel_request(){
	require_once('../class/oop.php');
	$return 			= $_POST;
	$result 			= "";
	$table 				= "tbl_request";
	$array_fields 		= array(
		'updated_by',
		'cancelled'
	);
	$array_values 		= array(
		isset($_SESSION['username'])?$_SESSION['username']:'',
		1
	);
	$sql_where 			= "WHERE pkid = '".$return['pkid']."'";
	$result 			= db_pps::getInstance()->update_query_with_other_condition($table,$array_fields,$array_values,$sql_where);
	$return['result'] 	= json_encode($result);
	echo json_encode($return);
	exit();
}

function acknowledgement(){
	require_once('../class/oop.php');
	$return 			= $_POST;
	$result 			= "";
	$table 				= "tbl_request";
	$table2 			= "tbl_WarehouseTransaction";
	$array_fields 		= array(
		'receive_date',
		'acknowledgedby',
		'acknowledged'
	);

	$array_fields2 		= array(
		'AcknowledgedBy'
	);

	$array_values 		= array(
		date('Y-m-d H:i:s'),
		isset($_SESSION['username'])?$_SESSION['username']:'',
		1
	);

	$array_values2 		= array(
		isset($_SESSION['username'])?$_SESSION['username']:''
	);
	$sql_where 			= "WHERE pkid = '".$return['pkid']."'";
	$result 			= db_pps::getInstance()->update_query_with_other_condition($table,$array_fields,$array_values,$sql_where);

	$sql_where2 		= "WHERE TransferSlipNo = '".$return['control_number']."'";
	$result 			= db_pps::getInstance()->update_query_with_other_condition2($table2,$array_fields2,$array_values2,$sql_where2);

	$return['result'] 	= json_encode($result);
	$return['result'] 	= json_encode($result);
	
	echo json_encode($return);
	exit();
}

function check_material_lot_number_fifo(){
	require_once('../class/oop.php');

	$return 		= $_POST;
	$result 		= "";
	$array_fields 	= array('*');
	$table 	   		= 'tbl_WarehouseTransaction';
	$joins 	   		= '';
	$sql_where 		= 'WHERE `Lot_number` = "'.$return['lot_no'].'"';
	$sql_order 		= '';
	$sql_limit 		= '';
	$result 		= db_pps::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
	$ctr 			= 0;
	while($row = mysqli_fetch_array($result)){
		
		$ctr ++;
	}

	$return['ctr'] 		= $ctr;
	$return["result"] 	= json_encode($result); //return the serialize values
	echo json_encode($return);
	exit();

}

function get_data_by_lot_no(){
	require_once('../class/oop.php');
	$return 		= $_POST;
	$result 		= "";
	$array_fields 	= array('*');
	$table 	   		= 'tbl_WarehouseTransaction';
	$joins 	   		= 'JOIN tbl_Warehouse ON tbl_WarehouseTransaction.fkid = tbl_Warehouse.id';
	$sql_where 		= 'WHERE tbl_WarehouseTransaction.Lot_number = "'.$return['lot_no'].'"';
	$sql_order 		= '';
	$sql_limit 		= '';
	$result 		= db_pps::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
	$ctr 			= 0;
	while($row = mysqli_fetch_array($result)){
		$return['data'][$ctr] = $row;
		$ctr ++;
	}

	$return['ctr'] 		= $ctr;
	$return["result"] 	= json_encode($result); //return the serialize values
	echo json_encode($return);
	exit();

}

?>