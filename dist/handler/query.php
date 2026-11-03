<?php
	class db_ppsmis extends mysqli{
		private static $instance = null;

		private $username 	= "root";	
		private $password 	= "r@p1d0nl1n3"; 
		private $db_name 	= "db_ppsmis"; 
		private $server 	= "localhost"; 

		public static function getInstance(){
			if(!self::$instance instanceof self){
				self::$instance = new self;
			}
			return self::$instance;
		}

		public function __clone(){
			trigger_error('Clone is not allowed.',E_USER_ERROR);
		}
		
		public function __wakeup(){
			trigger_error('Deserializing is not allowed.',E_USER_ERROR);
		}
		
		private function __construct(){
			parent::__construct($this->server, $this->username, $this->password, $this->db_name);
			if(mysqli_connect_error())
			{
				exit('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
			}
			parent::set_charset('utf-8');
		}

		public function select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit){
			$query = "SELECT SQL_CALC_FOUND_ROWS " . implode(", ", $array_fields) . " FROM {$table} {$joins} {$sql_where} {$sql_order} {$sql_limit};";
			return $this->query($query);
		}

		public function select_count_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit,$as_count){
			$query = "(SELECT COUNT SQL_CALC_FOUND_ROWS " . implode(", ", $array_fields) . " FROM {$table} {$joins} {$sql_where} {$sql_order} {$sql_limit}) AS ".$as_count.";";
			return $this->query($query);
		}

		public function query_select_count_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit,$as_count){
			$query = "(SELECT COUNT SQL_CALC_FOUND_ROWS " . implode(", ", $array_fields) . " FROM {$table} {$joins} {$sql_where} {$sql_order} {$sql_limit}) AS ".$as_count.";";
			return $query;
		}

		public function select_sum_query($count_what,$table,$joins,$sql_where,$sql_order,$sql_limit,$as_ano){
			$query = "SELECT SUM(".$count_what.") AS ".$as_ano." FROM ".$table." ".$joins." ".$sql_where." ".$sql_order." ".$sql_limit.";";
			return $this->query($query);
		}

		public function query_select_sum_query($count_what,$table,$joins,$sql_where,$sql_order,$sql_limit){
			$query = "SELECT SUM(".$count_what.") FROM ".$table." ".$joins." ".$sql_where." ".$sql_order." ".$sql_limit.";";
			return $query;
		}

		public function query_select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit){
			$query = "SELECT SQL_CALC_FOUND_ROWS " . implode(", ", $array_fields) . " FROM {$table} {$joins} {$sql_where} {$sql_order} {$sql_limit};";
			return $query;
		}
		
		public function insert_query($table,$array_fields,$array_values){
			$new_values = $this->add_quotations($array_values); 
			$query 		= "INSERT INTO {$table} (". implode(", ", $array_fields).") VALUES (". implode(",", $new_values) .");";
			$this->query($query);
		}
		
		public function insert_query_return_id($table,$array_fields,$array_values,$as_target,$as_name){
			$new_values = $this->add_quotations($array_values); 
			$query 		= "INSERT INTO {$table} (". implode(", ", $array_fields).") VALUES (". implode(",", $new_values) .");";
			$return      = "SELECT ".$as_target." AS ".$as_name." FROM ".$table." WHERE ".$as_target."=@@Identity;";
			$this->query($query);
			return $this->query($return);
		}
		
		public function query_insert_query_return_id($table,$array_fields,$array_values,$as_target,$as_name){
			$new_values = $this->add_quotations($array_values); 
			$query 		= "INSERT INTO {$table} (". implode(", ", $array_fields).") VALUES (". implode(",", $new_values) .");";
			$return      = "SELECT ".$as_target." AS ".$as_name." FROM ".$table." WHERE ".$as_target."=@@Identity;";
			return $query;
		}

		public function insert_query_return_success($table,$array_fields,$array_values){
			$new_values = $this->add_quotations($array_values); 
			$query 		= "INSERT INTO {$table} (". implode(", ", $array_fields).") VALUES (". implode(",", $new_values) .");";
			$status = $this->query($query);
			return $status;
		}

		public function query_insert_query_return_success($table,$array_fields,$array_values){
			$new_values = $this->add_quotations($array_values); 
			$query 		= "INSERT INTO {$table} (". implode(", ", $array_fields).") VALUES (". implode(",", $new_values) .");";	
			return $query;
		}

		public function insert_selected_query($table,$array_fields,$select){
			$query 		= "INSERT INTO {$table} (". implode(", ", $array_fields).") ".$select.";";
			return $this->query($query);
		}

		public function insert_selected_query_return_success($table,$array_fields,$select){
			$query 		= "INSERT INTO {$table} (". implode(", ", $array_fields).") ".$select.";";
			return $this->query($query);
		}

		public function insert_where_query($table,$array_fields,$array_values,$where){
			$new_values = $this->add_quotations($array_values); 
			$query 		= "INSERT INTO {$table} (". implode(", ", $array_fields).") VALUES (". implode(",", $new_values) .") ".$where.";";
			$this->query($query);
			return $query;
		}

		public function query_insert_query($table,$array_fields,$array_values){
			$new_values = $this->add_quotations($array_values); 
			$query 		= "INSERT INTO {$table} (". implode(", ", $array_fields).") VALUES (". implode(",", $new_values) .");";
			return $query;
		}

		public function query_insert_selected_query($table,$array_fields,$select){
			$new_values = $this->add_quotations($array_values); 
			$query 		= "INSERT INTO {$table} (". implode(", ", $array_fields).") ".$select.";";
			return $query;
		}

		public function update_query($table,$array_fields,$array_values,$pkid,$idname){
			$update_array =  $this->create_update_param($array_fields,$array_values);
			$query 		  = "UPDATE {$table} SET ". implode(",", $update_array) ."WHERE ".$idname."='{$pkid}';";
			$this->query($query);
		}

		public function update_query_2_conditions($table,$array_fields,$array_values,$pkid,$idname,$pkid2,$idname2){
			$update_array =  $this->create_update_param($array_fields,$array_values);
			$query 		  = "UPDATE {$table} SET ". implode(",", $update_array) ."WHERE ".$idname."='{$pkid}' AND ".$idname2."='{$pkid2}';";
			$this->query($query);
		}

		public function query_update_query_2_conditions($table,$array_fields,$array_values,$pkid,$idname,$pkid2,$idname2){
			$update_array =  $this->create_update_param($array_fields,$array_values);
			$query 		  = "UPDATE {$table} SET ". implode(",", $update_array) ."WHERE ".$idname."='{$pkid}' AND ".$idname2."='{$pkid2}';";
			return $query;
		}	

		public function update_query_multi_conditions($table,$array_fields,$array_values,$where){
			$update_array =  $this->create_update_param($array_fields,$array_values);
			$query 		  = "UPDATE {$table} SET ". implode(",", $update_array) ."WHERE ".$where.";";
			$this->query($query);
		}	

		public function query_update_query_multi_conditions($table,$array_fields,$array_values,$where){
			$update_array =  $this->create_update_param($array_fields,$array_values);
			$query 		  = "UPDATE {$table} SET ". implode(",", $update_array) ."WHERE ".$where.";";
			return $query;
		}	

		public function update_query_with_return($table,$array_fields,$array_values,$pkid,$idname){
			$update_array =  $this->create_update_param($array_fields,$array_values);
			$query 		  = "UPDATE {$table} SET ". implode(",", $update_array) ."WHERE ".$idname."='{$pkid}';";
			$status = $this->query($query);
			return $status;
		}

		public function query_update_query($table,$array_fields,$array_values,$pkid,$idname){
			$update_array =  $this->create_update_param($array_fields,$array_values);
			$query 		  = "UPDATE {$table} SET ". implode(",", $update_array) ."WHERE ".$idname."='{$pkid}';";
			return $query;
		}

		public function delete_query($table,$idname,$pkid){
			$query = "DELETE FROM {$table} WHERE `".$idname."` = '{$pkid}';";
			$this->query($query);
		}

		public function delete_in_query($table,$in){
			$query = "DELETE FROM {$table} ".$in.";";
			$this->query($query);
		}	

		public function query_delete_in_query($table,$in){
			$query = "DELETE FROM {$table} ".$in.";";
			return $query;
		}	

		public function add_quotations($array_values){
			$array_values_r = array();
			for($i=0;$i<count($array_values);$i++){
				if(isset($array_values[$i]) || $array_values[$i] != ''){
					$array_values[$i] = $this->real_escape_string($array_values[$i]);
					$array_values_r[$i] = "'".$array_values[$i]."'";
				}else{
					$array_values_r[$i] = '';
				}
			}
			return $array_values_r;
		}

		public function create_update_param($array_fields,$array_values){
			$array_update = array();
			for($i=0;$i<count($array_fields);$i++){
				$array_update[$i] = "`".$array_fields[$i]."` = "."'".$this->real_escape_string($array_values[$i])."' ";
			}
			return $array_update;
		}

		// public function save_logs($type,$query){
		// 	$date_today = date('Y-m-d H:i:s');
		// 	$new_query = str_replace("'",'"',$query);
		// 	$logs = "INSERT INTO `tbl_logs` (`type_of_query`,`query`,`date`) VALUES('".$type."','".$new_query."','".$date_today."');";
		// 	$this->query($logs);
		// }
		
		public function check_unset($variable){
			if(!isset($variable)){
				$new_variable = '';
			}else{
				$new_variable = $variable;
			}
			return $new_variable;
		}
		
		public function check_if_array($array){
			if(!is_array($array)){
				$new_array = array($array);
			}else{
				$new_array = $array;
			}
			return $new_array;
		}
	}

	class db_pps extends mysqli{
		private static $instance = null;

		private $username 	= "root";	
		private $password 	= "r@p1d0nl1n3"; 
		private $db_name 	= "db_pps"; 
		private $server 	= "localhost"; 

		public static function getInstance(){
			if(!self::$instance instanceof self){
				self::$instance = new self;
			}
			return self::$instance;
		}

		public function __clone(){
			trigger_error('Clone is not allowed.',E_USER_ERROR);
		}
		
		public function __wakeup(){
			trigger_error('Deserializing is not allowed.',E_USER_ERROR);
		}
		
		private function __construct(){
			parent::__construct($this->server, $this->username, $this->password, $this->db_name);
			if(mysqli_connect_error())
			{
				exit('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
			}
			parent::set_charset('utf-8');
		}

		public function select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit){
			$query = "SELECT SQL_CALC_FOUND_ROWS " . implode(", ", $array_fields) . " FROM {$table} {$joins} {$sql_where} {$sql_order} {$sql_limit};";
			return $this->query($query);
		}

		public function select_count_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit,$as_count){
			$query = "(SELECT COUNT SQL_CALC_FOUND_ROWS " . implode(", ", $array_fields) . " FROM {$table} {$joins} {$sql_where} {$sql_order} {$sql_limit}) AS ".$as_count.";";
			return $this->query($query);
		}

		public function query_select_count_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit,$as_count){
			$query = "(SELECT COUNT SQL_CALC_FOUND_ROWS " . implode(", ", $array_fields) . " FROM {$table} {$joins} {$sql_where} {$sql_order} {$sql_limit}) AS ".$as_count.";";
			return $query;
		}

		public function select_sum_query($count_what,$table,$joins,$sql_where,$sql_order,$sql_limit,$as_ano){
			$query = "SELECT SUM(".$count_what.") AS ".$as_ano." FROM ".$table." ".$joins." ".$sql_where." ".$sql_order." ".$sql_limit.";";
			return $this->query($query);
		}

		public function query_select_sum_query($count_what,$table,$joins,$sql_where,$sql_order,$sql_limit){
			$query = "SELECT SUM(".$count_what.") FROM ".$table." ".$joins." ".$sql_where." ".$sql_order." ".$sql_limit.";";
			return $query;
		}

		public function query_select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit){
			$query = "SELECT SQL_CALC_FOUND_ROWS " . implode(", ", $array_fields) . " FROM {$table} {$joins} {$sql_where} {$sql_order} {$sql_limit};";
			return $query;
		}
		
		public function insert_query($table,$array_fields,$array_values){
			$new_values = $this->add_quotations($array_values); 
			$query 		= "INSERT INTO {$table} (". implode(", ", $array_fields).") VALUES (". implode(",", $new_values) .");";
			$this->query($query);
		}
		
		public function insert_query_return_id($table,$array_fields,$array_values,$as_target,$as_name){
			$new_values = $this->add_quotations($array_values); 
			$query 		= "INSERT INTO {$table} (". implode(", ", $array_fields).") VALUES (". implode(",", $new_values) .");";
			$return      = "SELECT ".$as_target." AS ".$as_name." FROM ".$table." WHERE ".$as_target."=@@Identity;";
			$this->query($query);
			return $this->query($return);
		}
		
		public function query_insert_query_return_id($table,$array_fields,$array_values,$as_target,$as_name){
			$new_values = $this->add_quotations($array_values); 
			$query 		= "INSERT INTO {$table} (". implode(", ", $array_fields).") VALUES (". implode(",", $new_values) .");";
			$return      = "SELECT ".$as_target." AS ".$as_name." FROM ".$table." WHERE ".$as_target."=@@Identity;";
			return $query;
		}

		public function insert_query_return_success($table,$array_fields,$array_values){
			$new_values = $this->add_quotations($array_values); 
			$query 		= "INSERT INTO {$table} (". implode(", ", $array_fields).") VALUES (". implode(",", $new_values) .");";
			$status = $this->query($query);
			return $status;
		}

		public function query_insert_query_return_success($table,$array_fields,$array_values){
			$new_values = $this->add_quotations($array_values); 
			$query 		= "INSERT INTO {$table} (". implode(", ", $array_fields).") VALUES (". implode(",", $new_values) .");";	
			return $query;
		}

		public function insert_selected_query($table,$array_fields,$select){
			$query 		= "INSERT INTO {$table} (". implode(", ", $array_fields).") ".$select.";";
			$this->query($query);
		}

		public function insert_selected_query_return_success($table,$array_fields,$select){
			$query 		= "INSERT INTO {$table} (". implode(", ", $array_fields).") ".$select.";";
			return $this->query($query);
		}

		public function insert_where_query($table,$array_fields,$array_values,$where){
			$new_values = $this->add_quotations($array_values); 
			$query 		= "INSERT INTO {$table} (". implode(", ", $array_fields).") VALUES (". implode(",", $new_values) .") ".$where.";";
			$this->query($query);
		}

		public function query_insert_query($table,$array_fields,$array_values){
			$new_values = $this->add_quotations($array_values); 
			$query 		= "INSERT INTO {$table} (". implode(", ", $array_fields).") VALUES (". implode(",", $new_values) .");";
			return $query;
		}

		public function query_insert_selected_query($table,$array_fields,$select){
			$new_values = $this->add_quotations($array_values); 
			$query 		= "INSERT INTO {$table} (". implode(", ", $array_fields).") ".$select.";";
			return $query;
		}

		public function update_query($table,$array_fields,$array_values,$pkid,$idname){
			$update_array =  $this->create_update_param($array_fields,$array_values);
			$query 		  = "UPDATE {$table} SET ". implode(",", $update_array) ."WHERE ".$idname."='{$pkid}';";
			$this->query($query);
		}

		public function update_query_2_conditions($table,$array_fields,$array_values,$pkid,$idname,$pkid2,$idname2){
			$update_array =  $this->create_update_param($array_fields,$array_values);
			$query 		  = "UPDATE {$table} SET ". implode(",", $update_array) ."WHERE ".$idname."='{$pkid}' AND ".$idname2."='{$pkid2}';";
			$this->query($query);
		}

		public function query_update_query_2_conditions($table,$array_fields,$array_values,$pkid,$idname,$pkid2,$idname2){
			$update_array =  $this->create_update_param($array_fields,$array_values);
			$query 		  = "UPDATE {$table} SET ". implode(",", $update_array) ."WHERE ".$idname."='{$pkid}' AND ".$idname2."='{$pkid2}';";
			return $query;
		}	

		public function update_query_multi_conditions($table,$array_fields,$array_values,$where){
			$update_array =  $this->create_update_param($array_fields,$array_values);
			$query 		  = "UPDATE {$table} SET ". implode(",", $update_array) ."WHERE ".$where.";";
			$this->query($query);
		}	

		public function query_update_query_multi_conditions($table,$array_fields,$array_values,$where){
			$update_array =  $this->create_update_param($array_fields,$array_values);
			$query 		  = "UPDATE {$table} SET ". implode(",", $update_array) ."WHERE ".$where.";";
			return $query;
		}	

		public function update_query_with_return($table,$array_fields,$array_values,$pkid,$idname){
			$update_array =  $this->create_update_param($array_fields,$array_values);
			$query 		  = "UPDATE {$table} SET ". implode(",", $update_array) ."WHERE ".$idname."='{$pkid}';";
			$status = $this->query($query);
			return $status;
		}

		public function query_update_query($table,$array_fields,$array_values,$pkid,$idname){
			$update_array =  $this->create_update_param($array_fields,$array_values);
			$query 		  = "UPDATE {$table} SET ". implode(",", $update_array) ."WHERE ".$idname."='{$pkid}';";
			return $query;
		}

		public function delete_query($table,$idname,$pkid){
			$query = "DELETE FROM {$table} WHERE `".$idname."` = '{$pkid}';";
			$this->query($query);
		}

		public function delete_in_query($table,$in){
			$query = "DELETE FROM {$table} ".$in.";";
			$this->query($query);
		}	

		public function query_delete_in_query($table,$in){
			$query = "DELETE FROM {$table} ".$in.";";
			return $query;
		}	

		public function add_quotations($array_values){
			$array_values_r = array();
			for($i=0;$i<count($array_values);$i++){
				if(isset($array_values[$i]) || $array_values[$i] != ''){
					$array_values[$i] = $this->real_escape_string($array_values[$i]);
					$array_values_r[$i] = "'".$array_values[$i]."'";
				}else{
					$array_values_r[$i] = '';
				}
			}
			return $array_values_r;
		}

		public function create_update_param($array_fields,$array_values){
			$array_update = array();
			for($i=0;$i<count($array_fields);$i++){
				$array_update[$i] = "`".$array_fields[$i]."` = "."'".$this->real_escape_string($array_values[$i])."' ";
			}
			return $array_update;
		}

		public function execute_query($query){
			return $this->query($query);
		}
		
		// public function save_logs($type,$query){
		// 	$date_today = date('Y-m-d H:i:s');
		// 	$new_query = str_replace("'",'"',$query);
		// 	$logs = "INSERT INTO `tbl_logs` (`type_of_query`,`query`,`date`) VALUES('".$type."','".$new_query."','".$date_today."');";
		// 	$this->query($logs);
		// }
	}

	class HRIS extends mysqli{
		private static $instance = null;
		private $user = "root";
		private $pass = "newd3v";
		private $dbName = "db_hris";
		private $dbHost = "192.168.3.240";

		public static function getInstance(){
			if(!self::$instance instanceof self){
				self::$instance = new self;
			}
			return self::$instance;
		}

		public function __clone(){
			trigger_error('Clone is not allowed.',E_USER_ERROR);
		}

		public function __wakeup(){
			trigger_error('Deserializing is not allowed.',E_USER_ERROR);
		}

		private function __construct(){
			parent::__construct($this->dbHost, $this->user, $this->pass, $this->dbName);
			if(mysqli_connect_error()){
				exit('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
			}
			parent::set_charset('utf-8');
		}

		public function select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit){
			$query = "SELECT SQL_CALC_FOUND_ROWS " . implode(", ", $array_fields) . " FROM {$table} {$joins} {$sql_where} {$sql_order} {$sql_limit};";
			return $this->query($query);
		}

		public function select_query_script($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit){
			$query = "SELECT SQL_CALC_FOUND_ROWS " . implode(", ", $array_fields) . " FROM {$table} {$joins} {$sql_where} {$sql_order} {$sql_limit};";
			return $query;
		}
    }
?>