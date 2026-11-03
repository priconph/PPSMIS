<?php
	require('../media/PHPExcel.php');
	require('./ppswarehouse-oop.php');
	//require_once("smpopsg-oop.php");
	session_start();
	if (array_key_exists("username", $_SESSION))
		{
			$username = $_SESSION['username'];
			//$username = 'arlbena';
		}
	
	$id = $_GET['id'];
	$result = PPSW::getInstance()->load_transfer_slip($id);
	while($row = mysqli_fetch_array($result)){
		$ProductLine 	= $row['ProductLine'];
		$ControlNo 		= $row['ControlNo'];
		$Date 			= $row['Date'];
		$IssuedBy 		= $row['IssuedBy'];
	}
	
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
        'color' => array('rgb'=>'FFFFFF'),
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
	
	// Set Orientation, size and scaling
	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToPage(true);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToHeight(0);
	
	
	//WORD WRAP
	$objPHPExcel->getActiveSheet()->getStyle('A2:Z999')
    ->getAlignment()->setWrapText(true);
	
	$objSheet->getColumnDimension('A')->setWidth(15); 
	$objSheet->getColumnDimension('B')->setWidth(25); 
	$objSheet->getColumnDimension('C')->setWidth(10); 
	$objSheet->getColumnDimension('D')->setWidth(15); 
	$objSheet->getColumnDimension('E')->setWidth(1);
	$objSheet->getColumnDimension('F')->setWidth(15); 
	$objSheet->getColumnDimension('G')->setWidth(25);
	$objSheet->getColumnDimension('H')->setWidth(10);
	$objSheet->getColumnDimension('I')->setWidth(15);
	
	$objSheet->mergeCells('A1:D1');
	$objSheet->getStyle('A1')->getFont()->setBold(true)->setSize(18);
	$objSheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objSheet->getCell('A1')->setValue('Pricon Microelectronics, Inc.');
		
	$objSheet->mergeCells('A2:D2');
	$objSheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
	$objSheet->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objSheet->getCell('A2')->setValue('TRANSFER SLIP');
	
	//$objSheet->getStyle('A3')->getFont()->setBold(true)->setSize(10);
	$objSheet->getCell('A3')->setValue('Product Line:');
	$objSheet->getCell('A4')->setValue($ProductLine);
	$objSheet->mergeCells('C4:D4');
	$objSheet->getCell('C4')->setValue("Date: ".$Date);
	
	
	$objSheet->mergeCells('C3:D3');
	//$objSheet->getStyle('C3')->getFont()->setBold(true)->setSize(10);
	
	$objSheet->getCell('C3')->setValue('Control No:'.$ControlNo);
	$objSheet->mergeCells('C3:D3');
	
	$objSheet->mergeCells('F1:I1');
	$objSheet->getStyle('F1')->getFont()->setBold(true)->setSize(18);
	$objSheet->getStyle('F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objSheet->getCell('F1')->setValue('Pricon Microelectronics, Inc.');
	
	$objSheet->mergeCells('F2:I2');
	$objSheet->getStyle('F2')->getFont()->setBold(true)->setSize(14);
	$objSheet->getStyle('F2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objSheet->getCell('F2')->setValue('TRANSFER SLIP');
	
	
	//$objSheet->getStyle('F3')->getFont()->setBold(true)->setSize(10);
	$objSheet->getCell('F3')->setValue('Product Line:');
	$objSheet->getCell('F4')->setValue($ProductLine);
	$objSheet->mergeCells('H4:I4');
	$objSheet->getCell('H4')->setValue("Date: ".$Date);
	
	$objSheet->mergeCells('H3:I3');
	//$objSheet->getStyle('H3')->getFont()->setBold(true)->setSize(10);
	$objSheet->getCell('H3')->setValue('Control No:'.$ControlNo);
	
	$row_cnt = 6;
	$row_cnt_start = $row_cnt;
	/* Header and Database values here */
	/* Headers */
	$objSheet->getCell('A'.$row_cnt)->setValue('Part Number');
	$objSheet->getCell('B'.$row_cnt)->setValue('Part Name');
	$objSheet->getCell('C'.$row_cnt)->setValue('Quantity');
	$objSheet->getCell('D'.$row_cnt)->setValue('Remarks');
	$objSheet->getCell('F'.$row_cnt)->setValue('Part Number');
	$objSheet->getCell('G'.$row_cnt)->setValue('Part Name');
	$objSheet->getCell('H'.$row_cnt)->setValue('Quantity');
	$objSheet->getCell('I'.$row_cnt)->setValue('Remarks');
		
	$total_items = 0;
	$result2 = PPSW::getInstance()->load_transfer_slip_per_control($ControlNo);
	while($row2 = mysqli_fetch_array($result2)){
		$row_cnt++;
		$total_items++;
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('A'.$row_cnt, $row2['PartNumber'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('B'.$row_cnt, $row2['MaterialType'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objSheet->getCell('C'.$row_cnt)->setValue($row2['Out']);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('D'.$row_cnt, $row2['Remarks'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('F'.$row_cnt, $row2['PartNumber'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('G'.$row_cnt, $row2['MaterialType'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objSheet->getCell('H'.$row_cnt)->setValue($row2['Out']);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('I'.$row_cnt, $row2['Remarks'],PHPExcel_Cell_DataType::TYPE_STRING);
	}
		$objSheet->getStyle('A'.$row_cnt_start.':D'.$row_cnt)->applyFromArray($styleArray);
		$objSheet->getStyle('F'.$row_cnt_start.':I'.$row_cnt)->applyFromArray($styleArray);
		//SET HEIGHT
		$objSheet->getRowDimension('2')->setRowHeight(15);
		$objSheet->getRowDimension('3')->setRowHeight(25);
		$row_cnt++;
		$objSheet->mergeCells('A'.$row_cnt.':D'.$row_cnt);
		$objSheet->getCell('A'.$row_cnt)->setValue('WAREHOUSE COPY');
		$objSheet->mergeCells('F'.$row_cnt.':I'.$row_cnt);
		$objSheet->getCell('F'.$row_cnt)->setValue('PRODUCTION COPY');
		$row_cnt++;
		$row_cnt_footer = $row_cnt;
		$objSheet->getCell('A'.$row_cnt)->setValue('Total Items:');
		$objSheet->getCell('F'.$row_cnt)->setValue('Total Items:');
		$objSheet->mergeCells('C'.$row_cnt.':D'.$row_cnt);
		$objSheet->getCell('B'.$row_cnt)->setValue('Issued By:');
		$objSheet->mergeCells('C'.$row_cnt.':D'.$row_cnt);
		$objSheet->getCell('C'.$row_cnt)->setValue('Received By:');
		$objSheet->mergeCells('H'.$row_cnt.':I'.$row_cnt);
		$objSheet->getCell('G'.$row_cnt)->setValue('Issued By:');
		$objSheet->getCell('H'.$row_cnt)->setValue('Received By:');
		$objSheet->getStyle('A'.$row_cnt.':I'.$row_cnt)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$row_cnt++;
		$objSheet->getStyle('A'.$row_cnt.':I'.$row_cnt)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objSheet->getCell('A'.$row_cnt)->setValue($total_items);
		$objSheet->getCell('F'.$row_cnt)->setValue($total_items);
		$row_cnt_1 = $row_cnt+1;
		$objSheet->mergeCells('A'.$row_cnt.':A'.$row_cnt_1);
		$objSheet->mergeCells('B'.$row_cnt.':B'.$row_cnt_1);
		$objSheet->getCell('B'.$row_cnt)->setValue($IssuedBy);
		$objSheet->mergeCells('F'.$row_cnt.':F'.$row_cnt_1);
		$objSheet->mergeCells('G'.$row_cnt.':G'.$row_cnt_1);
		$objSheet->getCell('G'.$row_cnt)->setValue($IssuedBy);
		$objSheet->mergeCells('C'.$row_cnt.':D'.$row_cnt_1);
		$objSheet->mergeCells('H'.$row_cnt.':I'.$row_cnt_1);
		
		$objSheet->getStyle('A'.$row_cnt_footer.':D'.$row_cnt_1)->applyFromArray($styleArray);
		$objSheet->getStyle('F'.$row_cnt_footer.':I'.$row_cnt_1)->applyFromArray($styleArray);
		
	
	unset($styleArray);
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename=PPS Resin Transferslip.xls');
	header('Cache-Control: max-age=0');
	
	//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
?>