<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="card-title pageHeader">Item History</h4>
                            </div> 
						</div>                                   
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
                                    <tr class="text-center">
                                        <th colspan="8">Item History </th>
                                    </tr>
									<tr>
										<th style="min-width:25px;">#</th>
										<th style="min-width:100px;">Trans. Type</th>
										<th style="min-width:100px;">Ref. No.</th>
										<th style="min-width:50px;">Trans. Date</th>
										<th style="min-width:50px;">Inward</th>
										<th style="min-width:50px;">Outward</th>
										<th style="min-width:50px;">Balance</th>
										<th style="min-width:50px;">Party Name</th>
									</tr>
								</thead>
								<tbody id="tbodyData"><?=$tbody?></tbody>
                                <tfoot id="tfootData"><?=$tfoot?></tfoot>
							</table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>


<?php $this->load->view('includes/footer'); ?>
<?=$floatingMenu?>
<script>
$(document).ready(function(){
	reportTable();

	$(document).on('change','#item_type', function(e){
		var item_type = $(this).val();
		if(item_type){
			$.ajax({
                url: base_url + controller + '/getItemList',
                data: {item_type:item_type},
				type: "POST",
				dataType:'json',
				success:function(data){
					$("#item_id").html("");
					$("#item_id").html(data.itemData);
					$("#item_id").comboSelect();
                }
            });
		}
	});

    $(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
		var item_id = $('#item_id').val();
		if($("#item_id").val() == ""){$(".item_id").html("Item is required.");valid=0;}
		if(valid)
		{
            $.ajax({
                url: base_url + controller + '/getItemHistory',
                data: {item_id:item_id},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").dataTable().fnDestroy();
					$("#tbodyData").html(data.tbody);
					$("#tfootData").html(data.tfoot);
					reportTable();
                }
            });
        }
    });   
});
function reportTable()
{
	var reportTable = $('#reportTable').DataTable( 
	{
		responsive: true,
		scrollY: '55vh',
        scrollCollapse: true,
		"scrollX": true,
		"scrollCollapse":true,
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
		buttons: [ 'pageLength', 'excel'],
		"initComplete": function(settings, json) {$('body').find('.dataTables_scrollBody').addClass("ps-scrollbar");}
	});
	reportTable.buttons().container().appendTo( '#reportTable_wrapper toolbar' );
	$('.dataTables_filter .form-control-sm').css("width","97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('.dataTables_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
	return reportTable;
}
</script>