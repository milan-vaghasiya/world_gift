$(document).ready(function(){
    packingBom();

    $(document).on('keyup change',".batchQty",function(){		
		var batchQtyArr = $("input[name='batch_qty[]']").map(function(){return $(this).val();}).get();
		var batchQtySum = 0;
		$.each(batchQtyArr,function(){batchQtySum += parseFloat(this) || 0;});
		$('#totalQty').html("");
		$('#totalQty').html(batchQtySum.toFixed(3));
		$("#packing_qty").val(batchQtySum.toFixed(3));

		var id = $(this).data('rowid');
		var cl_stock = $(this).data('cl_stock');
		var batchQty = $(this).val();
		$(".batch_qty"+id).html("");
		$(".packing_qty").html();
		if(parseFloat(batchQty) > parseFloat(cl_stock)){
			$(".batch_qty"+id).html("Stock not avalible.");
		}
	});

    $(document).on('change',"#item_id",function(){	
        var id = $(this).val();
        if(id){
            $.ajax({
				url: base_url + 'packing/batchWiseItemStock',
				data: {item_id:id,trans_id:"",batch_no:"",location_id:"",batch_qty:""},
				type: "POST",
				dataType:'json',
				success:function(data){
					$("#batchData").html(data.batchData);
				}
			});
        }
    });
});


function AddRow() {
    var valid = 1;
	$(".error").html("");
    
    if($("#box_id").val() == ""){$(".box_id").html("Packing Material is required.");valid = 0;}
	if($("#capacity").val() == "" || $("#capacity").val() == 0){$(".capacity").html("Capacity is required.");valid = 0;}
	
	if(valid)
	{
        $(".box_id").html("");
        $(".capacity").html("");
        //Get the reference of the Table's TBODY element.
        $("#packingBom").dataTable().fnDestroy();
        var tblName = "packingBom";
        
        var tBody = $("#"+tblName+" > TBODY")[0];
        
        //Add Row.
        row = tBody.insertRow(-1);
        
        //Add index cell
        var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
        var cell = $(row.insertCell(-1));
        cell.html(countRow);
        
        
        cell = $(row.insertCell(-1));
        cell.html($("#box_idc").val() + '<input type="hidden" name="box_id[]" value="'+$("#box_id").val()+'">');

        cell = $(row.insertCell(-1));
        cell.html($("#box_typec").val() + '<input type="hidden" name="box_type[]" value="'+$("#box_type").val()+'">');

        var capacityErrorDiv = $("<div></div>",{class:"error capacity"+countRow});
        cell = $(row.insertCell(-1));
        cell.html($("#capacity").val() + '<input type="hidden" name="capacity[]" value="'+$("#capacity").val()+'">');
	    cell.append(capacityErrorDiv);

        //Add Button cell.
        cell = $(row.insertCell(-1));
        var btnRemove = $('<button><i class="ti-trash"></i></button>');
        btnRemove.attr("type", "button");
        btnRemove.attr("onclick", "Remove(this);");
        btnRemove.attr("class", "btn btn-sm btn-outline-danger waves-effect waves-light");
        cell.append(btnRemove);
        cell.attr("class","text-center");
        packingBom();
        $("#box_id").val("");
        $("#box_idc").val("");
        $("#capacity").val("");
	}
};

function Remove(button) {
    //Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#packingBom")[0];
	table.deleteRow(row[0].rowIndex);
	$('#packingBom tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
};

function packingBom(){
    var table = $('#packingBom').DataTable( {
		lengthChange: false,
		responsive: true,
		ordering: true,
		//'stateSave':true,
        'pageLength': 25,
		buttons: ['pageLength', 'copy', 'excel' ]
	});
	table.buttons().container().appendTo( '#packingBom_wrapper .col-md-6:eq(0)' );
}