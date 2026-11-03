var handler = 'dist/handler/handler.php';
var dt_home = '';
var dt_wip_o = '';
var dt_wip_transactions = '';
var dt_wip_transactions_o = '';
$(document).ready(function(){
	let test = [];

	dt_home = $('#tbl_home').DataTable({
		"bProcessing" 				: true,
		"bServerSide" 				: true,
		"sAjaxSource" 				: "dist/server_side/dt_home.php?current_page="+current_page,
        "fnServerParams": function ( aoData ) {
            // aoData.push( { "name": "date_from", "value": $('#txt_wip_date_from').val() } );
            // aoData.push( { "name": "date_to", "value": $('#txt_wip_date_to').val() } );
        },
	    aLengthMenu: [
	        [10, 25, 50, 100, 500, -1],
	        [10, 25, 50, 100, 500, "All"]
	    ],
		"columnDefs" 				: [
			// { className 			: "text-right", 	targets : [ 3,4,5,6,7,8 ]},
			// { "orderable" 			: false, "targets": 3 },
		],
		aaSorting 					: [[0, 'desc']],
		"drawCallback" 				: function(settings){
			// init_plugins();
		},
		"fnRowCallback" 			: function( nRow, aData, iDisplayIndex, iDisplayIndexFull){
			// $('td:eq(8)', nRow).addClass('table-primary');
		},

	});

	// $('#tbl_home').on('click','.print_lot_no',function(){
	// 	$('#mdl_print_preview').modal('show');
	// });

// -------------------------------------------------------------
	$(function(){
		dt_wip_o = $("#tbl_home").dataTable();
	});
// -------------------------------------------------------------
	$('tbody.naa_table_selectable').on("click", "tr", function(){
		$(this).closest('tbody').find('tr').removeClass('table-active');
		$(this).closest('tr').addClass('table-active');
		changeVisibilityHidden();
		changeVisibilityVisible();
	});

	$('.btn').click(function(){
		check_session_expired( $(this).prop('id') );
	});

	$('#txt_req_product_code').on('input', function(e){ // this script is for changing the value of input after selection in datalist - chris 061124
		let input = e.target;
		let options = $('#dl_req_product_code option[value="'+input.value+'"]');
		// var input = e.target,	
		// 	list = input.getAttribute('list'),
		// 	options = document.querySelectorAll('#' + list + ' option[value="'+input.value+'"]'),
		// 	hiddenInput = document.getElementById(input.getAttribute('hiddenSelectedId'));
	
		if (options.length > 0) {
		  	input.value = options[0].innerText;
			$('#hiddenSelectedId').val(options[0].value)

		}
	});


	$('#txt_req_product_code').on('keyup', function(e){
		var thiss = $(this);
		var dl_req_product_code = $(thiss).closest('.form-group').find('#dl_req_product_code');
		$(dl_req_product_code).html('');

		$(thiss).closest('.modal').find('#txt_req_product_name').val('').attr('data-fkid','0');
		var data 				= {
			"action" 		: "select_item_list",
			"keyword" 		: $(this).val()
		}
		$.ajax({
			type 			: "post",
			dataType 		: "json",
			data 			: data,
			url 			: handler,
			success 		: function(data){
				var html = '';
				// for (var ctr = 0; ctr < data['ctr']; ctr++) {
				// 	html += '<option data-pkid = "' + data['pkid_itemlist'][ctr] + '" value = "' + data['partcode'][ctr] + '">' + data['partname'][ctr] + ' / Factory ' + data['Factory'][ctr] +'</option>';
				// }

				for (var ctr = 0; ctr < data['ctr']; ctr++) { // for factory 3 selection
					html += '<option data-pkid = "' + data['pkid_itemlist'][ctr] + '" value = "' +data['partname'][ctr] + ' / Factory ' + data['Factory'][ctr]+ '" data-part-name = "' + data['partcode'][ctr] + '">' + data['partcode'][ctr]  +'</option>';
				}
				$(dl_req_product_code).html(html);

				if(e.keyCode == 13 || e.keyCode == undefined){
					let selectedValue = $('#hiddenSelectedId').val();
					// var pkid = $(dl_req_product_code).find('option').filter(function() {//check if value exist in options
					// 	return this.value == $(thiss).val();
					// }).data('pkid');
					// var pkid = $(dl_req_product_code).find('option[value="'+ $(thiss).val() +'"]').attr('data-pkid');
					var pkid = $(dl_req_product_code).find('option[value="'+ selectedValue +'"]').attr('data-pkid');
					// console.log('pkid', pkid);
					var pkid = pkid ? pkid : 0;
					$(thiss).closest('.modal').find('#txt_req_product_name').attr('data-fkid',pkid);
					$(thiss).closest('.modal').find('#txt_req_product_name').val( $(dl_req_product_code).find('option[value="'+selectedValue+'"]')[0].value);

					// $(dl_req_product_code).find('option').each(function(index) {//get the option text
					// 	if( $(thiss).val() == $(this).val() ){
					// 		$(thiss).closest('.modal').find('#txt_add_product_name').val( $(this).text() );
					// 	}
					// });
				}
			}
		});
	});

	$('#txt_req_destination').keyup(function(e){
		var data = {
			"action" 		: "select_destination_list",
			"keyword" 		: $(this).val()
		}
		$.ajax({
			type 			: "post",
			dataType 		: "json",
			data 			: data,
			url 			: handler,
			success 		: function(data){
				var html = '<option value="">---</option>';
				html += '<option>PPS CN</option>';
				html += '<option>PPS TS</option>';

				for(var ctr = 0; ctr < data['ctr']; ctr++){
					html += '<option>'+data['destination'][ctr]+'</option>';
				}
				$('#dl_destination').html(html);
			}
		});
	})

	$('#frm_request').on('submit', function(e){
		e.preventDefault();
		var thiss = $(this);
		//----------------
		if( $(thiss).find('#txt_req_product_name').attr('data-fkid') == '' || $(thiss).find('#txt_req_product_name').attr('data-fkid') == '0' || $(thiss).find('#txt_req_product_name').attr('data-fkid') == "undefined" ){
			show_popup_message( '#alert_popup_container_new_request', 'alert-warning', 'default', 'Please select a valid Part Code.' );
			return;
		}
		//----------------
		// var val = $(thiss).find("#txt_req_destination").val();
		// var obj = $(thiss).find("#dl_destination").find("option[value='" + val + "']");
		// if(obj != null && obj.length > 0){
		// }
		// else{
		// 	show_popup_message( '#alert_popup_container_new_request', 'alert-warning', 'default', 'Please select a valid Destination.' );
		// 	return;
		// }
		//----------------
		var data = {
			"action" 			: "insert_request",
			"fkid" 				: $(thiss).find('#txt_req_product_name').attr('data-fkid'),
			"pkid" 				: $(thiss).find('#txt_req_product_name').attr('data-pkid'),
		}
		data = $.param(data)+"&"+$(this).serialize();
		$.ajax({
			type 				: "post",
			dataType 			: "json",
			data 				: data,
			url 				: handler,
			success 			: function(data){
				show_popup_message( '#alert_popup_container_main', 'alert-success', '', $.parseJSON(data['result']) );
				$(thiss).closest('.modal').modal('hide');
				dt_home.ajax.reload();
			},
		});
	});

	$('#frm_issue').on('submit', function(e){
		e.preventDefault();
		var thiss = $(this);

		//----------------
		var qty_request 	= $(thiss).closest('.modal').find('#txt_req_qty').val();
		var qty_issue 		= $(thiss).find('#txt_issue_qty').val();
		var qty_allowed 	= $(thiss).find('#txt_issue_allowed_qty').val();

		if( qty_issue > qty_allowed ){
			show_popup_message( '#alert_popup_container_new_request', 'alert-warning', 'default', 'The issued quantity is more than the allowed quantity.' );
			return;
		}
		if( qty_issue > qty_request ){
			show_popup_message( '#alert_popup_container_new_request', 'alert-warning', 'default', 'The issued quantity is more than the requested quantity.' );
			return;
		}
		if( qty_issue < qty_request ){
			confirm('The issued quantity is less than the requested quantity, are you sure you want to continue?');
		}
		//----------------
		var data = {
			"action" 				: "insert_issue",  
			"fkid" 					: $(thiss).closest('.modal').find('#txt_req_product_name').attr('data-fkid'),
			"pkid" 					: $(thiss).closest('.modal').find('#txt_req_product_name').attr('data-pkid'),
			"span_ctrl_no" 			: $(thiss).closest('.modal').find('#span_ctrl_no').text(),
			"txt_issue_allowed_qty" : $(thiss).closest('.modal').find('#txt_issue_allowed_qty').val(),
			"txt_req_by" 			: $(thiss).closest('.modal').find('#txt_req_by').val(),
			"txt_req_destination" 	: $(thiss).closest('.modal').find('#txt_req_destination').val()

		}


		data = $.param(data)+"&"+$(this).serialize();
		$.ajax({
			type 				: "post",
			dataType 			: "json",
			data 				: data,
			url 				: handler,
			success 			: function(data){
				show_popup_message( '#alert_popup_container_main', 'alert-success', '', $.parseJSON(data['result']) );
				$(thiss).closest('.modal').modal('hide');
				dt_home.ajax.reload();
			},
		});
	})


	$('#frm_issue_multiple').on('submit', function(e){
		e.preventDefault();
		var thiss = $(this);

		//----------------
		var qty_request 	= parseFloat($(thiss).closest('.modal').find('#txt_req_qty').val());
		var qty_issue 		= parseFloat($(thiss).find('#txt_total_issue_qty').val());
		var qty_allowed 	= parseFloat($(thiss).find('#txt_issue_allowed_qty').val());
		console.log('r'+qty_request);
		console.log('i'+qty_issue);
		console.log('a'+qty_allowed);


		if( qty_issue > qty_allowed ){
			show_popup_message( '#alert_popup_container_new_request', 'alert-warning', 'default', 'The issued quantity is more than the allowed quantity.' );
			return;
		}
		if( qty_issue > qty_request ){
			show_popup_message( '#alert_popup_container_new_request', 'alert-warning', 'default', 'The issued quantity is more than the requested quantity.' );
			return;
		}
		if( qty_issue < qty_request ){
			if (!confirm('The issued quantity is less than the requested quantity, are you sure you want to continue?')){
				return;
			}
		}
		//-------------P---
		var data = {
			"action" 				: "insert_issue_multiple",  
			"fkid" 					: $(thiss).closest('.modal').find('#txt_req_product_name').attr('data-fkid'),
			"pkid" 					: $(thiss).closest('.modal').find('#txt_req_product_name').attr('data-pkid'),
			"span_ctrl_no" 			: $(thiss).closest('.modal').find('#span_ctrl_no').text(),
			"txt_issue_allowed_qty" : $(thiss).closest('.modal').find('#txt_issue_allowed_qty').val(),
			"txt_req_by" 			: $(thiss).closest('.modal').find('#txt_req_by').val(),
			"txt_req_destination" 	: $(thiss).closest('.modal').find('#txt_req_destination').val()

		}


		data = $.param(data)+"&"+$(this).serialize();
		$.ajax({
			type 				: "post",
			dataType 			: "json",
			data 				: data,
			url 				: handler,
			success 			: function(data){
				show_popup_message( '#alert_popup_container_main', 'alert-success', '', $.parseJSON(data['result']) );
				$(thiss).closest('.modal').modal('hide');
				dt_home.ajax.reload();
			},
		});
	})
// ------------------------------------------------------------- /WIP TRANSACTION



});

