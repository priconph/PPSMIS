<?php
class PPSW extends mysqli
{
	private static $instance = null;
	
	//db connection config variables
	private $user = "root";
	private $pass = "r@p1d0nl1n3";
	private $dbName = "db_pps"; //live
	// private $dbName = "db_pps_v2";
	private $dbHost = "localhost";

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
		parent::__construct($this->dbHost, $this->user, $this->pass, $this->dbName);
		if(mysqli_connect_error())
		{
			exit('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
		}
		parent::set_charset('utf-8');
	}

	////////////////////////////////////PUBLIC FUNCTIONS STARTS HERE//////////////////////////////////////////////
	
	//04082024 by Nessa
	// public function addNewProduct($Classification,$ProductLine,$PartNumber,$MaterialType,$Model,$Balance,$LastUpdate,$Username,$Supplier,$Cost){
	// 	$query = "INSERT INTO tbl_Warehouse (Classification,ProductLine,PartNumber,MaterialType,Model,Balance,LastUpdate,Username,Supplier,Cost) VALUES('$Classification','$ProductLine','$PartNumber','$MaterialType','$Model','$Balance','$LastUpdate','$Username','$Supplier','$Cost')";
	// 	return $this->query($query);
	// }

	public function addNewProduct($Classification,$ProductLine,$PartNumber,$MaterialType,$Model,$Balance,$LastUpdate,$Username,$Supplier,$Cost,$Factory){
		$query = "INSERT INTO tbl_Warehouse (Classification,ProductLine,PartNumber,MaterialType,Model,Balance,LastUpdate,Username,Supplier,Cost,Factory) VALUES('$Classification','$ProductLine','$PartNumber','$MaterialType','$Model','$Balance','$LastUpdate','$Username','$Supplier','$Cost','$Factory')";
		return $this->query($query);
	}

	public function addNewItem($partcode,$ProductLine,$partname,$Model){
		$query = "INSERT INTO tbl_itemList (partcode,ProductLine,partname,Model) VALUES ('$partcode','$ProductLine','$partname','$Model')";
		return $this->query($query);
	}

	public function addTotal($fkid,$BOH,$IN,$OUT,$EOH,$LastUpdate){
		$query = "INSERT INTO tbl_received VALUES('','$fkid','$BOH','$IN','$OUT','$EOH','$LastUpdate')";
		return $this->query($query);
	}
	
	public function addNewProductWithrawal($fkid,$Date,$Price,$InvoiceNo,$TransferSlipNo,$BOH,$IN,$OUT,$EOH,$RecordedBy,$Remarks,$inspection_class,$Lot_number,$LastUpdate,$updated_at,$Username,$AcknowledgedBy){
		$query = "INSERT INTO tbl_WarehouseTransaction VALUES('','$fkid','$Date','','$InvoiceNo','$TransferSlipNo','$BOH','$IN','$OUT','$EOH','$RecordedBy','$Remarks','$inspection_class','$Lot_number','$LastUpdate','$updated_at','$Username','$AcknowledgedBy')";
		return $this->query($query);
	}
	//used on add module
	public function addNewProductWithrawal2($Date,$InvoiceNo,$TransferSlipNo,$BOH,$IN,$OUT,$EOH,$RecordedBy,$Remarks,$LastUpdate,$updated_at,$Username){
		$get_max_id = $this->query("SELECT MAX(`id`) AS id FROM tbl_Warehouse;");
		while($row = mysqli_fetch_array($get_max_id)){
			$fkid = $row['id'];
		}
		$query = "INSERT INTO tbl_WarehouseTransaction VALUES('','$fkid','$Date','$InvoiceNo','$TransferSlipNo','$BOH','$IN','$OUT','$EOH','$RecordedBy','$Remarks','$LastUpdate','$updated_at','$Username')";
		return $this->query($query);
	}

	public function select_invoice_num() {
		$query = ("SELECT DISTINCT InvoiceNo FROM tbl_WarehouseTransaction WHERE InvoiceNo != ' ' ");
		return $this->query($query);		
	}
	

