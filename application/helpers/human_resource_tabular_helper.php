<?php
    if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getHrDtHeader($page)
{
   /* Department Header */
   $data['departments'][] = ["name"=>"Action","style"=>"width:5%;","sortable"=>"FALSE"];
   $data['departments'][] = ["name"=>"#","style"=>"width:5%;","sortable"=>"FALSE"];
   $data['departments'][] = ["name"=>"Department Name"];
   $data['departments'][] = ["name"=>"Category"];

   
	/* Employee Header */
    $data['employees'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['employees'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>'center']; 
    $data['employees'][] = ["name"=>"User Name"];
    // $data['employees'][] = ["name"=>"Employee Code"];
    $data['employees'][] = ["name"=>"Contact No. (User Id)"];
    // $data['employees'][] = ["name"=>"Department"];
    // $data['employees'][] = ["name"=>"Role"];
    $data['employees'][] = ["name"=>"Active/In-Active","textAlign"=>'center'];

    /* Leave Setting Header */
    $data['leaveSetting'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['leaveSetting'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['leaveSetting'][] = ["name"=>"Leave Type"];
    $data['leaveSetting'][] = ["name"=>"Remark"];

    
	/* Leave Header */
    $data['leave'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['leave'][] = ["name"=>"Employee"];
    $data['leave'][] = ["name"=>"Leave Type"];
    $data['leave'][] = ["name"=>"From"];
    $data['leave'][] = ["name"=>"To"];
    $data['leave'][] = ["name"=>"Leave Days"];
    $data['leave'][] = ["name"=>"Reason"];
    $data['leave'][] = ["name"=>"Status"];
    
	/* Leave Approve Header */
    $data['leaveApprove'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['leaveApprove'][] = ["name"=>"Employee"];
    $data['leaveApprove'][] = ["name"=>"Leave Type"];
    $data['leaveApprove'][] = ["name"=>"From"];
    $data['leaveApprove'][] = ["name"=>"To"];
    $data['leaveApprove'][] = ["name"=>"Leave Days"];
    $data['leaveApprove'][] = ["name"=>"Reason"];
    $data['leaveApprove'][] = ["name"=>"Status"];

    /* HR Payroll*/
    $data['payroll'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['payroll'][] = ["name"=>"Month"];
    $data['payroll'][] = ["name"=>"Total Employees"];
    $data['payroll'][] = ["name"=>"Salary Amount"];

    /* Manual Attendance Header */
    $data['manualAttendance'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['manualAttendance'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['manualAttendance'][] = ["name"=>"Emp Code"];
    $data['manualAttendance'][] = ["name"=>"Employee"];
    $data['manualAttendance'][] = ["name"=>"Punch Time"];
    $data['manualAttendance'][] = ["name"=>"Reason"];
    
    /* Extra Hours Header */
    $data['extraHours'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['extraHours'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['extraHours'][] = ["name"=>"Employee"];
	$data['extraHours'][] = ["name"=>"Emp Code"];
    $data['extraHours'][] = ["name"=>"Extra Hours"];
    $data['extraHours'][] = ["name"=>"Reason"];

    /* Advance Salary  */
    $data['advanceSalary'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['advanceSalary'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['advanceSalary'][] = ["name"=>"Voucher No."];
    $data['advanceSalary'][] = ["name"=>"Voucher Date"];    
    $data['advanceSalary'][] = ["name"=>"Party Name"];
    $data['advanceSalary'][] = ["name"=>"Amount","style"=>"width:5%;","textAlign"=>"center"];
    $data['advanceSalary'][] = ["name"=>"Doc. No."];	
    $data['advanceSalary'][] = ["name"=>"Doc. Date"];
    $data['advanceSalary'][] = ["name"=>"Remark"];

    /* Designation Header */
    $data['designation'][] = ["name"=>"Action","style"=>"width:5%;","sortable"=>"FALSE"];
	$data['designation'][] = ["name"=>"#","style"=>"width:5%;","sortable"=>"FALSE","textAlign"=>"center"];
    $data['designation'][] = ["name"=>"Designation Name"];
    $data['designation'][] = ["name"=>"Remark"];
	
	return tableHeader($data[$page]);
}

/* Department Table Data */
function getDepartmentData($data){
    $deleteParam = $data->id.",'Department'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editDepartment', 'title' : 'Update Department'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->name,$data->category];
}


//* Employee Table Data */
function getEmployeeData($data){
    $deleteParam = $data->id.",'User'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editEmployee', 'title' : 'Update User'}";
    $salaryParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'empSalary', 'title' : 'Employee Salary', 'fnEdit' : 'getEmpSalary', 'fnsave' : 'updateEmpSalary'}";
    $empDocsParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'empDocs', 'title' : 'Employee Documents', 'fnEdit' : 'getEmpDocs', 'fnsave' : 'updateEmpDocs'}";
    $empNomParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'empNom', 'title' : 'Employee Nomination', 'fnEdit' : 'getEmpNom', 'fnsave' : 'updateEmpNom'}";
    $empEduParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'empEdu', 'title' : 'Employee Education', 'fnEdit' : 'getEmpEdu', 'fnsave' : 'updateEmpEdu'}";

    $salaryButton = '<a class="btn btn-info btn-salary permission-modify" href="javascript:void(0)" datatip="Employee Salary" flow="down" onclick="edit('.$salaryParam.');"><i class="fas fa-rupee-sign"></i></a>';
    
    $docsButton = '<a class="btn btn-warning btn-documents permission-modify" href="javascript:void(0)" datatip="Documents" flow="down" onclick="edit('.$empDocsParam.');"><i class="sl-icon-bag"></i></a>';

    $nomButton = '<a class="btn btn-purple btn-nomination permission-modify" href="javascript:void(0)" datatip="Nomination" flow="down" onclick="edit('.$empNomParam.');"><i class="icon-Receipt-3"></i></a>';

    $eduButton = '<a class="btn btn-facebook btn-education permission-modify" href="javascript:void(0)" datatip="Education" flow="down" onclick="edit('.$empEduParam.');"><i class="fa fa-graduation-cap"></i></a>';

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    // $empName = '<a href="'.base_url("hr/employees/empProfile/".$data->id).'" datatip="View Profile" flow="down">'.$data->emp_name.'</a>';

    $action = getActionButton($editButton.$deleteButton);//($salaryButton.$docsButton.$nomButton.$eduButton.)
    return [$action,$data->sr_no,$data->emp_name,$data->emp_contact,$data->active_html];//($data->emp_code,,$data->name,$data->emp_role)
}

    
/* Leave Setting Table Data */
function getLeaveSettingData($data){
    $deleteParam = $data->id.",'Leave Type'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editLeaveType', 'title' : 'Update Leave Type'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->leave_type,$data->remark];
}

/* Leave Table Data */
function getLeaveData($data){
    $deleteParam = $data->id.",'Leave'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editLeave', 'title' : 'Update Leave'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $approveButton = '<a class="btn btn-warning btn-leaveAction permission-modify" href="javascript:void(0)" data-id="'.$data->id.'" data-min_date="'.date("Y-m-d",strtotime($data->start_date)).'" datatip="Leave Action" flow="down"><i class="ti-direction-alt"></i></a>';
	
    if($data->showLeaveAction){$action = getActionButton( $approveButton.$editButton.$deleteButton);}
	else{$action = getActionButton( $editButton.$deleteButton);}
	
    return [$action,$data->emp_name,$data->leave_type,$data->start_date,$data->end_date,$data->total_days,$data->leave_reason,$data->status];
}

 /* Leave Approve Table Data */
function getLeaveApproveData($data){

    $approveButton = '<a class="btn btn-success btn-leaveAction permission-modify" href="javascript:void(0)" data-id="'.$data->id.'" data-min_date="'.date("Y-m-d",strtotime($data->start_date)).'" datatip="Leave Action" flow="down"><i class="ti-loop"></i></a>';
	
	$action = getActionButton( $approveButton);
	
    return [$action,$data->emp_name,$data->leave_type,$data->start_date,$data->end_date,$data->total_days,$data->leave_reason,$data->status];
}


/* Payroll Table Data */
function getPayrollData($data){
    $deleteParam = $data->id.",'Payroll'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editDepartment', 'title' : 'Update Payroll'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
	$mnth = '<a href="'.base_url('hr/payroll/getPayrollData/'.$data->month).'" target="_blank">'.date("F-Y",strtotime($data->month)).'</a>';
    return [$action,$data->sr_no,$mnth,$data->salary_sum];
}

/* Designation Table Data */
function getDesignationData($data){
    $deleteParam = $data->id.",'Designation'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editDesignation', 'title' : 'Update Designation'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->title,$data->description];
}
/* Manual Attendance Table Data */
function getManualAttendanceData($data){
    $deleteParam = $data->id.",'Manual Attendance'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editManualAttendance', 'title' : 'Manual Attendance'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
    $punchin = (!empty($data->punch_in)) ? formatDate($data->punch_in, 'd-m-Y H:i:s') : "";
    return [$action,$data->sr_no, $data->emp_code, $data->emp_name ,$punchin,$data->remark];
}
/* Extra Hours Table Data */
function getExtraHoursData($data){
    $deleteParam = $data->id.",'Extra Hours'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editExtraHours', 'title' : 'Extra Hours'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
     $punchin = (!empty($data->ex_hours)) ? ($data->ex_hours.":".$data->ex_mins):'';
    return [$action,$data->sr_no,$data->emp_name ,$data->emp_code,$punchin,$data->remark];
}


/* Advance Salary Data */
function getAdvanceSalaryData($data){
    $deleteParam = $data->id.",'AdvanceSalary'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'edit', 'title' : 'Update AdvanceSalary'}";    
   
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
   
    $action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,getPrefixNumber($data->trans_prefix,$data->trans_no),$data->trans_date,$data->opp_acc_name,$data->net_amount,$data->doc_no,$data->doc_date,$data->remark];
}
?>