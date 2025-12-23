<?php $this->load->view('includes/header'); ?>
<style> 
	.typeahead.dropdown-menu{width:95.5% !important;padding:0px;border: 1px solid #999999;box-shadow: 0 2px 5px 0 rgb(0 0 0 / 26%);}
	.typeahead.dropdown-menu li{border-bottom: 1px solid #999999;}
	.typeahead.dropdown-menu li .dropdown-item{padding: 8px 1em;margin:0;}
</style>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Offers</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="saveOffers">
                            <div class="col-md-12">

								<input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
								<input type="hidden" name="item_id[]" value="<?=(!empty($dataRow->item_id))?$dataRow->item_id:""?>" />
		
								<div class="row form-group">
                                    <div class="col-md-2 form-group">
										<label for="offer_code">Offer Code</label>
										<input type="text" name="offer_code" id="offer_code" class="form-control"  value="<?=(!empty($dataRow->offer_code))?$dataRow->offer_code:""?>">
									</div>
                                    <div class="col-md-2">
										<label for="offer_date">Offer Date</label>
                                        <input type="date" id="offer_date" name="offer_date" class=" form-control req" value="<?=(!empty($dataRow->offer_date))?$dataRow->offer_date:date("Y-m-d")?>" />	
									</div>
                                    <div class="col-md-4 form-group">
										<label for="offer_title">Offer Title</label>
										<input type="text" name="offer_title" id="offer_title" class="form-control"  value="<?=(!empty($dataRow->offer_title))?$dataRow->offer_title:""?>">
									</div>
                                    <div class="col-md-2">
										<label for="valid_from">Valid From </label>
                                        <input type="date" id="valid_from" name="valid_from" class=" form-control req" value="<?=(!empty($dataRow->valid_from))?$dataRow->valid_from:date("Y-m-d")?>" />	
									</div>
                                    <div class="col-md-2">
										<label for="valid_to">Valid To</label>
                                        <input type="date" id="valid_to" name="valid_to" class=" form-control req"  value="<?=(!empty($dataRow->valid_to))?$dataRow->valid_to:date("Y-m-d")?>" />	
									</div>
                                    <div class="col-md-2 form-group">
                                        <label for="percentage">Percentage</label>
                                        <input type="number" name="percentage" id="percentage" class="form-control floatOnly" value="<?=(!empty($dataRow->percentage))?$dataRow->percentage:0?>" />
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="amount">Amount</label>
                                        <input type="number" name="amount" id="amount" class="form-control floatOnly" value="<?=(!empty($dataRow->amount))?$dataRow->amount:0?>" />
                                    </div>

									
									
									<div class="col-md-8 form-group">
										<label for="remark">Remark</label>
										<input type="text" name="remark" id="remark" class="form-control" placeholder="Enter Remark" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">
									</div>
									
								</div>
							</div>
							<hr>
                            <div class="table-responsive">
                        	<table id='commanTable' name='commanTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
									<tr>
										
         								<th>#</th>
										<th>Product Image</th>
										<th>Product Name</th>
										<th>Category</th>
										<th>MRP</th>
									</tr>
								</thead>
								<tbody>
									<?php $i=1;$items=array();
										if (!empty($dataRow->item_id)) :
											$items=explode(',',$dataRow->item_id);
										endif;
										//print_r($items);exit;
										
										foreach($itemData as $row):
											$checked = "";
											if (in_array($row->id, $items)) :
												$checked = "checked";													
											endif;
											if(!empty($row->item_image)):
												$productImg = '<img src="'.base_url('assets/uploads/product/'.$row->item_image).'" width="40" height="40" style="border-radius:0%;border: 0px solid #ccc;padding:3px;">';
											else:
												$productImg = '<img src="'.base_url('assets/uploads/product/default.png').'" width="40" height="40" style="border-radius:0%;border: 0px solid #ccc;padding:3px;">';
											endif;
											echo '<tr>
												
												<td class="text-center">
												<input type="checkbox" id="md_checkbox_'.$i.'" name="item_id1[]" class="filled-in chk-col-success chkbox" value="'.$row->id.'" '.$checked.'><label for="md_checkbox_'.$i.'" class="mr-3"></label>
												</td>
												<td class="text-center">'.$productImg.'</td>
												<td>'.$row->item_name.'</td>
												<td>'.$row->item_name.'</td>
												<td>'.$row->price.'</td>
											</tr>';
											$i++;
											
										endforeach;

									?>
								</tbody>
							</table>
                        </div>
                            														
							
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-12">
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form saveOffers" onclick="saveOffers('saveOffers');" ><i class="fa fa-check"></i> Save</button>
                            <a href="<?=base_url($headData->controller)?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>



<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/master-form.js?v=<?=time()?>"></script>
<script>
    $(document).ready(function (){
		jpReportTable('commanTable')

		$('#commanTable').on('change', ':checkbox', function () {
			
			if(this.checked){
				var itm = $("input[name='item_id[]']").val().split(',');
				itm.push($(this).val());
				$("input[name='item_id[]']").val(itm);
				console.log(itm);
			}
			else{
				var itm = $("input[name='item_id[]']").val().split(',');
				var index = itm.indexOf($(this).val());
				if (index !== -1) {itm.splice(index, 1);}
				$("input[name='item_id[]']").val(itm);
				console.log(itm);
			}
		});
	});
    function saveOffers(formId){
		var fd = $('#'+formId).serialize();

		console.log(fd);
		$.ajax({
			url: base_url + controller + '/save',
			data:fd,
			type: "POST",
			dataType:"json",
		}).done(function(data){
			if(data.status===0){
				if(data.field_error == 1){
					$(".error").html("");
					$.each( data.field_error_message, function( key, value ) {$("."+key).html(value);});
				}else{
					toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
				}	
			}else if(data.status==1){
				toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
				window.location = data.url;
			}else{
				toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			}				
		});
	}
</script>