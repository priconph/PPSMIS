<html>
	<head>
		<?php
			require_once('./includes.php');
    		echo '<input type="hidden" id="hdn_username" value="'.$_GET['username'].'">';
		?>
	</head>
	<body>
		<div style="height:450px;" id="body-valid">
	    	<input type="hidden" id="hdn_username" value="<?php echo $_GET['username'];?>">
			<div class="panel panel-primary" style="border:none;">
				<div class="panel-heading" style="background:none;color:black;height:55px;border:none;">
					<div class="col-sm-12" style="padding-right:0;padding-left:0;margin-bottom:0;">
						<div class="col-sm-12" style="padding-left:0;color:green;margin-bottom:10px;">
							<ul class="nav nav-pills">
								<li class="active"><a data-toggle="tab" href="#">Consolidated Data</a></li>
								<li><a href="./Resin_ppc.php?username=<?php echo $_GET['username'];?>">Warehouse Data</a></li>
								<li><a href="./Production_ppc.php?username=<?php echo $_GET['username'];?>">Production Data</a></li>
							</ul>
						</div>
						<div class="col-sm-12" style="padding-left:0;">
							<div class="btn-group pull-left col-sm-1" style="padding-right:2px;padding-left:2px;">
								<select class="form-control" style="height:33px;" name="slt_data_month">
									<option value="1">JAN</option>
									<option value="2">FEB</option>
									<option value="3">MAR</option>
									<option value="4">APR</option>
									<option value="5">MAY</option>
									<option value="6">JUN</option>
									<option value="7">JUL</option>
									<option value="8">AUG</option>
									<option value="9">SEP</option>
									<option value="10">OCT</option>
									<option value="11">NOV</option>
									<option value="12">DEC</option>
								</select>
							</div>
							<div class="btn-group pull-left col-sm-1" style="padding-right:2px;padding-left:2px;">
								<select class="form-control" style="height:33px;" name="slt_data_year">
								</select>
							</div>
							<div class="btn-group pull-left col-sm-1" style="padding-left:2px;padding-right:2px;">
								<button type="button" class="btn btn-warning fa fa-download" id="btn_export_report" style="height:33px;width:100%;">
									Export
								</button>
							</div>
							<div class="btn-group pull-left col-sm-1" style="padding-left:2px;padding-right:2px;">
								<button type="button" class="btn btn-success fa fa-plus" id="btn_add_price" style="height:33px;min-width:100%;">
									Add Price
								</button>
							</div>
						</div>
						<span style="color:#3f6d57;position:absolute;top:86;left:6;font-size:80%;z-index:99;"><i><span id="spn_range_of_date"></span></i><a style="cursor:pointer;" id="a_edit_range_of_date">&nbsp;<i class="fa fa-edit"></i></a></span>
					</div>
				</div>
				<div class="panel-body">
					<div style="margin-top:-44px;">
						<table id="t_consolidated_ppc" class="nowrap compact">
							<thead style="font-size:80%;">
								<tr>
									<th rowspan="2" style="text-align:center;vertical-align:middle;">ALLOCATION</th>
									<th rowspan="2" style="text-align:center;vertical-align:middle;">MATERIAL<br>CODE</th>
									<th rowspan="2" style="text-align:center;vertical-align:middle;">DESCRIPTION</th>
									<th rowspan="2" style="text-align:center;vertical-align:middle;">BOH U/P</th>
									<th rowspan="2" style="text-align:center;vertical-align:middle;">RECEIVED<br>U/P</th>
									<th rowspan="2" style="text-align:center;vertical-align:middle;">EOH PRICE</th>
									<th colspan="2" style="text-align:center;vertical-align:middle;">BOH(QTY. BASED)</th>
									<th rowspan="2" style="text-align:center;vertical-align:middle;">TOTAL</th>									
									<th colspan="3" style="text-align:center;vertical-align:middle;"></th>
									<th colspan="2" style="text-align:center;vertical-align:middle;">EOH(QTY. BASED)</th>
									<th rowspan="2" style="text-align:center;vertical-align:middle;">TOTAL</th>	
									<th colspan="2" style="text-align:center;vertical-align:middle;">BOH(AMOUNT BASED)</th>
									<th rowspan="2" style="text-align:center;vertical-align:middle;">TOTAL</th>	
									<th colspan="3" style="text-align:center;vertical-align:middle;"></th>
									<th colspan="2" style="text-align:center;vertical-align:middle;">EOH(AMOUNT BASED)</th>
									<th rowspan="2" style="text-align:center;vertical-align:middle;">TOTAL</th>	
								</tr>
								<tr>
									<th style="text-align:center;vertical-align:middle;">Warehouse</th>
									<th style="text-align:center;vertical-align:middle;">Production</th>
									<th style="text-align:center;vertical-align:middle;">Received</th>
									<th style="text-align:center;vertical-align:middle;">Adjustment</th>
									<th style="text-align:center;vertical-align:middle;">Used</th>
									<th style="text-align:center;vertical-align:middle;">Warehouse</th>
									<th style="text-align:center;vertical-align:middle;">Production</th>
									<th style="text-align:center;vertical-align:middle;">Warehouse</th>
									<th style="text-align:center;vertical-align:middle;">Production</th>
									<th style="text-align:center;vertical-align:middle;">Received</th>
									<th style="text-align:center;vertical-align:middle;">Adjustment</th>
									<th style="text-align:center;vertical-align:middle;">Used</th>
									<th style="text-align:center;vertical-align:middle;">Warehouse</th>
									<th style="text-align:center;vertical-align:middle;">Production</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
