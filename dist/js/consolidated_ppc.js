var handler 	= "./dist/handler/handler.php";
var username 	= $('#hdn_username').val();
var dvsion 		= $('#hdn_user_division').val();

$(document).ready(function(){
	$('#t_consolidated_ppc tbody').on('dblclick','tr',function(){
        $(this).addClass('selected');
		var material_code	= $(this).find('td:eq(1)').text();
		var data_month 		= $('select[name=slt_data_month]').val();
		var data_year 		= $('select[name=slt_data_year]').val();
		var data = {
			"action"		: "load_prices",
			"material_code"	: material_code,
			"data_month"	: data_month,
			"data_year"		: data_year
		}
		data = $.param(data);
		$.ajax({
			type 	: "post",
			dataType: "json",
			data 	: data,
			url 	: handler,
			success : function(data){
				$('input[name=txt_allocation]').val(data['ProductLine'][0]);
				$('input[name=txt_material_code]').val(data['PartNumber'][0]);
				$('input[name=txt_description]').val(data['MaterialType'][0]);
				$('input[name=txt_whs_id]').val(data['id'][0]);

				$('#content_loop').empty();
				var j = 1;
				for(var i=0;i<data['ctr'];i++){
				var html  = '<div class="col-md-12" style="text-align:center;display:inline;" id="'+data['mc_id'][i]+'">';
					html += '	<div class="col-md-9" style="padding:1px;">';
					html += '		<div class="col-md-12" style="padding:0;margin:0;">';
					html += '			<div class="col-md-3" style="padding:0;margin:0;">';					
					html += '				<div class="col-md-6" style="padding:1px;">';
					html += '					<input class="form-control" value="'+data['mc_id'][i]+'" type="hidden" name="txt_mc_id_'+j+'" style="text-align:right;">';
					html += '					<input class="form-control" value="'+data['mc_boh_price'][i]+'" type="text" name="txt_boh_price_'+j+'" style="text-align:right;border: 1px solid #3f85f4;border-radius:2px;">';
					html += '				</div>';
					html += '				<div class="col-md-6" style="padding:1px;">';
					html += '					<input class="form-control" value="'+data['mc_received_price'][i]+'" type="text" name="txt_received_price_'+j+'" style="text-align:right;border: 1px solid #3f85f4;border-radius:2px;">';
					html += '				</div>';
					html += '			</div>';
					html += '			<div class="col-md-3" style="padding:0;margin:0;">';
					html += '				<div class="col-md-6" style="padding:1px;">';
					html += '					<input class="form-control" value="'+data['mc_eoh_price'][i]+'" type="text" name="txt_eoh_price_'+j+'" style="text-align:right;border: 1px solid #3f85f4;border-radius:2px;">';
					html += '				</div>';
					html += '				<div class="col-md-6" style="padding:1px;">';
					html += '					<input class="form-control" value="'+data['wmid_whs_balance_amount'][i]+'" type="text" name="txt_whs_remaining_'+j+'" style="text-align:right;border: 1px solid green;border-radius:2px;">';
					html += '				</div>';
					html += '			</div>';
					html += '			<div class="col-md-3" style="padding:0;margin:0;">';
					html += '				<div class="col-md-6" style="padding:1px;">';
					html += '					<input class="form-control" value="'+data['wmid_prodn_balance_amount'][i]+'" type="text" name="txt_prodn_remaining_'+j+'" style="text-align:right;border: 1px solid green;border-radius:2px;">';
					html += '				</div>';
					html += '				<div class="col-md-6" style="padding:1px;">';
					// html += '					<input class="form-control" value="'+data['mc_received_quantity'][i]+'" type="text" name="txt_received_qty_'+j+'" style="text-align:right;border: 1px solid green;border-radius:2px;">';
					html += '				</div>';
					html += '			</div>';
					html += '			<div class="col-md-3" style="padding:0;margin:0;">';
					html += '				<div class="col-md-6" style="padding:1px;">';
					html += '					<input class="form-control" value="'+data['wmid_ppc_adjustment'][i]+'" type="text" name="txt_adjustment_'+j+'" style="text-align:right;border: 1px solid red;border-radius:2px;">';
					html += '				</div>';
					html += '				<div class="col-md-6" style="padding:1px;">';
					// html += '					<input class="form-control" value="'+data['mc_used_qty'][i]+'" type="text" name="txt_used_qty_'+j+'" style="text-align:right;border: 1px solid red;border-radius:2px;">';
					html += '				</div>';
					html += '			</div>';
					html += '		</div>';
					html += '	</div>';
					html += '	<div class="col-md-2" style="padding:2px;">';
					html += '		<input class="form-control" value="'+moment(data['mc_adate'][i]).format('MMM DD, YYYY HH:mm')+'" type="text" name="txt_adate_'+j+'" readonly>';
					html += '	</div>';
					html += '	<div class="col-md-1" style="padding:2px;">';
					html += '		<a class="a_existing_remove_row"><i class="fa fa-close pointer" style="margin-top:22%;"></i></a>';
					html += '	</div>';
					html += '</div>';
					$('#content_loop').append(html);
					j++;
				}
				$('input[name=txt_max_row]').val(j-1);
				$('#mdl_price_allocation').modal('show');
			},
			error 	: function(data){
				alert('ISS:Error on load_prices!');
				// alert('Error encountered. Please immediately contact any of our IT Specialists. Thank you!');
			}
		});
	});

	$('#a_add_row').click(function(){
		var i = $('input[name=txt_add_max_row]').val();
		i++;
		var html  = '<div class="col-md-12" style="text-align:center;display:inline;">';
			html += '	<div class="col-md-9" style="padding:1px;">';
			html += '		<div class="col-md-12" style="padding:0;margin:0;">';
			html += '			<div class="col-md-3" style="padding:0;margin:0;">';					
			html += '				<div class="col-md-6" style="padding:1px;">';
			html += '					<input class="form-control" name="txt_adn_boh_price_'+i+'" style="text-align:right;border: 1px solid #3f85f4;border-radius:2px;">';
			html += '				</div>';
			html += '				<div class="col-md-6" style="padding:1px;">';
			html += '					<input class="form-control" name="txt_adn_received_price_'+i+'" style="text-align:right;border: 1px solid #3f85f4;border-radius:2px;">';
			html += '				</div>';
			html += '			</div>';
			html += '			<div class="col-md-3" style="padding:0;margin:0;">';
			html += '				<div class="col-md-6" style="padding:1px;">';
			html += '					<input class="form-control" name="txt_adn_eoh_price_'+i+'" style="text-align:right;border: 1px solid #3f85f4;border-radius:2px;">';
			html += '				</div>';
			html += '				<div class="col-md-6" style="padding:1px;">';
			html += '					<input class="form-control" name="txt_adn_whs_remaining_'+i+'" style="text-align:right;border: 1px solid green;border-radius:2px;">';
			html += '				</div>';
			html += '			</div>';
			html += '			<div class="col-md-3" style="padding:0;margin:0;">';
			html += '				<div class="col-md-6" style="padding:1px;">';
			html += '					<input class="form-control" name="txt_adn_prodn_remaining_'+i+'" style="text-align:right;border: 1px solid green;border-radius:2px;">';
			html += '				</div>';
			html += '				<div class="col-md-6" style="padding:1px;">';
			html += '					<input class="form-control" e="txt_adn_received_qty_'+i+'" style="text-align:right;border: 1px solid green;border-radius:2px;">';
			html += '				</div>';
			html += '			</div>';
			html += '			<div class="col-md-3" style="padding:0;margin:0;">';
			html += '				<div class="col-md-6" style="padding:1px;">';
			html += '					<input class="form-control" name="txt_adjustment_'+i+'" style="text-align:right;border: 1px solid red;border-radius:2px;">';
			html += '				</div>';
			html += '				<div class="col-md-6" style="padding:1px;">';
			html += '					<input class="form-control" name="txt_used_qty_'+i+'" style="text-align:right;border: 1px solid red;border-radius:2px;">';
			html += '				</div>';
			html += '			</div>';
			html += '		</div>';
			html += '	</div>';
			html += '	<div class="col-md-2" style="padding:2px;">';
			html += '		<input class="form-control" value="'+moment().format('MMM DD, YYYY HH:mm')+'" type="text" name="txt_adn_add_adate_'+i+'" readonly>';
			html += '	</div>';
			html += '	<div class="col-md-1" style="padding:2px;">';
			html += '		<a class="a_remove_row"><i class="fa fa-close pointer" style="margin-top:22%;"></i></a>';
			html += '	</div>';
			html += '</div>';
		$('input[name=txt_add_max_row]').val(i);
		$('#content_loop').append(html);
	});

	$("#content_loop").on('click', '.a_remove_row', function(){
		var i = $('input[name=txt_add_max_row]').val();
        $(this).parent().parent().remove();
        i--;
        $('input[name=txt_add_max_row]').val(i);
    });

	$("#content_loop").on('click', '.a_existing_remove_row', function(){
		var confirmation = confirm("Are you sure you want to proceed?");
		if(confirmation==true){
	        mc_id = $(this).parent().parent().attr('id');
			$(this).parent().parent().remove();
			var data = {
				"action"		: "delete_price",
				"mc_id"			: mc_id
			}
			data = $.param(data);
			$.ajax({
				type 	: "post",
				dataType: "json",
				data 	: data,
				url 	: handler,
				success : function(data){
					var remaining_row = ($('input[name=txt_max_row]').val()-1);
					fn_destroy_table('t_consolidated_ppc');					
					load_consolidated_data_table($('select[name=slt_data_month]').val(),$('select[name=slt_data_year]').val());
					alert('Successfully removed!');
					$('input[name=txt_max_row]').val(remaining_row);
					$('#mdl_price_allocation').modal('hide');
				},
				error 	: function(data){
					alert('ISS:Error on delete_price!');
				}
			});

		};
    });

	$('#btn_export_report').click(function(){
		var data_month = $('select[name=slt_data_month]').val();
		var data_year = $('select[name=slt_data_year]').val();
		var r = confirm("Are you sure you want to proceed?");
		if(r==true){
			window.location.href = './export_materials_inventory_and_consumption_report.php?data_month='+data_month+'&data_year='+data_year;
		}
	});

	$('#btn_add_price').click(function(){
		$('#mdl_choose_material').modal('show');
	});

	$('#frm_choose_material').submit(function(e){
		e.preventDefault();
		var r = confirm("Are you sure you want to proceed?");
		if(r==true){
			$('#mdl_choose_material').modal('hide');
			var material_code	= $('input[name=txt_cm_material_code]').val();
			// alert(material_code);
			var data = {
				"action"		: "load_prices",
				"material_code"	: material_code
			}
			data = $.param(data);
			$.ajax({
				type 	: "post",
				dataType: "json",
				data 	: data,
				url 	: handler,
				success : function(data){
					// console.log(data['sd']);
					if(data['ctr']=='0'){
						$('input[name=txt_allocation]').val(data['ProductLine']);
						$('input[name=txt_material_code]').val(data['PartNumber']);
						$('input[name=txt_description]').val(data['MaterialType']);
						$('input[name=txt_whs_id]').val(data['id']);
						$('#content_loop').empty();
					}
					else{
						$('input[name=txt_allocation]').val(data['mc_allocation'][0]);
						$('input[name=txt_material_code]').val(data['mc_material_code'][0]);
						$('input[name=txt_description]').val(data['mc_description'][0]);
						$('input[name=txt_whs_id]').val(data['tbl_whs_id'][0]);

						$('#content_loop').empty();
						var j = 1;
						for(var i=0;i<data['ctr'];i++){
							var html  = '<div class="col-md-12" style="text-align:center;display:inline;">';
							html += '	<div class="col-md-9" style="padding:1px;">';
							html += '		<div class="col-md-12" style="padding:0;margin:0;">';
							html += '			<div class="col-md-3" style="padding:0;margin:0;">';					
							html += '				<div class="col-md-6" style="padding:1px;">';
							html += '					<input class="form-control" value="'+data['mc_id'][i]+'" type="hidden" name="txt_mc_id_'+j+'" style="text-align:right;">';
							html += '					<input class="form-control" value="'+data['mc_boh_price'][i]+'" type="text" name="txt_boh_price_'+j+'" style="text-align:right;border: 1px solid #3f85f4;border-radius:2px;">';
							html += '				</div>';
							html += '				<div class="col-md-6" style="padding:1px;">';
							html += '					<input class="form-control" value="'+data['mc_received_price'][i]+'" type="text" name="txt_received_price_'+j+'" style="text-align:right;border: 1px solid #3f85f4;border-radius:2px;">';
							html += '				</div>';
							html += '			</div>';
							html += '			<div class="col-md-3" style="padding:0;margin:0;">';
							html += '				<div class="col-md-6" style="padding:1px;">';
							html += '					<input class="form-control" value="'+data['mc_eoh_price'][i]+'" type="text" name="txt_eoh_price_'+j+'" style="text-align:right;border: 1px solid #3f85f4;border-radius:2px;">';
							html += '				</div>';
							html += '				<div class="col-md-6" style="padding:1px;">';
							html += '					<input class="form-control" value="'+data['mc_whs_remaining'][i]+'" type="text" name="txt_whs_remaining_'+j+'" style="text-align:right;border: 1px solid green;border-radius:2px;">';
							html += '				</div>';
							html += '			</div>';
							html += '			<div class="col-md-3" style="padding:0;margin:0;">';
							html += '				<div class="col-md-6" style="padding:1px;">';
							html += '					<input class="form-control" value="'+data['mc_prodn_remaining'][i]+'" type="text" name="txt_prodn_remaining_'+j+'" style="text-align:right;border: 1px solid green;border-radius:2px;">';
							html += '				</div>';
							html += '				<div class="col-md-6" style="padding:1px;">';
							html += '					<input class="form-control" value="'+data['mc_received_quantity'][i]+'" type="text" name="txt_received_qty_'+j+'" style="text-align:right;border: 1px solid green;border-radius:2px;">';
							html += '				</div>';
							html += '			</div>';
							html += '			<div class="col-md-3" style="padding:0;margin:0;">';
							html += '				<div class="col-md-6" style="padding:1px;">';
							html += '					<input class="form-control" value="'+data['mc_whs_remaining'][i]+'" type="text" name="txt_whs_remaining_'+j+'" style="text-align:right;">';
							html += '				</div>';
							html += '				<div class="col-md-6" style="padding:1px;">';
							html += '					<input class="form-control" value="'+data['mc_prodn_remaining'][i]+'" type="text" name="txt_prodn_remaining_'+j+'" style="text-align:right;">';
							html += '				</div>';
							html += '			</div>';
							html += '		</div>';
							html += '	</div>';
							html += '	<div class="col-md-2" style="padding:2px;">';
							html += '		<input class="form-control" value="'+moment(data['mc_adate'][i]).format('MMM DD, YYYY HH:mm')+'" type="text" name="txt_adate_'+j+'" readonly>';
							html += '	</div>';
							html += '	<div class="col-md-1" style="padding:2px;">';
							html += '		<a class="a_existing_remove_row"><i class="fa fa-close pointer" style="margin-top:22%;"></i></a>';
							html += '	</div>';
							html += '</div>';
							$('#content_loop').append(html);
							j++;
						}
						$('input[name=txt_max_row]').val(i-1);

					}
					$('#mdl_price_allocation').modal('show');
				},
				error 	: function(data){
					alert('ISS:Error on submit frm_choose_material!');
				}
			});
		}
	});

	$('#frm_price_allocation').submit(function(e){
		e.preventDefault();
		var data_month = $('select[name=slt_data_month]').val();
		var data_year = $('select[name=slt_data_year]').val();
		var r = confirm("Are you sure you want to proceed?");
		if(r==true){
			var data = {
				"action"		: "update_material_info",
				"data_month"	: data_month,
				"data_year"		: data_year,
				"username"		: username
			}
			data = $.param(data) + "&" + $(this).serialize();
			$.ajax({
				type 	: "post",
				dataType: "json",
				data 	: data,
				url 	: handler,
				success : function(data){
					console.log(data['sd']);
					fn_destroy_table('t_consolidated_ppc');					
					load_consolidated_data_table($('select[name=slt_data_month]').val(),$('select[name=slt_data_year]').val());
					alert('Successfully updated!');
				},
				error 	: function(data){
					console.log(data);
					alert('ISS:Error on update_material_info!');
				}
			});
		}
	});

	$('#frm_update_cut_off_data').submit(function(e){
		e.preventDefault();
		var r = confirm("Are you sure you want to proceed?");
		if(r==true){
			var data = {
				"action"		: "update_cutoff_data",
				"username"		: username
			}
			data = $.param(data) + "&" + $(this).serialize();
			$.ajax({
				type 	: "post",
				dataType: "json",
				data 	: data,
				url 	: handler,
				success : function(data){
					if(data['data_count']==0){
						$('#body-invalid').fadeOut();
						$('#body-valid').fadeIn();
						load_consolidated_data_table(data['max_month'],data['max_year']);
					}
					alert('Successfully updated!');		
				},
				error 	: function(data){
					console.log(data);
					alert('ISS:Error on update_cutoff_data!');
				}
			});
		}
	});

	$('#frm_edit_range_of_data').submit(function(e){
		e.preventDefault();
		var slt_data_month = $('select[name=slt_data_month]').val();
		var slt_data_year = $('select[name=slt_data_year]').val();
		var r = confirm("Are you sure you want to proceed?");
		if(r==true){
			var data = {
				"action"			: "update_cutoff_data",
				"username"			: username,
				"slt_add_data_month": slt_data_month,
				"slt_add_data_year"	: slt_data_year
			}
			data = $.param(data) + "&" + $(this).serialize();
			$.ajax({
				type 	: "post",
				dataType: "json",
				data 	: data,
				url 	: handler,
				success : function(data){
					console.log(data['sd']);
					if(data['data_count']==1){
						$('#spn_range_of_date').text(data['formatted_cd_start_date']+' - '+data['formatted_cd_end_date']);
						fn_destroy_table('t_consolidated_ppc');
						load_consolidated_data_table(slt_data_month,slt_data_year);
						alert('Successfully updated!');
					}
				},
				error 	: function(data){
					console.log(data);
					alert('ISS:Error on update_cutoff_data!');
				}
			});
		}
	});

	$('#frm_choose_daterange_new_data').submit(function(e){
		e.preventDefault();
		var data_month = $('select[name=slt_data_month]').val();
		var data_year = $('select[name=slt_data_year]').val();
		$('#t_consolidated_ppc').DataTable().clear().destroy();
		var r = confirm("No generated data for the selected month and year. Do you want to generate? This may take a while.");
		if(r==true){
			var data = {
				'action'				: 'load_new_consolidated_data',
				'username'				: username,
				'data_month'			: data_month,
				'data_year'				: data_year
			}
			data = $.param(data) + "&" + $(this).serialize();;
			$.ajax({
				type 	: "post",
				dataType: "json",
				data 	: data,
				url 	: handler,
				success : function(data){
					// console.log(data['sd']);
					fn_destroy_table('t_consolidated_ppc');	
					load_consolidated_data_table($('select[name=slt_data_month]').val(),$('select[name=slt_data_year]').val());
					$('#mdl_choose_daterange_new_data').modal('hide')
				},
				error 	: function(data){
					console.log(data_child);
					alert('ISS:Error on load_new_consolidated_data'); 
				}
			});						
		}
		else{		
			$('#t_consolidated_ppc').css('visibility','hidden');
			alert('No data! Choose another set of month and year.');	
		}
	});

	$('input[name=txt_cm_material_code]').keyup(function(){
		var partial_material_code = $('input[name=txt_cm_material_code]').val();
		var data = {
			'action'				: 'load_suggestions_material',
			'partial_material_code'	: partial_material_code
		}
		data = $.param(data);
		$.ajax({
			type 	: "post",
			dataType: "json",
			data 	: data,
			url 	: handler,
			success : function(data){
				var ctr = data['ctr'];
				$('#list_material_code').empty();
				if(ctr==0){
					var html = '<option value="'+partial_material_code+'">'+partial_material_code+': Not found</option>';
					$('#list_material_code').append(html);
				}		
				else{
					for(var rka = 0;rka<ctr;rka++){
						var html = '<option value="'+data['PartNumber'][rka]+'">';
						$('#list_material_code').append(html);
					}
				}
			},
			error 	: function(data){
				alert('ISS:Error on input[name=txt_cm_material_code] .keyup'); 
			}
		});
	});

	$('input[name=txt_cm_material_code]').change(function(){
		var material_code = $('input[name=txt_cm_material_code]').val();
		var data = {
			'action'				: 'load_material',
			'partial_material_code'	: material_code
		}
		data = $.param(data);
		$.ajax({
			type 	: "post",
			dataType: "json",
			data 	: data,
			url 	: handler,
			success : function(data){
				$('input[name=txt_cm_description]').val(data['MaterialType']);
				$('input[name=txt_cm_material_id]').val(data['id']);
			},
			error 	: function(data){
				alert('ISS:Error on input[name=txt_cm_material_code] .keyup'); 
			}
		});
	});

	$('select[name=slt_data_month], select[name=slt_data_year]').change(function(){
		var data_month = $('select[name=slt_data_month]').val();
		var data_year = $('select[name=slt_data_year]').val();
		var data = {
			'action' 		: 'load_consolidated_data',
			'data_month'	: data_month,
			'data_year'		: data_year
		}
		data = $.param(data);
		$.ajax({
			type 	: "post",
			dataType: "json",
			data 	: data,
			url 	: handler,
			success : function(data){
				if(data['check']=='1'){
					$('#t_consolidated_ppc').DataTable().clear().destroy();
					$('#t_consolidated_ppc').css('visibility','visible');
					load_consolidated_data_table($('select[name=slt_data_month]').val(),$('select[name=slt_data_year]').val());
					$('input[name=txt_start_date]').val(data['cd_start_date']);
					$('input[name=txt_end_date]').val(data['cd_end_date']);
					$('#spn_range_of_date').text(data['formatted_cd_start_date']+' - '+data['formatted_cd_end_date']);
				}
				else{
					$('#mdl_choose_daterange_new_data').modal('show');
				}
			},
			error 	: function(data){
				alert('ISS:Error on load_consolidated_data'); 
			}
		});
	});

	$('#a_edit_range_of_date').click(function(){
		$('#mdl_edit_range_of_data').modal('show');
	});

	$('#t_consolidated_ppc tbody').on('click', 'tr', function(){
		// alert(test);
        if($(this).parent().hasClass('selected')){
            $(this).parent().removeClass('selected');
        }
        else if($(this).hasClass('selected')){
            $(this).removeClass('selected');
            $(this).parent().removeClass('selected');
        }
        else{
            $('tr').removeClass('selected');
            $(this).addClass('selected');
        }
        return false;
    });
});