function clear_form(parent_container){
	$(parent_container).find('input').val('');
	$(parent_container).find('textarea').val('');

	$(parent_container).find('.btn_submit').hide();
	$(parent_container).find('#txt_req_product_name').attr('data-fkid','0');
	$(parent_container).find('#txt_req_product_name').attr('data-pkid','0');

	$(parent_container).find('#span_status').html('---');
	$(parent_container).find('#span_ctrl_no').html('---');

	$(parent_container).find('#col_allowed_qty').prop('hidden',true);
}

function set_readonly_request(parent_container,state){
	$(parent_container).find('#txt_req_product_code').prop('readonly',state);
	$(parent_container).find('#txt_req_qty').prop('readonly',state);
	$(parent_container).find('#txt_req_destination').prop('readonly',state);
	$(parent_container).find('#txt_req_remarks').prop('readonly',state);
}

function set_readonly_issue(parent_container,state){
	$(parent_container).find('#txt_issue_qty').prop('readonly',state);
	$(parent_container).find('#txt_issue_remarks').prop('readonly',state);	
	$(parent_container).find('#txt_multi_issue_qty').prop('readonly',state);
	$(parent_container).find('#txt_multi_issue_remarks').prop('readonly',state);	
}



function setup_modal_request(parent_container){
	$(parent_container).find('.modal-dialog').removeClass('modal-lg');
	$(parent_container).find('#row_alert_info_control_number').show();
	$(parent_container).find('.panel_1').removeClass('border-right');
	$(parent_container).find('.panel_1').removeClass('col-sm-6').addClass('col-sm-12');
	$(parent_container).find('.panel_2').prop('hidden',true);
}

