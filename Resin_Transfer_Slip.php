<?php
require_once('./includes.php');
require_once('./ppswarehouse-oop.php');
$dateToday = date('Y-m-d');
?>
<html>
<title>PPS Database System</title>
<head>
<link href="./css/ppsStyleSheet.css" rel="stylesheet">
<script>
$('document').ready(function(){
	var oTable = $('#tbl_ResinMain').DataTable({
		"ajaxSource"	: "server_side_scripts/transfer_slip.php",
		"aaSorting"		: [ [ 2, "desc" ] ]
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
	
	$('#tbl_ResinMain tbody').on( 'dblclick', 'tr', function () {
		var tbl_data = oTable.row(this).data();
		var id = $(this).find('td:eq(0) input').val();
		window.location.href="./Resin_Update_TransferSlip.php?id="+id;
	});
	
	 $('#tbl_ResinMain tbody').on( 'click', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
            oTable.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
	 });
	
	$('#btn_New_Transfer_Slip').click(function(){ new_transfer_slip(); });
	
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

function new_transfer_slip(){
	window.location.href = "./Resin_New_TransferSlip.php";
}
</script>
</head>
<body>
<div class="panel panel-primary">
	<div class="panel-heading" style="background:none;color:black;height:55px;border:none;">
		<div class="col-sm-12" style="padding-right:0;padding-left:0;margin-bottom:0;">
			<div class="col-sm-8" style="padding-left:2px;">
				<button id="btn_New_Transfer_Slip" type="button" class="btn btn-default fa fa-plus"> New Transfer Slip</button>
				<a href="Resin.php"><button id="" type="button" class="btn btn-default fa fa-arrow-left"> Go Back</button></a>
			</div>
			<div class="col-sm-4" id="cont_table_search" style="padding-right:14px;">
				<h5 style="float:right;border: 2px solid;padding:.5%;color:red;">Note: Double Click Row to Edit</h5>
			</div>
		</div>
	</div>
	<div class="panel-body">
		<?php
			if($_SESSION['date']==""){
				//echo "ginawa nya to!";
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
			
		
		
		<table id="tbl_ResinMain" class="table table-bordered table-condensed table-hover table-striped">
			<thead>
				<tr>					
					<th>Product Line</th>
					<th>Control No</th>
					<th>Date</th>
					<th>Issued By</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
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

</body>
</html>