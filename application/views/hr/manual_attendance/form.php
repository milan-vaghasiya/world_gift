<form autocomplete="off">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="source" value="2" />
            <input type="hidden" name="attend_status" value="1" />
            <div class="col-md-12 form-group">
                <label for="emp_id">Employee</label>
                <select name="emp_id" id="emp_id" class="form-control single-select req">
                    <option value="">Select Employee</option>
                    <option value="<?=$loginID?>" <?=(!empty($dataRow->emp_id) && $loginID == $dataRow->emp_id)?"selected":"";?>>My Self</option>
                    <?php
                        foreach($empList as $row):
							if($loginID != $row->id):
								$selected = (!empty($dataRow->emp_id) && $row->id == $dataRow->emp_id)?"selected":"";
								echo '<option value="'.$row->id.'" '.$selected.'>['.$row->emp_code.'] '.$row->emp_name.'</option>';
							endif;
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="attendance_date">Attendance Date</label>
                <input type="date" name="attendance_date" id="attendance_date" class="form-control req" value="<?=(!empty($dataRow->attendance_date))?$dataRow->attendance_date:date("Y-m-d")?>" max="<?php date("Y-m-d")?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="punch_in">Punch Time</label>
                <input type="time" name="punch_in" id="punch_in" class="form-control req" value="<?=(!empty($dataRow->punch_in))?date('H:i', strtotime($dataRow->punch_in)):""?>" />
            </div>
			
            <!-- <div class="col-md-6 form-group">
                <label for="punch_out">Punch Out</label>
                <input type="time" name="punch_out" id="punch_out" class="form-control req" value="<?=(!empty($dataRow->punch_out))?date('H:i', strtotime($dataRow->punch_out)):""?>" />
            </div> -->
            
            <div class="col-md-12 form-group">
                <label for="remark">Reason</label>
                <textarea rows="2" name="remark" class="form-control req" placeholder="Reason" ><?=(!empty($dataRow->remark))?$dataRow->remark:""?></textarea>
            </div>
        </div>
    </div>
</form>
