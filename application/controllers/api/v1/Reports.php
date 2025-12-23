<?php
class Reports extends MY_Apicontroller{
    public function __construct(){
        parent::__construct();
    }
    
    public function getSalesByEmp(){
		$postData = $this->input->post();
		if(!isset($postData['from_date']) OR empty($postData['from_date'])){$postData['from_date']=$this->startYearDate;}
		else{$postData['from_date']=date('Y-m-d',strtotime($postData['from_date']));}
		if(!isset($postData['to_date']) OR empty($postData['to_date'])){$postData['to_date']=$this->endYearDate;}
		else{$postData['to_date']=date('Y-m-d',strtotime($postData['to_date']));}
        $this->data['salesData'] = $this->salesReportModel->getSalesByEmp($postData);
        $this->printJson(['status'=>1,'message'=>'Recored found.','field_error'=>0,'field_error_message'=>null,'data'=>$this->data]);
    }

    public function getIncentiveByEmp(){
		$postData = $this->input->post();
		if(!isset($postData['from_date']) OR empty($postData['from_date'])){$postData['from_date']=$this->startYearDate;}
		else{$postData['from_date']=date('Y-m-d',strtotime($postData['from_date']));}
		if(!isset($postData['to_date']) OR empty($postData['to_date'])){$postData['to_date']=$this->endYearDate;}
		else{$postData['to_date']=date('Y-m-d',strtotime($postData['to_date']));}
        $this->data['salesData'] = $this->salesReportModel->getIncentiveByEmp($postData);
        $this->printJson(['status'=>1,'message'=>'Recored found.','field_error'=>0,'field_error_message'=>null,'data'=>$this->data]);
    }
}
?>