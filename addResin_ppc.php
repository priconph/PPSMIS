<?php
require_once('./includes.php');
require_once('./ppswarehouse-oop.php');
$dateToday = date("Y-m-d");
$lastUpdate = date("Y-m-d H-i-s");
$dateReset = '2000-01-01';
session_start();
$username = $_SESSION["username"];
echo '<input type="date" id="txt_dateReset" style="display:none;" value="'.$dateReset.'">';
//REMINDER: MAG ADD KA NG DELETE NA AJAX SA MISMONG REMOVE
?>
<html>
<title>PPS Database System</title>
<head>
<link href="./css/ppsStyleSheet.css" rel="stylesheet">
<link href="./css/pps_warehouse.css" rel="stylesheet">
<style>
#btn_ModalAddNewData{
	float:right;
	margin-right:5;
}

.hidden_div{
	display:none;
}
</style>
<script>
//nasa ilalim yung ibang script kase may niloload sya na base sa database tapos mag cocompute ng data basta yun na yun
function CheckNumeric()
{
  // Get ASCII value of key that user pressed
   var key = window.event.keyCode;
  // Was key that was pressed a numeric character (0-9)?
   if ( key > 45 && key < 58 )
	 return; // if so, do nothing
   else
	 window.event.returnValue = null; // otherwise, 
								   // discard character
}
</script>
</head>
<body>
	<div class="panel panel-primary" style="width:100%;margin:auto;">
	  <div class="panel-heading">Add Resin</div>
	  <div class="panel-body">
		<div class="panel panel-primary">
			<div class="panel-body">
				
				<div class="row">
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-4">
								<label class="fa fa-lg label-pad">Product Line:</label>
							</div>
							<div class="col-sm-6">
								<select id="txt_ProductLine" class="form-control condensed">
								<?php
									$result = PPSW::getInstance()->get_product_line();
									while($row = mysqli_fetch_array($result)){
										echo '<option>'.$row['ProductLine'].'</option>';
										
									}
								?>	
								</select>
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-4">
								<label class="fa fa-lg label-pad">Part Number:</label>
							</div>
							<div class="col-sm-6">
								<input type="text" id="txt_PartNumber" class="form-control condensed">
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-4">
								<label class="fa fa-lg label-pad">Material Type:</label>
							</div>
							<div class="col-sm-6">
								<input type="text" id="txt_MaterialType" class="form-control condensed">
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-4">
								<label class="fa fa-lg label-pad">Model / Location:</label>
							</div>
							<div class="col-sm-6">
								<input type="text" id="txt_Model" class="form-control condensed">
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-4">
								<label class="fa fa-lg label-pad">Supplier:</label>
							</div>
							<div class="col-sm-6">
								<select id="txt_Supplier" class="form-control condensed">
									<option>INABATA</option>
									<option>NAGASE</option>
									<option>MORIMURA</option>
									<option>YEC</option>
									<option>DOHO</option>
								</select>
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-4">
								<label class="fa fa-lg label-pad">Balance as of <label id="lbl_RecentDate"></label></label>
							</div>
							<div class="col-sm-6">
								<input type="text" id="txt_EOH" class="form-control condensed" value="<?php echo $Balance; ?>" readonly>
							</div>
						</div>
					</div>
				</div>
				<br />
				<!-- <button class='btn btn-primary' id="btn_hide_unhide_container_add_record" style="float:right;">Add New Transaction</button> -->
				<button class='btn btn-primary pull-right' id="btn_open_modal_transaction">Add New Transaction</button>
			</div>
		  </div>
		
		<div class="panel panel-primary">
			<div class="panel-body">
				<div class="btn-group">
					<button id="btn_ImportCSV" type="button" class="btn btn-default">IMPORT CSV FILE</button>
				</div><br><br>
				<table id="tbl_Process" border="" class= "table"width="100%">
					<thead>
						<tr>
							<th>Date</th>
							<th>Invoice No.</th>
							<th>Transfer Slip No.</th>
							<th>BOH</th>
							<th>IN</th>
							<th>OUT</th>
							<th>EOH</th>
							<th>Recorded By</th>
							<th>Remarks</th>
							<th>(X)</th>
						</tr>
					</thead>
					<tbody>
						<?php
							appendData();
						?>
					</tbody>
				</table>	
			</div>
		</div>
		
		<div class="panel panel-primary">
			<div class="panel-body" align="right">
				<button id="btn_SaveTransaction" class="btn btn-success" style="width:10%;">Save</button>
				<a href="./Resin_ppc.php"><button class="btn btn-danger" id="btn_Cancel" style="width:10%;">Cancel</button></a>
			</div>
		</div>
	  </div>
	</div>
	
