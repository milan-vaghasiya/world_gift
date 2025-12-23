<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Stock Transfer</h4>
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
										<a href="<?=base_url($headData->controller."/stockTransfer")?>" class="btn waves-effect waves-light btn-outline-primary float-right permission-write ml-2"><i class="fa fa-plus"></i> Stock Transfer</a>
							        </div>
								</div>
							</div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='itemTable' class="table table-bordered ssTable" data-url='/getDTRows/3'></table>
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
  
    $(document).on('change',"#item_type",function(){
        var item_type = $(this).val();
        $("#itemTable").attr("data-url",'/getDTRows/'+item_type);
        initTable(1);
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

function stockTransfer(data){
    var button = "";
	var fnSave = data.fnSave;if(fnSave == "" || fnSave == null){fnSave="save";}

    $.ajax({ 
		type: "POST",   
		url: base_url + controller + '/stockTransfer',   
		data: {location_id:data.location_id,location_name:data.location_name,item_id:data.item_id,stock_qty:data.stock_qty}
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"saveStockTrans('"+data.form_id+"','"+fnSave+"');");
		if(button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
		}else if(button == "save"){
			$("#"+data.modal_id+" .modal-footer .btn-close").hide();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}else{
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}
		$(".single-select").comboSelect();
		$("#"+data.modal_id+" .scrollable").perfectScrollbar({suppressScrollX: true});
		initMultiSelect();setPlaceHolder();
        $('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });
	});
}

function saveStockTrans(formId,fnSave){
    setPlaceHolder();
	if(fnSave == "" || fnSave == null){fnSave="save";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + '/store/' + fnSave,
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			if(data.field_error == 1){
				$(".error").html("");
				$.each( data.field_error_message, function( key, value ) {$("."+key).html(value);});
			}else{
				initTable(1); $('#'+formId)[0].reset();$(".modal").modal('hide');
				toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			}
		}else if(data.status==1){
			initTable(1); $('#'+formId)[0].reset();$(".modal").modal('hide');
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{
			initTable(1); $('#'+formId)[0].reset();$(".modal").modal('hide');
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}
</script>