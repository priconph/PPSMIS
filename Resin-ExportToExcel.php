<?php
	require('../media/PHPExcel.php');
	//require_once("smpopsg-oop.php");
	session_start();
	if (array_key_exists("username", $_SESSION))
		{
			$username = $_SESSION['username'];
			//$username = 'arlbena';
		}
	//$ControlNumber = $_GET['ControlNumber'];
	$dateToday = date("M. d, Y");
	$dateCtrl = date("Y-m");
	//create new phpexcel page
	$objPHPExcel = new PHPExcel;
	$objPHPExcel->getDefaultStyle()->getFont()->setName('Lucida Fax');
	//set font
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	
	$objSheet = $objPHPExcel->getActiveSheet();
	$title = "Resin Export"; //set title cont.
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
	$objPHPExcel->getActiveSheet()->getStyle('A2:Z999')
    ->getAlignment()->setWrapText(true);
	
	
	$objSheet->mergeCells('A1:G1');
	$objSheet->mergeCells('C2:F2');
	$objSheet->mergeCells('B3:D3');
	$objSheet->mergeCells('E3:G3');
	$objSheet->mergeCells('B4:D4');
	$objSheet->mergeCells('E4:G4');
	$objSheet->getStyle('A1')->getFont()->setBold(true)->setSize(18);
	$objSheet->getStyle('C2')->getFont()->setBold(true)->setSize(18);
	$objSheet->getCell('A1')->setValue('Pricon Microelectronics, Inc.');
	$objSheet->getCell('C2')->setValue('PPS WAREHOUSE - ( All Records )');
	$objSheet->getCell('B3')->setValue('Product Line:PRODUCT LINE HERE');
	$objSheet->getCell('E3')->setValue('Part Number:PART NUMBER HERE');
	$objSheet->getCell('B4')->setValue('Material Type:');
	$objSheet->getCell('E4')->setValue('Model:');
	//row here
	$objSheet->getCell('B5')->setValue('Date');
	$objSheet->getCell('C5')->setValue('Invoice No.');
	$objSheet->getCell('D5')->setValue('Transfer Slip No.');
	$objSheet->getCell('E5')->setValue('BOH');
	$objSheet->getCell('F5')->setValue('IN');
	$objSheet->getCell('G5')->setValue('OUT');
	$objSheet->getCell('H5')->setValue('EOH');
	$objSheet->getCell('I5')->setValue('Recorded by:');
	$objSheet->getCell('J5')->setValue('Remarks');
	
	//SET WIDTH
	$objSheet->getColumnDimension('A')->setWidth(2); //small row for spacing
	$objSheet->getColumnDimension('B')->setWidth(8); //date
	$objSheet->getColumnDimension('C')->setWidth(20); //invoice num
	$objSheet->getColumnDimension('D')->setWidth(20); //transfer slip no.
	$objSheet->getColumnDimension('E')->setWidth(10); //B.O.H.
	$objSheet->getColumnDimension('F')->setWidth(10); //IN
	$objSheet->getColumnDimension('G')->setWidth(10); //OUT
	$objSheet->getColumnDimension('H')->setWidth(10); //EOH
	$objSheet->getColumnDimension('I')->setWidth(15); //Recorded By
	$objSheet->getColumnDimension('J')->setWidth(30); //Remarks
	//$objSheet->getColumnDimension('J5')->setWidth(10); 
	//$objSheet->getColumnDimension('K5')->setWidth(10);
	//$objSheet->getColumnDimension('L5')->setWidth(10);
	//$objSheet->getColumnDimension('M5')->setWidth(10);
	//$objSheet->getColumnDimension('N')->setWidth(10);
	//$objSheet->getColumnDimension('O')->setWidth(10);
	//$objSheet->getColumnDimension('P')->setWidth(15);
	
	//SET HEIGHT
	$objPHPExcel->getActiveSheet()
    ->getRowDimension('2')
    ->setRowHeight(23);
	
	/*		
		$objSheet->getStyle('A6')->applyFromArray($styleArray3);
		$objSheet->getStyle('B6')->applyFromArray($styleArray3);
		$objSheet->getStyle('C6')->applyFromArray($styleArray3);
		$objSheet->getStyle('D6')->applyFromArray($styleArray3);
		$objSheet->getStyle('E6')->applyFromArray($styleArray3);
		$objSheet->getStyle('F6')->applyFromArray($styleArray3);
		$objSheet->getStyle('G6')->applyFromArray($styleArray3);
		$objSheet->getStyle('H6')->applyFromArray($styleArray3);
		$objSheet->getStyle('I6')->applyFromArray($styleArray3);
		$objSheet->getStyle('J6')->applyFromArray($styleArray3);
		$objSheet->getStyle('K6')->applyFromArray($styleArray3);
		$objSheet->getStyle('L6')->applyFromArray($styleArray3);
		$objSheet->getStyle('M6')->applyFromArray($styleArray3);
		$objSheet->getStyle('N6')->applyFromArray($styleArray3);
		$objSheet->getStyle('O6')->applyFromArray($styleArray3);
		$objSheet->getStyle('P6')->applyFromArray($styleArray3);
		
	$objSheet->getStyle('A5:P5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objSheet->getStyle('A1:P4')->getFont()->setBold(false)->setSize(10);
	$objSheet->mergeCells('A1:B1');
	$objSheet->mergeCells('A2:B2');
	$objSheet->mergeCells('A3:B3');
	$objSheet->mergeCells('A4:B4');
	$objSheet->mergeCells('A5:B5');
	$objSheet->getCell('A1')->setValue('To:');
	$objSheet->getCell('A2')->setValue('Attn:'); 
	$objSheet->getCell('A3')->setValue('Cc:'); 
	$objSheet->getCell('A4')->setValue('Date:'); 
	$objSheet->getCell('A5')->setValue('PMI-'.$dateCtrl.'-'); 
	//$objSheet->mergeCells('D1:P1'); 
	
	$objSheet->getStyle('A6:P6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objSheet->getStyle('A6:P6')->getFont()->setBold(true)->setSize(8);
	//$objSheet->getStyle('A5:S5')->getFont()->setBold(true)->setSize(8);
	$objSheet->getCell('A6')->setValue('Item #'); 
	$objSheet->getCell('B6')->setValue('Device'); 
	$objSheet->getCell('C6')->setValue('Material Name'); 
	$objSheet->getCell('D6')->setValue(' '); 
	$objSheet->getCell('E6')->setValue('# of Cavity'); 
	$objSheet->getCell('F6')->setValue('Product Wt.(g)'); 
	$objSheet->getCell('G6')->setValue('Sprue Wt.(g)'); 
	$objSheet->getCell('H6')->setValue('Shot Weight(g)'); 
	$objSheet->getCell('I6')->setValue('Labor Cost/hr($)'); 
	$objSheet->getCell('J6')->setValue('Cycle Time'); 
	$objSheet->getCell('K6')->setValue('Process Cost'); 
	$objSheet->getCell('L6')->setValue('Material Cost (per piece)'); 
	$objSheet->getCell('M6')->setValue('2nd Process Time (secs)'); 
	$objSheet->getCell('N6')->setValue('2nd Process Cost'); 
	$objSheet->getCell('O6')->setValue('Profit'); 
	$objSheet->getCell('P6')->setValue('Molding Product Quotation($)'); 
	*/
	unset($styleArray);
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename=PPSResin.xlsx');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
?>