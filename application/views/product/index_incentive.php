<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                <form id="saveMultipleInsentive" action="<?=base_url($headData->controller.'/saveMultipleInsentive')?>" method="POST"  target="_blank">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title pageHeader text-left"><?=$pageHeader?></h4>
                            </div> 
                            <div class="col-md-2">
                                <input type="text" name="incentive" value="" id="incentive" placeholder="Enter Incentive" class="form-control" style="align-items: center;" >
                            </div>
                            <div class="col-md-4">  
                                <div class="input-group">
                                    <select id="category" class="form-control float-right" style="width: 40%;">
                                        <option value="">ALL Category</option>
                                        <?php
                                            foreach ($categoryList as $row) :
                                                $selected = (!empty($category_id) && $category_id == $row->id)?'selected':'';
                                                echo '<option value="'.$row->id.'" '.$selected.'>' . $row->category_name . '</option>';
                                            endforeach;
                                        ?>
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
                    <div class="card-body reportDiv" style="min-height:75vh">                            
                            <div class="table-responsive">
                                <table id='reportTable' class="table table-bordered">
                                    <thead class="thead-info" id="theadData">
                                    <div class="row">
                                        <div class="col-md-4">
                                        </div>
                                        <!-- <div class="col-md-2">
                                            <label>Enter Incentive</label>
                                            <input type="text" name="incentive" value="" id="incentive" class="form-control" style="align-items: center;" >
                                        </div>        -->
                                        
                                    </div>
                                        <tr>
                                            <?php 
                                                $allCheckBox = '<input type="checkbox" name ="SelectAll" id="SelectAll" class="filled-in chk-col-success bulkIncentive" value=""><label for="SelectAll">ALL</label>'; 
                                                $inputBox = '';
                                            ?>
                                            <th style="min-width:10px;">#</th>
                                            <th style="min-width:100px;"><?= $allCheckBox?></th>
                                            <th style="min-width:100px;">Item Name</th>
                                            <th style="min-width:100px;">Category</th>
                                            <th style="min-width:100px;">Stock Qty.</th>
                                            <th style="min-width:100px;">Incentive(%)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyData"></tbody>
                                </table>
                            </div>
                        
                        
                    </div>
                
                
                </div>
                </form>
            </div>
        </div>        
    </div>
</div>
<div class="bottomBtn bottom-25 right-25 permission-write">
    <button type="button" class="btn btn-primary btn-rounded font-bold permission-write saveMultipleInsentive" data-button="both" data-modal_id="modal-md" data-function="addStoreLocation" data-form_title="Add Store Location" style="letter-spacing:1px;">SAVE INCENTIVE</button>
</div>


<?php $this->load->view('includes/footer'); ?>

<script>
$(document).ready(function(){
	reportTable();

	$(document).on("click",".saveMultipleInsentive",function(){
        var fd = $('#saveMultipleInsentive').find('input,select,textarea').serializeArray();
        console.log(fd);
        $.ajax({
            url: base_url + controller + '/saveMultipleInsentive',
            data: fd,
            type: "POST",
            dataType:'json',
            success:function(data){
                $("#reportTable").dataTable().fnDestroy();
                if(data.status==0)
				{
                    toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
					
				}
				else
				{
					
                    toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                    window.location.href = base_url + controller + '/indexIncentive';
				}
                
                reportTable();
            }
        });

        //$('#saveMultipleInsentive').submit();
        
        //window.location.href = base_url + controller + '/indexIncentive'
    });

	$(document).on('click','.bulkIncentive',function(){
		var id = $(this).data('rowid');
        var items =document.getElementsByName('item_id[]');
        var incentive = $("#incentive").val();
        console.log(incentive);


        if($(this).attr('id') == "SelectAll"){
            if ($(this).prop('checked') == true) { 
                $('input[name="item_id[]"]').prop('checked',true);
            }
            else{
                $('input[name="item_id[]"]').prop('checked',false);
            }
        }else{
            if($('input[name="item_id[]"]').not(':checked').length != $('input[name="item_id[]"]').length)
            {
                $('#SelectAll').prop('checked',false);
            }

            if($('input[name="item_id[]"]:checked').length == $('input[name="item_id[]"]').length)
            {
                $('#SelectAll').prop('checked',true);
            }
            else{
                $('#SelectAll').prop('checked',false);
            }
            $('input[name="item_id[]"]').each(function(){
                
            });
        }
	});

    $(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
		var category = $('#category').val();		
        $.ajax({
            url: base_url + controller + '/getItemIncentive',
            data: {category:category},
            type: "POST",
            dataType:'json',
            success:function(data){
                $("#reportTable").dataTable().fnDestroy();
                $("#tbodyData").html(data.tbody);
                reportTable();
            }
        });
        
    });   
});
function reportTable()
{
	var reportTable = $('#reportTable').DataTable( 
	{
        "paging": false,
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
		buttons: [ 'pageLength', 'excel'],
		"initComplete": function(settings, json) {$('body').find('.dataTables_scrollBody').addClass("ps-scrollbar");}
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