function load_latest_consolidated_data(){
	var data = {
		'action'	: 'load_latest_consolidated_data'
	}
	data = $.param(data);
	$.ajax({
		type 	: "post",
		dataType: "json",
		data 	: data,
		url 	: handler,
		success : function(data){
			if(data['max_month']==undefined&&data['max_year']==undefined){
				var html = '<option value="">--Opt--</option>';
					html +=	'<option value="1">JAN</option>';
					html +=	'<option value="2">FEB</option>';
					html +=	'<option value="3">MAR</option>';
					html +=	'<option value="4">APR</option>';
					html +=	'<option value="5">MAY</option>';
					html +=	'<option value="6">JUN</option>';
					html +=	'<option value="7">JUL</option>';
					html +=	'<option value="8">AUG</option>';
					html +=	'<option value="9">SEP</option>';
					html +=	'<option value="10">OCT</option>';
					html +=	'<option value="11">NOV</option>';
					html +=	'<option value="12">DEC</option>';
				$('select[name=slt_add_data_month]').append(html);
				$('select[name=slt_add_data_year]').append('<option value="">--Opt--</option>');
				for(max_year=2017;max_year<=data['this_year'];max_year++){
					$('select[name=slt_add_data_year]').append('<option value="'+max_year+'">'+max_year+'</option>');
				}
				$('#body-valid').css('display','none');
				$('#body-invalid').css('display','inline');
			}
			else{
				$('#body-invalid').css('display','none');
				$('#body-valid').css('display','inline');
				$('select[name=slt_data_month]').val(data['max_month']);
				for(max_year=2017;max_year<=2019;max_year++){
					$('select[name=slt_data_year]').append('<option value="'+max_year+'">'+max_year+'</option>');
				}
				$('select[name=slt_data_year]').val(data['max_year']);
				$('input[name=txt_start_date]').val(data['cd_start_date']);
				$('input[name=txt_end_date]').val(data['cd_end_date']);
				$('#spn_range_of_date').text(data['formatted_cd_start_date']+' - '+data['formatted_cd_end_date']);
				load_consolidated_data_table(data['max_month'],data['max_year']);
			}
		},
		error 	: function(data){
			alert('ISS:Error on load_latest_consolidated_data'); 
		}
	});
}

