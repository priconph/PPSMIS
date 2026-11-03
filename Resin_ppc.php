<?php
require_once('./includes.php');
require_once('./ppswarehouse-oop.php');
$dateToday = date('Y-m-d');
?>
<html>
<head>
<!--<link href="./css/ppsStyleSheet.css" rel="stylesheet">-->
<script>
$('document').ready(function(){
	var oTable = $('#tbl_ResinMain').dataTable({
			"autoWidth"	: false,
			paginate	: true,
        	"lengthMenu": [[-1], ["All"]],
			"scrollY"  	: "70vh",
			"scrollX"  	: "80%",
			order		: [[0,"desc"],[4,"desc"]],
			"initComplete": function(settings, json){
				$('thead tr th').css('background-color','yellow');
				$('#tbl_ResinMain_info').parent().css('font-size','80%');
				$('#tbl_ResinMain_length').empty();
				var html = '<div style="position:absolute;top:0;margin-top:-7px;">';
					html +=	'	<button id="btn_AddResin" type="button" class="btn btn-success fa fa-plus pull-left"> <span>Add Material</span></button>';
					html +=	'	<button id="btn_ImportCSV" type="button" class="btn btn-success fa fa-upload pull-left"> <span>Import CSV</span></button>';
					html +=	'	<div class="btn-group pull-left">';
					html +=	'		<button type="button" class="btn btn-success dropdown-toggle fa fa-download" data-toggle="dropdown">';
					html +=	'			 <span>Reports</span>';
					html +=	'			<span class="caret"></span>';
					html +=	'		</button>';
					html +=	'		<ul class="dropdown-menu">';
					html +=	'			<li><a href="#" id="btn_ExportToPDF" class="glyphicon glyphicon-export"> Export (PDF)</a></li>';
					html +=	'			<li><a href="#" id="btn_ExportToExcel" class="glyphicon glyphicon-export"> Export (Excel)</a></li>';
					html +=	'			<li><a href="#" id="btn_ExportToExcelSummaryI" class="glyphicon glyphicon-export"> Export - Issuance Summary (Excel)</a></li>';
					html +=	'			<li><a href="#" id="btn_ExportToExcelSummaryR" class="glyphicon glyphicon-export"> Export - Received Summary (Excel)</a></li>';
					html +=	'			<li><a href="#" id="btn_export_month_end_report" class="glyphicon glyphicon-export"> Export - Month-end Report (Excel)</a></li>';
					html +=	'		</ul>';
					html +=	'	</div>';
					html +=	'	<button id="btn_Transfer_Slip" type="button" class="btn btn-success fa fa-file pull-left" style="margin-left:4px;"> <span>Transfer Slip</span></button>';
					html +=	'</div>';
				$('#tbl_ResinMain_length').append(html);	
			},
			columnDefs			: [
								{"className":"dt-right","targets":[4]},
								{"className":"dt-center","targets":[2]}
			],        
	});

    // $('#tbl_ResinMain_filter').appendTo('#cont_table_search');
    // $('#tbl_ResinMain_filter').css('margin-right','0');
    // $('#tbl_ResinMain_filter').css('float','right');
    // $('#tbl_ResinMain_filter').css('text-align','right');
	
	$('#btn_ExportToPDF').click(function(){
		window.location.href = "./ExportResin-PDF.php";
	});
	
	$("#btn_ExportToExcel").click(function(){
		window.location.href = "./export_actual_balance.php";
	});
	
		
	$("#btn_ExportToExcelSummaryI").click(function(){
		$('#modal_export_summary_report').modal('show');
		$('#frm-select-date-range').data('id','I');
	});
	
	$("#btn_ExportToExcelSummaryR").click(function(){
		$('#modal_export_summary_report').modal('show');
		$('#frm-select-date-range').data('id','R');
	});

	$('#btn_export_month_end_report').click(function(){		
		$('#mdl_export_month_end_report').modal('show');
	});

	$('#mdl_export_month_end_report').submit(function(e){
		e.preventDefault();
		window.location.href = "./export_month_end_report.php?start_date="+$('input[name=txt_esr_date_start]').val()+"&end_date="+$('input[name=txt_esr_date_end]').val();
	});

	$('#frm-select-date-range').submit(function(e){
		e.preventDefault();
		if ( $(this).data('id') == 'I' ){
			window.location.href = "./export_issued_summary.php?start_date="+ $('#txt_date_start').val() +'&end_date=' + $('#txt_date_end').val();
		}
		if ( $(this).data('id') == 'R' ){
			window.location.href = "./export_received_summary.php?start_date="+ $('#txt_date_start').val() +'&end_date=' + $('#txt_date_end').val();
		}		
	});
	
	$("#btn_AddResin").click(function(){
		window.location.href = "./addResin_ppc.php";
	});
	
	var iPos,x,idGlobal,idForDelete;
	$("#tbl_ResinMain tbody tr").click(function(){
		idForDelete = $(this).find(".PartNumber").text();
		iPos = oTable.fnGetPosition( this );
		if ( $(this).hasClass('selected') ) {
			$(this).removeClass('selected');
		}
		else {
			oTable.$('tr.selected').removeClass('selected');
			$(this).addClass('selected');
		}
	});
	
	$('#btn_Delete').click(function(){
		if(confirm('Warning! All information about this product will be deleted! Proceed?')){
			var data = {
				"action" : "deleteAllProductInfo",
				"PartNumber" : idForDelete
			}
			data = $.param(data);
			$.ajax({
				type 		: "post",
				dataType 	: "json",
				data 		: data,
				url 		: "./ppswarehouseHandler.php",
				success : function(data){
					alert("Data Deleted!");
					window.location.href = "./Resin_ppc.php";
				}
			});			
		}else{
			alert("Delete Cancelled!");
		}
	});
		
	//START DATE VALIDATION
	$('#txtStartDate').change(function(){
		var startDate = $('#txtStartDate').val();
		var endDate = $('#txtEndDate').val();
			if (startDate>endDate){
				$('#txtStartDate').val(endDate);  
			}
	});
	//END DATE VALIDATION
	$('#txtEndDate').change(function(){
		var startDate = $('#txtStartDate').val();
		var endDate = $('#txtEndDate').val();
			if (endDate<startDate){
				$('#txtEndDate').val(startDate);  
			}
	});
	
	$("#tbl_ResinMain tr").dblclick(function(){
		var id = $(this).find('.id').text();
		var PartNumber = $(this).find('.PartNumber').text();
		window.location.href = "./updateResin_ppc.php?id="+id+"&PartNumber="+PartNumber;
	});
	
	$('#btn_ImportCSV').click(function(){ $("#ModalImportResinCSV").modal("show"); });
	$('#btn_ImportWarehouseInfo').click(function(){ importMaterials(); });
	$('#btn_Transfer_Slip').click(function(){ transfer_slip(); });	
});

