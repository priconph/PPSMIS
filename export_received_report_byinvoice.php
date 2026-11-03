<?php
    require('PHPExcel.php'); //change this with your OOP path
    
	session_start();
    $invoiceno 	= $_GET['invoiceno'];
   
    $datetoday = date("F j, Y H:i:s"); 
   
    //Database Connection
    mysql_connect('localhost', 'root', 'r@p1d0nl1n3');
    mysql_select_db('db_pps');

    //Create new PHPExcel Page
    $objPHPExcel = new PHPExcel;
    $objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
    
	//Font Size: Global
    $objPHPExcel->getDefaultStyle()->getFont()->setSize(11);

    //Create Sheet
    $objWorkSheet = $objPHPExcel->getActiveSheet();
    $title = "IQC Application"; //set title cont.
    $objWorkSheet->setTitle($title); //set title cont.

    //FORMAT
    //Border Style
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

	
	$OutlineStyle = array(
	  'borders' => array(
		'outline' => array(
		  'style' => PHPExcel_Style_Border::BORDER_THIN
		)
	  )
	);

    $styleHeader = array(
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



   //Horizontal
    $objWorkSheet->getStyle('A1:H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objWorkSheet->getStyle('A2:H2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objWorkSheet->getStyle('A3:H3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objWorkSheet->getStyle('A8:H8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objWorkSheet->getStyle('B5:B7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objWorkSheet->getStyle('F5:F6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    //Vertical
    $objWorkSheet->getStyle('A1:H1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objWorkSheet->getStyle('A2:H2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objWorkSheet->getStyle('A3:H3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objWorkSheet->getStyle('A8:H8')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objWorkSheet->getStyle('B5:B7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objWorkSheet->getStyle('F5:F6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
 
    //Set width: Static
    $objWorkSheet->getColumnDimension('A')->setWidth(20);
    $objWorkSheet->getColumnDimension('B')->setWidth(30);
    $objWorkSheet->getColumnDimension('C')->setWidth(20);
    $objWorkSheet->getColumnDimension('D')->setWidth(20);
    $objWorkSheet->getColumnDimension('E')->setWidth(20);
    $objWorkSheet->getColumnDimension('F')->setWidth(20);
    $objWorkSheet->getColumnDimension('G')->setWidth(20);
    $objWorkSheet->getColumnDimension('H')->setWidth(20);
	
    //Border
    $objWorkSheet->getStyle('A8:H8')->applyFromArray($styleHeader);
    $objWorkSheet->getStyle('A1:H7')->applyFromArray($OutlineStyle);

    //Wrap Text
    // $objWorkSheet->getStyle('A1:G1')->getAlignment()->setWrapText(true);

    //Set height: Static 
	// $objPHPExcel->getActiveSheet()->setAutoFilter('A3:F3');
	
    //Set font size
    // $objWorkSheet->getStyle('A2:G2')->getFont()->setSize(10);

    //Merge Cells
    $objWorkSheet->mergeCells('A1:H1');
    $objWorkSheet->mergeCells('A2:H2');
    $objWorkSheet->mergeCells('A3:H4');
      
	$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setUnderline(true);
  
    //DEFAULT Values
    $objWorkSheet->setCellValue('A1', 'PRICON MICROELECTRONICS, INC.');
    $objWorkSheet->setCellValue('A2', '#14 AMPERE ST., LIGHT INDUSTRY AND SCIENCE PARK 1, SEPZ, CABUYAO, LAGUNA');
    $objWorkSheet->setCellValue('A3', 'APPLICATION FOR IQC INSPECTION');
    $objWorkSheet->setCellValue('A5', 'CONTROL #:');
    $objWorkSheet->setCellValue('F5', 'DATE/TIME:');
    $objWorkSheet->setCellValue('A6', 'INVOICE #:');
    $objWorkSheet->setCellValue('A7', 'SERIES NAME / P.O:');
    $objWorkSheet->setCellValue('A8', 'Part Number');
    $objWorkSheet->setCellValue('B8', 'Part Name');
    $objWorkSheet->setCellValue('C8', 'Supplier / Vendor');
    $objWorkSheet->setCellValue('D8', 'Quantity');
    $objWorkSheet->setCellValue('E8', 'Lot #');
    $objWorkSheet->setCellValue('F8', 'Drawing No.');
    $objWorkSheet->setCellValue('G8', 'IQC Result');
    $objWorkSheet->setCellValue('H8', 'Price');
   
		 $ctrRow = 9;
		//Horizontal
		$objWorkSheet->getStyle('A:H')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		//Vertical
		$objWorkSheet->getStyle('A:H')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	    //Border
		$objWorkSheet->getStyle('A'.$ctrRow.':H'.$ctrRow)->applyFromArray($styleArray);
	
		//Wrap Text
		$objWorkSheet->getStyle('A'.$ctrRow.':H'.$ctrRow)->getAlignment()->setWrapText(true);

        //Set font size
	 	$objWorkSheet->getStyle('A'.$ctrRow)->getFont()->setSize(10);
				
			$sql = "SELECT * FROM `tbl_WarehouseTransaction` INNER JOIN tbl_Warehouse ON tbl_WarehouseTransaction.fkid = tbl_Warehouse.id WHERE invoiceno = '$invoiceno' ";

		$result = mysql_query($sql);
        while ($row = mysql_fetch_array($result))
        {
					
			//title  
            $objWorkSheet->getStyle('A3:H3')->getFont()->setBold(true)->setSize(16);
            $objWorkSheet->getStyle('A8:H8')->getFont()->setBold(true)->setSize(11);
            $objWorkSheet->getStyle('B5:B7')->getFont()->setBold(true)->setSize(11);
            $objWorkSheet->getStyle('G5:G6')->getFont()->setBold(true)->setSize(11);
			
			//Horizontal
			$objWorkSheet->getStyle('A:H')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			//Vertical
			$objWorkSheet->getStyle('A:H')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

			//Border
			$objWorkSheet->getStyle('A'.$ctrRow.':H'.$ctrRow)->applyFromArray($styleArray);
			
           //Wrap Text
            $objWorkSheet->getStyle('A'.$ctrRow.':H'.$ctrRow)->getAlignment()->setWrapText(true);

            //Set font size
            $objWorkSheet->getStyle('A'.$ctrRow.':H'.$ctrRow)->getFont()->setSize(10);
		
            // Set value
            $objWorkSheet->setCellValue('G5', $datetoday);
            $objWorkSheet->setCellValue('B6', $row['InvoiceNo']);
            $objWorkSheet->setCellValue('A'.$ctrRow, " " . $row['PartNumber']);
            $objWorkSheet->setCellValue('B'.$ctrRow, $row['MaterialType']);
            $objWorkSheet->setCellValue('C'.$ctrRow, $row['Supplier']);
            $objWorkSheet->setCellValue('D'.$ctrRow, $row['In']);
            $objWorkSheet->setCellValue('E'.$ctrRow, $row['Date']);
            $objWorkSheet->setCellValue('H'.$ctrRow, $row['Price']);
			$ctrRow++;
			
        }
		
		$objWorkSheet->getStyle('A'.$ctrRow)->getFont()->setBold(true);
		$objWorkSheet->getStyle('F'.$ctrRow)->getFont()->setBold(true);
			
		//Set Value
		$objWorkSheet->setCellValue('A'.$ctrRow, 'PREPARED BY:');	
		$objWorkSheet->setCellValue('F'.$ctrRow, 'RECEIVED BY:');	
	
	ob_end_clean();
		
    //SAVING
    $writer = new PHPExcel_Writer_Excel2007($objPHPExcel);
    unset($styleArray);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename=IQC Application ('.date("Y/m/d").').xls'); // used for excel 2003 format
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); // used for excel 2003 format
    $objWriter->save('php://output');
?>