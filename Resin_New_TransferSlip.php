<?php
require_once('./includes.php');
require_once('./ppswarehouse-oop.php');
session_start();
$dateToday = date("Y-m-d");
$dateReset = '2000-01-01';
$lastUpdate = date("Y-m-d H-i-s");
$username = $_SESSION["username"];
echo '<input type="date" id="txt_dateReset" style="display:none;" value="'.$dateReset.'">';
//REMINDER: MAG ADD KA NG DELETE NA AJAX SA MISMONG REMOVE
?>
<html>
<title>PPS Warehouse System</title>
<head>
<style>
table#tbl_LotList thead th:nth-child(1){
	width:2%;
}
</style>
<link href="./css/pps_warehouse.css" rel="stylesheet">
<script>
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
	<?php
	/* fetch last control number */
	$last_ctrl = "";
	$result = PPSW::getInstance()->getResin_TransferSlip_lst_ctrl(); 
	while($row=mysqli_fetch_array($result)){
		$last_ctrl = $row['ControlNo'];
	}
	/* Used for validation */
	//$last_ctrl = 'PPS-1503-521';
	
	if($last_ctrl == ""){
		$last_ctrl = "PPS-".date('ym').'-001';
	}else{
		if($last_ctrl == "PPS-Resin-1711-042"){
			$last_ctrl_array = explode("-", $last_ctrl);
			if($last_ctrl_array[2] != date('ym')){
				$last_ctrl = "PPS-".date('ym').'-'.'001';
			}else{
				$new_ct = $last_ctrl_array[3] + 1;
				if(strlen($new_ct) == 1){
					$new_ct = '00'.$new_ct;
				}else if(strlen($new_ct) == 2){
					$new_ct = '0'.$new_ct;
				}
				$last_ctrl = "PPS-".date('ym').'-'.$new_ct;
				// $last_ctrl = "PPS-".date('ym', strtotime("-1 month")).'-'.$new_ct;
			}
		}else{
			$last_ctrl_array = explode("-", $last_ctrl);
			// echo $last_ctrl;
			if($last_ctrl_array[1] != date('ym')){
			// if($last_ctrl_array[1] != date('ym', strtotime("-1 month"))){
				$last_ctrl = "PPS-".date('ym').'-'.'001';
			}else{
				$new_ct = $last_ctrl_array[2] + 1;
				if(strlen($new_ct) == 1){
					$new_ct = '00'.$new_ct;
				}else if(strlen($new_ct) == 2){
					$new_ct = '0'.$new_ct;
				}
				$last_ctrl = "PPS-".date('ym').'-'.$new_ct;
				// $last_ctrl = "PPS-".date('ym', strtotime("-1 months")).'-'.$new_ct;
			}
		}		
	}

	?>
	<div class="panel panel-primary" style="width:100%;margin:auto;">
	  <div class="panel-heading">New Transfer Slip</div>
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
								<input value="PPS Resin" type="text" id="txt_ProductLine" class="form-control condensed">
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-4">
								<label class="fa fa-lg label-pad">Control No:</label>
							</div>
							<div class="col-sm-6">
								<input value="<?php echo $last_ctrl;?>" type="text" id="txt_ControlNo" class="form-control condensed" readonly>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-4">
								<label class="fa fa-lg label-pad">Date:</label>
							</div>
							<div class="col-sm-6">
								<input value="<?php echo date('Y-m-d'); ?>" type="date" id="txt_Date" readonly class="form-control condensed" class="form-control condensed">
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-4">
								<label class="fa fa-lg label-pad">Issued By:</label>
							</div>
							<div class="col-sm-6">
								<input type="text" id="txt_IssuedBy" class="form-control condensed" value="<?php echo $username; ?>">
							</div>
						</div>
					</div>
				</div>
				<br><button id="btn_Show_Lot_Select" class='btn btn-primary' style="float:right;margin-right:2%;">SELECT TRANSACTION</button>
			</div>
		  </div>
					
		<div class="panel panel-primary">
			<div class="panel-body">
				
				<br><br>
				<button class='btn btn-info pull-right' id="btn_remove_appended">Remove selected row</button>
				<table id="tbl_Process" border="" class= "table" width="100%">
					<thead>
						<tr>
							<th>pkid</th>
							<th>Part Number</th>
							<th>Part Name</th>
							<th>Quantity</th>
							<th>Remarks</th>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
				</table>
				<!--<div style="float:right;"><b>Total Qty: </b><input type="text" id="txt_TotalQty" style=""></div>-->
			</div>
		</div>
		
		<div class="panel panel-primary">
			<div class="panel-body" align="right">
				<button id="btn_SaveRecord" class="btn btn-success" style="width:10%;">Save</button>
				<a href="./Resin_Transfer_Slip.php"><button class="btn btn-danger" id="btn_Cancel" style="width:10%;">Cancel</button></a>
			</div>
		</div>
	  </div>
	</div>
	