<!--Modal-->
<div id="modal_DeleteData" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ModalDeleteLabel" aria-hidden="true" style="overflow:inherit">
	 <div class="modal-dialog">
		<div class="modal-content" style="width:400px">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				  <h4 class="modal-title" id="ModalDeleteLabel">DELETE</h4>
			</div>
			<div class="modal-body">
				<p>WARNING! DATA WILL BE PERMANENTLY DELETED!</p>
				<p>PROCEED?</p>
			</div>
			<div class="modal-footer">
			   <a href="#" id="btn_Delete_Yes" class="btn btn-success">YES</a>
			   <a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-danger">NO</a>
			</div>
		</div>
	</div>
</div>
<!--Modal-->
<div id="modal_GenerateReport" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ModalDeleteLabel" aria-hidden="true" style="overflow:inherit">
	 <div class="modal-dialog">
		<div class="modal-content" style="width:400px">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				  <h4 class="modal-title" id="ModalDeleteLabel">GENERATE REPORT</h4>
			</div>
			<div class="modal-body">
				<?php
					echo '<label style="margin-right:5%;width:20%;">Start Date:</label></label><input type="date" id="txt_DateDailyStart" style="width:50%;" value="'.$dateToday.'"><br>';
					echo '<label style="margin-right:5%;width:20%;">End Date:</label></label><input type="date" id="txt_DateDailyEnd" style="width:50%;" value="'.$dateToday.'">';
				?>
			</div>
			<div class="modal-footer">
			   <a href="#" id="btn_GenereteReportExcel" class="btn btn-success">GENERATE REPORT</a>
			   <a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-danger">CLOSE</a>
			</div>
		</div>
	</div>
</div>
<?php
function appendData(){
	if(isset($_FILES['file']['name'])) {
	$file = $_FILES['file']['name'];
	$fileCheck = explode(".",$file);
	if($fileCheck[1] != 'csv') {
		echo '<center><br><br>ERROR: PLEASE MAKE SURE YOU CONVERTED THE EXCEL FILE TO CSV FORMAT.<br><br>
			How to convert excel file to csv:<br>
			1. Open File <br>
			2. Save File as csv (example: itemlist.csv) then press save.
			<br><br><img src="/images/csv.png" style="width:700px"><br><br>';
		echo '<a href="poReceived.php">BACK</a></center><br>';
	} else {
		if ($_FILES["file"]["error"] > 0) {
			$msg = "Error: " . $_FILES["file"]["error"] . "<br>";
		}
		else
		{
			   
			$a=$_FILES["file"]["tmp_name"]; //FILENAME
							 
			$csv_file = $a; //PASS THE FILE NAME 
			 
			if (($getfile = fopen($csv_file, "r")) !== FALSE) { //OPEN FILE 
				$data = fgetcsv($getfile, 1000, ",");
				while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
					$result = $data;
					$str = implode(",", $data);	
					$slice = explode(",", $str);
					if(empty($slice[0]) && empty($slice[1]) && empty($slice[2])){
					
					}else{
						//SET VALUES OF EACH ITEM IN A ROW
						echo "<tr>";
						$dateAppend = strtotime($slice[0]);
						$dateAppend = date("Y-m-d",$dateAppend);
						echo "<td>".$dateAppend."</td>";
						echo "<td>".$slice[1]."</td>";
						echo "<td>".$slice[2]."</td>";
						echo "<td>".$slice[3]."</td>";
						echo "<td>".$slice[4]."</td>";
						echo "<td>".$slice[5]."</td>";
						echo "<td>".$slice[6]."</td>";
						echo "<td>".$slice[7]."</td>";
						echo "<td>".$slice[8]."</td>";
						echo "<td><a href='javascript:void(0);' class='remCF'>Remove</a></td>";
						echo "</tr>";
					}
				}
			}
		}
	}	
	}
	
}
?>

