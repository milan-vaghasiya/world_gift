<?php $this->load->view('hr/employee/change_password');?>
<script>
	var base_url = '<?=base_url();?>'; 
	var controller = '<?=(isset($headData->controller)) ? $headData->controller : ''?>'; 
	var popupTitle = '<?=POPUP_TITLE;?>';
	var theads = '<?=(isset($tableHeader)) ? $tableHeader[0] : ''?>';
	var textAlign = '<?=(isset($tableHeader[1])) ? $tableHeader[1] : ''?>';
	var srnoPosition = '<?=(isset($tableHeader[2])) ? $tableHeader[2] : 1?>';
	var tableHeaders = {'theads':theads,'textAlign':textAlign,'srnoPosition':srnoPosition};
	var cm_id = '<?= $this->CMID ?>'; 

    var startYearDate = '<?=$this->startYearDate?>';
	var endYearDate = '<?=$this->endYearDate?>';
</script>
<div class="chat-windows"></div>

<!-- Permission Checking -->
<?php
	$script= "";
	if($permission = $this->session->userdata('emp_permission')):
		if(!empty($headData->pageUrl)):
    		$empPermission = $permission[$headData->pageUrl];
    		$script .= '
    			<script>
    				var permissionRead = "'.$empPermission['is_read'].'";
    				var permissionWrite = "'.$empPermission['is_write'].'";
    				var permissionModify = "'.$empPermission['is_modify'].'";
    				var permissionRemove = "'.$empPermission['is_remove'].'";
    				var permissionApprove = "'.$empPermission['is_approve'].'";
    			</script>
    		';
    		echo $script;
		else:
			$script .= '
			<script>
				var permissionRead = "1";
				var permissionWrite = "1";
				var permissionModify = "1";
				var permissionRemove = "1";
				var permissionApprove = "1";
			</script>
		';
		echo $script;
		endif;
	else:
		$script .= '
			<script>
				var permissionRead = "";
				var permissionWrite = "";
				var permissionModify = "";
				var permissionRemove = "";
				var permissionApprove = "";
			</script>
		';
		echo $script;
	endif;
?>

<!-- ============================================================== -->
<!-- All Jquery -->
<!-- ============================================================== -->
<script src="<?=base_url()?>assets/libs/jquery/dist/jquery.min.js"></script>

<!-- Bootstrap tether Core JavaScript -->
<script src="<?=base_url()?>assets/libs/popper.js/dist/umd/popper.min.js"></script>
<script src="<?=base_url()?>assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>

<!-- apps -->
<script src="<?=base_url()?>assets/js/app.min.js"></script>
<!-- Theme settings -->
<script src="<?=base_url()?>assets/js/app.init.light-sidebar.js"></script>
<script src="<?=base_url()?>assets/js/app-style-switcher.js"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="<?=base_url()?>assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
<script src="<?=base_url()?>assets/extra-libs/sparkline/sparkline.js"></script>
<!--Wave Effects -->
<script src="<?=base_url()?>assets/js/waves.js"></script>
<!--Menu sidebar -->
<script src="<?=base_url()?>assets/js/sidebarmenu.js"></script>
<!--Custom JavaScript -->
<script src="<?=base_url()?>assets/js/custom.min.js"></script>
<script src="<?=base_url()?>assets/extra-libs/jquery-ui/jquery-ui.min.js"></script>
<!--This page plugins -->
<script src="<?=base_url()?>assets/extra-libs/datatables.net/js/jquery.dataTables.min.js"></script>
<!--<script src="<?=base_url()?>assets/extra-libs/datatables.net/js/dataTables.scroller.min.js"></script>-->
<script src="<?=base_url()?>assets/extra-libs/bootstrap-datatable/js/dataTables.bootstrap4.min.js"></script>
<script src="<?=base_url()?>assets/extra-libs/bootstrap-datatable/js/dataTables.buttons.min.js"></script>
<script src="<?=base_url()?>assets/extra-libs/bootstrap-datatable/js/buttons.bootstrap4.min.js"></script>
<script src="<?=base_url()?>assets/extra-libs/bootstrap-datatable/js/jszip.min.js"></script>
<script src="<?=base_url()?>assets/extra-libs/bootstrap-datatable/js/pdfmake.min.js"></script>
<script src="<?=base_url()?>assets/extra-libs/bootstrap-datatable/js/vfs_fonts.js"></script>
<script src="<?=base_url()?>assets/extra-libs/bootstrap-datatable/js/buttons.html5.min.js"></script>
<script src="<?=base_url()?>assets/extra-libs/bootstrap-datatable/js/buttons.print.min.js"></script>
<script src="<?=base_url()?>assets/extra-libs/bootstrap-datatable/js/buttons.colVis.min.js"></script>
<script src="<?=base_url()?>assets/extra-libs/bootstrap-datatable/js/natural.js"></script>
<script src="<?=base_url()?>assets/extra-libs/bootstrap-datatable/js/moment.js"></script>
<script src="<?=base_url()?>assets/extra-libs/bootstrap-datatable/js/dataTables.fixedHeader.min.js"></script>
<script src="<?=base_url()?>assets/extra-libs/bootstrap-datatable/js/dataTables.checkboxes.min.js"></script>
<script src="<?=base_url()?>assets/js/jquery.resize.js"></script>
<!--This page JavaScript -->
<!--c3 JavaScript -->
<script src="<?=base_url()?>assets/extra-libs/c3/d3.min.js"></script>
<script src="<?=base_url()?>assets/extra-libs/c3/c3.min.js"></script>

