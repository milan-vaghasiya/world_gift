<div class="col-md-12">
    <form>
        <div class="row">
        <input type="hidden" name="id" id="id" value="" />
            <input type="hidden" name="party_id" id="party_id" value="<?= (!empty($dataRow->party_id)) ? $dataRow->party_id : $party_id; ?>" />
            <div class="col-md-6 form-group">
                <label for="special_date">Special Date </label>
                <input type="date" name="special_date" class="form-control req" value="<?=date("Y-m-d")?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="form-control req" value="" />
            </div>
            <div class="col-md-4 form-group">                                                  
                <label for="type">Type</label>
                    <select name="type" id="type" class="form-control single-select" >
                        <option value="1" >Birthday</option>
                        <option value="2" >Anniversary</option>
                    </select>
            </div>
          
            <div class="col-md-4 form-group">                                                  
                <label for="relation">Relations</label>
                    <select name="relation" id="relation" class="form-control single-select" >
                        <option value="1">Self</option> 
                        <option value="2">Daughter</option>
                        <option value="3">Son</option>   
                        <option value="4">Friend</option>
                        <option value="5">Spouse</option>
                        <option value="6">Cousin</option>
                    </select>
            </div>
            <div class="col-md-4 ">
            <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form float-right mt-4" onclick="storePersonalDetail('personalDetail','savePersonalDetail');"><i class="fa fa-check"></i> Save</button>
        </div>
        </div>
    </form> 
     <div class="row">
        <div class="table-responsive">
            <table id="disctbl" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>Special Date</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Relation</th>
                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="discBody">
                    <?php
                        if (!empty($personalData)) :
                            $i=1;
                            foreach ($personalData as $row) :
                            $type = ($row->type == 1) ? 'Birthday' : 'Anniversary';
                            $relation = '';
                            if($row->relation == 1):
                                $relation = 'Self';
                            elseif($row->relation == 2):
                                $relation = 'Daughter';
                            elseif($row->relation == 3):
                                $relation = 'Son';
                            elseif($row->relation == 4):
                                $relation = 'Friend';
                            elseif($row->relation == 5):
                                $relation = 'Spouse';
                            else:
                                $relation = 'Cousin';
                            endif;    
                                $partyId = (!empty($dataRow->party_id)) ? $dataRow->party_id : $party_id;
                                $deleteParam = $row->id.','.$partyId.",'Personal Data'";
                                echo '<tr>
                                        <td>'.$i.'</td>
                                        <td>'.$row->special_date.'</td>
                                        <td>'.$row->name.'</td>
                                        <td>'.$type.'</td>
                                        <td>'.$relation.'</td>
                                        <td class="text-center">';
                                            echo '<a class="btn btn-outline-danger btn-delete" href="javascript:void(0)" onclick="trashPersonalDetail('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
                                        echo '</td>
                                    </tr>'; $i++;
                            endforeach;
                        else:
                            echo '<tr><td colspan="6" style="text-align:center;">No Data Found</td></tr>';
                        endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div> 
<script>
function storePersonalDetail(formId,fnsave,srposition=1){
	setPlaceHolder();
	if(fnsave == "" || fnsave == null){fnsave="save";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + controller + '/' + fnsave,
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
			initTable(srposition); //$('#'+formId)[0].reset(); //$(".modal").modal('hide');
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            $("#discBody").html(data.tbodyData);
            $("#party_id").val(data.partyId);
            $("#special_date").val("");
            $("#name").val("");
            $("#type").val("");
            $("#relation").val("");
        }else{
			initTable(srposition); //$('#'+formId)[0].reset(); //$(".modal").modal('hide');
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}		
	});
}

function trashPersonalDetail(id,party_id,name='Record'){
	var send_data = { id:id,party_id:party_id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to delete this '+name+'?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/deletePersonalDetail',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0)
							{
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							else
							{
                                $("#discBody").html(data.tbodyData);
                                $("#party_id").val(data.partyId);
                                $("#special_date").val("");
                                $("#name").val("");
                                $("#type").val("");
                                $("#relation").val("");
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
						}
					});
				}
			},
			cancel:{
                btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                action: function(){
                }
            }
		}
	});
}
</script>
