<html>
	<head>
		<?php
			require_once('./includes.php');
    		echo '<input type="hidden" id="hdn_username" value="'.$_GET['username'].'">';
		?>
	</head>
	<body style="height:auto;overflow-y:auto;">
		<br>

		<div class="panel panel-primary" style="border:none;">
			<div class="panel-body" style="position:absolute;">
				<div>
					<table id="t_production_data" class="nowrap compact" style="display:absolute;">						
						<thead>
							<tr>					
								<th rowspan="2" style="width:24%;vertical-align:middle;background-color:#95dba7;">Allocation</th>
								<th rowspan="2" style="width:12%;vertical-align:middle;background-color:#95dba7;">Matl. Code</th>
								<th rowspan="2" style="width:22%;vertical-align:middle;background-color:#95dba7;">Description</th>
								<th colspan="3" style="width:21%;vertical-align:middle;background-color:#95dba7;">EOH (<span id="previous_data_month_date"></span>)</th>
								<th colspan="3" style="width:21%;vertical-align:middle;background-color:#95dba7;">EOH (<span id="latest_data_month_date"></span>)</th>
							</tr>
							<tr>
								<th style="width:7%;vertical-align:middle;background-color:#95dba7;border-top:none;">Mix</th>
								<th style="width:7%;vertical-align:middle;background-color:#95dba7;border-top:none;">Virgin</th>
								<th style="width:7%;vertical-align:middle;background-color:#95dba7;border-top:none;">Equiv.</th>
								<th style="width:7%;vertical-align:middle;background-color:#95dba7;border-top:none;">Mix</th>
								<th style="width:7%;vertical-align:middle;background-color:#95dba7;border-top:none;">Virgin</th>
								<th style="width:7%;vertical-align:middle;background-color:#95dba7;border-top:none;">Equiv.</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		


		<div class="panel panel-primary" id="panel_edit_production" style="display: none;">
			<div class="panel-heading"><i class="fa fa-edit"></i> Edit Record
		        <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="panel-body">
				<form id="frm_edit_production" method="post">
					<div class="row">
						<div class="form-group col-sm-12">
							<label class="control-label">Material Code</label>
							<input type="text" class="form-control" id="txt_edit_material_code" name="txt_edit_material_code" readonly>
						</div>
						<div class="form-group col-sm-6">
							<label class="control-label">Mix</label>
							<input type="number" step="0.01" class="form-control" id="txt_edit_mix" name="txt_edit_mix" placeholder="0.00">
						</div>
						<div class="form-group col-sm-6">
							<label class="control-label">Virgin</label>
							<input type="number" step="0.01" class="form-control" id="txt_edit_virgin" name="txt_edit_virgin" placeholder="0.00">
						</div>
					</div>
					<input type="hidden" id="txt_pkid" name="txt_pkid">
			</div>
			<div class="panel-footer text-right">
				<button type="button" class="btn btn-default btn_close">Close</button>
				<button type="submit" class="btn btn-success">Save</button>
			</div>
				</form>
		</div>


		<div class="modal fade" id="mdl_import_data">
			<div class="modal-dialog" style="width:500px;">
			  	<form method="post" id="frm_import_data">
				    <div class="modal-content">
				      <div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				        <h4 class="modal-title"><i class="fa fa-file-excel-o"></i>&nbsp&nbspImport MRP Data</h4>
				      </div>
				      <div class="modal-body" style="overflow-y:auto;padding-top:20px;padding-bottom:20px;">
						<div class="form-group">			
							<div class="col-sm-12">
						    	<label>Month & Year</label>
								<input type="text" name="txt_month_year" class="form-control monthPicker" autocomplete="off" required>
							</div>		
							<div class="col-sm-12" style="margin-top:10px;">
						    	<label>Inventory File</label>
								<input type="file" name="txt_inventory_file" class="form-control pointer" required>
							</div>
						</div>
					  </div>
				      <div class="modal-footer">
				        <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Import Data</button>
						<button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Close</button> 
				      </div>
				    </div>
			    </form>
			</div>
		</div>
		<script type="text/javascript" src="./dist/js/functions.js"></script>
		<script type="text/javascript" src="./dist/js/Production.js"></script>
	</body>
</html>