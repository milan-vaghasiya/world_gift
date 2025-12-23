<?php
class TransactionMainModel extends MasterModel{
    private $transMain = "trans_main";
    private $transChild = "trans_child";
    private $paymentModeEffect = "payment_mode_effect";

    public function nextTransNo($entry_type,$memo_type=""){
        if(empty($memo_type)) {$memo_type = ($this->CMID == 1) ? "CASH" : "DEBIT";}
        $data['tableName'] = $this->transMain;
        $data['select'] = "MAX(trans_no) as trans_no";
        $data['where']['entry_type'] = $entry_type;
        if($entry_type == 6)
        {
            if($this->CMID == 1):
                if($memo_type == "CASH"){$data['where']['trans_prefix'] = "WGM/";}else{$data['where']['trans_prefix'] = "WG/";}
            elseif($this->CMID == 2):
                if($memo_type == "CASH"){$data['where']['trans_prefix'] = "RJM/";}else{$data['where']['trans_prefix'] = "WGM/";}
            endif;
        }
        $data['where']['trans_main.trans_date >='] = $this->startYearDate;
        $data['where']['trans_main.trans_date <='] = $this->endYearDate;
		$trans_no = $this->specificRow($data)->trans_no;
		//print_r($this->db->last_query());exit;
		$nextTransNo = (!empty($trans_no))?($trans_no + 1):1;
		return $nextTransNo;
    }
	
    public function getTransPrefix($entry_type){
		$prefix = 'TRANS/';
        switch($entry_type)
		{
			case 1: $prefix = 'SE/';break;
			case 2: $prefix = 'SQ/';break;
			case 3: $prefix = 'SQR/';break;
			case 4: $prefix = 'SO/';break;
			case 5: $prefix = 'SCH/';break;
			case 6: $prefix = 'GT/';break;
			case 7: $prefix = 'JW/';break;
			case 8: $prefix = 'EXP/';break;
			case 9: $prefix = 'PINV/';break;
			case 15: $prefix = 'RV/';break;
			case 16: $prefix = 'PV/';break;
			case 13: $prefix = 'CRN/';break;
			case 14: $prefix = 'DRN/';break;
			case 18: $prefix = 'GEXP/';break;
			case 17: $prefix = 'JV/';break;
			default : $prefix = 'TRANS/';break;
		}
		return $prefix.$this->shortYear.'/';
    }
    
    public function getPaymentModes($payment_mode=''){
        if(!empty($payment_mode)){
            $queryData['tableName'] = $this->paymentModeEffect;
            $queryData['where']['payment_mode'] = $payment_mode;
            return $this->row($queryData);
        }else{
            $queryData['tableName'] = $this->paymentModeEffect;
            return $this->rows($queryData);
        }
    }