function setup_modal_edit(parent_container){
	$(parent_container).find('.modal-dialog').removeClass('modal-lg');
	$(parent_container).find('#row_alert_info_control_number').hide();
	$(parent_container).find('.panel_1').removeClass('border-right');
	$(parent_container).find('.panel_1').removeClass('col-sm-6').addClass('col-sm-12');
	$(parent_container).find('.panel_2').prop('hidden',true);
}

function setup_modal_issue(parent_container){
	$(parent_container).find('.modal-dialog').addClass('modal-lg');
	$(parent_container).find('#row_alert_info_control_number').hide();
	$(parent_container).find('.panel_1').addClass('border-right');
	$(parent_container).find('.panel_1').removeClass('col-sm-12').addClass('col-sm-6');
	$(parent_container).find('.panel_2').prop('hidden',false);
}
function select_record(mode){
	//mode : 1=details, 2=edit, 3=issue
	var parent_container 	= $('#modal_request_details');
	var select_pkid 		= $('#tbl_home tbody tr.table-active td').find('.row_pkid').text();
	//----------------
	var data = {
		"action" 		: "select_request_details",
		"select_pkid" 	: select_pkid
	}
	$.ajax({
		type 			: "post",
		dataType 		: "json",
		data 			: data,
		url 			: handler,
		success 		: function(data){
			for(var ctr = 0; ctr < data['ctr']; ctr++){
				$(parent_container).find('#span_status').html( data['status'][ctr] );
				$(parent_container).find('#span_ctrl_no').html( data['control_number'][ctr] );

				$(parent_container).find('#txt_req_product_code').val( data['partcode'][ctr] );
				$(parent_container).find('#txt_req_product_name').val( data['partname'][ctr] );
				$(parent_container).find('#txt_req_product_name').attr('data-fkid', data['fk_itemlist'][ctr] );
				$(parent_container).find('#txt_req_product_name').attr('data-pkid', data['pkid'][ctr] );
				$(parent_container).find('#txt_req_qty').val( data['qty'][ctr] );
				$(parent_container).find('#txt_req_destination').val( data['destination'][ctr] );
				$(parent_container).find('#txt_req_remarks').val( data['r_remarks'][ctr] );
				$(parent_container).find('#txt_req_on').val( data['created_on'][ctr] );
				$(parent_container).find('#txt_req_by').val( data['created_by'][ctr] );

				$(parent_container).find('#txt_issue_qty').val( data['issuanceqty'][ctr] );
				$(parent_container).find('#txt_issue_remarks').val( data['i_remarks'][ctr] );
				$(parent_container).find('#txt_issue_on').val( data['issuancedate'][ctr] );
				$(parent_container).find('#txt_issue_by').val( data['issuedby'][ctr] );

				if(mode == 3){
					$(parent_container).find('#txt_issue_qty').val( data['qty'][ctr] );
					select_available_balance(parent_container,data['fk_itemlist'][ctr]);
					select_current_user_and_datetime(2);
				}

				$(parent_container).modal('show');
			}
		}
	});
	return true;
}

