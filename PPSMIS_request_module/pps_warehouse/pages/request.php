<br>
<div class="card shadow-sm">
	<span class="card-header h6">
		<i class="fa fa-fw fa-box-open"></i>
		<strong>PPD </strong> Warehouse 
		<?php
			if(isset($_GET['page'])&&$_GET['page']=='req'){
				echo "Request";
			}
			else if(isset($_GET['page'])&&$_GET['page']=='s'){
				echo "Issuance";
			}
		?>
	</span>
	<div class="card-body bg-white">
		<div class="row">
			<div class="col-sm-12" id="alert_popup_container_main"></div>
		</div>

	<div class="btn-toolbar justify-content-between" role="toolbar" aria-label="Toolbar with button groups">
  		<div class="btn-group" role="group" aria-label="First group">
  		<?php
		if(isset($_GET['page'])&&$_GET['page']=='s'){
		?>
			<a href="../../Resin.php"><button type="button" class="btn btn-sm btn-primary" id="back"><i class="fa fa-arrow-circle-left"></i> Back</button></a>
		<?php }?>
		</div>		

<div class="input-group">
    <div class="input-group-prepend">
		<div class="row mb-2">
			<div class="col-sm-12 text-right">

					<?php
					if(isset($_GET['page'])&&$_GET['page']=='req'){
					?>
						<button type="button" class="btn btn-sm btn-primary" id="btn_add_new_request_show_form"><i class="fa fa-plus"></i> New Request</button>
						<button type="button" class="btn btn-sm btn-primary" id="btn_edit_request" title="Edit"><i class="fa fa-pen-alt"></i></button>
						<button type="button" class="btn btn-sm btn-success" id="btn_ack" title="Acknowledge"><i class="fa fa-check-square"></i> Acknowledge</button>
					<?php }?>
					<?php
					if(isset($_GET['page'])&&($_GET['page']=='req'||$_GET['page']=='s') ){
					?>
						<button type="button" class="btn btn-sm btn-danger" id="btn_cancel_request" title="Cancel"><i class="fa fa-ban"></i></button>

					<?php }?>
					<button type="button" class="btn btn-sm btn-primary" id="btn_open_details"><i class="fa fa-info-circle"></i> Open Details</button>
					<?php
					if(isset($_GET['page'])&&$_GET['page']=='s'){
					?>
						<!--<button type="button" class="btn btn-sm btn-primary" id="btn_issue"><i class="fa fa-check"></i> Issue</button>-->
						<button type="button" class="btn btn-sm btn-primary" id="btn_issue_multiple"><i class="fa fa-check"></i> Issue</button>	
					<?php }?>
					</div>
				</div>
			</div>
		</div>
		<div class="table-responsive max_height_700px">
			<table class="table table-hover table-bordered table-striped table-sm" id="tbl_home">
				<thead>
					<tr>
						<th>Action</th>
						<th>Status</th>
						<th>Ctrl No</th>
						<th>Part Number</th>
						<th>Material Type</th>
						<th>Specifications</th>
						<th>Qty Requested</th>
						<th>Date Requested</th>
						<th>Requestor</th>
						<th>Destination</th>
						<th>Remarks (Requestor)</th>
						<!-- <th>Lot no</th> -->
						<th class="bg-info text-white">Qty Issued</th>
						<th class="bg-info text-white">Date Issued</th>
						<th class="bg-info text-white">Issued by</th>
						<th class="bg-info text-white">Lot No</th>
						<th class="bg-info text-white">Remarks (Issuer)</th>
						<th class="bg-info text-white">Balance Qty</th>
						<th class="bg-success text-white">Acknowledged By</th>
						<th class="bg-success text-white">Acknowledged</th>
						<th class="bg-success text-white">Receive Date</th>
					</tr>
				</thead>
				<tbody class="naa_cursor_pointer naa_table_selectable"></tbody>
			</table>
		</div>
	</div>