	public function acknowledge($id,$AcknowledgedBy){
		$query = "UPDATE tbl_Warehouse_TranSlip SET AcknowledgedBy = '$AcknowledgedBy', Status = '1' WHERE id = '$id';";
		return $this->query($query);
	}

	public function getResinInfoMain(){
		/* $query = "SELECT c.*, p1.*
				FROM tbl_Warehouse c
				LEFT JOIN tbl_WarehouseTransaction p1 ON (c.id = p1.pkid)
				LEFT OUTER JOIN tbl_WarehouseTransaction p2 ON (c.id = p2.fkid AND 
					(p1.pkid < p2.pkid))
				WHERE p2.pkid IS NULL";  */
		// $query = "SELECT A.*,tbl_Warehouse_Classification.Classification,(SELECT Eoh FROM tbl_WarehouseTransaction WHERE fkid=A.id ORDER BY Date DESC, pkid DESC LIMIT 1) AS EOH  FROM tbl_Warehouse AS A LEFT JOIN tbl_Warehouse_Classification ON A.Classification=tbl_Warehouse_Classification.id ORDER BY A.Classification ASC, A.PartNumber;";//change novs 2019-07-29
		$queryxx = "
			SELECT * FROM(
				SELECT A.*,tbl_Warehouse_Classification.Classification Classification2,(SELECT Eoh FROM tbl_WarehouseTransaction WHERE fkid=A.id ORDER BY Date DESC, pkid DESC LIMIT 1) AS EOH  FROM tbl_Warehouse AS A LEFT JOIN tbl_Warehouse_Classification ON A.Classification=tbl_Warehouse_Classification.id ORDER BY A.Classification ASC, A.PartNumber
			)T_ALL
			WHERE EOH IS NOT NULL

			;";//change novs 2019-07-29

		$queryxxxxx = "
			SELECT A.*, B.Classification Classification2, (d_BOH+c_EOH) EOH FROM
			tbl_Warehouse A
			LEFT JOIN tbl_Warehouse_Classification B
			ON A.Classification=B.id
			LEFT JOIN
			( SELECT fkid c_fkid, (SUM(`In`) - SUM(`Out`)) c_EOH FROM tbl_WarehouseTransaction GROUP BY fkid ORDER BY Date DESC, pkid DESC ) C
			ON A.id = C.c_fkid

			LEFT JOIN 
			(
				SELECT * FROM (
					SELECT fkid d_fkid, Boh d_BOH FROM tbl_WarehouseTransaction ORDER BY Date ASC, pkid ASC
				) AA
				GROUP BY d_fkid
			) D
			ON A.id = D.d_fkid

			WHERE c_EOH IS NOT NULL
			ORDER BY A.Classification ASC, A.PartNumber
			;";//change novs 2019-08-21

		$queryxxxx = "
			SELECT A.*, B.Classification Classification2, (d_BOH+c_EOH) EOH FROM
			tbl_Warehouse A
			LEFT JOIN tbl_Warehouse_Classification B
			ON A.Classification=B.id
			LEFT JOIN
			( SELECT fkid c_fkid, (SUM(`In`) - SUM(`Out`)) c_EOH FROM tbl_WarehouseTransaction GROUP BY fkid ORDER BY Date DESC, pkid DESC ) C
			ON A.id = C.c_fkid

			LEFT JOIN 
			(
				SELECT * FROM (
					SELECT fkid d_fkid, Boh d_BOH FROM tbl_WarehouseTransaction ORDER BY Date ASC, pkid ASC
				) AA
				GROUP BY d_fkid
			) D
			ON A.id = D.d_fkid

			ORDER BY A.Classification ASC, A.PartNumber
			;";//change novs 2019-09-02 due to new materials not viewable

		$query = "
			SELECT A.*, B.Classification Classification2, (d_BOH+c_EOH) EOH FROM
			tbl_Warehouse A
			LEFT JOIN tbl_Warehouse_Classification B
			ON A.Classification=B.id
			LEFT JOIN
			( SELECT fkid c_fkid, (SUM(`In`) - SUM(`Out`)) c_EOH FROM tbl_WarehouseTransaction GROUP BY fkid ORDER BY Date DESC, pkid DESC ) C
			ON A.id = C.c_fkid

			LEFT JOIN 
			(
				SELECT * FROM (
					SELECT fkid d_fkid, Boh d_BOH FROM tbl_WarehouseTransaction ORDER BY Date ASC, pkid ASC
				) AA
				GROUP BY d_fkid
			) D
			ON A.id = D.d_fkid

			WHERE c_EOH IS NOT NULL or (year(LastUpdate) > 2019 and LastUpdate <> 42065)
			ORDER BY A.Classification ASC, A.PartNumber
			;";//change novs 2020-12-09

		return $this->query($query);
	}
	
