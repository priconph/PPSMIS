<?php
require_once('./includes.php');
require_once('./ppswarehouse-oop.php');
$dateToday = date("Y-m-d");
$lastUpdate = date("Y-m-d H-i-s");
$dateReset = '2000-01-01';
session_start();
$username = $_SESSION["username"];
$id = $_GET["id"];
$PartNumber = $_GET["PartNumber"];
$result = PPSW::getInstance()->getResinInfoById($id);
if($row = mysqli_fetch_array($result)){
	$Classification = $row["Classification"];
	$ProductLine = $row["ProductLine"];
	$MaterialType = $row["MaterialType"];
	$Model = $row["Model"];
	$Balance = $row["Balance"];
	$dateforBalance = $row["LastUpdate"];
	$Supplier = $row["Supplier"];
}
echo '<input type="date" id="txt_dateReset" style="display:none;" value="'.$dateReset.'">';
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
#tbl_Process_length select{
	padding: 5px 10px;
	border-radius: 4px;
	border:1px solid #aaa;
}
#tbl_Process_filter input{
	padding: 5px 10px;
	border-radius: 4px;
	border:1px solid #aaa;
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
	<div class="" style="width:100%;margin:auto;">
	  <div class="panel-heading" style="background:none;color:black;height:55px;border:none;padding-left:20px;">Update Resin Transaction</div>
	  <div class="panel-body" style="margin-top:-30px;">
		<div class="panel panel-primary">
			<div class="panel-body">
				<div class="row">
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-4">
								<label class="label-pad">Product Line:</label>
							</div>
							<div class="col-sm-6">
								<input type="text" id="txt_ProductLine" class="form-control condensed" value="<?php echo $ProductLine; ?>" readonly> 
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-4">
								<label class="label-pad">Part Number:</label>
							</div>
							<div class="col-sm-6">
								<input type="text" id="txt_PartNumber" class="form-control condensed" value="<?php echo $PartNumber; ?>">
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-4">
								<label>Material Type:</label>
							</div>
							<div class="col-sm-6">
								<input type="text" id="txt_MaterialType" class="form-control condensed" value="<?php echo $MaterialType; ?>">
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-4">
								<label>Model / Location:</label>
							</div>
							<div class="col-sm-6">
								<input type="text" id="txt_Model" class="form-control condensed" value="<?php echo $Model; ?>">
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-4">
								<label>Supplier:</label>
							</div>
							<div class="col-sm-6">
								<input type="text" id="txt_Supplier" class="form-control condensed" value="<?php echo $Supplier; ?>">
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-4">
								<label>Balance as of <label id="lbl_RecentDate"></label></label>
							</div>
							<div class="col-sm-6">
								<input type="text" id="txt_EOH" class="form-control condensed" value="<?php echo $Balance; ?>" readonly>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-4">
								<label>Classification:</label>
							</div>
							<div class="col-sm-6">
								<select id="txt_Classification" class="form-control condensed">
									<option value="">Select an Option</option>
									<?php
										$result = PPSW::getInstance()->get_classification();
										while($row = mysqli_fetch_array($result)){
											echo '<option value="'.$row['id'].'">'.$row['Classification'].'</option>';
											
										}
									?>
								</select>
							</div>
						</div>
					</div>
				</div>
				<br />
				<button class='btn btn-primary pull-right' id="btn_open_modal_transaction">Add New Transaction</button>
			</div>
		  </div>
		
		
		<div class="panel panel-primary">
			<div class="btn-group" style="padding-top:1%;padding-left:1%;">
					<button id="btn_GenerateReport" type="button" class="btn btn-default">GENERATE REPORT</button>
				</div>
			<div class="panel-body" style="height:50%;overflow:scroll;margin-top: 10px;border-top: 1px solid #eee;">
				<table id="tbl_Process" border="" class="table table-bordered" width="100%">
					<thead>
						<tr>
							<th>Date</th>
							<th>Price</th>
							<th>Invoice No.</th>
							<th>Transfer Slip No.</th>
							<th>BOH</th>
							<th>IN</th>
							<th>OUT</th>
							<th>EOH</th>
							<th>Recorded By</th>
							<th>Remarks</th>
							<th><i class="fa fa-cog fa-spin>"></i></th>
							<th style="display:none;">id</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$result = PPSW::getInstance()->getResinTransactionInfo_auto($id);
							$boh = 0;
							$ctr = 0;
							while($row = mysqli_fetch_array($result)){
								if($ctr==0){
									$boh = $row["Boh"];
								}
								$row_new['Date'][] = $row["Date"];
								$row_new['Price'][] = $row["Price"];
								$row_new['InvoiceNo'][] = $row["InvoiceNo"];
								$row_new['TransferSlipNo'][] = $row["TransferSlipNo"];
								$boh = sprintf("%.2f",$boh);
								$row_new['Boh'][] = $boh;
								$row_new['In'][] = $row["In"];
								$row_new['Out'][] = $row["Out"];
								$boh = ((float)$boh + (float)$row["In"]) - (float)$row["Out"];//eoh
								$row_new['Eoh'][] = $boh;
								$row_new['RecordedBy'][] = $row["RecordedBy"];
								$row_new['Remarks'][] = $row["Remarks"];
								$row_new['pkid'][] = $row["pkid"];
								$ctr++;
							}

							$row_new['Date'] = array_reverse($row_new['Date']);
							$row_new['Price'] = array_reverse($row_new['Price']);
							$row_new['InvoiceNo'] = array_reverse($row_new['InvoiceNo']);
							$row_new['TransferSlipNo'] = array_reverse($row_new['TransferSlipNo']);
							$row_new['Boh'] = array_reverse($row_new['Boh']);
							$row_new['In'] = array_reverse($row_new['In']);
							$row_new['Out'] = array_reverse($row_new['Out']);
							$row_new['Eoh'] = array_reverse($row_new['Eoh']);
							$row_new['RecordedBy'] = array_reverse($row_new['RecordedBy']);
							$row_new['Remarks'] = array_reverse($row_new['Remarks']);
							$row_new['pkid'] = array_reverse($row_new['pkid']);
						
							for($ctr2 = 0; $ctr2<$ctr; $ctr2++){
								echo "<tr>";
								echo "<td>".$row_new["Date"][$ctr2]."</td>";
								echo "<td>".$row_new["Price"][$ctr2]."</td>";
								echo "<td>".$row_new["InvoiceNo"][$ctr2]."</td>";
								echo "<td>".$row_new["TransferSlipNo"][$ctr2]."</td>";
								echo "<td>".$row_new["Boh"][$ctr2]."</td>";
								echo "<td>".$row_new["In"][$ctr2]."</td>";
								echo "<td>".$row_new["Out"][$ctr2]."</td>";
								echo "<td>".$row_new["Eoh"][$ctr2]."</td>";
								echo "<td>".$row_new["RecordedBy"][$ctr2]."</td>";
								echo "<td>".$row_new["Remarks"][$ctr2]."</td>";
								echo "<td style='text-align:center;'><a href='#' onclick='rl_update_data(".$row_new["pkid"][$ctr2].")'><i class='fa fa-edit'></i></a></td>";
								echo "<td class='id' style='display:none;'>".$row_new["pkid"][$ctr2]."</td>";
								echo "</tr>";
							}
						?>
					</tbody>
				</table>
			</div>
		</div>
		
		<div class="panel panel-primary">
			<div class="panel-body" align="right">
				<a href="#"><button class="btn btn-success glyphicon glyphicon-ok" id="btn_UpdateData" style="width:10%;">DONE</button></a>
			</div>
		</div>
	  </div>
	</div>
	

