<?php
$parent_class = "../media/PHPExcel.php";
if(file_exists($parent_class)){
	include($parent_class); //call the parent class
}else{
	echo 'file '.$parent_class.' does not exist!';
	exit;
}
	
class EXCEL extends PHPExcel{
	public $title = '';
	public $objSheet = '';
	/* 
		create new phpexcel page 
	*/
	public function add_page(){
		$this->objSheet = $this->getActiveSheet();
		$this->objSheet->setTitle($this->title); //set title cont.
	}
	/* 
		Set default font style
	*/
	public function set_default_font_style(){
		$this->getDefaultStyle()->getFont()->setName('Lucida Fax');
	}
	/* 
		Set active sheet
	*/
	public function set_active_sheet($sheet_number){
		$this->setActiveSheetIndex($sheet_number);
	}
	/* 
		Set cell width
	*/
	public function set_width($array_cell,$array_width){
		for($x=0;$x<count($array_cell);$x++){
			$this->objSheet->getColumnDimension($array_cell[$x])->setWidth($array_width[$x]);
		}
	}
	/* 
		Set height
	*/
	public function set_height($array_row,$array_height){
		for($x=0;$x<count($array_row);$x++){
			$this->objSheet->getRowDimension($array_row[$x])->setRowHeight($array_height[$x]);
		}
	}
	/* 
		Set borders
	*/
	public function set_borders($cell,$left,$right,$top,$bottom){
		$default_border = PHPExcel_Style_Border::BORDER_THIN;
		if($left == '1' && $right == '1' && $top == '1' && $bottom == '1'){
			$styleArray = array(
			  'borders' => array(
				'allborders' => array(
				  'style' => $default_border,
				)
			  )
			);
			$this->objSheet->getStyle($cell)->applyFromArray($styleArray);
		}else{
			if($left == '1'){
				$left_border = array(
				  'borders' => array(
					'left' => array(
						  'style' => $default_border,
						)
					)
				);
				$this->objSheet->getStyle($cell)->applyFromArray($left_border);
			}
			if($right == '1'){
				$right_border = array(
				  'borders' => array(
					'right' => array(
						  'style' => $default_border,
						)
					)
				);
				$this->objSheet->getStyle($cell)->applyFromArray($right_border);
			}
			if($top == '1'){
				$top_border = array(
				  'borders' => array(
					'top' => array(
						  'style' => $default_border,
						)
					)
				);
				$this->objSheet->getStyle($cell)->applyFromArray($top_border);
			}
			if($bottom == '1'){
				$bottom_border = array(
				  'borders' => array(
					'bottom' => array(
						  'style' => $default_border,
						)
					)
				);
				$this->objSheet->getStyle($cell)->applyFromArray($bottom_border);
			}
		}
	}
	/* 
		merge cells
	*/
	public function merge_cells($cell){
		$this->objSheet->mergeCells($cell); 
	}
	/* 
		set font 
	*/
	public function set_font($cell,$bold,$color,$background_color,$size,$font){
		if(!$bold){
			$bold = false;
		}
		if($color == ''){
			$color = '000000';
		}
		if($background_color != ''){
			$background_type = PHPExcel_Style_Fill::FILL_SOLID;
		}else{
			$background_color 	= '';
			$background_type	= '';
		}
		if($size == ''){
			$size = '12';
		}
		if($font == ''){
			$font = 'Arial';
		}
		if($background_color==''){
			$styleArray = array(
				'font'  => array(
					'bold'  => $bold,
					'color' => array('rgb' => $color),
					'size'  => $size,
					'name'  => $font
				)
			);
		}
		else{
			$styleArray = array(
				'font'  => array(
					'bold'  => $bold,
					'color' => array('rgb' => $color),
					'size'  => $size,
					'name'  => $font
				)
				,'fill' => array(
						'type' => $background_type,
						'color' => array('rgb' => $background_color)
				)
			);	
		}
		$this->objSheet->getStyle($cell)->applyFromArray($styleArray);
	}
	/* 
		place values
	*/
	public function place_value($cell,$value,$type,$alignment,$wordwrap){
		if($alignment == 'left'){
			$this->objSheet->getStyle($cell)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		}
		if($alignment == 'right'){
			$this->objSheet->getStyle($cell)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		}
		if($alignment == 'center'){
			$this->objSheet->getStyle($cell)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		}
		if($wordwrap){
			$this->objSheet->getStyle($cell)->getAlignment()->setWrapText(true);
		}
		if($type == 'string'){
			$this->objSheet->setCellValueExplicit($cell, $value,PHPExcel_Cell_DataType::TYPE_STRING);
		}else if($type == 'number_2_decimal'){
			$this->objSheet->getCell($cell)->setValue($value);
			$this->objSheet->getStyle($cell)->getNumberFormat()->setFormatCode('0.00');
		}else if($type == 'number_0_decimal'){
			$this->objSheet->getCell($cell)->setValue($value);
			$this->objSheet->getStyle($cell)->getNumberFormat()->setFormatCode('0');
		}else if($type == 'number_0_decimal_with_comma'){
			$this->objSheet->getCell($cell)->setValue($value);
			$this->objSheet->getStyle($cell)->getNumberFormat()->setFormatCode('#,##0');
		}else if($type == 'number_0_decimal_with_comma_parenthesis'){
			$this->objSheet->getCell($cell)->setValue($value);
			$this->objSheet->getStyle($cell)->getNumberFormat()->setFormatCode('#,##0;[Red](#,##0)');
		}else if($type == 'number_2_decimal_with_comma'){
			$this->objSheet->getCell($cell)->setValue($value);
			$this->objSheet->getStyle($cell)->getNumberFormat()->setFormatCode('#,##0.00');
		}else if($type == 'percentage_0_decimal'){
			$this->objSheet->getCell($cell)->setValue($value);
			$this->objSheet->getStyle($cell)->getNumberFormat()->setFormatCode('0%');
			$style_array_percentage = array( 
												'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE
											);
			$this->objSheet->getStyle($cell)->getNumberFormat()->applyFromArray( $style_array_percentage );
		}else if($type == 'percentage_2_decimal'){
			$this->objSheet->getCell($cell)->setValue($value);
			$this->objSheet->getStyle($cell)->getNumberFormat()->setFormatCode('0.00%;[Red]-0.00%');
			$style_array_percentage = array( 
												'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
											);
			$this->objSheet->getStyle($cell)->getNumberFormat()->applyFromArray( $style_array_percentage );
		}
		else{
			$this->objSheet->getCell($cell)->setValue($value);
		}
		
	}
	/* 
		Generate Excel Output 
	*/
	public function output($filename, $file_type){
		if($file_type == "2007"){
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename='.$filename.'.xlsx'); //used for excel 2007 format
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($this, 'Excel2007'); //used for excel 2007 format
		}else if($file_type == "2003"){
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename='.$filename.'.xls'); // used for excel 2003 format
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($this, 'Excel5'); // used for excel 2003 format
		}else{
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename='.$filename.'.xlsx'); //used for excel 2007 format
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($this, 'Excel2007'); //used for excel 2007 format
		}
		ob_end_clean();
		$objWriter->save('php://output');
	}	
}