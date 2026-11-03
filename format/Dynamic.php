<?php
include('phpqrcode/qrlib.php');
require('../../media/FPDF/fpdf.php');
// echo 'sdfsdfsdf';
// exit;
class PDF extends FPDF
{
	public $ctrlno_packinglist 	= '';
	public $section 			= '';
	public $casemark 			= '';
	public $font_size 			= '22';
	public $spacing 			= '10';
	public $array_details		= array();
	public $sticker_count		= '1';
	public $page_numbers		= array();
	public $ctr					= 1;

	public $ship_to_details		= 'N/A';

	public $index_data_cntr		= 0;
	public $index_data_to_check = 0;
	public $active_ctrlno = '';
	
	function SetBorder($panel_number){
		/* Line(x axis, x abscissa, y axis, y abscissa) */
		if($panel_number == 1){
			/* border first panel */
			$this->Line(5,5,140,5); 		/* 	top 	*/
			$this->Line(5,100,140,100); 	/* 	bottom 	*/
			$this->Line(5,5,5,100); 		/* 	left 	*/
			$this->Line(140,5,140,100); 	/* 	right 	*/
		}
		if($panel_number == 2){
			/* border second panel */
			$this->Line(150,5,285,5); 		/* 	top 	*/
			$this->Line(150,100,285,100); 	/* 	bottom 	*/
			$this->Line(150,5,150,100); 	/* 	left 	*/
			$this->Line(285,5,285,100); 	/* 	right 	*/
		}
		if($panel_number == 3){
			/* border third panel */
			$this->Line(5,109,140,109); 	/* 	top 	*/
			$this->Line(5,205,140,205); 	/* 	bottom 	*/
			$this->Line(5,205,5,109); 		/*	left 	*/
			$this->Line(140,205,140,109); 	/* 	right 	*/
		}
		if($panel_number == 4){
			/* border fourth panel */
			$this->Line(150,109,285,109); 	/* 	top 	*/
			$this->Line(150,205,285,205); 	/* 	bottom 	*/
			$this->Line(150,205,150,109); 	/* 	left 	*/
			$this->Line(285,205,285,109); 	/* 	right 	*/
		}
	}
	
	function LoadCtrlNo(){
		/* Get temp values from database */
		require_once('../class/casemark.php');
		session_start();
		$return = array();
		$array_fields 	= array('*');
		$table		 	= 'tbl_casemark_temp';
		$joins		 	= '';
		$sql_where		= 'WHERE `username` = "'.$_SESSION['username'].'"';
		$sql_order		= '';
		$sql_limit		= '';
		$result = CASEMARK::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		$ctrlno_array 	= array();
		while( $row = mysqli_fetch_array($result) ){
			/* assign class values here */
			$ctrlno_array[]				= $row['ctrlno_packinglist'];
		}
		return $ctrlno_array;
	}

