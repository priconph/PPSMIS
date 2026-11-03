var handler 	= "./dist/handler/handler.php";
var username 	= $('#hdn_username').val();
var dvsion 		= $('#hdn_user_division').val();
var export_production_report_href = '#';
var dt_t_production_data = '';
// alert(username);
$(document).ready(function(){
	var data = {
		'action'	: 'load_latest_data'
	}
	data = $.param(data);
	$.ajax({
		type	: 'post',
		dataType: 'json',
		data	: data,
		url		: handler,
		success : function(data){
			const month_names = ["January", "February", "March", "April", "May", "June","July", "August", "September", "October", "November", "December"];

			if(data['max_month']==undefined){
				$('#latest_data_month_date').text('NO DATA');
			}
			else{
				$('#latest_data_month_date').text(month_names[data['max_month']-1]+' '+data['max_year']);
			}

			if(data['prev_max_month']==undefined){
				$('#previous_data_month_date').text('NO DATA');
			}
			else{
				$('#previous_data_month_date').text(month_names[data['prev_max_month']-1]+' '+data['prev_max_year']);
			}
			export_production_report_href = 'production_summary.php?max_month='+data['max_month']+'&&max_year='+data['max_year']+'&&prev_month='+data['prev_max_month']+'&&prev_year'+data['prev_max_year'];
			console.log('./dist/server-side/production_data.table.php?max_month='+data['max_month']+'&&max_year='+data['max_year']+'&&prev_month='+data['prev_max_month']+'&&prev_year'+data['prev_max_year']);
			var t_production_data = $('#t_production_data').dataTable({
					ajax 		: './dist/server-side/production_data.table.php?max_month='+data['max_month']+'&&max_year='+data['max_year']+'&&prev_month='+data['prev_max_month']+'&&prev_year'+data['prev_max_year'], 
					// paginate	: false,
					"autoWidth"	: false,
					paginate	: true,
					"scrollY"  	: "70vh",
        			"lengthMenu": [[-1], ["All"]],
					order		: [1,"asc"],
					"initComplete": function(settings, json){
						$('thead tr th').css('background-color','yellow');
						$('#t_production_data_info').parent().css('font-size','80%');
						$('#t_production_data_length').empty();
						var html = '<div style="position:absolute;top:0;margin-top:-7px;">';
						html += '<button id="btn_import_prod_data" type="button" class="btn btn-success fa fa-upload pull-left"> <span>Update Data - Import Excel</span></button>';
						html +=	'	<div class="btn-group pull-left">';
						html +=	'		<button type="button" class="btn btn-success dropdown-toggle fa fa-download" data-toggle="dropdown">';
						html +=	'			 <span>Reports</span>';
						html +=	'			<span class="caret"></span>';
						html +=	'		</button>';
						html +=	'		<ul class="dropdown-menu">';
						html +=	'			<li><a href="#" id="btn_ExportToExcel" class="glyphicon glyphicon-export"> Export (Excel)</a></li>';
						html +=	'		</ul>';
						html +=	'	</div>';
						html +=	'</div>';

						$('#t_production_data_length').append(html);						
						$('#btn_import_prod_data').click(function(){
							$('#mdl_import_data').modal('show');
						});
					}
			});

			$('#t_production_data tbody').on( 'click', 'tr', function () {
		        if($(this).hasClass('selected')){
		            $(this).removeClass('selected');
		        }
		        else{
		            t_production_data.$('tr.selected').removeClass('selected');
		            $(this).addClass('selected');
		        }
		    } );
		},
		error	: function(data){
			alert('ajax error on load_latest_data');
		}
	});	

	$('div').on('click', 'a#btn_ExportToPDF',function(){
		// window.location.href = "ExportResin-PDF.php";
	});
	
	$("div").on('click', 'a#btn_ExportToExcel',function(){
		window.location.href = export_production_report_href;//"production_summary.php";
	});

    $('#t_production_data_filter').css('margin-right','13px');


	$('#frm_import_data').submit(function(e){
		e.preventDefault();
		var r = confirm("Are you sure you want to proceed?");
		if(r==true){
			var serialized_data = new FormData(this);
			serialized_data.append("action","import_production_monthly_data");
			serialized_data.append("username",username);
			$.ajax({
				url				: handler, 		
				type			: "POST",          
				data			: serialized_data, 
				contentType		: false,       
				dataType		: 'json',
				cache			: false,             
				processData		: false,
				success	: function(data){
					console.log(data);
					alert('Successfully Imported!');
					// location.reload();
				}, 
				error : function(data){
					console.log(data);
					alert('ajax error on save_import_assessment_data');
				}
			}); 
		}
	});



    $('#t_production_data tbody').on('dblclick', 'tr', function () {
    	$(this).addClass('selected');
    	clear_edit_form();
    	dt_t_production_data = $('#t_production_data').DataTable();
        var data = dt_t_production_data.row( this ).data();
		var txt_edit_material_code = data[1].split("</span>").pop();
		var txt_edit_mix = data[6];
		var txt_edit_virgin = data[7];
		var row_pkid = $(this).find('.row_pkid').text();

        $('#txt_edit_material_code').val( txt_edit_material_code );
        $('#txt_edit_mix').val( txt_edit_mix.replace(',','') );
        $('#txt_edit_virgin').val( txt_edit_virgin.replace(',','') );
        $('#txt_pkid').val(row_pkid);

		var position = $(this).offset();
        $('#panel_edit_production').css('left', parseInt(position.left)+parseInt($(this).css('width')));
        $('#panel_edit_production').css('top', position.top);
        $('#panel_edit_production').show("slide", {direction: "left"}, 100);
    } );
    $('#panel_edit_production .close, #panel_edit_production .btn_close').click(function(){
    	clear_edit_form();
    	$(this).closest('.panel').hide("slide", {direction: "left"}, 100);
    });
	$('#frm_edit_production').submit(function(e){
		var thiss = $(this);
		e.preventDefault();
		var serialized_data = new FormData(this);
		serialized_data.append("action","update_production");
		serialized_data.append("username",username);
		$.ajax({
			url				: handler,
			type			: "POST",
			data			: serialized_data, 
			contentType		: false,
			dataType		: 'json',
			cache			: false,
			processData		: false,
			success	: function(data){
				console.log(data);
				$(thiss).closest('.panel').hide("slide", {direction: "left"}, 100);
				clear_edit_form();
				alert('Successfully Updated!');
				if(dt_t_production_data){dt_t_production_data.ajax.reload();}
			}, 
			error : function(data){
				console.log(data);
				alert('ajax error on update production');
			}
		}); 
	});


});



function load_user_role(user_id){
	var data = {
		'action'	: 'load_user_role',
		'id'		: user_id
	}
	data = $.param(data);
	$.ajax({
		type	: 'post',
		dataType: 'json',
		data	: data,
		url		: handler,
		success : function(data){

		},
		error	: function(data){
			alert('ajax error on load_user_role');
		}
	});	
}

function clear_edit_form(){
    $('#txt_edit_material_code').val('');
    $('#txt_edit_mix').val('');
    $('#txt_edit_virgin').val('');
    $('#txt_pkid').val('');
}