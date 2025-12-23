<div class="col-md-12">
    <form>
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <input type="hidden" name="item_id" id="item_id" value="<?= (!empty($dataRow->item_id)) ? $dataRow->item_id : $item_id; ?>" />
            <div class="col-md-4 form-group">
                <label for="image_path">Item Image</label>
                <input type="file" name="image_path" class="form-control-file req" />
            </div>
            <div class="col-md-6 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control"  value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">
            </div>
            <div class="col-md-2 form-group">
                <label for="">&nbsp;</label>
                <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form float-right mt-4" onclick="imageUpload('imageUpload','uploadImage');"><i class="fa fa-check"></i> Upload</button>
            </div>
        </div>
    </form>
    <div class="row">
        <div class="table-responsive">
            <table id="disctbl" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>Item Image</th>
                        <th>Remark</th>
                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="discBody">
                    <?php
                    if (!empty($imageData)) :
                        $i = 1;
                        foreach($imageData as $row) :
                            if(!empty($row->image_path)):
                                $productImg = '<img src="'.base_url('assets/uploads/item_image/'.$row->image_path).'" width="60" height="60" style="border-radius:0%;border: 0px solid #ccc;padding:3px;">';
                            else:
                                $productImg = '<img src="'.base_url('assets/uploads/item_image/default.png').'" width="60" height="60" style="border-radius:0%;border: 0px solid #ccc;padding:3px;">';
                            endif;
                            echo '<tr>
                                        <td>' .  $i++ . '</td>
                                        <td>' . $productImg . '</td>
                                        <td>' . $row->remark . '</td>
                                        <td class="text-center">
                                        <a href="javascript:void(0);" class="btn btn-outline-danger btn-delete" onclick="deleteImage(' . $row->id . ');"><i class="ti-trash"></i></a> 
                                        </td>
                                    </tr> ';
                        endforeach;
                    else :
                        echo '<tr><td colspan="4" style="text-align:center;">No Data Found</td></tr>';
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    function imageUpload(formId, fnsave, srposition = 1) {
        setPlaceHolder();
        if (fnsave == "" || fnsave == null) {
            fnsave = "save";
        }
        var form = $('#' + formId)[0];
        var fd = new FormData(form);
        $.ajax({
            url: base_url + controller + '/' + fnsave,
            data: fd,
            type: "POST",
            processData: false,
            contentType: false,
            dataType: "json",
        }).done(function(data) {
            if (data.status === 0) {
                $(".error").html("");
                $.each(data.message, function(key, value) {
                    $("." + key).html(value);
                });
            } else if (data.status == 1) {
                initTable(srposition); //$('#'+formId)[0].reset(); //$(".modal").modal('hide');
                toastr.success(data.message, 'Success', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
                $("#discBody").html(data.tbodyData);
                $("#item_id").val(data.item_id);
                $("#image_path").val("");
                $("#remark").val("");
            } else {
                initTable(srposition); //$('#'+formId)[0].reset(); //$(".modal").modal('hide');
                toastr.error(data.message, 'Error', {
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

    function deleteImage(id) {
        var item_id = $("#item_id").val();
        var send_data = {
            id: id,
            item_id: item_id,
        };
        console.log(send_data);
        $.confirm({
            title: 'Confirm!',
            content: 'Are you sure want to delete this ' + name + '?',
            type: 'red',
            buttons: {
                ok: {
                    text: "ok!",
                    btnClass: 'btn waves-effect waves-light btn-outline-success',
                    keys: ['enter'],
                    action: function() {
                        $.ajax({
                            url: base_url + controller + '/deleteImage',
                            data: send_data,
                            type: "POST",
                            dataType: "json",
                            success: function(data) {
                                if (data.status == 0) {
                                    toastr.error(data.message, 'Sorry...!', {
                                        "showMethod": "slideDown",
                                        "hideMethod": "slideUp",
                                        "closeButton": true,
                                        positionClass: 'toastr toast-bottom-center',
                                        containerId: 'toast-bottom-center',
                                        "progressBar": true
                                    });
                                } else {
                                    $("#discBody").html(data.tbodyData);
                                    $("#item_id").val(data.item_id);
                                    $("#image_path").val("");
                                    $("#remark").val("");
                                    toastr.success(data.message, 'Success', {
                                        "showMethod": "slideDown",
                                        "hideMethod": "slideUp",
                                        "closeButton": true,
                                        positionClass: 'toastr toast-bottom-center',
                                        containerId: 'toast-bottom-center',
                                        "progressBar": true
                                    });
                                }
                            }
                        });
                    }
                },
                cancel: {
                    btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                    action: function() {

                    }
                }
            }
        });
    }
</script>