</div>
<div class="modal fade" id="modal_request_details" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="">Details!</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body form_group_narrow_label pb-0">
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label>Status:</label>
							<span id="span_status"></span>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label>Ctrl No:</label>
							<span id="span_ctrl_no"></span>
						</div>
					</div>
				</div>
				<hr class="row my-0">
				<div class="row">
					<div class="col-sm-12 border-right panel_1" id="panel_request">
						<h5 class="pt-3">Request Details</h5>
						<form id="frm_request">
							<div class="row" id="row_alert_info_control_number">
								<div class="col-sm-12">
									<div class="alert alert-info"><i class="fa fa-info-circle"></i> A <b>Control Number</b> will be generated after the request has been submitted.</div>
								</div>
							</div>
							<input type="hidden" id="hiddenSelectedId">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label>Material Type!</label>
										<input type="text" class="form-control form-control-sm placeholder_search" id="txt_req_product_code" list="dl_req_product_code" data-pkid=''>
										<datalist id="dl_req_product_code"></datalist>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label>Part Number</label>
										<input type="text" class="form-control form-control-sm label_input" id="txt_req_product_name" readonly>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label>Requested qty</label>
										<input type="number" class="form-control form-control-sm" id="txt_req_qty" name="txt_req_qty" step="any" required>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label>Destination</label>
										<input type="text" class="form-control form-control-sm" id="txt_req_destination" name="txt_req_destination" list="dl_destination" required>
										<datalist id="dl_destination"></datalist>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<label>Remarks</label>
										<textarea rows="4" class="form-control form-control-sm" id="txt_req_remarks" name="txt_req_remarks"></textarea>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label>Requested on</label>
										<input type="text" class="form-control form-control-sm  label_input" id="txt_req_on" name="txt_req_on" readonly required>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label>Requested by</label>
										<input type="text" class="form-control form-control-sm  label_input" id="txt_req_by" name="txt_req_by" readonly required>
									</div>
								</div>
							</div>
						</form>
					</div>
					<div class="col-sm-6 panel_2" id="panel_issuance" hidden>
						<h5 class="pt-3">Issuance Details</h5>
						<form id="frm_issue">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label>Issued qty</label>
										<input type="number" min="1" class="form-control form-control-sm" id="txt_issue_qty" name="txt_issue_qty" required>
									</div>
								</div>
								<div class="col-sm-6" id="col_allowed_qty">
									<div class="form-group">
										<label>Allowed qty !</label>
										<input type="number" step="0.01" class="form-control form-control-sm label_input" id="txt_issue_allowed_qty" readonly>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<label>Remarks</label>
										<textarea rows="4" min="1" class="form-control form-control-sm" id="txt_issue_remarks" name="txt_issue_remarks"></textarea>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label>Issued on</label>
										<input type="text" class="form-control form-control-sm label_input" id="txt_issue_on" name="txt_issue_on" readonly>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label>Issued by</label>
										<input type="text" class="form-control form-control-sm label_input" id="txt_issue_by" name="txt_issue_by" readonly>
									</div>
								</div>
								<div class="col-sm-6">
								</div>
							</div>
						</form>
					</div>
				</div>
				<div class="row">
					<!-- <div class="col-sm-12" id="alert_popup_container_new_request"></div> -->
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-success btn_submit" id="btn_ok_request" form="frm_request">Request</button>
				<button type="submit" class="btn btn-success btn_submit" id="btn_ok_update" form="frm_request">Save</button>
				<button type="submit" class="btn btn-success btn_submit" id="btn_ok_issue" form="frm_issue">Issue</button>
			</div>
		</div>
	</div>
</div>