function select_record_multiple(mode){
	// mode : 1=details, 2=edit, 3=issue
	var parent_container 	= $('#modal_request_details_multiple');
	var select_pkid 		= $('#tbl_home tbody tr.table-active td').find('.row_pkid').text();
	//----------------
	var data = {
		"action" 		: "select_request_details_multiple",
		"select_pkid" 	: select_pkid
	}
	$.ajax({
		type 			: "post",
		dataType 		: "json",
		data 			: data,
		url 			: handler,
		success 		: function(data){
			// console.log('issuanceqty',data['issuanceqty']);
			// console.log('issuanceqtym',data['issuanceqtym']);
			// console.log('im_remarks',data['im_remarks']);
			for(var ctr = 0; ctr < data['ctr']; ctr++){
				$(parent_container).find('#span_status').html( data['status'][ctr] );
				$(parent_container).find('#span_ctrl_no').html( data['control_number'][ctr] );

				$(parent_container).find('#txt_req_product_code').val( data['partcode'][ctr] );
				$(parent_container).find('#txt_req_product_name').val( data['partname'][ctr] );
				$(parent_container).find('#txt_req_product_name').attr('data-fkid', data['fk_itemlist'][ctr] );
				$(parent_container).find('#txt_req_product_name').attr('data-pkid', data['pkid'][ctr] );
				$(parent_container).find('#txt_req_qty').val( data['qty'][ctr] );
				$(parent_container).find('#txt_req_destination').val( data['destination'][ctr] );
				$(parent_container).find('#txt_req_remarks').val( data['r_remarks'][ctr] );
				$(parent_container).find('#txt_req_on').val( data['created_on'][ctr] );
				$(parent_container).find('#txt_req_by').val( data['created_by'][ctr] );

				if(data['issuanceqtym'].length > 0){
					$("#dynamic_field > tbody").html('');
					for(let index = 0; index < data['issuanceqtym'].length; index++){

						if (data['issuanceqtym'][index] == 0 || data['issuanceqtym'][index] == null ){
							var issuanceqtym = '';
						}else{
							var issuanceqtym = data['issuanceqtym'][index];
						}

						if (data['im_remarks'][index] == 0 || data['im_remarks'][index] == null ){
							var im_remarks = '';
						}else{
							var im_remarks = data['im_remarks'][index];
						}

						if (data['opt_remarks'][index] == 0 || data['opt_remarks'][index] == null ){
							var opt_remarks = '';
						}else{
							var opt_remarks = data['opt_remarks'][index];
						}





							let htmlRowAttachments = "";
							// htmlRowAttachments += '<tr>';
							// 	htmlRowAttachments += '<td width="100px" style="border-style:hidden;"><label>Issued qty</label><input type="number" onKeyUp = "calculateSum()" class="form-control form-control-sm qty" id="txt_multi_issue_qty" name="txt_multi_issue_qty[]" value = "' + data['issuanceqtym'][index] + '" step="any" required></td>';
							// 	htmlRowAttachments += '<td style="border-style:hidden;"><label>Remarks</label><input type="text" class="form-control form-control-sm" id="txt_multi_issue_remarks" name="txt_multi_issue_remarks[]" value="' + data['im_remarks'][index] + '" required><button type="button" class="btn btn-info" id="btn_scan_lot_no" data-target="#mdl_scan_lot_no"><i class="fa fa-qrcode"></i></button></td>';					
							// htmlRowAttachments += '</tr>';
							//nmodify
							htmlRowAttachments += `<tr>
														<td width="100px" style="border-style:hidden;">
															<label>Issued qtys</label>
															<input type="text" onKeyUp = ${calculateSum()} class="form-control form-control-sm qty" id="txt_multi_issue_qty${index}" name="txt_multi_issue_qty[]" value = "${issuanceqtym}" required>
														</td>
														<td width="100px" style="border-style:hidden;">
															<label>Lot No</label>
															<div class="input-group input-group-sm">
																<div class="input-group-prepend">
																	<button type="button" class="btn btn-info" id="btn_scan_lot_no" index="${index}" data-target="#mdl_scan_lot_no"><i class="fa fa-qrcode"></i></button>
																	<input type="text" class="form-control form-control-sm" id="txt_multi_issue_remarks${index}" name="txt_multi_issue_remarks[]" value="${im_remarks}" required>
																</div>
															</div>
														</td>'	
														<td width="100px" style="border-style:hidden;">
															<label>Remarks</label>
															<input type="text" class="form-control form-control-sm" id="txt_opt_remarks_id${index}" name="txt_opt_remarks[]" value = "${opt_remarks}" required>
														</td>		
														<td width ="50px" style="border-style:hidden; padding: 0; margin-top: 0.75rem;">
														</td>
													</tr>`;
													// ${data['im_remarks'][index]} value ng remarks
							$("#dynamic_field > tbody").append(htmlRowAttachments);
							$("#dynamic_field > tbody").find('tr:last').find('td .txt_multi_issue_qty').focus();

						}
					}
				else{
						let htmlRowAttachments = `<tr>;

						// htmlRowAttachments += '<td colspan="">No Data</td>';

						</tr>`;

						$("#dynamic_field > tbody").html(htmlRowAttachments);
					}	

				$(parent_container).find('#txt_total_issue_qty').val( data['issuanceqty']);
				$(parent_container).find('#txt_issue_on_multi').val( data['issuancedate'][ctr] );
				$(parent_container).find('#txt_issue_by_multi').val( data['issuedby'][ctr] );
				//im_remarks 

				if(mode == 3){
					// $(parent_container).find('#txt_total_issue_qty').val(calculateSum());
					select_available_balance(parent_container,data['fk_itemlist'][ctr]);
					select_current_user_and_datetime_multiple(2);
				}

				$(parent_container).modal('show');
			}
		}

	});

	return true;
}


