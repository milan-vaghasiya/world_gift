<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="card-title">Print Tags</h4>
                            </div>  
                            <div class="col-md-4">
                                <select id="category_id_filter" class="form-control single-select float-right">
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
                        <form id="printMultiTags" action="<?=base_url($headData->controller.'/printMultiTags')?>" method="POST"  target="_blank">
                            <div class="row">
                                <?php if($this->CMID == 2){ ?>
            						<div class="col-md-4 form-group">
            							<label for="catelog_type_footer">Catalogue Type</label>
            							<select name="catelog_type_footer" id="catelog_type_footer" class="form-control single-select req">
            								<option value="Regular">Regular</option>
            								<option value="Wholesale">Wholesale</option>
            								<option value="SemiWholesale">Semi-Wholesale</option>
            							</select>
            						</div>
            					<?php } ?>
        						<div class="col-md-4 form-group">
        							<label for="prod_per_page">No. Items Per Row</label>
        							<input type="text" name="prod_per_page" id="prod_per_page" max="6" class="form-control numericOnly" value="5">
        						</div>
        						<div class="col-md-4 form-group">
        							<label for="with_qty">With Qty?</label>
        							<select name="with_qty" id="with_qty" class="form-control single-select req">
        								<option value="No">No</option>
        								<option value="Yes">Yes</option>
        							</select>						
        						</div>
                            </div>
                            <div class="table-responsive mt-4">
                                <table id='printTagsTable' class="table table-bordered ssTable" data-url='/getTagsDTRow'></table>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<div class="bottomBtn bottom-25 right-25 permission-write">
    <button type="button" class="btn btn-primary btn-rounded font-bold permission-write printMultiTags" style="letter-spacing:1px;">PRINT TAGS</button>
    <button type="button" class="btn btn-primary btn-rounded font-bold permission-write printItemCatalogue" style="letter-spacing:1px;">PRINT CATALOGUE</button>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){ 
	
	$(document).on("click",".printMultiTags",function(){
	    $('#printMultiTags').attr('action', base_url + 'products/printMultiTags');
	    $('#printMultiTags').submit();
	});
	$(document).on("click",".printItemCatalogue",function(){
	    $('#printMultiTags').attr('action', base_url + 'products/printItemCatalogue');
	    $('#printMultiTags').submit();
	});

	$(document).on('click','.bulkTags',function(){
		var id = $(this).data('rowid');
		var stockqty = $(this).data('stockqty');
        var items =document.getElementsByName('item_id[]');
        if ($(this).prop('checked') == true) { 
            $('#tag_qty_'+$(this).data('id')).prop( "disabled", false );
            $('#tag_qty_'+$(this).data('id')).val($(this).data('stockqty'));
            
        }
        else{
            $('#tag_qty_'+$(this).data('id')).prop( "disabled", true );
            $('#tag_qty_'+$(this).data('id')).val("");
        }
        if($(this).attr('id') == "masterSelect"){
            if ($(this).prop('checked') == true) { 
                $('input[name="item_id[]"]').prop('checked',true);
                $('input[name="tag_qty[]"]').prop( "disabled", false );
                $('.bulkTags').each(function(){
                    $('#tag_qty_'+$(this).data('id')).val($(this).data('stockqty'));
                });
            }
            else{
                $('input[name="item_id[]"]').prop('checked',false);
                $('input[name="tag_qty[]"]').prop( "disabled", true );
                $('.bulkTags').each(function(){
                    $('#tag_qty_'+$(this).data('id')).val("");
                });
            }
        }else{
            if($('input[name="item_id[]"]').not(':checked').length != $('input[name="item_id[]"]').length)
            {
                $('#masterSelect').prop('checked',false);
            }

            if($('input[name="item_id[]"]:checked').length == $('input[name="item_id[]"]').length)
            {
                $('#masterSelect').prop('checked',true);
            }
            else{
                $('#masterSelect').prop('checked',false);
            }
            $('input[name="item_id[]"]').each(function(){
                
            });
        }
	});
	
	$(document).on('change','#category_id_filter',function(){
        initTable(0,{ 'category_id': $(this).val()});
    });
});

function reportTable()
{
	var reportTable = $('#reportTable').DataTable( 
	{
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
		"fnInitComplete":function(){$('.dataTables_scrollBody').perfectScrollbar();},
		"fnDrawCallback": function( oSettings ) {$('.dataTables_scrollBody').perfectScrollbar('destroy').perfectScrollbar();}
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