	public function ledgerEffects($transMainData,$expenseData = array()){
		try{
			if(!isset($transMainData['cm_id'])):
				$transMainData['cm_id'] = $this->CMID;
			endif;
			if(!isset($transMainData['created_by'])):
				$transMainData['created_by'] = $this->loginID;
			endif;

			$this->deleteLedgerTrans($transMainData['id'],$transMainData['cm_id']);
			$this->deleteExpenseTrans($transMainData['id']);
			
			$partyData = $this->party->getParty($transMainData['opp_acc_id']);
			if(!empty($partyData)):
				$transLedgerData['currency'] = (!empty($partyData->currency))?$partyData->currency:"INR";
				$transLedgerData['inrrate'] = (!empty($partyData->inrrate) && $partyData->inrrate > 0)?$partyData->inrrate:1;
			endif;

			if(in_array($transMainData['entry_type'],[15,16])):
				$cord = getCrDrEff($transMainData['entry_type']);
				
				//Save Party Account Detail
				$transLedgerData = ['id'=>"",'entry_type'=>$transMainData['entry_type'],'trans_main_id'=>$transMainData['id'],'trans_date'=>$transMainData['trans_date'],'trans_number'=>$transMainData['trans_number'],'doc_date'=>$transMainData['doc_date'],'doc_no'=>$transMainData['doc_no'],'vou_acc_id'=>$transMainData['opp_acc_id'],'opp_acc_id'=>$transMainData['vou_acc_id'],'amount'=>$transMainData['net_amount'],'c_or_d'=>$cord['opp_type'],'remark'=>$transMainData['remark'],'trans_mode'=>$transMainData['trans_mode'],'cm_id'=>$transMainData['cm_id'],'created_by'=>$transMainData['created_by']];
				$this->storeTransLedger($transLedgerData);

				//Save BankCash Account Detail
				$transLedgerData['vou_acc_id'] = $transMainData['vou_acc_id'];
				$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
				$transLedgerData['c_or_d'] = $cord['vou_type'];
				$this->storeTransLedger($transLedgerData);
			endif;

			if(in_array($transMainData['entry_type'],[12,6,7,8,10,11,13,14,18])):
				if($transMainData['ledger_eff'] == 1):
					if(!empty($expenseData)):
						$expenseData['id'] = "";
						$expenseData['trans_main_id'] = $transMainData['id'];
						$this->store('trans_expense',$expenseData);
					endif;

					$cord = getCrDrEff($transMainData['entry_type']);
					//Save Party Account Detail
					$transLedgerData = ['id'=>'','entry_type'=>$transMainData['entry_type'],'trans_main_id'=>$transMainData['id'],'trans_date'=>$transMainData['trans_date'],'trans_number'=>$transMainData['trans_number'],'doc_date'=>$transMainData['doc_date'],'doc_no'=>$transMainData['doc_no'],'vou_acc_id'=>$transMainData['opp_acc_id'],'opp_acc_id'=>$transMainData['vou_acc_id'],'amount'=>$transMainData['net_amount'],'c_or_d'=>$cord['vou_type'],'remark'=>$transMainData['remark'],'cm_id'=>$transMainData['cm_id'],'created_by'=>$transMainData['created_by']];
					$this->storeTransLedger($transLedgerData);

					//Save Sale/Purc Account Detail
					if($transMainData['entry_type'] != 18):
						if(!isset($transMainData['sp_acc_id'])):
							$accType = getSystemCode($transMainData['entry_type'],true,$transMainData['gst_type']);
							if(!empty($accType)):
								$spAcc = $this->ledger->getLedgerOnSystemCode($accType);
								$transMainData['sp_acc_id'] = (!empty($spAcc))?$spAcc->id:0;
							else:
								$transMainData['sp_acc_id'] = 0;
							endif;
						endif;
						$transLedgerData['vou_acc_id'] = $transMainData['sp_acc_id'];
						$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
						$transLedgerData['amount'] = $transMainData['taxable_amount'];
						$transLedgerData['c_or_d'] = $cord['opp_type'];
						$this->storeTransLedger($transLedgerData);
					else:
						$gstExpenseTrans = $this->gstExpense->gstExpenseTransaction($transMainData['id']);
						foreach($gstExpenseTrans as $row):
							$transLedgerData['vou_acc_id'] = $row->item_id;
							$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
							$transLedgerData['amount'] = $row->taxable_amount;
							$transLedgerData['c_or_d'] = $cord['opp_type'];
							$this->storeTransLedger($transLedgerData);
						endforeach;
					endif;

					//Save Tax Account Detail
					if($transMainData['gst_type'] == 2):
						if($transMainData['igst_amount'] <> 0):
							$transLedgerData['vou_acc_id'] = $transMainData['igst_acc_id'];
							$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
							$transLedgerData['amount'] = $transMainData['igst_amount'];
							$transLedgerData['c_or_d'] = $cord['opp_type'];
							$this->storeTransLedger($transLedgerData);
						endif;
					else:
						if($transMainData['cgst_amount'] <> 0 && $transMainData['sgst_amount'] <> 0):
							$transLedgerData['vou_acc_id'] = $transMainData['cgst_acc_id'];
							$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
							$transLedgerData['amount'] = $transMainData['cgst_amount'];
							$transLedgerData['c_or_d'] = $cord['opp_type'];
							$this->storeTransLedger($transLedgerData);

							$transLedgerData['vou_acc_id'] = $transMainData['sgst_acc_id'];
							$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
							$transLedgerData['amount'] = $transMainData['sgst_amount'];
							$transLedgerData['c_or_d'] = $cord['opp_type'];
							$this->storeTransLedger($transLedgerData);
						endif;
					endif;


					if($transMainData['cess_amount'] <> 0):
						$transLedgerData['vou_acc_id'] = $transMainData['cess_acc_id'];
						$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
						$transLedgerData['amount'] = $transMainData['cess_amount'];
						$transLedgerData['c_or_d'] = $cord['opp_type'];
						$this->storeTransLedger($transLedgerData);
					endif;

					if($transMainData['cess_qty_amount'] <> 0):
						$transLedgerData['vou_acc_id'] = $transMainData['cess_qty_acc_id'];
						$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
						$transLedgerData['amount'] = $transMainData['cess_qty_amount'];
						$transLedgerData['c_or_d'] = $cord['opp_type'];
						$this->storeTransLedger($transLedgerData);
					endif;

					if($transMainData['tcs_amount'] <> 0):
						$transLedgerData['vou_acc_id'] = $transMainData['tcs_acc_id'];
						$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
						$transLedgerData['amount'] = $transMainData['tcs_amount'];
						$transLedgerData['c_or_d'] = $cord['opp_type'];
						$this->storeTransLedger($transLedgerData);
					endif;

					if($transMainData['tds_amount'] <> 0):
						$transLedgerData['vou_acc_id'] = $transMainData['tds_acc_id'];
						$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
						$transLedgerData['amount'] = $transMainData['tds_amount'];
						$transLedgerData['c_or_d'] = $cord['opp_type'];
						$this->storeTransLedger($transLedgerData);
					endif;

					//Save Expense Account Detail
					$expType = (in_array($transMainData['entry_type'],[12,14]))?1:2;
					$expenseMaster = $this->expenseMaster->getActiveExpenseList($expType);
					$expBFTAmt = $expBFTDeductionAmt = $expBFTAddAmt = 0;
					foreach($expenseMaster as $row):
						if(isset($expenseData[$row->map_code."_acc_id"]) && isset($expenseData[$row->map_code.'_amount'])):
							if($expenseData[$row->map_code.'_amount'] <> 0 && $row->map_code != "roff"): 
								$transLedgerData['vou_acc_id'] = $expenseData[$row->map_code."_acc_id"];
								$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
								$transLedgerData['amount'] = abs($expenseData[$row->map_code.'_amount']);
								$transLedgerData['c_or_d'] = (in_array($transMainData['entry_type'],[12,13]))?(($row->add_or_deduct == 1)?"DR":"CR"):(($row->add_or_deduct == 1)?"CR":"DR");
								$this->storeTransLedger($transLedgerData);

								if($row->position == 1): 
									$expBFTAmt += floatval($expenseData[$row->map_code.'_amount']);
									if(floatval($expenseData[$row->map_code.'_amount']) > 0):
										$expBFTAddAmt += floatval($expenseData[$row->map_code.'_amount']);
									else:
										$expBFTDeductionAmt += floatval($expenseData[$row->map_code.'_amount']);
									endif;
								endif;
							endif;
						endif;
					endforeach;

					//remove old expense amount and gst amount
					$setData = array();
					$setData['tableName'] = $this->transChild;
					$setData['where']['trans_main_id'] = $transLedgerData['trans_main_id'];
					$setData['update']['exp_taxable_amount'] = 0;
					$setData['update']['exp_gst_amount'] = 0;
					$this->setValue($setData);

					//Before Tax Expense Amount add to Items and calculate gst
					if($expBFTAmt <> 0):
						//Before Tax Expense Amount Add from Items and calculate max gst
						if(floatval($expBFTAddAmt) > 0):
							//Get Invoice Items
							$queryData = [];
							$queryData['tableName'] = $this->transChild;
							$queryData['select'] = "id,gst_per";
							$queryData['where']['trans_main_id'] = $transLedgerData['trans_main_id'];
							$queryData['whereFalse']['gst_per'] = "(SELECT MAX(gst_per) as gst_per FROM trans_child WHERE is_delete = 0 AND trans_main_id = ".$transLedgerData['trans_main_id'].")";
							$itemDetails = $this->rows($queryData);

							$itemCount = count($itemDetails);
							$expTaxableAmt = 0;
							if($itemCount > 0):
								$expTaxableAmt = round(($expBFTAddAmt / $itemCount),3);
								foreach($itemDetails as $row):
									$extGstAmt = 0;
									if(floatval($row->gst_per) > 0):
										$expGstAmt = round(( ($expTaxableAmt * $row->gst_per) / 100),3);
									endif;

									//update new values
									$setData = array();
									$setData['tableName'] = $this->transChild;
									$setData['where']['id'] = $row->id;
									$setData['update']['exp_taxable_amount'] = $expTaxableAmt;
									$setData['update']['exp_gst_amount'] = $expGstAmt;
									$this->setValue($setData);
								endforeach;
							endif;
						endif;

						//Before Tax Expense Amount Deduction from Items and calculate gst
						if(floatval($expBFTDeductionAmt) < 0):
							//Get Invoice Items
							$queryData = [];
							$queryData['tableName'] = $this->transChild;
							$queryData['select'] = "id,gst_per,taxable_amount";
							$queryData['where']['trans_main_id'] = $transLedgerData['trans_main_id'];
							$itemDetails = $this->rows($queryData);

							$itemCount = count($itemDetails);
							$expTaxableAmt = 0;$totalTaxableAmt = abs($transMainData['taxable_amount']);;
							if($itemCount > 0):
								$taxablePer = 0;
								foreach($itemDetails as $row):
									$taxablePer = round((($row->taxable_amount * 100) / $totalTaxableAmt),3);
									$expTaxableAmt = round((($expBFTDeductionAmt * $taxablePer) / 100),3);

									$extGstAmt = 0;
									if(floatval($row->gst_per) > 0):
										$expGstAmt = round((($expTaxableAmt * $row->gst_per) / 100),3);
									endif;

									//update new deduction values
									$setData = array();
									$setData['tableName'] = $this->transChild;
									$setData['where']['id'] = $row->id;
									$setData['set']['exp_taxable_amount'] = 'exp_taxable_amount, + '.$expTaxableAmt;
									$setData['set']['exp_gst_amount'] = 'exp_gst_amount, + '.$expGstAmt;
									$this->setValue($setData);
								endforeach;
							endif;
						endif;
					endif;

					//Save Round off Account Detail 
					if($transMainData['round_off_amount'] != 0): 
						$transLedgerData['vou_acc_id'] = $transMainData["round_off_acc_id"];
						$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
						$transLedgerData['amount'] = $transMainData['round_off_amount'];
						$transLedgerData['c_or_d'] = (in_array($transMainData['entry_type'],[12,13]))?(($transMainData['round_off_amount'] > 0)?"DR":"CR"):(($transMainData['round_off_amount'] > 0)?"CR":"DR");
						$this->storeTransLedger($transLedgerData);
					endif;
				endif;
			endif;

			return true;
		}catch(\Exception $e){
			return false;
        }
	}