<!--Modal-->
<div id="modal_lot_list" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ModalDeleteLabel" aria-hidden="true" style="overflow:inherit">
	 <div class="modal-dialog">
		<div class="modal-content" style="width:650px">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				  <h4 class="modal-title" id="ModalDeleteLabel">Transaction List</h4>
			</div>
			<div class="modal-body">
				<div class="panel-body" id="div_Lot_List" class='hidden_div'>
				<div style="height:50%;overflow-x:hidden;overflow-y:scroll;">
					<table id="tbl_LotList" style="font-size:12px;" border='1' class="" width="100%">
						<thead>
							<tr>
								<th></th>
								<th>pkid</th>
								<th>Part Number</th>
								<th>Part Name</th>
								<th>Quantity</th>
								<th>Remarks</th>
							</tr>
						</thead>
						<tbody>
							
						</tbody>
					</table>
					<!--<button id="btn_AddDataToTable" type="button" class="btn btn-primary" style="float:right;">ADD TO LIST</button><br>-->
				</div>
			</div>
			<div class="modal-footer">
			   <a href="#" id="btn_AddDataToTable" class="btn btn-success">ADD TO LIST</a>
			   <a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-danger">CLOSE</a>
			</div>
		</div>
	</div>
</div>

</body>
<script>
var row;
var Handler = "./ppswarehouseHandler.php";
$('document').ready(function(){
	//var oTable = $('#tbl_LotList').DataTable();
	load_transactions();
	$('#btn_Show_Lot_Select').click(function(){ modal_lot_list(); });
	$('#btn_SaveRecord').click(function(){ check_session_status(); });
	$('#btn_AddDataToTable').click(function(){ appendList(); });
	
	$('#tbl_LotList tbody').on('click','tr',function(){
		 var t = oTable.row( this ).data();
		 //alert(t[1]);
	});
	
});

function modal_lot_list(){
	$('#modal_lot_list').modal('show');
}

function load_transactions(){
	var data = {
		"action" : "load_transfer_slip_transactions"
	}
	data = $.param(data);
	$.ajax({
		type	: "post",
		dataType: "json",
		data	: data,
		url		: Handler,
		success : function(data){
			//Append data to tbl lot list
			var oTable = $('#tbl_LotList').DataTable();
			var ctr = 0;
			for(ctr=0;ctr<data['ctr'];ctr++){
				var pkid 		= data['pkid'][ctr];
				var PartNumber 	= data['PartNumber'][ctr];
				var MaterialType= data['MaterialType'][ctr];
				var Qty 		= data['Qty'][ctr];
				var Remarks 	= data['Remarks'][ctr];
				var checkbox = document.createElement('input');
					checkbox.type = "checkbox";
					checkbox.name = "name";
					checkbox.value = "value";
					checkbox.id = "id";
					
				oTable.row.add( [
					"<input type='checkbox' id='chk_"+pkid+"'>",
					//checkbox,
					pkid,
					PartNumber,
					MaterialType,
					Qty,
					Remarks
				] ).draw();
			}
		}
	});
}

function computeTotalQty(){ //Computation base on table
	//load initial value here
	row = 0;
	var TotalQty = 0;
	$('#tbl_Process tr').each(function(row, tr){	//loop to each tr 
		if(row>0){ 	//REMOVE ROW 0 WHICH IS A LABEL
			//GET VALUES HERE
			TotalQty = parseFloat(TotalQty) + parseFloat($(tr).find('td:eq(6)').text());
		}
		row++;
	});
	$('#txt_TotalQty').val(TotalQty);
}