<!-- Custom Scripts -->
<script src="<?=base_url()?>assets/extra-libs/toastr/dist/build/toastr.min.js"></script>
<script src="<?=base_url()?>assets/js/custom/comman-js.js?v=<?=time()?>"></script>
<script src="<?=base_url()?>assets/js/custom/custom_ajax.js?v=<?=time()?>"></script>
<script src="<?=base_url()?>assets/js/custom/jpstt.js?v=<?=time()?>"></script>
<script src="<?=base_url()?>assets/js/custom/typehead.js?v=<?=time()?>"></script>
<script src="<?=base_url()?>assets/js/custom/adhar_validatation.js?v=<?=time()?>"></script>
<script src="<?=base_url();?>assets/js/jquery-confirm.js"></script>
<script src="<?=base_url();?>assets/js/custom/jquery.alphanum.js"></script>
<script src="<?=base_url();?>assets/libs/sweetalert2/dist/sweetalert2.all.min.js"></script>
<script src="<?=base_url();?>assets/libs/sweetalert2/sweet-alert.init.js"></script>

<!-- Combo Select -->
<script src="<?=base_url()?>assets/extra-libs/comboSelect/jquery.combo.select.js"></script>

<!-- Select2 js -->
<script src="<?=base_url()?>assets/extra-libs/select2/js/select2.min.js"></script>
<script src="<?=base_url()?>assets/js/pages/multiselect/js/bootstrap-multiselect.js"></script>
<script src="<?=base_url();?>assets/js/custom/jp-tagsinput.min.js"></script>

<!-- Switchery js -->
<!--<script src="<?php echo base_url();?>assets/extra-libs/switchery/switchery.js"></script>
<script src="<?php echo base_url();?>assets/extra-libs/switchery/js/switchery.min.js"></script>-->
<script src="<?=base_url()?>assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
<script src="<?=base_url()?>assets/libs/bootstrap-switch/dist/js/bootstrap-switch.min.js"></script>
<script src="<?=base_url()?>assets/libs/inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>

<div class="ajaxModal"></div>
<div class="centerImg">
	<img src="<?=base_url()?>assets/images/logo.png" style="width:85%;height:auto;"><br>
	<img src="<?=base_url()?>assets/images/ajaxLoading.gif" style="margin-top:-25px;">