	public function storeTransLedger($data){
		try{
			$data['p_or_m'] = ($data['c_or_d'] == "DR")?-1:1;
			$data['vou_name_s'] = getVoucherNameShort($data['entry_type']);
			$data['vou_name_l'] = getVoucherNameLong($data['entry_type']);
			$this->store("trans_ledger",$data);
			$this->updateAccountBalance($data['vou_acc_id'],( $data['p_or_m'] * $data['amount'] ));
			return true;
		}catch(\Exception $e){
			return false;
        }			
	}

	public function updateAccountBalance($acc_id,$amount){
		try{
			$setData = Array();
			$setData['tableName'] = "party_master";
			$setData['where']['id'] = $acc_id;
			$setData['set']['cl_balance'] = 'cl_balance, + '.$amount;
			$this->setValue($setData);
			return true;
		}catch(\Exception $e){
			return false;
        }
	}

	public function deleteLedgerTrans($trans_main_id,$cm_id = 0){
		try{
			$queryData = array();
			$queryData['tableName'] = "trans_ledger";
			$queryData['where']['trans_main_id'] = $trans_main_id;
			if(!empty($cm_id)){$transLedgerData = $this->rows($queryData,$cm_id);}
			else{$transLedgerData = $this->rows($queryData);}
            
			if(!empty($transLedgerData)):
				foreach($transLedgerData as $row):
					$amount = $row->amount * $row->p_or_m * -1;
					$this->updateAccountBalance($row->vou_acc_id,$amount);				
				endforeach;
				$this->remove("trans_ledger",['trans_main_id'=>$trans_main_id]);
			endif;
			return true;
		}catch(\Exception $e){
			return false;
        }
	}

	public function deleteExpenseTrans($trans_main_id){
		try{
			$this->trash('trans_expense',['trans_main_id'=>$trans_main_id]);
			return true;
		}catch(\Exception $e){
			return false;
		}
	}
}
?>