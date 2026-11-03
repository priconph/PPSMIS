<?php
require_once('./includes.php');
require_once('./ppswarehouse-oop.php');

include 'phpqrcode/qrlib.php'; 
date_default_timezone_set('Asia/Manila');
// $dateToday 		= date("Y-m-d");
$dateToday 		= date("Y-m-d H:i:s");
$lastUpdate 	= date("Y-m-d H-i-s");
$dateReset 		= '2000-01-01';

session_start();
$username 		= $_SESSION["username"];
$id 			= $_GET["id"];
$PartNumber 	= $_GET["PartNumber"];

$result = PPSW::getInstance()->getResinInfoById($id);
if($row = mysqli_fetch_array($result)){
	$Classification = $row["Classification"];
	$ProductLine 	= $row["ProductLine"];
	$PartNumber 	= $row["PartNumber"];
	$MaterialType 	= $row["MaterialType"];
	$Model 			= $row["Model"];
	$Balance 		= $row["Balance"];
	$dateforBalance = $row["LastUpdate"];
	$Supplier 		= $row["Supplier"];
	$Factory 		= $row["Factory"]; // 04082024 by Nessa
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

.hidden_scanner_input{
    position: absolute;
    opacity: 0;
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
  	<div class="panel-heading" style="background:none;color:black;height:55px;border:none;padding-left:20px;">
  		<h3>UPDATE RESIN / STAMPING TRANSACTION</h3>
	</div><br>

<div class="panel-body" style="margin-top:-30px;">
	<div class="panel panel-primary">
		<div class="panel-body">
			<div class="row">
				<div class="col-sm-6">
					<div class="row">
						<div class="col-sm-4">
							<label class="label-pad">PRODUCT LINE:</label>
						</div>
						<div class="col-sm-6">
							<input type="text" id="txt_ProductLine" class="form-control condensed" value="<?php echo $ProductLine; ?>" readonly> 
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="row">
						<div class="col-sm-4">
							<label class="label-pad">PART NUMBER:</label>
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
							<label>MATERIAL TYPE:</label>
						</div>
						<div class="col-sm-6">
							<input type="text" id="txt_MaterialType" class="form-control condensed" value="<?php echo $MaterialType; ?>">
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="row">
						<div class="col-sm-4">
							<label>MODEL / LOCATION:</label>
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
							<label>SUPPLIER:</label>
						</div>
						<div class="col-sm-6">
							<input type="text" id="txt_Supplier" class="form-control condensed" value="<?php echo $Supplier; ?>">
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="row">
						<div class="col-sm-4">
							<label>BALANCE as of <label id="lbl_RecentDate"></label></label>
						</div>
						<div class="col-sm-6">
							<input type="text" id="txt_EOH" class="form-control condensed" value="<?php echo $Balance; ?>" style="color:blue;" readonly>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<div class="row">
						<div class="col-sm-4">
							<label>CLASSIFICATION:</label>
						</div>
						<div class="col-sm-6">
							<select id="txt_Classification" class="form-control condensed">
								<option value="" selected disabled>--Select One--</option>
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
				<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-4">
								<label >FACTORY:</label>
							</div>
							<div class="col-sm-6">
								<input type="text" id="txt_Factory" class="form-control condensed" value="<?php echo $Factory; ?>">
								<!-- <select id="txt_Factory" class="form-control condensed">
									<option selected disabled>--Select One--</option>
									<option value="1">1</option>
									<option value="3">3</option>
								</select> -->
							</div>
						</div>
					</div>
			</div>
		</div>
	  </div>
	
	
	<div class="panel panel-primary">
		<div class="panel-body" style="height:50%;overflow:scroll;margin-top: 10px;border-top: 1px solid #eee;">

			<div class="pull-right">
				<button type="button" class='btn btn-primary btn-sm' id="btn_open_modal_transaction">ADD NEW TRANSACTION</button>
				<button type="button" class='btn btn-warning btn-sm' id="btn_GenerateReport">GENERATE REPORT</button>
				<button type="button" class='btn btn-success btn-sm' id="btn_UpdateData">DONE</button>
			</div>
			<br>
			<br>

			<table id="tbl_Process" border="" class="table table-bordered" width="100%">
				<thead>
					<tr>
						<th>Date</th>
						<!-- <th>Price</th> -->
						<th>Invoice No.</th>
						<th>Transfer Slip No.</th>
						<th>BOH</th>
						<th>IN</th>
						<th>OUT</th>
						<th>EOH</th>
						<th>Lot Number</th>
						<th>Remarks</th>
						<th>Recorded By</th>
						<th>Inspection Category</th>
						<th>Action</th>
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
							// $row_new['Price'][] = $row["Price"];
							$row_new['InvoiceNo'][] = $row["InvoiceNo"];
							$row_new['TransferSlipNo'][] = $row["TransferSlipNo"];
							$boh = sprintf("%.2f",$boh);
							$row_new['Boh'][] = $boh;
							$row_new['In'][] = $row["In"];
							$row_new['Out'][] = $row["Out"];
							$boh = ((float)$boh + (float)$row["In"]) - (float)$row["Out"];//eoh
							$row_new['Eoh'][] = $boh;
							$row_new['RecordedBy'][] = $row["RecordedBy"];
							$row_new['Lot_number'][] = $row["Lot_number"];
							$row_new['inspection_class'][] = $row["inspection_class"];
							$row_new['Remarks'][] = $row["Remarks"];
							$row_new['pkid'][] = $row["pkid"];

							$ctr++;
						}

						$row_new['Date'] = array_reverse($row_new['Date']);
						// $row_new['Price'] = array_reverse($row_new['Price']);
						$row_new['InvoiceNo'] = array_reverse($row_new['InvoiceNo']);
						$row_new['TransferSlipNo'] = array_reverse($row_new['TransferSlipNo']);
						$row_new['Boh'] = array_reverse($row_new['Boh']);
						$row_new['In'] = array_reverse($row_new['In']);
						$row_new['Out'] = array_reverse($row_new['Out']);
						$row_new['Eoh'] = array_reverse($row_new['Eoh']);
						$row_new['RecordedBy'] = array_reverse($row_new['RecordedBy']);
						$row_new['Lot_number'] = array_reverse($row_new['Lot_number']);
						$row_new['Remarks'] = array_reverse($row_new['Remarks']);
						$row_new['inspection_class'] = array_reverse($row_new['inspection_class']);
						$row_new['pkid'] = array_reverse($row_new['pkid']);
						
						for($ctr2 = 0; $ctr2<$ctr; $ctr2++){
							echo "<tr>";
							echo "<td>".$row_new["Date"][$ctr2]."</td>";
							// echo "<td>".$row_new["Price"][$ctr2]."</td>";
							echo "<td>".$row_new["InvoiceNo"][$ctr2]."</td>";
							echo "<td>".$row_new["TransferSlipNo"][$ctr2]."</td>";
							echo "<td>".$row_new["Boh"][$ctr2]."</td>";
							echo "<td>".$row_new["In"][$ctr2]."</td>";
							echo "<td>".$row_new["Out"][$ctr2]."</td>";
							echo "<td>".$row_new["Eoh"][$ctr2]."</td>";
							echo "<td>".$row_new["Lot_number"][$ctr2]."</td>";
							echo "<td>".$row_new["Remarks"][$ctr2]."</td>";
							echo "<td>".$row_new["RecordedBy"][$ctr2]."</td>";

							if ($row_new["inspection_class"][$ctr2] == '1'){
								$inspection_class = '<p style="color:purple;"><b>FOR IQC INSPECTION</b></p>';
							}else if ($row_new["inspection_class"][$ctr2] == '2'){
								$inspection_class = '<p style="color:black;"><i>Not required</i></p>';
							}else if ($row_new["inspection_class"][$ctr2] == '3'){
								$inspection_class = '<p style="color:green;"><b>Inspected by IQC</b></p>';
							}else{
								$inspection_class = '---';
							}
							echo "<td>".$inspection_class."</td>";
							echo "<td style='text-align:center;'>";
							echo "	<a href='#' onclick='rl_update_data(".$row_new["pkid"][$ctr2].")'><i class='fa fa-edit' style='color:blue'></i></a>";
							echo "	<a href='#' onclick='print_preview(".$row_new["pkid"][$ctr2].")'><i class='fa fa-print' style='color:green'></i></a>";
							echo "</td>";

							echo "<td class='id' style='display:none;'>".$row_new["pkid"][$ctr2]."</td>";
							echo "</tr>";
						}
					?>
				</tbody>
			</table>
		</div>
	</div>
	
	<!-- <div class="panel panel-primary">
		<div class="panel-body" align="right">
			<a href="#"><button class="btn btn-success glyphicon glyphicon-ok" id="btn_UpdateData" style="width:10%;">DONE</button></a>
		</div>
	</div> -->

  </div>
</div>
	
<div class="modal fade" tabindex="-1" role="dialog" id="modal_add_transaction">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><span class="fa fa-plus"></span> ADD TRANSACTION</h4>
      </div>
      <div class="modal-body">
        <div class="row">
			<div class="col-sm-3">
				<input type="radio" id="for_insp" name="inspection_class" value="1" required> <b>FOR IQC INSPECTION</b>
			</div>
			<div class="col-sm-3">
				<input type="radio" id="not_required" name="inspection_class" value="2"> <b>NOT REQUIRED</b>
			</div>
			<div class="col-sm-3">
			<input type="hidden" id="txt_MaterialCode" class="form-control condensed" value="<?php echo $PartNumber; ?>">
			<input type="hidden" id="txt_MaterialName" class="form-control condensed" value="<?php echo $MaterialType; ?>">
			</div>
		</div><br>
        <div class="row">
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-4">
						<label>DATE:</label>
					</div>
					<div class="col-sm-8">
						<!-- below is the code before FIFO -->
						<!-- <input type="date" id="txt_DateDaily" class="form-control condensed" readonly value="<?php echo $dateToday; ?>">  -->
						
						<!-- below is the code for FIFO -->
						<input type="datetime" id="txt_DateDaily" class="form-control condensed" readonly value="<?php echo $dateToday; ?>"> 

					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-4">
						<label>TRANS. SLIP:</label>
					</div>
					<div class="col-sm-8">
						<input type="" id="txt_TransferSlipNoDaily" class="form-control condensed">
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-4">
						<label hidden>PRICE:</label>
					</div>
					<div class="col-sm-8">
						<input type="" id="txt_Price" class="form-control condensed" readonly style="display:none;"> 
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-4">
						<label>INVOICE NO.:</label>
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
						<label>BOH:</label>
					</div>
					<div class="col-sm-8">
						<input onkeypress="CheckNumeric()" type="" id="txt_BOHDaily" class="form-control condensed" value="0" readonly>
					</div>
				</div>
			</div>
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
						<label>RECORDED BY:</label>
					</div>
					<div class="col-sm-8">
						<input type="" value= "<?php echo $username; ?>" id="txt_RecordedByDaily" class="form-control condensed" readonly>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-4">
						<label>LOT NO.:</label>
					</div>
					<div class="col-sm-8">
						<input type="" id="txt_LotNumber" class="form-control condensed">
						<div class="input-group-prepend">
                            <button type="button" class="btn btn-primary" id="btnScanQrPMaterialLotNo" form-value="formMaterialLotNo"><i class="fa fa-qrcode w-100"></i></button>
                        </div>
					</div>
				</div>
			</div>

		</div>
		<div class="row">
			<div class="col-sm-2">
				<label>REMARKS:</label>
			</div>
			<div class="col-sm-10">
				<input type="" id="txt_RemarksDaily" class="form-control condensed">
			</div>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="btn_ModalClose">CLOSE</button>
        <button id="btn_ModalAddNewData" class="btn btn-success btn-sm">ADD!</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalAddLotNo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body pt-0">
                <input type="text" id="textQrLotNo" class="hidden_scanner_input" autocomplete="off">
                <div class="text-center text-secondary">
                    Please scan the Lot #
                    <br><br>
                    <h1><i class="fa fa-qrcode fa-lg"></i></h1>
                </div>

            </div>
        </div>
    </div>
</div>

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
			   <a href="#" id="btn_Delete_Yes" class="btn btn-success btn-sm">YES</a>
			   <a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-danger btn-sm">NO</a>
			</div>
		</div>
	</div><!--  -->
</div>

<div id="modal_GenerateReport" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ModalDeleteLabel" aria-hidden="true" style="overflow:inherit">
	 <div class="modal-dialog">
		<div class="modal-content" style="width:400px">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				  <h4 class="modal-title" id="ModalDeleteLabel"><span class="fa fa-file-excel-o"> GENERATE REPORT</h4>
			</div>
			<div class="modal-body">
				<?php
					echo '<label style="margin-right:5%;width:20%;">Start Date:</label></label><input type="date" id="txt_DateDailyStart" style="width:50%;" value="'.$dateToday.'"><br>';
					echo '<label style="margin-right:5%;width:20%;">End Date:</label></label><input type="date" id="txt_DateDailyEnd" style="width:50%;" value="'.$dateToday.'">';
				?>
			</div>
			<div class="modal-footer">
			   <a href="#" id="btn_GenereteReportExcelAll" class="btn btn-success btn-sm">ALL RECORD</a>
			   <a href="#" id="btn_GenereteReportExcel" class="btn btn-success btn-sm">GENERATE REPORT</a>
			   <a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-danger btn-sm">CLOSE</a>
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
	        <h4 class="modal-title"><span class="fa fa-plus"></span> UPDATE TRANSACTION</h4>
	      </div>
	      <div class="modal-body">
			<input type="hidden" name="txt_rl_pkid">
	        <div class="row">
				<div class="col-sm-3">
					<input type="radio" id="txt_rl_for_insp" name="txt_rl_inspection_class" value="1"> <b>FOR IQC INSPECTION</b>
				</div>
				<div class="col-sm-3">
					<input type="radio" id="txt_rl_not_required" name="txt_rl_inspection_class" value="2"> <b>NOT REQUIRED</b>
				</div>
			</div><br>

	        <div class="row">
				<div class="col-sm-6">
					<div class="row">
						<div class="col-sm-4">
							<label hidden>PRICE:</label>
						</div>
						<div class="col-sm-8">
							<input type="text" name="txt_rl_price" class="form-control condensed" readonly style="display: none;">
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="row">
						<div class="col-sm-4">
							<label>INVOICE NO.:</label>
						</div>
						<div class="col-sm-8">
							<input type="text" name="txt_rl_invoice_no" class="form-control condensed">
						</div>
					</div>
				</div>
			</div>
	        <div class="row">
				<div class="col-sm-6">
					<div class="row">
						<div class="col-sm-4">
							<label>TRANS. SLIP:</label>
						</div>
						<div class="col-sm-8">
							<input type="text" name="txt_rl_transfer_slip_no" class="form-control condensed">
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="row">
						<div class="col-sm-4">
							<label>LOT NO.:</label>
						</div>
						<div class="col-sm-8">
							<input type="text" name="txt_rl_lot_no" class="form-control condensed">
						</div>
					</div>
				</div>

			</div>
			<div class="row">
				<div class="col-sm-2">
					<label>REMARKS:</label>
				</div>
				<div class="col-sm-10">
					<input type="text" name="txt_rl_remarks" class="form-control condensed">
				</div>
			</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">CLOSE</button>
	        <button type="submit" class="btn btn-success btn-sm">UPDATE</button>
	      </div>
	    </div>
	</form>
  </div><!-- /.modal-dialog -->
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="mdl_print_preview">
  <div class="modal-dialog" role="document">
  	<form method="post" id="frm_print_preview">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title"><span class="fa fa-qrcode"></span> GENERATE QR CODE STICKER</h4>
	      </div>
	      <div class="modal-body">
			<input type="hidden" name="txt_rl_pkid">
			<input type="hidden" name="invoice_num">
			<input type="hidden" name="rcvd_date">
			<input type="hidden" name="item_code">
			<input type="hidden" name="item_name">
			<input type="hidden" name="location">
			<input type="hidden" name="lot_num">

	        <div class="row">
				<div class="col-sm-3">
					<img src="data:image/png;base64," id="imgQRCodeSticker" style="width:130px;height:130px;"><br>
				</div>
				<div class="col-sm-6">
					Invoice Number:	<label id="invoice_num"></label><br>
					Received Date:	<label id="rcvd_date"></label><br>
					Item Code:		<label id="item_code"></label><br>
					Item Name:		<label id="item_name"></label><br>
					Location:		<label id="location"></label><br>
					Lot Number:		<label id="lot_num"></label>
				</div>
			</div>
	      <div class="modal-footer">
	        <button type="submit" id="btnPrintQRCode" class="btn btn-success btn-sm">PRINT</button>
			<!-- window.location.href = "./ExportUpdate.php?startDate="+startDate+"&endDate="+endDate+"&ProductLine="+productLine+"&fkid="+fkid+"&MaterialType="+materialType+"&Model="+model; -->
	        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">CLOSE</button>
	      </div>
	    </div>
	</form>
  </div><!-- /.modal-dialog -->
</div>

</body>
<?php
?>
<script>
let test = [];
$('document').ready(function(){
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
			// total_EOH = parseFloat($(tr).find('td:eq(7)').text()).toFixed(2);
			total_EOH = parseFloat($(tr).find('td:eq(6)').text()).toFixed(2);
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
		"Factory"		: $('#txt_Factory').val(), //04082024 by Nessa
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
		"Factory"		: $('#txt_Factory').val(), //04082024 by Nessa
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

	if (!$("input[name='inspection_class']").is(':checked')) {
		alert('Please select Inspection Category');
		return false;
	}

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
				// "Price" : $("#txt_Price").val(),
				"Remarks" : $("#txt_RemarksDaily").val(),
				"inspection_class" : $("input[name='inspection_class']:checked").val(),
				"Lot_number" : $("#txt_LotNumber").val(),
				"LastUpdate" : "<?php echo $lastUpdate;?>",
				"updated_at" : "<?php echo $lastUpdate;?>",
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

		if ($("input[name='inspection_class']:checked").val() == 1){
			var ins_cat = '<p style="color:purple;"><b>FOR IQC INSPECTION</b></p>';
		}else{
			var ins_cat = '<p style="color:black;"><i>not required</i></p>';
		}	

		var AppendThis = '<tr class=""><td>'+$("#txt_DateDaily").val()+'</td><td></td><td>'+$("#txt_InvoiceNoDaily").val()+'</td><td>'+$("#txt_TransferSlipNoDaily").val()+'</td><td>'+$("#txt_BOHDaily").val()+'</td><td>'+$("#txt_INDaily").val()+'</td><td>'+$("#txt_OUTDaily").val()+'</td><td>'+$("#txt_EOHDaily").val()+'</td><td>'+$("#txt_LotNumber").val()+'</td><td>'+$("#txt_RemarksDaily").val()+'</td><td>'+$("#txt_RecordedByDaily").val()+'</td><td>'+ins_cat+'</td><td style="text-align: center;"><a href="#" onclick="rl_update_data('+data['lastID']+')"><i class="fa fa-edit" style="color:blue"></i></a><a href="#" onclick="print_preview('+data['lastID']+')"><i class="fa fa-print" style="color:green"></i></a></td></tr>';
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
	$("#txt_LotNumber").val("");

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
			 		// if(rl_remarks.indexOf('MR') > -1 || rl_remarks.indexOf('mr') > -1 || rl_remarks.indexOf('MIMF') > -1|| rl_remarks.indexOf('mimf') > -1){
			 		if(rl_remarks == ''){
						alert('Warning! Kindly input the appropriate "OUT" data! and Add Justification');
			 		}
					else{
						SaveRecord();
						
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
			// $('input[name=txt_rl_price]').val(data['Price']);
			$('input[name=txt_rl_invoice_no]').val(data['InvoiceNo']);
			$('input[name=txt_rl_transfer_slip_no]').val(data['TransferSlipNo']);
			$('input[name=txt_rl_remarks]').val(data['Remarks']);
			$('input[name=txt_rl_lot_no]').val(data['Lot_number']);

			if (data['inspection_class'] == '1'){
				$('#txt_rl_for_insp').prop('checked', true);
			}else if (data['inspection_class'] == '2'){
				$('#txt_rl_not_required').prop('checked', true);
			}else{
				$('#txt_rl_not_required').prop('checked', false);
			}

		} 
	});

}