var row = 0;
function importMaterials(){
	if(confirm("Import these data?")){
	$('#tbl_ImportWarehouseData tr').each(function(row, tr){	//loop to each tr 
		if(row>0){ 	//REMOVE ROW 0 WHICH IS A LABEL
			//GET VALUES HERE
			var data = {
				"action"		: "ImportWarehouseData",
				"ProductLine"	: $(tr).find('td:eq(0)').text(),
				"PartNumber"	: $(tr).find('td:eq(1)').text(),
				"MaterialType"	: $.trim($(tr).find('td:eq(2)').text()),
				"Supplier"		: $(tr).find('td:eq(3)').text(),
				"Model"			: $(tr).find('td:eq(4)').text(),
				"LastUpdate"	: "<?php echo $dateToday;?>",
				"Username"		: "<?php echo $username;?>"
			}
			data = $.param(data);
			$.ajax({
				type	: "post",
				dataType: "json",
				data	: data,
				url		: "./ppswarehouseHandler.php",
				success	: function(data){
					
				}
			});
		}
		row++;			
	});
		alert("Material Import Success!");
		window.location.href = "./Resin_ppc.php";
	}else{
		alert("Import Cancelled");
		window.location.href = "./Resin_ppc.php";
	}
}

function transfer_slip(){
	window.location.href = "Resin_Transfer_Slip_ppc.php";
}
</script>
</head>

<body style="height:auto;">
<div class="panel panel-primary" style="border:none;box-shadow: none;">
	<div class="panel-heading" style="background:none;color:black;height:55px;border:none;border-shadow:none;">
		<div class="col-sm-12" style="padding-right:0;padding-left:0;margin-bottom:0;">
			<div class="col-sm-12" style="padding-left:0;color:green;margin-bottom:10px;">
				<ul class="nav nav-pills">
					<li><a href="./Consolidated_ppc.php?username=<?php echo $_GET['username'];?>">Consolidated Data</a></li>
					<li class="active"><a data-toggle="tab" href="#">Warehouse Data</a></li>
					<li><a href="./Production_ppc.php?username=<?php echo $_GET['username'];?>">Production Data</a></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="panel-body" style="position:absolute;">
		<?php
			if($_SESSION['date']==""){
				if(isset($_POST['txtStartDate'])){
					$_SESSION['date'] = $_POST['txtStartDate'];
					$_SESSION['date_out'] = $_POST['txtEndDate'];
				}else{
					$_SESSION['date'] = $dateToday;
					$_SESSION['date_out'] = $dateToday;
				}
			}else{
				if($_POST['txtStartDate']!=""){
				$_SESSION['date'] = $_POST['txtStartDate'];
				$_SESSION['date_out'] = $_POST['txtEndDate'];
				}else{
					
				}
			}
		?>		
		<div>
			<table id="tbl_ResinMain" class="nowrap compact" style="display:absolute;">	
				<thead>
					<tr>			
						<th>Classification</th>			
						<th>Allocation</th>
						<th>Matl. Code</th>
						<th>Description</th>
						<th>WHS Stock</th>
						<th>Remarks</th>
						<th class="id" style="display:none;">ID</th>  <!-- This is needed, B. BTW, not my idea.-->
					</tr>
				</thead>
				<tbody>
					<?php
						$result = PPSW::getInstance()->getResinInfoMain();
						$total_balance = 0;
						while ($row = mysqli_fetch_array($result)){
							echo "<tr>";
							echo "	<td>".$row["Classification"]."</td>";
							echo "	<td>".$row["ProductLine"]."</td>";
							echo "	<td class='PartNumber'>".$row["PartNumber"]."</td>";
							echo "	<td>".$row["MaterialType"]."</td>";
							echo "	<td>".number_format($row["Balance"], 2, '.', ',')."</td>";
							echo "	<td>".$row["Remarks"]."</td>";
							echo "	<td class='id' style='display:none;'>".$row["id"]."</td>";
							echo "</tr>";
							$total_balance = $total_balance+$row["Balance"];
						}
					?>
				</tbody>
				<tfoot>
					<tr>			
						<th colspan="4" style="text-align:right;font-size:90%;">TOTAL</th>
						<th style="text-align:right;font-size:90%;background-color:green;color:white;"><?php echo number_format($total_balance, 2, '.', ',');?></th>
						<th></th>
						<th style="display:none;"></th>  <!-- This is needed, B. BTW, not my idea.-->
					</tr>
				</thead>
			</table>
		</div>
	</div>
