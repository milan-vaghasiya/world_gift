<?php
class Instrument extends MY_Controller
{
    private $indexPage = "instrument/index";
    private $formPage = "instrument/form";
    private $requestForm = "purchase_request/purchase_request";
    private $gstPercentage = Array(["rate"=>0,"val"=>'NIL'],["rate"=>0.25,"val"=>'0.25%'],["rate"=>3,"val"=>'3%'],["rate"=>5,"val"=>'5%'],["rate"=>12,"val"=>'12%'],["rate"=>18,"val"=>'18%'],["rate"=>28,"val"=>'28%']);
   
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Instrument";
		$this->data['headData']->controller = "instrument";
		$this->data['headData']->pageUrl = "instrument";
	}
	
	public function index(){
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
		$data=$this->input->post();
		$data['select'] = "id,item_name,item_type,item_code,make_brand,instrument_range,least_count,permissible_error,cal_required, cal_freq,cal_agency,description";
        $data['where']['item_master.item_type'] = 6;
		$data['searchCol'][] = "item_name";
        $data['searchCol'][] = "item_code";
        $data['searchCol'][] = "make_brand";
        $data['searchCol'][] = "instrument_range";
        $data['searchCol'][] = "least_count";
        $data['searchCol'][] = "permissible_error";
        $data['searchCol'][] = "cal_required";
        $data['searchCol'][] = "cal_freq";
        $data['searchCol'][] = "cal_agency";
		
		$columns =array('','','item_name','item_code','make_brand','instrument_range','least_count','permissible_error','cal_required','cal_freq','cal_agency','description');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		
		$result = $this->instrument->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getInstrumentData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addInstrument(){
        $this->data['categoryList'] = $this->item->getCategoryList(6);
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['threadType'] = explode(',', $this->item->getMasterOptions()->thread_types);
        $this->load->view($this->formPage,$this->data);
    }

    public function save()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['item_name']))
            $errorMessage['item_name'] = "Insrtument Name is required.";

        if ($_FILES['item_image']['name'] != null || !empty($_FILES['item_image']['name'])) :
            $this->load->library('upload');
            $_FILES['userfile']['name']     = $_FILES['item_image']['name'];
            $_FILES['userfile']['type']     = $_FILES['item_image']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['item_image']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['item_image']['error'];
            $_FILES['userfile']['size']     = $_FILES['item_image']['size'];

            $imagePath = realpath(APPPATH . '../assets/uploads/instrument/');
            $config = ['file_name' => time() . "_order_item_" . $_FILES['userfile']['name'], 'allowed_types' => '*', 'max_size' => 10240, 'overwrite' => FALSE, 'upload_path'    => $imagePath];

            $this->upload->initialize($config);
            if (!$this->upload->do_upload()) :
                $errorMessage['item_image'] = $this->upload->display_errors();
                $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
            else :
                $uploadData = $this->upload->data();
                $data['item_image'] = $uploadData['file_name'];
            endif;
        else :
            unset($data['item_image']);
        endif;

        if (!empty($errorMessage)) :
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else :
            $data['item_type'] = 6;
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->instrument->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['categoryList'] = $this->item->getCategoryList(6);
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['threadType'] = explode(',', $this->item->getMasterOptions()->thread_types);
        $this->data['dataRow'] = $this->instrument->getItem($id);
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.','field_error'=>0,'field_error_message'=>NULL]);
        else:
            $this->printJson($this->instrument->delete($id));
        endif;
    }
    
    /* Purchase Request */
    public function addPurchaseRequest(){
        $this->data['itemData'] = $this->item->getItemLists(6,7); 
        $this->load->view($this->requestForm,$this->data);
    }

    public function savePurchaseRequest(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['req_item_id'][0]))
            $errorMessage['req_item_id'] = "Item Name is required.";
        if(empty($data['req_date']))
            $errorMessage['req_date'] = "Request Date is required.";
        if(empty($data['req_qty'][0]))
            $errorMessage['req_qty'] = "Request Qty. is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else:
            $data['item_data'] = "";$itemArray = array();
			if(isset($data['req_item_id']) && !empty($data['req_item_id'])):
				foreach($data['req_item_id'] as $key=>$value):
					$itemArray[] = [
						'req_item_id' => $value,
						'req_qty' => $data['req_qty'][$key],
						'req_item_name' => $data['req_item_name'][$key]
					];
				endforeach;
				$data['item_data'] = json_encode($itemArray);
			endif;
            unset($data['req_item_id'], $data['req_item_name'], $data['req_qty']);
            $this->printJson($this->jobMaterial->savePurchaseRequest($data));
        endif;
    }
}
?>