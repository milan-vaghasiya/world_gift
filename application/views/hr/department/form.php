<form>
	<div class="col-md-12">
        <div class="row">
			<input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
			
			<div class="col-md-12 form-group">
				<label for='name' class="control-label">Department Name</label>
				<input type="text" id="name" name="name" class="form-control req" value="<?=(!empty($dataRow->name))?$dataRow->name:""?>">
			</div>
			<div class="col-md-12 form-group">
				<label for="category">Category</label>
				<select name="category" id="category" class="form-control single-select req">
                    <?php
                        foreach($categoryData as  $key => $value):
							$selected = (!empty($dataRow->category) && $key == $dataRow->category)?"selected":"";
                            echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                        endforeach;
                    ?>
                </select>
			</div>
			
			<!-- <div class="col-md-12 form-group">
				<label for="empSelect">Select Employees who have rights to Apptove Leave</label>
                <select name="empSelect" id="empSelect" data-input_id="leave_authorities" class="form-control jp_multiselect" multiple="multiple">
                    <?php
                        foreach($empData as $row):
							$selected='';$leave_auth = (!empty($dataRow->leave_authorities)) ? explode(',',$dataRow->leave_authorities) : array();
                            if(!empty($dataRow->leave_authorities) && in_array($row->id,$leave_auth)){$selected = "selected";}
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->emp_name.'</option>';
                        endforeach;
                    ?>
                </select>
				<input type="hidden" name="leave_authorities" id="leave_authorities" value="" />
			</div> -->
		</div>
	</div>	
</form>
            
