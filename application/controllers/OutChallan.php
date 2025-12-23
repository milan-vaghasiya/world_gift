<?php
class OutChallan extends MY_Controller{
    private $indexPage = "out_challan/index";
    private $formPage = "out_challan/form";
   
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Out Challan";
		$this->data['headData']->controller = "outChallan";
		$this->data['headData']->pageUrl = "outChallan";
	}
	
	public function index(){
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->outChallan->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;  
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getOutChallanData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addChallan(){
        $this->data['challan_prefix'] = 'OCH/'.$this->shortYear.'/';
        $this->data['challan_no'] = $this->outChallan->nextTransNo(2);
        $this->data['partyData'] = $this->party->getVendorList();
        $this->data['itemData']  = $this->item->getItemLists([6,7]);
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->load->view($this->formPage,$this->data);
    }

    public function getDepartmentOrJobcard(){
        $party_id = $this->input->post('party_id');
        $ref_id = $this->input->post('ref_id');
        
        $options = '';
        if($party_id == 0):
            $options = '<option value="">Select Department</option>';
            $departments = $this->department->getDepartmentList();
            foreach($departments as $row):
                $selected = (!empty($ref_id) && $ref_id == $row->id)?"selected":"";
                $options .= '<option value="'.$row->id.'" '.$selected.'>'.
                $row->name.'</option>';
            endforeach;
        else:
            $options = '<option value="">Select Job Card No.</option>';
            $jobcards = $this->jobcard->getJobcardList();
            foreach($jobcards as $row):
                $selected = (!empty($ref_id) && $ref_id == $row->id)?"selected":"";
                $options .= '<option value="'.$row->id.'" '.$selected.'>'.
                getPrefixNumber($row->job_prefix,$row->job_no).'</option>';
            endforeach;
        endif;
        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function getBatchNo(){
        $item_id = $this->input->post('item_id');
        $location_id = $this->input->post('location_id');
        $batchData = $this->item->locationWiseBatchStock($item_id,$location_id);
        $options = '<option value="">Select Batch No.</option>';
        foreach($batchData as $row):
			//if($row->qty > 0):
				$options .= '<option value="'.$row->batch_no.'" data-stock="'.$row->qty.'">'.$row->batch_no.'</option>';
			//endif;
        endforeach;
        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if($data['party_id'] == "")
            $errorMessage['party_id'] = "Customer Name is required.";
        if(empty($data['item_id'][0]))
            $errorMessage['item_name_error'] = "Items is required.";

        if(!empty($data['item_id'])):
            $i=1;
            foreach($data['item_id'] as $key=>$value):
                if(empty($data['location_id'][$key]) || empty($data['batch_no'][$key])):
                    if(empty($data['location_id'][$key])):
                        $errorMessage['location_id'.$i] = "Location is required.";
                    endif;
                    if(empty($data['batch_no'][$key])):
                        $errorMessage['batch_no'.$i] = "Batch No. is required.";
                    endif;
                else:
                    $currentStock = $this->item->getBatchNoCurrentStock($value,$data['location_id'][$key],$data['batch_no'][$key])->stock_qty;
                    if(empty($data['challan_id'])):
                        if($currentStock < $data['qty'][$key]):
                            $errorMessage["qty".$i] = "Stock not available.";
                        endif;
                    else:
                        if(!empty($data['trans_id'][$key])):
                            $transData = $this->outChallan->challanTransRow($data['trans_id'][$key]);
                            if($transData->qty < $data['qty'][$key]):
                                $qty = $data['qty'][$key] - $transData->qty;
                                if($currentStock < $qty):
                                    $errorMessage["qty".$i] = "Stock not available.";
                                endif;
                            endif;
                        else:
                            if($currentStock < $data['qty'][$key]):
                                $errorMessage["qty".$i] = "Stock not available.";
                            endif;
                        endif;
                    endif;
                endif;
                $i++;
            endforeach;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else:
            $masterData = [
                'id' => $data['challan_id'],
                'challan_prefix' => $data['challan_prefix'],  
                'challan_no' => $data['challan_no'],
                'challan_type' => 2,
                'ref_id' => $data['ref_id'],
                'challan_date' => $data['challan_date'],
                'party_id' => $data['party_id'],
                'party_name' => $data['party_name'],
                'remark' => $data['remark'],
                'created_by' => $this->session->userdata('loginId')
            ];

            $itemData = [
                'id' => $data['trans_id'],
                'item_id' => $data['item_id'],
                'item_name' => $data['item_name'],
                'qty' => $data['qty'],
                'unit_id' => $data['unit_id'],
                'unit_name' => $data['unit_name'],
                'is_returnable' => $data['is_returnable'],
                'location_id' => $data['location_id'],
                'batch_no' => $data['batch_no'],
                'item_remark' => $data['item_remark'],
                'created_by' => $this->session->userdata('loginId')
            ];

            $this->printJson($this->outChallan->save($masterData,$itemData));
        endif;
    }

    public function edit($id){
        $this->data['partyData'] = $this->party->getVendorList();
        $this->data['itemData']  = $this->item->getItemLists([6,7]);
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->data['dataRow'] = $this->outChallan->getOutChallan($id);       
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.','field_error'=>0,'field_error_message'=>NULL]);
		else:
			$this->printJson($this->outChallan->deleteChallan($id));
		endif;
	}

    public function getReceiveItemTrans(){
        $this->printJson($this->outChallan->getReceiveItemTrans($this->input->post()));
    }

    public function saveReceiveItem(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['qty']))
            $errorMessage['qty'] = "Qty. is required.";

        if(!empty($data['qty'])):
            $inItemData = $this->outChallan->getOutChallanTransRow($data['ref_id']);
            $pendingQty = $inItemData->qty - $inItemData->return_qty;
            if($data['qty'] > $pendingQty):
                $errorMessage['qty'] = "Invalid Qty.";
            endif;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->loginId;
            $this->printJson($this->outChallan->saveReceiveItem($data));
        endif;
    }

    public function deleteReceiveItem(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.','field_error'=>0,'field_error_message'=>NULL]);
		else:
			$this->printJson($this->outChallan->deleteReceiveItem($id));
		endif;
	}
}
?>