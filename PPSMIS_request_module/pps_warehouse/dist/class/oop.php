<?php
//Set Class name here, it's a good practice to Capitalize the first letter, I've just read it somewhere on the internet... so its up to you if will follow...
class db_pps extends mysqli 
{
	private static $instance = null;
	
	//db connection config variables
	// private $user = "ojt";	//Username
	// private $pass = "ojt1234"; //Password
	// private $dbName = "db_training"; //db_name Note: if you are going to use multiple database just place one here.. use sql to call the other one just in case, for example : database2.tablename
	// private $dbHost = "localhost"; //host
	
	
	//NO NEED TO CHANGE HERE 
	public static function getInstance() {
		if(!self::$instance instanceof self) 
		{
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function __clone()
	{
		trigger_error('Clone is not allowed.',E_USER_ERROR);
	}
	
	public function __wakeup()
	{
		trigger_error('Deserializing is not allowed.',E_USER_ERROR);
	}
	
	private function __construct() {
		$path 		= '../';
		$db_config  = 'db_config/config.php';
		if(file_exists($path.$db_config)){
			include($path.$db_config);
		}else{
			$path = '../../';
			if(file_exists($path.$db_config)){
				include($path.$db_config);
			}else{
				$path = '../../../';
				if(file_exists($path.$db_config)){
					include($path.$db_config);
				}else{
					exit;
				}
				
			}
		}
		parent::__construct($server, $username, $password, $db_name);
		
		if(mysqli_connect_error())
		{
			exit('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
		}
		parent::set_charset('utf-8');
	}

	////////////////////////////////////UP TO HERE////////////////////////////////////////
	
	////////////////////////////////////PUBLIC FUNCTIONS STARTS HERE//////////////////////////////////////////////
	public function select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit){
		$query = "SELECT SQL_CALC_FOUND_ROWS " . implode(", ", $array_fields) . " FROM {$table} {$joins} {$sql_where} {$sql_order} {$sql_limit};";
		// echo $query;
		return $this->query($query);
	}
		
	public function select_query_script($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit){
		$query = "SELECT SQL_CALC_FOUND_ROWS " . implode(", ", $array_fields) . " FROM {$table} {$joins} {$sql_where} {$sql_order} {$sql_limit};";
		return ($query);
	}
		
	public function insert_query($table,$array_fields,$array_values){
		$new_values = $this->add_quotations($array_values); 
		$query 		= "INSERT INTO {$table} (". implode(", ", $array_fields).") VALUES (". implode(",", $new_values) .");";
		$this->query($query);
		$this->save_logs('1',$query);
		return "<i class='fa fa-check-circle'></i> New record has been saved";
	}

	public function insert_query2($table2,$array_fields2,$array_values2){
		$new_values2 = $this->add_quotations($array_values2); 
		$query 		= "INSERT INTO {$table2} (". implode(", ", $array_fields2).") VALUES (". implode(",", $new_values2) .");";
		$this->query($query);
		$this->save_logs('1',$query);
		return "<i class='fa fa-check-circle'></i> New record has been saved";
	}

	public function insert_query3($table3,$array_fields3,$array_values3){
		$new_values3 = $this->add_quotations($array_values3); 
		$query 		= "INSERT INTO {$table3} (". implode(", ", $array_fields3).") VALUES (". implode(",", $new_values3) .");";
		$this->query($query);
		$this->save_logs('1',$query);
		return "<i class='fa fa-check-circle'></i> New record has been saved";
	}

	public function insert_query4($table4,$array_fields4,$array_values4){
		$new_values4 = $this->add_quotations($array_values4); 
		$query 		= "INSERT INTO {$table4} (". implode(", ", $array_fields4).") VALUES (". implode(",", $new_values4) .");";
		$this->query($query);
		$this->save_logs('1',$query);
		return "<i class='fa fa-check-circle'></i> New record has been saved";
	}
	//remove
	public function insert_or_update_query($table,$username){
		// $new_values = $this->add_quotations($array_values); 
		// $query 		= "INSERT INTO {$table} (". implode(", ", $array_fields).") VALUES (". implode(",", $new_values) .");";

		$query 		= "
			INSERT INTO {$table} (`username`) 
			VALUES ('".$username."')
			ON DUPLICATE KEY UPDATE
			`date_time`='".date('Y-m-d H:i:s')."'
			";


		$this->query($query);
		$this->save_logs('1',$query);
		return "New record has been saved";
	}
	//remove


	public function insert_query_get_last_id($table,$array_fields,$array_values){
		$new_values = $this->add_quotations($array_values);
		$query 		= "INSERT INTO {$table} (". implode(", ", $array_fields).") VALUES (". implode(",", $new_values) .");";
		$this->query($query);
		$last_id = $this->insert_id;
		$this->save_logs('1',$query);
		return $last_id;
	}

	public function update_query($table,$array_fields,$array_values,$pkid){
		$update_array =  $this->create_update_param($array_fields,$array_values);
		$query 		  = "UPDATE {$table} SET ". implode(",", $update_array) ."WHERE pkid='{$pkid}';";
		$this->query($query);
		$this->save_logs('2',$query);
		return "<i class='fa fa-check-circle'></i> Record has been updated";
	}

	public function update_query_with_other_condition($table,$array_fields,$array_values,$sql_where){
		$update_array =  $this->create_update_param($array_fields,$array_values);
		$query 		  = "UPDATE {$table} SET ". implode(",", $update_array) ." {$sql_where};";
		$this->query($query);
		$this->save_logs('2',$query);
		return "<i class='fa fa-check-circle'></i> Record has been updated";
	}

	public function update_query_with_other_condition2($table2,$array_fields2,$array_values2,$sql_where2){
		$update_array2 =  $this->create_update_param($array_fields2,$array_values2);
		$query2 		  = "UPDATE {$table2} SET ". implode(",", $update_array2) ." {$sql_where2};";
		$this->query($query2);
		$this->save_logs('2',$query2);
		return "<i class='fa fa-check-circle'></i> Record has been updated";
	}

	public function delete_query($table,$pkid){
		$query = "DELETE FROM {$table} WHERE `pkid` = '{$pkid}';";
		$this->query($query);
		$this->save_logs('3',$query);
		return "Record has been deleted";
	}
	
	public function add_quotations($array_values){
		$array_values_r = array();
		for($i=0;$i<count($array_values);$i++){
			$array_values_r[$i] = "'".$this->real_escape_string($array_values[$i])."'";
			// $array_values_r[$i] = "'".$array_values[$i]."'";
		}
		return $array_values_r;
	}

	public function create_update_param($array_fields,$array_values){
		$array_update = array();
		for($i=0;$i<count($array_fields);$i++){
			$array_update[$i] = "`".$array_fields[$i]."` = "."'".$array_values[$i]."' ";
		}
		return $array_update;
	}
	
	public function save_logs($type,$query){
		$date_today = date('Y-m-d H:i:s');
		$new_query = str_replace("'",'"',$query);
		$logs = "INSERT INTO `tbl_logs` (`type_of_query`,`query`,`date`) VALUES('".$type."','".$new_query."','".$date_today."');";
		$this->query($logs);
	}
	
}


?>