function appendList(){
	//Lot List
	var oTable = $('#tbl_LotList').DataTable();
	var data = oTable
		.rows()
		.data();
	var row_length = data.length;
	
	var ctr  = 0;
	
	var oTable2 = $('#tbl_Process').DataTable();
		//oTable2.clear().draw();
	
	var data2 = oTable2
		.rows()
		.data();
	var row_length2 = data2.length;
		
	for(ctr = 0;ctr<row_length;ctr++){
		var checker = $('#chk_'+data[ctr][1]).prop('checked');
		if(checker == true){
			//Appended Data
			var  ctr2 = 0;
			var checker2 = 0;
			for(ctr2=0;ctr<row_length2;ctr++){
				if(data[ctr][1] == data2[ctr2][0]){
					checker2 = 1;
				}
			}
			if(checker2 == 0){
				oTable2.row.add( [
					data[ctr][1],
					data[ctr][2],
					data[ctr][3],
					data[ctr][4],
					data[ctr][5]
				] ).draw();
			}
		}
	}
	
	selected_row();
	//computeTotalQty();
}

function selected_row(){
	var oTable2 = $('#tbl_Process').DataTable();
	$('#tbl_Process tbody').on( 'click', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
            oTable2.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
    } );
	
	$('#btn_remove_appended').click( function () {
        oTable2.row('.selected').remove().draw(false);
    } );
}

function validateSaveRecord(){
	var ProductLine 	= $('#txt_ProductLine').val();
	var ControlNo 		= $('#txt_ControlNo').val();
	var Date 			= $('#txt_Date').val();
	var IssuedBy 		= $('#txt_IssuedBy').val();
	
	if(ProductLine == ""){
		alert("Product line required!");
		$('#txt_ProductLine').focus();
		return false;
	}
	if(ProductLine == ""){
		alert("Product line required!");
		$('#txt_ProductLine').focus();
		return false;
	}
	if(ProductLine == ""){
		alert("Product line required!");
		$('#txt_ProductLine').focus();
		return false;
	}
	SaveRecord();
}

function SaveRecord(){
	var data = {
		"action"		: "save_new_transfer_slip",
		"ProductLine"	: $('#txt_ProductLine').val(),
		"ControlNo"		: $('#txt_ControlNo').val(),
		"Date"			: $('#txt_Date').val(),
		"IssuedBy"		: $('#txt_IssuedBy').val(),
		"LastUpdate"	: "<?php echo date('Y-m-d H-i-s'); ?>",
		"Username"		: "<?php echo $username; ?>"
	}
	/* for validation */
	//alert(data['ProductLine']+"/"+data["ControlNo"]+"/"+data['Date']+"/"+data["IssuedBy"]+"/"+data['LastUpdate']+"/"+data["Username"]);	
	data = $.param(data);
	$.ajax({
		type	: "post",
		dataType: "json",
		data	: data,
		url		: Handler,
		success : function(data){
			update_transactions();
		}
	});
}

function update_transactions(){
	var oTable = $('#tbl_Process').DataTable();
	var data = oTable
		.rows()
		.data();
	var row_length = data.length;
	var ctr = 0;
	
	for(ctr=0;ctr<row_length;ctr++){
		var data_table = {
			"action"	: "update_transfer_slip",
			"pkid"		: data[ctr][0],
			"Trans_Slip": $('#txt_ControlNo').val()
		}
		data_table = $.param(data_table);
		$.ajax({
			type 	: "post",
			dataType: "json",
			data	: data_table,
			url		: Handler,
			success	: function(data){
				
			}
		});
	}
	alert("Record has been saved!");
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
				validateSaveRecord();
			}
		}
	});
}


function load_last_ctrl_no(){
	var data = {
		"action" : "get_last_ctrl_pre_shipment"
	}
	data = $.param(data);
	$.ajax({
		type 	: "post",
		dataType: "json",
		data	: data,
		url		: "./ppsproductionHandler.php",
		success	: function(data){
			if(data['ControlNo'] == null){
				data['ControlNo'] = "<?php echo date('ym')."-00"; ?>";
			}
			//generate control
			var lastCtrl = data['ControlNo'].split("-");
			var Control_txt = $('#txt_PackingListControlNo').val();
			var Control_1st = Control_txt.split("-");
			
			if(parseInt(lastCtrl[0]) < parseInt(Control_1st['0'])){
				Control_1st['1'] = "01";
				$('#txt_PackingListControlNo').val(Control_1st['0']+"-"+Control_1st['1']);
			}else{
				lastCtrl['1'] = parseInt(lastCtrl['1']) + parseInt("1");
				if(parseInt(lastCtrl['1']) < 10){
					$('#txt_PackingListControlNo').val(lastCtrl['0']+"-0"+lastCtrl['1']);
				}else{
					$('#txt_PackingListControlNo').val(lastCtrl['0']+"-"+lastCtrl['1']);
				}
			}
		}
	});
}
</script>
</html>