function select_available_balance(parent_container,fkid){
	var remainingqty 		= 0;
	var data = {
		"action" 	: "select_available_balance",
		"fkid" 		: fkid
	}
	$.ajax({
		"type" 		: "post",
		"dataType" 	: "json",
		"data" 		: data,
		"url" 		: handler,
		success 	: function(data){
			for(var ctr = 0; ctr < data['ctr']; ctr++){
				remainingqty = data['remainingqty'][ctr];
			}
			console.log('F1 data[remainingqty]: ',data['remainingqty'])
			console.log('F1 ctr: ',data['ctr'])
			console.log('parent_container', parent_container);
			$(parent_container).find('#txt_issue_allowed_qty').val(remainingqty);
		}
	});
}

function select_current_user_and_datetime(mode){
	var parent_container 	= $('#modal_request_details');
	var data = {
		"action" 		: "select_current_user_and_datetime",
	}
	$.ajax({
		type 			: "post",
		dataType 		: "json",
		data 			: data,
		url 			: handler,
		success 		: function(data){
			if(mode==1){
				$('#txt_req_by').val(data['username']);
				$('#txt_req_on').val(data['datetime']);
			}
			else if(mode==2){
				$('#txt_issue_by').val(data['username']);
				$('#txt_issue_on').val(data['datetime']);
			}
		}
	});
}
	
