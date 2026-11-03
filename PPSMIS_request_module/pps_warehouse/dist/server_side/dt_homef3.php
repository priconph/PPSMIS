<?php
	/*
	 * Script:    DataTables server-side script for PHP and MySQL
	 * Copyright: 2010 - Allan Jardine
	 * License:   GPL v2 or BSD (3-point)
	 */

	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Easy set variables
	 */
	
	/* Array of database columns which should be read and sent back to DataTables. Use a space where
	 * you want to insert a non-database field (for example a counter or static image)
	 */

	session_start();//$_SESSION['username']
	$session_username 	= isset($_SESSION['username'])?$_SESSION['username']:'';
	$session_department 	= isset($_SESSION['department'])?$_SESSION['department']:'';
	$current_page 		= isset($_GET['current_page'])?$_GET['current_page']:'';

	$aColumns = array(  
			'status',
			'control_number',
			'partcode',
			'partname',
			'Model',//boh field
		 	'qty',
			'created_on',
		 	'created_by',
			'destination',
			'r_remarks',
			'issuanceqty',
			'issuancedate',//init sort
			'issuedby',
			'i_remarks',
			'opt_remarks',
			'acknowledgedby',
			'acknowledged',
			'receive_date',
			'pkid',
			'cancelled',
			'pkid_issuance',
		);
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "pkid";
	
	/* DB table to use */
	$sTable = "
		(
			SELECT 	a.pkid,
					a.created_on,
					a.created_by,
					a.control_number,
					a.qty,
					a.destination,
					a.r_remarks,
					a.i_remarks,
					a.opt_remarks,
					a.cancelled,
					a.acknowledgedby,
					a.acknowledged,
					a.receive_date,
					b.partcode,
					b.partname,
					b.Model,
					c.pkid_issuance,
					c.issuanceqty,
					c.issuancedate,
					c.issuedby,

					CASE WHEN c.issuanceqty IS NOT NULL 
				       THEN 0
				       ELSE 1
					END AS status

			FROM tbl_request a
			LEFT JOIN tbl_itemList b
			ON a.fk_itemlist = b.pkid_itemlist
			LEFT JOIN tbl_issuance c
			ON a.fk_issuance  = c.pkid_issuance
			WHERE a.deleted = 0 and a.created_by = 'aldequito' or a.created_by = 'kareforma'
			ORDER BY pkid DESC
		) tbl1
	";

	require_once('../db_config/config.php');
	/* Database connection information */
	$gaSql['user']       = $username;
	$gaSql['password']   = $password;
	$gaSql['db']         = $db_name;
	$gaSql['server']     = $server;
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
	 * no need to edit below this line
	 */
	
	/* 
	 * MySQL connection
	 */
	$gaSql['link'] =  mysql_pconnect( $gaSql['server'], $gaSql['user'], $gaSql['password']  ) or
		die( 'Could not open connection to server' );
	
	mysql_select_db( $gaSql['db'], $gaSql['link'] ) or 
		die( 'Could not select database '. $gaSql['db'] );
	
	
	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
			mysql_real_escape_string( $_GET['iDisplayLength'] );
	}
	
	
	/*
	 * Ordering
	 */
	if ( isset( $_GET['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
		{
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
				 	".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}
	
	
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	$sWhere = "";


	// $device_name = $_GET['sr_name'];
	// $m_cycle 	 = $_GET['sr_m'];
	// $cavty 	 	 = $_GET['sr_c'];
	// $drawing 	 = $_GET['sr_d'];
	// $l_update 	 = $_GET['sr_l'];	

	// if($device_name != ""){
	// 	$device_name_query = ' `v_dmcms_main_page`.`d_name` LIKE "%'.$device_name.'%"';
	// 	$sWhere = $device_name_query;
	// }

	// if($m_cycle != ""){
	// 	$m_cycle_query = ' `v_dmcms_main_page`.`dc_maintenance_cycle` = "'.$m_cycle.'"';
	// 	$sWhere = $m_cycle_query;
	// }

	// if($cavty != ""){
	// 	$cavty_query = ' `v_dmcms_main_page`.`d_cavty` = "'.$cavty.'"';
	// 	$sWhere = $cavty_query;
	// }

	// if($drawing != ""){
	// 	$drawing_query = ' `v_dmcms_main_page`.`d_drawing` = "'.$drawing.'"';
	// 	$sWhere = $drawing_query;
	// }

	// if($l_update != ""){
	// 	$l_update_query = ' `v_dmcms_main_page`.`dc_aupdate` = "'.$l_update.'"';
	// 	$sWhere = $l_update_query;
	// }

	if ( $_GET['sSearch'] != "" )
	{
		$sWhere = "WHERE (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	/* Individual column filtering */
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
		if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
		{
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE ";
			}
			else
			{
				$sWhere .= " AND ";
			}
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
		}
	}
	


	//====== CUSTOM

	if($sWhere == ""){
		$sWhere .= " WHERE pkid != -1";
	}else{
		$sWhere .= " AND pkid != -1";
	}

	if($current_page!='s') {
		if($current_page=='req'){
			// $sWhere .= " AND created_by = '$session_username'"; // CHAN APRIL 12, 2024
		}
		else{
		}
	}
	else{
			$sWhere .= " AND cancelled = '0'";
	}
	// $sOrder = "ORDER BY fk_batch_number ASC, if(record_number1 = '' or record_number1 is null,1,0), record_number1 ASC, record_number ASC";
	// $sOrder = "ORDER BY created_on DESC";//both should DESC
	// $sOrder = "ORDER BY issuancedate ASC";//both should DESC


	//======/CUSTOM


	
	/*
	 * SQL queries
	 * Get data to display
	 */
	$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit
	";
	// echo "string".$sQuery;
	$rResult = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	// echo $sQuery;
	/* Data set length after filtering */
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	$rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	/* Total data set length */
	$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM   $sTable
	";
	$rResultTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	$aResultTotal = mysql_fetch_array($rResultTotal);
	$iTotal = $aResultTotal[0];
	
	
	/*
	 * Output
	 */
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);

	require_once('../../../dist/handler/functions.php');
	while ( $aRow = mysql_fetch_array( $rResult ) ){
		$row = array();
		unset($row);

		$pkid = '<span class = "row_pkid" hidden>'.$aRow['pkid'].'</span>';
		$control_number = '<span class = "row_control_number" hidden>'.$aRow['control_number'].'</span>';
		$status = '<div class="d-flex justify-content-center"><i class="fa fa-clock fa-lg text-secondary status_pending" title="Pending"></i></div>';
		$acknowledged = '<div class="d-flex justify-content-center"><i class="fa fa-clock fa-lg text-secondary status_unacknowledged" title="Unacknowledged"></i></div>';
		$print = "";

		if( $aRow['issuanceqty'] ){
			// CHAN - 20/03/2024
			$print = "<div class='d-flex justify-content-center'><a href='#' onclick='print_preview(".$aRow["pkid"].")'><i class='fa fa-print' style='color:green'></i></a></div>";

			if( $aRow['qty'] == $aRow['issuanceqty'] ){
				$status = '<div class="d-flex justify-content-center"><i class="fa fa-check-circle fa-lg text-success status_served" title="Served"></i></div>';
				$print = "<div class='d-flex justify-content-center'><a href='#' onclick='print_preview(".$aRow["pkid"].")'><i class='fa fa-print' style='color:green'></i></a></div>";
			}
			elseif( $aRow['qty'] < $aRow['issuanceqty'] ){
				$status = '<div class="d-flex justify-content-center"><i class="fa fa-exclamation-circle fa-lg text-danger status_excess" title="Excess"></i></div>';
			}
			elseif( $aRow['qty'] > $aRow['issuanceqty'] ){
				// $status = '<i class="fa fa-exclamation-circle fa-lg text-warning status_incomplete" title="Incomplete"></i>';
				$print = "<div class='d-flex justify-content-center'><a href='#' onclick='print_preview(".$aRow["pkid"].")'><i class='fa fa-print' style='color:green'></i></a></div>";

			}
		}
		if( $aRow['cancelled'] == 1 ){
			$status = '<div class="d-flex justify-content-center"><i class="fa fa-ban fa-lg text-danger status_cancelled" title="Cancelled"></i></div>';
		}

		if( $aRow['acknowledged'] == 1 ){
			$acknowledged = '<div class="d-flex justify-content-center"><i class="fa fa-check-square fa-lg text-success status_acknowledged" title="Acknowledged"></i></div>';
			// $print = '<div class="d-flex justify-content-center"><button title="Print Lot No." type="button" class="btn btn-success btn-sm print_lot_no" "'.$aRow["pkid"].'"><i class="fa fa-print fa-sm"></i></div>';
			$print = "<div class='d-flex justify-content-center'><a href='#' onclick='print_preview(".$aRow["pkid"].")'><i class='fa fa-print' style='color:green'></i></a></div>";
		}


		$rqtd_qty 	= $aRow['qty'];
		$issued_qty = $aRow['issuanceqty'];
		$blce_qty = ($rqtd_qty - $issued_qty);

		if ($current_page == 's'){
			$print_qr = $print;
		}else{
			$print_qr = '';
		}
		$row[] = $print_qr;
		$row[] = $status.$pkid.$control_number;
		$row[] = $aRow['control_number'];
		$row[] = $aRow['partcode'];
		$row[] = $aRow['partname'];
		$row[] = $aRow['Model'];
		$row[] = $aRow['qty'];
		$row[] = datetime_convert( $aRow['created_on'] );
		$row[] = $aRow['created_by'];
		$row[] = $aRow['destination'];
		$row[] = $aRow['r_remarks'];

		$row[] = $aRow['issuanceqty'];
		// $row[] = datetime_convert( $aRow['issuancedate'] );
		$row[] = $aRow['issuancedate'];
		$row[] = $aRow['issuedby'];
		$row[] = $aRow['i_remarks'];
		$row[] = $aRow['opt_remarks'];
		$row[] = $blce_qty;


		$row[] = $aRow['acknowledgedby'];
		$row[] = $acknowledged;
		$row[] = datetime_convert( $aRow['receive_date'] );

		array_push($output['aaData'],$row);
	}
	echo json_encode( $output );
?>