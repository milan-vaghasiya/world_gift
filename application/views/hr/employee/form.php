<form autocomplete="off">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />

            <div class="col-md-12 form-group">
                <label for="emp_name">User Name</label>
                <input type="text" name="emp_name" class="form-control text-capitalize req" value="<?=(!empty($dataRow->emp_name))?$dataRow->emp_name:""; ?>" />
            </div>
           
            <div class="col-md-12 form-group">
                <label for="emp_contact">Phone No.(User Id)</label>
                <input type="number" name="emp_contact" class="form-control numericOnly req" value="<?=(!empty($dataRow->emp_contact))?$dataRow->emp_contact:""?>" />
            </div>
            <?php if(empty($dataRow->id)) { ?>
            <div class="col-md-12 form-group">
                <label for="emp_password">User Password</label>
                <input type="text" name="emp_password" class="form-control req" value="<?=(!empty($dataRow->emp_password))?$dataRow->emp_password:""?>" />
            </div>
            <?php } ?>
            <!-- <div class="col-md-12 form-group">
                <label for="emp_password">Emp. Password</label>
                <input type="number" name="emp_password"  id="emp_password" class="form-control numericOnly req" value="<?=(!empty($dataRow->emp_password))?$dataRow->emp_password:""?>" />
            </div> -->
            <!-- <div class="col-md-4 form-group">
                <label for="father_name">Father Name</label>
                <input type="text" name="father_name" class="form-control" value="<?=(!empty($dataRow->father_name))?$dataRow->father_name:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="husband_name">Spouse Name</label>
                <input type="text" name="husband_name" class="form-control" value="<?=(!empty($dataRow->husband_name))?$dataRow->husband_name:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="emp_alt_contact">Alt. Phone</label>
                <input type="number" name="emp_alt_contact" class="form-control numericOnly" value="<?=(!empty($dataRow->emp_alt_contact))?$dataRow->emp_alt_contact:""?>" />
            </div> -->

            <!-- <div class="col-md-4 form-group">
                <label for="marital_status">Marital Status</label>
                <select name="marital_status" id="marital_status" class="form-control " >
                    <option value="Married" <?=(!empty($dataRow->marital_status) && $dataRow->marital_status == "Married")?"selected":""?>>Married</option>
                    <option value="UnMarried" <?=(!empty($dataRow->marital_status) && $dataRow->marital_status == "UnMarried")?"selected":""?>>UnMarried</option>
                    <option value="Widow" <?=(!empty($dataRow->marital_status) && $dataRow->marital_status == "Widow")?"selected":""?>>Widow</option>
                </select>
            </div> -->
            <!-- <div class="col-md-4 form-group">
                <label for="emp_gender">Gender</label>
                <select name="emp_gender" id="emp_gender" class="form-control single-select">
                    <option value="">Select Gender</option>
                    <?php
                        foreach($genderData as $value):
                            $selected = (!empty($dataRow->emp_gender) && $value == $dataRow->emp_gender)?"selected":"";
                            echo '<option value="'.$value.'" '.$selected.'>'.$value.'</option>';
                        endforeach;
                    ?>
                </select>
            </div> -->
            <!-- <div class="col-md-4 form-group">
                <label for="emp_experience">Experience</label>
                <input type="text" name="emp_experience" class="form-control" value="<?=(!empty($dataRow->emp_experience))?$dataRow->emp_experience:""?>" />
            </div>

            <div class="col-md-4 form-group">
                <label for="mark_id">Mark of Identification</label>
                <input type="text" name="mark_id" class="form-control" value="<?=(!empty($dataRow->mark_id))?$dataRow->mark_id:""?>" />
            </div>
            <div class="col-md-4">
                <label for="emp_birthdate">Date of Birth</label>
                <input type="date" name="emp_birthdate" id="emp_birthdate" class="form-control" value="<?=(!empty($dataRow->emp_birthdate))?$dataRow->emp_birthdate:date("Y-m-d")?>" max="<?=(!empty($dataRow->emp_birthdate))?$dataRow->emp_birthdate:date("Y-m-d")?>" />
            </div>
            <div class="col-md-4">
                <label for="emp_joining_date">Joining Date</label>
                <input type="date" name="emp_joining_date" id="emp_joining_date" class="form-control" value="<?=(!empty($dataRow->emp_joining_date))?$dataRow->emp_joining_date:date("Y-m-d")?>" max="<?=(!empty($dataRow->emp_joining_date))?$dataRow->emp_joining_date:date("Y-m-d")?>" />
            </div>
             -->
            <!-- <div class="col-md-3 form-group">
                <label for="emp_dept_id">Department</label>
                <select name="emp_dept_id" id="emp_dept_id" class="form-control single-select req">
                    <option value="">Select Department</option>
                    <?php
                        foreach($deptRows as $row):
                            $selected = (!empty($dataRow->emp_dept_id) && $row->id == $dataRow->emp_dept_id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div> -->
            <!-- <div class="col-md-3 from-group">
                <label for="emp_designation">Designation</label>
                <select name="emp_designation" id="emp_designation" class="form-control single-select req" tabindex="-1">
                    <option value="">Select Designation</option>
                    <?php
                        foreach($descRows as $row):
                            $selected = (!empty($dataRow->emp_designation) && $row->id == $dataRow->emp_designation)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->title.'</option>';
                        endforeach;
                    ?>
                </select>
                <input type="hidden" id="designationTitle" name="designationTitle" value="" />
            </div> -->
            <!-- <div class="col-md-3 form-group">
                <label for="emp_sys_desc_id">System Designation</label>
                <select name="emp_sys_desc_id" id="emp_sys_desc_id" class="form-control single-select">
                    <option value="">Select System Designation</option>
                    <?php
                        foreach($systemDesignation as $key=>$value):
                            $selected = (!empty($dataRow->emp_sys_desc_id) && $dataRow->emp_sys_desc_id == $key)?"selected":"";
                            echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                        endforeach;
                    ?>
                </select>
            </div> -->
            <!-- <div class="col-md-3 form-group">
                <label for="emp_role">Role</label>
                <select name="emp_role" id="emp_role" class="form-control single-select req">
                    <option value="">Select Role</option>
                    <?php
                        foreach($roleData as $key => $value):
                            $selected = (!empty($dataRow->emp_role) && $key == $dataRow->emp_role)?"selected":"";
                            echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                        endforeach;
                    ?>
                </select>
            </div> -->
            
            <!-- Mark of Identification -->

            <!-- <div class="col-md-12 form-group">
                <label for="emp_address">Address</label>
                <textarea name="emp_address" class="form-control" style="resize:none;" rows="1"><?=(!empty($dataRow->emp_address))?$dataRow->emp_address:""?></textarea>
            </div>

            <div class="col-md-12 form-group">
                <label for="pemenant_address">Pemenant Address</label>
                <textarea name="pemenant_address" class="form-control" style="resize:none;" rows="1"><?=(!empty($dataRow->pemenant_address))?$dataRow->pemenant_address:""?></textarea>
            </div> -->

            <?php if(empty($dataRow->id)): ?>
                <!--<div class="col-md-6 form-group">
                    <label for="emp_password">Password</label>
                    <div class="input-group"> 
                        <input type="password" name="emp_password" id="emp_password" class="form-control pswType req" value="">
                        <div class="input-group-append">
                            <button type="button" class="btn waves-effect waves-light btn-outline-primary pswHideShow"><i class="fa fa-eye"></i></button>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 form-group">
                    <label for="emp_password_c">Confirm Password</label>
                    <input type="text" name="emp_password_c" id="emp_password_c" class="form-control req" value="">
                </div>-->
            <?php endif; ?>
        </div>
    </div>
</form>

<!-- <script>
$(document).ready(function(){
    $(document).on('keyup','#emp_designationc',function(){
        $('#designationTitle').val($(this).val());
    });
});
</script> -->