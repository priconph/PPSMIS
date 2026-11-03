<html>
	<head>
		<?php
			require_once('./includes.php');
    		echo '<input type="hidden" id="hdn_username" value="'.$_GET['username'].'">';
		?>
	</head>
	<body style="height:700px;overflow-y:auto;">
		<div class="panel-heading" style="background:none;color:black;height:55px;border:none;">
			<div class="col-sm-12" style="padding-right:0;padding-left:0;margin-bottom:0;">
				<div class="col-sm-12" style="padding-left:0;color:green;margin-bottom:10px;">
					<ul class="nav nav-pills">
						<li><a href="./Consolidated_ppc.php?username=<?php echo $_GET['username'];?>">Consolidated Data</a></li>
						<li><a href="./Resin_ppc.php?username=<?php echo $_GET['username'];?>">Warehouse Data</a></li>
						<li class="active"><a data-toggle="tab" href="#">Production Data</a></li>
					</ul>
				</div>
				<div class="col-sm-8" style="padding-left:0">
					<!-- <div class="btn-group pull-left">
						<button type="button" class="btn btn-default dropdown-toggle fa fa-download" data-toggle="dropdown">
							Reports
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							<li><a href="#" id="btn_ExportToPDF" class="glyphicon glyphicon-export"> Export (PDF)</a></li>
							<li><a href="#" id="btn_ExportToExcel" class="glyphicon glyphicon-export"> Export (Excel)</a></li>
						</ul>
					</div> -->
				</div>
				<div class="col-sm-4" id="cont_table_search" style="padding-right:14px;padding-top:9px;">
				</div>
			</div>
		</div>
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