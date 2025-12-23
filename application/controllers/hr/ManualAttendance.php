<?php
class ManualAttendance extends MY_Controller
{
    private $indexPage = "hr/manual_attendance/index";
    private $manualForm = "hr/manual_attendance/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Manual Attendance";
		$this->data['headData']->controller = "hr/manualAttendance";
	}
	
	public function index(){
        $this->data['tableHeader'] = getHrDtHeader("manualAttendance");
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows(){
        $result = $this->manualAttendance->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getManualAttendanceData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addManualAttendance(){
        $this->data['empData'] = $this->leave->getEmpData($this->session->userdata('loginId'));
        $this->data['empList'] = $this->employee->getEmployeeList();
        $this->data['loginID'] = $this->session->userdata('loginId');
        $this->load->view($this->manualForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['emp_id']))
			$errorMessage['emp_id'] = "Employee is required.";
        if(empty($data['attendance_date']))
			$errorMessage['attendance_date'] = "Attendance Date Time is required.";
        if(empty($data['punch_in']))
			$errorMessage['punch_in'] = "Punch Time is required.";
        if(empty($data['remark']))
			$errorMessage['remark'] = "Reason is required.";
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else:
            $data['punch_in'] = (!empty($data['punch_in'])) ? date('Y-m-d H:i:s', strtotime($data['attendance_date'].' '.$data['punch_in'])) : "";
            if(empty($data['punch_in'])){unset($data['punch_in']);}
            
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->manualAttendance->save($data));
        endif;
    }

    public function edit(){     
        $id = $this->input->post('id');
        $this->data['empData'] = $this->leave->getEmpData($this->session->userdata('loginId'));
        $this->data['empList'] = $this->employee->getEmployeeList();
        $this->data['dataRow'] = $this->manualAttendance->getManualAttendance($id);
        $this->data['loginID'] = $this->session->userdata('loginId');
        $this->load->view($this->manualForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.','field_error'=>0,'field_error_message'=>NULL]);
        else:
            $this->printJson($this->manualAttendance->delete($id));
        endif;
    }
}
?>