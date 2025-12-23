<?php
class SalesEnquiryModel extends MasterModel{
    private $salesEnquiryMaster = "sales_enquiry";
    private $salesEnquiryTrans = "trans_child";
    private $salesQuotation = "sales_quotation";
    private $salesQuotationTrans = "sales_quote_transaction";
    private $itemMaster = "item_master";
    private $partyMaster = "party_master";
    private $transMain = "trans_main";
    private $transChild = "trans_child";
    
    
	public function getDTRows($data){
        $data['tableName'] = $this->transChild;
        $data['select'] = 'trans_child.id,trans_child.trans_main_id,trans_child.item_name,trans_child.item_code,trans_child.trans_status,trans_child.qty, trans_child.feasible, trans_main.trans_prefix,trans_main.trans_no,trans_main.trans_date,trans_main.party_name,trans_main.remark,trans_main.ref_by';
        $data['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
		$data['where']['trans_child.entry_type'] = 1;
		$data['group_by'][]='trans_child.trans_main_id';
		if($data['status'] == 2) { $data['where']['trans_child.feasible'] = "No"; } 
		if($data['status'] == 1) { $data['where']['trans_child.trans_status'] = 1; $data['where']['trans_child.feasible'] = "Yes"; } 
		if($data['status'] == 0) { $data['where']['trans_child.trans_status != '] = 1; $data['where']['trans_child.feasible'] = "Yes"; }
        $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";
         $data['searchCol'][] = "CONCAT('/',trans_main.trans_no)";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "trans_main.party_name";
        $data['searchCol'][] = "trans_child.item_name";
        $data['searchCol'][] = "trans_child.qty";
        $data['searchCol'][] = "trans_main.remark";

		$columns =array('','','trans_main.trans_no','trans_main.trans_date','trans_main.party_name','trans_child.item_name','trans_child.trans_status','trans_main.remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		
		return $this->pagingRows($data);
    }
    
    public function getSalesEnquiry($id){
		$data['tableName'] = $this->transMain;
		$data['select'] = "trans_main.id,trans_main.trans_prefix,trans_main.trans_no,trans_main.trans_date,trans_main.party_id,trans_main.ref_by, party_master.party_name,party_master.contact_person,party_master.party_mobile,party_master.contact_email,party_master.party_phone,party_master.party_email,party_master.party_address,party_master.party_pincode";		
		$data['join']['party_master'] = 'party_master.id = trans_main.party_id';
		$data['where']['trans_main.id'] = $id;
		$result = $this->row($data);		
		$result->itemData = $this->getTransChild($id);
		return $result;
	} 
	
    public function getSalesEnquiryById($id){
		$data['tableName'] = $this->transMain;
		$data['select'] = "id,trans_prefix,trans_no,trans_date,party_id,party_name";
		$data['where']['id'] = $id;
		$result = $this->row($data);
		return $result;
	} 
	
    public function itemSearch(){
		$data['tableName'] = 'item_master';
		$data['select'] = 'item_name';
		$data['where']['item_type'] = 1;
		$result = $this->rows($data);
		$searchResult = array();
		foreach($result as $row){$searchResult[] = $row->item_name;}
		return  $searchResult;
    }

	public function getTransChild($id){
		$data['tableName'] = $this->transChild;
		/* $data['select'] = "id,entry_type,from_entry_type,trans_main_id,item_id,item_name,qty,unit_id,unit_name,automotive,price,item_remark,trans_status"; */
        $data['where']['trans_main_id'] = $id;
		$result = $this->rows($data);
		return $result;
	}

	public function getEnquiryTransPenddingConfirm($id){
		$data['tableName'] = $this->transChild;
		$data['where']['trans_main_id'] = $id;
		$data['where']['trans_status'] = 0;
		$result = $this->numRows($data);
		return $result;
	}

	public function getEnquiryTransConfirm($id){		
		$data['tableName'] = $this->transChild;
		$data['where']['trans_main_id'] = $id;
		$data['where']['trans_status'] = 1;
		$result = $this->numRows($data);
		return $result;
	}

    public function save($masterData,$itemData){
        $transMainId = $masterData['id'];
		
		if($this->checkDuplicateEnquiry($masterData['party_id'],$masterData['trans_no'],$transMainId) > 0):
			$errorMessage['trans_no'] = "Enquiry No. is duplicate.";
			return ['status'=>0,'message'=>$errorMessage];
		endif;

		$custData['party_name'] = $masterData['party_name'];$custData['contact_person'] = $masterData['contact_person'];
		$custData['party_mobile'] = $masterData['contact_no'];$custData['contact_email'] = $masterData['contact_email'];
		$custData['party_phone'] = $masterData['party_phone'];$custData['party_email'] = $masterData['party_email'];
		$custData['party_address'] = $masterData['party_address'];$custData['party_pincode'] = $masterData['party_pincode'];
		if(empty($masterData['party_id'])):
			$masterData['party_id'] = $this->saveLead($custData);
		else:
			$custData['id'] = $masterData['party_id'];
			$custData['lead_status'] = 3;
			$customerSave = $this->store('party_master',$custData);
		endif;
		
		unset($masterData['contact_person'],$masterData['contact_no'],$masterData['contact_email'],$masterData['party_email'],$masterData['party_phone'],$masterData['party_address'],$masterData['party_pincode']);
		
		if(empty($transMainId)):			
			//save Sales Enquiry data
			$salesEnquirySave = $this->store($this->transMain,$masterData);
			$transMainId = $salesEnquirySave['insert_id'];	

			$result = ['status'=>1,'message'=>'Sales Enquiry saved successfully.','url'=>base_url("salesEnquiry")];			
		else:
			$this->store($this->transMain,$masterData);
			
			$data = array();
			$data['select'] = "id";
			$data['where']['trans_main_id'] = $transMainId;
			$data['tableName'] = $this->transChild;
			$ptransIdArray = $this->rows($data);

			foreach($ptransIdArray as $key=>$value):
				if(!in_array($value->id,$itemData['id'])):		
					$this->trash($this->transChild,['id'=>$value->id]);
				endif;
			endforeach;
			
			$result = ['status'=>1,'message'=>'Sales Enquiry updated successfully.','url'=>base_url("salesEnquiry")];
		endif;		

		//save sales enquiry Items
		foreach($itemData['item_name'] as $key=>$value):
			$itmQuery['tableName'] = $this->itemMaster;
			$itmQuery['where']['item_name'] = $value;
			$itmMaster = $this->row($itmQuery);
			$item_id = $itemData['item_id'][$key];
			if(!empty($itmMaster)){$item_id = $itmMaster->id;}
			$transData = [
							'id' => $itemData['id'][$key],
							'entry_type' => $masterData['entry_type'],
							'from_entry_type' => $itemData['from_entry_type'][$key],
							'trans_main_id' => $transMainId,
							'item_id' => $item_id,
							'item_name' => $value,
							'item_type' => $itemData['item_type'][$key],
							'item_code' => $itemData['item_code'][$key],
							'item_desc' => $itemData['item_desc'][$key],
							'hsn_code' => $itemData['hsn_code'][$key],
							'price' => $itemData['price'][$key],
							'gst_per' => $itemData['gst_per'][$key],
							'qty' => $itemData['qty'][$key],
							'unit_id' => $itemData['unit_id'][$key],
							'unit_name' => $itemData['unit_name'][$key],
							'automotive' => $itemData['automotive'][$key],
							'feasible' => $itemData['feasible'][$key],
                			'drg_rev_no' => $itemData['drg_rev_no'][$key],
							'item_remark' => $itemData['item_remark'][$key],
							'rev_no' => $itemData['rev_no'][$key],
							'batch_no' => $itemData['batch_no'][$key],
							'grn_data' => $itemData['grn_data'][$key],
							'created_by' => $itemData['created_by']
						];
			$this->store($this->transChild,$transData);					
		endforeach;

		return $result;
    }

    public function checkDuplicateEnquiry($party_id,$trans_no,$id = ""){
		$data['tableName'] = $this->transMain;
		$data['where']['party_id'] = $party_id;
		$data['where']['trans_no']  = $trans_no;
		$data['where']['entry_type']  = 1;
		if(!empty($id))
			$data['where']['id != '] = $id;		
		return $this->numRows($data);
    }
    
	public function saveLead($custData){
		if(!empty($custData['party_name'])):
			$custData['id'] = '';$custData['party_category'] = 1;$custData['party_type'] = 2;$custData['lead_status'] = 3;
			$customerSave = $this->store('party_master',$custData);
			return $customerSave['insert_id'];
		else:
			return 0;
		endif;
	}
	
    public function deleteEnquiry($id){
        //enquiry transation delete
		$where['trans_main_id'] = $id;
		$this->trash($this->transChild,$where);
        
        //enquiry master delete
		return $this->trash($this->transMain,['id'=>$id],'Sales Enquiry');
    }

	public function closeEnquiry($id){
		$this->store($this->transMain,['id'=>$id,'trans_status'=>1]);
		return ['status'=>1,'message'=>'Sales Enquiry Closed Successfully.'];
	}

	public function reopenEnquiry($id){
		$this->store($this->transMain,['id'=>$id,'trans_status'=>0]);
		return ['status'=>1,'message'=>'Sales Enquiry re-open Successfully.'];
	}
    
    /* public function getEnquiryData($enq_id){
		
		$result = $this->gettransChild($enq_id);

		if(!empty($result)):
			$i=1; $html="";
			foreach($result as $row):
				if(empty($row->confirm_status)):
					$checked = "";
					$disabled = "disabled";
					$html .= '<tr>
							<td class="text-center">
								<input type="checkbox" id="md_checkbox'.$i.'" class="filled-in chk-col-success itemCheck" data-rowid="'.$i.'" check="'.$checked.'" '.$checked.' />
								<label for="md_checkbox'.$i.'"></label>
							</td>
							<td>
								'.$row->item_name.'
								<input type="hidden" name="item_name[]" id="item_name'.$i.'" class="form-control" value="'.$row->item_name.'" '.$disabled.' />
								<input type="hidden" name="trans_id[]" id="trans_id'.$i.'" class="form-control" value="'.$row->id.'" '.$disabled.' />
							</td>
							<td>
								<input type="number" name="qty[]" id="qty'.$i.'" class="form-control floatOnly countItem" data-id="'.$i.'" value="'.$row->qty.'" min="0" '.$disabled.' />
								<div class="error qty'.$row->id.'"></div>
							</td>
							<td>
								<input type="number" name="price[]" id="price'.$i.'" class="form-control floatOnly countItem" data-id="'.$i.'" value="0" min="0" '.$disabled.' />
								<div class="error price'.$row->id.'"></div>
							</td>
							<td>
								<input type="text" name="currency[]" id="currency'.$i.'" class="form-control floatOnly" data-id="'.$i.'" value="INR" '.$disabled.' readonly />
							</td>
						</tr>';
				
				else:
					$itemData = $this->item->getItem($row->item_id);
					
					$checked = "checked";
					$disabled = "disabled";

					$html .= '<tr>
							<td class="text-center">
								<input type="checkbox" id="md_checkbox'.$i.'" class="filled-in chk-col-success itemCheck" data-rowid="'.$i.'" check="'.$checked.'" '.$checked.' '.$disabled.' />
								<label for="md_checkbox'.$i.'"></label>
							</td>
							<td>
								'.$itemData->item_name.'
								<input type="hidden" name="item_name[]" id="item_name'.$i.'" class="form-control" value="'.$itemData->item_name.'" '.$disabled.' />
								<input type="hidden" name="trans_id[]" id="trans_id'.$i.'" class="form-control" value="'.$row->id.'" '.$disabled.' />
							</td>
							<td>
								<input type="number" name="qty[]" id="qty'.$i.'" class="form-control floatOnly" value="'.$row->qty.'" min="0" '.$disabled.' />
								<div class="error qty'.$row->id.'"></div>
							</td>
							<td>
								<input type="number" name="price[]" id="price'.$i.'" class="form-control floatOnly" value="" min="0" '.$disabled.' />
								<div class="error price'.$row->id.'"></div>
							</td>
							<td>
								<input type="text" name="currency[]" id="currency'.$i.'" class="form-control floatOnly" data-id="'.$i.'" value="INR" '.$disabled.' readonly />
							</td>
						</tr>';

				endif;$i++;
			endforeach;
		else:
			$html = '<tr><td colspan="6" class="text-center">No data available in table</td></tr>';
		endif;
		return $html;
	} */

	/* public function saveQuotation($data){
		$queryData = array();
		$queryData['tableName'] = $this->transMain;
		$queryData['where']['id'] = $data['enq_id'];
		$enquiryData = $this->row($queryData);

		$queryData = array();
		$queryData['select'] = "trans_child.*,unit_master.unit_name,unit_master.description";
        $queryData['leftJoin']['unit_master'] = "unit_master.id = trans_child.unit_id";
        $queryData['where']['trans_child.trans_main_id'] = $data['enq_id'];
        $queryData['where_in']['trans_child.id'] = $data['trans_id'];
		$queryData['tableName'] = $this->transChild;
		$enquiryItemData = $this->rows($queryData);
		
		$terms = Array();
		$terms[] = ["title"=>"Price Factor","condition"=>"FOB"];
		$terms[] = ["title"=>"Port of Loading","condition"=>"ICD - Ahmedabad - India"];
		$terms[] = ["title"=>"Consignment Value","condition"=>"Single Shipment"];
		$terms[] = ["title"=>"Insurance","condition"=>"N/A"];
		$terms[] = ["title"=>"Payment","condition"=>" 30% advance with purchase order and balance against scan copy of B/L"];
		$terms[] = ["title"=>"Development Charges","condition"=>"300 GBP"];
		$terms[] = ["title"=>"Raw Material","condition"=>"AL 6082 T6"];
		$terms[] = ["title"=>"Plating","condition"=>"Self Colour"];
		$terms[] = ["title"=>"Packing","condition"=>"Export Worthy Packing"];
		$terms[] = ["title"=>"Delivery","condition"=>"Within 06 to 08 Weeks from the Date of your sample Approval"];
		$terms[] = ["title"=>"Validity","condition"=>" 30 Days from the Date of Quotation"];
		$terms[] = ["title"=>"Remarks","condition"=>" We may provide +/- 5% Quantity of purchase order"];
		$terms[] = ["title"=>"Special Note","condition"=>"General terms and conditions applied"];
		
		$inquiryUpdate = [
			'id'=>$data['enq_id'],
			'contact_person'=>$data['contact_person'],
			'contact_no'=>$data['contact_no'],
			'contact_email'=>$data['contact_email'],
			'party_phone'=>$data['party_phone'],
			'party_email'=>$data['party_email'],
			'party_address'=>$data['party_address'],
			'party_pincode'=>$data['party_pincode']
		];
		$custUpdate = [
			'id'=>$enquiryData->party_id,
			'contact_person'=>$data['contact_person'],
			'party_mobile'=>$data['contact_no'],
			'contact_email'=>$data['contact_email'],
			'party_phone'=>$data['party_phone'],
			'party_email'=>$data['party_email'],
			'party_address'=>$data['party_address'],
			'party_pincode'=>$data['party_pincode'],
			'lead_status'=>4
		];
		//save sales Quotation master data
		$updateInquiry = $this->store($this->transMain,$inquiryUpdate);
		$updateCustomer = $this->store('party_master',$custUpdate);
		
		$masterData = [
			'id' => $data['id'],
			'enq_id' => $data['enq_id'],
			'quote_no' => $this->transModel->nextTransNo(2),
			'quote_prefix' => $this->transModel->getTransPrefix(2),
			'quotation_date' => formatDate($data['quotation_date'],'Y-m-d'),
			'party_id' => $enquiryData->party_id,
			'customer_name'=>$enquiryData->customer_name,
			'contact_person'=>$data['contact_person'],
			'contact_no'=>$data['contact_no'],
			'contact_email'=>$data['contact_email'],
			'party_phone'=>$data['party_phone'],
			'party_email'=>$data['party_email'],
			'party_address'=>$data['party_address'],
			'party_pincode'=>$data['party_pincode'],
			'terms'=>json_encode($terms)
		];
		
		//save sales Quotation master data
		$savedQuote = $this->store($this->salesQuotation,$masterData);
		$quote_id = $savedQuote['insert_id'];
		
		//save sales Quotation items
		foreach($enquiryItemData as $key=>$row):
			$transData = [
							'id' => '',
							'quote_id' => $quote_id,
							'inq_trans_id' => $row->id,
							'item_id' => $row->item_id,
							'unit_id' => $row->unit_id,
							'item_name' => $row->item_name,
							'qty' => $data['qty'][$key],
							'price' => $data['price'][$key],
							'item_remark' => ''
						];
			$this->store($this->salesQuotationTrans,$transData);			
		endforeach;

		return ['status'=>1,'message'=>'Sales Quotation Generated Successfully.'];
	} */

	

    /* public function getQuotationItems($quote_id){
		
		$qdata['tableName'] = $this->salesQuotationTrans;
		$qdata['select'] = "sales_quote_transaction.*,unit_master.unit_name,unit_master.description,trans_child.automotive";
        $qdata['leftJoin']['unit_master'] = "unit_master.id = sales_quote_transaction.unit_id";
        $qdata['join']['trans_child'] = "trans_child.id = sales_quote_transaction.inq_trans_id";
        $qdata['where']['sales_quote_transaction.id'] = $quote_id;
		$quoteItems = $this->rows($qdata);

		if(!empty($quoteItems)):
			$i=1; $html="";
			foreach($quoteItems as $row):
				if(empty($row->confirm_status)):
					$checked = "";
					$disabled = "disabled";
					$html .= '<tr>
							<td class="text-center">
								<input type="checkbox" id="md_checkbox'.$i.'" class="filled-in chk-col-success itemCheckCQ" data-rowid="'.$i.'" check="'.$checked.'" '.$checked.' />
								<label for="md_checkbox'.$i.'" style="margin-bottom:0px;"></label>
							</td>
							<td>
								'.$row->item_name.'
								<input type="hidden" name="item_id[]" id="item_id'.$i.'" class="form-control" value="'.$row->item_id.'" '.$disabled.' />
								<input type="hidden" name="item_name[]" id="item_name'.$i.'" class="form-control" value="'.$row->item_name.'" '.$disabled.' />
								<input type="hidden" name="trans_id[]" id="trans_id'.$i.'" class="form-control" value="'.$row->id.'" '.$disabled.' />
								<input type="hidden" name="inq_trans_id[]" id="inq_trans_id'.$i.'" class="form-control" value="'.$row->inq_trans_id.'" '.$disabled.' />
								<input type="hidden" name="unit_id[]" id="unit_id'.$i.'" class="form-control" value="'.$row->unit_id.'" '.$disabled.' />
								<input type="hidden" name="automotive[]" id="automotive'.$i.'" class="form-control" value="'.$row->automotive.'" '.$disabled.' />
							</td>
							<td>
								'.floatVal($row->qty).' <small>('.$row->unit_name.')</small>
								<input type="hidden" name="qty[]" id="qty'.$i.'" class="form-control floatOnly" data-id="'.$i.'" value="'.$row->qty.'" min="0" '.$disabled.' />
								<div class="error qty'.$row->id.'"></div>
							</td>
							<td>
								'.$row->price.'
								<input type="hidden" name="price[]" id="price'.$i.'" class="form-control floatOnly" data-id="'.$i.'" value="'.$row->price.'" min="0" '.$disabled.' />
								<div class="error price'.$row->id.'"></div>
							</td>
							<td>
								<input type="number" name="confirm_price[]" id="confirm_price'.$i.'" class="form-control floatOnly" data-id="'.$i.'" value="0" min="0" '.$disabled.' />
								<div class="error confirm_price'.$row->id.'"></div>
							</td>
						</tr>';
				
				else:
					$itemData = $this->item->getItem($row->item_id);
					
					$checked = "checked";
					$disabled = "disabled";

					$html .= '<tr>
							<td class="text-center">
								<input type="checkbox" id="md_checkbox'.$i.'" class="filled-in chk-col-success itemCheck" data-rowid="'.$i.'" check="'.$checked.'" '.$checked.' '.$disabled.' />
								<label for="md_checkbox'.$i.'" style="margin-bottom:0px;"></label>
							</td>
							<td>
								'.$row->item_name.'
								<input type="hidden" name="item_id[]" id="item_id'.$i.'" class="form-control" value="'.$row->item_id.'" '.$disabled.' />
								<input type="hidden" name="item_name[]" id="item_name'.$i.'" class="form-control" value="'.$row->item_name.'" '.$disabled.' />
								<input type="hidden" name="trans_id[]" id="trans_id'.$i.'" class="form-control" value="'.$row->id.'" '.$disabled.' />
								<input type="hidden" name="inq_trans_id[]" id="inq_trans_id'.$i.'" class="form-control" value="'.$row->inq_trans_id.'" '.$disabled.' />
								<input type="hidden" name="unit_id[]" id="unit_id'.$i.'" class="form-control" value="'.$row->unit_id.'" '.$disabled.' />
								<input type="hidden" name="automotive[]" id="automotive'.$i.'" class="form-control" value="'.$row->automotive.'" '.$disabled.' />
							</td>
							<td>
								'.$row->qty.'
								<input type="hidden" name="qty[]" id="qty'.$i.'" class="form-control floatOnly" data-id="'.$i.'" value="'.$row->qty.'" min="0" '.$disabled.' />
								<div class="error qty'.$row->id.'"></div>
							</td>
							<td>
								'.$row->price.'
								<input type="hidden" name="price[]" id="price'.$i.'" class="form-control floatOnly" data-id="'.$i.'" value="'.$row->price.'" min="0" '.$disabled.' />
								<div class="error price'.$row->id.'"></div>
							</td>
							<td>
								<input type="number" name="confirm_price[]" id="confirm_price'.$i.'" class="form-control floatOnly" data-id="'.$i.'" value="'.$row->confirm_price.'" min="0" '.$disabled.' />
								<div class="error confirm_price'.$row->id.'"></div>
							</td>
						</tr>';

				endif;$i++;
			endforeach;
		else:
			$html = '<tr><td colspan="6" class="text-center">No data available in table</td></tr>';
		endif;
		return $html;
	} */

	/* public function saveConfirmQuotation($data){
		$queryData = array();
		$queryData['tableName'] = $this->salesQuotation;
		$queryData['where']['id'] = $data['id'];
		$quotationData = $this->row($queryData);

		$queryData = array();
        $queryData['where']['quote_id'] = $data['id'];
		$queryData['tableName'] = $this->salesQuotationTrans;
		$quotationItems = $this->rows($queryData);
		
		$quote_id = $data['id'];
		
		//save sales Quotation items
		foreach($data['trans_id'] as $key=>$value):
			
			// Store New Confirmed Item to Item Master
			if(empty($data['item_id'][$key])):
				$itmData['id']="";
				$itmData['item_name']=$data['item_name'][$key];
				$itmData['price']=$data['confirm_price'][$key];
				$itmData['unit_id']=$data['unit_id'][$key];
				$itmData['automotive']=$data['automotive'][$key];
				$itmData['party_id']=$data['party_id'];
				$itmData['item_type'] = 1;
				$newItem = $this->store($this->itemMaster,$itmData);
			endif;
			
			// Update Quotation Transaction with Confirmed Parameters
			$transData = [
							'id' =>  $value,
							'item_id' =>  $newItem['insert_id'],
							'confirm_price' => $data['confirm_price'][$key],
							'confirm_status' => 1,
							'confirm_date' => formatDate($data['confirm_date'],'Y-m-d'),
							'confirm_by' => $data['confirm_by']
						];
			$this->store($this->salesQuotationTrans,$transData);
			
			// Update Inquiry Transaction with Confirmed Parameters
			$this->store($this->transChild,['id'=>$data['inq_trans_id'][$key],'confirm_status' => 1,'item_id' =>  $newItem['insert_id']]);
		endforeach;

		$customerSave = $this->store('party_master',['id'=>$data['party_id'],'party_type'=>1,'lead_status'=>6]);

		return ['status'=>1,'message'=>'Sales Quotation Confirmed Successfully.'];
	} */


}
?>