function select_current_user_and_datetime_multiple(mode){
	var parent_container 	= $('modal_request_details_multiple');
	var data = {
		"action" 		: "select_current_user_and_datetime",
	}
	$.ajax({
		type 			: "post",
		dataType 		: "json",
		data 			: data,
		url 			: handler,
		success 		: function(data){
			if(mode==1){
				$('#txt_req_by').val(data['username']);
				$('#txt_req_on').val(data['datetime']);
			}
			else if(mode==2){
				$('#txt_issue_by_multi').val(data['username']);
				$('#txt_issue_on_multi').val(data['datetime']);
			}
		}
	});
}

function check_session_expired(btn_id){
	var data = {
		"action" 		: "check_session_expired",
	}
	$.ajax({
		type 			: "post",
		dataType 		: "json",
		data 			: data,
		url 			: handler,
		success 		: function(data){
			if(!data['username']){
				alert('Your session has expired and you need to re-login.');
				window.location = 'http://rapid';
			}
			else{
				if(btn_id=='btn_add_new_request_show_form'){
					btn_add_new_request_show_form();
				}
				else if(btn_id=='btn_edit_request'){
					btn_edit_request();
				}
				else if(btn_id=='btn_cancel_request'){
					btn_cancel_request();
				}
				else if(btn_id=='btn_ack'){
					btn_ack();
				}
				else if(btn_id=='btn_open_details'){
					btn_open_details();
				}
				else if(btn_id=='btn_issue'){
					btn_issue();
				}
				else if(btn_id=='btn_issue_multiple'){
					btn_issue_multiple();
				}
			}
		}
	});
}

function btn_add_new_request_show_form(){
	var parent_container 	= $('#modal_request_details');
	var panel_request 		= $(parent_container).find('#panel_request');
	var panel_issuance 		= $(parent_container).find('#panel_issuance');

	clear_form(parent_container);
	setup_modal_request(parent_container);
	set_readonly_request(panel_request,false);

	$(parent_container).find('#btn_ok_request').show();
	select_current_user_and_datetime(1);
	$(parent_container).modal('show');
}
function btn_edit_request(){
	//----------------
	if( !check_active_record() ){return;}
	if( !check_select_pending_record() ){return;}
	//----------------

	var parent_container 	= $('#modal_request_details');
	var panel_request 		= $(parent_container).find('#panel_request');
	var panel_issuance 		= $(parent_container).find('#panel_issuance');

	clear_form(parent_container);
	setup_modal_edit(parent_container);
	set_readonly_request(panel_request,false);
	set_readonly_issue(panel_issuance,true);

	$(parent_container).find('#btn_ok_update').show();
	select_record(2);
}

function btn_cancel_request(){
	//----------------
	if( !check_active_record() ){return;}
	if( !check_select_pending_record() ){return;}
	//----------------
	if(confirm('Are you sure you want to cancel the request?')){
		var data = {
			"action" 		: "cancel_request",
			"pkid" 			: $('#tbl_home tbody tr.table-active td').find('.row_pkid').text()
		}
		$.ajax({
			type 		: "post",
			dataType 	: "json",
			data 		: data,
			url 		: handler,
			success 	: function(data){
				show_popup_message( '#alert_popup_container_main', 'alert-success', 'default', 'Request has been cancelled.' );
				dt_home.ajax.reload();
			}
		});
	}
}

function btn_ack(){
	//----------------
	if( !check_select_unack() ){return;}
	//----------------
	if(confirm('Are you sure you want to acknowledge the request?')){
		var data = {
			"action" 		: "acknowledgement",
			"pkid" 			: $('#tbl_home tbody tr.table-active td').find('.row_pkid').text(),
			"control_number": $('#tbl_home tbody tr.table-active td').find('.row_control_number').text()

		}
		$.ajax({
			type 		: "post",
			dataType 	: "json",
			data 		: data,
			url 		: handler,
			success 	: function(data){
				show_popup_message( '#alert_popup_container_main', 'alert-success', 'default', 'Request has been Acknowledged.' );
				dt_home.ajax.reload();
			}
		});
	}
}

