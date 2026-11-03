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
	$excel->title = 'Month-end Report';
	$excel->add_page();
	$excel->set_active_sheet(0);
	$excel->getDefaultStyle()->applyFromArray($font)->applyFromArray($vertical_align_middle);
	$excel->getActiveSheet()->setAutoFilter('A4:I4');
	$excel->getActiveSheet()->freezePane('A5');
	$array_cell = array('A','B','C','D','E','F','G','H','I');
	$array_width = array('15','40','16','16','16','16','15','15','25');
	$excel->set_width($array_cell,$array_width);
	$array_row = array('1','2');
	$array_height = array(30,20);
	$excel->set_height($array_row,$array_height);
	$excel->set_font('A1',true,'','6ff298',17,'Arial Narrow');
	$excel->merge_cells('A1:I1');
	$excel->place_value('A1','PPS RAW MATERIALS INVENTORY','string','left',true);
	$excel->set_font('A2',false,'','6ff298',14,'Arial Narrow');
	$excel->merge_cells('A2:I2');
	$excel->place_value('A2',date('F d, Y',strtotime($_GET['end_date'])),'string','left',true);
	$excel->set_borders('A3:I4',1,1,1,1);	
	$excel->merge_cells('A3:A4');
	$excel->place_value('A3','PART CODE','string','center',true);
	$excel->merge_cells('B3:B4');
	$excel->place_value('B3','DESCRIPTION','string','center',true);
	$excel->place_value('C4','RAPID DATA','string','center',true);
	$excel->place_value('C3','BOH - '.date('F d, Y',strtotime($_GET['start_date'])),'string','center',true);
	$excel->set_font('C3',false,'','',10,'Arial Narrow');
	$excel->place_value('D3',date('Fd',strtotime($_GET['start_date'])).'-'.date('Fd',strtotime($_GET['end_date'])),'string','center',true);
	$excel->place_value('D4','RECEIVED','string','center',true);
	$excel->place_value('E3',date('Fd',strtotime($_GET['start_date'])).'-'.date('Fd',strtotime($_GET['end_date'])),'string','center',true);
	$excel->place_value('E4','USAGE/KGS.','string','center',true);
	$excel->place_value('F3',date('F d, Y',strtotime($_GET['end_date'])),'string','center',true);
	$excel->place_value('F4','EOH','string','center',true);
	$excel->merge_cells('G3:G4');
	$excel->place_value('G3','TICKET NUMBER','string','center',true);
	$excel->merge_cells('H3:H4');
	$excel->place_value('H3','LOCATION','string','center',true);
	$excel->merge_cells('I3:I4');
	$excel->place_value('I3','PPC REMARKS','string','center',true);
	$excel->set_font('A3:I4',true,'','FFFF00',12,'Arial Narrow');

	$array_fields 		= array('*');
	$table 	   			= 'tbl_Warehouse_Classification';
	$joins 	   			= '';
	$sql_where 			= 'WHERE Classification<>""';
	$sql_order 			= 'ORDER BY id ASC';
	$sql_limit 			= '';
	$result 			= db_pps::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
	$ctr 				= 5;
	while($row = mysqli_fetch_array($result)){
		$array_fields 		= array('PartNumber',
									'MaterialType',
									'Model',
									'(SELECT Eoh FROM tbl_WarehouseTransaction AS B WHERE B.fkid=A.id AND `C`.`Date`<="'.$_GET['start_date'].'") as Boh',
									'(SELECT SUM(`In`) FROM tbl_WarehouseTransaction AS C WHERE C.fkid=A.fkid AND `C`.`Date` BETWEEN "'.$_GET['start_date'].'" AND "'.$_GET['end_date'].'" AND (`C`.`In` <> "0.00" OR `C`.`Out` <> "0.00")) as total_received',
									'(SELECT SUM(`Out`) FROM tbl_WarehouseTransaction AS D WHERE D.fkid=A.fkid AND `D`.`Date` BETWEEN "'.$_GET['start_date'].'" AND "'.$_GET['end_date'].'" AND (`D`.`In` <> "0.00" OR `D`.`Out` <> "0.00")) as total_issued');
		$table 	   			= 'tbl_Warehouse AS A';
		$joins 	   			= '';
		$sql_where 			= 'WHERE Classification="'.$row['id'].'"';
		$sql_order 			= 'ORDER BY id ASC';
		$sql_limit 			= '';
		$result_child 		= db_pps::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		$test 		= db_pps::getInstance()->query_select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		$ctr 				= 5;
		while($row_child = mysqli_fetch_array($result_child)){
			$excel->place_value('A'.$ctr,$row['PartNumber'],'string','center',true);
			$excel->place_value('B'.$ctr,$row['MaterialType'],'string','left',true);
			$excel->place_value('C'.$ctr,$row['Boh'],'number_2_decimal_with_comma','right',true);
			$excel->place_value('D'.$ctr,$row['total_received'],'number_2_decimal_with_comma','right',true);
			$excel->place_value('E'.$ctr,$row['total_issued'],'number_2_decimal_with_comma','right',true);
			$excel->place_value('F'.$ctr,'=(C'.$ctr.'+D'.$ctr.')-E'.$ctr,'number_2_decimal_with_comma','right',true);
			$excel->place_value('H'.$ctr,$row['Model'],'string','left',true);
			$ctr++;
		}
	}
	$excel->set_borders('A5:I'.($ctr-1),1,1,1,1);

	// $excel->place_value('C'.$ctr,'BOH','string','center',true);	
	// $excel->place_value('D'.$ctr,'RECEIVED','string','center',true);
	// $excel->place_value('E'.$ctr,'USAGE/KGS','string','center',true);
	// $excel->place_value('F'.$ctr,'EOH','string','center',true);
	// $ctr++;
	$excel->place_value('B'.$ctr,'TOTAL:','string','right',true);

	$excel->place_value('C'.$ctr,'=SUM(C5:C'.($ctr-1).')','number_2_decimal_with_comma','right',true);	
	$excel->place_value('D'.$ctr,'=SUM(D5:D'.($ctr-1).')','number_2_decimal_with_comma','right',true);
	$excel->place_value('E'.$ctr,'=SUM(E5:E'.($ctr-1).')','number_2_decimal_with_comma','right',true);
	$excel->place_value('F'.$ctr,'=SUM(F5:F'.($ctr-1).')','number_2_decimal_with_comma','right',true);

	$excel->place_value('B'.$ctr,'TOTAL:','string','right',true);
	$excel->set_font('B'.$ctr.':F'.$ctr,true,'','',13,'Arial');
	$excel->place_value('I'.$ctr,'*Note: Generated from Rapid. Data as of '.date('M d, Y g:i:s A'),'string','left',true).'.';
	$excel->set_font('I'.$ctr,false,'blue','',10,'Arial Italic');
	
	// $ctr++;
	// $excel->place_value('A'.$ctr,$test,'string','right',true);

	$filename = 'month_end_report_'.$_GET['start_date'].'_'.$_GET['end_date'];
	$excel->output($filename,'2007');
?>