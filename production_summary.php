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
	$excel->title = 'Production Summary';
	$excel->add_page();
	$excel->set_active_sheet(0);
	$excel->getDefaultStyle()->applyFromArray($font)->applyFromArray($vertical_align_middle);
	$excel->getActiveSheet()->setAutoFilter('A4:I4');
	$excel->getActiveSheet()->freezePane('A5');
	$array_cell = array('A','B','C','D','E','F','G','H','I');
	$array_width = array('20','15','40','15','15','15','15','15','15');
	$excel->set_width($array_cell,$array_width);
	$array_row = array('1','2');
	$array_height = array(30,20);
	$excel->set_height($array_row,$array_height);
	$excel->set_font('A1',true,'','6ff298',17,'Arial Narrow');
	$excel->merge_cells('A1:I1');
	$excel->place_value('A1','Production Summary Report','string','left',true);
	$excel->set_font('A2',false,'','6ff298',14,'Arial Narrow');
	$excel->merge_cells('A2:I2');
	$excel->place_value('A2','As of '.date('F d, Y'),'string','left',true);
	$excel->set_borders('A3:I4',1,1,1,1);
	$excel->place_value('A3','ALLOCATION','string','center',true);
	$excel->place_value('B3','MAT\'L CODE','string','center',true);
	$excel->place_value('C3','DESCRIPTION','string','center',true);
	$excel->place_value('D3','EOH ()','string','center',true);
	$excel->place_value('G3','EOH ()','string','center',true);
	$excel->place_value('D4','MIX','string','center',true);
	$excel->place_value('E4','VIRGIN','string','center',true);
	$excel->place_value('F4','EQUIV.','string','center',true);
	$excel->place_value('G4','MIX','string','center',true);
	$excel->place_value('H4','VIRGIN','string','center',true);
	$excel->place_value('I4','EQUIV.','string','center',true);
	$excel->set_font('A3:I4',true,'','FFFF00',12,'Arial Narrow');
	$excel->merge_cells('A3:A4');
	$excel->merge_cells('B3:B4');
	$excel->merge_cells('C3:C4');
	$excel->merge_cells('D3:F3');
	$excel->merge_cells('G3:I3');


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ctr = 5;
		$array_fields 		= array(
									'ProductLine',
	                                'A.pmid_material_code',
	                                'A.pmid_material_name',
	                                'A.pmid_mix_value AS mpmid_mix_value',
	                                'A.pmid_virgin_value AS mpmid_virgin_value',
	                                'A.pmid_actual_value AS mpmid_actual_value',
	                                'B.pmid_mix_value AS vpmid_mix_value',
	                                'B.pmid_virgin_value AS vpmid_virgin_value',
	                                'B.pmid_actual_value AS vpmid_actual_value'
									);

		$table 	   			= 't_production_monthly_inventory_data AS A LEFT JOIN db_pps.tbl_Warehouse ON A.pmid_material_code=tbl_Warehouse.PartNumber LEFT JOIN t_production_monthly_inventory_data AS B ON A.pmid_material_code=B.pmid_material_code AND B.pmid_logdel="0"';
		$joins 	   			= '';
		$sql_where 			= 'WHERE A.pmid_logdel="0" AND A.pmid_material_code != ""';
		$sql_order 			= '';
		$sql_limit 			= '';
		$result_child 		= db_pps::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		// $test 				= db_pps::getInstance()->query_select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		$row_count = mysqli_num_rows($result_child);
		if($row_count==0){
			$ctr++;
		}
		else{
			while($aRow = mysqli_fetch_array($result_child)){

		        $actual_value       = 0;
		        $prev_actual_value  = 0;
		        $row                = array();
		        unset($row);  

		        $rowA = $aRow['ProductLine'];

		        if(($aRow['mpmid_mix_value']!=''&&$aRow['mpmid_virgin_value']!='')){
		            $actual_value = ($aRow['mpmid_mix_value']*.8)+$aRow['mpmid_virgin_value'];
		            $rowB = $aRow['pmid_material_code'];
		        }
		        else{
		            if($aRow['mpmid_actual_value']!=''){
		                $rowB = $aRow['pmid_material_code'];
		                $actual_value = $aRow['mpmid_actual_value'];
		            }
		            else{
		                $rowB = $aRow['pmid_material_code'];   
		            } 
		        }

		        if(($aRow['vpmid_mix_value']!=''&&$aRow['vpmid_virgin_value']!='')){
		            $prev_actual_value = ($aRow['vpmid_mix_value']*.8)+$aRow['vpmid_virgin_value'];
		        }
		        else{
		            $prev_actual_value = $aRow['vpmid_actual_value'];
		        }
		        $rowC = $aRow['pmid_material_name'];
		        $rowD = number_format($aRow['vpmid_mix_value'], 2, '.', ',');
		        $rowE = number_format($aRow['vpmid_virgin_value'], 2, '.', ',');
		        $rowF = number_format($prev_actual_value, 2, '.', ',');
		        $rowG = number_format($aRow['mpmid_mix_value'], 2, '.', ',');
		        $rowH = number_format($aRow['mpmid_virgin_value'], 2, '.', ',');
		        $rowI = number_format($actual_value, 2, '.', ',');

				$excel->place_value('A'.$ctr,$rowA,'string','left',true);
				$excel->place_value('B'.$ctr,$rowB,'string','left',true);
				$excel->place_value('C'.$ctr,$rowC,'string','left',true);
				$excel->place_value('D'.$ctr,$rowD,'number_2_decimal_with_comma','right',true);
				$excel->place_value('E'.$ctr,$rowE,'number_2_decimal_with_comma','right',true);
				$excel->place_value('F'.$ctr,$rowE,'number_2_decimal_with_comma','right',true);
				$excel->place_value('G'.$ctr,$rowE,'number_2_decimal_with_comma','right',true);
				$excel->place_value('H'.$ctr,$rowE,'number_2_decimal_with_comma','right',true);
				$excel->place_value('I'.$ctr,$rowE,'number_2_decimal_with_comma','right',true);
				$ctr++;
			}
		}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	$excel->set_borders('A4:I'.($ctr-1),1,1,1,1);
	// $excel->place_value('E'.$ctr,'=SUM(E4:E'.($ctr-1).')','number_2_decimal_with_comma','right',true);

	// $excel->place_value('B'.$ctr,'TOTAL:','string','right',true);
	// $excel->set_font('A'.$ctr.':E'.$ctr,true,'','ffaab1',11,'Arial Italic');

	// $ctr = ($ctr+4);
	// $excel->place_value('E'.$ctr,'*Note: Generated from Rapid. Data as of '.date('M d, Y g:i:s A'),'string','left',true).'.';
	// $excel->set_font('E'.$ctr,false,'blue','',10,'Arial Italic');
	
	// $ctr++;
	// $excel->place_value('A'.$ctr,$test,'string','right',true);

	$filename = 'production_summary_report_'.date('Y_m_d');
	$excel->output($filename,'2007');
?>