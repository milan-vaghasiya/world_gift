<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="card-title text-center">ITEM/PRODUCT LEDGER</h4>
                            </div>                       
                        </div>
                        <hr style="width:100%;">
                        <div class="row">
                            <div class="col-md-2">  
								<select id="location_id" class="form-control single-select req float-right">
									<?php
									    foreach($locationList as $row){echo '<option value="'.$row->id.'">'.$row->location.'</option>';}
									?>
								</select>
							</div>
							<div class="col-md-3">  
								<select id="category_id" class="form-control single-select float-right">
									<option value="">Select All</option>
									<?php
										foreach ($categoryList as $row) :
											$selected = (!empty($dataRow->category_id) && $dataRow->category_id == $row->id) ? "selected" : "";
											echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->category_name . '</option>';
										endforeach;
									?>
								</select>
							</div>
							<div class="col-md-2">
                                <select name="hsn_code" id="hsn_code" class="form-control single-select float-right">
									<option value="">Select ALL</option>
									<?php
										foreach ($hsnList as $row) :
										    if(!empty($row->hsn_code)):
											    echo '<option value="' . $row->hsn_code . '">' . $row->hsn_code . '</option>';
											endif;
										endforeach;
									?>
								</select>
                            </div>
                            <div class="col-md-2">
                                <select name="stock_type" id="stock_type" class="form-control single-select">
                                    <option value="1">With Zero</option>
                                    <option value="2">Without Zero</option>
                                </select>
                            </div>
                            <div class="col-md-3">  
                                <div class="input-group">
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
                                    <div class="input-group-append">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
    								        <i class="fas fa-sync-alt"></i> Load
    							        </button>
                                    </div>
                                    <div class="error toDate"></div>
                                </div>
                            </div>                                        
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
                                    <tr class="text-center">
                                        <th colspan="7">Stock Register</th>
                                    </tr>
									<tr>
										<th>#</th>
										<th>Item Description</th>
										<th>Receipt Qty.</th>
										<th>Issued Qty.</th>
										<th>Balance Qty.</th>
										<th>Amount</th>
										<th>Balance Qty.</th>
									</tr>
								</thead>
								<tbody id="tbodyData"></tbody>
							</table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
	reportTable();
	dataSet = {};
    getDynamicHSNList(dataSet);
        
    $(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
		var to_date = $('#to_date').val();
		var category_id = $('#category_id').val();
		var item_type = '1,3';$('#item_type').val();   
		var location_id = $('#location_id').val();  
		var hsn_code = $('#hsn_code :selected').val();
		var stock_type = $('#stock_type').val();  
		
		if($("#item_type").val() == ""){$(".item_type").html("Item Type is required.");valid=0;}
		if($("#to_date").val() == ""){$(".toDate").html("Date is required.");valid=0;}
	
		if(valid)
		{
            $.ajax({
                url: base_url + controller + '/getStockRegister',
                data: {location_id:location_id,item_type:item_type,category_id:category_id,to_date:to_date,hsn_code:hsn_code,stock_type:stock_type},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").dataTable().fnDestroy();
					$("#tbodyData").html(data.tbody);
					$("#theadData").html(data.thead);
					$(".totalInventory").html(data.totalInventory);
					$(".totalUP").html(data.totalUP);
					$(".totalValue").html(data.totalValue);
					//jpReportTable('reportTable');
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
		buttons: [ 'pageLength', 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {loadAttendanceSheet();}}]
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
