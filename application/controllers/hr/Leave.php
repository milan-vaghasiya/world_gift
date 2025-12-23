<?php
class Leave extends MY_Controller
{
    private $indexPage = "hr/leave/index";
    private $leaveForm = "hr/leave/leave_form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Leave";
		$this->data['headData']->controller = "hr/leave";
		$this->data['headData']->pageUrl = "hr/leave";
	}
	
	public function index(){
        $this->data['tableHeader'] = getHrDtHeader('leave');
        $this->load->view($this->indexPage,$this->data);
    }
	
	public function getDTRows(){
		$postData = $this->input->post();
		$postData['login_emp_id']=$this->session->userdata('loginId');
        $result = $this->leave->getDTRows($postData);
        $sendData = array();$i=1;$count=0;
		
		foreach($result['data'] as $row):
			$row->sr_no = $i++;
			$row->start_date = date('d-m-Y',strtotime($row->start_date));
			$row->end_date = date('d-m-Y',strtotime($row->end_date));
			$row->total_days = $row->total_days. ($row->total_days>1) ? $row->total_days.' Days' : $row->total_days.' Day';
			$row->emp_name = $row->emp_name .'<br><small>'.$row->title.'</small>';
			if($row->approve_status == 2):
				$row->status = '<span class="badge badge-pill badge-danger m-1">Declined</span>';$row->approveButtonLabel = 'Declined';
			elseif($row->approve_status == 1):
				$row->status = '<span class="badge badge-pill badge-success m-1">Approved</span>';$row->approveButtonLabel = 'Approved';
			else:
				$row->status = '<span class="badge badge-pill badge-info m-1">Pending</span>';$row->approveButtonLabel = 'Pending';
			endif;
			$row->showLeaveAction = false;
			if($row->emp_id != $this->session->userdata('loginId'))
			{
				$emp1 = $this->leave->getEmpData($this->session->userdata('loginId'));
				$emp2 = $this->leave->getEmpData($row->emp_id);
				if($emp1->emp_dept_id == $emp2->emp_dept_id)
				{
					
				}
			}
			$sendData[] = getLeaveData($row);
		endforeach;
		
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addLeave(){
        $this->data['leaveType'] = $this->leave->getLeaveType();
		// print_r($this->data['leaveType']);exit;
        $this->data['empData'] = $this->leave->getEmpData($this->session->userdata('loginId'));
        $this->load->view($this->leaveForm,$this->data);
    }

    public function getEmpLeaves(){
		$login_id = $this->session->userdata('loginId');
		$start_date=date("Y-m-d",strtotime($this->session->userdata('startDate')));
		$end_date=date("Y-m-d",strtotime($this->session->userdata('endDate')));
        $this->printJson($this->leave->getEmpLeaves($login_id,$this->input->post('leave_type_id'))[0],$start_date,$end_date);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['leave_type_id'])):
            $errorMessage['leave_type_id'] = "Leave Type is required.";
		else:
			$login_id = $this->session->userdata('loginId');
			$start_date=date("Y-m-d",strtotime($this->session->userdata('startDate')));
			$end_date=date("Y-m-d",strtotime($this->session->userdata('endDate')));
			$empLD = $this->leave->getEmpLeaves($login_id,$data['leave_type_id'],$start_date,$end_date)[0];
			if($data['total_days'] > $empLD['remain_leaves'])
				$errorMessage['generalError'] = "You have not remain leaves for selected leave type";
		endif;
		if(empty($data['start_date']))
            $errorMessage['start_date'] = "Start Date is required.";
		if(empty($data['end_date']))
            $errorMessage['end_date'] = "End Date is required.";
		if(empty($data['total_days']))
            $errorMessage['generalError'] = "You have to apply atleast 1 Day Leave";
			
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else:
			$data['leave_type'] = $this->leaveSetting->getLeaveType($data['leave_type_id'])->leave_type;
			$data['created_by'] = $this->session->userdata('loginId');
			$this->printJson($this->leave->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['leaveType'] = $this->leave->getLeaveType();
        $this->data['empData'] = $this->leave->getEmpData($this->session->userdata('loginId'));
        $this->data['dataRow'] = $this->leave->getLeave($id);
        $this->load->view($this->leaveForm,$this->data);
    }

    public function approveLeave(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['id']))
            $errorMessage['generalError'] = "Leave is not defined.";
		if(empty($data['approved_date']))
            $errorMessage['approved_date'] = "Approve Date is required.";
		if(empty($data['approve_status']))
            $errorMessage['approve_status'] = "Status is required.";
			
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else:
			$data['approved_by'] = $this->session->userdata('loginId');
			$this->printJson($this->leave->save($data));
        endif;
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.','field_error'=>0,'field_error_message'=>NULL]);
        else:
            $this->printJson($this->leave->delete($id));
        endif;
    }
}
?>