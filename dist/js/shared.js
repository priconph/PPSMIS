var handler 	= "./dist/handler/handler.php";

$(document).ready(function(){	
	if(self==top){
		window.location = 'http://rapid';
	}

	$('.datepicker').datepicker({
		format: 'mm/dd/yyyy',
		changeMonth: true, 
		changeYear: true,
	});

	$('.datepicker-today').datepicker({
		format: 'mm/dd/yyyy',
		changeMonth: true, 
		changeYear: true,
	});

	$('.datepicker-today').val(
		moment().format('MM/DD/YYYY')
	);

	$('.datepicker').attr('autocomplete','off');

	$('.datepicker-today').attr('autocomplete','off');

	$('.decimal').keypress(function(evt){
		return (/^[0-9]*\.?[0-9]*$/).test($(this).val()+evt.key);
	});

	$('.decimal').change(function(){
		$(this).val(fn_number_format(fn_remove_comma_from_number($(this).val())));
	});

	$('.decimal').focus(function(){
		$(this).val(fn_remove_comma_from_number($(this).val()));
	});
});

function character_only(input){
	var regex = /[^a-z]/gi;
	input.value = input.value.replace(regex,'');
}

function refresh_user_list(){
	var data = {
		'action'	: 'refresh_user_list'
	}
	data = $.param(data);
	$.ajax({
		type	: 'post',
		dataType: 'json',
		data	: data,
		url		: handler,
		error	: function(data){
			alert('ajax error on refresh_user_list');
		}
	});
}

function get_account_info(){
	refresh_user_list();	
	var username = $('#hdn_username').val(); 
	var data = {
		'action'		: 'get_account_info',
		'username'		: username
	}
	data = $.param(data);
	$.ajax({
		type	: 'post',
		dataType: 'json',
		data	: data,
		url		: handler,
		success	: function(data){		
			if(Number(data['user_id'])==0||Number(data['u_user_type'])==8||(Number(data['u_division'])==0&&Number(data['u_user_type'])!==9)){
				window.location.href = './pages/pre_page/pre_error_page.inc.php';
			}
			else{
				if(Number(data['u_user_type'])==9){
					alert('Welcome Master '+data['name']+'!');
					window.location.href = './index.php?utp='+data['u_user_type']+'&dvsion=admin&page=users_list_page.inc.php';		
				}
				else{					
					switch(Number(data['u_division'])){
						case 1:
							var division = 'cn';
							break;
						case 2:
							var division = 'ts';
							break;
						case 3:
							var division = 'yf';
							break;
						default:
							window.location.href = './pages/pre_page/pre_error_page.inc.php';
							break;
					}	

					switch(data['u_secondary_division']){
						case '1':
							data['u_secondary_division'] = 'cn';
							break;
						case '2':
							data['u_secondary_division'] = 'ts';
							break;
						case '3':
							data['u_secondary_division'] = 'yf';
							break;
						default:
							data['u_secondary_division'] = '';
							break;
					}
					if(division=='yf'){
						switch(Number(data['u_department'])){
							case 1:
								window.location.href = './index.php?dvsion='+division+'&page=kitting_schedules.inc.php&sec_dvision='+data['u_secondary_division'];
								break;
							case 2:
								window.location.href = './index.php?dept=2&dvsion='+division+'&page=kitting_schedules.inc.php&sec_dvision='+data['u_secondary_division'];
								break;
							case 3:
								window.location.href = './index.php?dept=3&dvsion='+division+'&page=kitting_schedules.inc.php&sec_dvision='+data['u_secondary_division'];
								break;
							case 4:
								window.location.href = './index.php?dept=4&dvsion='+division+'&page=kitting_schedules.inc.php&sec_dvision='+data['u_secondary_division'];
								break;
							case 5:
								window.location.href = './index.php?dept=5&dvsion='+division+'&page=kitting_schedules.inc.php&sec_dvision='+data['u_secondary_division'];
								break;
							case 6:
								window.location.href = './index.php?dept=6&dvsion='+division+'&page=kitting_schedules.inc.php&sec_dvision='+data['u_secondary_division'];
								break;
							case 7:
								window.location.href = './index.php?dept=7&dvsion='+division+'&page=kitting_schedules.inc.php&sec_dvision='+data['u_secondary_division'];
								break;
							default:
								window.location.href = './pages/pre_page/pre_error_page.inc.php';
								break;
						}						
					}
					else{
						switch(Number(data['u_department'])){
							case 1:
								window.location.href = './index.php?dvsion='+division+'&page=product_orders.inc.php&sec_dvision='+data['u_secondary_division'];
								break;
							case 2:
								window.location.href = './index.php?dept=2&dvsion='+division+'&page=kitting_schedules.inc.php&sec_dvision='+data['u_secondary_division'];
								break;
							case 3:
								window.location.href = './index.php?dept=3&dvsion='+division+'&page=kitting_schedules.inc.php&sec_dvision='+data['u_secondary_division'];
								break;
							case 4:
								window.location.href = './index.php?dept=4&dvsion='+division+'&page=kitting_schedules.inc.php&sec_dvision='+data['u_secondary_division'];
								break;
							case 5:
								window.location.href = './index.php?dept=5&dvsion='+division+'&page=kitting_schedules.inc.php&sec_dvision='+data['u_secondary_division'];
								break;
							case 6:
								window.location.href = './index.php?dept=6&dvsion='+division+'&page=kitting_schedules.inc.php&sec_dvision='+data['u_secondary_division'];
								break;
							case 7:
								window.location.href = './index.php?dept=7&dvsion='+division+'&page=kitting_schedules.inc.php&sec_dvision='+data['u_secondary_division'];
								break;
							default:
								window.location.href = './pages/pre_page/pre_error_page.inc.php';
								break;
						}						
					}
				}
			}
		},
		error	: function(data){
			alert('ajax error on get_account_info');
		}
	});
}