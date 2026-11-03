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
	$objSheet->getColumnDimension('F')->setWidth(20);
	$objSheet->getColumnDimension('G')->setWidth(20);
	$objSheet->getColumnDimension('H')->setWidth(30);
	// $objSheet->getColumnDimension('G')->setWidth(20);
			
	//$objSheet->mergeCells('A1:G1'); 
	$objSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
	$objSheet->getStyle('A2:Z2')->getFont()->setBold(true)->setSize(10);
	
	$date_start = $_GET['start_date'];
	$date_end = $_GET['end_date'];
	
	$objSheet->getCell('A1')->setValue('PPS Warehouse Resin Issuance Summary From '. date('M d, Y',strtotime($date_start)) . ' - ' . date('M d, Y',strtotime($date_end)) ); 
	
	/* Header */
	$objSheet->getCell('A2')->setValue('Product Line');
	$objSheet->getCell('B2')->setValue('Part Number');
	$objSheet->getCell('C2')->setValue('Invoice Number');
	$objSheet->getCell('D2')->setValue('Material Type');
	$objSheet->getCell('E2')->setValue('Model');
	$objSheet->getCell('F2')->setValue('Date Issued');
	$objSheet->getCell('G2')->setValue('Issued Qty');
	$objSheet->getCell('H2')->setValue('Remarks');
		

	$result = PPSW::getInstance()->exportResinExcelIssuanceSummary($date_start,$date_end);
	$count = 3;
	// $objSheet->getCell('A'.$count)->setValue($result); 
	while($row = mysqli_fetch_array($result)){
		$objSheet->getCell('A'.$count)->setValue($row['ProductLine']); 
		$objSheet->getCell('B'.$count)->setValue($row['PartNumber']); 
		$objSheet->getCell('C'.$count)->setValue($row['InvoiceNo']); 
		$objSheet->getCell('D'.$count)->setValue($row['MaterialType']); 
		$objSheet->getCell('E'.$count)->setValue($row['Model']);
		$objSheet->getCell('F'.$count)->setValue( date('M d, Y',strtotime($row['DateIssued'])) ); 
		$objSheet->getCell('G'.$count)->setValue($row['IssuedQty']); 
		$objSheet->getCell('H'.$count)->setValue($row['Remarks']); 
		$count++;
	}
	$countEnd = $count - 1;
	$objSheet->getStyle('A2:H'.$countEnd)->applyFromArray($styleArray);
	$objSheet->getStyle("F3:H".$countEnd)->getNumberFormat()->setFormatCode('0.00');
	unset($styleArray);
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename=Warehouse Issuance Summary '.$date_start.' to '.$date_end.'.xls');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
?>