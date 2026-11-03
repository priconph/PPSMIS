<?php
	require('../media/PHPExcel.php');
	require_once("ppswarehouse-oop.php");
	session_start();
	
	$startDate = $_GET["startDate"];
	$endDate = $_GET["endDate"];
	
	$ProductLine = $_GET["ProductLine"];
	$fkid = $_GET["fkid"];
	$MaterialType = $_GET["MaterialType"];
	$Model = $_GET["Model"];
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
	$title = "Warehouse Inventory"; //set title cont.
	$objSheet->setTitle($title); //set title cont.
	
		//BORDERS STYLE
	//set all borders corner to thin 
	$styleArray = array(
	  'borders' => array(
		'allborders' => array(
		  'style' => PHPExcel_Style_Border::BORDER_THIN
		)
	  ),
	  'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb'=>'FFFFF'),
		)
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
	$objPHPExcel->getActiveSheet()->getStyle('A5:Z999')
    ->getAlignment()->setWrapText(true);
	
	//SET WIDTH
	
	$objSheet->getColumnDimension('A')->setWidth(10);
	$objSheet->getColumnDimension('B')->setWidth(10);
	$objSheet->getColumnDimension('C')->setWidth(10);
	$objSheet->getColumnDimension('D')->setWidth(10);
	$objSheet->getColumnDimension('E')->setWidth(10);
	$objSheet->getColumnDimension('F')->setWidth(10);
	$objSheet->getColumnDimension('G')->setWidth(10);
	$objSheet->getColumnDimension('H')->setWidth(10);
	$objSheet->getColumnDimension('I')->setWidth(10);
	$objSheet->getColumnDimension('J')->setWidth(10);
	$objSheet->getColumnDimension('K')->setWidth(10);
	$objSheet->getColumnDimension('L')->setWidth(10);
	$objSheet->getColumnDimension('M')->setWidth(10);
	$objSheet->getColumnDimension('N')->setWidth(10);
	$objSheet->getColumnDimension('O')->setWidth(10);
	$objSheet->getColumnDimension('P')->setWidth(10);
	$objSheet->getColumnDimension('Q')->setWidth(10);
	$objSheet->getColumnDimension('R')->setWidth(10);
	$objSheet->getColumnDimension('S')->setWidth(10);
	$objSheet->getColumnDimension('T')->setWidth(10);
	
	
	$objSheet->mergeCells('A1:G1'); 
	$objSheet->mergeCells('B3:F3'); 
	$objSheet->mergeCells('B4:F4');
	$objSheet->mergeCells('I3:M3'); 
	$objSheet->mergeCells('I4:M4'); 	
	$objSheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objSheet->getStyle('A1')->getFont()->setBold(true)->setSize(18);
	$objSheet->getStyle('E2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objSheet->getStyle('E2')->getFont()->setBold(true)->setSize(18);	
	//SET ALL VALUES
	$objSheet->getCell('A1')->setValue('Pricon Microelectronics, Inc.');
		if($startDate!=""){
			$objSheet->getCell('E2')->setValue('PPS WAREHOUSE - ('.$startDate.' - '.$endDate.')');
		}else{
			$objSheet->getCell('E2')->setValue('PPS WAREHOUSE - (All Records)');
		}
	$objSheet->getCell('B3')->setValue('Product Line : '.$ProductLine);
	$objSheet->getCell('G3')->setValue('Part Number : '.$PartNumber);
	$objSheet->getCell('B4')->setValue('Material Type : '.$MaterialType);
	$objSheet->getCell('G4')->setValue('Model : '.$Model);
	
	$objSheet->getStyle('A5:Z5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objSheet->getStyle('A5:Z5')->getFont()->setBold(true)->setSize(8);	
	//Label here
	$objSheet->getCell('A5')->setValue('Date');
	$objSheet->getCell('B5')->setValue('Invoice No.');
	$objSheet->getCell('C5')->setValue('Transfer Slip No');
	$objSheet->getCell('D5')->setValue('B.O.H.');
	$objSheet->getCell('E5')->setValue('IN');
	$objSheet->getCell('F5')->setValue('OUT');
	$objSheet->getCell('G5')->setValue('E.O.H.');
	$objSheet->getCell('H5')->setValue('Recorded By:');
	$objSheet->getCell('I5')->setValue('Remarks:');
		
	$objSheet->getStyle('A6:T999')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objSheet->getStyle('A6:T999')->getFont()->setBold(false)->setSize(8);
	$result = PPSW::getInstance()->exportResinExcel($fkid,$startDate,$endDate);
	
	$count = 6;
	while($row = mysqli_fetch_array($result)){
		/*$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('A'.$count, $row['ProductCode'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('B'.$count, $row['PoNum'],PHPExcel_Cell_DataType::TYPE_STRING); Reserved for text*/
		$objSheet->getCell('A'.$count)->setValue($row['Date']); 
		$objSheet->getCell('B'.$count)->setValue($row['InvoiceNo']); 
		$objSheet->getCell('C'.$count)->setValue($row['TransferSlipNo']); 
		$objSheet->getCell('D'.$count)->setValue($row['Boh']); 
		$objSheet->getCell('E'.$count)->setValue($row['In']); 
		$objSheet->getCell('F'.$count)->setValue($row['Out']); 
		$objSheet->getCell('G'.$count)->setValue($row['Eoh']); 
		$objSheet->getCell('H'.$count)->setValue($row['RecordedBy']); 
		$objSheet->getCell('I'.$count)->setValue($row['Remarks']); 
		$count++;
	}
	/*	
	//set borders
	$endCount = $count;
	$count = 5;
	while ($count < $endCount){
		$objSheet->getStyle('A'.$count)->applyFromArray($styleArray3);
		$objSheet->getStyle('B'.$count)->applyFromArray($styleArray3);
		$objSheet->getStyle('C'.$count)->applyFromArray($styleArray3);
		$objSheet->getStyle('D'.$count)->applyFromArray($styleArray3);
		$objSheet->getStyle('E'.$count)->applyFromArray($styleArray3);
		$objSheet->getStyle('F'.$count)->applyFromArray($styleArray3);
		$objSheet->getStyle('G'.$count)->applyFromArray($styleArray3);
		$objSheet->getStyle('H'.$count)->applyFromArray($styleArray3);
		$objSheet->getStyle('I'.$count)->applyFromArray($styleArray3);
	}
	*/
	
	
	unset($styleArray);
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename='.$MaterialType.'.xls');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
?>