	public function getResin_TransferSlip(){
		$query = "SELECT * FROM tbl_Warehouse_TranSlip ORDER BY `ControlNo` DESC;";
		return $this->query($query);
	}
	
	public function getResin_TransferSlip_lst_ctrl(){
		/* $query = "SELECT c.*, p1.*
				FROM tbl_Warehouse c
				LEFT JOIN tbl_WarehouseTransaction p1 ON (c.id = p1.pkid)
				LEFT OUTER JOIN tbl_WarehouseTransaction p2 ON (c.id = p2.fkid AND 
					(p1.pkid < p2.pkid))
				WHERE p2.pkid IS NULL";  */
		// $query = "SELECT MAX(REPLACE (`ControlNo`, '-Resin', '')) AS ControlNo FROM `tbl_Warehouse_TranSlip`;";
		$query = "SELECT `ControlNo` AS ControlNo FROM `tbl_Warehouse_TranSlip` ORDER BY `id` DESC LIMIT 0,1;";
		return $this->query($query);
	}
	
	public function update_transfer_slip($pkid,$ControlNo){
		/* $query = "SELECT c.*, p1.*
				FROM tbl_Warehouse c
				LEFT JOIN tbl_WarehouseTransaction p1 ON (c.id = p1.pkid)
				LEFT OUTER JOIN tbl_WarehouseTransaction p2 ON (c.id = p2.fkid AND 
					(p1.pkid < p2.pkid))
				WHERE p2.pkid IS NULL";  */
		$query = "UPDATE `tbl_WarehouseTransaction` SET `TransferSlipNo`='$ControlNo' WHERE `pkid`='$pkid';";
		return $this->query($query);
	}
	
	public function save_new_transfer_slip($ProductLine,$ControlNo,$Date,$IssuedBy,$LastUpdate,$Username){
		$query = "INSERT INTO `tbl_Warehouse_TranSlip`(`ProductLine`, `ControlNo`, `Date`, `IssuedBy`, `LastUpdate`, `Username`) VALUES ('$ProductLine','$ControlNo','$Date','$IssuedBy','$LastUpdate','$Username');";
		return $this->query($query);
		//return $query;
	}
	
	public function get_max_control_no(){
		$query = $this->query("SELECT MAX(`ControlNo`) AS ControlNo FROM `tbl_Warehouse_TranSlip`;");
		while($row = mysqli_fetch_array()){
			$max_control = $row['ControlNo'];
		}
	}
	
	public function load_transfer_slip_transactions(){
		//di ka pa tapos dito, filter mo kung existing na sya sa mga list or check mo kung meron na sya transfer slip no
		//temporary, di pala ok to kase i loload ko pala kada add mag pwede mag double input kung sakali man
		$query  = "SELECT `tw`.`PartNumber`,`tw`.`MaterialType`,`twt`.`pkid`,`twt`.`Out`,`twt`.`Remarks` ";
		$query .= "FROM `tbl_Warehouse` `tw` ";
		$query .= "INNER JOIN tbl_WarehouseTransaction `twt` ";
		$query .= "ON `tw`.`id` = `twt`.`fkid` ";
		$query .= "WHERE `twt`.`Out` <> '0' AND `twt`.`TransferSlipNo` = '' ";
		$query .= "ORDER BY `pkid`;";
		return $this->query($query);
	}
	