function btn_open_details(){

	// console.log('pumasok dito');
	//----------------
	if( !check_active_record() ){return;}
	//----------------
	var parent_container 	= $('#modal_request_details_multiple');
	var panel_request 		= $(parent_container).find('#panel_request');
	var panel_issuance 		= $(parent_container).find('#panel_issuance_multiple');

	

	clear_form(parent_container);
	setup_modal_issue(parent_container);
	set_readonly_request(panel_request,true);
	set_readonly_issue(panel_issuance,true);
	$(parent_container).find('#add').hide();
	//----------------
	select_record_multiple(1);
	$('.qty').prop('disabled', true);
}
function btn_issue(){
	//----------------
	if( !check_active_record() ){return;}
	if( !check_select_pending_record() ){return;}
	//----------------
	var parent_container 	= $('#modal_request_details');
	var panel_request 		= $(parent_container).find('#panel_request');
	var panel_issuance 		= $(parent_container).find('#panel_issuance');

	clear_form(parent_container);
	setup_modal_issue(parent_container);
	set_readonly_request(panel_request,true);
	set_readonly_issue(panel_issuance,false);
	//----------------
	$(parent_container).find('#col_allowed_qty').prop('hidden',false);
	$(parent_container).find('#btn_ok_issue').show();
	select_record(3);
}

function btn_issue_multiple(){
	//----------------
	if( !check_active_record() ){return;}
	if( !check_select_pending_record() ){return;}
	//----------------
	var parent_container 	 = $('#modal_request_details_multiple');
	var panel_request 		 = $(parent_container).find('#panel_request');
	var panel_issuance_multi = $(parent_container).find('#panel_issuance_multiple');

	clear_form(parent_container);
	setup_modal_issue(parent_container);
	set_readonly_request(panel_request,true);
	set_readonly_issue(panel_issuance_multi,false);
	//----------------
	$(parent_container).find('#col_allowed_qty').prop('hidden',false);
	$(parent_container).find('#btn_ok_issue').show();
	$(parent_container).find('#add').show();
	select_record_multiple(3);
}

function check_active_record(){
	var select_active = $('#tbl_home tbody tr.table-active').length;
	if(!select_active){
		show_popup_message( '#alert_popup_container_main', 'alert-warning', 'default', 'Please select a record first.' );
		return false;
	}
	return true;
}

function check_select_pending_record(){
	var select_status = $('#tbl_home tbody tr.table-active td').find('.status_pending').length;
	if(!select_status){
		show_popup_message( '#alert_popup_container_main', 'alert-warning', 'default', 'The record is no longer editable.' );
		return false;
	}
	return true;
}

function check_select_unack(){
	var select_unacknowledged = $('#tbl_home tbody tr.table-active td').find('.status_unacknowledged').length;
	if(!select_unacknowledged){
		show_popup_message( '#alert_popup_container_main', 'alert-warning', 'default', 'The record has already been Acknowledged' );
		return false;
	}
	return true;
}

function changeVisibilityHidden() {
	var status = $('#tbl_home tbody tr.table-active td').find('.status_pending').length;
	if(!status){
		return false;
	}
	// document.querySelector("#btn_ack").style.display = "none";
}

function changeVisibilityVisible() {
	var status = $('#tbl_home tbody tr.table-active td').find('.status_served').length;
	if(!status){
		return false;
	}
  	document.getElementById("btn_ack").style.display = "initial";
}

var i=1; 
$('#add').click(function(){  
	$('#dynamic_field').append(
	`<tr id="row${i}">
		<td width="100px" style="border-style:hidden;">
			<input type="number" onKeyup ="${calculateSum()}" class="form-control form-control-sm qty" id="txt_multi_issue_qty${i}" name="txt_multi_issue_qty[]" step="any" required>
		</td>
		<td width="100px" style="border-style:hidden;">
			<div class="input-group input-group-sm">	
				<div class="input-group-prepend">
					<button type="button" class="btn btn-info" id="btn_scan_lot_no${i}" index="${i}" data-target="#mdl_scan_lot_no"><i class="fa fa-qrcode"></i></button>
					<input type="text" class="form-control form-control-sm" id="txt_multi_issue_remarks${i}" name="txt_multi_issue_remarks[]" required>
				</div>
			</div>
		</td>
		<td width="100px" style="border-style:hidden;">
			<input type="text" class="form-control form-control-sm" id="txt_opt_remarks_id${i}" name="txt_opt_remarks[]"required>
		</td>
		<td style="border-style:hidden; margin-top: 0.75rem;">
			<button style="line-height: 1;" type="button" name="remove" id="${i}" class="btn btn-danger btn_remove" >X</button>
		</td>
	</tr>`);  

	$(document).on('click', `#btn_scan_lot_no${i}`,function(e){
		// console.log('qweqwe',i)
		// $('#txt_scan_lot_no_id').focus();
		// $('#txt_multi_issue_remarks').val('');
		let id = $(this).attr('index');
		$('#id_index_no').val(id);
		$(`#txt_scan_lot_no_id`).val('');
		$('#mdl_scan_lot_no').attr('data-formid','fn_scan_po_number').modal('show');
	});

	$(document).on('keypress',`#txt_scan_lot_no_id${i}`,function(e){
        if( e.keyCode == 13 ){
            var scanLotNoIdMultiple = $(`#txt_scan_lot_no_id${i}`).val();
            $(`#txt_multi_issue_remarks${i}`).val(scanLotNoIdMultiple);
            $('#mdl_scan_lot_no').modal('hide');
			// console.log('qweqwe',scanLotNoIdMultiple);
        }
    });
	i++;
});  