<div class="modal fade " id="modal_request_details_multiple" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="">Details</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body form_group_narrow_label pb-0">
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label>Status:</label>
							<span id="span_status"></span>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label>Ctrl No:</label>
							<span id="span_ctrl_no"></span>
						</div>
					</div>
				</div>
				<hr class="row my-0">
				<div class="row">
					<div class="col-sm-12 border-right panel_1" id="panel_request">
						<h5 class="pt-3">Request Details</h5>
						<form id="frm_request_multiple">
							<div class="row" id="row_alert_info_control_number">
								<div class="col-sm-12">
									<div class="alert alert-info"><i class="fa fa-info-circle"></i> A <b>Control Number</b> will be generated after the request has been submitted.</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label>Material Type</label>
										<input type="text" class="form-control form-control-sm placeholder_search" id="txt_req_product_code" list="dl_req_product_code">
										<datalist id="dl_req_product_code"></datalist>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label>Part Number</label>
										<input type="text" class="form-control form-control-sm label_input" id="txt_req_product_name" readonly>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label>Requested qty</label>
										<input type="number" class="form-control form-control-sm" id="txt_req_qty" name="txt_req_qty" step="any" required>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label>Destination</label>
										<input type="text" class="form-control form-control-sm" id="txt_req_destination" name="txt_req_destination" list="dl_destination" required>
										<datalist id="dl_destination"></datalist>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<label>Remarks</label>
										<textarea rows="4" class="form-control form-control-sm" id="txt_req_remarks" name="txt_req_remarks"></textarea>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label>Requested on</label>
										<input type="text" class="form-control form-control-sm  label_input" id="txt_req_on" name="txt_req_on" readonly required>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label>Requested by</label>
										<input type="text" class="form-control form-control-sm  label_input" id="txt_req_by" name="txt_req_by" readonly required>
									</div>
								</div>
							</div>
						</form>
					</div>
					<div class="col-sm-6 panel_2" id="panel_issuance_multiple" hidden>
						<h5 class="pt-3">Issuance Details</h5>
						<form id="frm_issue_multiple">
							<button type="button" class="btn btn-success" name="add" id="add"  form="frm_issue_multiple">Add Lot No.</button>
							<div>
								<br>
							</div>

							<!-- <div class="input-group input-group-sm">
								<div class="input-group-prepend">
									<button type="button" class="btn btn-info" id="btn_scan_lot_no" data-target="#mdl_scan_lot_no"><i class="fa fa-qrcode"></i></button>
								</div>
								<input type="search" class="form-control" placeholder="Scan Lot no" id="txt_search_lot_no" readonly>
							</div> -->

							<div class="row">
							<table class="table table-bordered" id="dynamic_field">
							<tr> 
								<!-- <td width="100px" style="border-style:hidden;">
									<label>Issued qty</label>
										<input type="number" class="form-control form-control-sm qty" id="txt_multi_issue_qty" name="txt_multi_issue_qty[]" onKeyUp = "calculateSum()" step="any" required>
								</td>
									<td style="border-style:hidden;">
										<label>Remarks</label>
											<input type="text" class="form-control form-control-sm" id="txt_multi_issue_remarks" name="txt_multi_issue_remarks[]" required>
									</td> -->
							</tr> 
                            </table>  
								<div class="col-sm-6">
									<div class="form-group">
										<label>Issued qty Total</label>
										<input type="number" class="form-control form-control-sm" id="txt_total_issue_qty" name="txt_total_issue_qty" step="any" required>
									</div>
								</div>								
								<div class="col-sm-6" id="col_allowed_qty">
									<div class="form-group">
										<label>Allowed qty !!</label>
										<input type="number" step="0.01" class="form-control form-control-sm label_input" id="txt_issue_allowed_qty" readonly>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label>Issued on</label>
										<input type="text" class="form-control form-control-sm label_input" id="txt_issue_on_multi" name="txt_issue_on_multi" readonly>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label>Issued by</label>
										<input type="text" class="form-control form-control-sm label_input" id="txt_issue_by_multi" name="txt_issue_by_multi" readonly>
									</div>
								</div>
								<div class="col-sm-6">
								</div>
							</div>
						</form>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12" id="alert_popup_container_new_request"></div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-success btn_submit" id="btn_ok_request" form="frm_request_multiple">Request</button>
				<button type="submit" class="btn btn-success btn_submit" id="btn_ok_update" form="frm_request_multiple">Save</button>
				<button type="submit" class="btn btn-success btn_submit" id="btn_ok_issue" form="frm_issue_multiple">Issue</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="mdl_scan_lot_no" data-formid="" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header border-bottom-0 pb-0">
            <!-- <h5 class="modal-title" id="exampleModalLongTitle"></h5> -->
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body pt-0">
            <div class="text-center text-secondary">
				<div class="text-center text-secondary"><input type="hidden" name="index_no" id="id_index_no" disabled></div>

              Please scan the QR code.
              <br>
              <br>
              <h1><i class="fa fa-qrcode fa-lg"></i></h1>
            </div>
            <input type="text" id="txt_scan_lot_no_id" autocomplete="off">
          </div>
        </div>
      </div>
