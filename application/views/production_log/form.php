<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Production</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="saveSalesInvoice">
                            <div class="col-md-12">
                                <input type="hidden" name="row_index" id="row_index" value="">
                                <input type="hidden" name="trans_id" id="trans_id" value="">
                                <div class="row">
                                    <?php
                                    $cmID = $this->session->userdata('CMID');
                                    ?>
                                    <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />

                                    <div class="col-md-2 form-group">
                                        <label for="trans_no">Voucher No.</label>
                                        <input type="text" id="trans_no" name="trans_no" class="form-control req" value="<?= (!empty($dataRow->trans_no)) ? $dataRow->trans_no : $nextTransNo ?>" readonly />
                                    </div>

                                    <div class="col-md-2 form-group">
                                        <label for="date">Voucher Date</label>
                                        <input type="date" id="prd_date" name="prd_date" class="form-control req" value="<?= (!empty($dataRow->prd_date)) ? $dataRow->prd_date : date("Y-m-d") ?>" />
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <label for="remark">Remark</label>
                                        <input type="text" name="remark" class="form-control" value="<?= (!empty($dataRow->remark)) ? $dataRow->remark : "" ?>" />
                                    </div>
                                    <hr>
                                    <div class="col-md-4 form-group">
                                        <label for="fg_item_name">Product Name</label>
                                        <select name="item_id" id="item_id" class="form-control large-select2 req" data-item_type="" data-category_id="" data-family_id="" autocomplete="off" data-default_id="<?= (!empty($dataRow->req_item_id)) ? $dataRow->req_item_id : "" ?>" data-default_text="<?= (!empty($dataRow->full_name)) ? $dataRow->full_name : "" ?>" data-url="products/getDynamicItemList">
                                            <option value="">Select Item</option>
                                        </select>
                                        <span class="text-info stock_qty"></span>
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="location_id">Store</label>
                                        <select id="location_id" class="form-control single-select req">
                                            <option value="">Select Store</option>
                                            <?php
                                                foreach($locationList as $row):
                                                    echo '<option value="'.$row->id.'">'.$row->location.'</option>'; 
                                                endforeach;
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="item_type">Item Type</label>
                                        <select id="item_type" class="form-control single-select">
                                            <option value="1">Finish Good</option>
                                            <option value="2">Row Material</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="qty"> Qty.</label>
                                        <input type="text" class="form-control numericOnly req" id="qty">
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn waves-effect waves-light btn-primary btn-block addRow"><i class="fas fa-plus"></i> Add</button>
                                    </div>
                                    <div class="col-md-12 ">
                                        <div class="col-md-6">
                                        <h4>Item Details : </h4>
                                        </div>
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <div class="error general_item_error"></div>
                                        <div class="table-responsive ">
                                            <table id="issueItems" class="table table-striped table-borderless">
                                                <thead class="thead-info">
                                                    <tr>
                                                        <th style="width:5%;">#</th>
                                                        <th>Item</th>
                                                        <th>Store</th>
                                                        <th>Item Type</th>
                                                        <th>Qty</th>
                                                        <th style="width:10%;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tempItem">
                                                <?php
                                                if (!empty($batchTrans)) :
                                                    $i = 1;
                                                    foreach ($batchTrans as $row) :
                                                        if ($this->uri->segment(2) == "addSalesInvoiceOnSalesOrder") :
                                                            $row->id = "";
                                                        endif;
                                                ?>
                                                        <tr>
                                                            <td style="width:5%;">
                                                                <?= $i ?>
                                                            </td>
                                                            <td>
                                                                <?= $row->item_name ?>
                                                                <input type="hidden" name="item_id[]" value="<?= $row->item_id ?>">
                                                                <input type="hidden" name="item_name[]" value="<?= htmlentities($row->item_name) ?>">
                                                                <input type="hidden" name="trans_id[]" value="<?= $row->id ?>">
                                                            </td>
                                                            <td>
                                                                <?= $row->location ?>
                                                                <input type="hidden" name="location_id[]" value="<?= $row->location_id ?>">
                                                            </td>
                                                            <td>
                                                                <?= ($row->trans_type==1)?'Finish Good':'Row Material' ?>
                                                                <input type="hidden" name="item_type[]" value="<?= $row->trans_type ?>">
                                                            </td>
                                                            <td>
                                                                <?= abs($row->qty) ?>
                                                                <input type="hidden" name="qty[]" value="<?= abs($row->qty) ?>">
                                                                <div class="error qty<?= $i ?>"></div>
                                                            </td>
                                                            <td class="text-center" style="width:10%;">
                                                                <?php
                                                                    $row->trans_id = $row->id;
                                                                    $row->qty = abs($row->qty);
                                                                    $row = json_encode($row);
                                                                ?>
                                                                <button type="button" onclick='Edit(<?= $row ?>,this);' class="btn btn-sm btn-outline-warning waves-effect waves-light"><i class="ti-pencil-alt"></i></button>

                                                                <button type="button" onclick="Remove(this);" class="btn btn-sm btn-outline-danger waves-effect waves-light m-l-2"><i class="ti-trash"></i></button>
                                                            </td>
                                                        </tr>
                                                    <?php $i++;
                                                    endforeach;
                                                else : ?>
                                                    <tr id="noData">
                                                        <td colspan="13" class="text-center">No data available in table</td>
                                                    </tr>
                                                <?php endif; ?>
                                                </tbody>
                                            </table>
                                            
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-12">
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveProductionLog('saveSalesInvoice');"><i class="fa fa-check"></i> Save</button>
                            <a href="<?= base_url($headData->controller) ?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<!-- <script src="<?php echo base_url(); ?>assets/js/custom/stock-transfer-form.js?v=<?= time() ?>"></script> -->
