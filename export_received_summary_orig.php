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
	$excel->title = 'Received Report';
	$excel->add_page();
	$excel->set_active_sheet(0);
	$excel->getDefaultStyle()->applyFromArray($font)->applyFromArray($vertical_align_middle);
	$excel->getActiveSheet()->setAutoFilter('A3:G3');
	$excel->getActiveSheet()->freezePane('A4');
	$array_cell = array('A','B','C','D','E','F','G');
	$array_width = array('15','40','20','16','16','16','40');
	$excel->set_width($array_cell,$array_width);
	$array_row = array('1','2');
	$array_height = array(30,20);
	$excel->set_height($array_row,$array_height);
	$excel->set_font('A1',true,'','6ff298',17,'Arial Narrow');
	$excel->merge_cells('A1:G1');
	$excel->place_value('A1','Received Summary Report','string','left',true);
	$excel->set_font('A2',false,'','6ff298',14,'Arial Narrow');
	$excel->merge_cells('A2:G2');
	$excel->place_value('A2',date('F d, Y',strtotime($_GET['start_date'])).' - '.date('F d, Y',strtotime($_GET['end_date'])),'string','left',true);
	$excel->set_borders('A3:G3',1,1,1,1);
	$excel->place_value('A3','PART CODE','string','center',true);
	$excel->place_value('B3','DESCRIPTION','string','center',true);
	$excel->place_value('C3','MODEL/LOCATION','string','center',true);
	$excel->place_value('D3','INVOICE NO.','string','center',true);
	$excel->place_value('E3','RECEIVED DATE','string','center',true);
	$excel->place_value('F3','RECEIVED QTY.','string','center',true);
	$excel->place_value('G3','REMARKS','string','center',true);
	$excel->set_font('A3:G3',true,'','FFFF00',12,'Arial Narrow');


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ctr = 4;
		$cts = 4;


		$array_fields 		= array('PartNumber',
									'MaterialType',
									'Model',
									'InvoiceNo',
									'`In`',
									'`Date`',
									'Remarks');
		$table 	   			= 'tbl_Warehouse AS A';
		$joins 	   			= 'LEFT JOIN tbl_WarehouseTransaction AS B ON (A.id=B.fkid) AND (B.Date BETWEEN "'.$_GET['start_date'].'" AND "'.$_GET['end_date'].'")';
		$sql_where 			= 'WHERE (B.In > 0 AND B.In <> "")';
		$sql_order 			= 'ORDER BY A.Classification ASC, A.PartNumber, B.Date ASC';
		$sql_limit 			= '';
		$result_child 		= db_pps::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		$test 				= db_pps::getInstance()->query_select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		$row_count = mysqli_num_rows($result_child);

		if($row_count==0){
			$ctr++;
		}
		else{
			while($row_child = mysqli_fetch_array($result_child)){		
				if($prev_partnumber!=$row_child['PartNumber']&&$ctr!=4){
						if($cts!=4){
							$subtotal_f[] = 'F'.($cts-1);			
						}
						$excel->place_value('B'.$ctr,'SUBTOTAL:','string','right',true);
						$excel->place_value('F'.$ctr,'=SUM(F'.$cts.':F'.($ctr-1).')','number_2_decimal_with_comma','right',true);
						$excel->set_font('A'.$ctr.':F'.$ctr,true,'','b1aaff',11,'Arial Italic');
						$ctr++;
						$cts = $ctr;
				}
				$prev_partnumber = $row_child['PartNumber'];
				$excel->place_value('A'.$ctr,$row_child['PartNumber'],'string','center',true);
				$excel->place_value('B'.$ctr,$row_child['MaterialType'],'string','left',true);
				$excel->place_value('C'.$ctr,$row_child['Model'],'string','left',true);
				$excel->place_value('D'.$ctr,$row_child['InvoiceNo'],'string','left',true);
				$excel->place_value('E'.$ctr,date('M d, Y',strtotime($row_child['Date'])),'string','left',true);
				$excel->place_value('F'.$ctr,$row_child['In'],'number_2_decimal_with_comma','right',true);
				$excel->place_value('G'.$ctr,$row_child['Remarks'],'string','left',true);
				$ctr++;
			}
		}

		if($cts!=4){
			$subtotal_f[] = 'F'.($cts-1);			
		}
		$excel->place_value('B'.$ctr,'SUBTOTAL:','string','right',true);
		$excel->place_value('F'.$ctr,'=SUM(F'.$cts.':F'.($ctr-1).')','number_2_decimal_with_comma','right',true);
		$excel->set_font('A'.$ctr.':F'.$ctr,true,'','b1aaff',11,'Arial Italic');
		$ctr++;
		$cts = $ctr;

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	$excel->set_borders('A4:G'.($ctr-1),1,1,1,1);
	$excel->place_value('F'.$ctr,'='.implode("+",$subtotal_f).'+F'.($ctr-1),'number_2_decimal_with_comma','right',true);

	$excel->place_value('B'.$ctr,'TOTAL:','string','right',true);
	$excel->set_font('A'.$ctr.':F'.$ctr,true,'','ffaab1',11,'Arial Italic');

	$ctr = ($ctr+4);
	$excel->place_value('G'.$ctr,'*Note: Generated from Rapid. Data as of '.date('M d, Y g:i:s A'),'string','left',true).'.';
	$excel->set_font('G'.$ctr,false,'blue','',10,'Arial Italic');
	
	// $ctr++;
	// $excel->place_value('A'.$ctr,$test,'string','right',true);

	$filename = 'received_summary_report_'.$_GET['start_date'].'_'.$_GET['end_date'];
	$excel->output($filename,'2007');
?>