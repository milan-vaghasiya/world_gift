<?php
class Invoice extends MY_Apicontroller{
    private $trans_mode=['CASH','CARD','UPI'];
	public function __construct(){
        parent::__construct();
    }
    
	public function getSalesInvoiceList($off_set=0){
        $limit = (isset($_REQUEST['per_page']) && !empty($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $search = (isset($_REQUEST['search']) && !empty($_REQUEST['search']))?$_REQUEST['search']:"";
        $postData = ['off_set'=>$off_set,'limit'=>$limit,'search'=>$search];
        $this->data['invoiceList'] = $this->salesInvoice->getSalesInvoiceList_api($postData);
        $this->printJson(['status'=>1,'message'=>'Recored found.','field_error'=>0,'field_error_message'=>null,'data'=>$this->data]);
    }
	
	public function viewInvoice($id){
        $this->data['invoiceData'] = $this->salesInvoice->getInvoice($id);
        $this->printJson(['status'=>1,'message'=>'Recored found.','field_error'=>0,'field_error_message'=>null,'data'=>$this->data]);
    }
	
    public function addInvoice(){
		$off_set = 0;
		$off_set = 0;
		$limit = (isset($_REQUEST['per_page']) && !empty($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $search = (isset($_REQUEST['search']) && !empty($_REQUEST['search']))?$_REQUEST['search']:"";
        $item_type = (isset($_REQUEST['item_type']) && !empty($_REQUEST['item_type']))?$_REQUEST['item_type']:1;
        $postData = ['off_set'=>$off_set,'limit'=>$limit,'search'=>$search,'item_type'=>$item_type];
		
        $this->data['from_entry_type'] = 0;
		$this->data['ref_id'] = "";
		$this->data['gst_type'] = 1;
		$this->data['gst_applicable'] = 1;
		if($this->CMID == 1):
			$this->data['trans_prefix'] = "WG/";
		else:
			$this->data['trans_prefix'] = "RJ/";
		endif;
		
		//$this->data['trans_prefix'] = $this->transModel->getTransPrefix(6);
        $this->data['trans_no'] = $this->transModel->nextTransNo(6);
		$this->data['trans_mode'] = $this->trans_mode;
		$this->data['vou_acc_id'] = 24;
		$partySelect = 'party_master.id, party_master.party_code, party_master.party_name, party_master.party_phone, party_master.gstin	, party_master.party_email';
        $this->data['customerData'] = $this->party->getCustomerList($partySelect);
        $this->printJson(['status'=>1,'message'=>'Recored found.','field_error'=>0,'field_error_message'=>null,'data'=>$this->data]);
    }
        
    public function save(){
		$data = $this->input->post();
		//print_r($data);exit;
		$errorMessage = array();$data['gstin'] = '';
		$data['currency'] = '';$data['inrrate'] = 0;$data['party_name'] = "";$data['party_state_code']="";$data['gst_type'] =1;
		if(empty($data['party_id'])):
			$errorMessage['party_id'] = "Party name is required.";
		else:
			$partyData = $this->party->getParty($data['party_id']);
			$data['party_name'] = $partyData->party_name; 
			if(!empty($partyData->gstin)):
				$data['gstin'] = $partyData->gstin;
				$data['party_state_code'] = substr($partyData->gstin, 0, 2);
			endif;
			if(floatval($partyData->inrrate) <= 0):
				$errorMessage['party_id'] = "Currency not set.";
			else:
				$data['currency'] = $partyData->currency;
				$data['inrrate'] = $partyData->inrrate;
			endif;
		endif;
		if(empty($data['item_id'])):
			$errorMessage['item_name_error'] = "Product is required.";
		else:
			$i=1;
			foreach($data['item_id'] as $key=>$value):
				$itmData = $this->item->getItem_api($value);
				$data['disc_per'][$key]=(isset($data['disc_per'][$key]))?$data['disc_per'][$key]:0;
				$data['trans_id'][$key]=(isset($data['trans_id'][$key]))?$data['trans_id'][$key]:0;
				$data['item_name'][$key]=$itmData->item_name;
				$data['item_type'][$key]=1;
				$data['item_desc'][$key]='';
				$data['item_code'][$key]=$itmData->item_code;
				$data['unit_id'][$key]=$itmData->unit_id;
				$data['unit_name'][$key]=$itmData->unit_name;
				$data['location_id'][$key]=11;
				$data['batch_no'][$key]='General Batch';
				$data['batch_qty'][$key]=$data['qty'][$key];
				$data['stock_eff'][$key]=1;
				$data['hsn_code'][$key]=$itmData->hsn_code;
				$data['price'][$key]=$itmData->price;
				$data['org_price'][$key]=$itmData->price;
				$data['gst_per'][$key]=$itmData->gst_per;
				$data['item_remark'][$key]='';
				$data['from_entry_type'][$key]='';
				$data['ref_id'][$key]='';
				$data['cm_id'][$key]=$this->CMID;
				$data['created_by'][$key]=$this->loginId;
				
				$mrp = $data['price'][$key];$gstReverse = 1;
				if(!empty($itmData->gst_per))
				{
					$gstReverse = round((($itmData->gst_per+100)/100),2);
					$data['price'][$key]=round((floatVal($data['price'][$key])/$gstReverse),2);
				}
				
				if(empty($data['price'][$key])):
					$errorMessage['price'.$i] = "Price is required.";
				elseif(empty($data['qty'][$key])):
					$errorMessage['qty'] = "Qty is required.";
				else:
					$currentStock = 0;$old_qty = 0;
					if($data['stock_eff'][$key] == 1):
						$cStock = $this->store->getItemCurrentStock($value,$this->RTD_STORE->id);
						$currentStock = (!empty($cStock)) ? $cStock->qty : 0;							
						if(!empty($data['trans_id'][$key])):
							$transData = $this->salesInvoice->salesTransRow($data['trans_id'][$key]);
							if(!empty($transData)){$old_qty = $transData->qty;}
						endif;
					endif;
					if(($currentStock + $old_qty) < $data['qty'][$key]):
						$errorMessage["qty"] = "Stock not available.";
					else:
					    // Discount Calc for MRP
					    if($this->CMID == 1)
					    {
                    		$disc_amt = $data['disc_amt'][$key];
                			$amt = round(($data['qty'][$key] * $mrp),2);
                			$discountedAmt = $amt - $disc_amt;
                    		if($disc_amt != "" AND $disc_amt > 0){
                    		    $discountedPrice = round(($discountedAmt / $data['qty'][$key]),2);
                    		    $qtyDisc = round(($disc_amt / $data['qty'][$key]),2);
                    			if($qtyDisc > 0)
                            	{
                            		$new_price = round(($discountedPrice/$gstReverse),2);
                            		$new_price += $qtyDisc;
                        		    $data['price'][$key] = $new_price;
                            	}
                    		}
					    }
						$data['amount'][$key] = round((floatVal($data['qty'][$key]) * floatVal($data['price'][$key])),2);
						if(!empty($data['disc_amt'][$key])):
						    $data['taxable_amount'][$key] = $data['amount'][$key] - $data['disc_amt'][$key];
							$data['disc_per'][$key] = round(((($data['amount'][$key] - $data['taxable_amount'][$key])*100) / $data['amount'][$key]),2);
						else:
							$data['disc_amt'][$key] = 0;$data['disc_per'][$key] = 0;
							$data['taxable_amount'][$key] = $data['amount'][$key];
						endif;
						if($data['gst_type'] == 1):
							if(!empty($itmData->gst_per)):
								$data['igst'][$key] = $itmData->gst_per;
								$data['cgst'][$key] = $data['sgst'][$key] = round(($itmData->gst_per/2),2);
								$data['cgst_amt'][$key] = $data['sgst_amt'][$key] = round((($data['taxable_amount'][$key]*$data['cgst'][$key])/100),2);
								$data['igst_amt'][$key] = $data['cgst_amt'][$key] + $data['sgst_amt'][$key];
								$data['net_amount'][$key] = $data['taxable_amount'][$key] + $data['igst_amt'][$key];
							endif;
						endif;
					endif;
				endif;
				$i++;
			endforeach;
		endif;
		
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else :
			$data['entry_type'] = 6;
			$data['inv_no'] = $this->transModel->nextTransNo(6);
			if($this->CMID == 1):
    			$data['inv_prefix'] = "WG/";
    		else:
    			$data['inv_prefix'] = "RJ/";
    		endif;
			//$data['inv_prefix'] = $this->transModel->getTransPrefix(6);
			$data['memo_type']='DEBIT';
			//if($data['payment_mode'] == 'CASH'){$data['memo_type']='CASH';}
			$data['net_inv_amount'] = 0;
			$netAmt = array_sum($data['net_amount']);
			$decimalPoints = round($netAmt - intVal($netAmt),2);
			$data['roff_amount'] = 0;
			if($decimalPoints > 0 AND $decimalPoints < 0.50){$data['roff_amount'] = round(($decimalPoints * -1),2);}
			if($decimalPoints >= 0.50){$data['roff_amount'] = round(((1 - $decimalPoints)/100),2);}
			
			$data['net_inv_amount'] = $netAmt + $data['roff_amount'];
			$masterData = [ 
				'id' => $data['sales_id'],
				'entry_type' => $data['entry_type'],
				'from_entry_type' => '',
				'ref_id' => '',
				'trans_no' => $data['inv_no'],
				'trans_prefix' => $data['inv_prefix'],
				'trans_number' => getPrefixNumber($data['inv_prefix'],$data['inv_no']),
				'trans_date' => date('Y-m-d',strtotime($data['inv_date'])), 
				'memo_type' => $data['memo_type'],
				'party_id' => $data['party_id'],
				'opp_acc_id' => $data['party_id'],
				'sp_acc_id' => 2,
				'party_name' => $data['party_name'],
				'party_state_code' => $data['party_state_code'],
				'gstin' => $data['gstin'],
				'gst_applicable' => 1,
				'gst_type' => $data['gst_type'],
				'doc_no'=>$data['inv_no'],
				'doc_date'=>date('Y-m-d',strtotime($data['inv_date'])),
				'total_amount' => array_sum($data['amount']),
				'taxable_amount' => array_sum($data['taxable_amount']),
				'gst_amount' => (isset($data['igst_amt']))?array_sum($data['igst_amt']):0,
				'igst_acc_id' => (isset($data['igst_acc_id']))?$data['igst_acc_id']:6,
				'igst_per' => (isset($data['igst']))?max($data['igst']):0,
				'igst_amount' => (isset($data['igst_amt']))?array_sum($data['igst_amt']):0,
				'sgst_acc_id' => (isset($data['sgst_acc_id']))?$data['sgst_acc_id']:5,
				'sgst_per' => (isset($data['sgst']))?max($data['sgst']):0,
				'sgst_amount' => (isset($data['sgst_amt']))?array_sum($data['sgst_amt']):0,
				'cgst_acc_id' => (isset($data['cgst_acc_id']))?$data['cgst_acc_id']:4,
				'cgst_per' => (isset($data['cgst']))?max($data['cgst']):0,
				'cgst_amount' => (isset($data['cgst_amt']))?array_sum($data['cgst_amt']):0,
				'cess_acc_id' => (isset($data['cess_acc_id']))?$data['cess_acc_id']:0,
				'cess_per' => (isset($data['cess_per']))?$data['cess_per']:0,
				'cess_amount' => (isset($data['cess_amount']))?$data['cess_amount']:0,
				'cess_qty_acc_id' => (isset($data['cess_qty_acc_id']))?$data['cess_qty_acc_id']:0,
				'cess_qty' => (isset($data['cess_qty']))?$data['cess_qty']:0,
				'cess_qty_amount' => (isset($data['cess_qty_amount']))?$data['cess_qty_amount']:0,
				'tcs_acc_id' => (isset($data['tcs_acc_id']))?$data['tcs_acc_id']:0,
				'tcs_per' => (isset($data['tcs_per']))?$data['tcs_per']:0,
				'tcs_amount' => (isset($data['tcs_amount']))?$data['tcs_amount']:0,
				'tds_acc_id' => (isset($data['tds_acc_id']))?$data['tds_acc_id']:0,
				'tds_per' => (isset($data['tds_per']))?$data['tds_per']:0,
				'tds_amount' => (isset($data['tds_amount']))?$data['tds_amount']:0,
				'disc_amount' => array_sum($data['disc_amt']),
				'apply_round' => 1, 
				'round_off_acc_id'  => 23,
				'round_off_amount' => (isset($data['roff_amount']))?$data['roff_amount']:0, 
				'net_amount' => $data['net_inv_amount'],
                'currency' => $data['currency'],
                'inrrate' => $data['inrrate'],
				'vou_name_s' => getVoucherNameShort($data['entry_type']),
				'vou_name_l' => getVoucherNameLong($data['entry_type']),
				'ledger_eff' => 1,
				'remark' => '',
				'is_app' => 1,
				'cm_id' => $this->CMID,
				'created_by' => $this->loginId
			];
			
			// IF CASH MEMO THEN AUTO VOUCHER ENTRY
			$ledgerData = [];
			if($data['memo_type'] == 'CASH'):
			    $paymentModes = $this->transModel->getPaymentModes('CASH');
			    if(!empty($paymentModes->ledger_id)):
        			$trans_prefix = $this->transModel->getTransPrefix(15);
        			$trans_no = $this->transModel->nextTransNo(15);
        			$ledgerData = [ 
        				'id' => "",//$data['voucher_id'],
        				'entry_type' => 15,
        				'trans_prefix'=>$trans_prefix,
        				'trans_no'=>$trans_no,
        				'trans_date' => date('Y-m-d',strtotime($data['inv_date'])), 
        				'doc_no' => $masterData['trans_number'],
        				'doc_date' => date('Y-m-d',strtotime($data['inv_date'])),
        				'party_id' => $data['party_id'],
        				'opp_acc_id' => $data['party_id'],
        				'vou_acc_id' => $paymentModes->ledger_id,
        				'trans_mode' => "CASH",//$data['trans_mode'],
        				'net_amount' => $data['net_inv_amount'],	
        				'created_by' => $this->session->userdata('loginId'),
        				'cm_id' => $this->CMID,
        				'remark'=>''
        			];
        		endif;
    		endif;
			
			/*$trans_prefix = $this->transModel->getTransPrefix(15);
			$trans_no = $this->transModel->nextTransNo(15);
			$paymentModes = $this->transModel->getPaymentModes($data['payment_mode']);
			$ledgerData = [ 
				'id' => '',//$data['voucher_id'],
				'entry_type' => 15,
				'trans_prefix'=>$trans_prefix,
				'trans_no'=>$trans_no,
				'trans_date' => date('Y-m-d',strtotime($data['inv_date'])), 
				'doc_no' => $data['voucher_doc_no'],
				'doc_date' => $data['voucher_doc_date'],
				'party_id' => $data['party_id'],
				'opp_acc_id' => $data['party_id'],
				'vou_acc_id' => (!empty($paymentModes->ledger_id)) ? $paymentModes->ledger_id : $data['vou_acc_id'],
				'trans_mode' => $data['payment_mode'],//$data['trans_mode'],	
				'net_amount' => $data['net_inv_amount'],
				'remark'=>''
			];*/
			
			$transExp = getExpArrayMap($data);
			$expAmount = $transExp['exp_amount'];
			$expenseData = array();
            if($expAmount > 0):
				unset($transExp['exp_amount']);    
				$expenseData = $transExp;
			endif;
			$accType = getSystemCode($data['entry_type'],false);
            if(!empty($accType)):
				$spAcc = $this->ledger->getLedgerOnSystemCode($accType);
                $masterData['vou_acc_id'] = (!empty($spAcc))?$spAcc->id:0;
            else:
                $masterData['vou_acc_id'] = 0;
            endif;
			
			$itemData = [
				'id' => $data['trans_id'],
				'from_entry_type' => $data['from_entry_type'],
				'ref_id' => $data['ref_id'],
				'item_id' => $data['item_id'],
				'item_name' => $data['item_name'],
				'item_type' => $data['item_type'],
				'item_code' => $data['item_code'],
				'unit_id' => $data['unit_id'],
				'unit_name' => $data['unit_name'],
				'location_id' => $data['location_id'],
				'batch_no' => $data['batch_no'],
				'batch_qty' => $data['batch_qty'],
				'stock_eff' => $data['stock_eff'],
				'hsn_code' => $data['hsn_code'],
				'qty' => $data['qty'],
				'price' => $data['price'],
				'org_price' => $data['org_price'],
				'amount' => $data['amount'],
				'taxable_amount' => $data['taxable_amount'],				
				'gst_per' => $data['gst_per'],
				'gst_amount' => $data['igst_amt'],
				'igst_per' => $data['igst'],
				'igst_amount' => $data['igst_amt'],
				'sgst_per' => $data['sgst'],
				'sgst_amount' => $data['sgst_amt'],
				'cgst_per' => $data['cgst'],
				'cgst_amount' => $data['cgst_amt'],
				'disc_per' => $data['disc_per'],
				'disc_amount' => $data['disc_amt'],
				'item_remark' => $data['item_remark'],
				'net_amount' => $data['net_amount'],
				'cm_id' => $data['cm_id'],
				'created_by' => $data['created_by']
			];
			//print_r($masterData);exit;
			$result = $this->salesInvoice->save($masterData,$itemData,$expenseData,$ledgerData);
			$this->printJson(['status'=>1,'message'=>"Invoice saved Successfully.",'field_error'=>0,'field_error_message'=>null]);
        endif;
        
    }

    public function editInvoice($id){
		$invoiceData = $this->salesInvoice->getInvoice($id);
		$voucherData=$this->paymentVoucher->getReceiveVoucherByRefId($id);
		
		$this->data['id'] = $id;
		$this->data['from_entry_type'] = $invoiceData->from_entry_type;
		$this->data['party_id'] = $invoiceData->party_id;
		$this->data['ref_id'] = $invoiceData->ref_id;
		$this->data['gst_type'] = $invoiceData->gst_type;
		$this->data['gst_applicable'] = $invoiceData->gst_applicable;
		$this->data['trans_prefix'] = $invoiceData->trans_prefix;
        $this->data['trans_no'] = $invoiceData->trans_no;
		$this->data['trans_mode'] = $this->trans_mode;
		$this->data['transMode'] = $voucherData->trans_mode;
		$this->data['vou_acc_id'] = $invoiceData->vou_acc_id;
		$this->data['invItems'] = Array();
		if(!empty($invoiceData->itemData))
		{
			$items = Array();
			foreach($invoiceData->itemData as $row)
			{
				$itm = New StdClass;
				$itm->id = $row->item_id;
				$itm->trans_id = $row->id;
				$itm->old_qty = $row->qty;
				$itm->disc_per = $row->disc_per;
				$items[] = $itm;
			}
			$this->data['invItems'] = $items;
		}
		$partySelect = 'party_master.id, party_master.party_code, party_master.party_name, party_master.party_phone, party_master.gstin	, party_master.party_email';
        $this->data['customerData'] = $this->party->getCustomerList($partySelect);
		
		
		print_r($this->data);
		exit;
		
        $this->printJson(['status'=>1,'message'=>'Recored found.','field_error'=>0,'field_error_message'=>null,'data'=>$this->data]);
    }
    
    public function invoice_pdf()
	{
		$postData = $this->input->post();
		$original=1;$duplicate=0;$triplicate=0;$header_footer=0;$extra_copy=0;
		if(isset($postData['original'])){$original=1;}
		if(isset($postData['duplicate'])){$duplicate=1;}
		if(isset($postData['triplicate'])){$triplicate=1;}
		if(isset($postData['header_footer'])){$header_footer=1;}
		if(!empty($postData['extra_copy'])){$extra_copy=$postData['extra_copy'];}
		
		$sales_id=$postData['id'];
		$salesData = $this->salesInvoice->getInvoice($sales_id);
		$companyData = $this->salesInvoice->getCompanyInfo();
		
		$partyData = $this->party->getParty($salesData->party_id);
		
		$response="";
		$letter_head=base_url('assets/images/letterhead_top.png');
		
		$currencyCode = "INR";
		$symbol = "";
		
		$response="";$inrSymbol=base_url('assets/images/inr.png');
		$headerImg = base_url('assets/images/rtth_lh_header.png');
		$footerImg = base_url('assets/images/rtth_lh_footer.png');
		$logoFile=(!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
		$logo=base_url('assets/images/'.$logoFile);
		$auth_sign=base_url('assets/images/rtth_sign.png');
		
		$gstHCol='';$gstCol='';$blankTD='';$bottomCols=2;$GSTAMT=$salesData->igst_amount;
		$subTotal=$salesData->taxable_amount;
		$itemList='<table class="table table-bordered poItemList">
					<thead><tr class="text-center">
						<th style="width:6%;">Sr.No.</th>
						<th class="text-left">Description of Goods</th>
						<th style="width:10%;">HSN/SAC</th>
						<th style="width:10%;">Qty</th>
						<th style="width:10%;">Rate</th>
						<th style="width:8%;">GST %</th>
						<th style="width:11%;">Amount</th>
					</tr></thead><tbody>';
		
		// Terms & Conditions
		
		$blankLines=25;if(!empty($header_footer)){$blankLines=25;}
		$terms = '<table class="table">';$t=0;$tc=new StdClass;	
		$terms1 = '<tr><td style="width:60%;vertical-align:top;">
						<table class="table"><tr><th style="width:60%;text-align:left;">Terms & Conditions : </th></tr>';
					
		$terms2 = '<td style="width:40%; vertical-align:top;">
						<table class="table">
							<tr>
							<th style="width:40%;vertical-align:top;text-align:center;font-size:1rem;padding:5px 2px;">
								For, '.$companyData->company_name.'</th>
							</tr>
							<tr><td height="40"></td></tr>
							<tr><td class="text-center fs-14">(Authorised Signatury)</td></tr>
						</table>';
		$termsLine=0;
		if(!empty($salesData->terms_conditions))
		{
			$tc=json_decode($salesData->terms_conditions);$termsLine=count($tc);
			$blankLines=21 - count($tc);if(!empty($header_footer)){$blankLines=21 - count($tc);}
			foreach($tc as $trms):
				$terms1 .= '<tr><td style="width:60%;font-size:12px;text-align:left;">'.($t+1).'. <i>'.$trms->condition.'</i></td></tr>';
				$t++;
			endforeach;
		}
		else
		{
			$tc = array();
			$terms1 .= '<tr>
							<td style="width:65%;font-size:12px;">Subject to RAJKOT Jurisdiction</td>
							<th rowspan="'.count($tc).'" style="width:35%;vertical-align:bottom;text-align:center;font-size:1rem;padding:5px 2px;">
								For, '.$companyData->company_name.'<br>
								<!--<img src="'.$auth_sign.'" style="width:120px;">-->
							</th>
					</tr>';
		}
		$terms1 .= '</table>';
		$terms .= $terms1.$terms2.'</td></tr></table>';
		
		$subTotal=0;$lastPageItems = '';$pageCount = 0; $sgstAmt = 0; $cgstAmt=0; $igstAmt=0; $taxableAmt=0;
		$i=1;$tamt=0;$cgst=9;$sgst=9;$cgst_amt=0;$sgst_amt=0;$netamt=0;$igst=0;$hsnCode='';$total_qty=0;$page_qty = 0;$page_amount = 0;
		$pageData = array();
		
		$itmLine=26;if(!empty($header_footer)){$itmLine=26;}
		$orderData = $this->salesInvoice->salesTransactions($sales_id);
		
		$totalItems = count($orderData);
		$firstArr = $orderData;$secondArr = Array();$lastPageRow = $totalItems;$pagedArray = Array();$rowPerPage = $itmLine;
		if($totalItems > $itmLine)
		{
			$rowPerPage = ($totalItems > $itmLine) ? $itmLine : $itmLine ;
			$lastPageRow = $totalItems % $rowPerPage;
			//$lastPageRow = $totalItems / $rowPerPage;
			$firstArr = array_slice($orderData,($totalItems - $lastPageRow),$lastPageRow);
			$secondArr = array_slice($orderData,0,($totalItems - $lastPageRow));
		}
		
		$pagedArray = array_chunk($secondArr,$rowPerPage);
		
		$pagedArray[] = $firstArr;
		$blankLines = $itmLine - $lastPageRow;
		
		$x=1;$totalPage = count($pagedArray);$i=1;$highestGst = 0;$itmGst = Array();
		foreach($pagedArray as $tempData)
		//for($x=0;$x<=$totalPage;$x++)
		{
			$page_qty = 0;$page_amount = 0;$pageItems = '';$page_nos = 0;$prevLines = 0;
			//$tempData = $this->salesInvoice->salesTransactions($sales_id,$pr.','.$pageCount);
			if(!empty($tempData))
			{
				foreach ($tempData as $row)
				{
					$pageItems.='<tr>';
						$pageItems.='<td class="text-center" height="26">'.$i.'</td>';
						$pageItems.='<td class="text-left">'.$row->item_name.'</td>';
						$pageItems.='<td class="text-center">'.$row->hsn_code.'</td>';
						$pageItems.='<td class="text-center">'.sprintf('%0.2f', $row->qty).'</td>';
						$pageItems.='<td class="text-right">'.sprintf('%0.2f', $row->price).'</td>';
						$pageItems.='<td class="text-center">'.floatval($row->igst_per).'</td>';
						$pageItems.='<td class="text-right">'.sprintf('%0.2f', $row->amount).'</td>';
					$pageItems.='</tr>';
					
					$total_qty += $row->qty;$page_qty += $row->qty;$page_amount += $row->amount; 
					if($this->CMID == 1):
						$subTotal += $row->amount; 
						$cgstAmt += $row->cgst_amount;
						$sgstAmt += $row->sgst_amount;
						$igstAmt += $row->igst_amount;
						$taxableAmt+= $row->taxable_amount;
					else:
						$subTotal += $row->amount ;
						$cgstAmt += $salesData->sgst_amount + round(($salesData->freight_gst / 2),2);
						$sgstAmt += $salesData->cgst_amount + round(($salesData->freight_gst / 2),2);
						$taxableAmt+= $subTotal + $salesData->freight_amount;
					endif;
					$itmGst[] = $row->igst_per;
					$i++;
				}
			}
			if($x==$totalPage)
			{
				$pageData[$x-1]= '';
				$lastPageItems = $pageItems;
			}
			else
			{
				$pageData[$x-1]=$itemList.$pageItems.'</tbody></table><div class="text-right"><i>Continue to Next Page</i></div>';
			}
			//$pageCount += $pageRow;
			$x++;
		}
		$fgst = round(($salesData->freight_gst / 2),2);
		$rwspan= 4;$cgstPer = $sgstPer = round((MAX($itmGst)/2),2);$igstPer = round(MAX($itmGst),2);
		$gstAmount = $salesData->cgst_amount + $salesData->sgst_amount;
		
		$gstRow='<tr>';
			$gstRow.='<td colspan="2" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">CGST '.$cgstPer.'%</td>';
			$gstRow.='<td class="text-right" style="border-top:0px !important;">'.sprintf('%0.2f', $salesData->cgst_amount).'</td>';
		$gstRow.='</tr>';
		
		$gstRow.='<tr>';
			$gstRow.='<td colspan="2" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">SGST '.$sgstPer.'%</td>';
			$gstRow.='<td class="text-right" style="border-top:0px !important;">'.sprintf('%0.2f', $salesData->sgst_amount).'</td>';
		$gstRow.='</tr>';
		
		$party_gstin = $salesData->gstin;$party_stateCode = $salesData->party_state_code;
		
		if(!empty($party_gstin))
		{
			if($party_stateCode!="24")
			{
				$gstRow='<tr>';
					$gstRow.='<td colspan="2" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">IGST '.$igstPer.'%</td>';
					$gstRow.='<td class="text-right" style="border-top:0px !important;">'.sprintf('%0.2f', ($salesData->igst_amount)).'</td>';
				$gstRow.='</tr>';$rwspan= 3;$gstAmount = $salesData->igst_amount;
			}
		}
		$totalCols = 8;
		$itemList .= $lastPageItems;
		if($i<$blankLines)
		{
			for($z=$i;$z<=$blankLines;$z++)
			{$itemList.='<tr><td height="26">&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';}
		}
		
		$itemList.='<tr>';
			$itemList.='<td colspan="3" class="text-right" style="vartical-align:top;border-top:1px solid #000;border-right:1px solid #000;"><b>Total Qty</b></td>';
			$itemList.='<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;">'.sprintf('%0.2f', $total_qty).'</th>';
			$itemList.='<th colspan="2" class="text-right" style="border:1px solid #000;border-left:0px solid #000;">Sub Total</th>';
			$itemList.='<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;">'.sprintf('%0.2f', $salesData->total_amount).'</th>';
		$itemList.='</tr>';
		
		$itemList.='<tr>';
			$itemList.='<td colspan="4" rowspan="'.$rwspan.'" class="text-left" style="vartical-align:top;border-top:1px solid #000;border-right:1px solid #000;"><b>Bank Name : </b>'.$companyData->company_bank_name.'<br>
			<b>A/c. No. : </b>'.$companyData->company_acc_no.'<br>
			<b>IFSC Code : </b>'.$companyData->company_ifsc_code.'
			</td>';
			$itemList.='<td colspan="2" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">&nbsp;</td>';
			$itemList.='<td class="text-right" style="border-top:0px !important;"></td>';
		$itemList.='</tr>';
		
		$itemList.='<tr>';
			$itemList.='<th colspan="2" class="text-right" style="border:1px solid #000;border-left:0px solid #000;">Taxable Amount</th>';
			$itemList.='<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;">'.sprintf('%0.2f', $salesData->taxable_amount).'</th>';
		$itemList.='</tr>';
		
		$itemList.=$gstRow;
		
		$itemList.='<tr>';
			$itemList.='<td colspan="4" rowspan="2" class="text-left" style="vartical-align:top;border:1px solid #000;border-left:0px;">
				<i><b>Total GST : </b>'.numToWordEnglish($gstAmount).'</i><br>
				<i><b>Bill Amount : </b>'.numToWordEnglish($salesData->net_amount).'</i>
			</td>';
			$itemList.='<td colspan="2" class="text-right" style="border-right:1px solid #000;">Round Off</td>';
			$itemList.='<td class="text-right" style="border-top:0px !important;border-left:0px;">'.sprintf('%0.2f', $salesData->round_off_amount).'</td>';
		$itemList.='</tr>';
		
		$itemList.='<tr>';
			$itemList.='<th colspan="2" class="text-right" style="border-top:1px solid #000;border-right:1px solid #000;font-size:14px;">Payable Amount</th>';
			$itemList.='<th class="text-right" height="40" style="border-top:1px solid #000;border-left:0px;font-size:14px;">'.sprintf('%0.2f', $salesData->net_amount).'</th>';
		$itemList.='</tr>';
		$itemList.='<tbody></table>';
		
		$pageData[$totalPage-1] .= $itemList;
		$pageData[$totalPage-1] .= $terms;
		
		$invoiceType=array();
		$invType = array("ORIGINAL","DUPLICATE","TRIPLICATE","EXTRA COPY");$i=0;
		$baseTable='<table class="poTopTable" style="margin-bottom:5px;">';
		foreach($invType as $it)
		{
			$invoiceType[$i++]='<tr>
									<th style="width:35%;letter-spacing:2px;border:0px;font-size:15px;" class="text-left" >GSTIN: '.$companyData->company_gst_no.'</th>
									<th style="width:30%;letter-spacing:2px;border:0px;font-size:15px;" class="text-center">TAX INVOICE</th>
									<th style="width:35%;letter-spacing:2px;border:0px;font-size:15px;" class="text-right">'.$it.'</th>
								</tr>';
		}
		$gstJson=json_decode($partyData->json_data);
		$partyAddress=(!empty($gstJson->{$salesData->gstin})?$gstJson->{$salesData->gstin}:'');
		$place_of_supply = '';
		if(!empty($party_stateCode))
		{
			$stateData = $this->party->getStateByIdOrCode('',$party_stateCode);
			if(!empty($stateData)){$place_of_supply = $party_stateCode.'-'.$stateData->name;}
		}
		$baseDetail='<table class="poTopTable" style="margin-bottom:5px;">
						<tr>
							<td style="width:65%;" rowspan="3">
								<table>
									<tr><td style="vertical-align:top;"><b>M/s. : '.$salesData->party_name.'</b></td></tr>
									<tr><td class="text-left" style="">'.(!empty($partyAddress->party_address)?$partyAddress->party_address:'').'</td></tr>
									<tr><td class="text-left" style=""><b>GSTIN : '.$salesData->gstin.'</b></td></tr>
								</table>
							</td>
							<td style="width:14%;border-right:0px;"><b>Invoice No.</b></td>
							<td style="width:21%;border-left:0px;">: '.$salesData->trans_prefix.$salesData->trans_no.'</td>
						</tr>
						<tr>
							<td style="border-right:0px;"><b>Date : </b></td>
							<td style="border-left:0px;">: '.date('d/m/Y', strtotime($salesData->trans_date)).'</td>
						</tr>
						<tr>
							<td style="border-right:0px;"><b>Supply of Place</b></td>
							<td style="border-left:0px;">: '.$place_of_supply.'</td>
						</tr>
					</table>';
				
		$orsp='';$drsp='';$trsp='';
		$htmlHeader = '<table class="topTable">
						<tr>
							<th colspan="3" class="org_title text-uppercase text-center bg-light" style="font-size:1.2rem;">'.$companyData->company_name.'</th>
						</tr>
						<tr><td colspan="3" class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.' | Mobile: '.$companyData->company_contact.'</td></tr>
					';
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:25%;font-size:12px;">INV No. & Date : '.$salesData->trans_prefix.$salesData->trans_no.'-'.formatDate($salesData->trans_date).'</td>
							<td style="width:25%;font-size:12px;"></td>
							<td style="width:25%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		//foreach($pageData as $pg){echo $pg;}exit;
		$mpdf = $this->m_pdf->load();
		$i=1;$p='P';
		$pdfFileName=str_replace('/','-',$salesData->trans_prefix.$salesData->trans_no).'_'.$sales_id.'.pdf';
		$fpath='/assets/uploads/sales/sales_invoice_'.$sales_id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/bill_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		
		if(!empty($header_footer))
		{
			$mpdf->SetWatermarkImage($logo,0.05,array(120,60));
			$mpdf->showWatermarkImage = true;
		}
		
		if(!empty($original))
		{
			$pdfData = '';
			if(!empty($header_footer))
			{
				$htmlHeader .= $invoiceType[0].'</table>';
				$mpdf->SetHTMLHeader($htmlHeader);
				$mpdf->SetHTMLFooter($htmlFooter);
				$pdfData = $baseDetail;
			}
			else
			{
				//$pdfData ='<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">'.$invoiceType[0].'</table>'.$baseDetail;
				$pdfData =$baseDetail;
			}
			foreach($pageData as $pg)
			{
				$mpdf->AddPage('P','','','','',7,7,28,7,5,6);
				$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv1">'.$pdfData.$pg.'</div></div>');
			}
		}
		
		if(!empty($duplicate))
		{
			foreach($pageData as $pg)
			{
				if(!empty($header_footer))
				{
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[1].$baseDetail.$pg.'</div></div>');
				}
				else
				{
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[1].$baseDetail.$pg.'</div></div>');
				}
			}
		}
		
		if(!empty($triplicate))
		{
			foreach($pageData as $pg)
			{
				if(!empty($header_footer))
				{
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[2].$baseDetail.$pg.'</div></div>');
				}
				else
				{
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[2].$baseDetail.$pg.'</div></div>');
				}
			}
		}
		
		for($x=0;$x<$extra_copy;$x++)
		{
			foreach($pageData as $pg)
			{
				if(!empty($header_footer))
				{
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[3].$baseDetail.$pg.'</div></div>');
				}
				else
				{
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[3].$baseDetail.$pg.'</div></div>');
				}
			}
		}
		
		$savePath = realpath(APPPATH . '../assets/uploads/sales_invoice/');
        $mpdf->Output( $savePath."/".$pdfFileName,'F');

        $this->printJson(['status'=>1,'message'=>'PDF generated successfully.','field_error'=>0,'field_error_message'=>null,'data'=>['file_path'=>base_url("assets/uploads/sales_invoice/".$pdfFileName)]]);
	}

}
?>