<script src="<?php echo base_url(); ?>assets/js/custom/master-form.js?v=<?= time() ?>"></script>
<script>
    $(document).ready(function() {
        dataSet = {};
        getDynamicItemList(dataSet);
        setPlaceHolder();

        $('#item_id').on('change', function() {
            $('.item_id').html('');
            var item_id = $(this).val();
            if ($(this).val() != '') {
                $.ajax({
                    url: base_url + controller + "/getItemStock",
                    type: 'post',
                    data: {
                        id: item_id
                    },
                    dataType: 'json',
                    success: function(data) {
                        $(".stock_qty").html("");
                        if (data.qty != null && data.qty != "0") {
                            $(".stock_qty").html("Stock Qty.:" + data.qty);
                        } else {
                            $(".stock_qty").html("");
                        }
                    }
                });
            }
        });

        $(document).on('click', '.addRow', function() {
            var item_id = $("#item_id").val();
            var item_type = $("#item_type").val();
            var item_type_label = $("#item_type option:selected").text();
            var location_id = $("#location_id").val();
            var location_name = $("#location_idc").val();
            var row_index = $("#row_index").val();
            var trans_id = $("#trans_id").val();
            var qty = $("#qty").val();
           
            var IsValid = 1;
            if (item_id == "") { $(".item_id").html("Item is required."); IsValid=0;}
            if (location_id == "") { $(".location_id").html("Store is required."); IsValid=0;}
            if (qty == "" || qty == "0" || qty == "0.000") { $(".qty").html("Qty. is required."); IsValid=0;}

               
            if(IsValid){
                var itemData = {};  //console.log($("#item_id").select2('data')[0]['row']);
                //console.log($("#item_id option:selected").val());
                //if($("#item_id").select2('data')[0]['row']){
                    //itemData = JSON.parse($("#item_id").select2('data')[0]['row']); 
                    var post = {
                        trans_id : trans_id,
                        qty: qty,
                        item_id: item_id,
                        location_id:location_id,
                        location_name:location_name,
                        item_name: $("#item_id option:selected").text(),//itemData.item_name,
                        item_type: item_type,
                        item_type_label: item_type_label,
                        row_index:row_index
                    };

                    addRow(post);
                    //dataSet = {};getDynamicItemList(dataSet);
                    setPlaceHolder();
                    $("#qty").val("");
                    $("#location_id").val("");
                    $("#location_id").comboSelect();
              //  }
                
            }
        });
    });

    function addRow(data) { 
        $('table#issueItems tr#noData').remove();
        //Get the reference of the Table's TBODY element.
        var tblName = "issueItems";

        var tBody = $("#" + tblName + " > TBODY")[0];

        //Add Row.
        if(data.row_index != ""){
    		var trRow = data.row_index;
    		//$("tr").eq(trRow).remove();
    		$("#"+tblName+" tbody tr:eq("+trRow+")").remove();
    	}
    	var ind = (data.row_index == "")?-1:data.row_index;
    	row = tBody.insertRow(ind);
        //row = tBody.insertRow(-1);

        //Add index cell
        var countRow = (data.row_index == "")?($('#'+tblName+' tbody tr:last').index() + 1):(parseInt(data.row_index) + 1);
        var cell = $(row.insertCell(-1));
        cell.html(countRow);
        cell.attr("style", "width:5%;");

        cell = $(row.insertCell(-1));
        cell.html(data.item_name + '<input type="hidden" name="item_id[]" value="' + data.item_id + '">');

        cell = $(row.insertCell(-1));
        cell.html(data.location_name + '<input type="hidden" name="location_id[]" value="' + data.location_id + '">');

        cell = $(row.insertCell(-1));
        cell.html(data.item_type_label + '<input type="hidden" name="item_type[]" value="' + data.item_type + '">');

        cell = $(row.insertCell(-1));
        cell.html(data.qty + '<input type="hidden" name="qty[]" value="' + data.qty + '"><div class="error qty'+countRow+'"><input type="hidden" name="trans_id[]" value="' + data.trans_id + '" />');

        cell = $(row.insertCell(-1));
        
        var btnRemove = $('<button><i class="ti-trash"></i></button>');
        btnRemove.attr("type", "button");
        btnRemove.attr("onclick", "Remove(this,'" + data.qty + "');");
        btnRemove.attr("style", "margin-left:4px;");
        btnRemove.attr("class", "btn btn-sm btn-outline-danger waves-effect waves-light");
        
        var btnEdit = $('<button><i class="ti-pencil-alt"></i></button>');
        btnEdit.attr("type", "button");
        btnEdit.attr("onclick", "Edit("+JSON.stringify(data)+",this);");
        btnEdit.attr("class", "btn btn-sm btn-outline-warning waves-effect waves-light");

        cell.append(btnEdit);
        cell.append(btnRemove);
        cell.attr("class", "text-center");
        cell.attr("style", "width:10%;");
        $("#row_index").val("");
    }
    
    function Edit(data,button){
    	var row_index = $(button).closest("tr").index();; 
        $.each(data,function(key, value) {
            $("#"+key).val(value);
            if(key == 'cm_id'){ cm_id = value; }
        }); 
    	$("#item_name_dis").val(data.item_name); 	   
    	$("#row_index").val(row_index);
    	$("#location_id").comboSelect();
    	
    	let dataSet = {};
        var iid = data.item_id;
        setTimeout(function(){
            if(iid){
                var jsonRow = JSON.stringify({item_name:data.item_name});
                dataSet = {id: iid, text: data.item_name,row: jsonRow};
            }
            getDynamicItemList(dataSet);
        },600);
    }

    function Remove(button, qty) {
        //Determine the reference of the Row using the Button.
        var row = $(button).closest("TR");
        var table = $("#issueItems")[0];
        table.deleteRow(row[0].rowIndex);
        $('#issueItems tbody tr td:nth-child(1)').each(function(idx, ele) {
            ele.textContent = idx + 1;
        });
        $('#issueItems tbody tr td:nth-child(5) div').each(function(idx, ele) {
			let newIdx = idx + 1;
			$(this).attr('class','error');
			$(this).addClass('qty'+newIdx);
        });
        var countTR = $('#issueItems tbody tr:last').index() + 1;
        if (countTR == 0) {
            $("#tempItem").html('<tr id="noData"><td colspan="6" align="center">No data available in table</td></tr>');
        }
    };

    function saveProductionLog(formId) {
    var fd = $('#' + formId)[0];
    var formData = new FormData(fd);
    $.ajax({
        url: base_url + controller + '/save',
        data: formData,
        processData: false,
        contentType: false,
        type: "POST",
        dataType: "json",
    }).done(function (data) {
        if (data.status === 0) {
            $(".error").html("");
            $.each(data.field_error_message, function (key, value) {
                $("." + key).html(value);
            });
        } else if (data.status == 1) {
            toastr.success(data.field_error_message, 'Success', {
                "showMethod": "slideDown",
                "hideMethod": "slideUp",
                "closeButton": true,
                positionClass: 'toastr toast-bottom-center',
                containerId: 'toast-bottom-center',
                "progressBar": true
            });
            window.location = data.url;
        } else {
            toastr.error(data.field_error_message, 'Error', {
                "showMethod": "slideDown",
                "hideMethod": "slideUp",
                "closeButton": true,
                positionClass: 'toastr toast-bottom-center',
                containerId: 'toast-bottom-center',
                "progressBar": true
            });
        }
    });
}
</script>