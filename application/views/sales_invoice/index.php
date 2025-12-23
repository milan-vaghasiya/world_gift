<?php $this->load->view('includes/header');
	$etype = "6,7,8";
?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
							<div class="col-md-6">
                                <h4 class="card-title">Tax Invoice</h4>
							</div>
							<div class="col-md-6"> 
							    <a href="<?=base_url($headData->controller."/addInvoice")?>" class="btn waves-effect waves-light btn-outline-primary float-right permission-write ml-2"><i class="fa fa-plus"></i> Add Invoice</a>
							</div>                          
                        </div>   
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"> <button onclick="statusTab('salesInvoiceTable',0);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending Audit</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('salesInvoiceTable',1);" class=" btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed Audit</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('salesInvoiceTable',3);" class=" btn waves-effect waves-light btn-outline-dark" style="outline:0px" data-toggle="tab" aria-expanded="false">Canceled Inv.</button> </li>
                                	<li class="nav-item"> <a href="<?=base_url($headData->controller."/salesInvoice_pdf")?>" target="_blank" class="btn waves-effect waves-light btn-outline-primary" ><i class="fas fa-print"></i></a> </li>
                                </ul>
                            </div>
                            <div class="col-md-6"> 
                                <div class="input-group">
									<input type="date" name="from_date" id="from_date" class="form-control" value="<?=date('Y-m-01')?>" min="<?=$startYearDate?>" max="<?=$endYearDate?>" />
									<div class="error fromDate"></div>
									<input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" min="<?=$startYearDate?>" max="<?=$endYearDate?>" />
									<select name="disc_filture" id="disc_filture" class="form-control single-select" style="width:30%">
										<option value="0">Select All</option>
										<option value="1">With Discount</option>
										<option value="2">Without Discount</option>
									</select>
									<div class="input-group-append">
										<button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
											<i class="fas fa-sync-alt"></i> Load
										</button>
									</div>
								</div>
							</div>  
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='salesInvoiceTable' class="table table-bordered ssTable" data-url='/getDTRows/'.<?=$etype?>></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<div class="modal fade" id="itemModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Item List</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="party_so" method="post" action="">
                <div class="modal-body">
                    <div class="col-md-12"><b>Party Name : <span id="partyName"></span></b></div>
                    <input type="hidden" name="party_id" id="party_id" value="">
                    <input type="hidden" name="party_name" id="party_name" value="">
                    <input type="hidden" name="from_entry_type" id="from_entry_type" value="4">
                    <div class="col-md-12">
                        <div class="error general"></div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-info">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Item Description</th>
                                        <th class="text-center">HSN/SAC</th>
                                        <th class="text-center">GST <small>%</small></th>
                                        <th class="text-center">Qty.</th>
                                        <th class="text-center">UOM</th>
                                        <th class="text-center">Rate<br><small></small></th>
                                        <th class="text-center">Amount<br><small></small></th>
                                    </tr>
                                </thead>
                                <tbody id="itemData">
                                    <tr>
                                        <td class="text-center" colspan="5">No Data Found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
  
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="print_dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog" style="min-width:30%;">
		<div class="modal-content animated zoomIn border-light">
			<div class="modal-header bg-light">
				<h5 class="modal-title text-dark"><i class="fa fa-print"></i> Print Options</h5>
				<button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="printModel" method="post" action="<?=base_url($headData->controller.'/invoice_pdf')?>" target="_blank">
				<div class="modal-body">
					<div class="col-md-12">
						<div class="row">
							<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" name="original" id="original" class="filled-in chk-col-success" value="1" checked>
									<label for="original">Original</label>
								</div>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" name="duplicate" id="duplicate" class="filled-in chk-col-success" value="0">
									<label for="duplicate">Duplicate</label>
								</div>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" name="triplicate" id="triplicate" class="filled-in chk-col-success" value="0">
									<label for="triplicate">Triplicate</label>
								</div>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" name="header_footer" id="header_footer" class="filled-in chk-col-success" value="1" checked>
									<label for="header_footer">Header/Footer</label>
								</div>
							</div>
							<div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
								<label>No. of Extra Copy</label>
								<input type="text" name="extra_copy" id="extra_copy" class="form-control" value="0">
								<input type="hidden" name="printsid" id="printsid" value="0">
								<label class="error_extra_copy text-danger"></label>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<a href="#" data-dismiss="modal" class="btn btn-secondary"><i class="fa fa-times"></i> Close</a>
					<button type="submit" class="btn btn-success" onclick="closeModal('print_dialog');"><i class="fa fa-print"></i> Print</button>
				</div>
			</form>
		</div>
	</div>
</div>