<!-- 							<tfoot style="font-size:85%;">
								<tr>
									<th colspan="6" style="text-align:right;vertical-align:middle;">TOTAL&nbsp;&nbsp;</th>
									<th style="text-align:right;vertical-align:middle;background-color:#f2e1b5;">0.00</th>
									<th style="text-align:right;vertical-align:middle;background-color:#f2e1b5;">0.00</th>
									<th style="text-align:right;vertical-align:middle;background-color:#b5ffc9;">0.00</th>
									<th style="text-align:right;vertical-align:middle;;background-color:#f2e1b5">0.00</th>
									<th style="text-align:right;vertical-align:middle;;background-color:#f2e1b5">0.00</th>
									<th style="text-align:right;vertical-align:middle;background-color:#b5ffc9;">0.00</th>
									<th style="text-align:right;vertical-align:middle;;background-color:#f2e1b5">0.00</th>
									<th style="text-align:right;vertical-align:middle;;background-color:#f2e1b5">0.00</th>
									<th style="text-align:right;vertical-align:middle;background-color:#b5ffc9;">0.00</th>
									<th style="text-align:right;vertical-align:middle;">0.00</th>
									<th style="text-align:right;vertical-align:middle;">0.00</th>
									<th style="text-align:right;vertical-align:middle;">0.00</th>
									<th style="text-align:right;vertical-align:middle;">0.00</th>
									<th style="text-align:right;vertical-align:middle;">0.00</th>
									<th style="text-align:right;vertical-align:middle;">0.00</th>
									<th style="text-align:right;vertical-align:middle;">0.00</th>
									<th style="text-align:right;vertical-align:middle;">0.00</th>
									<th style="text-align:right;vertical-align:middle;">0.00</th>
								</tr>
							</tfoot> -->
						</table>
					</div>
				</div>
			</div>
			<div class="modal fade" id="mdl_price_allocation">
				<div class="modal-dialog" style="width:1200px;margin-top:0">
				  	<form method="post" id="frm_price_allocation">
					    <div class="modal-content">
					      <div class="modal-header">
					        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					        <h4 class="modal-title"><i class="fa fa-file-excel-o"></i>&nbsp&nbspRemaining Stock: <span id="spn_material_remaining_stock" style="color:blue;font-weight:bold">10,000</span></h4>
					      </div>
					      <div class="modal-body" style="overflow-y:scroll;padding-top:20px;padding-bottom:20px;max-height:500px;">
							<div class="form-group">
								<div class="col-sm-12" style="margin-bottom:10px;padding-right:0;padding-left:0;">
									<div class="col-sm-6">
									    <label class="col-form-label">Allocation</label>
									    <input type="hidden" name="txt_whs_id">
										<input type="text" name="txt_allocation" class="form-control">
									</div>
									<div class="col-sm-6">
									    <label class="col-form-label">Material Code</label>
									    <input class="form-control" name="txt_material_code" style="color:blue;font-weight:bold;">
									</div>
								</div>
								<div class="col-sm-12" style="padding-right:0;padding-left:0;">
									<div class="col-sm-12">
									    <label class="col-form-label">Description</label>
									    <input class="form-control" name="txt_description" style="color:blue;font-weight:bold;">
									</div>
								</div>
							</div>
							<div class="form-group">							
								<div id="cont_prices">
									<div class="col-md-12" style="text-align:center;margin-bottom:5px;">
										<hr class="style7">
										<input type="hidden" name="txt_max_row" value="0">
										<input type="hidden" name="txt_add_max_row" value="0">
										<div class="col-md-9" style="padding:0;">
											<div class="col-md-12" style="margin:0;padding:0;">
												<div class="col-md-3" style="margin:0;padding:0;">
													<div class="col-md-6" style="padding:0;color:#07337a;">
														BOH Price
													</div>
													<div class="col-md-6" style="padding:0;color:#07337a;">
														Recv. Price
													</div>
												</div>
												<div class="col-md-3" style="margin:0;padding:0;">
													<div class="col-md-6" style="padding:0;color:#07337a;">
														EOH Price
													</div>
													<div class="col-md-6" style="padding:0;color:#067a16;">
														WHS Rem.
													</div>
												</div>
												<div class="col-md-3" style="margin:0;padding:0;">
													<div class="col-md-6" style="padding:0;color:#067a16;">
														Prodn. Rem.
													</div>
													<div class="col-md-6" style="padding:0;color:#067a16;">
														Recv. Qty.
													</div>
												</div>
												<div class="col-md-3" style="margin:0;padding:0;">
													<div class="col-md-6" style="padding:0;color:9b0303;">
														Adjustment
													</div>
													<div class="col-md-6" style="padding:0;color:9b0303;">
														Used Qty.
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-2">
											Added Date/Time
										</div>
										<div class="col-md-1">
										<i class="fa fa-gear fa-spin" style="margin-top:8%;"></i>
										</div>
									</div>
								  <div id="content_loop"></div>
								  <div class="col-md-12" style="margin-top:2%;">
										<div style="margin-left:1.5%;font-size:80%;">
											<a class="pointer" id="a_add_row"><i class="fa fa-plus"></i> Add Row</a>	
										</div>
										<hr class="style7">
								  </div>
								</div>
							</div>
						  </div>
					      <div class="modal-footer">
					        <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Save</button>
							<button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Close</button> 
					      </div>
					    </div>
				    </form>
				</div>
			</div>
			<div class="modal fade" id="mdl_choose_material">
				<div class="modal-dialog" style="width:500px;margin-top:0">
				  	<form method="post" id="frm_choose_material">
					    <div class="modal-content">
					      <div class="modal-header">
					        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					        <h4 class="modal-title"><i class="fa fa-file-excel-o"></i>&nbsp&nbspChoose Material</h4>
					      </div>
					      <div class="modal-body" style="overflow-y:auto;padding-top:20px;padding-bottom:20px;max-height:500px;overflow-y:auto;">
							<div class="form-group">
								<div class="col-sm-12" style="margin-bottom:10px;padding-right:0;padding-left:0;">
									<div class="col-sm-6">
									    <label class="col-form-label">Material Code</label>
									    <input class="form-control" name="txt_cm_material_code" style="color:blue;font-weight:bold;" list="list_material_code" autocomplete="off">
									    <datalist id="list_material_code">
									    </datalist>
									</div>
									<div class="col-sm-6">
									    <label class="col-form-label">Description</label>
										<input type="hidden" name="txt_cm_material_id">
										<input type="text" name="txt_cm_description" class="form-control" readonly>
									</div>
								</div>
							</div>
						  </div>
					      <div class="modal-footer">
					        <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Proceed</button>
							<button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Close</button> 
					      </div>
					    </div>
				    </form>
				</div>
			</div>
			<div class="modal fade" id="mdl_edit_range_of_data">
				<div class="modal-dialog" style="width:500px;margin-top:0">
				  	<form method="post" id="frm_edit_range_of_data">
					    <div class="modal-content">
					      <div class="modal-header">
					        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					        <h4 class="modal-title"><i class="fa fa-file-excel-o"></i>&nbsp&nbspChoose Range of Date</h4>
					      </div>
						  <div class="modal-body">
						  	<h4>Enter a date range </h4>
							<label>Start Date:</label>
							<input class="form-control" type="date" name="txt_start_date" required>
							<label>End Date:</label>
							<input class="form-control" type="date" name="txt_end_date" required>
						  </div>
					      <div class="modal-footer">
					        <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Proceed</button>
							<button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Close</button> 
					      </div>
					    </div>
				    </form>
				</div>
			</div>
			<div class="modal fade" id="mdl_choose_daterange_new_data">
				<div class="modal-dialog" style="width:500px;margin-top:0">
				  	<form method="post" id="frm_choose_daterange_new_data">
					    <div class="modal-content">
					      <div class="modal-header">
					        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					        <h4 class="modal-title"><i class="fa fa-file-excel-o"></i>&nbsp&nbspChoose Range of Date</h4>
					      </div>
						  <div class="modal-body">
						  	<h4>Enter a date range </h4>
							<label>Start Date:</label>
							<input class="form-control" type="date" name="txt_new_start_date" required>
							<label>End Date:</label>
							<input class="form-control" type="date" name="txt_new_end_date" required>
						  </div>
					      <div class="modal-footer">
					        <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Proceed</button>
							<button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Close</button> 
					      </div>
					    </div>
				    </form>
				</div>
			</div>
		</div>
		<div style="height:450px;" id="body-invalid">
			<div class="panel panel-primary" style="border:none;">
				<div class="panel-body">
					<form method="post" id="frm_update_cut_off_data">
						<div class="col-sm-12">
							<div class="btn-group pull-left col-sm-1" style="padding-right:2px;padding-left:2px;">
								<label>Month</label>
								<select class="form-control" style="height:33px;" name="slt_add_data_month" required>
								</select>
							</div>
							<div class="btn-group pull-left col-sm-1" style="padding-right:2px;padding-left:2px;">
								<label>Year</label>
								<select class="form-control" style="height:33px;" name="slt_add_data_year" required>
								</select>
							</div>
							<div class="btn-group pull-left col-sm-2" style="padding-right:2px;padding-left:2px;">
								<label>Start Date</label>
								<input class="form-control" type="date" style="height:33px;" name="txt_start_date" placeholder="Start Date" required>
							</div>
							<div class="btn-group pull-left col-sm-2" style="padding-right:2px;padding-left:2px;">
								<label>End Date</label>
								<input class="form-control" type="date" style="height:33px;" name="txt_end_date" placeholder="Start Date" required>
							</div>
							<div class="btn-group pull-left col-sm-2" style="padding-left:2px;padding-right:2px;">
								<label style="visibility:hidden;">null</label>
								<button type="submit" class="btn btn-success fa fa-send" id="btn_proceed" style="height:33px;width:100%;">
									Proceed
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<script type="text/javascript" src="./dist/js/consolidated_ppc.js"></script>
		<script type="text/javascript">
			load_latest_consolidated_data();
		</script>
	</body>
</html>