<?php
class Hsn extends MY_Controller{

	private $indexPage = "hsn/index";
	private $form = "hsn/form";
	private $gstPercentage = Array(["rate"=>0,"val"=>'NIL'],["rate"=>0.25,"val"=>'0.25%'],["rate"=>3,"val"=>'3%'],["rate"=>5,"val"=>'5%'],["rate"=>12,"val"=>'12%'],["rate"=>18,"val"=>'18%'],["rate"=>28,"val"=>'28%']);

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "HSN";
		$this->data['headData']->controller = "hsn";
	}

	public function index(){
        $this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

	public function addHsn(){
		$this->data['gstPercentage'] = $this->gstPercentage;
		$this->load->view($this->form ,$this->data);
	}

	public function getDTRows(){    
        $result = $this->hsn->getDTRows($this->input->post()); 
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getHsnData($row);
        endforeach;
        $result['data'] = $sendData; ;
        $this->printJson($result); 
	}

	public function save(){
		$data = $this->input->post();
		$data['created_by'] = $this->session->userdata('loginId');
		$this->printJson($this->hsn->save($data));
	}
	
	public function edit(){
        $id = $this->input->post('id');
		$this->data['dataRow'] = $this->hsn->getHsn($id);
		$this->data['gstPercentage'] = $this->gstPercentage;
		$this->load->view($this->form ,$this->data);
	}
	
    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->hsn->delete($id));
        endif;
    }
}
?>