	function LoadLabelInfo1(){
		/* Get temp values from database */
			/* assign class values here */
			$this->material_type	= 'material type';
			$this->part_number 		= 'part number';
			$this->lot_number 		= 'lot number';
			/* returning the # symbols */
		}
	}

	function LoadLabelDetails1(){
		
		unset($this->array_details);
		switch ($this->section){
			case "TS"	: 
				$class = PL_YPICS4::getInstance(); 
			break;
			case "CN"	: 
				$class = PL_CN_YPICS4::getInstance();
			break;
			default		: 
				$class = PL_YPICS4::getInstance(); 
		}	
		$result = $class->select_packing_list_main4_per_control($this->ctrlno_packinglist);
		if($row = mysqli_fetch_array($result)) {
			$fkCtrl = $row['id'];
		}		
		$result2 = $class->select_packing_list_details4_per_control($fkCtrl);
		while( $row = mysqli_fetch_array($result2) ){
			$this->array_details['active_ctrlno'][]     = $row['packing_id'];
			$this->array_details['BoxNo'][] 			= $row['box_no'];
			$this->array_details['PONo'][] 				= $row['po'];
			$this->array_details['Description'][] 		= $row['description'];
			$this->array_details['ProductCode'][] 		= $row['item_code'];
			$this->array_details['Quantity'][] 			= $row['qty'];
			if( count(explode('-', $row['box_no'])) == 1 )
				// $this->array_details['UnitOfMeasurement'][] = $row['gross_weight'];qty
				$this->array_details['UnitOfMeasurement'][] = $row['qty'];
			else{
				$_tmp = explode('/', $row['gross_weight']);
				$_tmp = explode('(', $_tmp[0]);
				$_tmp = $_tmp[count($_tmp)-1];
				$this->array_details['UnitOfMeasurement'][] = $_tmp;
			}
			// echo 'box no '.$row['box_no'].' <br>';
		}
			// $this->ship_to_details = $fkCtrl;
		$result3 = $class->select_packing_list_details_by_id($fkCtrl);
		while( $row = mysqli_fetch_array($result3) ){
			$this->ship_to_details = $row['ship_to'];
		}

		// if( strpos(strtolower($this->array_details['BoxNo'][0]), 'a') )
		// 	$this->$packing_list_mode = 'a_mode';
		// else
		// 	$this->$packing_list_mode = '-_mode';

		// echo json_encode();
	}
	
	function LoadLabelInfo($Ctrlno){
		/* Get temp values from database */
		require_once('../class/casemark.php');
		session_start();
		$return = array();
		$array_fields 	= array('*');
		$table		 	= 'tbl_casemark_temp';
		$joins		 	= '';
		$sql_where		= 'WHERE `username` = "'.$_SESSION['username'].'" AND `ctrlno_packinglist` = "'.$Ctrlno.'"';
		$sql_order		= '';
		$sql_limit		= '';
		$result = CASEMARK::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		$html 			= '';
		while( $row = mysqli_fetch_array($result) ){
			/* assign class values here */
			$this->ctrlno_packinglist	= $row['ctrlno_packinglist'];
			$this->section 				= $row['section'];
			$this->casemark 			= urldecode($row['casemark']);
			/* returning the # symbols */
			$this->casemark				= str_replace('()','#',$this->casemark);
			$this->font_size			= $row['font_size'];
			$this->spacing 				= $row['spacing'];
		}
	}
	
	function get_database_view_name($section){
		$return = array();
		switch ($section){
			case "TS"	: 
				$return['database'] = 'PMD_PackingLIst';
				$return['vw_main'] = 'vw_casemark_pmd_bu2_main';
				$return['vw_details'] = 'vw_casemark_pmd_bu2_details';
				break;
			case "CN"	:
				$return['database'] = 'FASFAP_PL';
				$return['vw_main'] = 'vw_casemark_fasfap_main';
				$return['vw_details'] = 'vw_casemark_fasfap_details';
				break;
			case "YF"	:
				$return['database'] = 'YFLEX_PackingList';
				$return['vw_main'] = 'vw_casemark_yflex_main';
				$return['vw_details'] = 'vw_casemark_yflex_details';
				break;
			case "PPS"	:
				$return['database'] = 'TSMolding_PackingList';
				$return['vw_main'] = 'vw_casemark_ts_molding_main';
				$return['vw_details'] = 'vw_casemark_ts_molding_details';
				break;
		}
		return $return;
	}

	function LoadLabelDetails(){
		require_once('../class/packing_list.php');
		// $connection_info = $this->get_database_view_name( $this->section );
		// $packing_list = new PACKINGLIST;
		// $result = $packing_list->select_packing_list_details_per_control($this->ctrlno_packinglist, $connection_info);
		/* reset_array_details */
		// unset($this->array_details);
		// $this->array_details = array();
		// while( $row = mssql_fetch_array($result) ){
			// $this->array_details['BoxNo'][] 		= $row['BoxNo'];
			// $this->array_details['PONo'][] 			= $row['PONo'];
			// $this->array_details['Description'][] 	= $row['Description'];
			// $this->array_details['ProductCode'][] 	= $row['ProductCode'];
			// $this->array_details['Quantity'][] 		= $row['Quantity'];
		// }
		
		unset($this->array_details);
		switch ($this->section){
			case "TS"	: 
				$class = PL_YPICS4::getInstance(); 
			break;
			case "CN"	: 
				$class = PL_CN_YPICS4::getInstance();
			break;
			default		: 
				$class = PL_YPICS4::getInstance(); 
		}	
		$result = $class->select_packing_list_main4_per_control($this->ctrlno_packinglist);
		if($row = mysqli_fetch_array($result)) {
			$fkCtrl = $row['id'];
		}		
		$result2 = $class->select_packing_list_details4_per_control($fkCtrl);
		while( $row = mysqli_fetch_array($result2) ){
			$this->array_details['active_ctrlno'][]     = $row['packing_id'];
			$this->array_details['BoxNo'][] 			= $row['box_no'];
			$this->array_details['PONo'][] 				= $row['po'];
			$this->array_details['Description'][] 		= $row['description'];
			$this->array_details['ProductCode'][] 		= $row['item_code'];
			$this->array_details['Quantity'][] 			= $row['qty'];
			if( count(explode('-', $row['box_no'])) == 1 )
				// $this->array_details['UnitOfMeasurement'][] = $row['gross_weight'];qty
				$this->array_details['UnitOfMeasurement'][] = $row['qty'];
			else{
				$_tmp = explode('/', $row['gross_weight']);
				$_tmp = explode('(', $_tmp[0]);
				$_tmp = $_tmp[count($_tmp)-1];
				$this->array_details['UnitOfMeasurement'][] = $_tmp;
			}
			// echo 'box no '.$row['box_no'].' <br>';
		}
			// $this->ship_to_details = $fkCtrl;
		$result3 = $class->select_packing_list_details_by_id($fkCtrl);
		while( $row = mysqli_fetch_array($result3) ){
			$this->ship_to_details = $row['ship_to'];
		}

		// if( strpos(strtolower($this->array_details['BoxNo'][0]), 'a') )
		// 	$this->$packing_list_mode = 'a_mode';
		// else
		// 	$this->$packing_list_mode = '-_mode';

		// echo json_encode();
	}

	function CheckSpecificCasemark($page_no){
		/* Get temp values from database */
		require_once('../class/casemark.php');
		session_start();
		$ctrlno_packinglist = $this->ctrlno_packinglist;
		$return = array();
		$array_fields 	= array('casemark');
		$table		 	= 'tbl_casemark_specific_temp';
		$joins		 	= '';
		$sql_where		= 'WHERE `page_no` = "'.$page_no.'" AND `username` = "'.$_SESSION['username'].'" AND `ctrlno_packinglist` = "'.$ctrlno_packinglist.'"';
		$sql_order		= '';
		$sql_limit		= '';
		$result = CASEMARK::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		$html 			= '';
		$casemark 		= '';
		while( $row = mysqli_fetch_array($result) ){
			$casemark = urldecode($row['casemark']);
		}
		// return $this->BreakdownCasemark($result."\n");
		return $this->BreakdownCasemark($casemark);
	}
	
	function BreakdownCasemark($casemark){
		return explode("\n",$casemark);
	}
	
	function StickerCount(){
		$larget_number = 1;
		/* check if details is not empty */
		/* Not yet coded */
		$with_number_in_details = false;
		for( $x=0;$x<count($this->array_details['BoxNo']);$x++ ){
			$current_value = $this->array_details['BoxNo'][$x]; //orig data
			// $current_value = '277'; // tempo solution
			if( !preg_match("/[0-9]/i", $current_value) ){
				continue;
			}else{
				//- 1/10/2020
				// $current_value = preg_replace('/[^\d-]+/', '', $current_value); 
				$current_value = preg_replace('/[^\d-]+/', '-', $current_value); 
			}
			
			/* check if a string contains a - or ~ */
			if( strpos($current_value, '-') !== false || strpos($current_value, '~') !== false) {
				$array_value = explode('-',$current_value);
				for($y=0;$y<count($array_value);$y++){
					if( $largest_number < $array_value[$y] ){
						$largest_number = $array_value[$y];
					}
				}
				$with_number_in_details = true;
			}else{
				if( $largest_number < $current_value ){
					$largest_number = $current_value;
				}	
				$with_number_in_details = true;
			}
		}
		if(!$with_number_in_details){
			$this->sticker_count = 1;
		}else{
			$this->sticker_count = $largest_number;
		}		
	}
	
	function GenerateStickerCountLabel(){
		/* Formatting */
		/* check if the format contains a letter */
		unset($this->page_numbers);
		if( preg_match("/[a-z]/i", $this->sticker_count) ){
			$sticker_count = preg_replace('/[a-z]/i','',$this->sticker_count);
			$padding 	   = preg_replace('/[0-9]/i','',$this->sticker_count);
			for($x=1;$x<=($sticker_count);$x++){
				/* replace numbers and add the sticker count at the front */
				$this->page_numbers[] = $x;
			}
		}else{
			$sticker_count 		= str_replace(' ','',$this->sticker_count);
			$format_num_char 	= strlen($sticker_count);
			if( $format_num_char > 1 && $this->sticker_count[0] != 0){
				$format_num_char -= 1;
			}
			for($x=1;$x<=($sticker_count);$x++){
				/* add trailing zeros */
				$this->page_numbers[] = str_pad($x, $format_num_char, '0', STR_PAD_LEFT);
			}
		}
	}
	
	function Print_Label()
	{
	   // $values = $this->BreakdownCasemark($this->casemark);
	   if($this->sticker_count == 0 || $this->sticker_count == ''){
			$this->sticker_count = 1;
	   }
	   for($x=1;$x<=($this->sticker_count);$x++){
	       $values = $this->BreakdownCasemark($this->casemark);
		   if($this->page_numbers[$x-1] == ''){
			  /* add checking on what format does the end page uses */
			  $this->page_numbers[] = 1;
		   }
		   if($this->ctr==1){
			  /* add page */
			  $this->AddPage('L');
			  $this->SetBorder($this->ctr);
			  /* check for specific casemark */
			  $specific_casemark = '';
			  $specific_casemark = $this->CheckSpecificCasemark($x);
			  if($specific_casemark[0] != ''){
				$values = $specific_casemark;
			  }
			  $this->PlaceValues($values,$this->page_numbers[$x-1],5,10,$x);
		   }
		  $this->ctr++;
	   }
	}
	
	// value
	function PlaceValues($values,$page_number,$x,$y,$index)
	{
		$this->SetAutoPageBreak(false);
		$this->SetFont('Arial', 'B', $this->font_size);
		$this->SetXY($x, $y);
		// $this->Write(0, $values[0] );

		if( $this->active_ctrlno != $this->ctrlno_packinglist ){
			$this->index_data_cntr = 0;
			$this->active_ctrlno = $this->ctrlno_packinglist;
		}

		$qrcode_content = "";
		$number = '';
		$data_in = '';

		if( $this->index_data_cntr < count($this->array_details['BoxNo']) ){
			$bxno = strtolower($this->array_details['BoxNo'][0]);
			// if( strpos('abcdefghijklmnopqrstuvwxyz', $bxno[strlen($bxno)-1]) ){
			if( in_array($bxno[strlen($bxno)-1], explode(',', 'a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z')) ){
				// $box_no_holder = strtolower($this->array_details['BoxNo'][$this->index_data_cntr]);
				// for ( ; $this->index_data_cntr < count($this->array_details['BoxNo']); ) { 
				// 	$qrcode_content .= $this->array_details['PONo'][$this->index_data_cntr] . " \n" . 
				// 		$this->array_details['ProductCode'][$this->index_data_cntr] . " \n" .
				// 		$this->array_details['BoxNo'][$this->index_data_cntr] . " \n" .
				// 		$this->array_details['Quantity'][$this->index_data_cntr] . " \n";
				// 	if( $this->index_data_cntr < count($this->array_details['BoxNo'])-1 ){
				// 		if( $box_no_holder !=  strtolower($this->array_details['BoxNo'][$this->index_data_cntr+1]) ){
				// 			$this->index_data_cntr++;
				// 			break;
				// 		}
				// 	}
				// 	$this->index_data_cntr++;
				// }

				$box_no_holder = explode('-', $this->array_details['BoxNo'][$this->index_data_cntr]);
				if( count($box_no_holder) == 1 ){
					$box_no_holder = strtolower($this->array_details['BoxNo'][$this->index_data_cntr]);
					for ( ; $this->index_data_cntr < count($this->array_details['BoxNo']); ) { 
						$qrcode_content .= $this->array_details['PONo'][$this->index_data_cntr] . " \n" . 
							$this->array_details['ProductCode'][$this->index_data_cntr] . " \n" .
							$this->array_details['BoxNo'][$this->index_data_cntr] . " \n" .
							$this->array_details['Quantity'][$this->index_data_cntr] . " \n";
						if( $this->index_data_cntr < count($this->array_details['BoxNo'])-1 ){
							if( $box_no_holder !=  strtolower($this->array_details['BoxNo'][$this->index_data_cntr+1]) ){
								$this->index_data_cntr++;
								break;
							}
						}
						$this->index_data_cntr++;
					}
					$data_in = '11-';
				}
				else{
					$box_no_holder = substr(str_replace(' ', '',$box_no_holder) , 0, -1);
					$box_no_holder = explode('-', $this->array_details['BoxNo'][$this->index_data_cntr]);
					$qrcode_content .= $this->array_details['PONo'][$this->index_data_cntr] . " \n" . 
					$this->array_details['ProductCode'][$this->index_data_cntr] . " \n" .
					$this->array_details['BoxNo'][$this->index_data_cntr] . " \n" .
					$this->array_details['UnitOfMeasurement'][$this->index_data_cntr] . " \n";
					// explode('/', explode('(', $this->array_details['UnitOfMeasurement'][$this->index_data_cntr])[1])[0] . " \n";
					// $this->array_details['Quantity'][$this->index_data_cntr] . " \n";

					if( $index >= (int)$box_no_holder[0] && $index <= (int)$box_no_holder[1] ){
						if( $index == (int)$box_no_holder[1] )
							$this->index_data_cntr++;
					}

					$number = $box_no_holder[0] . "_" . $box_no_holder[1];
					$data_in = '44-';
				}
			}else{
				$add = false;
				$box_no_holder = explode('-', $this->array_details['BoxNo'][$this->index_data_cntr]);
				if( count($box_no_holder) == 1 ){

					// $qrcode_content .= $this->array_details['PONo'][$this->index_data_cntr] . " \n" . 
					// $this->array_details['ProductCode'][$this->index_data_cntr] . " \n" .
					// $this->array_details['BoxNo'][$this->index_data_cntr] . " \n" .
					// $this->array_details['Quantity'][$this->index_data_cntr] . " \n";
					// $this->index_data_cntr++;

					for ( ; $this->index_data_cntr < count($this->array_details['BoxNo']); ) { 
						$qrcode_content .= $this->array_details['PONo'][$this->index_data_cntr] . " \n" . 
							$this->array_details['ProductCode'][$this->index_data_cntr] . " \n" .
							$this->array_details['BoxNo'][$this->index_data_cntr] . " \n" .
							$this->array_details['Quantity'][$this->index_data_cntr] . " \n";
						if( $this->index_data_cntr < count($this->array_details['BoxNo'])-1 ){
							if( $box_no_holder[0] !=  strtolower($this->array_details['BoxNo'][$this->index_data_cntr+1]) ){
								$this->index_data_cntr++;
								break;
							}
						}
						$this->index_data_cntr++;
					}

					$number = $box_no_holder[0];
					$data_in = '22-';
				}
				else{
					$qrcode_content .= $this->array_details['PONo'][$this->index_data_cntr] . " \n" . 
					$this->array_details['ProductCode'][$this->index_data_cntr] . " \n" .
					$this->array_details['BoxNo'][$this->index_data_cntr] . " \n" .
					$this->array_details['UnitOfMeasurement'][$this->index_data_cntr] . " \n";
					// explode('/', explode('(', $this->array_details['UnitOfMeasurement'][$this->index_data_cntr])[1])[0] . " \n";
					// $this->array_details['Quantity'][$this->index_data_cntr] . " \n";

					if( $index >= (int)$box_no_holder[0] && $index <= (int)$box_no_holder[1] ){
						if( $index == (int)$box_no_holder[1] )
							$this->index_data_cntr++;
					}

					$number = $box_no_holder[0] . "_" . $box_no_holder[1];
					$data_in = '33-';
				}
			}
		}

		// $this->Write(0, $data_in . $values[0] );
		// $this->Write(0, 'rev3_' . $this->ctrlno_packinglist . '_' );
		$this->Write(0, $values[0] );
		// $this->Write(0, $values[0] . "_" . $number );

		$qrcode_content .= $this->ship_to_details;
		// $qrcode_content .= $values[0];

		for($ctr=1;$ctr<count($values);$ctr++){
			/* page number */
			// $qrcode_content .= "\n" . $values[$ctr];

			$check = str_replace( ' ', '', $values[$ctr] );
			if ( strpos($check, 'C/NO') !== false ) {
				$last_char = $check[strlen($check)-1];
				$letter_padding = '';
				if( preg_match("/[a-z]/i", $last_char) ){
					$letter_padding = $last_char;
				}
				$this->SetFont( 'Arial', 'B', $this->font_size );
				$this->SetXY( $x, ($y+=$this->spacing) );
				$this->Write( 0, 'C/NO.' );
				$this->SetXY( $x+40, $y );
				// $this->Write( 0, str_pad($page_number, 3, '0', STR_PAD_LEFT).$letter_padding );
				$this->Write( 0, $page_number.$letter_padding );
				$this->SetXY( $x+70, $y );
				$this->Write( 0, 'OF' );
				$this->SetXY( $x+100, $y );
				$this->Write( 0, $this->sticker_count.$letter_padding );
				$this->SetDrawColor( 0, 0, 0 );

				// $qrcode_content .= $page_number . '/' . $this->sticker_count;

				continue;
			}
			// if ( strpos($check, 'P/NO') !== false ) {
				// $this->SetFont( 'Arial', 'B', $this->font_size );
				// $this->SetXY( $x, ($y+=$this->spacing) );
				// $this->Write( 0, 'P/NO.' );
				// $this->SetXY( $x+40, $y );
				// $this->Write( 0, $page_number );
				// $this->SetXY( $x+70, $y );
				// $this->Write( 0, 'OF' );
				// $this->SetXY( $x+100, $y );
				// $this->Write( 0, $this->sticker_count );
				// $this->SetDrawColor( 0, 0, 0 );
				// continue;
			// }
			$this->SetXY( $x, ($y+=$this->spacing) );
			$this->Write( 0, $values[$ctr] );
		}
		$qrcode_content = urlencode($qrcode_content);
		// $this->Image( 'http://rapid/Casemark_YPICS4_ppts/handler/handler.php?action=qrcodeGenerator&&value=' . $qrcode_content ,$x + 113, $y - 43,20, 20, 'PNG'); //07222022
		$this->Image( 'http://rapid/Casemark_YPICS4_ppts_rev3/handler/handler.php?action=qrcodeGenerator&&value=' . $qrcode_content ,$x + 113, $y - 43,20, 20, 'PNG');
	}
// }

/* call class */
$pdf = new PDF();
// $CtrlnoArray = $pdf->LoadCtrlNo();
// for($x=0;$x<count($CtrlnoArray);$x++){
	// echo $CtrlnoArray[$x].'<br>';
	// $pdf->LoadLabelInfo($CtrlnoArray[$x]);
	// $pdf->LoadLabelDetails();
	// $pdf->StickerCount();
	// $pdf->GenerateStickerCountLabel();
	// /* do the printing here */
	// $pdf->Print_Label();
// }
$pdf->LoadLabelInfo1();
$pdf->LoadLabelDetails1();
$pdf->StickerCount1();
$pdf->GenerateStickerCountLabel1();
/* do the printing here */
$pdf->Print_Label1();
$pdf->Output();

