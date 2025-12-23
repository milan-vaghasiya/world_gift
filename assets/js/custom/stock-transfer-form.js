
$(document).ready(function () {


    $(document).on('click', '.saveItem', function () {
        var fd = $('.invoiceItemForm').find('input,select,textarea').serializeArray();
        //var fd = $('#invoiceItemForm').serializeArray();
        var formData = {};
        $.each(fd, function (i, v) {
            if (v.name != "batch_number[]" && v.name != "location[]" && v.name != "batch_quantity[]") {
                formData[v.name] = v.value;
            }
        });
        $(".item_id").html("");
        $(".qty").html("");
        var location_id = $("#from_location_id").val();
        
        if (formData.item_id == "" || location_id == 0 || location_id == '' || formData.qty > parseFloat($("#stockQty").html())) {
            if (formData.item_id == "") {
                $(".item_id").html("Item Name is required.");
            }
            if (location_id == 0 || location_id == '') {
                $(".from_location_id").html("Location is required.");
            }

            if (formData.qty > parseFloat($("#stockQty").html())) {
                $(".qty").html("Qty. is Invalid.");
            }
        } else {
            var item_ids = $("input[name='item_id[]']").map(function () {
                return $(this).val();
            }).get();
            if ($.inArray(formData.item_id, item_ids) >= 0 && formData.row_index == "") {
                $(".item_id").html("Item already added.");
            } else {
                if (formData.qty == "" || formData.qty == "0") {
                    if (formData.qty == "" || formData.qty == "0") {
                        $(".qty").html("Qty is required.");
                    }
                } else {
                    var itemData = JSON.parse($("#item_id").select2('data')[0]['row']);
                    formData.item_name = itemData.item_name;
                    AddRow(formData);
                    $("#item_id").val("");
                    resetFormByClass('invoiceItemForm');
                    $("#from_location_id").trigger("change");
                    $("#stockQty").html("");
                }
            }
        }
    });

    $(document).on('change', '#from_location_id', function () {
        var from_location_id = $(this).val();
        $("#to_location_id option").attr("disabled", false);
        $("#to_location_id option[value=" + from_location_id + "]").attr("disabled", "disabled");
        dataSet = { 'location_id': from_location_id };

        getDynamicItemList(dataSet);
        setPlaceHolder();

        $("#to_location_id").comboSelect();
    });

    $(document).on('change', '#item_id', function () {
        var item_id = $(this).val();
        if (item_id) {
            var itemData = JSON.parse($(this).select2('data')[0]['row']); 
            $("#stockQty").html(itemData.qty);
        }
    });
});

function Remove(button) {
    //Determine the reference of the Row using the Button.
    var row = $(button).closest("TR");
    var table = $("#invoiceItems")[0];
    table.deleteRow(row[0].rowIndex);
    $('#invoiceItems tbody tr td:nth-child(1)').each(function (idx, ele) {
        ele.textContent = idx + 1;
    });
    var countTR = $('#invoiceItems tbody tr:last').index() + 1;
    if (countTR == 0) {
        if ($("#gst_type").val() == 1) {
            $("#tempItem").html('<tr id="noData"><td colspan="12" align="center">No data available in table</td></tr>');
        } else if ($("#gst_type").val() == 2) {
            $("#tempItem").html('<tr id="noData"><td colspan="11" align="center">No data available in table</td></tr>');
        } else {
            $("#tempItem").html('<tr id="noData"><td colspan="10" align="center">No data available in table</td></tr>');
        }
    }
};

function Edit(data, button) {
    var row_index = $(button).closest("tr").index();
    //$("#itemModel").modal();
    $(".btn-close").show();
    $(".btn-save").hide();
    var batchNo = "";
    var locationId = "";
    var batchQty = "";
    $.each(data, function (key, value) {
        if (key == "batch_no") {
            batchNo = value;
        } else if (key == "location_id") {
            locationId = value;
        } else if (key == "batch_qty") {
            batchQty = value;
        } else {
            $("#" + key).val(value);
        }
    });
    //$("#item_id").comboSelect();
    var item_id = $("#item_id").val();
    var trans_id = $("#trans_id").val();



    $("#item_name_dis").val(data.item_name);
    $("#row_index").val(row_index);
    //Remove(button);
}


function AddRow(data) {
    $('table#invoiceItems tr#noData').remove();
    //Get the reference of the Table's TBODY element.
    var tblName = "invoiceItems";

    var tBody = $("#" + tblName + " > TBODY")[0];

    //Add Row.
    if (data.row_index != "") {
        var trRow = data.row_index;
        //$("tr").eq(trRow).remove();
        $("#" + tblName + " tbody tr:eq(" + trRow + ")").remove();
    }
    var ind = (data.row_index == "") ? -1 : data.row_index;
    row = tBody.insertRow(ind);

    //Add index cell
    var countRow = (data.row_index == "") ? ($('#' + tblName + ' tbody tr:last').index() + 1) : (parseInt(data.row_index) + 1);
    var cell = $(row.insertCell(-1));
    cell.html(countRow);
    cell.attr("style", "width:5%;");

    var itemIdInput = $("<input/>", {
        type: "hidden",
        name: "item_id[]",
        value: data.item_id
    });
    var itemNameInput = $("<input/>", {
        type: "hidden",
        name: "item_name[]",
        value: data.item_name
    });
    var transIdInput = $("<input/>", {
        type: "hidden",
        name: "trans_id[]",
        value: data.trans_id
    });

    cell = $(row.insertCell(-1));
    cell.html(data.item_name);
    cell.append(itemIdInput);
    cell.append(itemNameInput);
    cell.append(transIdInput);




    var qtyInput = $("<input/>", {
        type: "hidden",
        name: "qty[]",
        value: data.qty
    });
    var qtyErrorDiv = $("<div></div>", {
        class: "error qty" + countRow
    });
    cell = $(row.insertCell(-1));
    cell.html(data.qty);
    cell.append(qtyInput);
    cell.append(qtyErrorDiv);


    cell = $(row.insertCell(-1));
    var btnRemove = $('<button><i class="ti-trash"></i></button>');
    btnRemove.attr("type", "button");
    btnRemove.attr("onclick", "Remove(this);");
    btnRemove.attr("style", "margin-left:4px;");
    btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");

    var btnEdit = $('<button><i class="ti-pencil-alt"></i></button>');
    btnEdit.attr("type", "button");
    btnEdit.attr("onclick", "Edit(" + JSON.stringify(data) + ",this);");
    btnEdit.attr("class", "btn btn-outline-warning waves-effect waves-light");

    // cell.append(btnEdit);
    cell.append(btnRemove);
    cell.attr("class", "text-center");
    cell.attr("style", "width:10%;");

};

function getFGSelect(row) {
    var itemData = row;
    var price = 0; //alert(cm_id);
    if (itemData.price != "") {
        var p = parseFloat(itemData.price);
        if (cm_id == 1) {
            price = parseFloat(p / 1.18).toFixed(2);
        } else {
            price = p;
        }
    }
    $("#item_id").val(itemData.id);

    $("#item_name_dis").val(itemData.item_name);
    $("#item_name").val(itemData.item_name);
    $("#stockQty").html(itemData.qty);
    var item_id = itemData.id;
    $("#modal-xl").modal('hide');
}

function saveStockTrasfer(formId) {
    var fd = $('#' + formId)[0];
    var formData = new FormData(fd);
    $.ajax({
        url: base_url + controller + '/saveStockTransfer',
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