<!--Modal-->
<div class="modal fade" tabindex="-1" role="dialog" id="modal_add_transaction">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><span class="fa fa-plus"></span> Add Transaction</h4>
      </div>
      <div class="modal-body">
        <div class="row">
			<div class="col-sm-6">
			</div>
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-4">
						<label>Price:</label>
					</div>
					<div class="col-sm-8">
						<input type="" id="txt_Price" class="form-control condensed" readonly> <!--Chan 10/17/2022 - readonly -->
					</div>
				</div>
			</div>
		</div>
        <div class="row">
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-4">
						<label>Date:</label>
					</div>
					<div class="col-sm-8">
						<input type="date" id="txt_DateDaily" class="form-control condensed" readonly value="<?php echo $dateToday; ?>">
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-4">
						<label>Invoice No:</label>
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
						<label>Trans. Slip:</label>
					</div>
					<div class="col-sm-8">
						<input type="" id="txt_TransferSlipNoDaily" class="form-control condensed">
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-4">
						<label>BOH:</label>
					</div>
					<div class="col-sm-8">
						<input onkeypress="CheckNumeric()" type="" id="txt_BOHDaily" class="form-control condensed" value="0" readonly>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-4">
						<label>IN:</label>
					</div>
					<div class="col-sm-8">
						<input onkeypress="CheckNumeric()" type="" id="txt_INDaily" class="form-control condensed" value="0">
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-4">
						<label>OUT:</label>
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
						<label>EOH:</label>
					</div>
					<div class="col-sm-8">
						<input onkeypress="CheckNumeric()" type="" id="txt_EOHDaily" class="form-control condensed" value="0" readonly>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-4">
						<label>Recorded By:</label>
					</div>
					<div class="col-sm-8">
						<input type="" value= "<?php echo $username; ?>" id="txt_RecordedByDaily" class="form-control condensed" readonly>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-2">
				<label>Remarks:</label>
			</div>
			<div class="col-sm-10">
				<input type="" id="txt_RemarksDaily" class="form-control condensed">
			</div>
		</div>
      </div>
      <div class="modal-footer">
		<!-- TEST CODE -->
		<button class="btn btn-primary fa fa-print" id="btn_print_preview">Print QR</button>
		<!-- TEST CODE -->
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button id="btn_ModalAddNewData" class="btn btn-success">Add Transaction</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

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
			   <a href="#" id="btn_GenereteReportExcelAll" class="btn btn-success">ALL RECORD</a>
			   <a href="#" id="btn_GenereteReportExcel" class="btn btn-success">GENERATE REPORT</a>
			   <a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-danger">CLOSE</a>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" tabindex="-1" role="dialog" id="mdl_rl_update_data">
  <div class="modal-dialog modal-lg" role="document">
  	<form method="post" id="frm_rl_update_data">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title"><span class="fa fa-plus"></span> Add Transaction</h4>
	      </div>
	      <div class="modal-body">
			<input type="text" name="txt_rl_pkid">
	        <div class="row">
				<div class="col-sm-6">
					<div class="row">
						<div class="col-sm-4">
							<label>Price:</label>
						</div>
						<div class="col-sm-8">
							<input type="text" name="txt_rl_price" class="form-control condensed">
						</div>
					</div>
				</div>
