<?php
	require('../media/PHPExcel.php'); //call the parent class
	require_once("-oop.php"); //change this with your OOP path
	session_start(); 	
	if (array_key_exists("username", $_SESSION))
		{
			$username = $_SESSION['username']; //change this if you do not set the username this way
		}
	
	//Add a get or post function here
	//Note: All php functions will work here except for codes that will display something like echo
	$startDate 	= $_GET['startDate'];
	$endDate 	= $_GET['endDate'];
	
	//create new phpexcel page
	$objPHPExcel = new PHPExcel;
	$objPHPExcel->getDefaultStyle()->getFont()->setName('Lucida Fax');
	//set font
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	
	$objSheet = $objPHPExcel->getActiveSheet();
	$title = "_title"; //set title cont.
	$objSheet->setTitle($title); //set title cont.
	
		//BORDERS STYLE
	//set all borders corner to thin 
	$styleArray = array(
	  'borders' => array(
		'allborders' => array(
		  'style' => PHPExcel_Style_Border::BORDER_THIN //set borders
		)
	  ),
	  'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID, //set back color
        'color' => array('rgb'=>'FFFFFF'),
		)
	); 
	
	$styleArray2 = array(
	  'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb'=>'FFFFFF'),		
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
		'bottom' => $default_border	//set border only at the bottom
	  )
	);
	//how to use the style, values can also b replaced with a variable
	$objSheet->getStyle('A1')->applyFromArray($styleArray);
	
	//WORD WRAP
	//$objPHPExcel->getActiveSheet()->getStyle('A2:Z999')
    //->getAlignment()->setWrapText(true);
	
	//MERGING OF CELLS
	//$objSheet->mergeCells('A1:G1'); 
	
	//ALIGNMENT SETTING
	//$objSheet->getStyle('A1:J1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
	//FONT SIZE AND STYLE CONFIGURATION
	//$objSheet->getStyle('A1')->getFont()->setBold(true)->setSize(18); 
			
	//SET WIDTH
	$objSheet->getColumnDimension('A')->setWidth(10);
	$objSheet->getColumnDimension('B')->setWidth(10);
	$objSheet->getColumnDimension('C')->setWidth(10);
	$objSheet->getColumnDimension('D')->setWidth(10);
	$objSheet->getColumnDimension('E')->setWidth(10);
		
	//SET HEIGHT
	$objPHPExcel->getActiveSheet()
    ->getRowDimension('2')
    ->setRowHeight(23);
	
	//Placing a value into a cell
	//$objSheet->getCell('B5')->setValue('Sample');
	//Placing a value into a cell then converting it to string
	$RowCount = 1;
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$RowCount, $row["DeviceName"],PHPExcel_Cell_DataType::TYPE_STRING);
		
		
	unset($styleArray);
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename=PPSResin.xlsx'); //used for excel 2007 format
	//header('Content-Disposition: attachment;filename=PR Information.xls'); // used for excel 2003 format
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); //used for excel 2007 format
	//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); // used for excel 2003 format
	$objWriter->save('php://output');
?>