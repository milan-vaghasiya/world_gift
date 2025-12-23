<?php
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\PHPExcel_RichText;

class SalesInvoice extends MY_Controller{	
	private $indexPage = "sales_invoice/index";
    private $invoiceForm = "sales_invoice/form";
	//private $paymentMode=['CASH','CHEQUE','IB','CARD','UPI'];
	private $paymentMode=['CASH','CARD','UPI'];
	
	public function __construct(){ 
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Sales Invoice";
		$this->data['headData']->controller = "salesInvoice";
		$this->data['headData']->pageUrl = "salesInvoice";
	}

	public function index(){
		$this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);    
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0,$entry_type="6,7,8"){
		$data = $this->input->post(); 
		$data['entry_type'] = $entry_type;
		$data['status'] = $status;
        $result = $this->salesInvoice->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++; $row->po_no = ''; $row->ref_no = '';
            if(!empty($row->from_entry_type)):
               $refData = $this->salesInvoice->getInvoice($row->ref_id);
               $row->po_no = $refData->doc_no;
            endif;
            $row->CMID = $this->CMID;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getSalesInvoiceData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	public function createInvoice(){
		$data = $this->input->post(); //print_r($data); exit;
		$invMaster = new stdClass();
        $invMaster = $this->party->getParty($data['party_id']);  
		$this->data['gst_type']  = (!empty($invMaster->gstin))?((substr($invMaster->gstin,0,2) == 24)?1:2):1;		
		$this->data['from_entry_type'] = $data['from_entry_type'];
		$this->data['ref_id'] = implode(",",$data['ref_id']);
		$this->data['bill_per'] = $data['bill_per'];
		
		if($data['from_entry_type'] == 4){
			$soData = $this->salesOrder->getSoData($this->data['ref_id'],4);
			
			$soTransNo= ''; $i=1;
			foreach($soData as $row):
				if($i==1){ $soTransNo .= getPrefixNumber($row->trans_prefix,$row->trans_no); }
				else{ $soTransNo .= ', '.getPrefixNumber($row->trans_prefix,$row->trans_no); }
				$i++;
			endforeach;		
			$this->data['soTransNo'] = $soTransNo; 
		}elseif($data['from_entry_type'] == 5){
			//$dcData = $this->challan->getChallanData($data['ref_id']);
			$dcData =$this->salesOrder->getSoData($this->data['ref_id'],5);
			
			$dcTransNo= ''; $i=1; $soTransNo ='';
			foreach($dcData as $row):
				if($i==1){
					$dcTransNo .= getPrefixNumber($row->trans_prefix,$row->trans_no);
					$soTransNo .= $row->doc_no;
				}
				else{
					$dcTransNo .= ', '.getPrefixNumber($row->trans_prefix,$row->trans_no);
					$soTransNo .= $row->doc_no;
				}
				$i++;
			endforeach;		
			$this->data['dcTransNo'] = $dcTransNo;
			$this->data['soTransNo'] = $soTransNo;
			
		}elseif($data['from_entry_type'] == 9){
			//$pInvData = $this->proformaInv->getPInvData($data['ref_id']);
			$pInvData =$this->salesOrder->getSoData($this->data['ref_id'],9);
			$soTransNo= ''; $i=1;
			foreach($pInvData as $row):
				if($i==1){ $soTransNo .= getPrefixNumber($row->trans_prefix,$row->trans_no); }
				else{ $soTransNo .= ', '.getPrefixNumber($row->trans_prefix,$row->trans_no); }
				$i++;
			endforeach;		
			$this->data['soTransNo'] = $soTransNo;
		}
		$this->data['invMaster'] = $invMaster; 
		$this->data['invItems'] = array();
		if($data['from_entry_type'] == 4)
		{
			$this->data['invItems'] = $this->salesOrder->getOrderItems($this->data['ref_id']);
		}
		elseif($data['from_entry_type'] == 5)
		{
			$this->data['invItems'] = $this->challan->getChallanItems($this->data['ref_id']);
		}
		elseif($data['from_entry_type'] == 9)
		{
			$this->data['invItems'] = $this->proformaInv->getPInvItems($this->data['ref_id']);
		}
		
		$mtype= "";
		if($this->CMID == 1):
			$this->data['trans_prefix'] = "WGM/";$mtype= "CASH";
		else:
			$this->data['trans_prefix'] = "WGM/";$mtype= "DEBIT";
		endif;
        $this->data['nextTransNo'] = $this->transModel->nextTransNo(6,$mtype);
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['locationData'] = $this->store->getLocationList();
		$this->data['terms'] = $this->terms->getTermsListByType('Sales');   
		$this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(2);
		$this->data['spAccounts'] = $this->ledger->getLedgerList(["'SA'"]); 
		$this->data['ledgerList'] = $this->ledger->getLedgerList(["'DT'","'ED'","'EI'","'ID'","'II'"]);
		$this->data['paymentMode'] = $this->paymentMode;
        $this->load->view($this->invoiceForm,$this->data);
	}

    public function addInvoice(){
		$this->data['from_entry_type'] = 0;
		$this->data['ref_id'] = "";
		$this->data['gst_type'] = 1;
		$mtype= "";
		if($this->CMID == 1):
			$this->data['trans_prefix'] = "WGM/";$mtype= "CASH";
		else:
			$this->data['trans_prefix'] = "WGM/";$mtype= "DEBIT";
		endif;
        $this->data['nextTransNo'] = $this->transModel->nextTransNo(6,$mtype);
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['locationData'] = $this->store->getLocationList();
		$this->data['terms'] = $this->terms->getTermsListByType('Sales');   
		$this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(2);
		$this->data['spAccounts'] = $this->ledger->getLedgerList(["'SA'"]); 
		$this->data['ledgerList'] = $this->ledger->getLedgerList(["'DT'","'ED'","'EI'","'ID'","'II'"]);
		$this->data['paymentMode'] = $this->paymentMode;
        $this->load->view($this->invoiceForm,$this->data);
    }

	public function getInvNo(){
        $memo_type = $this->input->post('memo_type');
        $inv_no = $this->transModel->nextTransNo(6,$memo_type);
        $inv_prefix = "";
		if($this->CMID == 1):
            if($memo_type == "CASH"){$inv_prefix = "WGM/";}else{$inv_prefix = "WG/";}
        elseif($this->CMID == 2):
            if($memo_type == "CASH"){$inv_prefix = "RJM/";}else{$inv_prefix = "WGM/";}
        endif;
        $this->printJson(['status'=>1,'inv_prefix'=>$inv_prefix,'inv_no'=>$inv_no]);
    }

	public function getBatchNo(){
        $item_id = $this->input->post('item_id');
        $location_id = $this->input->post('location_id');
        $batchData = $this->item->locationWiseBatchStock($item_id,$location_id);
        $options = '<option value="">Select Batch No.</option>';
        foreach($batchData as $row):
			if($row->qty > 0):
				$options .= '<option value="'.$row->batch_no.'" data-stock="'.$row->qty.'">'.$row->batch_no.'</option>';
			endif;
        endforeach;
        $this->printJson(['status'=>1,'options'=>$options]);
    }
	
	public function getItemData(){
		$id = $this->input->post('itemId');
        $result = $this->item->getItem($id);
        $unitData = $this->item->itemUnit($result->unit_id);
        $result->unit_name = $unitData->unit_name;
        $result->description = $unitData->description;
		$this->printJson($result);
	}

	public function getCustomerData(){
		$memo_type = $this->input->post('memo_type');
        $customerData = $this->party->getCustomerList(); $options='<option value="">Select Party</option>';
		foreach ($customerData as $row) :
			if($memo_type == 'DEBIT'):
				if(!empty($row->gstin)):
					$options .= "<option data-row='" . json_encode($row) . "' value='" . $row->id . "'>" . $row->party_name . "</option>";
				endif;
			else:
				$options .= "<option data-row='" . json_encode($row) . "' value='" . $row->id . "'>" . $row->party_name . "</option>";
			endif;
		endforeach;
        $this->printJson(['status'=>1,'options'=>$options]);
	}
	
	public function save(){
		$data = $this->input->post(); //print_r($data); exit;
		$errorMessage = array();
		$data['currency'] = '';$data['inrrate'] = 0;
		if(empty($data['party_id'])):
			$errorMessage['party_id'] = "Party name is required.";
		else:
			$partyData = $this->party->getParty($data['party_id']); 
			if(floatval($partyData->inrrate) <= 0):
				$errorMessage['party_id'] = "Currency not set.";
			else:
				$data['currency'] = $partyData->currency;
				$data['inrrate'] = $partyData->inrrate;
			endif;
		endif;
		if(empty($data['sp_acc_id']))
			$errorMessage['sp_acc_id'] = "Sales A/c. is required.";

		if($this->CMID == 1):
			if($data['memo_type'] == 'CASH' AND $data['inv_prefix'] != 'WGM/')
				$errorMessage['inv_prefix'] = "Invalid Invoice No.";
			if($data['memo_type'] == 'DEBIT' AND $data['inv_prefix'] != 'WG/')
				$errorMessage['inv_prefix'] = "Invalid Invoice No.";
		endif;

		if(empty($data['item_id'][0]))
			$errorMessage['item_name_error'] = "Product is required.";
		
        if(!empty($data['item_id'])):
			$i=1;
			foreach($data['item_id'] as $key=>$value):
				if(empty($data['price'][$key])):
					$errorMessage['price'.$key] = "Price is required.";
				endif;
				if(empty($data['location_id'][$key])):
					$errorMessage["qty".$key] = "Location is required.";
				elseif($data['stock_eff'][$key] == 1):
					$cStock = $this->store->getItemCurrentStock($value,$data['location_id'][$key]);
					$currentStock = (!empty($cStock)) ? $cStock->qty : 0;
					$old_qty = 0;
					if(!empty($data['trans_id'][$key])):
						$transData = $this->salesInvoice->salesTransRow($data['trans_id'][$key]);
						if(!empty($transData)){$old_qty = $transData->qty;}
					endif;
					if(($currentStock + $old_qty) < $data['qty'][$key]):
				        $errorMessage["qty".$key] = "Stock not available. ".$data['item_name'][$key]." : ".($currentStock + $old_qty);
					endif;
				endif;
				$i++;
			endforeach;
		endif;
		
		/*if(empty($data['term_id'][0]))
			$errorMessage['term_id'] = "Terms Conditions is required.";*/
			
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
		else:
			$data['terms_conditions'] = "";$termsArray = array();
			if(isset($data['term_id']) && !empty($data['term_id'])):
				foreach($data['term_id'] as $key=>$value):
					$termsArray[] = [
						'term_id' => $value,
						'term_title' => $data['term_title'][$key],
						'condition' => $data['condition'][$key]
					];
				endforeach;
				$data['terms_conditions'] = json_encode($termsArray);
			endif;
			$gstAmount = 0;
			if($data['gst_type'] == 1):
				if(isset($data['cgst_amount'])):
					$gstAmount = $data['cgst_amount'] + $data['sgst_amount'];
				endif;	
			elseif($data['gst_type'] == 2):
				if(isset($data['igst_amount'])):
					$gstAmount = $data['igst_amount'];
				endif;
			endif;
			if(empty($data['sales_id']))
			{
                $data['inv_no'] = $this->transModel->nextTransNo(6,$data['memo_type']);
                $data['inv_prefix'] = "";
        		if($this->CMID == 1):
                    if($data['memo_type'] == "CASH"){$data['inv_prefix'] = "WGM/";}else{$data['inv_prefix'] = "WG/";}
                elseif($this->CMID == 2):
                    if($data['memo_type'] == "CASH"){$data['inv_prefix'] = "RJM/";}else{$data['inv_prefix'] = "WGM/";}
                endif;
			}
			$gstin = (!empty($data['gstin']))? $data['gstin']:'';
			$masterData = [ 
				'id' => $data['sales_id'],
				'entry_type' => $data['entry_type'],
				'from_entry_type' => $data['reference_entry_type'],
				'ref_id' => $data['reference_id'],
				'trans_no' => $data['inv_no'], 
				'trans_prefix' => $data['inv_prefix'],
				'trans_number' => getPrefixNumber($data['inv_prefix'],$data['inv_no']),
				'trans_date' => date('Y-m-d',strtotime($data['inv_date'])), 
				'invoice_type' => $data['invoice_type'],
				'party_id' => $data['party_id'],
				'opp_acc_id' => $data['party_id'],
				'sp_acc_id' => $data['sp_acc_id'],
				'party_name' => $data['party_name'],
				'party_alias' => $data['party_alias'], 
				'party_state_code' => $data['party_state_code'],
				'gstin' => $gstin,
				'gst_applicable' => $data['gst_applicable'],
				'gst_type' => $data['gst_type'],
				'sales_type' => $data['sales_type'], 
				'challan_no' => $data['challan_no'], 
				'doc_no'=>$data['so_no'],
				'doc_date'=>date('Y-m-d',strtotime($data['inv_date'])),
				'gross_weight' => $data['gross_weight'],
				'eway_bill_no' => $data['eway_bill_no'],
				'lr_no' => $data['lrno'],
				'transport_name' => $data['transport'],
				'shipping_address' => $data['supply_place'],
				'total_amount' => array_sum($data['amount']) + array_sum($data['disc_amt']),
				'taxable_amount' =>(isset($data['taxable_amount']))?$data['taxable_amount']:0,
				'gst_amount' => $gstAmount,
				'igst_acc_id' => (isset($data['igst_acc_id']))?$data['igst_acc_id']:0,
				'igst_per' => (isset($data['igst_per']))?$data['igst_per']:0,
				'igst_amount' => (isset($data['igst_amount']))?$data['igst_amount']:0,
				'sgst_acc_id' => (isset($data['sgst_acc_id']))?$data['sgst_acc_id']:0,
				'sgst_per' => (isset($data['sgst_per']))?$data['sgst_per']:0,
				'sgst_amount' => (isset($data['sgst_amount']))?$data['sgst_amount']:0,
				'cgst_acc_id' => (isset($data['cgst_acc_id']))?$data['cgst_acc_id']:0,
				'cgst_per' => (isset($data['cgst_per']))?$data['cgst_per']:0,
				'cgst_amount' => (isset($data['cgst_amount']))?$data['cgst_amount']:0,
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
				'apply_round' => $data['apply_round'], 
				'round_off_acc_id'  => (isset($data['roff_acc_id']))?$data['roff_acc_id']:0,
				'round_off_amount' => (isset($data['roff_amount']))?$data['roff_amount']:0, 
				'net_amount' => $data['net_inv_amount'],
				'terms_conditions' => $data['terms_conditions'],
                'remark' => $data['remark'],
                'currency' => $data['currency'],
                'inrrate' => $data['inrrate'],
				'vou_name_s' => getVoucherNameShort($data['entry_type']),
				'vou_name_l' => getVoucherNameLong($data['entry_type']),
				'ledger_eff' => 1,
				'memo_type' => $data['memo_type'],
				'created_by' => $this->session->userdata('loginId')
			];
			
			// IF CASH MEMO THEN AUTO VOUCHER ENTRY
			$ledgerData = [];
			if($masterData['memo_type'] == 'CASH'):
			    $paymentModes = $this->transModel->getPaymentModes('CASH');
			    if(!empty($paymentModes->ledger_id)):
        			$trans_prefix = $this->transModel->getTransPrefix(15);
        			$trans_no = $this->transModel->nextTransNo(15,"CASH");
        			$ledgerData = [ 
        				'id' => $data['voucher_id'],
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
        				'is_delete' => 0,
        				'remark'=>''
        			];
        		endif;
    		endif;
			$transExp = getExpArrayMap($data);
			$expAmount = $transExp['exp_amount'];
			$expenseData = array();
            if($expAmount <> 0):
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
				'item_desc' => $data['item_desc'],
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
				'taxable_amount' => $data['amount'],				
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
				'net_amount' => $data['net_amount']
			];
			
			$result = $this->salesInvoice->save($masterData,$itemData,$expenseData,$ledgerData);
			
			if($this->CMID == 2 AND $partyData->party_cm_id == 1):
				if(empty($data['sales_id']) && isset($result['insert_id'])):
					$this->sendPurchaseInvoice($result['insert_id']);
				else:
					$this->editPurchaseInvoice($data['sales_id']);
				endif;
			endif;

			if($this->CMID == 1 AND $partyData->party_cm_id == 2):
				if(empty($data['sales_id']) && isset($result['insert_id'])):
					$this->sendPurchaseInvoice($result['insert_id']);
				else:
					$this->editPurchaseInvoice($data['sales_id']);
				endif;
			endif;

			$this->printJson($result);
			
		endif;
	}
	
	public function edit($id){
		$this->data['from_entry_type'] = 0;
		$this->data['ref_id'] = "";
		$this->data['invoiceData'] = $this->salesInvoice->getInvoice($id);
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['locationData'] = $this->store->getLocationList();
		$this->data['terms'] = $this->terms->getTermsListByType('Sales');   
		$this->data['invMaster'] = $this->party->getParty($this->data['invoiceData']->party_id);  
		$this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(2);
		$this->data['spAccounts'] = $this->ledger->getLedgerList(["'SA'"]); 
		$this->data['ledgerList'] = $this->ledger->getLedgerList(["'DT'","'ED'","'EI'","'ID'","'II'"]);
		$this->data['paymentMode'] = $this->paymentMode;

		$vaoucherData = $this->paymentVoucher->getReceiveVoucherByRefId($id); //print_r($vaoucherData); exit;
		if(!empty($vaoucherData)):
			$this->data['voucherData'] = $vaoucherData;
			$trans_mode=['"BA"'];
			if($this->data['voucherData']->trans_mode == 'CASH'){
				$trans_mode=['"CS"'];
			}
			$this->data['ledgerData'] = $this->party->getPartyListOnGroupCode($trans_mode);
		endif;
        $this->load->view($this->invoiceForm,$this->data);
	}
	
	public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.','field_error'=>0,'field_error_message'=>NULL]);
		else:
			$result = $this->salesInvoice->deleteInv($id);
			if($this->CMID == 2):
				$this->deletePurchaseInvoice($id);
			endif;
			$this->printJson($result);
		endif;
	}
	
	public function batchWiseItemStock(){
		$data = $this->input->post();
        $result = $this->challan->batchWiseItemStock($data);
        $this->printJson($result);
	}
	
	public function getInvoiceNo(){
		$type = $this->input->post('sales_type');
		if($type == "1"):
			if($this->CMID == 1):
				$trans_prefix = "WG/";
			else:
				$trans_prefix ="WGM/";
			endif;
			//$trans_prefix = $this->transModel->getTransPrefix(6);
        	$nextTransNo = $this->transModel->nextTransNo(6);
			$entry_type = 6;
		elseif($type == "2"):
			$trans_prefix = $this->transModel->getTransPrefix(8);
        	$nextTransNo = $this->transModel->nextTransNo(8);
			$entry_type = 8;
		elseif($type == "3"):
			$trans_prefix = $this->transModel->getTransPrefix(7);
        	$nextTransNo = $this->transModel->nextTransNo(7);
			$entry_type = 7;
		endif;
		$this->printJson(['status'=>1,'trans_prefix'=>$trans_prefix,'nextTransNo'=>$nextTransNo,'entry_type'=>$entry_type]);
	}
	
	public function getPartyItems(){
		$this->printJson($this->item->getPartyItems($this->input->post('party_id')));
	}
	public function invoiceThermalPrint()
	{
		$postData = $this->input->post();		
		$sales_id=$postData['sales_id'];
		if(empty($sales_id)){return '<h2>sorry...something goes wrong...!</h2>';}
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
		
		$itemList='';
		$itemList.='<div style="text-align:center;">
					<span style="font-size:0.8rem;"><b>'.$companyData->company_name.'</b></span><br>
					<span style="font-size:0.6rem;">'.$companyData->company_address.'</span><br>
				</div>';
		$custName = '';	
		if(!empty($salesData->party_alias)){$custName = '<tr><th colspan="2">Bill To : '.$salesData->party_alias.'</th></tr>';}
		$itemList.='<table style="font-size:0.6rem;text-align:left;width:100%;">
					<thead>
					    <tr>
							<td>GSTIN : '.$companyData->company_gst_no.'</td>
							<td style="width:120px;text-align:right"> MOBILE NO:'.$companyData->company_contact.'</td>
						</tr>
						<tr>
							<th>#'.$salesData->trans_prefix.$salesData->trans_no.'</th>
							<th style="width:120px;text-align:right">'.date('d/m/Y', strtotime($salesData->trans_date)).'</th>
						</tr>
						'.$custName.'
					</thead>
					</table>
					<table class="tbl-bordered" style="font-size:0.6rem;text-align:left;width:100%;">
						<thead> 
							<tr>
								<th style="width:10%;text-align:center;">Sr</th>
								<th style="width:50%;">Perticulars</th>
								<th style="width:10%;text-align:center;">Qty</th>
								<th style="width:15%;text-align:right;">Rate</th>
								<th style="width:15%;text-align:right;">Amount</th>
							</tr>
						</thead>
						<tbody>';

						$i=1; $totalQty=0; $totalAmt=0;
						foreach($salesData->itemData as $row){
							$itemList.='<tr>
								<td style="text-align:center;">'.$i++.'</td>
								<td style="">'.$row->item_name.'</td>
								<td style="text-align:center;">'.floatval($row->qty).'</td>
								<td style="text-align:right;">'.number_format($row->price,2).'</td>
								<td style="text-align:right;">'.number_format($row->amount,2).'</td>
							</tr>';
							$totalQty+=$row->qty; $totalAmt+=$row->amount;
						}
						$itemList.='
							<tr>
								<th colspan="2">Total</th>
								<th style="text-align:center;">'.$totalQty.'</th>
								<th></th>
								<th style="text-align:right;">'.$totalAmt.'</th>
							</tr>
							<tr>
								<th style="font-size:0.85rem; text-align:center;border-bottom:0px;" colspan="2" rowspan="3">'.$salesData->memo_type.' Memo</th>
								<td colspan="2" style="border-width: 1px 0px 0px 1px;">Discount</td> <td style="text-align:right;border-width: 1px 1px 0px 0px;">'.$salesData->disc_amount.'</th>
							</tr>
							<tr>
								<td colspan="2" style="border-width: 0px 0px 0px 1px;">CGST ('.floatval($salesData->cgst_per).'%)</td> <td style="text-align:right;border-width: 0px 1px 0px 0px;">'.$salesData->cgst_amount.'</td>
							</tr>
							<tr>
								<td colspan="2" style="border-width: 0px 0px 0px 1px;">SGST ('.floatval($salesData->sgst_per).'%)</td> <td style="text-align:right;border-width: 0px 1px 0px 0px;">'.$salesData->sgst_amount.'</td>
							</tr>
							<tr>
								<th style="font-size:0.85rem; text-align:center;border-top:0px;" colspan="2" rowspan="2">Duplicate</th>
								<td colspan="2" style="border-width: 0px 0px 0px 1px;">Round Off</td> <td style="text-align:right;border-width: 0px 1px 0px 0px;">'.$salesData->round_off_amount.'</td>
							</tr>
							<tr>
								<th colspan="2" style="border-width: 1px 0px 0px 1px;">Grand Total</th> <td style="text-align:right;border-width: 1px 1px 0px 0px;">'.$salesData->net_amount.'</th>
							</tr>
							<tr>
								<td colspan="5">'.numToWordEnglish($salesData->net_amount).'</td>
							</tr>
							<tr>
								<td colspan="4" style="border-width: 1px 0px 0px 1px;">
									<b>Terms & Condition:</b><br>
									1.Goods Once Sold Will Not Be Accepted & Changed.<br>
									2.Subject to RAJKOT Jurisdiction. E.&.O.E.<br>
									3.No Warranty,No Replacement.
								</td>
								<td style="text-align:center;border-width: 1px 1px 0px 0px;vertical-align:bottom;">Sign</td>
							</tr>
							<tr>
								<th colspan="5" style="font-size:0.8rem;text-align:center;border-width: 0px 1px 1px 1px;">*** Thank You ***</th>
							</tr>
						</tbody>
						</table>';


		$printData = $itemList.'<br><div style="page-break-after: always !important;"></div>';
		//echo $printData;exit;
		$this->printJson(['status'=>1,'printData'=>$printData]);
	}
	public function invoiceThermalPrintPdf($sales_id=0)
	{
	    if(empty($sales_id)){$postData = $this->input->post();		$sales_id=$postData['sales_id'];}
		if(empty($sales_id)){return '<h2>sorry...something goes wrong...!</h2>';}
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
		$custName = '';	
		if(!empty($salesData->party_alias)){$custName = '<tr><th colspan="2" class="text-left">Bill To : '.$salesData->party_alias.'</th></tr>';}
						
		$itemList='';
		$itemList.='<div style="text-align:center;">
					<span style="font-size:0.8rem;"><b>'.$companyData->company_name.'</b></span><br>
					<span style="font-size:0.55rem;">'.$companyData->company_address.'</span><br>
				</div>';
		$itemList.='<table style="font-size:0.6rem;text-align:left;width:100%;">
					<thead>
					    <tr>
							<td>GSTIN : '.$companyData->company_gst_no.'</td>
							<td style="width:120px;text-align:right"> MOBILE NO:'.$companyData->company_contact.'</td>
						</tr>
						<tr>
							<th style="text-align:left">#'.$salesData->trans_prefix.$salesData->trans_no.'</th>
							<th style="width:120px;text-align:right">'.date('d/m/Y', strtotime($salesData->trans_date)).'</th>
						</tr>
						'.$custName.'
					</thead>
					</table>
					<table class="tbl-bordered" style="font-size:0.55rem;text-align:left;width:100%;">
						<thead> 
							<tr>
								<th style="width:9%;text-align:center;">Sr</th>
								<th style="width:51%;">Perticulars</th>
								<th style="width:10%;text-align:center;">Qty</th>
								<th style="width:15%;text-align:right;">Rate</th>
								<th style="width:15%;text-align:right;">Amt</th>
							</tr>
						</thead>
						<tbody>';

						$i=1; $totalQty=0; $totalAmt=0;$countRow=0;$receiptHeight = 70;$itmGst=array();
						foreach($salesData->itemData as $row){
							$itemList.='<tr>
								<td style="text-align:center;">'.$i++.'</td>
								<td style="">'.$row->item_name.'</td>
								<td style="text-align:center;">'.floatval($row->qty).'</td>
								<td style="text-align:right;">'.number_format($row->price,2).'</td>
								<td style="text-align:right;">'.number_format($row->amount,2).'</td>
							</tr>';$countRow++;
							if(strlen($row->item_name) > 22){$countRow++;}if(strlen($row->item_name) > 44){$countRow++;}
							$totalQty+=$row->qty; $totalAmt+=$row->amount;
							$itmGst[] = $row->igst_per;
						}
						$amountWords = numToWordEnglish($salesData->net_amount);
						if(strlen($amountWords) > 45){$countRow++;}
						$cgstPer = $sgstPer = round((MAX($itmGst)/2),2);$igstPer = round(MAX($itmGst),2);
						$itemList.='
							<tr>
								<th colspan="2">Total</th>
								<th style="text-align:center;">'.$totalQty.'</th>
								<th></th>
								<th style="text-align:right;">'.$totalAmt.'</th>
							</tr>
							<tr>
								<th style="font-size:0.85rem; text-align:center;border-bottom:0px;" colspan="2" rowspan="3">'.$salesData->memo_type.' MEMO</th>
								<td colspan="2" style="border-width: 1px 0px 0px 1px;">'.((!empty($salesData->disc_amount) && $salesData->disc_amount > 0)? 'Discount</td> <td style="text-align:right;border-width: 1px 1px 0px 0px;">'.$salesData->disc_amount:'').'</th>
							</tr>
							<tr>
								<td colspan="2" style="border-width: 0px 0px 0px 1px;">CGST ('.floatval($cgstPer).'%)</td> <td style="text-align:right;border-width: 0px 1px 0px 0px;">'.$salesData->cgst_amount.'</td>
							</tr>
							<tr>
								<td colspan="2" style="border-width: 0px 0px 0px 1px;">SGST ('.floatval($sgstPer).'%)</td> <td style="text-align:right;border-width: 0px 1px 0px 0px;">'.$salesData->sgst_amount.'</td>
							</tr>
							<tr>
								<th style="font-size:0.85rem; text-align:center;border-top:0px;" colspan="2" rowspan="2">ORIGINAL</th>
								<td colspan="2" style="border-width: 0px 0px 0px 1px;">Round Off</td> <td style="text-align:right;border-width: 0px 1px 0px 0px;">'.$salesData->round_off_amount.'</td>
							</tr>
							<tr>
								<th colspan="2" style="border-width: 1px 0px 0px 1px;">Grand Total</th> <td style="text-align:right;border-width: 1px 1px 0px 0px;">'.$salesData->net_amount.'</th>
							</tr>
							<tr><td colspan="5">'.$amountWords.'</td></tr>
							<tr>
								<td colspan="4" style="border-width: 1px 0px 0px 1px;">
									<b>Terms & Condition:</b><br>
									1.Goods Once Sold Will Not Be Accepted & Changed.<br>
									2.Subject to RAJKOT Jurisdiction. E.&.O.E.<br>
									3.No Warranty,No Replacement.
								</td>
								<td style="text-align:center;border-width: 1px 1px 0px 0px;vertical-align:bottom;">Sign</td>
							</tr>
							<tr>
								<th colspan="5" style="font-size:0.8rem;text-align:center;border-width: 0px 1px 1px 1px;">*** Thank You ***</th>
							</tr>
						</tbody>
						</table>';
        $rowHeight = ($countRow < 11) ? ($countRow * 2) : ($countRow * 2.5);
        $rw = 70;
        $rh = 70 + $rowHeight;
        
        if($rh < $rw){$receiptWidth = $rh;$receiptHeight = $rw;}else{$receiptWidth = $rw;$receiptHeight = $rh;}
        
		$printData = $itemList;
		//echo $printData;exit;
		$pdfFileName=str_replace('/','-',($salesData->trans_prefix.$salesData->trans_no)).'_'.time().'.pdf';
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [$receiptWidth, $receiptHeight]]);
		$stylesheet = file_get_contents(base_url('assets/css/jp_helper.css?v='.time()));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		$mpdf->setTitle(str_replace('/','-',($salesData->trans_prefix.$salesData->trans_no)));
		$mpdf->AddPage('P','','','','',3,3,1,1,0,0);
		$mpdf->WriteHTML($printData);
		$printData1 = $mpdf->Output($pdfFileName,'I');
		//$this->printJson(['status'=>1,'printData'=>$printData]);
	}

	/**
	 *Updated By Mansee @ 29-12-2021 503,504,511,512
	 */
    public function invoice_pdf_old()
	{
		$postData = $this->input->post();
		$original=0;$duplicate=0;$triplicate=0;$header_footer=0;$extra_copy=0;
		if(isset($postData['original'])){$original=1;}
		if(isset($postData['duplicate'])){$duplicate=1;}
		if(isset($postData['triplicate'])){$triplicate=1;}
		if(isset($postData['header_footer'])){$header_footer=1;}
		if(!empty($postData['extra_copy'])){$extra_copy=$postData['extra_copy'];}
		
		$sales_id=$postData['printsid'];
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
						<th style="width:10%;">Rate<br><small>('.$partyData->currency.')</small></th>
						<th style="width:6%;">Disc.</th>
						<th style="width:8%;">GST</th>
						<th style="width:11%;">Amount<br><small>('.$partyData->currency.')</small></th>
					</tr></thead><tbody>';
		
		// Terms & Conditions
		
		$blankLines=10;if(!empty($header_footer)){$blankLines=10;}
		$terms = '<table class="table">';$t=0;$tc=new StdClass;		
		if(!empty($salesData->terms_conditions))
		{
			$tc=json_decode($salesData->terms_conditions);
			$blankLines=12 - count($tc);if(!empty($header_footer)){$blankLines=12 - count($tc);}
			foreach($tc as $trms):
				if($t==0):
					$terms .= '<tr>
									<th style="width:17%;font-size:12px;text-align:left;">'.$trms->term_title.'</th>
									<td style="width:48%;font-size:12px;">: '.$trms->condition.'</td>
									<th rowspan="'.count($tc).'" style="width:35%;vertical-align:bottom;text-align:center;font-size:1rem;padding:5px 2px;">
										For, '.$companyData->company_name.'<br>
										<!--<img src="'.$auth_sign.'" style="width:120px;">-->
									</th>
							</tr>';
				else:
					$terms .= '<tr>
									<th style="font-size:12px;text-align:left;">'.$trms->term_title.'</th>
									<td style="font-size:12px;">: '.$trms->condition.'</td>
							</tr>';
				endif;$t++;
			endforeach;
		}
		else
		{
			$tc = array();
			$terms .= '<tr>
							<td style="width:65%;font-size:12px;">Subject to RAJKOT Jurisdiction</td>
							<th rowspan="'.count($tc).'" style="width:35%;vertical-align:bottom;text-align:center;font-size:1rem;padding:5px 2px;">
								For, '.$companyData->company_name.'<br>
								<!--<img src="'.$auth_sign.'" style="width:120px;">-->
							</th>
					</tr>';
		}
		
		$terms .= '</table>';
		
		$subTotal=0;$lastPageItems = '';$pageCount = 0; $sgstAmt = 0; $cgstAmt=0; $taxableAmt=0;
		$i=1;$tamt=0;$cgst=9;$sgst=9;$cgst_amt=0;$sgst_amt=0;$netamt=0;$igst=0;$hsnCode='';$total_qty=0;$page_qty = 0;$page_amount = 0;
		$pageData = array();$totalPage = 0;
		$totalItems = count($salesData->itemData);
		
		$lpr = $blankLines ;$pr1 = $blankLines + 6 ;
		$pageRow = $pr = ($totalItems > $lpr) ? $pr1 : $totalItems;
		$lastPageRow = (($totalItems % $lpr)==0) ? $lpr : ($totalItems % $lpr);
		$remainRow = $totalItems - $lastPageRow;
		$pageSection = round(($remainRow/$pageRow),2);
		$totalPage = (numberOfDecimals($pageSection)==0)? (int)$pageSection : (int)$pageSection + 1;
		for($x=0;$x<=$totalPage;$x++)
		{
			$page_qty = 0;$page_amount = 0;
			$pageItems = '';$pr = ($x==$totalPage) ? $totalItems - ($i-1) : $pr;
			$tempData = $this->salesInvoice->salesTransactions($sales_id,$pr.','.$pageCount);
			if(!empty($tempData))
			{
				foreach ($tempData as $row)
				{
					$pageItems.='<tr>';
						$pageItems.='<td class="text-center" height="37">'.$i.'</td>';
						$pageItems.='<td class="text-left">'.$row->item_name.'</td>';
						$pageItems.='<td class="text-center">'.$row->hsn_code.'</td>';
						$pageItems.='<td class="text-center">'.sprintf('%0.2f', $row->qty).'</td>';
						$pageItems.='<td class="text-right">'.sprintf('%0.2f', $row->price).'</td>';
						$pageItems.='<td class="text-center">'.floatval($row->disc_per).'</td>';
						$pageItems.='<td class="text-center">'.floatval($row->igst_per).'%</td>';
						$pageItems.='<td class="text-right">'.sprintf('%0.2f', $row->amount).'</td>';
					$pageItems.='</tr>';
					
					$total_qty += $row->qty;$page_qty += $row->qty;$page_amount += $row->amount; 
					if($this->CMID == 1):
						$subTotal += $row->amount - (($row->amount*18)/100); 
						$cgstAmt += ($row->amount*9)/100;
						$sgstAmt += ($row->amount*9)/100;
						$taxableAmt= $row->amount - (($row->amount*18)/100);

					else:
						$subTotal += $row->amount ;
						$cgstAmt += $salesData->sgst_amount + round(($salesData->freight_gst / 2),2);
						$sgstAmt += $salesData->cgst_amount + round(($salesData->freight_gst / 2),2);
						$taxableAmt= $subTotal + $salesData->freight_amount;
					endif;
					$i++;
				}
			}
			if($x==$totalPage)
			{
				$pageData[$x]= '';
				$lastPageItems = $pageItems;
			}
			else
			{
				/*$pageItems.='<tr>';
					$pageItems.='<th class="text-right" style="border:1px solid #000;" colspan="5">Page Total</th>';
					$pageItems.='<th class="text-right" style="border-top:1px solid #000000;">'.sprintf('%0.3f', $page_qty).'</th>';
					$pageItems.='<th class="text-right" style="border-top:1px solid #000000;"></th>';
					$pageItems.='<th class="text-right" style="border-top:1px solid #000000;">'.sprintf('%0.2f', $page_amount).'</th>';
				$pageItems.='</tr>';*/
				$pageData[$x]=$itemList.$pageItems.'</tbody></table><div class="text-right"><i>Continue to Next Page</i></div>';
			}
			$pageCount += $pageRow;
		}
		//$taxableAmt= $subTotal + $salesData->freight_amount;
		$fgst = round(($salesData->freight_gst / 2),2);
		$rwspan= 4;
		
		$gstRow='<tr>';
			$gstRow.='<td colspan="3" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">CGST</td>';
			$gstRow.='<td class="text-right" style="border-top:0px !important;">'.sprintf('%0.2f', $cgstAmt).'</td>';
		$gstRow.='</tr>';
		
		$gstRow.='<tr>';
			$gstRow.='<td colspan="3" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">SGST</td>';
			$gstRow.='<td class="text-right" style="border-top:0px !important;">'.sprintf('%0.2f', $sgstAmt).'</td>';
		$gstRow.='</tr>';
		
		$party_gstin = (!empty($salesData->party_state_code)) ? explode('#',$salesData->party_state_code)[0] : '';
		$party_stateCode = (!empty($salesData->party_state_code)) ? explode('#',$salesData->party_state_code)[1] : '';
		
		if(!empty($party_gstin))
		{
			if($party_stateCode!="24")
			{
				$gstRow='<tr>';
					$gstRow.='<td colspan="3" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">IGST</td>';
					$gstRow.='<td class="text-right" style="border-top:0px !important;">'.sprintf('%0.2f', ($salesData->cgst_amount + $salesData->sgst_amount + $salesData->freight_gst)).'</td>';
				$gstRow.='</tr>';$rwspan= 3;
			}
		}
		$totalCols = 9;
		$itemList .= $lastPageItems;
		if($i<$blankLines)
		{
			for($z=$i;$z<=$blankLines;$z++)
			{$itemList.='<tr><td  height="37">&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';}
		}
		
		$itemList.='<tr>';
			$itemList.='<td colspan="3" class="text-right" style="vartical-align:top;border-top:1px solid #000;border-right:1px solid #000;"><b>Total Qty</b></td>';
			$itemList.='<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;">'.sprintf('%0.2f', $total_qty).'</th>';
			$itemList.='<th colspan="3" class="text-right" style="border:1px solid #000;border-left:0px solid #000;">Sub Total</th>';
			$itemList.='<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;">'.sprintf('%0.2f', $subTotal).'</th>';
		$itemList.='</tr>';
		
		$itemList.='<tr>';
			$itemList.='<td colspan="4" rowspan="'.$rwspan.'" class="text-left" style="vartical-align:top;border-top:1px solid #000;border-right:1px solid #000;"><b>Bank Name : </b>'.$companyData->company_bank_name.'<br>
			<b>A/c. No. : </b>'.$companyData->company_acc_no.'<br>
			<b>IFSC Code : </b>'.$companyData->company_ifsc_code.'
			</td>';
			$itemList.='<td colspan="3" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">P & F</td>';
			$itemList.='<td class="text-right" style="border-top:0px !important;">'.sprintf('%0.2f', $salesData->freight_amount).'</td>';
		$itemList.='</tr>';
		
		$itemList.='<tr>';
			$itemList.='<th colspan="3" class="text-right" style="border:1px solid #000;border-left:0px solid #000;">Taxable Amount</th>';
			$itemList.='<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;">'.sprintf('%0.2f', $taxableAmt).'</th>';
		$itemList.='</tr>';
		
		$itemList.=$gstRow;
		
		$itemList.='<tr>';
			$itemList.='<td colspan="4" rowspan="2" class="text-left" style="vartical-align:top;border:1px solid #000;border-left:0px;"><i><b>Bill Amount In Words ('.$partyData->currency.') : </b>'.numToWordEnglish($salesData->net_amount).'</i></td>';
			$itemList.='<td colspan="3" class="text-right" style="border-right:1px solid #000;">Round Off</td>';
			$itemList.='<td class="text-right" style="border-top:0px !important;border-left:0px;">'.sprintf('%0.2f', $salesData->round_off_amount).'</td>';
		$itemList.='</tr>';
		
		$itemList.='<tr>';
			$itemList.='<th colspan="3" class="text-right" style="border-top:1px solid #000;border-right:1px solid #000;font-size:14px;">Payable Amount</th>';
			$itemList.='<th class="text-right" height="40" style="border-top:1px solid #000;border-left:0px;font-size:14px;">'.sprintf('%0.2f', $salesData->net_amount).'</th>';
		$itemList.='</tr>';
		$itemList.='<tbody></table>';
		
		$pageData[$totalPage] .= $itemList;
		$pageData[$totalPage] .= '<br><b><u>Terms & Conditions : </u></b><br>'.$terms.'';
		
		$invoiceType=array();
		$invType = array("ORIGINAL","DUPLICATE","TRIPLICATE","EXTRA COPY");$i=0;
		foreach($invType as $it)
		{
			$invoiceType[$i++]='<table style="margin-bottom:5px;">
									<tr>
										<th style="width:35%;letter-spacing:2px;" class="text-left fs-17" >GSTIN: '.$companyData->company_gst_no.'</th>
										<th style="width:30%;letter-spacing:2px;" class="text-center fs-17">TAX INVOICE</th>
										<th style="width:35%;letter-spacing:2px;" class="text-right">'.$it.'</th>
									</tr>
								</table>';
		}
		$gstJson=json_decode($partyData->json_data);
		$partyAddress=(!empty($gstJson->{$salesData->gstin})?$gstJson->{$salesData->gstin}:'');
		$baseDetail='<table class="poTopTable" style="margin-bottom:5px;">
						<tr>
							<td style="width:55%;" rowspan="3">
								<table>
									<tr><td style="vartical-align:top;"><b>BILL TO</b></td></tr>
									<tr><td style="vertical-align:top;"><b>'.$salesData->party_name.'</b></td></tr>
									<tr><td class="text-left" style="">'.(!empty($partyData->party_address)?$partyData->party_address:'').'</td></tr>
									<tr><td class="text-left" style=""><b>GSTIN : '.$salesData->gstin.'</b></td></tr>
								</table>
							</td>
							<td style="width:25%;border-bottom:1px solid #000000;border-right:0px;padding:2px;">
								<b>Invoice No. : '.$salesData->trans_prefix.$salesData->trans_no.'</b>
							</td>
							<td style="width:20%;border-bottom:1px solid #000000;border-left:0px;text-align:right;padding:2px 5px;">
								<b>Date : '.date('d/m/Y', strtotime($salesData->trans_date)).'</b>
							</td>
						</tr>
						<tr>
							<td style="width:45%;" colspan="2">
								<table>
									<tr><td style="vertical-align:top;"><b>P.O. No.</b></td><td>: '.$salesData->doc_no.'</td></tr>
									<!-- <tr><td style="vertical-align:top;"><b>Challan No</b></td><td>: '.$salesData->challan_no.'</td></tr>-->
									<tr><td style="vertical-align:top;"><b>Transport</b></td><td>: '.$salesData->transport_name.'</td></tr>
								</table>
							</td>
						</tr>
					</table>';
				
		$orsp='';$drsp='';$trsp='';
		$htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
						<tr>
							<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:100%">'.$companyData->company_name.'</td>
						</tr>
					</table>
					<table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
						<tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
					</table>';
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:25%;font-size:12px;">INV No. & Date : '.$salesData->trans_prefix.$salesData->trans_no.'-'.formatDate($salesData->trans_date).'</td>
							<td style="width:25%;font-size:12px;"></td>
							<td style="width:25%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		
		$mpdf = $this->m_pdf->load();
		$i=1;$p='P';
		$pdfFileName=base_url('assets/uploads/sales/sales_invoice_'.$sales_id.'.pdf');
		$fpath='/assets/uploads/sales/sales_invoice_'.$sales_id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/bill_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		
		if(!empty($header_footer))
		{
			$mpdf->SetWatermarkImage($logo,0.05,array(120,60));
			$mpdf->showWatermarkImage = true;
			$mpdf->SetHTMLHeader($htmlHeader);
			$mpdf->SetHTMLFooter($htmlFooter);
		}
		
		if(!empty($original))
		{
			foreach($pageData as $pg)
			{
				if(!empty($header_footer))
				{
					$mpdf->AddPage('P','','','','',7,7,38,7,4,6);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv1">'.$invoiceType[0].$baseDetail.$pg.'</div></div>');
				}
				else
				{
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[0].$baseDetail.$pg.'</div></div>');
				}
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
		
		// $mpdf->Output(FCPATH.$fpath,'F');
		
		$mpdf->Output($pdfFileName,'I');
	}

	public function invoice_pdf()
	{
		$postData = $this->input->post();
		$original=0;$duplicate=0;$triplicate=0;$header_footer=0;$extra_copy=0;
		if(isset($postData['original'])){$original=1;}
		if(isset($postData['duplicate'])){$duplicate=1;}
		if(isset($postData['triplicate'])){$triplicate=1;}
		if(isset($postData['header_footer'])){$header_footer=1;}
		if(!empty($postData['extra_copy'])){$extra_copy=$postData['extra_copy'];}
		
		$sales_id=$postData['printsid'];
		$salesData = $this->salesInvoice->getInvoice($sales_id);
		$companyData = $this->salesInvoice->getCompanyInfo();
		
		$partyData = $this->party->getParty($salesData->party_id);
		
		$response="";
		$letter_head=base_url('assets/images/letterhead_top.png');
		
		$currencyCode = "INR";
		$symbol = "";
		
		$response="";$inrSymbol=base_url('assets/images/inr.png');
		//$headerImg = base_url('assets/images/rtth_lh_header.png');
		//$footerImg = base_url('assets/images/rtth_lh_footer.png');
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
		$terms .= $terms1.'</td></tr></table>';
		
		$subTotal=0;$lastPageItems = '';$pageCount = 0; $sgstAmt = 0; $cgstAmt=0; $igstAmt=0; $taxableAmt=0;
		$i=1;$tamt=0;$cgst=9;$sgst=9;$cgst_amt=0;$sgst_amt=0;$netamt=0;$igst=0;$hsnCode='';$total_qty=0;$page_qty = 0;$page_amount = 0;
		$pageData = array();
		
		$itmLine=26;if(!empty($header_footer)){$itmLine=26;}
		$orderData = $this->salesInvoice->salesTransactions($sales_id);
		//print_r($orderData);exit;
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
						$pageItems.='<td class="text-center">'.sprintf('%0.2f', $row->qty).' ('.$row->unit_name.')</td>';
						$pageItems.='<td class="text-right">'.sprintf('%0.2f', $row->price).'</td>';
						$pageItems.='<td class="text-center">'.floatval($row->gst_per).'</td>';
						$pageItems.='<td class="text-right">'.sprintf('%0.2f', ($row->amount+$row->disc_amount)).'</td>';
					$pageItems.='</tr>';
					
					$total_qty += $row->qty;$page_qty += $row->qty;$page_amount += $row->amount; 
					if($this->CMID == 1):
						$subTotal += $row->amount; 
						$cgstAmt += $row->cgst_amount;
						$sgstAmt += $row->sgst_amount;
						$igstAmt += $row->igst_amount;
						$taxableAmt+= $row->taxable_amount;
					else:
						$subTotal += ($row->amount - $row->disc_amount);
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
		$rwspan= 4; $cgstPer = $sgstPer = round((MAX($itmGst)/2),2);$igstPer = round(MAX($itmGst),2);
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
		
		$beforExp = ""; $afterExp ="";
		$expenseList = $this->expenseMaster->getActiveExpenseList(2);
		$taxList = $this->taxMaster->getActiveTaxList(2);
		$invExpenseData = (!empty($salesData->expenseData)) ? $salesData->expenseData : array();
		$rowCount = 1; $totalExpAmt=0;
		foreach ($expenseList as $row) {
			$expAmt = 0; 
			$amtFiledName = $row->map_code . "_amount";
			$perFiledName = $row->map_code . "_per";
			if (!empty($invExpenseData) && $row->map_code != "roff") :
				$expAmt = $invExpenseData->{$amtFiledName};
				$expPer = $invExpenseData->{$perFiledName};
			endif;
			if ($expAmt <> 0) {
				if ($row->position == 1) {
					$beforExp .= '<tr><td colspan="2" class="text-right" style="border-top:1px solid #000;border-right:1px solid #000;">' . $row->exp_name.'</td>
								<td class="text-right" style="border-top:1px solid #000;border-left:0px solid #000;">' . $expAmt . ' </td>';
								
				    $totalExpAmt += $expAmt;
				}else{
                    $afterExp .= '<tr>
                        <td colspan="2" class="text-right">'.$row->exp_name.'</td>
                        <td class="text-right">'.sprintf('%.2f',$expAmt).'</td>
                    </tr>';
				}
				$rowCount++;
			}
		} 
		/*if ($salesData->disc_amount > 0) {
			$beforExp .= '<td colspan="2" class="text-right" style="border-top:1px solid #000;border-right:1px solid #000;">Discount</td>
			<td class="text-right" style="border-top:1px solid #000;border-left:0px solid #000;">' . $salesData->disc_amount . ' </td>';
		}*/
		
		$itemList.='<tr>';
			$itemList.='<td colspan="3" class="text-right" style="vartical-align:top;border-top:1px solid #000;border-right:1px solid #000;"><b>Total Qty</b></td>';
			$itemList.='<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;">'.sprintf('%0.2f', $total_qty).'</th>';
			$itemList.='<th colspan="2" class="text-right" style="border:1px solid #000;border-left:0px solid #000;">Sub Total</th>';
			$itemList.='<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;">'.sprintf('%0.2f', $salesData->total_amount).'</th>';
		$itemList.='</tr>';
		
		$itemList.='<tr>';
			$itemList.='<td colspan="4" rowspan="'.($rwspan+($rowCount-1)).'" class="text-left" style="vartical-align:top;border-top:1px solid #000;border-right:1px solid #000;"><b>Bank Name : </b>'.$companyData->company_bank_name.'<br>
    			<b>A/c. No. : </b>'.$companyData->company_acc_no.'<br>
    			<b>IFSC Code : </b>'.$companyData->company_ifsc_code.'
    			</td>';
			$itemList.= $beforExp;
		$itemList.='</tr>';
		
		$itemList.='<tr>';
			$itemList.='<th colspan="2" class="text-right" style="border:1px solid #000;border-left:0px solid #000;">Taxable Amount</th>';
			$itemList.='<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;">'.sprintf('%0.2f', ($salesData->taxable_amount - abs($totalExpAmt))).'</th>';
		$itemList.='</tr>';
		//$itemList.= $beforExp;
		$itemList.=$gstRow.$afterExp;
		
		$itemList.='<tr>';
			$itemList.='<td colspan="4" rowspan="2" class="text-left" style="vartical-align:top;border:1px solid #000;border-left:0px;">
				<i><b>Total GST : </b>'.numToWordEnglish($gstAmount).'</i><br>
				<i><b>Bill Amount : </b>'.numToWordEnglish($salesData->net_amount).'</i><br>
				
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
									<th style="width:40%;letter-spacing:2px;border:0px;font-size:15px;" class="text-left" >GSTIN:'.$companyData->company_gst_no.'</th>
									<th style="width:20%;letter-spacing:2px;border:0px;font-size:15px;" class="text-center">TAX INVOICE</th>
									<th style="width:40%;letter-spacing:2px;border:0px;font-size:15px;" class="text-right">'.$it.'</th>
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
									<tr><td class="text-left" style="">'.(!empty($partyData->party_address)?$partyData->party_address:'').'<br>'.(!empty($partyData->party_phone)?'<b>Phone No:</b> '.$partyData->party_phone:'').'</td></tr>
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
							<td style="border-left:0px;">: '.(!empty($partyData->city_name)?$partyData->city_name:'').'</td>
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
		$pdfFileName=base_url('assets/uploads/sales/sales_invoice_'.$sales_id.'.pdf');
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
		
		// $mpdf->Output(FCPATH.$fpath,'F');
		
		$mpdf->Output($pdfFileName,'I');
	}
	
	public function getItemList(){
        $this->printJson($this->salesInvoice->getItemList($this->input->post('id')));
    }

	public function getLedgerListONPaymentMode()
	{
		$data=$this->input->post();
		$trans_mode=['"BA"'];
		if($data['payment_mode']=='CASH')
		{
			$trans_mode=['"CS"'];
		}
		$ledgerData=$this->party->getPartyListOnGroupCode($trans_mode);
		// print_r($this->input->post());exit;
		$html='<option value="">Select Leader</option>';
		if(!empty($ledgerData))
		{
			foreach ($ledgerData as $row) :
				$html.='<option value="' . $row->id . '">' . $row->party_name . '</option>';
			endforeach;
												
		}
		$this->printJson(['status'=>1,'options'=>$html]);
	}
	
	//Created By Karmi @31/03/2022
	public function getOfferItems(){
		$data = $this->input->post();
		$data['items']= json_decode($data['myJsonItem']);
		$this->printJson($this->offers->getOfferItems($data));
	}
	
	public function sendPurchaseInvoice($id){
		$invoiceData = $this->salesInvoice->getInvoice($id);

		$partyData = $this->db->select('party_master.*,currency.inrrate')->join('currency','currency.currency = party_master.currency','left')->where('party_master.is_delete',0)->where('party_cm_id',$this->CMID)->get('party_master')->row();
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

			$roff_acc =  $this->db->where('party_category',4)->where('cm_id',$partyData->cm_id)->where('is_delete',0)->where_in('system_code','ROFFACC')->get('party_master')->row();//ROFFACC
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
				'memo_type' => $invoiceData->memo_type,
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
	
	public function editPurchaseInvoice($id){
		$invoiceData = $this->salesInvoice->getInvoice($id);

		$purchaseInvoiceData = $this->db->where('ref_id',$id)->where('trans_status',99)->where('entry_type',12)->where('is_delete',0)->get('trans_main')->row();
		if(!empty($purchaseInvoiceData)):
			
			$partyData = $this->db->where('id',$purchaseInvoiceData->party_id)->get('party_master')->row();

			$purchaseItems = $this->db->where('is_delete',0)->where('trans_main_id',$purchaseInvoiceData->id)->get('trans_child')->result();

			$spAccounts = $this->db->where('party_category',4)->where('cm_id',$partyData->cm_id)->where('is_delete',0)->where_in('group_code',['PA'])->get('party_master')->result();
			
			$ledgerList = $this->db->where('party_category',4)->where('cm_id',$partyData->cm_id)->where('is_delete',0)->where_in('group_code',["DT", "ED", "EI", "ID", "II"])->get('party_master')->result();
			
			$sp_acc_key = array_search("PURGSTACC",array_column($spAccounts,'system_code'));
			$sp_acc_id = $spAccounts[$sp_acc_key]->id;

			$igst_acc_key = array_search("IGSTIPACC",array_column($ledgerList,'system_code'));
			$igst_acc_id = $ledgerList[$igst_acc_key]->id;

			$cgst_acc_key = array_search("CGSTIPACC",array_column($ledgerList,'system_code'));
			$cgst_acc_id = $ledgerList[$cgst_acc_key]->id;

			$sgst_acc_key = array_search("SGSTIPACC",array_column($ledgerList,'system_code'));
			$sgst_acc_id = $ledgerList[$sgst_acc_key]->id;			

			$masterData = [
				'id' => $purchaseInvoiceData->id,
				'entry_type' => 12,
				'from_entry_type' => 0,
				'ref_id' => $id,
				'trans_status' => 99,
				'trans_no' => $purchaseInvoiceData->trans_no,
				'trans_prefix' => $purchaseInvoiceData->trans_prefix,
				'trans_number' => $purchaseInvoiceData->trans_number,
				'trans_date' => date('Y-m-d', strtotime($invoiceData->trans_date)),
				'memo_type' => $invoiceData->memo_type,
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
				'round_off_acc_id'  => 0,
				'round_off_amount' => $invoiceData->round_off_amount,
				'net_amount' => $invoiceData->net_amount,
				'terms_conditions' => $invoiceData->terms_conditions,
				'remark' => $invoiceData->remark,
				'currency' => $purchaseInvoiceData->currency,
				'inrrate' => $purchaseInvoiceData->inrrate,
				'vou_name_s' => getVoucherNameShort(12),
				'vou_name_l' => getVoucherNameLong(12),
				'ledger_eff' => 1,
				'created_by' => 0,
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
			//$this->sendPurchaseInvoice($id);
			return false;
		endif;
	}

	public function deletePurchaseInvoice($id){		
		$this->db->where('ref_id',$id);
		$this->db->where('is_delete',0);
		$this->db->where('trans_status',99);
		$invData = $this->db->get('trans_main')->row();

		if(!empty($invData)):
			$transData = $this->db->where('trans_main_id',$invData->id)->where('is_delete',0)->get('trans_child')->result();

			foreach($transData as $row):
				if($row->stock_eff == 1):
					$this->db->where('id',$row->item_id);
					$this->db->set('qty',"`qty` - ".$row->qty,false);
					$this->db->update('item_master');

					$this->db->where('ref_id',$invData->id);
					$this->db->where('trans_ref_id',$row->id);
					$this->db->where('trans_type',1);
					$this->db->where('ref_type',2);
					$this->db->delete('stock_transaction');
				endif;

				$this->db->where('id',$row->id);
				$this->db->update('trans_child',['is_delete'=>1]);
			endforeach;

			$this->db->where('id',$invData->id);
			$this->db->update('trans_main',['is_delete'=>1]);

			$this->transModel->deleteLedgerTrans($invData->id,$invData->cm_id);
            $this->transModel->deleteExpenseTrans($invData->id);

			return true;
		else:
			return false;
		endif;
	}
	
	//Created By Karmi @20/06/2022
	public function createExcel($id){
		$salesData = $this->salesInvoice->getInvoice($id); 
		$companyData = $this->salesInvoice->getCompanyInfo();
		
		$partyData = $this->party->getParty($salesData->party_id); 
		$table_header1 = array('World Gift');
		$table_column = array('#', 'Item Name', 'Qty','AMT','T.AMT','MRP');
		$spreadsheet = new Spreadsheet();
		$inspSheet = $spreadsheet->getActiveSheet();
		$inspSheet = $inspSheet->setTitle('SALES INVOICE SHEET');
		$styleArray = [
			'font' => [
				'bold' => true,
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
			],
		];
		$spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
		
		//ROW 1
		$xlCol1 = 'A';
		$rows = 1;
		$isk_name = $spreadsheet->getActiveSheet()->mergeCells('A1:C1');
		$inspSheet->setCellValue($xlCol1 . 1,'WORLD GIFT');
		$spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
		$isk_name = $spreadsheet->getActiveSheet()->mergeCells('D1:E1');
		$inspSheet->setCellValue('D'. 1,'SALES INVOICE');
		$spreadsheet->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);


		//ROW 2
		$inspSheet->setCellValue('A' . 2, ".");
		$spreadsheet->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);
		$inspSheet->setCellValue('B' . 2, getPrefixNumber($salesData->trans_prefix,$salesData->trans_no));		
		$inspSheet->setCellValue('C' . 2, date('d/m/Y', strtotime($salesData->trans_date)));
		$isk_name = $spreadsheet->getActiveSheet()->mergeCells('D2:E2');
		$inspSheet->setCellValue('D' . 2, $partyData->party_name);
		
	
		
		//ROW 3
		$xlCol1 = 'A';
		$rows = 3;
		$isk_name = $spreadsheet->getActiveSheet()->mergeCells('A3:E3');
		$inspSheet->setCellValue('A' . 3, "Item Details:");
		$spreadsheet->getActiveSheet()->getStyle('A3')->applyFromArray($styleArray);
		
		//ROW 4
		$xlCol = 'A';
		$rows = 4;
		foreach ($table_column as $tCols) {
			$inspSheet->setCellValue($xlCol . $rows, $tCols);
			$spreadsheet->getActiveSheet()->getStyle($xlCol . $rows)->applyFromArray($styleArray);
			$xlCol++;
		}

		//Row 5
		$xlCol = 'A';
		$rows = 5;
		$i = 1; $totalQty = 0; $totalAmt = 0;
		foreach ($salesData->itemData as $row) {
            $inspSheet->setCellValue('A' . $rows, $i++);
            $inspSheet->setCellValue('B' . $rows, $row->item_name);            
            $inspSheet->setCellValue('C' . $rows, $row->qty);
            $inspSheet->setCellValue('D' . $rows, $row->amount);
            $inspSheet->setCellValue('E' . $rows, $row->net_amount);
            $inspSheet->setCellValue('F' . $rows, $row->mrp);
            $rows++; $totalQty += $row->qty; $totalAmt += $row->amount;  $totalAmt += $row->amount;
        }
		
		//ROW 6	
		$inspSheet->setCellValue('B' . $rows, "Total Qty:");
		$spreadsheet->getActiveSheet()->getStyle('B' . $rows)->applyFromArray($styleArray);
		$inspSheet->setCellValue('C' . $rows,$totalQty) ;
		//$inspSheet->setCellValue('H' . $rows,$totalAmt) ;
		
	
		//ROW 7	
		$rows++;
		$inspSheet->setCellValue('D' . $rows, "Total");
		$spreadsheet->getActiveSheet()->getStyle('D' . $rows)->applyFromArray($styleArray);
		$inspSheet->setCellValue('E' . $rows,$totalAmt) ;

		//ROW 8	
		$rows++;
		$inspSheet->setCellValue('D' . $rows, "DISC.AMT");
		$spreadsheet->getActiveSheet()->getStyle('D' . $rows)->applyFromArray($styleArray);
		$inspSheet->setCellValue('E' . $rows,$salesData->disc_amount);

		//ROW 9
		$rows++;
		$inspSheet->setCellValue('D' . $rows, "GST AMT");
		$spreadsheet->getActiveSheet()->getStyle('D' . $rows)->applyFromArray($styleArray);
		$inspSheet->setCellValue('E' . $rows,$salesData->gst_amount) ;

		//ROW 10
		$rows++;
		$inspSheet->setCellValue('D' . $rows, "PACKING");
		$spreadsheet->getActiveSheet()->getStyle('D' . $rows)->applyFromArray($styleArray);
		$inspSheet->setCellValue('E' . $rows,$salesData->packing_amount) ;

		//ROW 11
		$rows++;
		$inspSheet->setCellValue('D' . $rows, "TOTAL");
		$spreadsheet->getActiveSheet()->getStyle('D' . $rows)->applyFromArray($styleArray);
		$inspSheet->setCellValue('E' . $rows,$salesData->net_amount) ;

		//ROW 12
		$rows++;
		$inspSheet->setCellValue('D' . $rows, "CHQ AMT");
		$spreadsheet->getActiveSheet()->getStyle('D' . $rows)->applyFromArray($styleArray);
		$inspSheet->setCellValue('E' . $rows,"") ;

		//ROW 12
		$rows++;
		$inspSheet->setCellValue('D' . $rows, "CHECK AMT");
		$spreadsheet->getActiveSheet()->getStyle('D' . $rows)->applyFromArray($styleArray);
		$inspSheet->setCellValue('E' . $rows,$salesData->net_amount) ;
		
		$fileDirectory = realpath(APPPATH . '../assets/uploads/sInvExcel');
		$fileName = '/sales_invoice_'.$salesData->trans_no.'_sheet_' . time() . '.xlsx';
		$writer = new Xlsx($spreadsheet);

		$writer->save($fileDirectory . $fileName);
		header("Content-Type: application/vnd.ms-excel");
		redirect(base_url('assets/uploads/sInvExcel') . $fileName);
	}
	
	// Created By Meghavi 09/07/2022
	public function auditStatus(){
		$data = $this->input->post();
		// print_r($data);exit;
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->salesInvoice->auditStatus($data));
		endif;
	}

    // Created By Meghavi @12/06/2023
	public function salesInvoice_pdf()
	{
		$companyData = $this->salesInvoice->getCompanyInfo();
		$letter_head=base_url('assets/images/letterhead_top.png');
		$symbol = "";
		$inrSymbol=base_url('assets/images/inr.png');
		$headerImg = base_url('assets/images/rtth_lh_header.png');
		$footerImg = base_url('assets/images/rtth_lh_footer.png');
		$logoFile=(!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
		$logo=base_url('assets/images/'.$logoFile);
		$auth_sign=base_url('assets/images/rtth_sign.png');

		$blankLines = 20;$pageItems = '';
		$lastPageItems = $pageItems;
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
	
		$totalCols = 8; $i=1;
		$itemList .= $lastPageItems;
		if($i<$blankLines)
		{
			for($z=$i;$z<=$blankLines;$z++)
			{$itemList.='<tr><td height="40">&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';}
		}
				
		{$itemList.='<tr><td height="26">&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr></tbody></table>';}
		
		$baseDetail='<table class="poTopTable" style="margin-bottom:5px;">
						<tr>
							<td style="width:65%;" rowspan="3">
								<table>
									<tr><td style="vertical-align:top;"><b>M/s. : </b></td></tr>
									<tr><td class="text-left" style=""><br><b>Phone No:</b> </td></tr>
									<tr><td class="text-left" style=""><b>GSTIN :</b></td></tr>
								</table>
							</td>
							<td style="width:14%;border-right:0px;"><b>Invoice No.</b></td>
							<td style="width:21%;border-left:0px;">:</td>
						</tr>
						<tr>
							<td style="border-right:0px;"><b>Date : </b></td>
							<td style="border-left:0px;">: </td>
						</tr>
						<tr>
							<td style="border-right:0px;"><b>Supply of Place</b></td>
							<td style="border-left:0px;">: </td>
						</tr>
					</table>';
				
		$invoiceType ='<table class="topTable">
						
					</table>';
					
		$htmlHeader = '<table class="topTable">
						<tr>
							<th colspan="3" class="org_title text-uppercase text-center bg-light" style="font-size:1.2rem;">'.$companyData->company_name.'</th>
						</tr>
						<tr><td colspan="3" class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.' | Mobile: '.$companyData->company_contact.'</td></tr>
						<tr>
							<th style="width:35%;letter-spacing:2px;border:0px;font-size:15px;" class="text-left" >GSTIN: '.$companyData->company_gst_no.'</th>
							<th style="width:30%;letter-spacing:2px;border:0px;font-size:15px;" class="text-center">TAX INVOICE</th>
							<th style="width:35%;letter-spacing:2px;border:0px;font-size:15px;" class="text-right"></th>
						</tr>
					</table>';
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:25%;font-size:12px;">INV No. & Date : </td>
							<td style="width:25%;font-size:12px;"></td>
							<td style="width:25%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		$pdfData =$invoiceType.$baseDetail.$itemList;
	
		$mpdf = new \Mpdf\Mpdf();
		$i=1;$p='P';
		$pdfFileName=base_url('assets/uploads/sales/sales_invoice_'.'.pdf');
		$fpath='/assets/uploads/sales/sales_invoice_'.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/bill_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');

		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',7,7,28,7,5,6);
		$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv1">'.$pdfData.'</div></div>');
		
		$mpdf->Output($pdfFileName,'I');
	}
}
?>