<!-- 				<div class="col-sm-6">
					<div class="row">
						<div class="col-sm-4">
							<label>Recorded By:</label>
						</div>
						<div class="col-sm-8">
							<input type="text" value= "<?php echo $username; ?>" name="txt_rl_recorded_by" class="form-control condensed" readonly>
						</div>
					</div>
				</div> -->
			</div>
			<br>
	        <div class="row">
				<div class="col-sm-6">
					<div class="row">
						<div class="col-sm-4">
							<label>Invoice No:</label>
						</div>
						<div class="col-sm-8">
							<input type="text" name="txt_rl_invoice_no" class="form-control condensed">
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="row">
						<div class="col-sm-4">
							<label>Transfer Slip No:</label>
						</div>
						<div class="col-sm-8">
							<input type="text" name="txt_rl_transfer_slip_no" class="form-control condensed">
						</div>
					</div>
				</div>
			</div>
			<br>
			<div class="row">
				<div class="col-sm-2">
					<label>Remarks:</label>
				</div>
				<div class="col-sm-10">
					<input type="text" name="txt_rl_remarks" class="form-control condensed">
				</div>
			</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        <button type="submit" class="btn btn-success">Update Transaction</button>
	      </div>
	    </div>
	</form>
  </div><!-- /.modal-dialog -->
</div>

<!-- CLARK NEW CODE -->
	<!-- TS Modals -->
	<div class="modal fade" tabindex="-1" role="dialog" id="modal-print_preview">
	<div class="modal-dialog" style="width:1000px;">
		<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">Print Preview</h4>
		</div>
		<div class="modal-body" style="font-size:25px;">
			<iframe width="100%" height="60%" id="ifrm-print_preview"></iframe>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<div class="modal fade" tabindex="-1" role="dialog" id="modal-system_message">
	<div class="modal-dialog">
		<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">System Message</h4>
		</div>
		<div class="modal-body" style="font-size:25px;">
			
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
<!-- CLARK NEW CODE -->