	public function getResinInfoById($id){
		$query = "SELECT * FROM tbl_Warehouse WHERE id='$id'";
		return $this->query($query);
	}
	
	public function getResinTransactionInfo($fkid){
		$query = "SELECT * FROM tbl_WarehouseTransaction WHERE fkid='$fkid' ORDER BY `Date` DESC, `pkid` DESC";
		return $this->query($query);
	}

	public function getResinTransactionInfo_auto($fkid){//novs auto eoh and boh
		$query = "SELECT * FROM tbl_WarehouseTransaction WHERE fkid='$fkid' ORDER BY `Date` ASC, `pkid` ASC";
		// $query = "SELECT a.*, b.* FROM tbl_WarehouseTransaction a
		// INNER JOIN tbl_Warehouse b on b.id = a.fkid
		// WHERE a.fkid='$fkid' 
		// ORDER BY a.Date ASC, a.pkid ASC";
		return $this->query($query);
	}

	// public function updateBalance($id,$Classification,$ProductLine,$PartNumber,$MaterialType,$Model,$Balance,$Cost,$Supplier,$LastUpdate,$Username){
	// 	$query = "UPDATE tbl_Warehouse SET Classification='$Classification', ProductLine='$ProductLine', PartNumber = '$PartNumber', MaterialType = '$MaterialType', Model = '$Model', Supplier = '$Supplier', Balance='$Balance', LastUpdate='$LastUpdate',Username='$Username' WHERE `id` ='$id'";
	// 	return $this->query($query);
	// 	//return $query;
	// }

	public function updateBalance($id,$Classification,$ProductLine,$PartNumber,$MaterialType,$Model,$Balance,$Cost,$Factory,$Supplier,$LastUpdate,$Username){
		$query = "UPDATE tbl_Warehouse SET Classification='$Classification', ProductLine='$ProductLine', PartNumber = '$PartNumber', MaterialType = '$MaterialType', Model = '$Model', Factory = '$Factory', Supplier = '$Supplier', Balance='$Balance', LastUpdate='$LastUpdate',Username='$Username' WHERE `id` ='$id'";
		return $this->query($query);
		//return $query;
	}

	public function updateBalancePerTransaction($fkid, $EOH){ // CHAN 08-13-2024
		$query = "UPDATE tbl_Warehouse SET Balance='$EOH' WHERE `id` ='$fkid'";
		return $this->query($query);
		//return $query;
	}

	// public function updateItem($pkid_itemlist,$Classification,$partcode,$ProductLine,$partname,$Model){
	// 	$query = "UPDATE tbl_itemList SET Classification='$Classification', partcode = '$partcode', ProductLine='$ProductLine', partname = '$partname', Model = '$Model' WHERE `pkid_itemlist` ='$pkid_itemlist'";
	// 	return $this->query($query);
	// 	//return $query;
	// }

	public function updateItem($pkid_itemlist,$Classification,$partcode,$ProductLine,$partname,$Model,$Factory){
		$query = "UPDATE tbl_itemList SET Classification='$Classification', partcode = '$partcode', ProductLine='$ProductLine', partname = '$partname', Model = '$Model', Factory = '$Factory' WHERE `pkid_itemlist` ='$pkid_itemlist'";
		return $this->query($query);
		//return $query;
	}
	
	public function load_transfer_slip($id){
		$query = "SELECT * FROM `tbl_Warehouse_TranSlip` WHERE `id`='$id';";
		return $this->query($query);
	}
	
	public function load_transfer_slip_per_control($ControlNo){
		$query = "SELECT `tw`.`PartNumber`,`tw`.`MaterialType`,`twt`.`pkid`, `twt`.`Out`,`twt`.`Remarks` FROM `tbl_WarehouseTransaction` `twt` INNER JOIN `tbl_Warehouse` `tw` ON `tw`.`id` = `twt`.`fkid` WHERE `twt`.`TransferSlipNo` = '$ControlNo';";
		return $this->query($query);
	}
	
