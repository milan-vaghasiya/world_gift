<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div>
						
							<div class="col-md-3">
								<select name="created_by" id="created_by" class="form-control single-select">
									<option value="0">Select All Employee</option>
									<?php
										foreach ($empData as $row) :
											echo "<option value='" . $row->id . "'>" . $row->emp_name . "</option>";
										endforeach;
									?>
								</select>
								<div class="error created_by"></div>
                            </div> 
							<div class="col-md-2">   
                                <input type="date" name="from_date" id="from_date" class="form-control" value="<?=date('Y-m-01')?>" />
                                <div class="error fromDate"></div>
                            </div>     
                            <div class="col-md-3">  
                                <div class="input-group">
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
                                    <div class="input-group-append ml-2">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
									        <i class="fas fa-sync-alt"></i> Load
								        </button>
                                    </div>
                                </div>
                                <div class="error toDate"></div>
                            </div>                
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
                                    <tr class="text-center"><th colspan="8" class="headRow"><?=$pageHeader?></th><th colspan="2">Incentive</th></tr>
									<tr class="text-center">
										<th style="min-width:25px;">#</th>
										<th style="min-width:80px;">Invoice No.</th>
										<th style="min-width:80px;">Invoice Date</th>
										<th style="min-width:50px;">Product</th> Name</th>
										<th style="min-width:50px;">Qty.</th>
										<th style="min-width:50px;">Price</th>
										<th style="min-width:50px;">Discount</th>
										<th style="min-width:50px;">Amount</th>
										<th style="min-width:50px;">(%)</th>
										<th style="min-width:50px;">Amount</th>
									</tr>
								</thead>
								<tbody id="tbodyData"></tbody>
                                <tfoot class="thead-info" id="tfootData">
								   <tr>
									   <th colspan="4" class="text-right">Total</th>
									   <th>-</th><th>-</th><th>-</th><th>-</th><th>-</th><th>-</th>
								   </tr>
							   </tfoot>
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

jpReportTable('reportTable');
$(document).ready(function(){
    //loadData();
    $(document).on('click','.loaddata',function(){loadData();});  


function loadData(){
	$(".error").html("");
	var valid = 1;
	var created_by = $('#created_by').val();
	var from_date = $('#from_date').val();
	var to_date = $('#to_date').val();
	if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
	if(valid){
		$.ajax({
				url: base_url + controller + '/getUserWiseSale',
				data: {created_by:created_by, from_date:from_date, to_date:to_date},
				type: "POST",
				dataType:'json',
				success:function(data){
					if(data.status===0){
						$(".error").html("");
						$.each( data.field_error_message, function( key, value ) {$("."+key).html(value);});
					} else {
						$("#reportTable").dataTable().fnDestroy();
                        $(".headRow").html("");$(".headRow").html(data.headRow);
                        $("#tbodyData").html("");$("#tbodyData").html(data.tbody);
						$("#tfootData").html("");$("#tfootData").html(data.tfoot);
						jpReportTable('reportTable');
					}
				}
			});
	}
}
});

</script>