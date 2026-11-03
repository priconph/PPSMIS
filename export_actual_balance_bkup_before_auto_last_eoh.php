<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

	$excel_class = './dist/excel_export/class/excel.php';
	if(file_exists($excel_class)){
		include($excel_class);
	}
	else{
		echo 'File '.$excel_class.' does not exist';
		exit;
	}
	require_once("./dist/handler/query.php");
	
	$font = array(
		'font'  => array(
		    'size'  => 12,
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


	$excel = new EXCEL;
	$excel->title = 'Balance Summary';
	$excel->add_page();
	$excel->set_active_sheet(0);
	$excel->getDefaultStyle()->applyFromArray($font)->applyFromArray($vertical_align_middle);
	$excel->getActiveSheet()->setAutoFilter('A3:E3');
	$excel->getActiveSheet()->freezePane('A4');
	$array_cell = array('A','B','C','D','E');
	$array_width = array('26','15','40','20','16');
	$excel->set_width($array_cell,$array_width);
	$array_row = array('1','2');
	$array_height = array(30,20);
	$excel->set_height($array_row,$array_height);
	$excel->set_font('A1',true,'','6ff298',17,'Arial Narrow');
	$excel->merge_cells('A1:E1');
	$excel->place_value('A1','Materials\' Balance Summary Report','string','left',true);
	$excel->set_font('A2',false,'','6ff298',14,'Arial Narrow');
	$excel->merge_cells('A2:E2');
	$excel->place_value('A2','As of '.date('F d, Y'),'string','left',true);
	$excel->set_borders('A3:E3',1,1,1,1);
	$excel->place_value('A3','CLASSIFICATION','string','center',true);
	$excel->place_value('B3','PART CODE','string','center',true);
	$excel->place_value('C3','DESCRIPTIONS','string','center',true);
	$excel->place_value('D3','MODEL/LOCATION','string','center',true);
	$excel->place_value('E3','BALANCE','string','center',true);
	$excel->set_font('A3:E3',true,'','FFFF00',12,'Arial Narrow');


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ctr = 4;
		$array_fields 		= array('B.Classification',
									'PartNumber',
									'MaterialType',
									'Model',
									'Balance',
									'(SELECT Eoh FROM tbl_WarehouseTransaction WHERE fkid=A.id ORDER BY Date DESC, pkid DESC LIMIT 1) AS EOH');
		$table 	   			= 'tbl_Warehouse AS A';
		$joins 	   			= 'LEFT JOIN tbl_Warehouse_Classification AS B ON A.Classification=B.id';
		$sql_where 			= '';
		$sql_order 			= 'ORDER BY A.Classification ASC, A.PartNumber';
		$sql_limit 			= '';
		$result_child 		= db_pps::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		// $test 				= db_pps::getInstance()->query_select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		$row_count = mysqli_num_rows($result_child);
		if($row_count==0){
			$ctr++;
		}
		else{
			while($row_child = mysqli_fetch_array($result_child)){
				if( $row_child['EOH'] == NULL){//added by novs 2019-07-30
					continue;
				}
				$excel->place_value('A'.$ctr,$row_child['Classification'],'string','left',true);
				$excel->place_value('B'.$ctr,$row_child['PartNumber'],'string','left',true);
				$excel->place_value('C'.$ctr,$row_child['MaterialType'],'string','left',true);
				$excel->place_value('D'.$ctr,$row_child['Model'],'string','left',true);
				$excel->place_value('E'.$ctr,$row_child['EOH'],'number_2_decimal_with_comma','right',true);
				$ctr++;
			}
		}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	$excel->set_borders('A4:E'.($ctr-1),1,1,1,1);
	$excel->place_value('E'.$ctr,'=SUM(E4:E'.($ctr-1).')','number_2_decimal_with_comma','right',true);

	$excel->place_value('B'.$ctr,'TOTAL:','string','right',true);
	$excel->set_font('A'.$ctr.':E'.$ctr,true,'','ffaab1',11,'Arial Italic');

	$ctr = ($ctr+4);
	$excel->place_value('E'.$ctr,'*Note: Generated from Rapid. Data as of '.date('M d, Y g:i:s A'),'string','left',true).'.';
	$excel->set_font('E'.$ctr,false,'blue','',10,'Arial Italic');
	
	// $ctr++;
	// $excel->place_value('A'.$ctr,$test,'string','right',true);

	$filename = 'warehouse_summary_report_'.date('Y_m_d');
	$excel->output($filename,'2007');
?>