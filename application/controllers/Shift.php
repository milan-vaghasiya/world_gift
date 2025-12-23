<?php
class Shift extends MY_Controller
{
    private $indexPage = "shift/index";
    private $shiftForm = "shift/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Shift";
		$this->data['headData']->controller = "shift";
        $this->data['headData']->pageUrl = "shift";
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows(){
        $result = $this->shiftModel->getDTRows($this->input->post());
        $sendData = array();$i=1;$count=0;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getShiftData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addShift(){
        $this->load->view($this->shiftForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['shift_name']))
			$errorMessage['shift_name'] = "Shift Name is required.";
        if(empty($data['start_time']))
			$errorMessage['start_time'] = "Start Time is required.";
        if(empty($data['production_hour']))
			$errorMessage['production_hour'] = "Production Hour is required.";
        if(empty($data['lunch_hour']))
			$errorMessage['lunch_hour'] = "Lunch Hour is required.";

        $data['shift_hour'] = ($data['lunch_hour'] + $data['production_hour']);
        if($data['shift_hour'] > 24)
            $errorMessage['lunch_hour'] = "Invalid Hours.";
		
        // $data['end_time'] = date('H:i:s',strtotime($data['start_time']) + ($data['shift_hour'] * 3600));
		
		$data['end_time'] = addTimeToDate($data['start_time'],$data['shift_hour'],$type="H",$dateFormat='H:i:s');
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->shiftModel->save($data));
        endif;
    }

    public function edit(){     
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->shiftModel->getShift($id);
        $this->load->view($this->shiftForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.','field_error'=>0,'field_error_message'=>NULL]);
        else:
            $this->printJson($this->shiftModel->delete($id));
        endif;
    }
}
?>