function print_preview(pkid){
	let path = "./format/Dynamic_test.php"
	var data = {
		"action"	: "get_qrcode_data",
		"pkid"		: pkid
	}
	data = $.param(data);
	$.ajax({
		type	: "post",
		dataType: "json",
		data	: data,
		url		: "./session_check.php",
		success	: function(data){
			test = data;
			console.log('DATA:', test)
			$('#imgQRCodeSticker').attr('src', "data:image/png;base64,"+data['qrcode']);
			
			$('input[name=txt_rl_pkid]').val(pkid);
			$('input[name=invoice_num]').val(data['InvoiceNo']);
			$('input[name=rcvd_date]').val(data['Date']);
			$('input[name=item_code]').val(data['PartNumber']);
			$('input[name=item_name]').val(data['MaterialType']);
			$('input[name=location]').val(data['Model']);
			// $('input[name=lot_num]').val(data['Remarks']);
			$('input[name=lot_num]').val(data['Lot_number']);

			if (data['InvoiceNo'] == null || data['InvoiceNo'] == '' || data['InvoiceNo'] == 'N/A'){
				$('#invoice_num').text('N/A');
			}else{
				$('#invoice_num').text(data['InvoiceNo']);
			}

			if (data['Date'] == null || data['Date'] == ''){
				$('#rcvd_date').text('N/A');
			}else{
				$('#rcvd_date').text(data['Date']);
			}

			if (data['PartNumber'] == null || data['PartNumber'] == ''){
				$('#item_code').text('N/A');
			}else{
				$('#item_code').text(data['PartNumber']);
			}

			if (data['MaterialType'] == null || data['MaterialType'] == ''){
				$('#item_name').text('N/A');
			}else{
				$('#item_name').text(data['MaterialType']);
			}

			if (data['Model'] == null || data['Model'] == ''){
				$('#location').text('N/A');
			}else{
				$('#location').text(data['Model']);
			}

			if (data['Lot_number'] == null || data['Lot_number'] == ''){
				$('#lot_num').text('N/A');
			}else{
				$('#lot_num').text(data['Lot_number']);
			}

			setTimeout(() => {
				$('#mdl_print_preview').modal('show');
			}, 100);

		} 
	});
}

