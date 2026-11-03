<?php
require_once('./includes.php');
require_once('./ppswarehouse-oop.php');
$dateToday = date('Y-m-d');
?>
<html> 
<head>
<!-- <link href="./css/ppsStyleSheet.css" rel="stylesheet"> -->
<script src="./resin.js?"></script>
<script>
$('document').ready(function(){
	// var oTable = $('#tbl_ResinMain').dataTable({
    //     	"lengthMenu": [[-1], ["All"]],
	// 		order		: [[0,"desc"],[4,"desc"]],
	// 		"initComplete": function(settings, json){
	// 			$('thead tr th').css('background-color','yellow');
	// 			$('#tbl_ResinMain_info').parent().css('font-size','80%');
	// 			$('#tbl_ResinMain_length').empty();
	// 			var html = '<div style="position:absolute;top:0;margin-top:-7px;">';
	// 				html +=	'	<button id="btn_AddResin" type="button" class="btn btn-success fa fa-plus pull-left"> <span>Add Material</span></button>';
	// 				html +=	'	<button id="btn_MatIssuance" type="button" class="btn btn-success fa fa-check-circle pull-left"> <span>Material Issuance</span></button>';
	// 				html +=	'	<button id="btn_ImportCSV" type="button" class="btn btn-success fa fa-upload pull-left"> <span>Import CSV</span></button>';
	// 				html +=	'	<div class="btn-group pull-left">';
	// 				html +=	'		<button type="button" class="btn btn-success dropdown-toggle fa fa-download" data-toggle="dropdown">';
	// 				html +=	'			 <span>Reports</span>';
	// 				html +=	'			<span class="caret"></span>';
	// 				html +=	'		</button>';
	// 				html +=	'		<ul class="dropdown-menu">';
	// 				html +=	'			<li><a href="#" id="btn_ExportToExcel" class="glyphicon glyphicon-export"> Export</a></li>';
	// 				html +=	'			<li><a href="#" id="btn_ExportToExcelSummaryI" class="glyphicon glyphicon-export"> Export - Issuance Summary</a></li>';
	// 				html +=	'			<li><a href="#" id="btn_ExportToExcelSummaryPI" class="glyphicon glyphicon-export"> Export - Received Summary per Invoice</a></li>';
	// 				html +=	'			<li><a href="#" id="btn_ExportToExcelSummaryR" class="glyphicon glyphicon-export"> Export - Received Summary</a></li>';
	// 				html +=	'			<li><a href="#" id="btn_export_month_end_report" class="glyphicon glyphicon-export"> Export - Month-end Report</a></li>';
	// 				html +=	'		</ul>';
	// 				html +=	'	</div>';
	// 				html +=	'	<button id="btn_Transfer_Slip" type="button" class="btn btn-success fa fa-file pull-left" style="margin-left:4px;"> <span>Transfer Slip</span></button>';
	// 				html +=	'</div>';
	// 			$('#tbl_ResinMain_length').append(html);	
	// 		},
	// 		columnDefs			: [
	// 							{"className":"dt-right","targets":[4]},
	// 							{"className":"dt-center","targets":[2]}
	// 		],        
	// });
	
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

	$('#btn_ExportToExcelSummaryPI').click(function(){		
		$('#modal_export_per_invoice').modal('show');
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
		window.location.href = "./addResin.php";
	});


	$("#btn_MatIssuance").click(function(){
		window.location.href = "./PPSMIS_request_module/pps_warehouse/s.php";
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
					window.location.href = "./Resin.php";
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
		window.location.href = "./updateResin.php?id="+id+"&PartNumber="+PartNumber;
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
		window.location.href = "./Resin.php";
	}else{
		alert("Import Cancelled");
		window.location.href = "./Resin.php";
	}
}

function transfer_slip(){
	window.location.href = "Resin_Transfer_Slip.php";
}


function exportToExcelReport() {
	var invoiceno = $("#id_invoiceno").val();
				
	if( invoiceno == '' ) {
		alert("Please select invoice number.");
		
	} else {
		window.location.href = "export_received_report_byinvoice.php?invoiceno="+invoiceno;
	}
}

// 04042024
// display_resin_summary();
// 	function display_resin_summary(){
				
// 		var data = {
// 			'action'		: 'display_resin_summary'
// 		}
		
// 		data = $.param(data);		
// 		$.ajax({
			
// 			type	: "post",
// 			dataType: "json",
// 			data	: data,
// 			url		: "./ppswarehouseHandler.php",
// 			success	: function(data){
// 				console.log(data)
// 				var ctr 					= data['ctr'];			
				
// 				$('#display_resin_summary').empty();
				
// 				var content  = '<table id="tbl_ResinMain" class="table table-striped table-hover table-compact cell-border" width="100%" style="font-size: 9pt; border: solid 1px gray;">';
// 					content += '	<thead>';
// 					content += '		<tr>';
// 					content += '			<th>Classification</th>';
// 					content += '			<th>Allocation</th>';
// 					content += '			<th>Matl. Code</th>';
// 					content += '			<th>Description</th>';
// 					content += '			<th>WHS Stock</th>';
// 					content += '			<th>Remarks</th>';
// 					content += '		</tr>';
// 					content += '	</thead>';
// 					content += '	<tbody>';
// 					content += '	</tbody>';
// 					content += '</table>';
					
// 				$('#display_resin_summary').append(content);
				
// 				for(i=0;i<ctr;i++){
				
// 				// var mp_unit_price_peso 		= parseFloat(data['mp_unit_price_peso'][i]).toFixed(4);
// 				// var mp_unit_price_peso1 	= ReplaceNumberWithCommas(mp_unit_price_peso);
				
// 				var values  = '<tr align="center" id='+data['id'][i]+'>';
// 					values += '		<td>'+data['Classification'][i]+'</td>';
// 					values += '		<td>'+data['ProductLine'][i]+'</td>';
// 					values += '		<td>'+data['PartNumber'][i]+'</td>';
// 					values += '		<td>'+data['MaterialType'][i]+'</td>';
// 					values += '		<td>'+data['EOH'][i]+'</td>';
// 					values += '		<td>'+data['Remarks'][i]+'</td>';
// 					values += '</tr>';
					
// 					$('#tbl_ResinMain tbody').append(values);
// 				}
				
// 				$("#tbl_ResinMain tbody").on('click','tr #btn_edit_details', function(e){
// 					e.preventDefault();
// 					select_mtls_dtls($(this).val());
// 				});


// 				$('#tbl_ResinMain').DataTable({ 
// 					paginate: true, 
// 					aaSorting: [],
// 				 });
					 				 
				
// 			},
// 			error	: function(data){
// 				console.log(data);
// 			}
// 		});
		
// 	}



</script>
</head>

<body>
<div class="panel panel-primary" style="border:none;">
	<div class="panel-body" style="position:absolute;border:none;">
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
	</div>

	<div id="tab_settings" class="container" style="width:95%;">	
	<br>
	<br>
	
	<div class="panel panel-primary">
		<div class="panel-heading">MATERIAL INFORMATION
			<!-- <div class="btn-group pull-right">
				<button type="button" id="id_btn_back_to_dashboard" class="btn btn-danger btn-sm"><i class="fa fa-reply"></i> BACK</button>
			</div> -->
		</div>
		<div class="panel-body">
		
		<div class="btn-group pull-right">
			<button id="btn_AddResin" type="button" class="btn btn-success fa fa-plus pull-left"> <span>Add Material</span></button>
			<button id="btn_MatIssuance" type="button" class="btn btn-success fa fa-check-circle pull-left"> <span>Material Issuance</span></button>
			<div class="btn-group pull-left">
				<button type="button" class="btn btn-success dropdown-toggle fa fa-download" data-toggle="dropdown">
					<span>Reports</span>
					<span class="caret"></span>
				</button>
			<ul class="dropdown-menu">
				<li><a href="#" id="btn_ExportToExcel" class="glyphicon glyphicon-export"> Export</a></li>
				<li><a href="#" id="btn_ExportToExcelSummaryI" class="glyphicon glyphicon-export"> Export - Issuance Summary</a></li>
				<li><a href="#" id="btn_ExportToExcelSummaryPI" class="glyphicon glyphicon-export"> Export - Received Summary per Invoice</a></li>
				<li><a href="#" id="btn_ExportToExcelSummaryR" class="glyphicon glyphicon-export"> Export - Received Summary</a></li>
				<li><a href="#" id="btn_export_month_end_report" class="glyphicon glyphicon-export"> Export - Month-end Report</a></li>
			</ul>
			</div>
			<button id="btn_ImportCSV" type="button" class="btn btn-success fa fa-upload pull-left"> <span>Import CSV</span></button>
			<button id="btn_Transfer_Slip" type="button" class="btn btn-success fa fa-file pull-left" style="margin-left:4px;"> <span>Transfer Slip</span></button>

		</div>

		<div class="tabbable"> 
			<ul class="nav nav-tabs">
				<li class="active" id="tab_f1">
					<a href="#tab1" data-toggle="tab" style="font-size: 14px;">FACTORY 1</a>
				</li>
				<li id="tab_f3">
					<a href="#tab2" data-toggle="tab" style="font-size: 14px;">FACTORY 3</a>
				</li>
			</ul>
					
			<div class="tab-content">
				
				<div class="tab-pane active" id="tab1">
					<div class="container" style="width:100%;">
			
						<div class="row sub_content">
							<br>
							<input type="hidden" id="id_hidden_fac" name="name_hidden_fac" value="fac1"><br>
									
								<div class="container col-sm-12">

								<div class="col-lg-12 col-md-12 col-sm-12">
									<div id="display_resin_summary1">
									</div>
								</div>

								</div>
							
						</div>
						
					</div>
				</div><!-- tab1-->
				
				<div class="tab-pane" id="tab2">
					<div class="container" style="width:100%;">
			
						<div class="row sub_content">
							<br>
							<input type="hidden" id="id_hidden_fac3" name="name_hidden_fac3" value="fac3"><br>
									
								<div class="container col-sm-12">

								<div class="col-lg-12 col-md-12 col-sm-12">
									<div id="display_resin_summary3">
									</div>
								</div>

								</div>
							
						</div>
						
					</div>
				</div><!-- tab1-->
				
			</div><!-- tab content-->

		</div>
		</div>
	</div>
	</div>
</div>

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
			<form action="Resin.php" method="POST" enctype="multipart/form-data">
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
</div>

<div id="modal_export_per_invoice" class="modal fade" tabindex="-1" role="dialog">
	 <div class="modal-dialog">
		<div class="modal-content" style="width:400px">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				  <h4 class="modal-title">SELECT INVOICE NUM.</h4>
			</div>
			<div class="modal-body">
				<select class="form-control" id="id_invoiceno" name="name_invoiceno" required> 
					<option value=''>--Select--</option>
						<?php
							$result = PPSW::getInstance()->select_invoice_num();
								while($row = mysqli_fetch_array($result)) {
									echo '<option value='.$row['InvoiceNo'].'>'.$row['InvoiceNo'].' </option>';
								}					
						?>
				</select>
			</div>
			<div class="modal-footer">
			   <a role="button" class="btn btn-success btn-sm" onClick="exportToExcelReport();" data-placement="bottom" title="Click this to generate excel file." data-original-title="">EXPORT</a>
			   <button type="button" id="id_btnACancel" class="btn btn-danger btn-sm" data-dismiss="modal"> CANCEL</button>
			</div>
		</div>
	</div>
</div>

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
		echo '<a href="Resin.php">BACK</a></center><br>';
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