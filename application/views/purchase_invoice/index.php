<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Purchase Invoice</h4>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
									<input type="date" name="from_date_f" id="from_date_f" class="form-control" value="<?=date('Y-m-01')?>" />
									<div class="error fromDate"></div>
									<input type="date" name="to_date_f" id="to_date_f" class="form-control" value="<?=date('Y-m-d')?>" />
									<div class="input-group-append">
										<button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
											<i class="fas fa-sync-alt"></i> Load
										</button>
										<a href="<?=base_url($headData->controller."/addPurchaseInvoice")?>" class="btn waves-effect waves-light btn-outline-primary float-right"><i class="fa fa-plus"></i> Add Invoice</a>
									</div>
								</div>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='purchaseInvoiceTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
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
                                        <td class="text-center" colspan="8">No Data Found</td>
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

<div class="modal fade" id="print_tags" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog" style="min-width:30%;">
		<div class="modal-content animated zoomIn border-light">
			<div class="modal-header bg-light">
				<h5 class="modal-title text-dark"><i class="fa fa-print"></i> Print Tags</h5>
				<button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="printModel" method="post" action="<?=base_url($headData->controller.'/printTags')?>" target="_blank">
				<div class="modal-body">
					<div class="col-md-12">
						<div class="row">
							
                            <table class="table table-bordered">
                                <thead class="thead-info">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Item Description</th>
                                        <th>No. of Tag</th>
                                        <th class="text-center">Qty.</th>
                                        <th class="text-center">Current Stock.</th>
                                    </tr>
                                </thead>
                                <tbody id="itemTagData">
                                    <tr>
                                        <td class="text-center" colspan="4">No Data Found</td>
                                    </tr>
                                </tbody>
                            </table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<a href="#" data-dismiss="modal" class="btn btn-secondary"><i class="fa fa-times"></i> Close</a>
					<button type="submit" class="btn btn-success printTags1" data-id="" onclick="closeModal('print_tags');"><i class="fa fa-print"></i> Print</button>
				</div>
			</form>
		</div>
	</div>
</div>


<?php $this->load->view('includes/footer'); ?>

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
        
        $(document).on("click",".printTagsModal",function(){
            $("#printModel").attr('action',base_url + controller + '/printTags');
            $("#printsid").val(id);
            $(".printTags").data('id',$(this).data('id'));
            var id = $(this).data('id');
            $("#print_tags").modal();
    
            $.ajax({
                url : base_url + controller + '/getItemListForTag',
                type: 'post',
                data: {id:id},
                dataType:'json',
                success:function(data){
                    $("#itemTagData").html("");
                    $("#itemTagData").html(data.htmlData);
                }
            });
        });
    
        $(document).on("click",".printTags",function(){
            var sendData = { id:$(this).data('id'),tag_qty:$("#tag_qty").val() };
            jQuery.ajax({
                url: base_url + controller + '/printTags',
                data:sendData,
                type: "POST",
                dataType:"json",
                success:function(data) {
                    newWin= window.open("",'');
                    newWin.document.write(data.printData);
                    newWin.document.close();
                    setTimeout(function(){ 
                        newWin.focus();
                        newWin.print();
                        newWin.close();
                    }, 100);
                }
            });
        });
        
        $(document).on('click','.loaddata',function(){
			var from_date = $('#from_date_f').val();
			var to_date = $('#to_date_f').val();
			
			$('.ssTable').DataTable().clear().destroy();
        	var tableOptions = {pageLength: 25,'stateSave':true};
        	var tableHeaders = {'theads':'','textAlign':textAlign,'srnoPosition':1,'reInit':'1'};
        	var dataSet = {from_date:from_date, to_date:to_date};
        	ssDatatable($('.ssTable'),tableHeaders,tableOptions,dataSet);
		});
    });  

    function grnTable() {
	var grnTable = $('#grnTable').DataTable(
		{
			responsive: true,
			//'stateSave':true,
			"autoWidth": false,
			order: [],
			"columnDefs": [
				{ type: 'natural', targets: 0 },
				{ orderable: false, targets: "_all" },
				{ className: "text-left", targets: [0, 1] },
				{ className: "text-center", "targets": "_all" }
			],
			pageLength: 100,
			language: { search: "" },
			lengthMenu: [
				[10, 25, 50, 100, -1], ['10 rows', '25 rows', '50 rows', '100 rows', 'Show all']
			],
			dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" + "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
			buttons: [] //[ 'pageLength', 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {loadAttendanceSheet();}}]
		});
        grnTable.buttons().container().appendTo('#grnTable_wrapper toolbar');
	$('.dataTables_filter .form-control-sm').css("width", "97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder", "Search.....");
	$('.dataTables_filter').css("text-align", "left");
	$('.dataTables_filter label').css("display", "block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius", "0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius", "0");
	return grnTable;
}

function closeModal(modalId){
	$("#"+ modalId).modal('hide');
}
</script>