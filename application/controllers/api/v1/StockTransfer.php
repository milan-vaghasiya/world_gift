<?php
class StockTransfer extends MY_Apicontroller{
	public function __construct(){
        parent::__construct();
    }

	public function stockInItemList($off_set=0){
		$limit = (isset($_REQUEST['per_page']) && !empty($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $search = (isset($_REQUEST['search']) && !empty($_REQUEST['search']))?$_REQUEST['search']:"";
        $item_type = (isset($_REQUEST['item_type']) && !empty($_REQUEST['item_type']))?$_REQUEST['item_type']:1;
        $postData = ['off_set'=>$off_set,'limit'=>$limit,'search'=>$search,'item_type'=>$item_type,'price_required'=>1,'stock_required'=>1,'cm_id'=>2];
        $this->data['itemList'] = $this->item->getItemForStockInOut($postData);
        $this->printJson(['status'=>1,'message'=>'Recored found.','field_error'=>0,'field_error_message'=>null,'data'=>$this->data]);
	}

	/* 
	Stock Transfer from RJ to World Gift and gerenate auto invoice
	Generate Sales invoice in RJ
	Generate Purchase invoice in World Gift
	*/
	public function saveInvoice(){
		$data = $this->input->post();

		if(empty($data['cm_id']))
			$this->printJson(['status'=>0,'message'=>'Please scan QR Code.','field_error'=>0,'field_error_message'=>null,'data'=>null]); 
		if(empty($data['item_id']))
			$this->printJson(['status'=>0,'message'=>'Please select items.','field_error'=>0,'field_error_message'=>null,'data'=>null]);
		if(!empty($data['cm_id'])):
			if($data['cm_id'] == $this->CMID):
				$this->printJson(['status'=>0,'message'=>'Invalid','field_error'=>0,'field_error_message'=>null,'data'=>null]); 
			endif;
		endif;

		$rjGiftData = $this->db->select('party_master.*,currency.inrrate')->join('currency','currency.currency = party_master.currency','left')->where('party_master.is_delete',0)->where('party_cm_id',$data['cm_id'])->get('party_master')->row();

		$worldGiftData = $this->db->select('party_master.*,currency.inrrate')->join('currency','currency.currency = party_master.currency','left')->where('party_master.is_delete',0)->where('party_cm_id',$rjGiftData->cm_id)->get('party_master')->row();

		$locationId = $this->db->where('is_delete',0)->where('cm_id',$worldGiftData->cm_id)->where('store_type',1)->get('location_master')->row()->id;
		
		$itemData = array();$total_amount = 0;$total_gst_amount=0;$total_net_amount = 0;
		foreach($data['item_id'] as $key=>$item_id):
			$this->db->select("item_master.*,unit_master.unit_name,SUM(stock_transaction.qty) as stock_qty");
			$this->db->join('unit_master',"item_master.unit_id = unit_master.id","left");
			$this->db->join('stock_transaction','stock_transaction.item_id = item_master.id');
			$this->db->where('item_master.id',$item_id);
			$this->db->where('stock_transaction.item_id',$item_id);
			$this->db->where('stock_transaction.cm_id',$worldGiftData->cm_id);
			$this->db->where('stock_transaction.is_delete',0);
			$productData = $this->db->get('item_master')->row();

			if($data['qty'][$key] > $productData->stock_qty):
				$this->printJson(['status'=>0,'message'=>'Stock not avlible. Item Name : '.$productData->item_name,'field_error'=>0,'field_error_message'=>null,'data'=>null]); 
			endif;

			$gst_per = $productData->gst_per;
			$amount = round(($data['qty'][$key] * $productData->price2),2);
			$gst_amount = round((($amount * $gst_per) / 100),2);
			$net_amount = round(($amount + $gst_amount),2);

			$total_amount += $amount;
			$total_gst_amount += $gst_amount;
			$total_net_amount += $net_amount;			

			$itemData['id'][] = "";
			$itemData['from_entry_type'][] = "";
			$itemData['ref_id'][] = "";
			$itemData['item_id'][] = $item_id;
			$itemData['item_name'][] = $productData->item_name;
			$itemData['item_type'][] = $productData->item_type;
			$itemData['item_code'][] = $productData->item_code;
			$itemData['item_desc'][] = $productData->description;
			$itemData['unit_id'][] = $productData->unit_id;
			$itemData['unit_name'][] = $productData->unit_name;
			$itemData['location_id'][] = $locationId;
			$itemData['batch_no'][] = "General Batch";
			$itemData['hsn_code'][] = $productData->hsn_code;
			$itemData['qty'][] = $data['qty'][$key];
			$itemData['stock_eff'][] = 1;
			$itemData['price'][] = $productData->price2;
			$itemData['org_price'][] = $productData->price2;
			$itemData['amount'][] = $amount;
			$itemData['taxable_amount'][] = $amount;
			$itemData['gst_per'][] = $gst_per;
			$itemData['gst_amount'][] = $gst_amount;
			$itemData['igst_per'][] = $gst_per;
			$itemData['igst_amount'][] = $gst_amount;
			$itemData['sgst_per'][] = round(($gst_per/2),2);
			$itemData['sgst_amount'][] = round(($gst_amount/2),2);
			$itemData['cgst_per'][] = round(($gst_per/2),2);
			$itemData['cgst_amount'][] = round(($gst_amount/2),2);
			$itemData['disc_per'][] = 0;
			$itemData['disc_amount'][] = 0;
			$itemData['item_remark'][] = "";
			$itemData['net_amount'][] = $net_amount;
		endforeach;
		
		$trans_no=0;
		if($worldGiftData->cm_id == 1):
		     $trans_no = $this->db->select('ifnull((MAX(trans_no) + 1),1) as trans_no')->where('entry_type',6)->where('is_delete',0)->where('trans_date >=',$this->startYearDate)->where('trans_date <=',$this->endYearDate)->where('cm_id',$worldGiftData->cm_id)->where('trans_prefix','WG/')->get('trans_main')->row()->trans_no;
        elseif($worldGiftData->cm_id == 2):
            $trans_no = $this->db->select('ifnull((MAX(trans_no) + 1),1) as trans_no')->where('entry_type',6)->where('is_delete',0)->where('trans_date >=',$this->startYearDate)->where('trans_date <=',$this->endYearDate)->where('cm_id',$worldGiftData->cm_id)->where('trans_prefix','RJ/')->get('trans_main')->row()->trans_no;
        endif;
		
		//$trans_no = $this->db->select('ifnull((MAX(trans_no) + 1),1) as trans_no')->where('entry_type',6)->where('is_delete',0)->where('cm_id',$worldGiftData->cm_id)->get('trans_main')->row()->trans_no;
		
		$trans_prefix = "";
		if($worldGiftData->cm_id == 1):
			$trans_prefix = "WG/";
		else:
			$trans_prefix = "RJ/";
		endif;

		$spAccounts = $this->db->where('party_category',4)->where('cm_id',$worldGiftData->cm_id)->where('is_delete',0)->where_in('group_code',['SA'])->get('party_master')->result();
		
		$ledgerList = $this->db->where('party_category',4)->where('cm_id',$worldGiftData->cm_id)->where('is_delete',0)->where_in('group_code',["DT", "ED", "EI", "ID", "II"])->get('party_master')->result();

		$sp_acc_key = array_search("SALESGSTACC",array_column($spAccounts,'system_code'));
		$sp_acc_id = $spAccounts[$sp_acc_key]->id;

		$igst_acc_key = array_search("IGSTOPACC",array_column($ledgerList,'system_code'));
		$igst_acc_id = $ledgerList[$igst_acc_key]->id;

		$cgst_acc_key = array_search("CGSTOPACC",array_column($ledgerList,'system_code'));
		$cgst_acc_id = $ledgerList[$cgst_acc_key]->id;

		$sgst_acc_key = array_search("SGSTOPACC",array_column($ledgerList,'system_code'));
		$sgst_acc_id = $ledgerList[$sgst_acc_key]->id;

		$roff_acc =  $this->db->where('party_category',4)->where('cm_id',$worldGiftData->cm_id)->where('is_delete',0)->where_in('system_code','ROFFACC')->get('party_master')->row();
		$roff_acc_id = (!empty($roff_acc))?$roff_acc->id:0;

		/*$totalNetAmount = array_sum($itemData['net_amount']);
		$totalNetAmount = round($totalNetAmount,2);
		$netAmount = 0;$roundOffAmt = 0;
		$decimal = explode(".",$totalNetAmount)[1];
		$decimal = round($decimal);
		if($decimal != 0):
			if($decimal >= 50):
				$roundOffAmt = ((100 - $decimal) / 100);
				$netAmount = $totalNetAmount + $roundOffAmt;
			elseif($decimal < 50):
				$roundOffAmt = (($decimal - ($decimal * 2)) / 100);
				$netAmount = $totalNetAmount + $roundOffAmt;
			endif;
		endif;*/
		
		$netAmount = 0;$roundOffAmt = 0;
		$totalNetAmount = array_sum($itemData['net_amount']);		
		$decimal = round($totalNetAmount - intVal($totalNetAmount),2);
		
		if($decimal > 0 AND $decimal < 0.50){$roundOffAmt = round(($decimal * -1),2);}
		if($decimal >= 0.50){$roundOffAmt = round(((1 - $decimal)/100),2);}
		
		$netAmount = $totalNetAmount + $roundOffAmt;

		if(floatval($worldGiftData->inrrate) <= 0) :
			$data['currency'] = "INR";
			$data['inrrate'] = 1;
		else :
			$data['currency'] = $worldGiftData->currency;
			$data['inrrate'] = $worldGiftData->inrrate;
		endif;
		$masterData = [
			'id' => "",
			'entry_type' => 6,
			'from_entry_type' => 0,
			'ref_id' => "",
			'trans_status' => 99,
			'trans_no' => $trans_no,
			'trans_prefix' => $trans_prefix,
			'trans_number' => getPrefixNumber($trans_prefix, $trans_no),
			'trans_date' => date('Y-m-d'),
			'memo_type' => "DEBIT",
			'party_id' => $worldGiftData->id,
			'opp_acc_id' => $worldGiftData->id,
			'sp_acc_id' => $sp_acc_id,
			'party_name' => $worldGiftData->party_name,
			'party_state_code' => 24,
			'gstin' => $worldGiftData->gstin,
			'gst_applicable' => 1,
			'gst_type' => 1,
			'doc_no' => "",
			'doc_date' => date('Y-m-d'),
			'challan_no' => "",
			'total_amount' => array_sum($itemData['amount']),
			'taxable_amount' => array_sum($itemData['taxable_amount']),
			'gst_amount' => array_sum($itemData['igst_amount']),
			'igst_acc_id' => $igst_acc_id,
			'igst_per' => 0,
			'igst_amount' => array_sum($itemData['igst_amount']),
			'sgst_acc_id' => $sgst_acc_id,
			'sgst_per' => 0,
			'sgst_amount' => array_sum($itemData['sgst_amount']),
			'cgst_acc_id' => $cgst_acc_id,
			'cgst_per' => 0,
			'cgst_amount' => array_sum($itemData['cgst_amount']),
			'cess_acc_id' => 0,
			'cess_per' => 0,
			'cess_amount' => 0,
			'cess_qty_acc_id' => 0,
			'cess_qty' => 0,
			'cess_qty_amount' => 0,
			'tcs_acc_id' => 0,
			'tcs_per' => 0,
			'tcs_amount' => 0,
			'tds_acc_id' => 0,
			'tds_per' => 0,
			'tds_amount' => 0,
			'disc_amount' => array_sum($itemData['disc_amount']),
			'apply_round' => 0,
			'round_off_acc_id'  => $roff_acc_id,
			'round_off_amount' => $roundOffAmt,
			'net_amount' => $netAmount,
			'terms_conditions' => json_encode(array()),
			'remark' => "",
			'currency' => $data['currency'],
			'inrrate' => $data['inrrate'],
			'vou_name_s' => getVoucherNameShort(6),
			'vou_name_l' => getVoucherNameLong(6),
			'ledger_eff' => 1,
			'created_by' => $this->loginId,
			'is_app' => 1,
			'cm_id' => $worldGiftData->cm_id
		];	

		$accType = getSystemCode(6, false);
		if (!empty($accType)) :
			$spAcc = $this->db->where('cm_id',$worldGiftData->cm_id)->where('is_delete',0)->where('system_code',$accType)->get('party_master')->row();
			$masterData['vou_acc_id'] = (!empty($spAcc)) ? $spAcc->id : 0;
		else :
			$masterData['vou_acc_id'] = 0;
		endif;
		
		$expenseData = array();
		$result = $this->salesInvoice->save($masterData,$itemData,$expenseData,array());
		if(isset($result['insert_id'])):
			$this->sendPurchaseInvoice($result['insert_id'],$data['cm_id']);
		endif;
		unset($result['url']);
		$result['message'] = "Stock transfer successfully.";
		$this->printJson($result);
	}

	public function sendPurchaseInvoice($id,$cm_id){
		$invoiceData = $this->db->where('id',$id)->where('is_delete',0)->get('trans_main')->row();
		$invoiceData->itemData = $this->db->where('trans_main_id',$id)->where('is_delete',0)->get('trans_child')->result();

		$partyData = $this->db->select('party_master.*,currency.inrrate')->join('currency','currency.currency = party_master.currency','left')->where('party_master.is_delete',0)->where('party_cm_id',$cm_id)->get('party_master')->row();
		if(!empty($partyData)):
			if (floatval($partyData->inrrate) <= 0) :
				$data['currency'] = "INR";
				$data['inrrate'] = 1;
			else :
				$data['currency'] = $partyData->currency;
				$data['inrrate'] = $partyData->inrrate;
			endif;

			$trans_no = $this->db->select('ifnull((MAX(trans_no) + 1),1) as trans_no')->where('entry_type',12)->where('is_delete',0)->where('trans_date >=',$this->startYearDate)->where('trans_date <=',$this->endYearDate)->where('cm_id',$partyData->cm_id)->get('trans_main')->row()->trans_no;
			$trans_prefix = $this->transModel->getTransPrefix(12);

			$spAccounts = $this->db->where('party_category',4)->where('cm_id',$partyData->cm_id)->where('is_delete',0)->where_in('group_code',['PA'])->get('party_master')->result();
			
			$ledgerList = $this->db->where('party_category',4)->where('cm_id',$partyData->cm_id)->where('is_delete',0)->where_in('group_code',["DT", "ED", "EI", "ID", "II"])->get('party_master')->result();
			
			$expenseList = $this->db->where('is_active',1)->where('entry_type',1)->where('is_delete',0)->where('cm_id',$partyData->cm_id)->get('expense_master')->result();

			$roff_acc =  $this->db->where('party_category',4)->where('cm_id',$partyData->cm_id)->where('is_delete',0)->where_in('system_code','ROFFACC')->get('party_master')->row();
			$roff_acc_id = (!empty($roff_acc))?$roff_acc->id:0;
			
			$sp_acc_key = array_search("PURGSTACC",array_column($spAccounts,'system_code'));
			$sp_acc_id = $spAccounts[$sp_acc_key]->id;

			$igst_acc_key = array_search("IGSTIPACC",array_column($ledgerList,'system_code'));
			$igst_acc_id = $ledgerList[$igst_acc_key]->id;

			$cgst_acc_key = array_search("CGSTIPACC",array_column($ledgerList,'system_code'));
			$cgst_acc_id = $ledgerList[$cgst_acc_key]->id;

			$sgst_acc_key = array_search("SGSTIPACC",array_column($ledgerList,'system_code'));
			$sgst_acc_id = $ledgerList[$sgst_acc_key]->id;

			$masterData = [
				'id' => "",
				'entry_type' => 12,
				'from_entry_type' => 0,
				'ref_id' => $id,
				'trans_status' => 99,
				'trans_no' => $trans_no,
				'trans_prefix' => $trans_prefix,
				'trans_number' => getPrefixNumber($trans_prefix, $trans_no),
				'trans_date' => date('Y-m-d', strtotime($invoiceData->trans_date)),
				'party_id' => $partyData->id,
				'opp_acc_id' => $partyData->id,
				'sp_acc_id' => $sp_acc_id,
				'party_name' => $partyData->party_name,
				'party_state_code' => 24,
				'gstin' => $partyData->gstin,
				'gst_applicable' => $invoiceData->gst_applicable,
				'gst_type' => $invoiceData->gst_type,
				'doc_no' => $invoiceData->trans_number,
				'doc_date' => date('Y-m-d', strtotime($invoiceData->trans_date)),
				'challan_no' => "",
				'total_amount' => $invoiceData->total_amount,
				'taxable_amount' => $invoiceData->taxable_amount,
				'gst_amount' => $invoiceData->gst_amount,
				'igst_acc_id' => $igst_acc_id,
				'igst_per' => $invoiceData->igst_per,
				'igst_amount' => $invoiceData->igst_amount,
				'sgst_acc_id' => $sgst_acc_id,
				'sgst_per' => $invoiceData->sgst_per,
				'sgst_amount' => $invoiceData->sgst_amount,
				'cgst_acc_id' => $cgst_acc_id,
				'cgst_per' => $invoiceData->cgst_per,
				'cgst_amount' => $invoiceData->cgst_amount,
				'cess_acc_id' => 0,
				'cess_per' => $invoiceData->cess_per,
				'cess_amount' => $invoiceData->cess_amount,
				'cess_qty_acc_id' => 0,
				'cess_qty' => $invoiceData->cess_qty,
				'cess_qty_amount' => $invoiceData->cess_qty_amount,
				'tcs_acc_id' => 0,
				'tcs_per' => $invoiceData->tcs_per,
				'tcs_amount' => $invoiceData->tcs_amount,
				'tds_acc_id' => 0,
				'tds_per' => $invoiceData->tds_per,
				'tds_amount' => $invoiceData->tds_amount,
				'disc_amount' => $invoiceData->disc_amount,
				'apply_round' => $invoiceData->apply_round,
				'round_off_acc_id'  => $roff_acc_id,
				'round_off_amount' => $invoiceData->round_off_amount,
				'net_amount' => $invoiceData->net_amount,
				'terms_conditions' => $invoiceData->terms_conditions,
				'remark' => $invoiceData->remark,
				'currency' => $data['currency'],
				'inrrate' => $data['inrrate'],
				'vou_name_s' => getVoucherNameShort(12),
				'vou_name_l' => getVoucherNameLong(12),
				'ledger_eff' => 1,
				'created_by' => 0,
				'is_app' => 1,
				'cm_id' => $partyData->cm_id
			];	

			$accType = getSystemCode(12, false);
			if (!empty($accType)) :
				$spAcc = $this->db->where('cm_id',$partyData->cm_id)->where('is_delete',0)->where('system_code',$accType)->get('party_master')->row();
				$masterData['vou_acc_id'] = (!empty($spAcc)) ? $spAcc->id : 0;
			else :
				$masterData['vou_acc_id'] = 0;
			endif;

			$expenseData = array();

			$RTD_STORE=$this->db->where('is_delete',0)->where('cm_id',$partyData->cm_id)->where('store_type',1)->get('location_master')->row();

			foreach($invoiceData->itemData as $row):
				$itemData['id'][] = "";
				$itemData['from_entry_type'][] = "";
				$itemData['ref_id'][] = "";
				$itemData['item_id'][] = $row->item_id;
				$itemData['item_name'][] = $row->item_name;
				$itemData['item_type'][] = $row->item_type;
				$itemData['item_code'][] = $row->item_code;
				$itemData['item_desc'][] = $row->item_desc;
				$itemData['unit_id'][] = $row->unit_id;
				$itemData['unit_name'][] = $row->unit_name;
				$itemData['location_id'][] = $RTD_STORE->id;
				$itemData['batch_no'][] = "General Batch";
				$itemData['hsn_code'][] = $row->hsn_code;
				$itemData['qty'][] = $row->qty;
				$itemData['stock_eff'][] = 1;
				$itemData['price'][] = $row->price;
				$itemData['org_price'][] = $row->org_price;
				$itemData['amount'][] = $row->amount;
				$itemData['taxable_amount'][] = $row->taxable_amount;
				$itemData['gst_per'][] = $row->gst_per;
				$itemData['gst_amount'][] = $row->gst_amount;
				$itemData['igst_per'][] = $row->igst_per;
				$itemData['igst_amount'][] = $row->igst_amount;
				$itemData['sgst_per'][] = $row->sgst_per;
				$itemData['sgst_amount'][] = $row->sgst_amount;
				$itemData['cgst_per'][] = $row->cgst_per;
				$itemData['cgst_amount'][] = $row->cgst_amount;
				$itemData['disc_per'][] = $row->disc_per;
				$itemData['disc_amount'][] = $row->disc_amount;
				$itemData['item_remark'][] = $row->item_remark;
				$itemData['net_amount'][] = $row->net_amount;
			endforeach;

			$result = $this->purchaseInvoice->save($masterData, $itemData, $expenseData);
			return $result;
		else:
			return false;
		endif;
	}

	public function stockOutItemList($off_set=0){
		$limit = (isset($_REQUEST['per_page']) && !empty($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $search = (isset($_REQUEST['search']) && !empty($_REQUEST['search']))?$_REQUEST['search']:"";
        $item_type = (isset($_REQUEST['item_type']) && !empty($_REQUEST['item_type']))?$_REQUEST['item_type']:1;
        $postData = ['off_set'=>$off_set,'limit'=>$limit,'search'=>$search,'item_type'=>$item_type,'price_required'=>1,'stock_required'=>1,'cm_id'=>1];
        $this->data['itemList'] = $this->item->getItemForStockInOut($postData);
        $this->printJson(['status'=>1,'message'=>'Recored found.','field_error'=>0,'field_error_message'=>null,'data'=>$this->data]);
	}

	/* 
	Stock Transfer from World Gift to RJ and gerenate auto Debit Note
	Generate Debit Note in World Gift
	Generate Credit Note in RJ	
	*/
	public function saveDebitNote(){
		$data = $this->input->post();

		if(empty($data['cm_id']))
			$this->printJson(['status'=>0,'message'=>'Please scan QR Code.','field_error'=>0,'field_error_message'=>null,'data'=>null]); 
		if(empty($data['item_id']))
			$this->printJson(['status'=>0,'message'=>'Please select items.','field_error'=>0,'field_error_message'=>null,'data'=>null]);
		if(!empty($data['cm_id'])):
			if($data['cm_id'] == $this->CMID):
				$this->printJson(['status'=>0,'message'=>'Invalid','field_error'=>0,'field_error_message'=>null,'data'=>null]); 
			endif;
		endif;

		$rjGiftData = $this->db->select('party_master.*,currency.inrrate')->join('currency','currency.currency = party_master.currency','left')->where('party_master.is_delete',0)->where('party_cm_id',$data['cm_id'])->get('party_master')->row();

		if (floatval($rjGiftData->inrrate) <= 0) :
			$data['currency'] = "INR";
			$data['inrrate'] = 1;
		else :
			$data['currency'] = $rjGiftData->currency;
			$data['inrrate'] = $rjGiftData->inrrate;
		endif;

		$locationId = $this->db->where('is_delete',0)->where('cm_id',$rjGiftData->cm_id)->where('store_type',1)->get('location_master')->row()->id;

		$itemData = array();$total_amount = 0;$total_gst_amount=0;$total_net_amount = 0;
		foreach($data['item_id'] as $key=>$item_id):
			$this->db->select("item_master.*,unit_master.unit_name,SUM(stock_transaction.qty) as stock_qty");
			$this->db->join('unit_master',"item_master.unit_id = unit_master.id","left");
			$this->db->join('stock_transaction','stock_transaction.item_id = item_master.id');
			$this->db->where('item_master.id',$item_id);
			$this->db->where('stock_transaction.item_id',$item_id);
			$this->db->where('stock_transaction.cm_id',$rjGiftData->cm_id);
			$this->db->where('stock_transaction.is_delete',0);
			$productData = $this->db->get('item_master')->row();

			if($data['qty'][$key] > $productData->stock_qty):
				$this->printJson(['status'=>0,'message'=>'Stock not avlible. Item Name : '.$productData->item_name,'field_error'=>0,'field_error_message'=>null,'data'=>null]); 
			endif;

			$gst_per = $productData->gst_per;
			$amount = round(($data['qty'][$key] * $productData->price2),2);
			$gst_amount = round((($amount * $gst_per) / 100),2);
			$net_amount = round(($amount + $gst_amount),2);

			$total_amount += $amount;
			$total_gst_amount += $gst_amount;
			$total_net_amount += $net_amount;			

			$itemData['id'][] = "";
			$itemData['from_entry_type'][] = "";
			$itemData['ref_id'][] = "";
			$itemData['item_id'][] = $item_id;
			$itemData['item_name'][] = $productData->item_name;
			$itemData['item_type'][] = $productData->item_type;
			$itemData['item_code'][] = $productData->item_code;
			$itemData['item_desc'][] = $productData->description;
			$itemData['unit_id'][] = $productData->unit_id;
			$itemData['unit_name'][] = $productData->unit_name;
			$itemData['location_id'][] = $locationId;
			$itemData['batch_no'][] = "General Batch";
			$itemData['hsn_code'][] = $productData->hsn_code;
			$itemData['qty'][] = $data['qty'][$key];
			$itemData['stock_eff'][] = 1;
			$itemData['price'][] = $productData->price2;
			$itemData['org_price'][] = $productData->price2;
			$itemData['amount'][] = $amount;
			$itemData['taxable_amount'][] = $amount;
			$itemData['gst_per'][] = $gst_per;
			$itemData['gst_amount'][] = $gst_amount;
			$itemData['igst_per'][] = $gst_per;
			$itemData['igst_amount'][] = $gst_amount;
			$itemData['sgst_per'][] = round(($gst_per/2),2);
			$itemData['sgst_amount'][] = round(($gst_amount/2),2);
			$itemData['cgst_per'][] = round(($gst_per/2),2);
			$itemData['cgst_amount'][] = round(($gst_amount/2),2);
			$itemData['disc_per'][] = 0;
			$itemData['disc_amount'][] = 0;
			$itemData['item_remark'][] = "";
			$itemData['net_amount'][] = $net_amount;
		endforeach;

		$trans_no = $this->db->select('ifnull((MAX(trans_no) + 1),1) as trans_no')->where('entry_type',14)->where('is_delete',0)->where('trans_date >=',$this->startYearDate)->where('trans_date <=',$this->endYearDate)->where('cm_id',$rjGiftData->cm_id)->get('trans_main')->row()->trans_no;
		$trans_prefix = $this->transModel->getTransPrefix(14);

		$spAccounts = $this->db->where('party_category',4)->where('cm_id',$rjGiftData->cm_id)->where('is_delete',0)->where_in('group_code',['PA'])->get('party_master')->result();
		
		$ledgerList = $this->db->where('party_category',4)->where('cm_id',$rjGiftData->cm_id)->where('is_delete',0)->where_in('group_code',["DT", "ED", "EI", "ID", "II"])->get('party_master')->result();

		$roff_acc =  $this->db->where('party_category',4)->where('cm_id',$rjGiftData->cm_id)->where('is_delete',0)->where_in('system_code','ROFFACC')->get('party_master')->row();
		$roff_acc_id = (!empty($roff_acc))?$roff_acc->id:0;
		
		$sp_acc_key = array_search("PURGSTACC",array_column($spAccounts,'system_code'));
		$sp_acc_id = $spAccounts[$sp_acc_key]->id;

		$igst_acc_key = array_search("IGSTIPACC",array_column($ledgerList,'system_code'));
		$igst_acc_id = $ledgerList[$igst_acc_key]->id;

		$cgst_acc_key = array_search("CGSTIPACC",array_column($ledgerList,'system_code'));
		$cgst_acc_id = $ledgerList[$cgst_acc_key]->id;

		$sgst_acc_key = array_search("SGSTIPACC",array_column($ledgerList,'system_code'));
		$sgst_acc_id = $ledgerList[$sgst_acc_key]->id;

		/*$totalNetAmount = array_sum($itemData['net_amount']);
		$totalNetAmount = sprintf("%.2f",$totalNetAmount);
		$netAmount = 0;$roundOffAmt = 0;
		$decimal = explode(".",$totalNetAmount)[1];
		$decimal = round($decimal);
		if($decimal != 0):
			if($decimal >= 50):
				$roundOffAmt = ((100 - $decimal) / 100);
				$netAmount = $totalNetAmount + $roundOffAmt;
			elseif($decimal < 50):
				$roundOffAmt = (($decimal - ($decimal * 2)) / 100);
				$netAmount = $totalNetAmount + $roundOffAmt;
			endif;
		endif;*/
		
		$netAmount = 0;$roundOffAmt = 0;
		$totalNetAmount = array_sum($itemData['net_amount']);		
		$decimal = round($totalNetAmount - intVal($totalNetAmount),2);
		
		if($decimal > 0 AND $decimal < 0.50){$roundOffAmt = round(($decimal * -1),2);}
		if($decimal >= 0.50){$roundOffAmt = round(((1 - $decimal)/100),2);}
		
		$netAmount = $totalNetAmount + $roundOffAmt;

		$masterData = [
			'id' => "",
			'entry_type' => 14,
			'from_entry_type' => 0,
			'ref_id' => "",
			'trans_status' => 99,
			'trans_no' => $trans_no,
			'trans_prefix' => $trans_prefix,
			'trans_number' => getPrefixNumber($trans_prefix, $trans_no),
			'trans_date' => date('Y-m-d'),
			'memo_type' => "DEBIT",
			'party_id' => $rjGiftData->id,
			'opp_acc_id' => $rjGiftData->id,
			'sp_acc_id' => $sp_acc_id,
			'party_name' => $rjGiftData->party_name,
			'party_state_code' => 24,
			'gstin' => $rjGiftData->gstin,
			'gst_applicable' => 1,
			'gst_type' => 1,
			'doc_no' => "",
			'doc_date' => date('Y-m-d'),
			'challan_no' => "",
			'total_amount' => array_sum($itemData['amount']),
			'taxable_amount' => array_sum($itemData['taxable_amount']),
			'gst_amount' => array_sum($itemData['igst_amount']),
			'igst_acc_id' => $igst_acc_id,
			'igst_per' => 0,
			'igst_amount' => array_sum($itemData['igst_amount']),
			'sgst_acc_id' => $sgst_acc_id,
			'sgst_per' => 0,
			'sgst_amount' => array_sum($itemData['sgst_amount']),
			'cgst_acc_id' => $cgst_acc_id,
			'cgst_per' => 0,
			'cgst_amount' => array_sum($itemData['cgst_amount']),
			'cess_acc_id' => 0,
			'cess_per' => 0,
			'cess_amount' => 0,
			'cess_qty_acc_id' => 0,
			'cess_qty' => 0,
			'cess_qty_amount' => 0,
			'tcs_acc_id' => 0,
			'tcs_per' => 0,
			'tcs_amount' => 0,
			'tds_acc_id' => 0,
			'tds_per' => 0,
			'tds_amount' => 0,
			'disc_amount' => array_sum($itemData['disc_amount']),
			'apply_round' => 0,
			'round_off_acc_id'  => $roff_acc_id,
			'round_off_amount' => $roundOffAmt,
			'net_amount' => $netAmount,
			'terms_conditions' => json_encode(array()),
			'remark' => "",
			'currency' => $data['currency'],
			'inrrate' => $data['inrrate'],
			'vou_name_s' => getVoucherNameShort(14),
			'vou_name_l' => getVoucherNameLong(14),
			'ledger_eff' => 1,
			'created_by' => $this->loginId,
			'is_app' => 1,
			'cm_id' => $rjGiftData->cm_id
		];	

		$accType = getSystemCode(14, false);
		if (!empty($accType)) :
			$spAcc = $this->db->where('cm_id',$rjGiftData->cm_id)->where('is_delete',0)->where('system_code',$accType)->get('party_master')->row();
			$masterData['vou_acc_id'] = (!empty($spAcc)) ? $spAcc->id : 0;
		else :
			$masterData['vou_acc_id'] = 0;
		endif;

		$result = $this->debitNote->save($masterData,$itemData,array());

		if(isset($result['insert_id'])):
			$cn = $this->sendCreditNote($result['insert_id'],$rjGiftData->cm_id);
		endif;
		unset($result['url']);
		$result['message'] = "Stock transfer successfully.";
		$this->printJson($result);
	}

	public function sendCreditNote($id,$cm_id){
		$debitNoteData = $this->db->where('id',$id)->where('is_delete',0)->get('trans_main')->row();
		$debitNoteData->itemData = $this->db->where('trans_main_id',$id)->where('is_delete',0)->get('trans_child')->result();

		$partyData = $this->db->select('party_master.*,currency.inrrate')->join('currency','currency.currency = party_master.currency','left')->where('party_master.is_delete',0)->where('party_cm_id',$cm_id)->get('party_master')->row();

		if(!empty($partyData)):
			if (floatval($partyData->inrrate) <= 0) :
				$data['currency'] = "INR";
				$data['inrrate'] = 1;
			else :
				$data['currency'] = $partyData->currency;
				$data['inrrate'] = $partyData->inrrate;
			endif;

			$trans_no = $this->db->select('ifnull((MAX(trans_no) + 1),1) as trans_no')->where('entry_type',13)->where('is_delete',0)->where('trans_date >=',$this->startYearDate)->where('trans_date <=',$this->endYearDate)->where('cm_id',$partyData->cm_id)->get('trans_main')->row()->trans_no;
			$trans_prefix = $this->transModel->getTransPrefix(13);

			$spAccounts = $this->db->where('party_category',4)->where('cm_id',$partyData->cm_id)->where('is_delete',0)->where_in('group_code',['SA'])->get('party_master')->result();
			
			$ledgerList = $this->db->where('party_category',4)->where('cm_id',$partyData->cm_id)->where('is_delete',0)->where_in('group_code',["DT", "ED", "EI", "ID", "II"])->get('party_master')->result();

			$roff_acc =  $this->db->where('party_category',4)->where('cm_id',$partyData->cm_id)->where('is_delete',0)->where_in('system_code','ROFFACC')->get('party_master')->row();
			$roff_acc_id = (!empty($roff_acc))?$roff_acc->id:0;
			
			$sp_acc_key = array_search("SALESGSTACC",array_column($spAccounts,'system_code'));
			$sp_acc_id = $spAccounts[$sp_acc_key]->id;

			$igst_acc_key = array_search("IGSTOPACC",array_column($ledgerList,'system_code'));
			$igst_acc_id = $ledgerList[$igst_acc_key]->id;

			$cgst_acc_key = array_search("CGSTOPACC",array_column($ledgerList,'system_code'));
			$cgst_acc_id = $ledgerList[$cgst_acc_key]->id;

			$sgst_acc_key = array_search("SGSTOPACC",array_column($ledgerList,'system_code'));
			$sgst_acc_id = $ledgerList[$sgst_acc_key]->id;

			$masterData = [
				'id' => "",
				'entry_type' => 13,
				'from_entry_type' => 0,
				'ref_id' => $id,
				'trans_status' => 99,
				'trans_no' => $trans_no,
				'trans_prefix' => $trans_prefix,
				'trans_number' => getPrefixNumber($trans_prefix, $trans_no),
				'trans_date' => date('Y-m-d', strtotime($debitNoteData->trans_date)),
				'memo_type' => $debitNoteData->memo_type,
				'party_id' => $partyData->id,
				'opp_acc_id' => $partyData->id,
				'sp_acc_id' => $sp_acc_id,
				'party_name' => $partyData->party_name,
				'party_state_code' => 24,
				'gstin' => $partyData->gstin,
				'gst_applicable' => $debitNoteData->gst_applicable,
				'gst_type' => $debitNoteData->gst_type,
				'doc_no' => $debitNoteData->trans_number,
				'doc_date' => date('Y-m-d', strtotime($debitNoteData->trans_date)),
				'challan_no' => "",
				'total_amount' => $debitNoteData->total_amount,
				'taxable_amount' => $debitNoteData->taxable_amount,
				'gst_amount' => $debitNoteData->gst_amount,
				'igst_acc_id' => $igst_acc_id,
				'igst_per' => $debitNoteData->igst_per,
				'igst_amount' => $debitNoteData->igst_amount,
				'sgst_acc_id' => $sgst_acc_id,
				'sgst_per' => $debitNoteData->sgst_per,
				'sgst_amount' => $debitNoteData->sgst_amount,
				'cgst_acc_id' => $cgst_acc_id,
				'cgst_per' => $debitNoteData->cgst_per,
				'cgst_amount' => $debitNoteData->cgst_amount,
				'cess_acc_id' => 0,
				'cess_per' => $debitNoteData->cess_per,
				'cess_amount' => $debitNoteData->cess_amount,
				'cess_qty_acc_id' => 0,
				'cess_qty' => $debitNoteData->cess_qty,
				'cess_qty_amount' => $debitNoteData->cess_qty_amount,
				'tcs_acc_id' => 0,
				'tcs_per' => $debitNoteData->tcs_per,
				'tcs_amount' => $debitNoteData->tcs_amount,
				'tds_acc_id' => 0,
				'tds_per' => $debitNoteData->tds_per,
				'tds_amount' => $debitNoteData->tds_amount,
				'disc_amount' => $debitNoteData->disc_amount,
				'apply_round' => $debitNoteData->apply_round,
				'round_off_acc_id'  => $roff_acc_id,
				'round_off_amount' => $debitNoteData->round_off_amount,
				'net_amount' => $debitNoteData->net_amount,
				'terms_conditions' => $debitNoteData->terms_conditions,
				'remark' => $debitNoteData->remark,
				'currency' => $data['currency'],
				'inrrate' => $data['inrrate'],
				'vou_name_s' => getVoucherNameShort(13),
				'vou_name_l' => getVoucherNameLong(13),
				'ledger_eff' => 1,
				'created_by' => 0,
				'is_app' => 1,
				'cm_id' => $partyData->cm_id
			];	

			$accType = getSystemCode(13, false);
			if (!empty($accType)) :
				$spAcc = $this->db->where('cm_id',$partyData->cm_id)->where('is_delete',0)->where('system_code',$accType)->get('party_master')->row();
				$masterData['vou_acc_id'] = (!empty($spAcc)) ? $spAcc->id : 0;
			else :
				$masterData['vou_acc_id'] = 0;
			endif;

			$expenseData = array();

			$RTD_STORE=$this->db->where('is_delete',0)->where('cm_id',$partyData->cm_id)->where('store_type',1)->get('location_master')->row();

			foreach($debitNoteData->itemData as $row):
				$itemData['id'][] = "";
				$itemData['from_entry_type'][] = "";
				$itemData['ref_id'][] = "";
				$itemData['item_id'][] = $row->item_id;
				$itemData['item_name'][] = $row->item_name;
				$itemData['item_type'][] = $row->item_type;
				$itemData['item_code'][] = $row->item_code;
				$itemData['item_desc'][] = $row->item_desc;
				$itemData['unit_id'][] = $row->unit_id;
				$itemData['unit_name'][] = $row->unit_name;
				$itemData['location_id'][] = $RTD_STORE->id;
				$itemData['batch_no'][] = "General Batch";
				$itemData['hsn_code'][] = $row->hsn_code;
				$itemData['qty'][] = $row->qty;
				$itemData['stock_eff'][] = 1;
				$itemData['price'][] = $row->price;
				$itemData['org_price'][] = $row->org_price;
				$itemData['amount'][] = $row->amount;
				$itemData['taxable_amount'][] = $row->taxable_amount;
				$itemData['gst_per'][] = $row->gst_per;
				$itemData['gst_amount'][] = $row->gst_amount;
				$itemData['igst_per'][] = $row->igst_per;
				$itemData['igst_amount'][] = $row->igst_amount;
				$itemData['sgst_per'][] = $row->sgst_per;
				$itemData['sgst_amount'][] = $row->sgst_amount;
				$itemData['cgst_per'][] = $row->cgst_per;
				$itemData['cgst_amount'][] = $row->cgst_amount;
				$itemData['disc_per'][] = $row->disc_per;
				$itemData['disc_amount'][] = $row->disc_amount;
				$itemData['item_remark'][] = $row->item_remark;
				$itemData['net_amount'][] = $row->net_amount;
			endforeach;

			$result = $this->creditNote->save($masterData, $itemData, $expenseData);
			return $result;
		else:
			return false;
		endif;
	}
}
?>