</div>

<!--Modal-->
<div id="modalNameHere" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ModalDeleteLabel" aria-hidden="true" style="overflow:inherit">
	 <div class="modal-dialog">
		<div class="modal-content" style="width:400px">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				  <h4 class="modal-title" id="ModalDeleteLabel">TITLE</h4>
			</div>
			<div class="modal-body">
				<p>CONTENT</p>
			</div>
			<div class="modal-footer">
			   <a href="#" id="btnYes" class="btn btn-success">YES</a>
			   <a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-danger">NO</a>
			</div>
		</div>
	</div>
</div>

<div id="ModalImportResinCSV" class="modal fade" tabindex="-1" style="overflow:scroll;" role="dialog" aria-labelledby="ModalImportResinCSV" aria-hidden="true" style="overflow:inherit">
	 <div class="modal-dialog">
		<div class="modal-content" style="width:600">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				  <h4 class="modal-title">Upload CSV File</h4>
			</div>
			<form action="Resin_ppc.php" method="POST" enctype="multipart/form-data">
				<div class="modal-body">
						<p><input type="file" name="file" id="file"></p>
				</div>
				<div class="modal-footer">
				   <input type="submit" class="btn btn-success" value="SUBMIT">
				   <a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-danger">CLOSE</a>
				</div>
			</form>
			<?php
			if(isset($_FILES['file']['name'])) {
				appendTableStart();
			}
			?>
		</div>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal_export_summary_report">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Export Summary Report</h4>
      </div>
      <div class="modal-body">
        <form id="frm-select-date-range" data-id="0">
			<h4>Enter a date range </h4>
			<label>Start Date:</label>
			<input class="form-control" type="date" id="txt_date_start" name="txt_date_start" required>
			<label>End Date:</label>
			<input class="form-control" type="date" id="txt_date_end" name="txt_date_end" required>
		
      </div>
      <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			<button type="submit" class="btn btn-success" id="export_report_summary">Export</button>
		</form>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="modal fade" tabindex="-1" role="dialog" id="mdl_export_month_end_report">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Export Summary Report</h4>
      </div>
      <div class="modal-body">
        <form id="frm_export_month_end_report" data-id="0">
			<h4>Enter a date range </h4>
			<label>Start Date:</label>
			<input class="form-control" type="date" name="txt_esr_date_start" required>
			<label>End Date:</label>
			<input class="form-control" type="date" name="txt_esr_date_end" required>
		
      </div>
      <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			<button type="submit" class="btn btn-success" id="export_report_summary">Export</button>
		</form>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
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
		echo '<a href="Resin_ppc.php">BACK</a></center><br>';
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
						//echo "<td>".$dateAppend."</td>";
						echo "<td>".$slice[0]."</td>";
						echo "<td>".$slice[1]."</td>";
						echo "<td>".$slice[2]."</td>";
						echo "<td>".$slice[3]."</td>";
						echo "<td>".$slice[4]."</td>";
						//echo "<td><a href='javascript:void(0);' class='remCF'>Remove</a></td>";
						echo "</tr>";
					}
				}
			}
		}
		echo "<script> $('#ModalImportResinCSV').modal('show'); </script>";
	}	
	}
}

function appendTableStart(){
	echo '<button class="btn btn-primary" id="btn_ImportWarehouseInfo" style="float:right;">IMPORT</button>';
	echo '<table id="tbl_ImportWarehouseData" class="table">
				<thead>
					<tr>
						<th>Product Line</th>
						<th>Part Number</th>
						<th>Material Type</th>
						<th>Supplier</th>
						<th>Model</th>
					</tr>
				</thead>
				<tbody>';
				 appendData();
				
	echo '</tbody>
		</table>';
}
?>
</body>
</html>