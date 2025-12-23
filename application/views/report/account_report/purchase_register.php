<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
				<div class="card-header">
                        <div class="row form-group">
                            <div class="col-md-12">
                                <h4 class="card-title text-center pageHeader"><?=$pageHeader?></h4>
                            </div>
						</div>
						<hr>
						<div class="row form-group">
                            <div class="col-md-3">
								<select name="party_id" id="party_id" class="form-control single-select">
									<option value="0">Select All Party</option>
									<?php
										foreach ($customerData as $row) :
											echo "<option value='" . $row->id . "'>" . $row->party_name . "</option>";
										endforeach;
									?>
								</select>      
                            </div> 
							<div class="col-md-2">
								<select name="memo_type" id="memo_type" class="form-control single-select">
									<option value="CASH">Cash</option>
									<option value="DEBIT">Debit</option>
								</select>      
                            </div> 
							<div class="col-md-2">
								<select name="emp_id" id="emp_id" class="form-control single-select">
									<option value="0">Select All Employee</option>
									<?php
										foreach ($empData as $row) :
											echo "<option value='" . $row->id . "'>" . $row->emp_name . "</option>";
										endforeach;
									?>
								</select>      
                            </div> 
							<div class="col-md-2">   
                                <input type="date" name="from_date" id="from_date" class="form-control" value="<?=date('Y-m-01')?>" />
                                <div class="error fromDate"></div>
                            </div>     
                            <div class="col-md-3">  
                                <div class="input-group">
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-t')?>" />
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
                            <table id='commanTable' class="table table-bordered">
								<thead class="thead-info purchaseRegisterDataHead" id="theadData">
                                    <tr class="text-center"><th colspan="17"><?=$pageHeader?></th></tr>
									<tr>
										<th style="min-width:25px;">#</th>
										<th style="min-width:80px;">Cash/Debit</th>
										<th style="min-width:80px;">Vou. Date</th>
										<th style="min-width:50px;">Vou. No</th>
										<th style="min-width:100px;">Account Name</th>
										<th style="min-width:100px;">State</th>
										<th style="min-width:100px;">Gst Number</th>
										<th style="min-width:100px;">Item Name</th>
										<th style="min-width:50px;">HSN</th>
										<th style="min-width:50px;">Quantity</th>
										<th style="min-width:50px;">Price</th>
										<th style="min-width:50px;">GST(%)</th>
										<th style="min-width:100px;">Taxable Amount</th>
										<th style="min-width:100px;">Cgst Amount</th>
										<th style="min-width:100px;">Sgst Amount</th>
										<th style="min-width:100px;">Igst Amount</th>
										<th style="min-width:50px;">Vou. Amount</th>
									</tr>
								</thead>
								<tbody id="purchaseRegisterData"></tbody>
								<tfoot id="purchaseRegisterDataFoot"></tfoot>
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
	//loadData();
	jpReportTable('commanTable');
    $(document).on('click','.loaddata',function(){
		loadData();
	});  
});

function loadData(){
	$(".error").html("");
	var valid = 1;
	var from_date = $('#from_date').val();
	var to_date = $('#to_date').val();
	var party_id = $('#party_id').val();
	var emp_id = $('#emp_id').val();
	var memo_type = $('#memo_type').val();
	if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
	if(valid){
		$.ajax({
			url: base_url + controller + '/getPurchaseRegisterReport',
			data: {party_id:party_id,emp_id:emp_id,from_date:from_date, to_date:to_date,memo_type:memo_type},
			type: "POST",
			dataType:'json',
			success:function(data){
				// $("#commanTable").DataTable().clear().destroy();
				// $("#purchaseRegisterData").html("");
				// $("#purchaseRegisterData").html(data.tbody);
				// jpReportTable('commanTable');
				
				$("#commanTable").DataTable().clear().destroy();
				$("#purchaseRegisterData").html("");$("#purchaseRegisterData").html(data.tbody);
				$(".purchaseRegisterDataHead").html("");$(".purchaseRegisterDataHead").html(data.thead);
				$("#purchaseRegisterDataFoot").html("");$("#purchaseRegisterDataFoot").html(data.tfoot);
				jpReportTable('commanTable');
			}
		});
	}
}
</script>