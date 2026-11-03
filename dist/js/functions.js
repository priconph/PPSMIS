$(document).ready(function(){
	$('.pull-down').each(function(){
		$(this).css('padding-top',$(this).parent().height()-$(this).height()-26);
	});	

	$(".monthPicker").datepicker({
        dateFormat: 'MM yy',
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,

        onClose: function(dateText, inst) {
            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
        }
    });

    $(".monthPicker").focus(function () {
        $(".ui-datepicker-calendar").hide();
        $("#ui-datepicker-div").position({
            my: "center top",
            at: "center bottom",
            of: $(this)
        });
    });

    $('.monthPicker').keypress(function(e) {
	    e.preventDefault();
	});
});

function topFunction() {
    document.body.scrollTop = 0; // For Chrome, Safari and Opera 
    // document.documentElement.scrollTop = 0; // For IE and Firefox
}

function fn_number_format(x){
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function fn_remove_comma_from_number(x){
	return x.replace(/[^\d\.\-\ ]/g, '');
}

function date_range_picker(id,type){		
	switch(type){
		case 'month' :
			$('#'+id).val(moment().startOf('month').format('MM/DD/YYYY')+' - '+moment().endOf('month').format('MM/DD/YYYY'));
			$('#'+id).daterangepicker({
				ranges: {
					'Last 3 Months': [moment().subtract(2,'month').startOf('month'), moment().endOf('month')],
					'This Month': [moment().startOf('month'), moment().endOf('month')],
					'Last Month': [moment().subtract(1,'month').startOf('month'), moment().subtract(1,'month').endOf('month')]
				},
				startDate: moment().startOf('month').startOf('month'),
				endDate: moment().endOf('month').endOf('month')
			});
			break;
		case '3months' :
			$('#'+id).val(moment().subtract(2,'month').startOf('month').format('MM/DD/YYYY')+' - '+moment().endOf('month').format('MM/DD/YYYY'));
			$('#'+id).daterangepicker({
				ranges: {
					'Last 3 Months': [moment().subtract(2,'month').startOf('month'), moment().endOf('month')],
					'This Month': [moment().startOf('month'), moment().endOf('month')],
					'Last Month': [moment().subtract(1,'month').startOf('month'), moment().subtract(1,'month').endOf('month')]
				},
				startDate: moment().subtract(2,'month').startOf('month').startOf('month'),
				endDate: moment().endOf('month').endOf('month')
			});
			break;
		case 'today' :
			$('#'+id).val(moment().format('MM/DD/YYYY')+' - '+moment().format('MM/DD/YYYY'));
			$('#'+id).daterangepicker({
				ranges: {
					'Last 7 Days': [moment().subtract(6,'days'), moment()],
					'Last 30 Days': [moment().subtract(29,'days'), moment()],
					'Yesterday': [moment().subtract(1,'days'), moment().subtract(1,'days')],
					'Today': [moment(), moment()],
					'Tomorrow': [moment().add(1,'days'), moment().add(1,'days')],
					'This Month': [moment().startOf('month'), moment().endOf('month')],
					'Last Month': [moment().subtract(1,'month').startOf('month'), moment().subtract(1,'month').endOf('month')]
				},
				startDate: moment(),
				endDate: moment()
			});
			break;
		default :
			break;
	}

	return false;
}

function stp_chosen_dropdown(){
	var config = {
      '.chosen-select'           : {},
      '.chosen-select-deselect'  : {allow_single_deselect:true},
      '.chosen-select-no-single' : {disable_search_threshold:10},
      '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
      '.chosen-select-width'     : {width:"95%"}
    }
    
    for (var selector in config) {
      $(selector).chosen(config[selector]);
    }
}

function add_automatic_comma(id){
	$('#'+id).keypress(function(event){
	  if(event.which >= 37 && event.which <= 40){
	      event.preventDefault();
	  }
	  var $this = $(this);
	 	var num = $this.val().replace(/(\s)/g, '');
			$this.val(num.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1 "));
	});
}

function fn_filterable_table(id,cont){
	$('#'+id+' thead .filterable').each(function(){
		var title = $(this).attr('id');
		var width = $(this).width();

		$(this).empty();
		if(id=='t_po_status'){
			var width = $(this).attr('class');
			exploded_width = width.split(' ');
			$(this).append('<input style="border:0;text-align:center;color:white;width:'+exploded_width[0]+'px" type="text" placeholder="'+title+'" />');
		}
		else if(id='t_product_lines'){
			$(this).append('<input style="font-size:90%;font-family: FontAwesome,Calibri;color:white;border:0;text-align:center;width:'+width+'px" type="text" placeholder="&#xF002 '+title+'" />');
		}
		else{
			$(this).append('<input style="color:white;border:0;text-align:center;width:'+width+'px" type="text" placeholder="'+title+'" />');
		}
    });
	 
	var state = cont.state.loaded();
	if(state){
		cont.columns().eq(0).each(function (colIdx){
			var colSearch = state.columns[colIdx].search;
			if(colSearch.search ) {
				$('input',cont.column(colIdx ).header()).val(colSearch.search);
			}
		});
		cont.draw();
	}

    cont.columns().every(function(){
        var that = this; 
        $('input',this.header()).on('keyup change',function(){
            if(that.search()!==this.value){
                that.search(this.value).draw();
            }
        });
    });
}

function fn_filterable_tfoot_table(id,cont){
    $('#'+id+' tfoot .filterable').each(function(){
        var title = $(this).text();
		var width = $(this).width();
        $(this).html('<input type="text" style="font-size:90%;font-family: FontAwesome,Calibri;color:auto;box-shadow: 0 0 5px rgba(116, 149, 204, 1);border: none;border-radius:2px;text-align:center;width:'+width+'px"/>');
    });
 
	var state = cont.state.loaded();
	if(state){
		cont.columns().eq(0).each(function (colIdx){
			var colSearch = state.columns[colIdx].search;
			if(colSearch.search ) {
				$('input',cont.column(colIdx ).footer()).val(colSearch.search);
			}
		});
		cont.draw();
	}

    cont.columns().every(function(){
        var that = this;	 
        $('input',this.footer()).on( 'keyup change',function(){
            if(that.search()!==this.value){
            	that.search(this.value).draw();
            }
        });
    });    
}

function fn_filterable_thead_table(id,cont){
    $('#'+id+' thead .filterable').each( function () {
        var title = $(this).text();
		var width = $(this).width();
		width = width+10;	
        $(this).html( '<input type="text" style="font-size:90%;font-family: FontAwesome,Calibri;color:auto;box-shadow: 0 0 5px rgba(81, 203, 238, 1);border: none;border-radius:2px;text-align:center;width:'+width+'px;" placeholder="&#xF002 '+title+'" />' );
    } );
 
	var state = cont.state.loaded();
	if(state){
		cont.columns().eq(0).each(function (colIdx){
			var colSearch = state.columns[colIdx].search;
			if(colSearch.search ) {
				$('input',cont.column(colIdx ).footer()).val(colSearch.search);
			}
		});
		cont.draw();
	}

    cont.columns().every(function(){
        var that = this;	 
        $('input',this.footer()).on( 'keyup change',function(){
            if(that.search()!==this.value){
            	that.search(this.value).draw();
            }
        } );
    });    
}

function wait_for_delay(ms){
	var start = new Date().getTime();
	var end = start;
	while(end<start+ms){
		end = new Date().getTime();
	}
}