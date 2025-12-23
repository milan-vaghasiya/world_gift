<?php
class Employees extends MY_Controller
{
    private $indexPage = "hr/employee/index";
    private $employeeForm = "hr/employee/form";
    private $empSalary = "hr/employee/emp_Salary";
    private $empDocs = "hr/employee/emp_Docs";
    private $empNom = "hr/employee/emp_Nom";
    private $empEdu = "hr/employee/emp_Edu";
    private $profile = "hr/employee/emp_profile";
    private $empRole = ["1"=>"Admin","2"=>"Production Manager","3"=>"Accountant","4"=>"Sales Manager","5"=>"Purchase Manager","6"=>"Employee"];
    private $gender = ["M"=>"Male","F"=>"Female","O"=>"Other"];
    private $systemDesignation = [1=>"Machine Operator",2=>"Line Inspector",3=>"Setup Inspector",4=>"Process Setter"];
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Employees";
		$this->data['headData']->controller = "hr/employees";
        $this->data['headData']->pageUrl = "hr/employees";
	}
	
	public function index(){        
        $this->data['tableHeader'] = getHrDtHeader('employees');
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->employee->getDTRows($this->input->post());
        $sendData = array();$i=1;$count=0;
		foreach($result['data'] as $row):              
			$value = ($row->is_active == 1)?0:1;
			$checked = ($row->is_active == 1)?"checked":"";
			if($row->emp_role!=1):
				$count = 1;
				$row->active_html = '<input type="checkbox" id="activeInactive'.$i.'" class="bt-switch activeInactive" data-on-color="success"  data-off-color="danger" data-on-text="Active" data-off-text="Inactive" data-id="'.$row->id.'" data-val="'.$value.'" value="1" data-row_id="'.$i.'" '.$checked.'>';
			else:
				$row->active_html = '<input type="checkbox" id="activeInactive'.$i.'" class="bt-switch activeInactive" data-on-color="success"  data-off-color="danger" data-on-text="Active" data-off-text="Inactive" data-id="'.$row->id.'" data-val="'.$value.'" value="1" data-row_id="'.$i.'" '.$checked.'>';
			endif;
			$row->sr_no = $i++; 
			// $row->emp_role = $this->empRole[$row->emp_role];         
			$sendData[] = getEmployeeData($row);
		endforeach;
		
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addEmployee(){
        $this->data['deptRows'] = $this->department->getDepartmentList();
        $this->data['roleData'] = $this->empRole;
        $this->data['genderData'] = $this->gender;
        $this->data['descRows'] = $this->employee->getDesignation();
        $this->data['systemDesignation'] = $this->systemDesignation;
        $this->load->view($this->employeeForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();// print_r($data);exit;
        $errorMessage = array();
        if(empty($data['emp_name']))
            $errorMessage['emp_name'] = "User name is required.";
        // if(empty($data['emp_role']))
        //     $errorMessage['emp_role'] = "Role is required.";
        if(empty($data['emp_contact']))
            $errorMessage['emp_contact'] = "Contact No. is required.";
        if(empty($data['id'])):
            if(empty($data['emp_password']))
                $errorMessage['emp_password'] = "Password is required.";
        endif;
        // if(empty($data['emp_dept_id']))
        //     $errorMessage['emp_dept_id'] = "Department is required.";
        // if(empty($data['emp_designation']))
        // {
        //     if(empty($data['designationTitle']))
        //         $errorMessage['emp_designation'] = "Designation is required.";
        //     else
        //         $data['emp_designation'] = $this->employee->saveDesignation($data['designationTitle'],$data['emp_dept_id']);
        // }
        // unset($data['designationTitle']);
        // if(empty($data['id'])):
            /* if(empty($data['emp_password']))
                $errorMessage['emp_password'] = "Password is required.";
            if(!empty($data['emp_password']) && $data['emp_password'] != $data['emp_password_c'])
                $errorMessage['emp_password_c'] = "Confirm Password not match."; */
            // $data['emp_password'] = "";
        // endif;
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);

        else:
            $data['emp_name'] = ucwords($data['emp_name']);
            $data['created_by'] = $this->session->userdata('loginId');            
            $this->printJson($this->employee->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['deptRows'] = $this->department->getDepartmentList();
        $this->data['roleData'] = $this->empRole;
        $this->data['genderData'] = $this->gender;
        $this->data['descRows'] = $this->employee->getDesignation();
        $this->data['systemDesignation'] = $this->systemDesignation;
        $result = $this->employee->getEmp($id);
        //$result->designation = $this->employee->getDesignation($result->emp_dept_id,$result->emp_designation)['result'];
        $this->data['dataRow'] = $result;
        $this->load->view($this->employeeForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.','field_error'=>0,'field_error_message'=>NULL]);
        else:
            $this->printJson($this->employee->delete($id));
        endif;
    }

    public function activeInactive(){
        $id = $this->input->post('id');
        $value = $this->input->post('value');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.','field_error'=>0,'field_error_message'=>NULL]);
        else:
            $this->printJson($this->employee->activeInactive($id,$value));
        endif;
    }
    
    public function changePassword(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['old_password']))
            $errorMessage['old_password'] = "Old Password is required.";
        if(empty($data['new_password']))
            $errorMessage['new_password'] = "New Password is required.";
        if(empty($data['cpassword']))
            $errorMessage['cpassword'] = "Confirm Password is required.";
        if(!empty($data['new_password']) && !empty($data['cpassword'])):
            if($data['new_password'] != $data['cpassword'])
                $errorMessage['cpassword'] = "Confirm Password and New Password is Not match!.";
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);

		else:
            $id = $this->session->userdata('loginId');
			$result =  $this->employee->changePassword($id,$data);
			$this->printJson($result);
		endif;
    }

    public function getDesignation(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.','field_error'=>0,'field_error_message'=>NULL]);
        else:
            $this->printJson($this->employee->getDesignation($id));
        endif;
    }

    public function getEmpSalary(){
        $emp_id = $this->input->post('id');
        $this->data['dataRow'] = $this->employee->getEmpSalary($emp_id);
        $this->data['emp_id'] = $emp_id;
        $this->load->view($this->empSalary,$this->data);
    }

    public function updateEmpSalary(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['salary_basis']))
            $errorMessage['salary_basis'] = "Salary Basis is required.";
        if(empty($data['basic_salary']))
            $errorMessage['basic_salary'] = "Basic Salary is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->employee->saveEmpSalary($data));
        endif;
    }

    public function getEmpDocs(){
        $emp_id = $this->input->post('id');
        $this->data['dataRow'] = $this->employee->getEmpDocs($emp_id);
        $this->data['emp_id'] = $emp_id;
        $this->load->view($this->empDocs,$this->data);
    }

    public function updateEmpDocs(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['emp_id']))
            $errorMessage['emp_id'] = "Employee id is required.";
        if(empty($data['old_uan_no']))
            $errorMessage['old_uan_no'] = "Old Uan No is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->employee->saveEmpDocs($data));
        endif;
    }

    public function getEmpNom(){
        $emp_id = $this->input->post('id');
        $this->data['nomData'] = $this->employee->getNominationData($emp_id);
        $this->data['genderData'] = $this->gender;
        $this->data['emp_id'] = $emp_id;
        $this->load->view($this->empNom,$this->data);
    }

    public function updateEmpNom(){
        $data = $this->input->post();
		$errorMessage = array();
		if(empty($data['nom_name'][0])){
			$errorMessage['nom_name'] = "Name is required.";
		}
		
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
		else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->employee->saveEmpNom($data));
		endif;
    }

    public function getEmpEdu(){
        $emp_id = $this->input->post('id');
        $this->data['eduData'] = $this->employee->getEducationData($emp_id);
        $this->data['emp_id'] = $emp_id;
        $this->load->view($this->empEdu,$this->data);
    }

    public function updateEmpEdu(){
        $data = $this->input->post();
		$errorMessage = array();
		if(empty($data['course'][0])){
			$errorMessage['course'] = "Course is required.";
		}
		
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
		else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->employee->saveEmpEdu($data));
		endif;
    }

    // public function empProfile($emp_id){
    //     $this->data['empData'] = $this->employee->getEmployee($emp_id);
    //     $this->data['empSalary'] = $this->employee->getEmpSalary($emp_id);
    //     $this->data['empDocs'] = $this->employee->getEmpDocs($emp_id);
    //     $this->data['empNom'] = $this->employee->getNominationData($emp_id);
    //     $this->data['empEdu'] = $this->employee->getEducationData($emp_id);
    //     $this->load->view($this->profile,$this->data);
    // }

	public function empPermission(){
        $this->data['empList'] = $this->employee->getEmpList();
        $this->data['permission'] = $this->permission->getPermission();
        $this->load->view('hr/employee/emp_permission',$this->data);
    }

    public function savePermission(){
        $data = $this->input->post();
        $errorMessage = array();
        
        if(empty($data['emp_id']))
            $errorMessage['emp_id'] = "Employee name is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else:
            $this->printJson($this->permission->save($data));
        endif;
    }

    public function editPermission(){
        $emp_id = $this->input->post('emp_id');
        $this->printJson($this->permission->editPermission($emp_id));
    }
    
    public function changeEmpPsw(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.','field_error'=>0,'field_error_message'=>NULL]);
        else:
            $this->printJson($this->employee->changeEmpPsw($id));
        endif;
    }
}
?>