	public function deleteResinRecord($pkid){
		$query = "DELETE FROM tbl_WarehouseTransaction WHERE pkid='$pkid'";
		return $this->query($query);
	}
	
	//Export to Excel Update Page query
	public function exportResinExcel($fkid,$startDate,$endDate){
		if($startDate!=""){
			$query = "SELECT * FROM tbl_WarehouseTransaction WHERE fkid='$fkid' AND Date BETWEEN '$startDate' AND '$endDate' ORDER BY Date";
		}else{
			$query = "SELECT * FROM tbl_WarehouseTransaction WHERE fkid='$fkid' ORDER BY Date";
		}
		return $this->query($query);
	}
	//Export to Excel Update Page query
	public function exportResinExcel_auto($fkid,$startDate,$endDate){
		if($startDate!=""){
			$query = "SELECT * FROM tbl_WarehouseTransaction WHERE fkid='$fkid' AND Date BETWEEN '$startDate' AND '$endDate' ORDER BY `Date` ASC, `pkid` ASC";
		}else{
			$query = "SELECT * FROM tbl_WarehouseTransaction WHERE fkid='$fkid' ORDER BY `Date` ASC, `pkid` ASC";
		}
		return $this->query($query);
	}

	//Export to Excel Main Page query
	public function exportResinExcelMain(){
		$query = "SELECT * FROM tbl_Warehouse";
		return $this->query($query);
	}
	
	public function exportResinExcelIssuanceSummary($start_date,$end_date){
		$query = "SELECT * FROM `vw_resin_issuance_summary` WHERE `DateIssued` BETWEEN '$start_date' AND '$end_date';";
		return $this->query($query);
	}
	
	public function exportResinExcelReceivedSummary($start_date,$end_date){
		$query = "SELECT * FROM `vw_resin_received_summary` WHERE `DateReceived` BETWEEN '$start_date' AND '$end_date';";
		return $this->query($query);
	}
	
	//getMaxPkid of this partnumber
	public function getLastID($PartNumber){
		$query = "SELECT pkid FROM tbl_WarehouseTransaction WHERE fkPartNumber='$PartNumber' ORDER BY pkid DESC";
		return $this->query($query);
	}
	
	//Delete whole material record
	public function deleteAllProductInfo($PartNumber){
		$query = "DELETE FROM tbl_Warehouse WHERE PartNumber ='$PartNumber'";
		$this->query($query);
		$query = "DELETE FROM tbl_WarehouseTransaction WHERE fkPartNumber ='$PartNumber'";
		return $this->query($query);
	}
	
	public function ImportWarehouseData($ProductLine,$PartNumber,$MaterialType,
										$Supplier,$Model,$LastUpdate,
										$Username){
		$checkExist = $this->query("SELECT * FROM `tbl_Warehouse` WHERE `PartNumber` = '$PartNumber';");
		if($checkExist->num_rows == 0){
			$query = "INSERT INTO `tbl_Warehouse`(`ProductLine`, `PartNumber`, `MaterialType`, `Supplier`, `Model`, `LastUpdate`, `Username`) VALUES ('$ProductLine','".$PartNumber."','".$MaterialType."','$Supplier','$Model','$LastUpdate','$Username');";
			return $this->query($query);
		}else{
			if($PartNumber == ""){
				$query = "INSERT INTO `tbl_Warehouse`(`ProductLine`, `PartNumber`, `MaterialType`, `Supplier`, `Model`, `LastUpdate`, `Username`) VALUES ('$ProductLine','".$PartNumber."','".$MaterialType."','$Supplier','$Model','$LastUpdate','$Username');";
				return $this->query($query);
			}else{
				//return $checkExist;
			}
		}
	}
	
