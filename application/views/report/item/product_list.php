<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div>     
                            <div class="col-md-6">  
                                <div class="input-group">
                                    <select id="category_id" class="form-control single-select float-right" style="width: 75%;">
                                        <option value="">ALL Category</option>
                                        <?php if(!empty($categoryList)):
                                                foreach ($categoryList as $row) :
                                                    echo '<option value="' . $row->id . '">' . $row->category_name . '</option>';
                                                endforeach;
                                            endif;
                                        ?>
                                    </select>
                                    <div class="input-group-append" style="width: 25%;">
                                        <button type="button" data-file_type="" class="btn btn-block waves-effect waves-light btn-success float-right loaddata" title="Load Data">
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
								<thead class="thead-info" id="theadData">
                                    <tr class="text-center"><th colspan="6"><?=$pageHeader?></th></tr>
									<tr>
										<th style="min-width:25px;">#</th>
										<th style="min-width:80px;">Product Name</th>
										<th style="min-width:80px;">Category</th>
										<th style="min-width:50px;">HSN Code</th>
										<th style="min-width:50px;">Rate of GST</th>
										<th style="min-width:50px;">Unit of Gst</th>
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
<?=$floatingMenu?>
<script>
$(document).ready(function(){
    $(document).on('click','.loaddata',function(){
		$(".error").html("");
    	var valid = 1;
    	var category_id = $('#category_id').val();
    	var postData= {category_id:category_id};
    	if(valid){
    		$.ajax({
    		url: base_url + controller + '/getProductList',
    		data: postData,
    		type: "POST",
    		dataType:'json',
    			success:function(data){
    				$("#commanTable").DataTable().clear().destroy();
    				$("#tbodyData").html("");
    				$("#tbodyData").html(data.tbody);
    				jpReportTable('commanTable');
    			}
    		});
    	}
	});  
});
</script>