</div>
<div class="modal fade" id="print_catalogue" data-backdrop="static" data-keyboard="false">
<div class="modal-dialog modal-xl" role="document">
	<div class="modal-content animated zoomIn border-light">
		<div class="modal-header bg-light">
			<h5 class="modal-title text-dark"><i class="fa fa-print"></i> Print Catalogue</h5>
			<button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<form id="printCatalogueModel" method="post" action="<?=base_url($headData->controller.'/catalogue_pdf')?>" target="_blank">
			<div class="modal-body">
				<div class="col-md-12">
					<?php
					    $col_md = ($this->CMID == 1)?'col-md-4':'col-md-3';
					?>
					<div class="row">
						<div class="<?=$col_md?> form-group">
							<label for="category_id_footer">Category</label>
							<select id="category_ids" data-input_id="category_id_footer" class="form-control jp_multiselect req" multiple="multiple">
								<option value="">Select All</option>
							</select>
							<input type="hidden" name="category_id_footer" id="category_id_footer" value="" />
							<input type="hidden" name="printsid" id="printsid" value="0">
						</div>
					<?php if($this->CMID == 2){ ?>
						<div class="<?=$col_md?> form-group">
							<label for="catelog_type_footer">Catalogue Type</label>
							<select name="catelog_type_footer" id="catelog_type_footer" class="form-control single-select req">
								<option value="Regular">Regular</option>
								<option value="Wholesale">Wholesale</option>
								<option value="SemiWholesale">Semi-Wholesale</option>
							</select>
						</div>
					<?php } ?>
						<div class="<?=$col_md?> form-group">
							<label for="prod_per_page">No. Items Per Row</label>
							<input type="text" name="prod_per_page" id="prod_per_page" max="6" class="form-control numericOnly" value="5">
						</div>
						<div class="<?=$col_md?> form-group">
							<label for="with_qty">With Qty?</label>
							<select name="with_qty" id="with_qty" class="form-control single-select req">
								<option value="No">No</option>
								<option value="Yes">Yes</option>
							</select>						
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<a href="#" data-dismiss="modal" class="btn btn-secondary"><i class="fa fa-times"></i> Close</a>
					<button type="submit" class="btn btn-success" onclick="closeModal('print_catalogue');"><i class="fa fa-print"></i> Print</button>
				</div>
			</div>
		</form>
	</div>
</div>
<script>
	$(document).ready(function(){
		$(document).on("click",".printCatalogue",function(){
			$("#printCatalogueModel").attr('action',base_url +  'products/catalogue_pdf');
			$.ajax({
    			url:base_url+'items/getCategoryList',
    			type:'post',
    			data:{},
    			dataType:'json',
    			success:function(data){
    				
    				$("#category_ids").html("");
    				$("#category_ids").html(data.options);
    				reInitMultiSelect();
    				//$("#category_ids").comboSelect();
    			}
    		});
			$("#printsid").val($(this).data('id')); 
			$(".catalogue_pdf").data('id',$(this).data('id'));
			$("#print_catalogue").modal();
		});
		
		//Created By Karmi @06/05/2022
		$(document).on('click',".addVoucher",function(){
			var functionName = $(this).data("function");
			var modalId = $(this).data('modal_id');
			var button = $(this).data('button');
			var title = $(this).data('form_title');
			var partyId = $(this).data('partyid');
			var formId = functionName.split('/')[0];
			var fnsave = $(this).data("fnsave");if(fnsave == "" || fnsave == null){fnsave="save";}
			$.ajax({ 
				type: "POST",   
				url: base_url  + 'paymentVoucher/' + functionName,   
				data: {partyId:partyId}
			}).done(function(response){
				$("#"+modalId).modal({show:true});
				$("#"+modalId+' .modal-title').html(title);
				$("#"+modalId+' .modal-body').html("");
				$("#"+modalId+' .modal-body').html(response);
				$("#"+modalId+" .modal-body form").attr('id',formId);
				$("#"+modalId+" .modal-footer .btn-save").attr('onclick',"storeVoucher('"+formId+"','"+fnsave+"');");
				if(button == "close"){
					$("#"+modalId+" .modal-footer .btn-close").show();
					$("#"+modalId+" .modal-footer .btn-save").hide();
				}else if(button == "save"){
					$("#"+modalId+" .modal-footer .btn-close").hide();
					$("#"+modalId+" .modal-footer .btn-save").show();
				}else{
					$("#"+modalId+" .modal-footer .btn-close").show();
					$("#"+modalId+" .modal-footer .btn-save").show();
				}
				$(".single-select").comboSelect();
				initModalSelect();
				$("#processDiv").hide();
				$("#"+modalId+" .scrollable").perfectScrollbar({suppressScrollX: true});
				setTimeout(function(){ initMultiSelect();setPlaceHolder(); }, 5);
            });
		});
	});
	
// Created By Karmi @06/05/2022
function storeVoucher(formId,fnsave,srposition=1){
	
	setPlaceHolder();
	if(fnsave == "" || fnsave == null){fnsave="save";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + 'paymentVoucher/save',
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.field_error_message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
			initTable(srposition); $('#'+formId)[0].reset();$(".modal").modal('hide');
			toastr.success(data.field_error_message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			window.location = base_url + 'paymentVoucher';
		}else{
			initTable(srposition); $('#'+formId)[0].reset();$(".modal").modal('hide');
			toastr.error(data.field_error_message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}
				
	});
}
</script>