$("#btnPrintQRCode").click(function(){
	popup = window.open();

	if (test['InvoiceNo'] == null || test['InvoiceNo'] == ''){
		var InvoiceNo ='N/A';
	}else{
		var InvoiceNo =test['InvoiceNo'];
	}

	if (test['Model'] == null || test['Model'] == ''){
		var location ='N/A';
	}else{
		var location =test['Model'];
	}

	let content = '';
		content += '<table style="margin-left: -5px;>';
		content += '<tr>';
		content += '<td>';
		content += '</td>';
		content += '</tr>';
		content += '<tr style="width: 290px;">';
		content += '	<td style="vertical-align: bottom;">';
		content += '		<img src="data:image/png;base64,'+test['qrcode']+'" style="width:70px;height:70px;">';
		content += '	</td>';
		content += '	<td>';
		content += '		<label style="text-align: center; font-family: Arial; font-size:8.5px;">'+InvoiceNo+'</label><br>';
		content += '		<label style="text-align: center; font-family: Arial; font-size:8.5px;">'+test['Date']+'</label><br>';
		content += '		<label style="text-align: center; font-family: Arial; font-size:8.5px;">'+test['PartNumber']+'</label><br>';
		content += '		<label style="text-align: center; font-family: Arial; font-size:8.5px;">'+test['MaterialType']+'</label><br>';
		content += '		<label style="text-align: center; font-family: Arial; font-size:8.5px;">'+location+'</label><br>';
		content += '		<label style="text-align: center; font-family: Arial; font-size:8.5px;">'+test['Lot_number']+'</label><br>';
		content += '	</td>';
		content += '</tr>';
		content += '</table>';

	popup.document.write(content);
	popup.focus(); //required for IE
	popup.print();
	popup.close();
});

