<?php
    if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getConfigDtHeader($page)
{
    /* terms header */
    $data['terms'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['terms'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['terms'][] = ["name"=>"Title"];
    $data['terms'][] = ["name"=>"Type"];
    $data['terms'][] = ["name"=>"Conditions"];

      /* Shift Header */
    $data['shift'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['shift'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['shift'][] = ["name"=>"Shift Name"];
	$data['shift'][] = ["name"=>"Start Time"];
	$data['shift'][] = ["name"=>"End Time"];
	$data['shift'][] = ["name"=>"Production Time"];
	$data['shift'][] = ["name"=>"Lunch Time"];
	$data['shift'][] = ["name"=>"Shift Hour"];

    /* Currency Header*/
    $data['currency'][] = ["name"=>"#","style"=>"width:5%;","srnoPosition"=>0];
    $data['currency'][] = ["name"=>"Currency Name"];
    $data['currency'][] = ["name"=>"Code"];
    $data['currency'][] = ["name"=>"Symbol"];
    $data['currency'][] = ["name"=>"Rate in INR"];
    
    /* Material Grade header */
    $data['materialGrade'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['materialGrade'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['materialGrade'][] = ["name"=>"Material Grade"];
    $data['materialGrade'][] = ["name"=>"scrap Group"];
    $data['materialGrade'][] = ["name"=>"Colour Code"];

    /* Attendance Policy Header */
    $data['attendancePolicy'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['attendancePolicy'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['attendancePolicy'][] = ["name"=>"Policy Name"];
    $data['attendancePolicy'][] = ["name"=>"Early In"];
    $data['attendancePolicy'][] = ["name"=>"No. Early In"];
    $data['attendancePolicy'][] = ["name"=>"Early Out"];
    $data['attendancePolicy'][] = ["name"=>"No. Early Out"];
    $data['attendancePolicy'][] = ["name"=>"Short Leave Hour"];
    $data['attendancePolicy'][] = ["name"=>"No. Short Leave"];
     
    /* Main Menu Header */
    $data['mainMenuConf'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['mainMenuConf'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['mainMenuConf'][] = ["name"=>"Menu Icon"];
    $data['mainMenuConf'][] = ["name"=>"Menu Name"];
    $data['mainMenuConf'][] = ["name"=>"Menu Sequence"];
    $data['mainMenuConf'][] = ["name"=>"Is Master"];

    /* Sub Menu Header */
    $data['subMenuConf'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['subMenuConf'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['subMenuConf'][] = ["name"=>"Sub Menu Sequence"];
    $data['subMenuConf'][] = ["name"=>"Sub Menu Icon"];
    $data['subMenuConf'][] = ["name"=>"Sub Menu Name"];
    $data['subMenuConf'][] = ["name"=>"Sub Menu Contoller Name"];
    $data['subMenuConf'][] = ["name"=>"Main Menu"];
    $data['subMenuConf'][] = ["name"=>"Is Report"];

    /* Tax Master Header */
    $data['taxMaster'][] = ["name" => "Action", "style" => "width:5%;"];
    $data['taxMaster'][] = ["name" => "#", "style" => "width:5%;"];
    $data['taxMaster'][] = ["name" => "Tax Name"];
    $data['taxMaster'][] = ["name" => "Tax Type"];
    $data['taxMaster'][] = ["name" => "Calcu. Type"];
    $data['taxMaster'][] = ["name" => "Ledger Name"];
    $data['taxMaster'][] = ["name" => "Is Active"];
    $data['taxMaster'][] = ["name" => "Add/Deduct"];

    /* Expense Master Header */
    $data['expenseMaster'][] = ["name" => "Action", "style" => "width:5%;"];
    $data['expenseMaster'][] = ["name" => "#", "style" => "width:5%;"];
    $data['expenseMaster'][] = ["name" => "Exp. Name"];
    $data['expenseMaster'][] = ["name" => "Entry Name"];
    $data['expenseMaster'][] = ["name" => "Sequence"];
    $data['expenseMaster'][] = ["name" => "Calcu. Type"];
    $data['expenseMaster'][] = ["name" => "Ledger Name"];
    $data['expenseMaster'][] = ["name" => "Is Active"];
    $data['expenseMaster'][] = ["name" => "Add/Deduct"];
    
    /* Contact Directory header */
   $data['contactDirectory'][] = ["name"=>"Action","style"=>"width:5%;"];
   $data['contactDirectory'][] = ["name"=>"#","style"=>"width:5%;"]; 
   $data['contactDirectory'][] = ["name"=>"Company Name"];
   $data['contactDirectory'][] = ["name"=>"Contact Person"];
   $data['contactDirectory'][] = ["name"=>"Contact No."];
   $data['contactDirectory'][] = ["name"=>"Email"];   
   $data['contactDirectory'][] = ["name"=>"Service"];
   $data['contactDirectory'][] = ["name"=>"Remark"];
     
	return tableHeader($data[$page]);
}

/* Terms Table Data */
function getTermsData($data){
    $deleteParam = $data->id.",'Terms'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editTerms', 'title' : 'Update Terms'}";

    $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->title,str_replace(',',', ',$data->type),$data->conditions];
}

    /* get Shift Data */
function getShiftData($data){
    $deleteParam = $data->id.",'Shift'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editShift', 'title' : 'Update Shift'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->shift_name,$data->shift_start,$data->shift_end,$data->production_hour,$data->total_lunch_time,$data->total_shift_time];
}


/* Currency Data */
function getCurrencyData($data){
    return [$data->sr_no,$data->currency_name,$data->currency,$data->code2000,$data->inrinput];
}
  
/* Material Grade Table Data */
function getMaterialData($data){
    $deleteParam = $data->id.",'Material Grade'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'edit', 'title' : 'Update Material Grade'}";

    $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->material_grade,$data->group_name,$data->color_code];
}

/* get Attendance Policy Data */
function getAttendancePolicyData($data){
    $deleteParam = $data->id.",'Attendance Policy'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editAttendancePolicy', 'title' : 'Update Attendance Policy'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$action = getActionButton($editButton.$deleteButton);

    return [$action,$data->sr_no,$data->policy_name,$data->early_in,$data->no_early_in,$data->early_out,$data->no_early_out,$data->short_leave_hour,$data->no_short_leave];
}

/* Main Menu Table Data */
function getMainMenuConfData($data){
    $deleteParam = $data->id.",'MainMenuConf'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editmainMenuConf', 'title' : 'Update MainMenuConf'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->menu_icon,$data->menu_name,$data->menu_seq,$data->is_master];
}

/* Sub Menu Table Data */
function getSubMenuConfData($data){
    $deleteParam = $data->id.",'SubMenuConf'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editsubMenuConf', 'title' : 'Update SubMenuConf'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	$isReport=($data->is_report == 0)?"No":"Yes";
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->sub_menu_seq,$data->sub_menu_icon,$data->sub_menu_name,$data->sub_controller_name,$data->menu_name,$isReport];
}

/* Expense Master Table Data */
function getExpenseMasterData($data){
    $deleteParam = $data->id.",'Expense'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editExpense', 'title' : 'Update Expense'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton.$deleteButton);    

    return [$action,$data->sr_no,$data->exp_name,$data->entry_name,$data->seq,$data->calc_type_name,$data->party_name,$data->is_active_name,$data->add_or_deduct_name];
}

function getTaxMasterData($data){
    $deleteParam = $data->id.",'Tax'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editTax', 'title' : 'Update Tax'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton.$deleteButton);    

    return [$action,$data->sr_no,$data->name,$data->tax_type_name,$data->calc_type_name,$data->acc_name,$data->is_active_name,$data->add_or_deduct_name];
}

function getContactDirectoryData($data){
    $deleteParam = $data->id.",'Contact'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editTerms', 'title' : 'Update Terms'}";

    $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->comapny_name,$data->contact_person,$data->contact_number,$data->email,$data->service,$data->remark];
}
?>