<!--MODALS-->
<div class="modal fade" tabindex="-1" role="dialog" id="modal_add_transaction">
  <div class="modal-dialog" role="document" style="width:800px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><span class="fa fa-plus"></span> Add Transaction</h4>
      </div>
      <div class="modal-body">
        <div class="row">
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-4">
						<label class="fa fa-lg label-pad">Date:</label>
					</div>
					<div class="col-sm-8">
						<input type="date" id="txt_DateDaily" class="form-control condensed" value="<?php echo $dateToday; ?>">
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-4">
						<label class="fa fa-lg label-pad">Invoice No:</label>
					</div>
					<div class="col-sm-8">
						<input type="" id="txt_InvoiceNoDaily" class="form-control condensed">
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-4">
						<label class="fa fa-lg label-pad">Transfer Slip No:</label>
					</div>
					<div class="col-sm-8">
						<input type="" id="txt_TransferSlipNoDaily" class="form-control condensed">
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-4">
						<label class="fa fa-lg label-pad">BOH:</label>
					</div>
					<div class="col-sm-8">
						<input onkeypress="CheckNumeric()" type="" id="txt_BOHDaily" class="form-control" value="0" readonly>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-4">
						<label class="fa fa-lg label-pad">IN:</label>
					</div>
					<div class="col-sm-8">
						<input onkeypress="CheckNumeric()" type="" id="txt_INDaily" class="form-control condensed" value="0">
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-4">
						<label class="fa fa-lg label-pad">OUT:</label>
					</div>
					<div class="col-sm-8">
						<input onkeypress="CheckNumeric()" type="" id="txt_OUTDaily" class="form-control condensed" value="0">
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-4">
						<label class="fa fa-lg label-pad">EOH:</label>
					</div>
					<div class="col-sm-8">
						<input onkeypress="CheckNumeric()" type="" id="txt_EOHDaily" class="form-control condensed" value="0" readonly>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-4">
						<label class="fa fa-lg label-pad">Recorded By:</label>
					</div>
					<div class="col-sm-8">
						<input type="" value= "<?php echo $username; ?>" id="txt_RecordedByDaily" class="form-control condensed" readonly>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-2">
				<label class="fa fa-lg label-pad">Remarks:</label>
			</div>
			<div class="col-sm-10">
				<input type="" id="txt_RemarksDaily" class="form-control condensed">
			</div>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button id="btn_ModalAddNewData" class="btn btn-success">Add Transaction</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div id="ModalImportResinCSV" class="modal fade" tabindex="-1" style="overflow:scroll;" role="dialog" aria-labelledby="ModalImportResinCSV" aria-hidden="true" style="overflow:inherit">
	 <div class="modal-dialog">
		<div class="modal-content" style="width:400px">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				  <h4 class="modal-title">Upload CSV File</h4>
			</div>
			<form action="addResin_ppc.php" method="POST" enctype="multipart/form-data">
				<div class="modal-body">
						<p><input type="file" name="file" id="file"></p>
				</div>
				<div class="modal-footer">
				   <input type="submit" class="btn btn-success" value="SUBMIT">
				   <a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-danger">CLOSE</a>
				</div>
			</form>
		</div>
	</div>
