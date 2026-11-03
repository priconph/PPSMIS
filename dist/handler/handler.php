<?php
	if(is_ajax()){
		if( isset($_GET['action']) && $_GET['action'] == 'qrcodeGenerator' ){
			include('../format/phpqrcode/qrlib.php');
			QRcode::png($_GET['value'], null, QR_ECLEVEL_L, 6, 0);
		}
		
		if(isset($_POST['action'])&&!empty($_POST['action'])){
			switch($_POST['action']){
				case 'import_production_monthly_data'	: import_production_monthly_data(); 	break;
				case 'load_latest_data'					: load_latest_data(); 					break;
				case 'load_latest_consolidated_data'	: load_latest_consolidated_data(); 		break;
				case 'load_prices'						: load_prices(); 						break;
				case 'delete_price'						: delete_price(); 						break;
				case 'load_suggestions_material'		: load_suggestions_material();			break;
				case 'load_new_consolidated_data'		: load_new_consolidated_data(); 		break;
				case 'load_material'					: load_material(); 						break;
				case 'load_consolidated_data'			: load_consolidated_data();				break;
				case 'update_material_info'				: update_material_info();				break;
				case 'update_cutoff_data'				: update_cutoff_data(); 				break;
				case 'update_range_of_date'				: update_range_of_date(); 				break;
				case 'update_production'				: update_production();					break;
				case "qrcodeGenerator"					: qrcodeGenerator(); 					break;
			}
		}
	} 
	
	function remove_spaces_and_other_characters($str){
		$str = str_replace(" ","",$str);
		$str = str_replace("\r","",$str);
		$str = str_replace("\n","",$str);
		return $str;
	}

	function is_ajax(){
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}

	function load_latest_data(){
		require_once('./query.php');
		$return 			= $_POST;
		$result 			= '';
		$array_fields 		= array('pmid_month','pmid_year');
		$table 	   			= 't_production_monthly_inventory_data';
		$joins 	   			= '';
		$sql_where 			= 'WHERE pmid_logdel="0"';
		$sql_order 			= 'ORDER BY pmid_year DESC, pmid_month DESC';
		$sql_limit 			= 'LIMIT 1';
		$result 			= db_ppsmis::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		if($row = mysqli_fetch_array($result)){
			$return['max_month'] 	= $row['pmid_month'];
			$return['max_year'] 	= $row['pmid_year'];
		}

		$result 			= '';
		$array_fields 		= array('pmid_month','pmid_year');
		$table 	   			= 't_production_monthly_inventory_data';
		$joins 	   			= '';
		$sql_where 			= 'WHERE pmid_logdel="0" AND pmid_month!="'.$return['max_month'].'"';
		$sql_order 			= 'ORDER BY pmid_year DESC, pmid_month DESC';
		$sql_limit 			= 'LIMIT 1';
		$result 			= db_ppsmis::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		if($row = mysqli_fetch_array($result)){
			$return['prev_max_month'] 	= $row['pmid_month'];
			$return['prev_max_year'] 	= $row['pmid_year'];
		}

		echo json_encode($return);			
	}

	function load_latest_consolidated_data(){
		require_once('./query.php');
		$return 			= $_POST;
		$result 			= '';
		$array_fields 		= array('cd_month','cd_year','cd_start_date','cd_end_date');
		$table 	   			= 't_cutoff_dates';
		$joins 	   			= '';
		$sql_where 			= '';
		$sql_order 			= 'ORDER BY cd_year DESC, cd_month DESC';
		$sql_limit 			= 'LIMIT 1';
		$result 			= db_ppsmis::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		$return['this_year'] = date('Y');
		if($row = mysqli_fetch_array($result)){
			$return['max_month'] 				= $row['cd_month'];
			$return['max_year'] 				= $row['cd_year'];
			$return['cd_start_date'] 			= $row['cd_start_date'];
			$return['cd_end_date'] 				= $row['cd_end_date'];
			$return['formatted_cd_start_date'] 	= date('M d',strtotime($row['cd_start_date']));
			$return['formatted_cd_end_date'] 	= date('M d',strtotime($row['cd_end_date']));
		}
		$return['username']		= $username;
		echo json_encode($return);			
	}

	function load_consolidated_data(){
		require_once('./query.php');
		$return 			= $_POST;
		$result 			= '';
		$array_fields 		= array('cd_start_date','cd_end_date');
		$table 	   			= 't_material_cost';
		$joins 	   			= ' LEFT JOIN t_cutoff_dates ON t_material_cost.mc_month=t_cutoff_dates.cd_month AND t_material_cost.mc_year=t_cutoff_dates.cd_year';
		$sql_where 			= 'WHERE mc_month="'.$return['data_month'].'" AND mc_year="'.$return['data_year'].'" AND mc_logdel="0"';
		$sql_order 			= '';
		$sql_limit 			= 'LIMIT 1';
		$result 			= db_ppsmis::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		$return['check'] 	= 0;
		if($row = mysqli_fetch_array($result)){
			$return['check'] 					= '1';
			$return['formatted_cd_start_date']	= date('M d',strtotime($row['cd_start_date']));
			$return['formatted_cd_end_date']	= date('M d',strtotime($row['cd_end_date']));
			$return['cd_start_date']			= $row['cd_start_date'];
			$return['cd_end_date']				= $row['cd_end_date'];
		}
		echo json_encode($return);			
	}

	function load_prices(){
		require_once('./query.php');
		$return 			= $_POST;
		$result 			= '';
		$array_fields 		= array('A.mc_id',
									'A.mc_boh_price',
									'A.mc_received_price',
									'A.mc_eoh_price',
									'A.mc_adate',
									'B.id',
									'B.ProductLine',
									'B.PartNumber',
									'B.MaterialType',
									'C.wmid_whs_balance_amount',
									'C.wmid_prodn_balance_amount',
									'C.wmid_ppc_adjustment');
		$table 	   			= 't_material_cost AS A';
		$joins 	   			= 'LEFT JOIN db_pps.tbl_Warehouse AS B ON A.mc_material_id=B.id LEFT JOIN t_consolidated_monthly_inventory_data AS C ON A.mc_id=C.wmid_mc_id';
		$sql_where 			= 'WHERE mc_logdel="0" AND mc_material_code="'.$return['material_code'].'" AND mc_month="'.$return['data_month'].'" AND mc_year="'.$return['data_year'].'"';
		$sql_order 			= 'ORDER BY mc_id ASC';
		$sql_limit 			= '';
		$result 			= db_ppsmis::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		// $return['sd'] 		= db_ppsmis::getInstance()->query_select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		$ctr 				= 0;
		while($row = mysqli_fetch_array($result)){
			if($ctr==0){
				$return['id'][0] 						= $row['id'];
				$return['PartNumber'][0] 				= $row['PartNumber'];
				$return['ProductLine'][0] 				= $row['ProductLine'];
				$return['MaterialType'][0] 				= $row['MaterialType'];
			}
			$return['mc_id'][$ctr] 						= $row['mc_id'];
			$return['mc_boh_price'][$ctr] 				= floatval($row['mc_boh_price']);
			$return['mc_received_price'][$ctr] 			= floatval($row['mc_received_price']);
			$return['mc_eoh_price'][$ctr] 				= floatval($row['mc_eoh_price']);
			$return['wmid_whs_balance_amount'][$ctr] 	= floatval($row['wmid_whs_balance_amount']);
			$return['wmid_prodn_balance_amount'][$ctr] 	= floatval($row['wmid_prodn_balance_amount']);
			// $return['mc_received_quantity'][$ctr] 		= $row['mc_received_quantity'];
			$return['wmid_ppc_adjustment'][$ctr] 		= floatval($row['wmid_ppc_adjustment']);
			$return['mc_adate'][$ctr] 					= $row['mc_adate'];
			$ctr++;
		}

		if($ctr==0){
			$result 			= '';
			$array_fields 		= array('id','MaterialType','PartNumber','ProductLine');
			$table 	   			= 'tbl_Warehouse';
			$joins 	   			= '';
			$sql_where 			= 'WHERE PartNumber ="'.$return['material_code'].'"';
			$sql_order 			= '';
			$sql_limit 			= '';
			$result 			= db_pps::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
			while($row = mysqli_fetch_array($result)){
				$return['id']			= $row['id'];
				$return['MaterialType']	= $row['MaterialType'];
				$return['PartNumber']	= $row['PartNumber'];
				$return['ProductLine']	= $row['ProductLine'];
			}
			$return['ctr'] = 0;
		}
		else{
			$return['ctr'] = $ctr;
		}

		echo json_encode($return);			
	}

	function load_suggestions_material(){
		require_once('./query.php');
		$return 			= $_POST;
		$result 			= '';
		$array_fields 		= array('PartNumber');
		$table 	   			= 'tbl_Warehouse';
		$joins 	   			= '';
		$sql_where 			= 'WHERE PartNumber LIKE "%'.$return['partial_material_code'].'%"';
		$sql_order 			= '';
		$sql_limit 			= 'LIMIT 10';
		$result 			= db_pps::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		$ctr 				= 0;
		while($row = mysqli_fetch_array($result)){
			$return['PartNumber'][$ctr] 		= $row['PartNumber'];
			$ctr++;
		}
		$return['ctr'] = $ctr;
		echo json_encode($return);			
	}

	function load_material(){
		require_once('./query.php');
		$return 			= $_POST;
		$result 			= '';
		$array_fields 		= array('MaterialType','id');
		$table 	   			= 'tbl_Warehouse';
		$joins 	   			= '';
		$sql_where 			= 'WHERE PartNumber ="'.$return['partial_material_code'].'"';
		$sql_order 			= '';
		$sql_limit 			= '';
		$result 			= db_pps::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		while($row = mysqli_fetch_array($result)){
			$return['MaterialType']	= $row['MaterialType'];
			$return['id']			= $row['id'];
		}
		echo json_encode($return);			
	}

	function load_new_consolidated_data(){
		require_once('./query.php');
		$return 			= $_POST;

		$previous_month     = date('n', strtotime($return['data_year'].'-'.$return['data_month'].'-01 -1 months'));
	    $previous_year      = date('Y', strtotime($return['data_year'].'-'.$return['data_month'].'-01 -1 months'));
		$array_fields 		= array('mc_material_id','mc_material_code','mc_boh_price','mc_received_price','mc_eoh_price','mc_month','mc_year','mc_username');
		$table 	   			= 't_material_cost';
		$select 			= 'SELECT mc_material_id,mc_material_code,mc_boh_price,mc_received_price,mc_eoh_price,"'.$return['data_month'].'","'.$return['data_year'].'","'.$return['username'].'" FROM t_material_cost WHERE mc_logdel=0 AND mc_month="'.$previous_month.'" AND mc_year="'.$previous_year.'"';
		db_ppsmis::getInstance()->insert_selected_query($table,$array_fields,$select);


		unset($array_fields);
		$result 			= '';
		$table 				= 't_cutoff_dates';
		$array_fields 		= array('cd_month',
									'cd_year',
									'cd_start_date',
									'cd_end_date',
									'cd_username');
		$array_values		= array($return['data_month'],
									$return['data_year'],
									$return['txt_new_start_date'],
									$return['txt_new_end_date'],
									$return['username']);
		db_ppsmis::getInstance()->insert_query($table,$array_fields,$array_values);		


		// $result 			= '';
		// $array_fields 		= array('cd_id');
		// $table 	   			= 't_cutoff_dates';
		// $joins 	   			= '';
		// $sql_where 			= 'WHERE cd_month ="'.$return['data_month'].'" AND cd_year="'.$return['data_year'].'"';
		// $sql_order 			= '';
		// $sql_limit 			= '';
		// $result 			= db_ppsmis::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		// if($row != mysqli_fetch_array($result)){  

		// 	unset($array_fields);
		// 	$result 			= '';
		// 	$table 				= 't_cutoff_dates';
		// 	$array_fields 		= array('cd_month',
		// 								'cd_year',
		// 								'cd_start_date',
		// 								'cd_end_date',
		// 								'cd_username');
		// 	$array_values		= array($return['data_month'],
		// 								$return['data_year'],
		// 								'SELECT ');
		// 	db_ppsmis::getInstance()->insert_query($table,$array_fields,$array_values);		
		// }

	  


		echo json_encode($return);
	}


	function import_production_monthly_data(){
		require_once( './query.php');
		require_once( '../../../media/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php');
		require_once( '../../../media/PHPExcel-1.8/Classes/PHPExcel.php');

		$return 						= array();
		$month 							= date('m',strtotime($_POST['txt_month_year']));
		$year 							= date('Y',strtotime($_POST['txt_month_year']));
		$input_file_name 				= $_FILES['txt_inventory_file']['tmp_name'];
		$original_file_name	 			= $_FILES['txt_inventory_file']['name'];
		$ext 							= pathinfo($original_file_name, PATHINFO_EXTENSION);
		$max_row = 100;
		$excel_content = array();
		if(!is_numeric($max_row) || $max_row <= 0){
			$excel_content['A1'] = "Incorrect max row value";
			return $excel_content;
		}		

		try {
			$inputFileType 	= PHPExcel_IOFactory::identify($input_file_name);
			$objReader 		= PHPExcel_IOFactory::createReader($inputFileType);
			$objReader->setLoadSheetsOnly('Resin');
			$objPHPExcel 	= $objReader->load($input_file_name);
		} catch(Exception $e){
			die('Error loading file "'.pathinfo($input_file_name,PATHINFO_BASENAME).'": '.$e->getMessage());
		}

		$sheet 		= $objPHPExcel->getActiveSheet();
		$max_row 	= $sheet->getHighestRow();
		$max_column = 'L';

		$row_ctr = 5;
		$col_ctr = 'A';
		for($x = $row_ctr; $x < $max_row; $x++){
			for($y = $col_ctr; $y != $max_column; $y++ ){
	            $cell = $sheet->getCell($y.$x);			
	    	    $excel_content[$y.$x] = $sheet->getCell($y.$x)->getValue(); 
	            if(PHPExcel_Shared_Date::isDateTime($cell)&&$excel_content[$y.$x]!==null) {
	                $InvDate = date("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($excel_content[$y.$x]));
	                $excel_content[$y.$x] = $InvDate;
	            }else{
	               $excel_content[$y.$x] = $sheet->getCell($y.$x)->getValue(); 
	            }

	            if(!isset($excel_content[$y.$x])){
					$excel_content[$y.$x] = '';
	            }
			}
		}

		$table 				= 't_production_monthly_inventory_data';
		$array_fields 		= array('pmid_logdel');
		$array_values		= array('1');
		$idname 			= 'pmid_month';
		$pkid				= $month;
		$idname2 			= 'pmid_year';
		$pkid2				= $year;
		db_ppsmis::getInstance()->update_query_2_conditions($table,$array_fields,$array_values,$pkid,$idname,$pkid2,$idname2);
		// $excel_content['shiiit'] = 'shitty co-workers';
		for($mba=5;$mba<$x;$mba++){
			if($excel_content['A'.$mba]!=''&&$excel_content['B'.$mba]){
				$return 			= $_POST;
				$result 			= '';
				$table 				= 't_production_monthly_inventory_data';
				$array_fields 		= array('pmid_month',
											'pmid_year',
											'pmid_material_code',
											'pmid_material_name',
											'pmid_material_type',
											'pmid_mix_value',
											'pmid_virgin_value',
											'pmid_ticket_number',
											'pmid_updater_username');
					$array_values		= array($month,
												$year,
												$excel_content['A'.$mba],
												$excel_content['B'.$mba],
												'1',
												$excel_content['C'.$mba],
												$excel_content['D'.$mba],
												$excel_content['G'.$mba],
												$return['username']);
				db_ppsmis::getInstance()->insert_query($table,$array_fields,$array_values);				
			}
		}

		unset($excel_content);

		$excel_content = array();
		if(!is_numeric($max_row) || $max_row <= 0){
			$excel_content['A1'] = "Incorrect max row value";
			return $excel_content;
		}		

		try {
			$inputFileType 	= PHPExcel_IOFactory::identify($input_file_name);
			$objReader 		= PHPExcel_IOFactory::createReader($inputFileType);
			$objReader->setLoadSheetsOnly('Metal Parts');
			$objPHPExcel 	= $objReader->load($input_file_name);
		} catch(Exception $e){
			die('Error loading file "'.pathinfo($input_file_name,PATHINFO_BASENAME).'": '.$e->getMessage());
		}

		$sheet 		= $objPHPExcel->getActiveSheet();
		$max_row 	= $sheet->getHighestRow();
		$max_column = $sheet->getHighestColumn();
		// $max_row 	= '50';
		// $max_column = 'F';

		$row_ctr = 5;
		$col_ctr = 'A';
		for($x = $row_ctr; $x < $max_row; $x++){
			for($y = $col_ctr; $y != $max_column; $y++ ){
	            $cell = $sheet->getCell($y.$x);			
	    	    $excel_content[$y.$x] = $sheet->getCell($y.$x)->getValue(); 
	            if(PHPExcel_Shared_Date::isDateTime($cell)&&$excel_content[$y.$x]!==null) {
	                $InvDate = date("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($excel_content[$y.$x]));
	                $excel_content[$y.$x] = $InvDate;
	            }else{
	               $excel_content[$y.$x] = $sheet->getCell($y.$x)->getValue(); 
	            }

	            if(!isset($excel_content[$y.$x])){
					$excel_content[$y.$x] = '';
	            }
			}
		}

		for($mba=5;$mba<$x;$mba++){
			$return 			= $_POST;
			$result 			= '';
			$table 				= 't_production_monthly_inventory_data';
			$array_fields 		= array('pmid_month',
										'pmid_year',
										'pmid_material_code',
										'pmid_material_name',
										'pmid_material_type',
										'pmid_actual_value',
										'pmid_ticket_number',
										'pmid_updater_username');
				$array_values		= array($month,
											$year,
											$excel_content['A'.$mba],
											$excel_content['B'.$mba],
											'2',
											$excel_content['C'.$mba],
											$excel_content['D'.$mba],
											$return['username']);
			db_ppsmis::getInstance()->insert_query($table,$array_fields,$array_values);
		}

		echo json_encode($excel_content);		
	}

	function update_material_info(){
		require_once('./query.php');
		$return 			= $_POST;
		$result 			= '';
		$array_fields 		= array('MaterialType','PartNumber','ProductLine');
		$table 	   			= 'tbl_Warehouse';
		$joins 	   			= '';
		$sql_where 			= 'WHERE id="'.$return['txt_whs_id'].'"';
		$sql_order 			= '';
		$sql_limit 			= '';
		$result 			= db_pps::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		if($row = mysqli_fetch_array($result)){
			if($row['ProductLine']==$return['txt_allocation']&&$row['PartNumber']==$return['txt_material_code']&&$row['MaterialType']==$return['txt_description']){
				$array_fields 		= array('ProductLine',
											'PartNumber',
											'MaterialType');
				$array_values		= array($return['txt_allocation'],
											$return['txt_material_code'],
											$return['txt_description']);
			}
			else{
				$array_fields 		= array('ProductLine',
											'PartNumber',
											'MaterialType',
											'LastUpdate',
											'Username');
				$array_values		= array($return['txt_allocation'],
											$return['txt_material_code'],
											$return['txt_description'],
											date('Y-m-d H:i:s'),
											$return['username']);
			}
			$result 			= '';
			$table        		= 'tbl_Warehouse';
			$pkid 				= $return['txt_whs_id'];
			$idname 			= 'id';	
			$result 			= db_pps::getInstance()->update_query($table,$array_fields,$array_values,$pkid,$idname);	    	
		}

		for($rka=1;$rka<=$return['txt_max_row'];$rka++){
			$array_fields 		= array('mc_boh_price',
										'mc_received_price',
										'mc_eoh_price',
										'mc_username');
			$array_values		= array($return['txt_boh_price_'.$rka],
										$return['txt_received_price_'.$rka],
										$return['txt_eoh_price_'.$rka],
										$return['username']);
			$result 			= '';
			$table        		= 't_material_cost';
			$pkid 				= $return['txt_mc_id_'.$rka];
			$idname 			= 'mc_id';
			db_ppsmis::getInstance()->update_query($table,$array_fields,$array_values,$pkid,$idname);

        	unset($array_fields_child);
			$array_fields_child = array('wmid_mc_id');
			$table 	   			= 't_consolidated_monthly_inventory_data';
			$joins 	   			= '';
			$sql_where 			= 'WHERE wmid_mc_id ="'.$return['txt_mc_id_'.$rka].'"';
			$sql_order 			= '';
			$sql_limit 			= '';
			$result_child 		= db_ppsmis::getInstance()->select_query($array_fields_child,$table,$joins,$sql_where,$sql_order,$sql_limit);
			// $return['sd'] 		= db_ppsmis::getInstance()->query_select_query($array_fields_child,$table,$joins,$sql_where,$sql_order,$sql_limit);
			if($row = mysqli_fetch_array($result_child)){
        		unset($array_fields_grandchild);
        		unset($array_values_grandchild);
				$array_fields_grandchild 	= array('wmid_whs_balance_amount',
													'wmid_prodn_balance_amount',
													'wmid_ppc_adjustment',
													'wmid_username');
				$array_values_grandchild	= array($return['txt_whs_remaining_'.$rka],
													$return['txt_prodn_remaining_'.$rka],
													$return['txt_adjustment_'.$rka],
													$return['username']);
				$result 			= '';
				$table        		= 't_consolidated_monthly_inventory_data';
				$pkid 				= $return['txt_mc_id_'.$rka];
				$idname 			= 'wmid_mc_id';
				db_ppsmis::getInstance()->update_query($table,$array_fields_grandchild,$array_values_grandchild,$pkid,$idname);			
				$return['sd'] = db_ppsmis::getInstance()->query_update_query($table,$array_fields_grandchild,$array_values_grandchild,$pkid,$idname);
			}
			else{
        		unset($array_fields_grandchild);
        		unset($array_values_grandchild);
				$array_fields_grandchild 	= array('wmid_month',
													'wmid_year',
													'wmid_whs_balance_amount',
													'wmid_prodn_balance_amount',
													'wmid_ppc_adjustment',
													'wmid_mc_id',
													'wmid_username');
				$array_values_grandchild	= array($return['data_month'],
													$return['data_year'],
													$return['txt_whs_remaining_'.$rka],
													$return['txt_prodn_remaining_'.$rka],
													$return['txt_adjustment_'.$rka],
													$return['txt_mc_id_'.$rka],
													$return['username']);
				$table        		= 't_consolidated_monthly_inventory_data';
				db_ppsmis::getInstance()->insert_query($table,$array_fields_grandchild,$array_values_grandchild);
			}
		}
		
		if($return['txt_add_max_row']>0){
			for($mmb=1;$mmb<=$return['txt_add_max_row'];$mmb++){
				$array_fields 		= array('mc_material_code',
											'mc_material_id',
											'mc_boh_price',
											'mc_received_price',
											'mc_eoh_price',
											'mc_month',
											'mc_year',
											'mc_adate',
											'mc_username');
				$array_values		= array($return['txt_material_code'],
											$return['txt_whs_id'],
											$return['txt_adn_boh_price_'.$mmb],
											$return['txt_adn_received_price_'.$mmb],
											$return['txt_adn_eoh_price_'.$mmb],
											$return['data_month'],
											$return['data_year'],
											date('Y-m-d H:i:s'),
											$return['username']);
				$table        		= 't_material_cost';
				$as_target 			= 'mc_id';
				$as_name 			= 'mc_id';
				$return_id 		= db_ppsmis::getInstance()->insert_query_return_id($table,$array_fields,$array_values,$as_target,$as_name);

				if($return_row = mysqli_fetch_array($return_id)){
	        		unset($array_fields);
	        		unset($array_values);
					$array_fields 		= array('wmid_month',
												'wmid_year',
												'wmid_whs_balance_amount',
												'wmid_prodn_balance_amount',
												'wmid_ppc_adjustment',
												'wmid_mc_id',
												'wmid_username');
					$array_values		= array($return['data_month'],
												$return['data_year'],
												$return['txt_adn_whs_remaining_'.$mmb],
												$return['txt_adn_prodn_remaining_'.$mmb],
												$return['txt_adjustment_'.$mmb],
												$return_row['mc_id'],
												$return['username']);
					$table        		= 't_consolidated_monthly_inventory_data';
					db_ppsmis::getInstance()->insert_query($table,$array_fields,$array_values);
					// $return['sd'] = db_ppsmis::getInstance()->query_insert_query($table,$array_fields,$array_values);
				}
			}
		}

		echo json_encode($return);
	}

	function update_cutoff_data(){
		require_once('./query.php');
		$return 			= $_POST;
		$result 			= '';
		$array_fields 		= array('cd_id');
		$table 	   			= 't_cutoff_dates';
		$joins 	   			= '';
		$sql_where 			= 'WHERE cd_month="'.$return['slt_add_data_month'].'" AND cd_year="'.$return['slt_add_data_year'].'"';
		$sql_order 			= '';
		$sql_limit 			= '';
		$result 			= db_ppsmis::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		if($row = mysqli_fetch_array($result)){
			$table        		= 't_cutoff_dates';
			$array_fields 		= array('cd_start_date','cd_end_date');
			$array_values		= array($return['txt_start_date'],$return['txt_end_date']);
			$pkid 				= $return['slt_add_data_month'];
			$idname 			= 'cd_month';
			$pkid2				= $return['slt_add_data_year'];
			$idname2			= 'cd_year';
			db_ppsmis::getInstance()->update_query_2_conditions($table,$array_fields,$array_values,$pkid,$idname,$pkid2,$idname2);
			$return['sd'] 						= db_ppsmis::getInstance()->query_update_query_2_conditions($table,$array_fields,$array_values,$pkid,$idname,$pkid2,$idname2);
			$return['data_count'] 				= 1;
			$return['formatted_cd_start_date']	= date('M d',strtotime($return['txt_start_date']));
			$return['formatted_cd_end_date']	= date('M d',strtotime($return['txt_end_date']));
		}
		else{
			$table_child 				= 't_cutoff_dates';
			$array_fields_child 		= array('cd_month',
												'cd_year',
												'cd_start_date',
												'cd_end_date',
												'cd_username');
			$array_values_child 		= array($return['slt_add_data_month'],
												$return['slt_add_data_year'],
												$return['txt_start_date'],
												$return['txt_end_date'],
												$return['username']);
			db_ppsmis::getInstance()->insert_query($table_child,$array_fields_child,$array_values_child);	
			$return['data_count'] = 0;

		}
		echo json_encode($return);
	}

	function delete_price(){
		require_once('./query.php');
		$return 			= $_POST;
		$result 			= '';
		$table        		= 't_material_cost';
		$array_fields 		= array('mc_logdel');
		$array_values		= array('1');
		$pkid 				= $return['mc_id'];
		$idname 			= 'mc_id';	
		$result 			= db_ppsmis::getInstance()->update_query($table,$array_fields,$array_values,$pkid,$idname);	    
		echo json_encode($return);	
	}

	function update_production(){
		require_once('./query.php');
		$return 			= $_POST;
		$result 			= '';
		$table 	   			= 't_production_monthly_inventory_data';
		$array_fields 		= array('pmid_mix_value','pmid_virgin_value','pmid_updater_username');
		$array_values		= array($return['txt_edit_mix'], $return['txt_edit_virgin'], $return['username']);
		$pkid 				= $return['txt_pkid'];
		$idname 			= 'pmid_id';	
		$result 			= db_ppsmis::getInstance()->update_query($table,$array_fields,$array_values,$pkid,$idname);
		$return['x'] = $return['txt_edit_mix'];
		echo json_encode($return);
	}


?>