 <?php
	function dateDifference($date_1,$date_2){
		$z = 0;
		$date = $date_1;
		while($date<$date_2){
			$date = date('Y-m-d',strtotime($date. "+1 days"));
			$z++;
		}  
	    return $z;	    
	}

    function has_dupes($array){
        $dupe_array = array();
        foreach($array as $val){
            if(++$dupe_array[$val]>1){
                return true;
            }
        }
        return false;
    }

	function split2($text){
	    $array = array();
	    foreach(explode(' ',$text) as $i=>$word)
	    {
	        if($i%2) {
	            $array[floor($i/2)] .= ' '.$word;
	        } else {
	            $array[$i/2] = $word;
	        }
	    }
	    return $array;
	}

	function getDateForSpecificDayBetweenDates($start, $end, $weekday = 0){
		$weekdays="Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday";

		$arr_weekdays=split(",", $weekdays);
		$weekday = $arr_weekdays[$weekday];
		if(!$weekday)
		    die("Invalid Weekday!");

		$start= strtotime("+0 day", strtotime($start) );
		$end= strtotime($end);

		$dateArr = array();
		$friday = strtotime($weekday, $start);
		while($friday <= $end)
		{
		    $dateArr[] = date("Y-m-d", $friday);
		    $friday = strtotime("+1 weeks", $friday);
		}
		$dateArr[] = date("Y-m-d", $friday);

		return $dateArr;
	}

	function countDayDates($from_date,$to_date,$day){
		$from_date = ($from_date);
		$to_date = ($to_date);

		while(strtotime($from_date) <= strtotime($to_date)){
		    if(date("N",strtotime($from_date))==$day){
		        $counter++;
		    }
		    $from_date = date ("Y-m-d", strtotime("+1 day", strtotime($from_date)));
		}

		return $counter;
	}

	// function get_months_in_range($start, $end, $format='timestamp', $create_empty_array=false) {
	// 	$i = $formatted_i = $start = strtotime($start);
	// 	if( $format != 'timestamp' ) $formatted_i = date($format, $i);
	// 	$end = strtotime($end);
	// 	$dates = $create_empty_array ? array($formatted_i => array()) : array($formatted_i);
	// 	while($i < $end) {
	// 		$month = $formatted_day = mktime(0, 0, 0, date('m', $i)+1, date('d',$i), date('Y', $i));
	// 		if( $format != 'timestamp' ) {
	// 			$formatted_month = date($format, $month);
	// 		}
	// 		if( $create_empty_array ) {
	// 			$dates[$formatted_month] = array();
	// 		} else {
	// 			$dates[] = $formatted_month;
	// 		}
	// 		$i = $month;
	// 	}
	// 	return $dates;
	// }
	function read_csv_file($filename){
		$csv_content = array();
		$file_handle = fopen($filename, "r");
		$header = 0;
		while(($line = fgetcsv($file_handle))!==false){
			/* remove heeader from array of data */
			if($header == 0){
				$header++;
				continue;
			}
			$csv_content[] = $line;
		}
		fclose($file_handle);
		return $csv_content;
	}
?>