</body>
<?php
?>
<script>
$('document').ready(function(){
	//################################# CLARK TEST START #################################
	var handler = "dist/handler/handler.php";

	function system_message(message){
		$('#modal-system_message .modal-body').empty();
		$('#modal-system_message .modal-body').append('<p><h4>'+message+'</h4></p>');
		$('.modal').modal('hide');
		$('#modal-system_message').modal('show');
	}

	$('#btn_print_preview').click(function(){
		$('#modal-print_preview').modal('show');
		generate_print_preview();
	});

	// $('#btn_print_preview').click(function(){ generate_print_preview(); });
	
	function generate_print_preview(){
	/* this should be different format but this one seems to work for all */
	var path = 'format/Dynamic.php';
	// console.log(path);
	check_session(path);			
	}

	function check_session(path){
		$.ajax({
			type	: "post",
			dataType: "json",
			data	: 'action=check_session',
			url		: handler,
			success	: function(result){
				// if(result['session_on'] == 1){
					// validate_sticker_data
					validate_sticker_data(path);
				// }else{
				// 	system_message("Warning Session Expired! Please, Relogin!");
				// }
				// validate_original_casemark(path,'print');
			}
		});
	}

	function validate_sticker_data(path){
		// if( $('#txt_MaterialType').val() != '' && $('#txt_PartNumber').val() != '' && $('#txt_RemarksDaily ').val() != ''){
		// 	// system_message('There are no label to print!');
		// 	// return false;
		// }
		// if($('#txt_RemarksDaily').val() == ''){
		// 	system_message('Please Place Lot Number Value on Field');
		// 	return false;
		// }
		validate_original_casemark(path,'print');
	}

	/* check if casemark has been modified */
	function validate_original_casemark(path,type){
		/* Array Initialization */
		// var material_type 	= [];
		// var part_number 	= [];
		// var remarks		 	= [];
		// var casemark 			= [];
		// var casemark_validate	= [];
		// var font_size 			= [];
		// var spacing 			= [];	
		
		if( $('#txt_RemarksDaily').val() != ''){
			var material_type = $('#txt_MaterialType').val();
			var part_number = $('#txt_PartNumber').val();
			var remarks = $('#txt_RemarksDaily').val();

			$('#ifrm-print_preview').attr('src',path);
			$('#modal-print_preview').modal('show');
			// casemark.push( encodeURI($('#txt-case_marks').val()) );
			// casemark_validate.push( ($('#txt-case_marks').val()) );
			// font_size.push( $('#txt-font_size').val() );
			// spacing.push( $('#txt-spacing').val() );
		}

		// console.log('qwe',material_type,
		// 			'qwert',part_number,
		// 			'qwerty',remarks);
		
		// var data = {
		// 	"action"			: "validate_original_casemark",
		// 	"material_type" 	: material_type,
		// 	"part_number"		: part_number,
		// 	"remarks"			: remarks
		// }
		
		// data = $.param(data);
		// $.ajax({
		// 	type	: "post",
		// 	dataType: "json",
		// 	data	: data,
		// 	url		: handler,
		// 	success	: function(result){
		// 		if(result['invalid'] != 1){
		// 			$('#ifrm-print_preview').attr('src',path);
		// 			$('#modal-print_preview').modal('show');
		// 			// if(type == 'print'){
		// 			// 	create_temp_casemark(material_type,casemark,font_size,spacing,path);
		// 			// }else{
		// 			// 	save_casemark();
		// 			// }
		// 		}else{
		// 			system_message('You cannot change a casemark!');
		// 		}
		// 		console.log(result);
		// 	},error	: function(result){
		// 		alert('error handler!');
		// 	}
		// });
	}

	// function create_temp_casemark(material_type,casemark,font_size,spacing,path){
	// 	var data = {
	// 		"action" 				: "create_temp_casemark",
	// 		"material_type"	: material_type,
	// 		"section"				: section,
	// 		"casemark"				: casemark,
	// 		"font_size"				: font_size,
	// 		"spacing"				: spacing,
	// 		"username"				: username
	// 	}
		
	// 	data = $.param(data);
	// 	$.ajax({
	// 		type	: "post",
	// 		dataType: "json",
	// 		data	: data,
	// 		url		: handler,
	// 		success	: function(result){
	// 			$('#ifrm-print_preview').attr('src',path);
	// 			$('#modal-print_preview').modal('show');
	// 		}
	// 	});
	// }
	//################################# CLARK TEST END #################################

	$('#tbl_Process').DataTable({
		"ordering":false,
		"columnDefs": [
			{"targets": "_all","orderable": false},
		],
		// "order": [[ 0, "asc" ]],
		"paging": false

	});


	$('#txt_Classification').val(<?php echo $Classification;?>)
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
	
	var LargestDay= $('#txt_dateReset').val();
	var total_BOH=0,total_EOH=0,total_IN=0;total_OUT=0;
		
	//load initial value here
	row = 0;
	var thisDate; 
	$('#tbl_Process tr').each(function(row, tr){	//loop to each tr 
		if(row>0){ 	//REMOVE ROW 0 WHICH IS A LABEL
			//GET VALUES HERE
			thisDate = $(tr).find('td:eq(0)').text();
			if(thisDate!=""){
				if(thisDate>LargestDay){
					LargestDay = thisDate;
				}
			}
			//total_BOH = parseFloat(total_BOH) + parseFloat($(tr).find('td:eq(3)').text());
			// total_IN = (parseFloat(total_IN) + parseFloat($(tr).find('td:eq(5)').text())).toFixed(2);
			// total_OUT = (parseFloat(total_OUT) + parseFloat($(tr).find('td:eq(6)').text())).toFixed(2);
		}
		if(row==1){
			total_EOH = parseFloat($(tr).find('td:eq(7)').text()).toFixed(2);
		}
		row++;	
	});

	if(total_EOH=='NaN'){
		total_EOH = '0.00';
	}
	// console.log(total_IN);
	// console.log(total_OUT);
	// total_EOH = 0;
	// total_EOH = (parseFloat(total_IN).toFixed(2) - parseFloat(total_OUT)).toFixed(2);
	// console.log(total_IN-total_OUT);
	//set txt_eom value
	$('#txt_EOH').val(total_EOH);
	$('#txt_EOHDaily').val(total_EOH);
	$('#lbl_RecentDate').text(LargestDay);
	$("#txt_BOHDaily").val(total_EOH);
		
	//set values after adding a product out data
	$("#btn_AddNewData").click(function(){
		$("#modal_AddNewData").modal("show");
		$("#txt_BOHDaily").val(total_EOH);
	});
	
	$('#btn_UpdateData').click(function(){
		UpdateRecord("2");
		UpdateItem("2");
	});
	
	$('#btn_ModalAddNewData').click(function(){
		check_session_status();
	});
	
	//On change functions, recompute data on user input
	$("#txt_INDaily").change(function(){
		$("#txt_EOHDaily").val((parseFloat($('#txt_INDaily').val())+parseFloat($('#txt_BOHDaily').val())).toFixed(2));
		//$("#txt_BOHDaily").val(parseFloat($('#txt_INDaily').val())-parseFloat($('#txt_OUTDaily').val()));
	});
	$("#txt_OUTDaily").change(function(){
		$("#txt_EOHDaily").val(((parseFloat($('#txt_BOHDaily').val()) + parseFloat($('#txt_INDaily').val())) - parseFloat($('#txt_OUTDaily').val())).toFixed(2));
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
	
	//Remove function here	
	$("#tbl_Process").on('click','.remCF',function(){ //TABLE ID = tblMain AND TABLE CLASS = remCF
		if (confirm('Delete this data?')) {
				var idforDelete = $(this).find(".id").text();
				var data = {
					"action" : "deleteRecordTransaction",
					"id" : "1"
				}
				alert(idforDelete);
				computetableData();
		}else{
			
		}		
	});
		
	$("#btn_GenereteReportExcel").click(function(){
		var productLine =  $("#txt_ProductLine").val();
		var fkid =  "<?php echo $id; ?>";
		var partNumber =  $("#txt_PartNumber").val();
		var materialType =  $("#txt_MaterialType").val();
		var model =  $("#txt_Model").val();
		var startDate = $('#txt_DateDailyStart').val();
		var endDate = $('#txt_DateDailyEnd').val();
		window.location.href = "./ExportUpdate.php?startDate="+startDate+"&endDate="+endDate+"&ProductLine="+productLine+"&fkid="+fkid+"&MaterialType="+materialType+"&Model="+model;
	});
			
	$("#btn_GenereteReportExcelAll").click(function(){
		var productLine =  $("#txt_ProductLine").val();
		var fkid =  "<?php echo $id; ?>";
		var partNumber =  $("#txt_PartNumber").val();
		var materialType =  $("#txt_MaterialType").val();
		var model =  $("#txt_Model").val();
		var startDate = "";
		var endDate = "";
		window.location.href = "./ExportUpdate.php?startDate="+startDate+"&endDate="+endDate+"&ProductLine="+productLine+"&fkid="+fkid+"&MaterialType="+materialType+"&Model="+model;
	});
	
	$('#btn_open_modal_transaction').click(function(){
		$('#modal_add_transaction').modal('show');
	});

	$('#frm_rl_update_data').submit(function(e){
		e.preventDefault();
		var data = {
			"action"		: "rl_update_data"
		}

		data = $.param(data)+"&"+$(this).serialize();
		$.ajax({
			type  	: "post",
			dataType: "json",
			data  	: data,
			url		: "./session_check.php",
			success : function(data){
				console.log(data);
				alert('Successfully Updated!');
				location.reload();
			},
			error   : function(data){
				console.log(data);
				alert('Something is wrong, please reload the page. If error persists, kindly contact ISS at local 205. Thank you!'); 
			}
		});			
	});

});

function UpdateRecord(redirect){
	var data = {
		"action" 		: "updateBalance",
		"id" 			: "<?php echo $id; ?>",
		"ProductLine" 	: $("#txt_ProductLine").val(),
		"PartNumber"	: $("#txt_PartNumber").val(),
		"MaterialType" 	: $("#txt_MaterialType").val(),
		"Model" 		: $("#txt_Model").val(),
		"Balance"		: $("#txt_EOH").val(),
		"Cost" 			: $("#txt_Cost").val(),
		"Supplier" 		: $('#txt_Supplier').val(),
		"Classification": $('#txt_Classification').val(),
		"LastUpdate" 	: "<?php echo $lastUpdate;?>",
		"Username" 		: "<?php echo $username;?>"
	}

	data = $.param(data);
	$.ajax({
		type : "post",
		dataType : "json",
		data : data,
		url : "./ppswarehouseHandler.php",
		success : function(data){
			if(redirect == "1"){
				
			}else{	
				window.location.href = './Resin.php';
			}
			
		}
	});
}

function UpdateItem(redirect){
	var data = {
		"action" 		: "updateItemList",
		"pkid_itemlist" : "<?php echo $id; ?>",
		"Classification": $('#txt_Classification').val(),		
		"partcode"		: $("#txt_PartNumber").val(),
		"ProductLine" 	: $("#txt_ProductLine").val(),
		"partname" 		: $("#txt_MaterialType").val(),
		"Model" 		: $("#txt_Model").val()
	}

	data = $.param(data);
	$.ajax({
		type : "post",
		dataType : "json",
		data : data,
		url : "./ppswarehouseHandler.php",
		success : function(data){
			if(redirect == "1"){
				
			}else{	
				window.location.href = './Resin.php';
			}
			
		}
	});
}


function SaveRecord(){
	if($('#txt_INDaily').val() == "0" && $('#txt_OUTDaily').val() == "0"){
		alert("IN and OUT = 0, Transaction was cancelled");
		return false;
	}
	
	if($("#txt_EOHDaily").val() < 0){
		alert("Item withrawn is greater than stock!");
		return false;
	}
	if($("#txt_DateDaily").val() != ""){
		if($('#txt_EOHDaily').val()=="0"){
			$('#txt_EOHDaily').val($('#txt_EOH').val());
		}
		
		var data = {
				"action" : "addNewProductWithrawal",
				"fkid" : "<?php echo $id; ?>",
				"Date" : $("#txt_DateDaily").val(),
				"InvoiceNo" : $("#txt_InvoiceNoDaily").val(),
				"TransferSlipNo" : $("#txt_TransferSlipNoDaily").val(),
				"BOH" : $("#txt_BOHDaily").val(),
				"IN" : $("#txt_INDaily").val(),
				"OUT" : $("#txt_OUTDaily").val(),
				"EOH" : $("#txt_EOHDaily").val(),
				"RecordedBy" : $("#txt_RecordedByDaily").val(),
				"Price" : $("#txt_Price").val(),
				"Remarks" : $("#txt_RemarksDaily").val(),
				"LastUpdate" : "<?php echo $lastUpdate;?>",
				"Username" : "<?php echo $username;?>",
				"AcknowledgedBy" : " ",
				"Status" : "0"
		}				
		data = $.param(data);
		$.ajax({
			type : "post",
			dataType : "json",
			data : data,
			url : "./ppswarehouseHandler.php",
			success : function(data){
				UpdateRecord("1");
				UpdateItem("1");
			}
		});
						
		var AppendThis = '<tr class=""><td>'+$("#txt_DateDaily").val()+'</td><td>'+$("#txt_Price").val()+'</td><td>'+$("#txt_InvoiceNoDaily").val()+'</td><td>'+$("#txt_TransferSlipNoDaily").val()+'</td><td>'+$("#txt_BOHDaily").val()+'</td><td>'+$("#txt_INDaily").val()+'</td><td>'+$("#txt_OUTDaily").val()+'</td><td>'+$("#txt_EOHDaily").val()+'</td><td>'+$("#txt_RecordedByDaily").val()+'</td><td>'+$("#txt_RemarksDaily").val()+'</td><td style="text-align: center;"><a href="#" onclick="rl_update_data('+data['lastID']+')"><i class="fa fa-edit"></i></a></td></tr>';
		$('#tbl_Process tbody').prepend(AppendThis); //APPEND NEW DATA INTO THE TABLE
		computetableData();
		
	}
	else{
		alert("Please add a Date!");
	}
}

function lastID(pn){
	var data = {
		"action" : "getLastID",
		"PartNumber" : pn
	}
	data = $.param(data);
	$.ajax({
		type : "post",
		dataType : "json",
		data : data,
		url : "./ppswarehouseHandler.php",
		success : function(data){
			
		}
	});
}

function remCF(id){
	alert("Temporary Unavailable!");
	return false;
	if (confirm('Delete this data?')) {
		var data = {
		"action" : "deleteResinRecord",
		"pkid" : id
		}
		data = $.param(data);
		$.ajax({
			type : "post",
			dataType : "json",
			data : data,
			url : "./ppswarehouseHandler.php",
			success : function(data){
				alert("Record has been deleted!");
			}
		});
		location.reload();
	}else{
		
	}		
}

function updateBalance(){
	var data = {
		"action" 		: "updateBalance",
		"id" 			: "<?php echo $id;?>",
		"ProductLine" 	: $("#txt_ProductLine").val(),
		"PartNumber"	: $("#txt_PartNumber").val(),
		"Balance"		: $("#txt_EOHDaily").val(),
		"LastUpdate" 	: "<?php echo $lastUpdate;?>",
		"Username" 		: "<?php echo $username;?>"
	}
	data = $.param(data);
	$.ajax({
		type : "post",
		dataType : "json",
		data : data,
		url : "./ppswarehouseHandler.php",
		success : function(data){
			
		}
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
			total_IN = (parseFloat(total_IN) + parseFloat($(tr).find('td:eq(5)').text())).toFixed(2);
			total_OUT = (parseFloat(total_OUT) + parseFloat($(tr).find('td:eq(6)').text())).toFixed(2);
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
	$("#txt_RemarksDaily").val("");
}

function check_session_status(){
	var rl_remarks = $('#txt_RemarksDaily').val();
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
			}
			else{
				if($('#txt_OUTDaily').val() > 0){
			 		if(rl_remarks.indexOf('MR') > -1 || rl_remarks.indexOf('mr') > -1 || rl_remarks.indexOf('MIMF') > -1|| rl_remarks.indexOf('mimf') > -1){
						SaveRecord();
			 		}
					else{
						alert('Warning! Kindly input the appropriate "OUT" data!');
					}
				}
				else{
					SaveRecord();

				}
			}
		} 
	});
}

function rl_update_data(pkid){
	var data = {
		"action"	: "rl_get_data_for_update",
		"pkid"		: pkid
	}
	data = $.param(data);
	$.ajax({
		type	: "post",
		dataType: "json",
		data	: data,
		url		: "./session_check.php",
		success	: function(data){
			$('#mdl_rl_update_data').modal('show');
			$('input[name=txt_rl_pkid]').val(pkid);
			$('input[name=txt_rl_price]').val(data['Price']);
			$('input[name=txt_rl_invoice_no]').val(data['InvoiceNo']);
			$('input[name=txt_rl_transfer_slip_no]').val(data['TransferSlipNo']);
			$('input[name=txt_rl_remarks]').val(data['Remarks']);
		} 
	});

}

</script>

</html>