$(document).on('click', '.btn_remove', function(){  
i--;
	var button_id = $(this).attr("id");   
	$('#row'+button_id+'').remove(); 	
	calculateSum(); 
});  


//  $(".txt_multi_issue_qty").each(function() {
//  $(".qty").each(function() {
// 	console.log('test keyup');
//     $(this).keyup(function() { 
//       calculateSum();
//     });
//   });
    $(document).on('keyup', '.qty', function() {
		console.log('keyup ');
		calculateSum();
		// return;
    });

 function calculateSum() { //nmodify
	// alert('111dsadsd');
  	var parent_container 	= $('#modal_request_details_multiple');
  	var sum = 0;
  //iterate through each textboxes and add the values
  $(".qty").each(function() {	

    //add only if the value is number
    if (!isNaN(this.value) && this.value.length != 0) {
      sum += parseFloat(this.value);
    }
	// console.log('this.value',sum);
  });
  //.toFixed() method will roundoff the final sum to 2 decimal places
  
  $(parent_container).find("#txt_total_issue_qty").val(sum.toFixed(2));
}


function print_preview(pkid){
	// let path = "../../../../format/Dynamic_test.php"
	var data = {
		"action"	: "get_qrcode_issuance_data",
		"pkid"		: pkid
	}
	data = $.param(data);
	// alert(data)
	$.ajax({
		type	: "post",
		dataType: "json",
		data	: data,
		url		: "../../session_check.php",
		success	: function(data){

			test = data;
			console.log('DATA:', test)
			$('#imgQRCodeSticker').attr('src', "data:image/png;base64,"+data['qrcode']);
			
			if (data['im_remarks'] == null || data['im_remarks'] == ''){
				$('#lot_num').text('N/A');
			}else{
				$('#lot_num').text(data['im_remarks']);
			}

			$('input[name=pkid]').val(pkid);
			$('#part_code').text(data['partcode']);
			$('#part_name').text(data['partname']);
			$('#issued_qty').text(data['issuanceqtym']);

			setTimeout(() => {
				$('#mdl_print_preview').modal('show');
			}, 100);

		} 
	});
}


$("#btnPrintQRCode").click(function(){
	// alert('printttttttt')

	popup = window.open();
	var fk_issuance = test['fk_issuance'];
	console.log(test['qrcode']);
	let content = '';
       for (var i = 0; i < fk_issuance.length; i++) {
			content += '<table style="margin-left: -5px;>';
			content += '<tr>';
			content += '<td>';
			content += '</td>';
			content += '</tr>';
			content += '<tr style="width: 290px;">';
			content += '	<td style="vertical-align: bottom;">';
			content += '		<img src="data:image/png;base64,'+test['qrcode'][i]+'" style="width:70px;height:70px;">';
			content += '	</td>';
			content += '	<td>';
			content += '		<label style="text-align: center; font-family: Arial; font-size:8.5px;">'+test['partcode'][i]+'</label><br>';
			content += '		<label style="text-align: center; font-family: Arial; font-size:8.5px;">'+test['partname'][i]+'</label><br>';
			content += '		<label style="text-align: center; font-family: Arial; font-size:8.5px;">'+test['issuanceqtym'][i]+'</label><br>';
			content += '		<label style="text-align: center; font-family: Arial; font-size:8.5px;">'+test['im_remarks'][i]+'</label><br>';
			content += '	</td>';
			content += '</tr>';
			content += '</table>';
		}
	popup.document.write(content);
	popup.focus(); //required for IE
	popup.print();
	popup.close();
})


// function checkIfPrinted(){

// 	var data = {
// 			"action" : "checkIfPrinted",
// 			"fkid" : "<?php echo $id; ?>",
// 	}				
// 	data = $.param(data);
// 	$.ajax({
// 		type : "post",
// 		dataType : "json",
// 		data : data,
// 		url : "./ppswarehouseHandler.php",
// 		success : function(data){
// 			UpdateRecord("1");
// 			UpdateItem("1");
// 		}
// 	});		
	
// }