/* Scanning of Lot No. */
$('#btnScanQrPMaterialLotNo').each(function(e){
    $(this).on('click',function (e) {
        let formValue = $(this).attr('form-value');
        $('#modalAddLotNo').attr('data-form-id', formValue).modal('show');
        $('#modalAddLotNo').val('');
        setTimeout(() => {
            $('#textQrLotNo').focus();
        }, 500);
    });
});

$('#textQrLotNo').on('keyup', function(e){
	if(e.keyCode == 13){

		try{
	        scannedItem = JSON.parse($(this).val());
	        console.log(scannedItem);
	        if ( scannedItem['code'] == $('#txt_MaterialCode').val() || scannedItem['name'] == $('#txt_MaterialName').val()){
	        	$('#txt_LotNumber').val(scannedItem['production_lot_no']);
	        	$('#txt_INDaily').val(scannedItem['sublot_qty']);
	        	$('#txt_RemarksDaily').val('Output Qty:'+scannedItem['output_qty']+', Sublot count:'+scannedItem['sublot_counter']);

	        	$('#txt_LotNumber').prop('readonly', true);
	        	$('#txt_INDaily').prop('readonly', true);


	        	$('#modalAddLotNo').modal('hide');

	        }else{
	        	alert('Invalid Sticker')
	        	$('#txt_LotNumber').val('');
	        	$('#txt_INDaily').val('');

	        	$('#txt_LotNumber').prop('readonly', true);
	        	$('#txt_INDaily').prop('readonly', true);

	        }
	    }
	    catch (e){
	        toastr.error('Invalid Sticker');
	    }
	    $(this).val('');
	}
});

$("#btn_ModalClose").click(function(){
	location.reload();
});




</script>

</html>
