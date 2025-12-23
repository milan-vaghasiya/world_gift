<?php
class Party extends MY_Apicontroller{
    public function __construct(){
        parent::__construct();
    }
    
    public function getCustomerList($off_set=0){
        $limit = (isset($_REQUEST['per_page']) && !empty($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $search = (isset($_REQUEST['search']) && !empty($_REQUEST['search']))?$_REQUEST['search']:"";
        $item_type = (isset($_REQUEST['item_type']) && !empty($_REQUEST['item_type']))?$_REQUEST['item_type']:1;
        $postData = ['off_set'=>$off_set,'limit'=>$limit,'search'=>$search,'party_category'=>'1,5'];
        $this->data['customerList'] = $this->party->getCustomerList_api($postData);
        $this->printJson(['status'=>1,'message'=>'Recored found.','field_error'=>0,'field_error_message'=>null,'data'=>$this->data]);
    }
	
    public function save(){
		$postData = $this->input->post();
        $errorMessage = array();
        if (empty($postData['party_category']))
            $errorMessage['party_category'] = "Category is required.";
        if (empty($postData['party_name']))
            $errorMessage['party_name'] = "Company name is required.";
        if (empty($postData['party_phone']))
            $errorMessage['party_phone'] = "Contact No. is required.";        
        if (!empty($errorMessage)) :
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else :
            $postData['party_name'] = ucwords($postData['party_name']);
            $postData['currency'] = 'INR';$postData['balance_type'] = 1;$postData['opening_balance'] = 0;
            $postData['party_address'] = '';$postData['party_pincode'] = '';$postData['delivery_address'] = '';$postData['delivery_pincode'] = '';
            $result = $this->party->save($postData);
			if($result['status']==1):
				$this->data['customerDetail']=$this->party->getParty($result['insert_id'],$select='party_master.id, party_master.party_name, party_master.party_phone, party_master.gstin');
				$this->printJson(['status'=>1,'message'=>'Customer Saved Successfully','field_error'=>0,'field_error_message'=>null,'data'=>$this->data]);
			else:
				$this->printJson($result);
			endif;
        endif;
        
    }
}
?>