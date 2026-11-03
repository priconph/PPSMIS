<?php
function datetime_convert($datetime){
	$new_datetime = '';
	if( $datetime ){
		$hh = date('H:i:s',strtotime($datetime));
		$new_datetime = date('M j, Y',strtotime($datetime));
		if($hh!='00:00:00'){
			$new_datetime.=' '.date('h:i:s A',strtotime($datetime));
		}
	}
	return $new_datetime;
}
?>