</div>

 <!-- Print QR Code -->
<div class="modal fade" id="mdl_print_preview" data-formid="" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header border-bottom-0 pb-0">
            <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
	        	<h4 class="modal-title"><span class="fa fa-qrcode"></span> GENERATE QR CODE STICKER</h4>
	      	</div>
          </div>
          <div class="modal-body pt-0">
			<input type="hidden" name="pkid">
	        <div class="row">
				<!-- <div class="col-sm-3">
					<img src="data:image/png;base64," id="imgQRCodeSticker" style="width:130px;height:130px;"><br>
				</div> -->
				<div class="col-sm-12">
					<b>Part Code:</b>	<label id="part_code"></label><br>
					<b>Part Name:</b>	<label id="part_name"></label><br>
					<b>Issued Qty:</b>	<label id="issued_qty"></label><br>
					<b>Lot Number:</b>	<label id="lot_num"></label>
				</div>
			</div>

			<div class="modal-footer">
	        <button type="submit" id="btnPrintQRCode" class="btn btn-success btn-sm">PRINT</button>
	        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">CLOSE</button>
	      </div>	

          </div>
        </div>
      </div>
</div>

<?php require_once('../footer-includes.inc.php');?>
<script type="text/javascript">

var current_page = '';
	<?php
		if(isset($_GET['page'])&&$_GET['page']=='req'){
			echo "current_page = 'req'";
		}
		else if(isset($_GET['page'])&&$_GET['page']=='s'){
			echo "current_page = 's'";
		}
	?>

	function myFunction() {
		
	}

	$('#mdl_scan_lot_no').on('shown.bs.modal', function () {
		document.getElementById("txt_scan_lot_no_id").style.opacity = 0;
		document.getElementById("txt_scan_lot_no_id").style.position = "absolute";
		$('#txt_scan_lot_no_id').trigger('focus')
	})

	$(document).on('click','#btn_scan_lot_no',function(e){
        // $('#txt_scan_lot_no_id').focus();
        // $('#txt_multi_issue_remarks').val('');
        // $('#txt_scan_lot_no_id').val('');
		let id = $(this).attr('index');
		$('#id_index_no').val(id);
		$('#txt_scan_lot_no_id').val('');
        $('#mdl_scan_lot_no').attr('data-formid','fn_scan_po_number').modal('show');
    });

	

	$(document).on('keypress','#txt_scan_lot_no_id',function(e){
        if( e.keyCode == 13 ){
            // var scanLotNoId = $('#txt_scan_lot_no_id').val();
            // $('#txt_multi_issue_remarks').val(scanLotNoId);
            $('#mdl_scan_lot_no').modal('hide');
			var index = $('#id_index_no').val();
			var val = $('#txt_scan_lot_no_id').val();
			$('#txt_multi_issue_remarks'+index+'').val(val);
			// $('#txt_multi_issue_qty'+index+'').val(val);
        }
    });


	
</script>
<script type="text/javascript" language="javascript" src="dist/js/home.js?v=123123123123"></script>