function load_consolidated_data_table(data_month,data_year){
	console.log('./dist/server-side/consolidated_data.table.php?data_month='+data_month+'&data_year='+data_year);
	var t_consolidated_ppc = $('#t_consolidated_ppc').dataTable({
			ajax 		: './dist/server-side/consolidated_data.table.php?data_month='+data_month+'&data_year='+data_year,
			paginate	: false,
			"autoWidth"	: false,
			"scrollY"  	: "70vh",
			"scrollX"  	: "80%",
			order		: [0,"asc"],
	        "language": {
	            "decimal": ",",
	            "thousands": "."
	        },
			columnDefs			: [
								{"targets":[0],"orderable":false},
								{"className":"dt-right","targets":[3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23]},
								{"className":"dt-center","targets":[1]}
			],      
			"drawCallback": function( settings ){
				$('#t_consolidated_ppc').attr('style','width:100%;');
			},
	        fixedColumns:   {
	            leftColumns: 6
	        },
			"initComplete": function(settings, json){
				$('.bg-color-c3d6f4').parent().css('background-color','#c3d6f4');
				$('.bg-color-f2e1b5').parent().css('background-color','#f2e1b5');
				$('.bg-color-b5ffc9').parent().css('background-color','#b5ffc9');
			}
	});
}

function fn_destroy_table(table_id){
	$('#'+table_id).DataTable().clear().destroy();
}