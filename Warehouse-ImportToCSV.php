<?php
	require_once('ppsp-oop.php');
	session_start();
	$username = $_SESSION['username'];
	if(isset($_FILES['file']['name'])) {
		$file = $_FILES['file']['name'];
		$fileCheck = explode(".",$file);
		if($fileCheck[1] != 'csv') {
			echo '<center><br><br>ERROR: PLEASE MAKE SURE YOU CONVERTED THE EXCEL FILE TO CSV FORMAT.<br><br>
				How to convert excel file to csv:<br>
				1. Open File <br>
				2. Save File as csv (example: itemlist.csv) then press save.
				<br><br><img src="/images/csv.png" style="width:700px"><br><br>';
			echo '<a href="dieset.php">BACK</a></center><br>';
		} else {
			if ($_FILES["file"]["error"] > 0) {
				$msg = "Error: " . $_FILES["file"]["error"] . "<br>";
			}
			else
			{
				echo "Upload: " . $_FILES["file"]["name"] . "<br>";
				echo "Type: " . $_FILES["file"]["type"] . "<br>";
				echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br>";
				   
				$a=$_FILES["file"]["tmp_name"]; //FILENAME
								 
				$csv_file = $a; //PASS THE FILE NAME 
				 
				if (($getfile = fopen($csv_file, "r")) !== FALSE) { //OPEN FILE 
					$data = fgetcsv($getfile, 1000, ",");
					while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
						$result = $data;
						$str = implode(",", $data);	
						$slice = explode(",", $str);
						//SET VALUES OF EACH ITEM IN A ROW
						$date = $slice[0];
						$status = $slice[1];
						$YECControlNum = $slice[2];
						$R3Code = $slice[3];
						$EXPCode = $slice[4];
						$DeviceName = $slice[5];
						//$DrawingName = $slice[6];
						$DrawingNum = $slice[6];
						$Rev = $slice[7];
						$MoldClass = $slice[8];
						$DieNo = $slice[9];
						$NumOfCavity = $slice[10];
						$CavNum = $slice[11];
						$ChangingPart = $slice[12];
						$DiesetSize = $slice[13];
						$DiesetWeight = $slice[14];
						$UnitWeight = $slice[15];
						$SprueWeight = $slice[16];
						$ShotWeight = $slice[17];
						$CycleTimePerShot = $slice[18];
						$FixedAssetNum = $slice[19];
						$DateManufactured = $slice[20];
						$MoldMake = $slice[21];
						$ToolLife = $slice[22];
						$ToolShot = $slice[23];
						$ToolLifePMI = $slice[24];
						$MaintenanceCycle = $slice[25];
						$InjectionMachine = $slice[26];
						$Material = $slice[27];
						$ToneColor = $slice[28];
						$Remarks = $slice[29];
						$Username = $username;
						$date = date("Y-m-d", strtotime($date));
						if($DateManufactured != "") {
							$DateManufactured = date("Y-m-d", strtotime($DateManufactured));
						} else {
							$DateManufactured = "";
						}						
						
						echo "insert: ".$date."/".$status."/".$YECControlNum."/".$R3Code."/".$EXPCode."/".$DeviceName."/".$DrawingNum."/".$Rev."/".$MoldClass."/".$DieNo."/".$NumOfCavity."/".$CavNum."/".$ChangingPart."/".$DiesetSize."/".$DiesetWeight."/".$UnitWeight."/".$SprueWeight."/".$ShotWeight."/".$CycleTimePerShot."/".$FixedAssetNum."/".$DateManufactured."/".$MoldMake."/".$ToolLife."/". $ToolShot."/".$ToolLifePMI."/".$MaintenanceCycle."/".$InjectionMachine."/".$Material."/".$ToneColor."/".$Remarks."/".$Username."<br>";
						if($status == "New") {
							$status = "1";
						} else if($status == "Return") {
							$status = "2";
						}
						$msg = PPSP::getInstance()->addDieset($date,$status,$YECControlNum,$R3Code,$EXPCode,$DeviceName,$DrawingNum,$Rev,$MoldClass,$DieNo,$NumOfCavity,$CavNum,$ChangingPart,$DiesetSize,$DiesetWeight,$UnitWeight,$SprueWeight,$ShotWeight,$CycleTimePerShot,$FixedAssetNum,$DateManufactured,$MoldMake,$ToolLife, $ToolShot,$ToolLifePMI,$MaintenanceCycle,$InjectionMachine,$Material,$ToneColor,$Remarks,$Username);
						echo $msg;
					}
				}
			}
			echo "<script>alert('".$msg."'); window.location.href='./dieset.php'; </script>";
		}
	}
?>