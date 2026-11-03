<?php
	require('../media/PHPExcel.php');
	require_once("ppswarehouse-oop.php");
	session_start();
	if (array_key_exists("username", $_SESSION))
		{
			$username = $_SESSION['username'];
			//$username = 'arlbena';
		}
	$ControlNumber = $_GET['ControlNumber'];
	$dateToday = date("M. d, Y");
	//create new phpexcel page
	$objPHPExcel = new PHPExcel;
	$objPHPExcel->getDefaultStyle()->getFont()->setName('Lucida Fax');
	//set font
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	
	$objSheet = $objPHPExcel->getActiveSheet();
	$title = "RESIN"; //set title cont.
	$objSheet->setTitle($title); //set title cont.
	
		//BORDERS STYLE
	//set all borders corner to thin 
	$styleArray = array(
	  'borders' => array(
		'allborders' => array(
		  'style' => PHPExcel_Style_Border::BORDER_THIN
		)
	  ),
	  /*
	  'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb'=>'FFFFF'),
		)
		*/
	); 
	
	$styleArray2 = array(
	  'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb'=>'FFFFF'),
		)
	);
	
	$default_border = array(
		'style' => PHPExcel_Style_Border::BORDER_THIN
	);

	$styleArray3 = array(
	  'borders' => array(
		'bottom' => $default_border,
        'left' => $default_border,
        'top' => $default_border,
        'right' => $default_border,
	  )
	);
	
	$styleArray4 = array(
	  'borders' => array(
		'bottom' => $default_border
	  )
	);
	//WORD WRAP
	$objPHPExcel->getActiveSheet()->getStyle('A2:Z999')
    ->getAlignment()->setWrapText(true);
	
	//SET WIDTH
	$objSheet->getColumnDimension('A')->setWidth(20);
	$objSheet->getColumnDimension('B')->setWidth(20);
	$objSheet->getColumnDimension('C')->setWidth(20);
	$objSheet->getColumnDimension('D')->setWidth(20);
	$objSheet->getColumnDimension('E')->setWidth(15);
			
	//$objSheet->mergeCells('A1:G1'); 
	$objSheet->getStyle('A1:F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$objSheet->getStyle('A1:F1')->getFont()->setBold(true)->setSize(10);
	//SET ALL VALUES
	$objSheet->getCell('A1')->setValue('Product Line');
	$objSheet->getCell('B1')->setValue('Part Number');
	$objSheet->getCell('C1')->setValue('Material Type');
	$objSheet->getCell('D1')->setValue('Model');
	$objSheet->getCell('E1')->setValue('Balance');
		
	$objSheet->getStyle('A2:T999')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$objSheet->getStyle('A2:T999')->getFont()->setBold(false)->setSize(10);
	
	$result = PPSW::getInstance()->exportResinExcelMain();
	$count = 2;
	while($row = mysqli_fetch_array($result)){
		$objSheet->getCell('A'.$count)->setValue($row['ProductLine']); 
		$objSheet->getCell('B'.$count)->setValue($row['PartNumber']); 
		$objSheet->getCell('C'.$count)->setValue($row['MaterialType']); 
		$objSheet->getCell('D'.$count)->setValue($row['Model']); 
		$objSheet->getCell('E'.$count)->setValue($row['Balance']); 
		$count++;
	}
	$countEnd = $count - 1;
	$objSheet->getStyle('A0:E'.$countEnd)->applyFromArray($styleArray);
	unset($styleArray);
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename=Warehouse Summary.xls');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
?>