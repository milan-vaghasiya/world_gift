<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
					<div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Products</h4>
                            </div>
							<div class="col-md-6">
								<select id="category_id_filter" class="form-control float-right" style="width: 40%;">
                                    <!--<option value="">ALL Category</option>-->
                                    <?php
                                        foreach ($categoryList as $row) :
                                            $selected = (!empty($category_id) && $category_id == $row->id)?'selected':'';
                                            echo '<option value="'.$row->id.'" '.$selected.'>' . $row->category_name . '</option>';
                                        endforeach;
                                    ?>
                                </select>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
							<form id="printMultiTags" action="<?=base_url($headData->controller.'/printMultiTags')?>" method="POST"  target="_blank">
                            	<!-- <table id='productTagsTable' class="table table-bordered ssTable" data-url='/getTagsDTRows'></table> -->
                                <div class="table-responsive">
                                    <table id='reportTable' class="table table-bordered">
                                        <?php 
                                            $masterCheckBox = '<input type="checkbox" name ="masterSelect" id="masterSelect" class="filled-in chk-col-success bulkTags" value=""><label for="masterSelect"></label>';
                                        ?>
                                        <thead class="thead-info" id="theadData">
                                            <tr class="text-center">
                                                <th>#</th>
                                                <th style="width:50px;"><?= $masterCheckBox ?></th>
                                                <th>No Of Tags.</th>
                                                <th>Item Image</th>
                                                <th>Item Name</th>
                                                <th>Category</th>
                                                <th>Price</th>
                                                <th>Stock Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody">
                                            <?php echo $tbody; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        </div>
                    </div> 
                </div>
            </div>
        </div>        
    </div>
</div>
<div class="bottomBtn bottom-25 right-25 permission-write">
    <button type="button" class="btn btn-primary btn-rounded font-bold permission-write printMultiTags" style="letter-spacing:1px;">PRINT TAGS</button>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){ 
	jpReportTable('reportTable');
    $(document).on('change','#category_id_filter',function(e){
		var category_id = $(this).val();
        window.location.href = base_url + controller + '/printTagsIndex/'+category_id;
    });  
	
	// Print Invoice in Thermal Printer
	/*$(document).on("click",".printMultiTags",function(){
		var item_id = $('input[name="item_id[]"]').map(function(){ return this.value; }).get();
		var tag_qty = $('input[name="tag_qty[]"]').map(function(){ return this.value; }).get();
		var sendData = { 'item_id[]':item_id,'tag_qty[]':tag_qty,tp:'DP'};
		jQuery.ajax({
			url: base_url + controller + '/printMultiTags',
			data:sendData,
			type: "POST",
			dataType:"json",
		}).done(function(data){
			newWin= window.open("",'');
			newWin.document.write(data.printData);
			newWin.document.close();
			setTimeout(function()
			{ 
				newWin.focus();
				newWin.print();
				newWin.close();
				
			}, 1000);
		});
	});*/

	
	$(document).on("click",".printMultiTags",function(){$('#printMultiTags').submit();});

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
});
function printTags()
{
	
}
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