	public function get_product_line(){
		$query = "SELECT * FROM tbl_warehouse_product_line ORDER BY `id`";
		return $this->query($query);
	}
	
	public function get_classification(){
		$query = "SELECT * FROM tbl_Warehouse_Classification ORDER BY `id`";
		return $this->query($query);
	}

	// public function rl_get_data_for_update($pkid,$ControlNo){ 
	public function rl_get_data_for_update($pkid){ //01072024
		/* $query = "SELECT c.*, p1.*
				FROM tbl_Warehouse c
				LEFT JOIN tbl_WarehouseTransaction p1 ON (c.id = p1.pkid)
				LEFT OUTER JOIN tbl_WarehouseTransaction p2 ON (c.id = p2.fkid AND 
					(p1.pkid < p2.pkid))
				WHERE p2.pkid IS NULL";  */
		$query = "SELECT * FROM `tbl_WarehouseTransaction` WHERE `pkid`='$pkid';";
		return $this->query($query);
	}

	public function rl_update_data($pkid,$price,$invoice_no,$transfer_slip_no,$remarks,$inspection_cat,$lotno){
		$query = "UPDATE `tbl_WarehouseTransaction` SET Price='$price', InvoiceNo='$invoice_no', TransferSlipNo='$transfer_slip_no', Remarks='$remarks', Lot_number='$lotno', inspection_class='$inspection_cat' WHERE `pkid`='$pkid';";
		return $this->query($query);		
	}

	// 01042024
	public function get_qrcode_data($pkid){
		$query = "SELECT a.*, b.*
				FROM tbl_WarehouseTransaction a
				INNER JOIN tbl_Warehouse b on b.id = a.fkid
				WHERE a.pkid = '$pkid'; ";
		return $this->query($query);
	}

	public function get_qrcode_issuance_data($pkid){
		$query = "SELECT a.`pkid`, a.`control_number`, a.`fk_issuance`, a.`fk_itemlist`, a.`i_remarks`, b.`im_remarks`, b.`issuanceqtym`, c.`partcode`, c.`partname`
				FROM tbl_request a
				INNER JOIN tbl_issuance_breakdown b on b.fk_issuance = a.`fk_issuance`
				INNER JOIN tbl_itemList c on c.pkid_itemlist = a.`fk_itemlist`
				WHERE a.pkid = '$pkid'; ";
		return $this->query($query);
		// SELECT a.*, b.*, c.*
		// 		FROM tbl_issuance a
		// 		INNER JOIN tbl_issuance_breakdown b on b.fk_issuance = a.pkid_issuance
		// 		INNER JOIN tbl_itemList c on c.pkid_itemlist = b.fk_itemlist
		// 		WHERE a.pkid_issuance = '$pkid';
	}

	//04042024
	public function select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit){
		$query = "SELECT SQL_CALC_FOUND_ROWS " . implode(", ", $array_fields) . " FROM {$table} {$joins} {$sql_where} {$sql_order} {$sql_limit};";
		return $this->query($query);
	}
	
}
	
class YPICS{
	//MALACHIXD CONNECTION INFO
	private $serverM = "192.168.3.251\MSSQLSERVER";
	private $userM ="builder"; 
	private $passM = ""; //empty
	private $databaseM = "";
	private $db_handleM =""; //empty
	
	//FUNCTION TO CONNECT TO MALACHI SERVER	
    private function connect_DBM(){
        $this->db_handleM = mssql_connect($this->serverM,$this->userM,$this->passM);
            return mssql_select_db($this->databaseM,$this->db_handleM);
    }
   
    //FUNCTION TO CLOSE MALACHI SERVER
    private function close_DBM(){
        if(isset($this->db_handleM)){
            mssql_close($this->db_handleM);
        }
    }
	