</div>

</body>
<script>

$('document').ready(function(){
	$('#container_add_record').attr('class','hidden_div');
	$('#btn_hide_unhide_container_add_record').click(function(){
		if($('#container_add_record').is(":visible") == false ){
			$('#btn_hide_unhide_container_add_record').text("CLOSE");
			$('#container_add_record').removeAttr('class');
		}else{
			$('#container_add_record').removeAttr();
			$('#btn_hide_unhide_container_add_record').text("Add New Transaction");
			$('#container_add_record').attr('class','hidden_div');
		}
		//alert($('#container_add_record').attr());
	});
	
	computetableData();
			
	//set values after adding a product out data
	$("#btn_AddNewData").click(function(){
		$("#modal_AddNewData").modal("show");
		$("#txt_BOHDaily").val(total_EOH);
	});
		
	$('#btn_ModalAddNewData').click(function(){
		if($("#txt_EOHDaily").val() < 0){
			alert("Item withrawn is greater than stock!");
			return false;
		}
		if($("#txt_DateDaily").val() == ""){
			alert("Please add a Date!");
		} else{
			$('#tbl_Process tbody').append('<tr class=""><td>'+$("#txt_DateDaily").val()+'</td><td>'+$("#txt_InvoiceNoDaily").val()+'</td><td>'+$("#txt_TransferSlipNoDaily").val()+'</td><td>'+$("#txt_BOHDaily").val()+'</td><td>'+$("#txt_INDaily").val()+'</td><td>'+$("#txt_OUTDaily").val()+'</td><td>'+$("#txt_EOHDaily").val()+'</td><td>'+$("#txt_RecordedByDaily").val()+'</td><td>'+$("#txt_RemarksDaily").val()+'</td><td><a href="javascript:void(0);" class="remCF">Remove</a></td></tr>'); //APPEND NEW DATA INTO THE TABLE
			computetableData();
		}
	});
	//On change functions, recompute data on user input
	$("#txt_INDaily").change(function(){
		$("#txt_EOHDaily").val(parseFloat($('#txt_INDaily').val())+parseFloat($('#txt_BOHDaily').val()));
		//$("#txt_BOHDaily").val(parseFloat($('#txt_INDaily').val())-parseFloat($('#txt_OUTDaily').val()));
	});
	$("#txt_OUTDaily").change(function(){
		$("#txt_EOHDaily").val((parseFloat($('#txt_BOHDaily').val()) + parseFloat($('#txt_INDaily').val())) - parseFloat($('#txt_OUTDaily').val()));
		//$("#txt_BOHDaily").val(parseFloat($('#txt_INDaily').val())-parseFloat($('#txt_OUTDaily').val()));
	});
	
	//FUNCTION FOR REMOVING A ROW INTO THE TABLE 
	$('#btn_Delete_Yes').click(function(){
		$(this).parent().parent().remove(); //REMOVES THE ROW
		window.location.href="./addResin";
	});
	
	//click functions
	$('#btn_GenerateReport').click(function(){
		$('#modal_GenerateReport').modal('show');
	});
	
	$("#btn_SaveTransaction").click(function(){
		check_session_status();
	});
	
	$("#tbl_Process").on('click','.remCF',function(){ //TABLE ID = tblMain AND TABLE CLASS = remCF
		$(this).parent().parent().remove(); //REMOVES THE ROW
		computetableData();
	});

	$('#btn_open_modal_transaction').click(function(){
		$('#modal_add_transaction').modal('show');
	});
	
});

