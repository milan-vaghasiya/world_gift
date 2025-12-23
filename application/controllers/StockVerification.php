<?php
class StockVerification extends MY_Controller
{
    private $indexPage = "stock_verification/index";
    private $formPage = "stock_verification/form";
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Stock Verification";
		$this->data['headData']->controller = "stockVerification";
		$this->data['headData']->pageUrl = "stockVerification";
	}

    public function index(){
        $this->data['pageHeader'] = 'STOCK VERIFICATION REPORT';
        $this->data['dataUrl'] = 'getStockVerification/1';
        $this->data['tableHeader'] = getStoreDtHeader("stockVerification");
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->stockVerify->getDTRows($data); 
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            
            $itmStock = $this->store->getLocationWiseItemStock($row->item_id,$this->RTD_STORE->id);
            $row->system_stock = 0;
            if(!empty($itmStock->qty)){ $row->system_stock = $itmStock->qty; }
            
            $sendData[] = getStockVerificationData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    // public function getStockVerification($item_type=""){
    //     $result = $this->stockVerify->getItemStock($this->input->post(),$item_type);
    //     $sendData = array();$i=1;
    //     foreach($result['data'] as $row):
    //         $editParam = "{'id' : ".$row->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editStock', 'title' : 'Update Stock'}";
    //         $editButton = '<a class="btn btn-success btn-sm waves-effect waves-light btn-edit" href="javascript:void(0)" datatip="Edit" onclick="editStock('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
            
    //         $pqData = $this->stockVerify->getPhysicalQty($row->id);
    //         $pQty = (!empty($pqData[0]))?$pqData[0]->total_qty:0;
    //         $varQty = $row->qty - $pQty;
            
    //         $sendData[] = [$i++,$row->item_name,$row->item_code,floatVal($row->qty),floatVal($pQty),floatVal($varQty),$editButton];
    //     endforeach;
    //     $result['data'] = $sendData;
    //     $this->printJson($result);
    // }

    public function editStock(){     
        $data = $this->input->post(); 
        $this->data['dataRow'] = $this->stockVerify->getStoreVerification($data['id']);
        $this->data['system_stock'] = $data['system_stock']; 
        $this->data['variation'] = $data['variation'];
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else:
            $this->printJson($this->stockVerify->save($data));
        endif;
    }
}
?>