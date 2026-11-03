$.ajaxSetup({
	type	: 'post',
	dataType: 'json',
    url     : './ppswarehouseHandler',
});

$(document).ready(function(){

// 04042024
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function ReplaceNumberWithCommas(NumberWithCommas) {
    var n = NumberWithCommas.toString().split(".");
    n[0] = n[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    return n.join(".");
}

display_resin_summary1();
function display_resin_summary1(){
            
    var data = {
        'action'		: 'display_resin_summary1'
    }
    
    data = $.param(data);		
    $.ajax({
        
        data	: data,
        success	: function(data){
            console.log(data)
            var ctr 					= data['ctr'];			
            
            $('#display_resin_summary1').empty();
            
            var content  = '<table id="tbl_ResinMain" class="table table-striped table-hover table-compact cell-border" width="100%" style="font-size: 9pt; border: solid 1px gray;">';
                content += '	<thead>';
                content += '		<tr>';
                content += '			<th hidden></th>';
                content += '			<th>Classification</th>';
                content += '			<th>Allocation</th>';
                content += '			<th>Matl. Code</th>';
                content += '			<th>Description</th>';
                content += '			<th>WHS Stock</th>';
                content += '			<th>Remarks</th>';
                content += '		</tr>';
                content += '	</thead>';
                content += '	<tbody>';
                content += '	</tbody>';
                content += '</table>';
                
            $('#display_resin_summary1').append(content);
            
            for(i=0;i<ctr;i++){
                
                var total_balance = 0;

                var EOH = parseFloat(data['EOH'][i]).toFixed(2);
                var EOH = ReplaceNumberWithCommas(EOH);
                
                var Classification = data['Classification'][i];
                var ProductLine = data['ProductLine'][i];
                var PartNumber = data['PartNumber'][i];
                var MaterialType = data['MaterialType'][i];

                if (Classification == '' || Classification == null) {
                    var Classification = '---';
                } else {
                    var Classification = data['Classification'][i];
                }

                if (ProductLine == '' || ProductLine == null) {
                    var ProductLine = '---';
                } else {
                    var ProductLine = data['ProductLine'][i];
                }

                if (PartNumber == '' || PartNumber == null) {
                    var PartNumber = '---';
                } else {
                    var PartNumber = data['PartNumber'][i];
                }

                if (MaterialType == 'CT6009-VE' || MaterialType == 'CT5869' || MaterialType == 'CT5870-VE') {
                    var Remarks = '<font color="green"><b> YEC Stocks </b></font>';
                } else {
                    var Remarks = '---';
                }                    

            var values  = '<tr align="center" id='+data['id'][i]+'>';
                values += '		<td class="id" style="display:none;">' + data['id'][i] +'</td>';
                values += '		<td>' + Classification + '</td>';
                values += '		<td>' + ProductLine+'</td>';
                values += '		<td class="PartNumber">' + PartNumber+'</td>';
                values += '		<td>'+data['MaterialType'][i]+'</td>';
                values += '		<td><font color="blue"><b>' + EOH +' </b></font></td>';
                values += '		<td>' + Remarks + '</td>';
                values += '</tr>';
                
                $('#tbl_ResinMain tbody').append(values);
            }
            
            $("#tbl_ResinMain tr").dblclick(function () {
                var id = $(this).find('.id').text();
                var PartNumber = $(this).find('.PartNumber').text();
                window.location.href = "./updateResin.php?id=" + id + "&PartNumber=" + PartNumber;
            });

            $('#tbl_ResinMain').DataTable({ 
                paginate: true, 
                aaSorting: [],
                });
                                    
            
        },
        error	: function(data){
            console.log(data);
        }
    });
    
}

    display_resin_summary1();
    function display_resin_summary1() {

        var data = {
            'action': 'display_resin_summary1'
        }

        data = $.param(data);
        $.ajax({

            data: data,
            success: function (data) {
                console.log(data)
                var ctr = data['ctr'];

                $('#display_resin_summary1').empty();

                var content = '<table id="tbl_ResinMain" class="table table-striped table-hover table-compact cell-border" width="100%" style="font-size: 9pt; border: solid 1px gray;">';
                content += '	<thead>';
                content += '		<tr>';
                content += '			<th hidden></th>';
                content += '			<th>Classification</th>';
                content += '			<th>Allocation</th>';
                content += '			<th>Matl. Code</th>';
                content += '			<th>Description</th>';
                content += '			<th>WHS Stock</th>';
                content += '			<th>Remarks</th>';
                content += '		</tr>';
                content += '	</thead>';
                content += '	<tbody>';
                content += '	</tbody>';
                content += '</table>';

                $('#display_resin_summary1').append(content);

                for (i = 0; i < ctr; i++) {

                    var total_balance = 0;

                    var EOH = parseFloat(data['EOH'][i]).toFixed(2);
                    var EOH = ReplaceNumberWithCommas(EOH);

                    var Classification = data['Classification'][i];
                    var ProductLine = data['ProductLine'][i];
                    var PartNumber = data['PartNumber'][i];
                    var MaterialType = data['MaterialType'][i];

                    if (Classification == '' || Classification == null) {
                        var Classification = '---';
                    } else {
                        var Classification = data['Classification'][i];
                    }

                    if (ProductLine == '' || ProductLine == null) {
                        var ProductLine = '---';
                    } else {
                        var ProductLine = data['ProductLine'][i];
                    }

                    if (PartNumber == '' || PartNumber == null) {
                        var PartNumber = '---';
                    } else {
                        var PartNumber = data['PartNumber'][i];
                    }

                    if (MaterialType == 'CT 6009-VE') {
                        var Remarks = '<font color="green"><b> Stamping Stocks </b></font>';
                    } else {
                        var Remarks = '---';
                    }

                    var values = '<tr align="center" id=' + data['id'][i] + '>';
                    values += '		<td class="id" style="display:none;">' + data['id'][i] + '</td>';
                    values += '		<td>' + Classification + '</td>';
                    values += '		<td>' + ProductLine + '</td>';
                    values += '		<td class="PartNumber">' + PartNumber + '</td>';
                    values += '		<td>' + data['MaterialType'][i] + '</td>';
                    values += '		<td><font color="blue"><b>' + EOH + ' </b></font></td>';
                    values += '		<td>' + Remarks + '</td>';
                    values += '</tr>';

                    $('#tbl_ResinMain tbody').append(values);
                }

                $("#tbl_ResinMain tr").dblclick(function () {
                    var id = $(this).find('.id').text();
                    var PartNumber = $(this).find('.PartNumber').text();
                    window.location.href = "./updateResin.php?id=" + id + "&PartNumber=" + PartNumber;
                });

                $('#tbl_ResinMain').DataTable({
                    paginate: true,
                    aaSorting: [],
                });


            },
            error: function (data) {
                console.log(data);
            }
        });

    }

    display_resin_summary3();
    function display_resin_summary3() {

        var data = {
            'action': 'display_resin_summary3'
        }

        data = $.param(data);
        $.ajax({

            data: data,
            success: function (data) {
                console.log(data)
                var ctr = data['ctr'];

                $('#display_resin_summary3').empty();

                var content = '<table id="tbl_ResinMain3" class="table table-striped table-hover table-compact cell-border" width="100%" style="font-size: 9pt; border: solid 1px gray;">';
                content += '	<thead>';
                content += '		<tr>';
                content += '			<th hidden></th>';
                content += '			<th>Classification</th>';
                content += '			<th>Allocation</th>';
                content += '			<th>Matl. Code</th>';
                content += '			<th>Description</th>';
                content += '			<th>WHS Stock</th>';
                content += '			<th>Remarks</th>';
                content += '		</tr>';
                content += '	</thead>';
                content += '	<tbody>';
                content += '	</tbody>';
                content += '</table>';

                $('#display_resin_summary3').append(content);

                for (i = 0; i < ctr; i++) {

                    var total_balance = 0;

                    var EOH = parseFloat(data['EOH'][i]).toFixed(2);
                    var EOH = ReplaceNumberWithCommas(EOH);

                    var Classification = data['Classification'][i];
                    var ProductLine = data['ProductLine'][i];
                    var PartNumber = data['PartNumber'][i];
                    var MaterialType = data['MaterialType'][i];

                    if (Classification == '' || Classification == null) {
                        var Classification = '---';
                    } else {
                        var Classification = data['Classification'][i];
                    }

                    if (ProductLine == '' || ProductLine == null) {
                        var ProductLine = '---';
                    } else {
                        var ProductLine = data['ProductLine'][i];
                    }

                    if (PartNumber == '' || PartNumber == null) {
                        var PartNumber = '---';
                    } else {
                        var PartNumber = data['PartNumber'][i];
                    }

                    if (MaterialType == 'CT6009-VE' || MaterialType == 'CT5869' || MaterialType == 'CT5870-VE') {
                        var Remarks = '<font color="green"><b> Stamping Stocks </b></font>';
                    } else {
                        var Remarks = '---';
                    } 

                    var values = '<tr align="center" id=' + data['id'][i] + '>';
                    values += '		<td class="id" style="display:none;">' + data['id'][i] + '</td>';
                    values += '		<td>' + Classification + '</td>';
                    values += '		<td>' + ProductLine + '</td>';
                    values += '		<td class="PartNumber">' + PartNumber + '</td>';
                    values += '		<td>' + data['MaterialType'][i] + '</td>';
                    values += '		<td><font color="blue"><b>' + EOH + ' </b></font></td>';
                    values += '		<td>' + Remarks + '</td>';
                    values += '</tr>';

                    $('#tbl_ResinMain3 tbody').append(values);
                }

                $("#tbl_ResinMain3 tr").dblclick(function () {
                    var id = $(this).find('.id').text();
                    var PartNumber = $(this).find('.PartNumber').text();
                    window.location.href = "./updateResin.php?id=" + id + "&PartNumber=" + PartNumber;
                });

                $('#tbl_ResinMain3').DataTable({
                    paginate: true,
                    aaSorting: [],
                });


            },
            error: function (data) {
                console.log(data);
            }
        });

    }





});	
