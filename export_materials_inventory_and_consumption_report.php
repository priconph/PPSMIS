<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



	$excel_class = './dist/excel_export/class/excel.php';
	require_once("./dist/handler/query.php");

	if(file_exists($excel_class)){
		include($excel_class);
	}
	else{
		echo 'File '.$excel_class.' does not exist';
		exit;
	}
	
	$font = array(
		'font'  => array(
		    'size'  => 10,
		    'name'  => 'Arial'
		)
	);

	$vertical_align_middle = array(
        'alignment' => array(
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        )
    );

	$border_thick = array(
	    'borders' => array(
	        'outline' => array(
	            'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
	        ),
	    ),
	);

	$border_double = array(
	    'borders' => array(
	        'outline' => array(
	            'style' => PHPExcel_Style_Border::BORDER_DOUBLE,
	        ),
	    ),
	);


	//FILL COLOR
	$fill_blue = array(
		'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('rgb' => 'b3ecff')
		)
	);
	$fill_indigo = array(
		'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('rgb' => 'c3d6f4')
		)
	);
	$fill_cream = array(
		'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('rgb' => 'f2e1b5')
		)
	);
	$fill_yellow = array(
		'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('rgb' => 'ffff99')
		)
	);
	$fill_green = array(
		'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('rgb' => 'b5ffc9')
		)
	);


	$excel = new EXCEL;
	$excel->title = 'Direct Materials Inventory';
	$excel->add_page();
	$excel->set_active_sheet(0);
	$excel->getDefaultStyle()->applyFromArray($font)->applyFromArray($vertical_align_middle);
	$excel->getActiveSheet()->setAutoFilter('A4:X4');
	$excel->getActiveSheet()->freezePane('G5');
	$array_cell = array('A','B','C','D','E','F','G','H','I','J','K','L');
	$array_width = array('13','13','30','10','10','10','10','10','10','10','10','10');
	$excel->set_width($array_cell,$array_width);
	$array_row = array('1','2');
	$array_height = array(30,20);
	$excel->set_height($array_row,$array_height);
	$excel->set_font('A1',true,'','6ff298',15,'Arial Narrow');
	$excel->merge_cells('A1:X1');
	$excel->place_value('A1','PPS (Direct Materials Inventory & Consumption Report) - '.date('F Y', strtotime($_GET['data_year'].'-'.$_GET['data_month'].'-01')),'string','left',true);
	$excel->set_font('A2',false,'','6ff298',12,'Arial Narrow');
	$excel->merge_cells('A2:X2');
	$excel->place_value('A2',date('F d, Y'),'string','left',true);
	$excel->set_borders('A3:X4',1,1,1,1);	
	$excel->merge_cells('A3:A4');
	$excel->place_value('A3','ALLOCATION','string','center',true);
	$excel->merge_cells('B3:B4');
	$excel->place_value('B3','MATERIAL CODE','string','center',true);
	$excel->merge_cells('C3:C4');
	$excel->place_value('C3','DESCRIPTION','string','center',true);
	$excel->merge_cells('D3:D4');
	$excel->place_value('D3','BOH U/P','string','center',true);
	$excel->merge_cells('E3:E4');
	$excel->place_value('E3','RECEIVED U/P','string','center',true);
	$excel->merge_cells('F3:F4');
	$excel->place_value('F3','RECEIVED U/P','string','center',true);
	$excel->merge_cells('G3:H3');
	$excel->place_value('G3','BOH(QUANTITY BASED)','string','center',true);
	$excel->place_value('G4','Warehouse','string','center',true);
	$excel->place_value('H4','Production','string','center',true);
	$excel->merge_cells('J3:K3');
	$excel->merge_cells('I3:I4');
	$excel->place_value('I3','TOTAL','string','center',true);
	$excel->merge_cells('J3:L3');
	$excel->place_value('J4','Received','string','center',true);
	$excel->place_value('K4','Adjmt.','string','center',true);
	$excel->place_value('L4','Used','string','center',true);
	$excel->merge_cells('M3:N3');
	$excel->place_value('M3','EOH(QUANTITY BASED)','string','center',true);
	$excel->place_value('M4','Warehouse','string','center',true);
	$excel->place_value('N4','Production','string','center',true);
	$excel->merge_cells('O3:O4');
	$excel->place_value('O3','TOTAL','string','center',true);
	$excel->merge_cells('P3:Q3');
	$excel->place_value('P3','BOH(AMOUNT BASED)','string','center',true);
	$excel->place_value('P4','Warehouse','string','center',true);
	$excel->place_value('Q4','Production','string','center',true);
	$excel->merge_cells('R3:R4');
	$excel->place_value('R3','TOTAL','string','center',true);

	$excel->merge_cells('S3:U3');
	$excel->place_value('S4','Received','string','center',true);
	$excel->place_value('T4','Adjmt.','string','center',true);
	$excel->place_value('U4','Used','string','center',true);
	$excel->merge_cells('V3:W3');
	$excel->place_value('V3','EOH(AMOUNT BASED)','string','center',true);
	$excel->place_value('V4','Warehouse','string','center',true);
	$excel->place_value('W4','Production','string','center',true);
	$excel->merge_cells('X3:X4');
	$excel->place_value('X3','TOTAL','string','center',true);
	$excel->set_font('A3:X4',true,'','FFFF00',9,'Arial Narrow');

    $previous_month     = date('n', strtotime($_GET['data_year'].'-'.$_GET['data_month'].'-01 -1 months'));
    $previous_year      = date('Y', strtotime($_GET['data_year'].'-'.$_GET['data_month'].'-01 -1 months'));
   
    $result             = '';
    $array_fields       = array('cd_start_date','cd_end_date');
    $table              = 't_cutoff_dates';
    $joins              = '';
    $sql_where          = 'WHERE cd_month="'.$previous_month.'" AND cd_year="'.$previous_year.'"';
    $sql_order          = '';
    $sql_limit          = '';
    $result             = db_ppsmis::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
    if($row = mysqli_fetch_array($result)){
        $previous_start_date   = $row['cd_start_date'];
        $previous_end_date     = $row['cd_end_date'];
    }

    $result             = '';
    $array_fields       = array('cd_start_date','cd_end_date');
    $table              = 't_cutoff_dates';
    $joins              = '';
    $sql_where          = 'WHERE cd_month="'.$_GET['data_month'].'" AND cd_year="'.$_GET['data_year'].'"';
    $sql_order          = '';
    $sql_limit          = '';
    $result             = db_ppsmis::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
    if($row = mysqli_fetch_array($result)){
        $current_start_date   = $row['cd_start_date'];
        $current_end_date     = $row['cd_end_date'];
    }

    $result             = '';
	$array_fields 		= array('A.mc_id',
                                'A.mc_boh_price',
                                'A.mc_received_price',
                                'A.mc_eoh_price',
                                'B.ProductLine',
                                'B.PartNumber',
                                'B.MaterialType',
                                'C.wmid_whs_balance_amount AS boh_whs_balance_amount',
                                'C.wmid_prodn_balance_amount AS boh_prodn_balance_amount',
                                'D.wmid_whs_balance_amount AS eoh_whs_balance_amount',
                                'D.wmid_prodn_balance_amount AS eoh_prodn_balance_amount',
                                'D.wmid_ppc_adjustment',
                                'D.wmid_received_qty',
                                '(SELECT SUM(`In`) FROM db_pps.tbl_WarehouseTransaction AS E WHERE E.fkid=A.mc_material_id AND `E`.`Date` BETWEEN "'.$current_start_date.'" AND "'.$current_end_date.'" AND (`E`.`In` <> "0.00" OR `E`.`Out` <> "0.00")) as total_received',
                                '(SELECT Eoh FROM db_pps.tbl_WarehouseTransaction AS F WHERE F.fkid=A.mc_material_id AND `F`.`Date`<="'.$current_start_date.'" ORDER BY `F`.`Date` DESC,`F`.`pkid` DESC LIMIT 1) as tbl_warehouse_data_boh',
                                '(SELECT Eoh FROM db_pps.tbl_WarehouseTransaction AS G WHERE G.fkid=A.mc_material_id AND `G`.`Date`<="'.$current_end_date.'" ORDER BY `G`.`Date` DESC,`G`.`pkid` DESC LIMIT 1) as tbl_warehouse_data_eoh',
                                'H.pmid_mix_value AS previous_month_pmid_mix_value',
                                'H.pmid_virgin_value AS previous_month_pmid_virgin_value',
                                'H.pmid_actual_value AS previous_month_pmid_actual_value',
                                'I.pmid_mix_value AS current_month_pmid_mix_value',
                                'I.pmid_virgin_value AS current_month_pmid_virgin_value',
                                'I.pmid_actual_value AS current_month_pmid_actual_value');
	$table 	   			= 't_material_cost AS A';
	$joins            	.= ' LEFT JOIN t_material_cost AS AA ON A.mc_material_id=AA.mc_material_id AND AA.mc_month="'.$previous_month.'" AND AA.mc_year="'.$previous_year.'" AND AA.mc_logdel=0 AND A.mc_eoh_price=AA.mc_eoh_price';
    $joins           	.= ' LEFT JOIN db_pps.tbl_Warehouse AS B ON A.mc_material_id=B.id';
    $joins           	.= ' LEFT JOIN t_consolidated_monthly_inventory_data AS C ON AA.mc_id=C.wmid_mc_id';
    $joins           	.= ' LEFT JOIN t_consolidated_monthly_inventory_data AS D ON A.mc_id=D.wmid_mc_id';
    $joins           	.= ' LEFT JOIN t_production_monthly_inventory_data AS H ON H.pmid_material_code=B.PartNumber AND H.pmid_month="'.$previous_month.'" AND H.pmid_year="'.$previous_year.'" AND H.pmid_logdel=0'; 
    $joins            	.= ' LEFT JOIN t_production_monthly_inventory_data AS I ON I.pmid_material_code=B.PartNumber AND I.pmid_month="'.$_GET['data_month'].'" AND I.pmid_year="'.$_GET['data_year'].'" AND I.pmid_logdel=0';
	$sql_where 			= 'WHERE A.mc_logdel=0 AND A.mc_month="'.$_GET['data_month'].'" AND A.mc_year="'.$_GET['data_year'].'"';
	$sql_order 			= 'ORDER BY B.MaterialType ASC, A.mc_id ASC';
	$sql_limit 			= '';
    $result             = db_ppsmis::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
	$ctr 				= 5;
	$cts 				= 5;
	$mc_id 				= '';

	$arr_new_alloc = array();

	while($aRow = mysqli_fetch_array($result)){
        // $row            = array();
        // unset($row);  
        if($mc_id!=$aRow['mc_id']){

        	$arr_new_alloc[$aRow['ProductLine']] = 1;

			$excel->getActiveSheet()->setCellValueExplicit('A'.$ctr, $aRow['ProductLine'],PHPExcel_Cell_DataType::TYPE_STRING);
			$excel->getActiveSheet()->setCellValueExplicit('B'.$ctr, $aRow['PartNumber'],PHPExcel_Cell_DataType::TYPE_STRING);
			$excel->getActiveSheet()->setCellValueExplicit('C'.$ctr, $aRow['MaterialType'],PHPExcel_Cell_DataType::TYPE_STRING);

			$excel->getActiveSheet()->setCellValueExplicit('D'.$ctr, number_format($aRow['mc_boh_price'], 4, '.', ','),PHPExcel_Cell_DataType::TYPE_NUMERIC);
			$excel->getActiveSheet()->setCellValueExplicit('E'.$ctr, number_format($aRow['mc_received_price'], 4, '.', ','),PHPExcel_Cell_DataType::TYPE_NUMERIC);
			$excel->getActiveSheet()->setCellValueExplicit('F'.$ctr, number_format($aRow['mc_eoh_price'], 4, '.', ','),PHPExcel_Cell_DataType::TYPE_NUMERIC);
			$excel->getActiveSheet()->getStyle('D'.$ctr)->applyFromArray($fill_indigo);
			$excel->getActiveSheet()->getStyle('E'.$ctr)->applyFromArray($fill_indigo);
			$excel->getActiveSheet()->getStyle('F'.$ctr)->applyFromArray($fill_indigo);



            if($aRow['boh_whs_balance_amount']==null||trim($aRow['boh_whs_balance_amount'])==''){                                               //BOH(QTY. BASED) Warehouse
                if($current_material_code==$aRow['PartNumber']){                                                                                //BOH(QTY. BASED) Warehouse
                    $for_total_whs_data_boh = 0;                                                                                                //BOH(QTY. BASED) Warehouse
					$excel->getActiveSheet()->setCellValueExplicit('G'.$ctr, 0,PHPExcel_Cell_DataType::TYPE_NUMERIC);
					$excel->getActiveSheet()->getStyle('G'.$ctr)->applyFromArray($fill_yellow);
                }                                                                                                                               //BOH(QTY. BASED) Warehouse
                else{                                                                                                                           //BOH(QTY. BASED) Warehouse
                    $for_total_whs_data_boh = $aRow['tbl_warehouse_data_boh'];                                                                  //BOH(QTY. BASED) Warehouse
					$excel->getActiveSheet()->setCellValueExplicit('G'.$ctr, number_format($for_total_whs_data_boh, 2, '.', ','),PHPExcel_Cell_DataType::TYPE_NUMERIC);
					$excel->getActiveSheet()->getStyle('G'.$ctr)->applyFromArray($fill_cream);
                }                                                                                                                               //BOH(QTY. BASED) Warehouse
            }                                                                                                                                   //BOH(QTY. BASED) Warehouse   
            else{                                                                                                                               //BOH(QTY. BASED) Warehouse
                $for_total_whs_data_boh = $aRow['boh_whs_balance_amount'];                                                                      //BOH(QTY. BASED) Warehouse
				$excel->getActiveSheet()->setCellValueExplicit('G'.$ctr, number_format($for_total_whs_data_boh, 2, '.', ','),PHPExcel_Cell_DataType::TYPE_NUMERIC);
				$excel->getActiveSheet()->getStyle('G'.$ctr)->applyFromArray($fill_cream);
            }                                                                                                                                   //BOH(QTY. BASED) Warehouse
			            
            if($aRow['boh_prodn_balance_amount']==null||trim($aRow['boh_prodn_balance_amount'])==''){                                           //BOH(QTY. BASED) Production
                if(floatval($aRow['previous_month_pmid_actual_value'])==0){                                                                     //BOH(QTY. BASED) Production
                    $for_total_prodn_data_boh = ($aRow['previous_month_pmid_mix_value']*.8)+$aRow['previous_month_pmid_virgin_value'];          //BOH(QTY. BASED) Production
                }                                                                                                                               //BOH(QTY. BASED) Production
                else{                                                                                                                           //BOH(QTY. BASED) Production
                    $for_total_prodn_data_boh = $aRow['previous_month_pmid_actual_value'];                                                      //BOH(QTY. BASED) Production
                }                                                                                                                               //BOH(QTY. BASED) Production
                                                                                                                                                //BOH(QTY. BASED) Production
                if($current_material_code==$aRow['PartNumber']){                                                                                //BOH(QTY. BASED) Production
                    $for_total_whs_data_boh = 0;                                                                                                //BOH(QTY. BASED) Production
					$excel->getActiveSheet()->setCellValueExplicit('H'.$ctr, 0,PHPExcel_Cell_DataType::TYPE_NUMERIC);
					$excel->getActiveSheet()->getStyle('H'.$ctr)->applyFromArray($fill_yellow);
                }                                                                                                                               //BOH(QTY. BASED) Production
                else{                                                                                                                           //BOH(QTY. BASED) Production
					$excel->getActiveSheet()->setCellValueExplicit('H'.$ctr, number_format($for_total_prodn_data_boh, 2, '.', ','),PHPExcel_Cell_DataType::TYPE_NUMERIC);
					$excel->getActiveSheet()->getStyle('H'.$ctr)->applyFromArray($fill_cream);
                }                                                                                                                               //BOH(QTY. BASED) Production
            }                                                                                                                                   //BOH(QTY. BASED) Production
            else{                                                                                                                               //BOH(QTY. BASED) Production
                $for_total_prodn_data_boh = $aRow['boh_prodn_balance_amount'];                                                                  //BOH(QTY. BASED) Production
				$excel->getActiveSheet()->setCellValueExplicit('H'.$ctr, number_format($for_total_prodn_data_boh, 2, '.', ','),PHPExcel_Cell_DataType::TYPE_NUMERIC);
				$excel->getActiveSheet()->getStyle('H'.$ctr)->applyFromArray($fill_cream);
            }                                                                                                                                   //BOH(QTY. BASED) Production


            $total_data_boh = $for_total_whs_data_boh+$for_total_prodn_data_boh;                                                                //BOH(QTY. BASED) Total
			$excel->getActiveSheet()->setCellValueExplicit('I'.$ctr, number_format($total_data_boh, 2, '.', ','),PHPExcel_Cell_DataType::TYPE_NUMERIC);
			$excel->getActiveSheet()->getStyle('I'.$ctr)->applyFromArray($fill_green);

            if($aRow['wmid_received_qty']==null||trim($aRow['wmid_received_qty'])==''||$aRow['wmid_received_qty']=0){                           //Received
                if($current_material_code==$aRow['PartNumber']){                                                                                //Received
					$excel->getActiveSheet()->setCellValueExplicit('J'.$ctr, 0,PHPExcel_Cell_DataType::TYPE_NUMERIC);
					$excel->getActiveSheet()->getStyle('J'.$ctr)->applyFromArray($fill_yellow);
                }                                                                                                                               //Received
                else{                                                                                                     //Received
					$excel->getActiveSheet()->setCellValueExplicit('J'.$ctr, $aRow['total_received'],PHPExcel_Cell_DataType::TYPE_NUMERIC);
                }                                                                                                                         //Received
            }                                                                                                                                   //Received
            else{                                                                                                                               //Received
				$excel->getActiveSheet()->setCellValueExplicit('J'.$ctr, number_format($aRow['wmid_received_qty'], 2, '.', ','),PHPExcel_Cell_DataType::TYPE_NUMERIC);
				$excel->getActiveSheet()->getStyle('J'.$ctr)->applyFromArray($fill_cream);
            }                                                                                                                                   //Received

			$excel->getActiveSheet()->setCellValueExplicit('K'.$ctr, number_format($aRow['wmid_ppc_adjustment'], 2, '.', ','),PHPExcel_Cell_DataType::TYPE_NUMERIC);
			$excel->getActiveSheet()->getStyle('K'.$ctr)->applyFromArray($fill_cream);

            if($aRow['eoh_whs_balance_amount']==null||trim($aRow['eoh_whs_balance_amount'])==''){                                                                                                                           //EOH(QTY. BASED) Warehouse Data Variable
                $for_total_whs_data_eoh = $aRow['tbl_warehouse_data_eoh'];                                                                //EOH(QTY. BASED) Warehouse Data Variable
            }                                                                                                                                   //EOH(QTY. BASED) Warehouse Data Variable
            else{                                                                                                                               //EOH(QTY. BASED) Warehouse Data Variable
                $for_total_whs_data_eoh = $aRow['eoh_whs_balance_amount'];                                                                      //EOH(QTY. BASED) Warehouse Data Variable
            }                                                                                                                                   //EOH(QTY. BASED) Warehouse Data Variable

            if($aRow['eoh_prodn_balance_amount']==null||trim($aRow['eoh_prodn_balance_amount'])==''){                                           //EOH(QTY. BASED) Production Data Variable
                if(floatval($aRow['current_month_pmid_actual_value'])==0){                                                                      //EOH(QTY. BASED) Production Data Variable
                    $for_total_prodn_data_eoh = ($aRow['current_month_pmid_mix_value']*.8)+$aRow['current_month_pmid_virgin_value'];            //EOH(QTY. BASED) Production Data Variable
                }                                                                                                                               //EOH(QTY. BASED) Production Data Variable
                else{                                                                                                                           //EOH(QTY. BASED) Production Data Variable
                    $for_total_prodn_data_eoh = $aRow['current_month_pmid_actual_value'];                                                       //EOH(QTY. BASED) Production Data Variable
                }                                                                                                                               //EOH(QTY. BASED) Production Data Variable
            }                                                                                                                                   //EOH(QTY. BASED) Production Data Variable
            else{                                                                                                                               //EOH(QTY. BASED) Production Data Variable
                $for_total_prodn_data_eoh = $aRow['eoh_prodn_balance_amount'];                                                                  //EOH(QTY. BASED) Production Data Variable
            }                                                                                                                                   //EOH(QTY. BASED) Production Data Variable


            $total_data_used_qty = (($for_total_whs_data_boh+$for_total_prodn_data_boh)+$aRow['total_received']+$aRow['wmid_ppc_adjustment'])-($for_total_whs_data_eoh+$for_total_prodn_data_eoh);  //Used
            if($total_data_used_qty<=0){
				$excel->getActiveSheet()->setCellValueExplicit('L'.$ctr, "",PHPExcel_Cell_DataType::TYPE_NUMERIC);
				$excel->getActiveSheet()->getStyle('L'.$ctr)->applyFromArray($fill_green);
            }
            else{
				$excel->getActiveSheet()->setCellValueExplicit('L'.$ctr, number_format($total_data_used_qty, 2, '.', ','),PHPExcel_Cell_DataType::TYPE_NUMERIC);
				$excel->getActiveSheet()->getStyle('L'.$ctr)->applyFromArray($fill_green);
            }                                                                              

			$excel->getActiveSheet()->setCellValueExplicit('M'.$ctr, number_format($for_total_whs_data_eoh, 2, '.', ','),PHPExcel_Cell_DataType::TYPE_NUMERIC);
			$excel->getActiveSheet()->getStyle('M'.$ctr)->applyFromArray($fill_cream);
			$excel->getActiveSheet()->setCellValueExplicit('N'.$ctr, number_format($for_total_prodn_data_eoh, 2, '.', ','),PHPExcel_Cell_DataType::TYPE_NUMERIC);
			$excel->getActiveSheet()->getStyle('N'.$ctr)->applyFromArray($fill_cream);


            $total_data_eoh = $for_total_whs_data_eoh+$for_total_prodn_data_eoh;                                                                //EOH(QTY. BASED) Total Data Variable
			$excel->getActiveSheet()->setCellValueExplicit('O'.$ctr, number_format($total_data_eoh, 2, '.', ','),PHPExcel_Cell_DataType::TYPE_NUMERIC);
			$excel->getActiveSheet()->getStyle('O'.$ctr)->applyFromArray($fill_green);


			$excel->getActiveSheet()->setCellValueExplicit('P'.$ctr, number_format($for_total_whs_data_boh*$aRow['mc_boh_price'], 2, '.', ','),PHPExcel_Cell_DataType::TYPE_NUMERIC);
			$excel->getActiveSheet()->setCellValueExplicit('Q'.$ctr, number_format($for_total_prodn_data_boh*$aRow['mc_boh_price'], 2, '.', ','),PHPExcel_Cell_DataType::TYPE_NUMERIC);


            $total_data_amount_based_boh = ($for_total_whs_data_boh*$aRow['mc_boh_price'])+($for_total_prodn_data_boh*$aRow['mc_boh_price']);
			$excel->getActiveSheet()->setCellValueExplicit('R'.$ctr, number_format($total_data_amount_based_boh, 2, '.', ','),PHPExcel_Cell_DataType::TYPE_NUMERIC);
			$excel->getActiveSheet()->getStyle('R'.$ctr)->applyFromArray($fill_green);
			$excel->getActiveSheet()->setCellValueExplicit('S'.$ctr, number_format($aRow['mc_received_price']*$aRow['total_received'], 2, '.', ','),PHPExcel_Cell_DataType::TYPE_NUMERIC);
			$excel->getActiveSheet()->setCellValueExplicit('T'.$ctr, number_format($aRow['mc_eoh_price']*$aRow['wmid_ppc_adjustment'], 2, '.', ','),PHPExcel_Cell_DataType::TYPE_NUMERIC);

            $total_whs_data_eoh_amount_based = $aRow['mc_eoh_price']*$for_total_whs_data_eoh;
            $total_prodn_data_eoh_amount_based = $aRow['mc_eoh_price']*$for_total_prodn_data_eoh;
            $total_data_amount_based_eoh = $total_whs_data_eoh_amount_based+$total_prodn_data_eoh_amount_based;

            $total_data_amount_based_used_qty = ((($for_total_whs_data_boh*$aRow['mc_boh_price'])+($for_total_prodn_data_boh*$aRow['mc_boh_price']))+($aRow['mc_received_price']*$aRow['total_received'])+($aRow['mc_eoh_price']*$aRow['wmid_ppc_adjustment']))-$total_data_amount_based_eoh;
            
            if($total_data_amount_based_used_qty<=0){
				$excel->getActiveSheet()->setCellValueExplicit('U'.$ctr, "$ 0.00",PHPExcel_Cell_DataType::TYPE_NUMERIC);
				$excel->getActiveSheet()->getStyle('U'.$ctr)->applyFromArray($fill_green);
            }
            else{
				$excel->getActiveSheet()->setCellValueExplicit('U'.$ctr, number_format($total_data_amount_based_used_qty, 2, '.', ','),PHPExcel_Cell_DataType::TYPE_NUMERIC);
				$excel->getActiveSheet()->getStyle('U'.$ctr)->applyFromArray($fill_green);
            }

			$excel->getActiveSheet()->setCellValueExplicit('V'.$ctr, number_format($total_whs_data_eoh_amount_based, 2, '.', ','),PHPExcel_Cell_DataType::TYPE_NUMERIC);
			$excel->getActiveSheet()->setCellValueExplicit('W'.$ctr, number_format($total_prodn_data_eoh_amount_based, 2, '.', ','),PHPExcel_Cell_DataType::TYPE_NUMERIC);
			$excel->getActiveSheet()->setCellValueExplicit('X'.$ctr, number_format($total_data_amount_based_eoh, 2, '.', ','),PHPExcel_Cell_DataType::TYPE_NUMERIC);
			$excel->getActiveSheet()->getStyle('X'.$ctr)->applyFromArray($fill_green);

            $current_received_qty =$aRow['wmid_received_qty'];
            $current_material_code = $aRow['PartNumber'];

	        $ctr++;

        }
        $mc_id = $aRow['mc_id'];
    }
	$excel->set_borders('A5:X'.$ctr,1,1,1,1);	




	$excel->getActiveSheet()->setCellValue('A'.$ctr,'TOTAL');
	$excel->getActiveSheet()->getStyle('A'.$ctr.':X'.$ctr)->getFont()->setBold(true);
	
	if($ctr>5){
		for ($i='G'; $i < 'Y'; $i++) {
			$excel->getActiveSheet()->setCellValue($i.$ctr,'=SUM('.$i.'5:'.$i.($ctr-1).')');
		}
	}
	$excel->set_borders('A'.$ctr.':X'.$ctr,1,1,1,1);
	$excel->getActiveSheet()->getStyle('A'.$ctr.':X'.$ctr)->applyFromArray($fill_yellow);

	$ctr_last_tbl1 = $ctr-1;
	$ctr +=2;
	$ctr_first_tbl2 = $ctr;

	//2nd table
	foreach ($arr_new_alloc as $key => $value) {
		$excel->getActiveSheet()->setCellValue('C'.$ctr,$key);

		for ($i='G'; $i < 'Y'; $i++) {
			$excel->getActiveSheet()->setCellValue($i.$ctr,'=SUMIF($A$5:$A$'.$ctr_last_tbl1.',$C'.$ctr.','.$i.'$5:'.$i.'$'.$ctr_last_tbl1.')');
		}
		$excel->set_borders('C'.$ctr.':X'.$ctr,1,1,1,1);
		$ctr++;
	}

	$excel->getActiveSheet()->setCellValue('E'.$ctr,'TOTAL');
	$excel->getActiveSheet()->getStyle('E'.$ctr.':X'.$ctr)->getFont()->setBold(true);

	if($ctr>7){
		for ($i='G'; $i < 'Y'; $i++) { 
			$excel->getActiveSheet()->setCellValue($i.$ctr,'=SUM('.$i.$ctr_first_tbl2.':'.$i.($ctr-1).')');
		}
	}

	$excel->set_borders('C'.$ctr.':X'.$ctr,1,1,1,1);
	$excel->getActiveSheet()->getStyle('C'.$ctr.':X'.$ctr)->applyFromArray($fill_yellow);

	$excel->getActiveSheet()->getStyle('D5:F'.$ctr)->getNumberFormat()->setFormatCode('0.0000'); 
	$excel->getActiveSheet()->getStyle('G5:O'.$ctr)->getNumberFormat()->setFormatCode('0.00'); 

	$excel->getActiveSheet()
	    ->getStyle('P5:X'.$ctr)
	    ->getNumberFormat()
	    ->setFormatCode(
	        '_("$"* #,##0.00_);_("$"* \(#,##0.00\);_(@_)'
	    );

	// $ctr++;
	// $excel->place_value('A'.$ctr,$ctr,'string','right',true);
	// $ctr = ($ctr+5);
	// $excel->place_value('C'.$ctr,'*Note: Generated from Rapid. Data as of '.date('M d, Y g:i:s A'),'string','left',true).'.';
	// $excel->set_font('C'.$ctr,false,'000FFF','',10,'Arial Italic');

	$filename = 'pps_materials_consumption_report';
	$excel->output($filename,'2003');
?>