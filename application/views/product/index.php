<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                            <div class="row">
                            <div class="col-md-3">
						        <a href="javascript:void(0)" class="btn btn-outline-primary productSheet"><i class="fas fa-file-excel"></i> Without Image</a>
							</div>
                            <div class="col-md-6">
                                <input type="file" name="item_excel" id="item_excel" class="form-control-file float-left col-md-3" />
                                <a href="javascript:void(0);" class="btn btn-labeled btn-success bg-success-dark ml-2 importExcel  " type="button">
                                    <i class="fa fa-upload"></i>&nbsp;
                                    <span class="btn-label">Upload Excel &nbsp;<i class="fa fa-file-excel"></i></span>
                                </a>
                                <a href="<?= base_url($headData->controller . '/createSampleExcel/') ?>" class="btn btn-labeled btn-info bg-info-dark mr-2" target="_blank">
                                    <i class="fa fa-download"></i>&nbsp;&nbsp;
                                    <span class="btn-label">Download Excel&nbsp;&nbsp;<i class="fa fa-file-excel"></i></span>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-xl" data-function="addProduct" data-form_title="Add Product"><i class="fa fa-plus"></i> Add Product</button>
                                <select id="category_id_filter" class="form-control float-right" style="width: 40%;">
                                    <option value="">ALL Category</option>
                                    <?php if(!empty($categoryList)):
                                            foreach ($categoryList as $row) :
                                                echo '<option value="' . $row->id . '">' . $row->category_name . '</option>';
                                            endforeach;
                                        endif;
                                    ?>
                                </select>
                            </div>                             
                        </div>                                         
                                                      
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='productTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
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
							<div class="col-xs-12">
								<label>No. of Tag</label>
								<input type="text" name="tag_qty" id="tag_qty" class="form-control req" value="1">
								<input type="hidden" name="printsid" id="printsid" value="0">
								<label class="tag_qty text-danger"></label>
							</div>
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

 <!-- For Product Sheet Model -->
<div class="modal fade" id="productSheetModel" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content animated zoomIn border-light">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark"><i class="fa fa-file-excel"></i> &nbsp;&nbsp; Product Sheet</h5>
                <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="productSheetForm" method="post" action="<?= base_url($headData->controller . '/createExcel/') ?>" target="_blank">
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label>Category</label>
                                <select name="category_id_excel" id="category_id_excel" class="form-control single-select req">
                                    <option value="">Select All</option>
                                    <input type="hidden" name="printsid" id="printsid" value="0">
                                </select>
                            </div>
                            <div class="col-sm-6 form-group">
                            <label>Download</label>
                                <a href="javascript:void(0);" class="btn btn-labeled btn-info bg-info-dark mr-2 createExcel" target="_blank"><i class="fa fa-download"></i>&nbsp;&nbsp;<span class="btn-label">Download Excel&nbsp;&nbsp;<i class="fa fa-file-excel"></i></span></a>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/product.js?v=<?=time()?>"></script>
<script src="<?php echo base_url();?>assets/js/custom/item-stock-update.js?v=<?=time()?>"></script>
<script>
$(document).ready(function(){
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
				setTimeout(function()
				{ 
					newWin.focus();
					newWin.print();
					newWin.close();
					
				}, 100);
			}
		});
	});
	
	$(document).on("click",".printTagsModal",function(){
		$("#printModel").attr('action',base_url + controller + '/printTags');
		$("#printsid").val($(this).data('id'));
		$(".printTags").data('id',$(this).data('id'));
		$("#print_tags").modal();
	});
	
    $(document).on("click",".productSheet",function(){
        $.ajax({
            url:base_url+'products/getCategoryList',
            type:'post',
            data:{},
            dataType:'json',
            success:function(data){
                $("#category_id_excel").html("");
                $("#category_id_excel").html(data.options);
                $("#category_id_excel").comboSelect();
            }
        });
        $("#productSheetModel").modal();
    });

	$('body').on('click', '.createExcel', function() {
        var category_id = $('#category_id_excel').val();
        if (category_id != "" || category_id != 0) {
            window.location.href = base_url + controller + '/createExcel/'+ $('#category_id_excel').val();
        }else {
			 window.location.href = base_url + controller + '/createExcel';
		}
    });
	
    $(document).on('change','#category_id_filter',function(){
        initTable(1,{ 'category_id': $(this).val()});
    });
    
    $('body').on('click', '.importExcel', function() {
        $(".msg").html("");
        $(this).attr("disabled", "disabled");
        var fd = new FormData();
        fd.append("item_excel", $("#item_excel")[0].files[0]);
        $.ajax({
            url: base_url + controller + '/importExcel',
            data: fd,
            type: "POST",
            processData: false,
            contentType: false,
            dataType: "json",
        }).done(function(data) {
            if (data.status === 0) {
                $(".msg").html("");
                var error='';
                $.each(data.message, function(key, value) {
                    error+=' '+value;
                });
                $(".msg").html(error);
            } else if (data.status == 1) {
                toastr.success(data.message, 'Success', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
                initTable();
            }
            else if (data.status == 2) {
                toastr.warning(data.message, 'Warning', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
                initTable();
            }
            $(this).removeAttr("disabled");
            $("#item_excel").val(null);
        });
    });
});
</script>