<?php
class Offers extends MY_Controller
{
    private $indexPage = "offers/index";
    private $offerForm = "offers/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Offers";
		$this->data['headData']->controller = "offers";
        $this->data['headData']->pageUrl = "offers";
	}
	
	public function index(){
        $this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows(){
        $result = $this->offers->getDTRows($this->input->post());
        $sendData = array();$i=1;$count=0;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getOffersData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addOffers(){
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->load->view($this->offerForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['offer_date']))
			$errorMessage['offer_date'] = " Offer Date is required.";
        if(empty($data['offer_title']))
			$errorMessage['offer_title'] = "Offer Title is required.";
        if(empty($data['valid_from']))
			$errorMessage['valid_from'] = "Valid From is required.";
        if(empty($data['valid_to']))
			$errorMessage['valid_to'] = "Valid To is required.";
        if(empty($data['percentage']))
            $errorMessage['percentage'] = "Offer Percentage  is required.";
        if(empty($data['amount']))
            $errorMessage['amount'] = "Offer Amount is required.";
        if(empty($data['item_id']))
            $errorMessage['item_id'] = "Product is required.";
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else:
            $data['item_id']=implode(",",$data['item_id']);
            $data['created_by'] = $this->session->userdata('loginId');
            unset($data['item_id1']);
            $this->printJson($this->offers->save($data));
        endif;
    }

    public function edit($id){    
        //= $this->input->post('id');// print_r($id);exit;
        $this->data['dataRow'] = $this->offers->getOffer($id); //print_r($this->data['dataRow']);exit;
        
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->load->view($this->offerForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.','field_error'=>0,'field_error_message'=>NULL]);
        else:
            $this->printJson($this->offers->delete($id));
        endif;
    }
}
?>