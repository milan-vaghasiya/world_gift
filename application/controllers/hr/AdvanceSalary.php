<?php
class AdvanceSalary extends MY_Controller
{
    private $indexPage = "hr/advance_salary/index";
    private $formPage = "hr/advance_salary/form";
	private $paymentMode=['CASH','CHEQUE','IB','CARD','UPI'];
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "AdvanceSalary";
		$this->data['headData']->controller = "hr/advanceSalary";
        $this->data['headData']->pageUrl = "hr/advanceSalary";
	}

	public function index(){
		$this->data['tableHeader'] = getHrDtHeader('advanceSalary');
		$this->load->view($this->indexPage,$this->data);
	}

	public function getDtRows(){
		$result = $this->paymentVoucher->getDtRows($this->input->post(),"ADSALARY"); 
		$sendData = array(); $i=1;
		foreach($result['data'] as $row):
			$row->sr_no = $i++; $row->invNo="";

			$opp_party=$this->party->getParty($row->opp_acc_id);
			$opp_acc_name=(!empty($opp_party->party_name)?$opp_party->party_name:"");
			$row->opp_acc_name=$opp_acc_name;

			$vou_party=$this->party->getParty($row->vou_acc_id);
			$vou_acc_id=(!empty($vou_party->party_name)?$vou_party->party_name:"");
			$row->vou_acc_name=$vou_acc_id;

			$sendData[] = getAdvanceSalaryData($row);
		endforeach;
		$result['data'] = $sendData;
		$this->printJson($result);
	}

    public function addAdvanceSalary(){
		$this->data['partyData'] = $this->paymentTrans->getPartyList();
		$this->data['empData'] = $this->employee->getEmployeeList();
		$this->data['ledgerData'] = $this->party->getPartyListOnGroupCode(['"BA"','"CS"']);
		$this->data['paymentMode'] = $this->paymentMode;
		$this->data['trans_prefix'] = $this->transModel->getTransPrefix(16);
        $this->data['nextTransNo'] = $this->transModel->nextTransNo(16);		
		$this->load->view($this->formPage,$this->data);
	}

	public function save()
	{
		$data = $this->input->post();
		$errorMessage = array();
		if(empty($data['trans_date']))
			$errorMessage['trans_date'] = "Voucher Date is required.";
		if(empty($data['entry_type']))
			$errorMessage['entry_type'] = "Entry Type is required.";
		if(empty($data['opp_acc_id']))
			$errorMessage['opp_acc_id'] = "Party Name is required.";
		if(empty($data['vou_acc_id']))
			$errorMessage['vou_acc_id'] = "Ledger Name is required.";
		if(empty($data['trans_mode']))
			$errorMessage['trans_mode'] = "Payment Mode is required.";
		if(empty($data['net_amount']))
			$errorMessage['net_amount'] = "Amount is required.";

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
		else:
			$data['extra_fields'] = "ADSALARY";
			$data['party_id'] = $data['opp_acc_id'];
			$data['created_by'] = $this->session->userdata('loginId');
			$this->printJson($this->paymentVoucher->save($data));
		endif;
	}

	public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->paymentVoucher->getVoucher($data['id']);
		$this->data['partyData'] = $this->paymentTrans->getPartyList();
		$this->data['empData'] = $this->employee->getEmployeeList();
		$this->data['ledgerData'] = $this->party->getPartyListOnGroupCode(['"BA"','"CS"']);
		$this->data['paymentMode'] =$this->paymentMode;

        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)): 
			$this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.','field_error'=>0,'field_error_message'=>NULL]);
        else:
            $this->printJson($this->paymentVoucher->delete($id));
        endif;
    }

}
?>