<?php $this->load->view('includes/footer'); ?>
<!-- <script src="<?=base_url()?>assets/js/custom/typehead.js"></script> --> 
<script src="<?=base_url()?>assets/js/custom/e-bill.js?v=<?=time()?>"></script> 
<script>
	$(document).ready(function(){
		$(document).on('click','.createItemList',function(){		
			var id = $(this).data('id');
			var party_name = $(this).data('party_name');

			$.ajax({
				url : base_url + controller + '/getItemList',
				type: 'post',
				data:{id:id},
				dataType:'json',
				success:function(data){
					$("#itemModal").modal();
					$("#partyName").html(party_name);
					$("#party_name").val(party_name);
					$("#party_id").val(party_id);
					$("#itemData").html("");
					$("#itemData").html(data.htmlData);
				}
			});
		});

		<?php if(!empty($printID)): ?>
			$("#printModel").attr('action',base_url + controller + '/invoice_pdf');
			$("#printsid").val(<?=$printID?>);
			$("#print_dialog").modal();
		<?php endif; ?>
	
	
		$(document).on("click",".printInvoice",function(){
			$("#printModel").attr('action',base_url + controller + '/invoice_pdf');
			$("#printsid").val($(this).data('id'));
			$("#print_dialog").modal();
		});
		
		// Print Invoice in Thermal Printer
		$(document).on("click",".invoiceThermalPrint",function(){
			var sendData = { sales_id:$(this).data('id')};
			var url = base_url + controller + '/invoiceThermalPrintPdf/'+$(this).data('id');
			//var url = base_url + controller + '/getOutstanding/' + encodeURIComponent(window.btoa(JSON.stringify(postData)));
			window.open(url);
			/*jQuery.ajax({
				url: base_url + controller + '/invoiceThermalPrintPdf',
				data:sendData,
				type: "POST",
				dataType:"json",
				success:function(data) {
					newWin= window.open("",'');
					newWin.document.write('<link href="<?=base_url();?>assets/css/jp_helper.css?v=<?=time()?>" rel="stylesheet">');
					newWin.document.write(data.printData);
					newWin.document.close();
					setTimeout(function()
					{ 
						newWin.focus();
						newWin.print();
						newWin.close();
						
					}, 100);
				}
			});*/
		});

		$(document).on("change",".entry_type",function(){
			$("#salesInvoiceTable").attr("data-url",'/getDTRows/'+$("#entry_type").val());
    		ssTable.state.clear();initTable();
		});
		
		//CREATED By Karmi 14/12/2021
		$(document).on('click','.loaddata',function(){
			var from_date = $('#from_date').val();
			var to_date = $('#to_date').val();
			var disc_filture = $('#disc_filture').val();
			initInvTable(from_date,to_date,disc_filture);
		});
		
		$(document).on('click',".auditStatus",function(){
			var id = $(this).data('id');
			var val = $(this).data('val');
			var msg= $(this).data('msg');
			$.confirm({
				title: 'Confirm!',
				content: 'Are you sure want to '+ msg +' this Sales Invoice?',
				type: 'green',
				buttons: {   
					ok: {
						text: "ok!",
						btnClass: 'btn waves-effect waves-light btn-outline-success',
						keys: ['enter'],
						action: function(){
							$.ajax({
								url: base_url + controller + '/auditStatus',
								data: {id:id,val:val,msg:msg},
								type: "POST",
								dataType:"json",
								success:function(data)
								{
									if(data.status==0)
									{
										toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
									}
									else
									{
										initTable(); 
										toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
										//window.location.reload();
									}
								}
							});
						}
					},
					cancel: {
						btnClass: 'btn waves-effect waves-light btn-outline-secondary',
						action: function(){
		
						}
					}
				}
		    });
		});
	});

	function closeModal(modalId)
	{
		$("#"+ modalId).modal('hide');
		
		<?php if(!empty($printID)): ?>
			window.location = base_url + controller;
		<?php endif; ?>
	}
	
	//CREATED By Karmi 14/12/2021
	function initInvTable(from_date,to_date,disc_filture){
		$('.ssTable').dataTable().fnDestroy();
		var tableOptions = {pageLength: 25,'stateSave':false};
		var tableHeaders = {'theads':'','textAlign':textAlign,'srnoPosition':1};
		var dataSet = {from_date:from_date, to_date:to_date, disc_amt:disc_filture}
		ssDatatable($('.ssTable'),tableHeaders,tableOptions,dataSet);
	}

	function orderTable(){
		var orderTable = $('#orderTable').DataTable( 
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
			pageLength:100,
			language: { search: "" },
			lengthMenu: [
				[ 10, 25, 50, 100, -1 ],[ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
			],
			dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
			buttons: [] //[ 'pageLength', 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {loadAttendanceSheet();}}]
		});
		orderTable.buttons().container().appendTo( '#orderTable_wrapper toolbar' );
		$('.dataTables_filter .form-control-sm').css("width","97%");
		$('.dataTables_filter .form-control-sm').attr("placeholder","Search.....");
		$('.dataTables_filter').css("text-align","left");
		$('.dataTables_filter label').css("display","block");
		$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
		$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
		return orderTable;
	}
</script>