	/////////////////////////////////PUBLIC FUNCTIONS///////////////////////////////////////////////	
	//FUNCTION TO LOAD PO NUM FROM MALACHI DATABASE
	public function selectPoNum(){ //sample
		$db_found = $this->connect_DBM();
		if($db_found){
			$SQL = "SELECT * FROM VwProdSchedMaster ORDER BY PONum";
			$result = mssql_query($SQL);
			return $result;
			mssql_free_result($result);
		}
		else{
			$errorMsg = "Database not found, Please contact your administrator";
		}
		$this->close_DBM();
	}
	
}
$tst = '

SELECT SQL_CALC_FOUND_ROWS PartNumber, MaterialType, Model, (SELECT Eoh FROM tbl_WarehouseTransaction AS B WHERE B.fkid=A.id AND `B`.`Date`<="2013-08-01" ORDER BY `B`.`Date` DESC,`B`.`pkid` DESC LIMIT 1 ) as Boh, (SELECT SUM(`In`) FROM tbl_WarehouseTransaction AS C WHERE C.fkid=A.id AND `C`.`Date` BETWEEN "2013-08-01" AND "2019-08-21" AND (`C`.`In` <> "0.00" OR `C`.`Out` <> "0.00")) as total_received, (SELECT SUM(`Out`) FROM tbl_WarehouseTransaction AS D WHERE D.fkid=A.id AND `D`.`Date` BETWEEN "2013-08-01" AND "2019-08-21" AND (`D`.`In` <> "0.00" OR `D`.`Out` <> "0.00")) as total_issued FROM tbl_Warehouse AS A WHERE Classification="1" ORDER BY id ASC ;
			SELECT * (
				SELECT * FROM (
					SELECT fkid d_fkid, Boh d_BOH FROM tbl_WarehouseTransaction ORDER BY Date ASC, pkid ASC
				) AA
				GROUP BY d_fkid
			) D
			ON A.id = D.d_fkid






			SELECT A.*, B.Classification Classification2, (d_BOH+c_EOH) EOH FROM
			tbl_Warehouse A
			LEFT JOIN tbl_Warehouse_Classification B
			ON A.Classification=B.id
			LEFT JOIN
			( SELECT fkid c_fkid, (SUM(`In`) - SUM(`Out`)) c_EOH FROM tbl_WarehouseTransaction GROUP BY fkid ORDER BY Date DESC, pkid DESC ) C
			ON A.id = C.c_fkid

			LEFT JOIN 
			(
				SELECT * FROM (
					SELECT fkid d_fkid, Boh d_BOH FROM tbl_WarehouseTransaction ORDER BY Date ASC, pkid ASC
				) AA
				GROUP BY d_fkid
			) D
			ON A.id = D.d_fkid

			WHERE c_EOH IS NOT NULL
			ORDER BY A.Classification ASC, A.PartNumber





	SELECT SQL_CALC_FOUND_ROWS PartNumber, MaterialType, Model, 
	(
		SELECT d_BOH FROM (
			SELECT fkid d_fkid, Boh d_BOH FROM tbl_WarehouseTransaction ORDER BY Date ASC, pkid ASC
		) AA
		WHERE A.id = AA.d_fkid LIMIT 0,1
	) as Boh_start, 
	( SELECT (SUM(`In`) - SUM(`Out`)) c_EOH FROM tbl_WarehouseTransaction B WHERE B.fkid=A.id AND `B`.`Date`<="2013-08-01" GROUP BY fkid ORDER BY Date DESC, pkid DESC ) Eoh_prev,

	(SELECT SUM(`In`) FROM tbl_WarehouseTransaction AS C WHERE C.fkid=A.id AND `C`.`Date` BETWEEN "2013-08-01" AND "2019-08-21" AND (`C`.`In` <> "0.00" OR `C`.`Out` <> "0.00")) as total_received, 
	(SELECT SUM(`Out`) FROM tbl_WarehouseTransaction AS D WHERE D.fkid=A.id AND `D`.`Date` BETWEEN "2013-08-01" AND "2019-08-21" AND (`D`.`In` <> "0.00" OR `D`.`Out` <> "0.00")) as total_issued 
	FROM tbl_Warehouse AS A WHERE Classification="1" ORDER BY id ASC ;






';


?>