function SaveRecord(){
		var data = {
			"action" 		: "addNewProduct",
			"ProductLine" 	: $("#txt_ProductLine").val(),
			"PartNumber" 	: $("#txt_PartNumber").val(),
			"MaterialType" 	: $("#txt_MaterialType").val(),
			"Model" 		: $("#txt_Model").val(),
			"Balance" 		: $("#txt_EOH").val(),
			"Cost" 			: $("#txt_Cost").val(),
			"LastUpdate" 	: "<?php echo $lastUpdate;?>",
			"Username" 		: "<?php echo $username;?>",
			"Supplier" 		: $('#txt_Supplier').val()
		}
		
		data = $.param(data);
		$.ajax({
			type 	 : "post",
			dataType : "json",
			data 	 : data,
			url	 	 : "./ppswarehouseHandler.php",
			success  : function(data){
				
				SaveTransaction();
			}
		});
		alert("New Material has been added!");
		window.location.href = "./Resin_ppc.php";
}

function SaveTransaction(){
	var row=0;
	$("#tbl_Process tr").each(function(row,tr){
		if(row>0){ //this first row is the label kaya di sya isasama
			var data = {
				"action" 		: "addNewProductWithrawal2",
				//"fkPartNumber" 	: $("#txt_PartNumber").val(),
				"Date"			: $(tr).find('td:eq(0)').text(),
				"InvoiceNo" 	: $(tr).find('td:eq(1)').text(),
				"TransferSlipNo": $(tr).find('td:eq(2)').text(),
				"BOH" 			: $(tr).find('td:eq(3)').text(),
				"IN" 			: $(tr).find('td:eq(4)').text(),
				"OUT" 			: $(tr).find('td:eq(5)').text(),
				"EOH" 			: $(tr).find('td:eq(6)').text(),
				"RecordedBy" 	: $(tr).find('td:eq(7)').text(),
				"Remarks" 		: $(tr).find('td:eq(8)').text(),
				"LastUpdate" 	: "<?php echo $lastUpdate;?>",
				"Username" 		: "<?php echo $username;?>"
			}				
			
			data = $.param(data);
			$.ajax({
				type 	: "post",
				dataType: "json",
				data 	: data,
				url 	: "./ppswarehouseHandler",
				success : function(data){
					
				}
			});
		}
		row++;
	});
}
function computetableData(){
	var total_BOH=0,total_EOH=0,total_IN=0;total_OUT=0;
	row = 0;
	var thisDate; 
	$('#tbl_Process tr').each(function(row, tr){	//loop to each tr
		if(row>0){ 	//REMOVE ROW 0 WHICH IS A LABEL
			//GET VALUES HERE
			//total_BOH = parseFloat(total_BOH) + parseFloat($(tr).find('td:eq(3)').text());
			total_IN = (parseFloat(total_IN) + parseFloat($(tr).find('td:eq(4)').text())).toFixed(2);
			total_OUT = (parseFloat(total_OUT) + parseFloat($(tr).find('td:eq(5)').text())).toFixed(2);
		}
		row++;
	});
	total_EOH = (parseFloat(total_IN) - parseFloat(total_OUT)).toFixed(2);
	$("#txt_EOH").val(total_EOH);
	//Reset Values of all Modal
	$("#txt_DateDaily").val("<?php echo $dateToday;?>");
	$("#txt_InvoiceNoDaily").val("");
	$("#txt_TransferSlipNoDaily").val("");
	$("#txt_BOHDaily").val($("#txt_EOH").val());
	$("#txt_INDaily").val("0");
	$("#txt_OUTDaily").val("0");
	$("#txt_EOHDaily").val("0");
	//$("#txt_RecordedByDaily").val("");
	$("#txt_RemarksDaily").val("");
}

function check_session_status(){
	var data = {
		"action"	: "session_check"
	}
	data = $.param(data);
	$.ajax({
		type	: "post",
		dataType: "json",
		data	: data,
		url		: "./session_check.php",
		success	: function(data){
			if(data['username'] == null){
				alert('Session expired, Please relogin.');
				parent.parent.location.reload();
			}else{
				SaveRecord();
			}
		} 
	});
}

$('#btn_ImportCSV').click(function(){ $("#ModalImportResinCSV").modal("show"); });
</script>
</html>