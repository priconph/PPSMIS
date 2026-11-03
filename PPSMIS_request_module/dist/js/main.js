var handler = 'dist/handler/handler.php';

$(document).ready(function(){
	$(document).on('show.bs.modal', '.modal', function () {
		var zIndex = 1040 + (10 * $('.modal:visible').length);
		$(this).css('z-index', zIndex);
		setTimeout(function() {
			$('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
		}, 0);
	});

	$(function () {
		$('[data-toggle="popover"]').popover();
		$('[data-toggle="tooltip"]').tooltip();

		$("form").prop("autocomplete","off");
		$("form input").prop("autocomplete","off");

		$('input[readonly]').prop("tabindex",-1);
		$('form').find('textarea').each(function(i, obj) {
			if( $(this).prop("maxlength") == -1 ){
				$(this).prop("maxlength",500);
			}
			if( $(this).prop("placeholder") == "" ){
				$(this).prop("placeholder","Type here...");
			}
		});
		$('form').find("input[type=number]").each(function(i, obj){
			if( $(this).prop("min") == "" ){
				$(this).prop("min",0);
			}
			if( $(this).prop("placeholder") == "" ){
				$(this).prop("placeholder",0);
			}
		});
		$('form').find(".placeholder_search").each(function(i, obj){
			if( $(this).prop("placeholder") == "" ){
				$(this).prop("placeholder","Search...");
			}
		});
		$('form').find("input[type=text]").each(function(i, obj){
			if( $(this).prop("placeholder") == "" ){
				$(this).prop("placeholder","---");
			}
		});


		$(".with_asterisk").after(' <span class="text-danger" title="Required">*</span>');

	});
});

function number_with_comma(val){
	while (/(\d+)(\d{3})/.test(val.toString())){
		val = val.toString().replace(/(\d+)(\d{3})/, '$1'+','+'$2');
	}
	return val;
}

function init_plugins(){
	$('[data-toggle="popover"]').popover();
	$('[data-toggle="tooltip"]').tooltip();
}

var pop_up_timeout;
function show_popup_message(parent_container,alert_type,icon,body){
	var icons = [];
	icons['alert-info'] 		= '<i class="fa fa-info-circle"></i>';
	icons['alert-warning'] 		= '<i class="fa fa-exclamation-triangle"></i>';
	icons['alert-danger'] 		= '<i class="fa fa-exclamation-triangle"></i>';
	icons['alert-success'] 		= '<i class="fa fa-check-circle"></i>';

	if(icon == 'default'){
		icon = icons[alert_type];
	}
	//else icon has its own text

	var alert = '<div class="alert '+alert_type+' collapse">';
	alert += '	<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
	alert += '		<span aria-hidden="true">&times</span>';
	alert += '	</button>';
	alert += '	<span>'+icon+'</span>&nbsp;';
	alert += '	<span>'+body+'</span>&nbsp;';
	alert += '</div>';
	$(parent_container).html(alert);
	$(parent_container).find('.alert').fadeIn(300);
	clearTimeout(pop_up_timeout);
	pop_up_timeout = setTimeout(function(){
		$(parent_container).find('.alert').fadeOut(300);
	},4000);
}