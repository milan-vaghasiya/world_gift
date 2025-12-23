$(document).ready(function(){
	attendanceTable()
	$(document).on('click',".attendanceInfo",function(){
        var attendance_id = $(this).data('id');
        var emp_name = $(this).data('emp_name');
        var emp_id = $(this).data('emp_id');

		$('#emp_id').val($(this).data('emp_id'));
		$('.emp_name').html(emp_name);
		$('.infotitle').html($(this).data('infotitle'));
		$('.totalhour').html($(this).data('totalhour'));
		$('.punch_in').html($(this).data('punch_in'));
		$('.punch_out').html($(this).data('punch_out'));
		$('.overtime').html($(this).data('overtime'));
		$('#attendanceInfo').modal();
        /* $.ajax({
            url:base_url + controller + '/attendanceInfo',
                type:'post',
                data:{id:purchase_id},
                dataType:'json',
                success:function(data){
                    
                }
        }); */
    });
});
function loadAttendanceSheet()
{
    var month = $("#month").val();
    $.ajax({
        url:base_url + controller + '/loadAttendanceSheet',
        type:'post',
        data:{month:month},
        dataType:'json',
        success:function(data){
            $("#attendanceTable").dataTable().fnDestroy();
            $("#theadData").html(data.thead);
            $("#tbodyData").html(data.tbody);
            attendanceTable();
        }
    });
}

function attendanceTable()
{
	var attendanceTable = $('#attendanceTable').DataTable( 
	{
		responsive: true,
		//'stateSave':true,
		"autoWidth" : false,
		order:[],
		"columnDefs": 	[
							{ type: 'natural', targets: 0 },
							{ orderable: false, targets: "_all" }, 
							{ className: "text-left", targets: [0,1] }, 
							{ className: "text-center", "targets": "_all" } 
						],
		pageLength:25,
		language: { search: "" },
		lengthMenu: [
            [ 10, 25, 50, 100, -1 ],[ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
        ],
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: [ 'pageLength', 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {loadAttendanceSheet();}}]
	});
	attendanceTable.buttons().container().appendTo( '#attendanceTable_wrapper toolbar' );
	$('#attendanceTable_filter .form-control-sm').css("width","97%");
	$('#attendanceTable_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('#attendanceTable_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
	return attendanceTable;
}

function printMonthlyAttendance(file_type)
{
    var month = $("#month").val();
	window.open(base_url + controller + '/printMonthlyAttendance/'+month+'/'+file_type, '_blank').focus();
}
function printMonthlySummary(file_type)
{
    var month = $("#month").val();
    var from_date = $("#from_date").val();
    var to_date = $("#to_date").val();
    var biomatric_id = $('#biomatric_id').val();
	window.open(base_url + controller + '/printMonthlySummary/'+from_date+'~'+to_date+'/'+biomatric_id+'/'+file_type, '_blank').focus();
}