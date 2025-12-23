<?php
class SalesInvoiceModel extends MasterModel{
    private $salesMaster = "sales_invoice";
    private $salesTrans = "sales_invoice_trans";
    private $itemMaster = "item_master";
    private $stockTrans = "stock_transaction";
    private $transMain = "trans_main";
    private $transChild = "trans_child";

    public function getDTRows($data){
        $data['tableName'] = $this->transMain; 
        $data['select'] = "trans_main.*";
        //$data['select'] = "trans_main.*,party_master.party_name as ledger_name";
        //$data['join']['party_master'] = "party_master.id = trans_main.vou_acc_id";
        $data['customWhere'][] = 'trans_main.entry_type IN ('.$data['entry_type'].')';
        if(!empty($data['from_date']) AND !empty($data['to_date'])){$data['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";}
        else{
            $data['where']['trans_main.trans_date >='] = $this->startYearDate;
            $data['where']['trans_main.trans_date <='] = $this->endYearDate;
        }
        
        if($data['status'] == 0){ 
            $data['where']['trans_main.audit_status'] = 0; 
            $data['where']['trans_main.trans_status !='] = 3; 
        }elseif($data['status'] == 1){ 
            $data['where']['trans_main.audit_status'] = 1; 
            $data['where']['trans_main.trans_status !='] = 3; 
        }else{
            $data['where']['trans_main.trans_status'] = 3; 
        }
        
        if(!empty($data['disc_amt'])){
            if($data['disc_amt'] == 1){ $data['where']['trans_main.disc_amount != '] = 0; }
            else{ $data['where']['trans_main.disc_amount'] = 0; }
        }
        $data['order_by']['trans_main.trans_date'] = "DESC";
        //$data['order_by']['trans_main.trans_no'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";

        $data['searchCol'][] = "trans_main.trans_no";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "trans_main.party_name";
        $data['searchCol'][] = "trans_main.net_amount";

        $columns =array('','','trans_main.trans_no','trans_main.trans_date','trans_main.party_name','','trans_main.net_amount');

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getDTRows1($data){
        $data['tableName'] = $this->transChild;
        $data['select'] = "trans_child.id,trans_child.trans_main_id,trans_child.item_name,trans_child.trans_status,trans_child.qty,trans_child.net_amount,trans_child.item_remark,trans_main.trans_prefix,trans_main.trans_no,trans_main.trans_date,trans_main.party_id,trans_main.party_name,trans_main.net_amount as inv_amount,trans_main.remark";

        $data['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['where_in']['trans_child.entry_type'] = [6,7,8];
        $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";

         $data['searchCol'][] = "CONCAT(trans_main.trans_prefix,trans_main.trans_no)";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "trans_main.party_name";
        $data['searchCol'][] = "trans_child.item_name";
        $data['searchCol'][] = "trans_child.net_amount";
        $data['searchCol'][] = "trans_main.net_amount";

        $columns =array('','','trans_main.trans_no','trans_main.trans_date','party_master.party_name','trans_child.item_name','trans_child.net_amount','trans_main.net_amount');

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function salesTransRow($id){
        $queryData['tableName'] = $this->transChild;
        $queryData['where']['id'] = $id;
        return $this->row($queryData);
    }

    /**Updated By Mansee @ 22-03-2022
     * Payment Voucher Effect
     */
    
    public function save($masterData,$itemData,$expenseData,$ledgerData,$redirect_url="salesInvoice"){
        try{
            $this->db->trans_begin();
            $id = $masterData['id'];	
            $masterData['cm_id'] = (isset($masterData['cm_id'])) ? $masterData['cm_id'] : $this->CMID ;
            if(empty($id)):
                /*$masterData['inv_no'] = $this->transModel->nextTransNo(6,$masterData['memo_type']);
                $masterData['inv_prefix'] = "WGM/";
                if($masterData['memo_type'] == "DEBIT"){$masterData['inv_prefix'] = "WG/";}*/
                
                $saveInvoice = $this->store($this->transMain,$masterData);
                $masterData['id'] = $saveInvoice['insert_id'];

                $result = ['status'=>1,'message'=>'Sales Invoice saved successfully.','url'=>base_url($redirect_url),'insert_id'=>$masterData['id']];
            else:
                $this->store($this->transMain,$masterData);
                $salesId = $id;	
                $masterData['id'] = $id;
                
                /**Payment Voucher Effect */
                $voucherData=$this->paymentVoucher->getReceiveVoucherByRefId($id);
                if(!empty($voucherData->id)){
                    $voucherEff=$this->paymentVoucher->delete($voucherData->id);
                    if($voucherEff == false):
                        $this->db->trans_rollback();
                        return ['status'=>2,'message'=>"somthing is wrong. Error : "];
                    endif;
                }
                
                $transDataResult = $this->salesTransactions($id);
                foreach($transDataResult as $row):
                    if($row->stock_eff == 1):
                        /** Remove Stock Transaction **/
                        $this->remove($this->stockTrans,['ref_id'=>$masterData['id'],'trans_ref_id'=>$row->id,'trans_type'=>2,'ref_type'=>5]);
                    endif;
					
                    if(!in_array($row->id,$itemData['id'])):
                        $this->trash($this->transChild,['id'=>$row->id]);
                    endif;
                endforeach;

                $result = ['status'=>1,'message'=>'Sales Invoice updated successfully.','url'=>base_url($redirect_url)];
            endif;
			
			$salesTransData1 = convertItemDataArray($itemData);
			
            //foreach($itemData['item_id'] as $key=>$value):
            foreach($salesTransData1 as $row):                
                $batch_qty = array(); $batch_no = array(); $location_id = array();
                $batch_qty[] = $row['qty'];
                $batch_no[] = "General Batch";
                $location_id[] = (isset($row['location_id']))?$row['location_id']:$this->RTD_STORE->id;
				$row['stock_eff'] = 1;
                
                //$iq['tableName'] = 'item_master';$iq['where']['id'] = $id;$itmDetail = $this->row($iq);
				$salesTransData = Array();
				$salesTransData = $row;
				$salesTransData['trans_main_id'] = $masterData['id'];
				$salesTransData['entry_type'] = $masterData['entry_type'];
				$salesTransData['currency'] = $masterData['currency'];
				$salesTransData['inrrate'] = $masterData['inrrate'];
				$salesTransData['location_id'] = implode(",",$location_id);
				$salesTransData['batch_no'] = implode(",",$batch_no);
				$salesTransData['batch_qty'] = implode(",",$batch_qty);
                $salesTransData['cm_id'] = $masterData['cm_id'];
                //$salesTransData['incentive'] = $itmDetail->incentive;

                $saveTrans = $this->store($this->transChild,$salesTransData);
                $refID = (empty($row['id']))?$saveTrans['insert_id']:$row['id'];
                
                if(!empty($row['ref_id'])):
                    $setData = Array();
                    $setData['tableName'] = $this->transChild;
                    $setData['where']['id'] = $row['ref_id'];
                    $setData['set']['dispatch_qty'] = 'dispatch_qty, + '.$row['qty'];
                    $this->setValue($setData);

                    $queryData = array();
                    $queryData['tableName'] = $this->transChild;
                    $queryData['where']['id'] = $row['ref_id'];
                    $transRow = $this->row($queryData);

                    if($transRow->qty <= $transRow->dispatch_qty):
                        $this->store($this->transChild,['id'=>$row['ref_id'],'trans_status'=>1]);
                    endif;
                endif;

                // UPDATE STOCK TRANSACTION DATA
                if($row['stock_eff'] == 1):
					$salesTransData['ref_type'] = 5;
					$salesTransData['ref_id'] = $salesTransData['trans_main_id'];
					$salesTransData['trans_ref_id'] = $refID;
					$salesTransData['ref_no'] = $masterData['trans_number'];
					$salesTransData['ref_date'] = $masterData['trans_date'];
					$salesTransData['cm_id'] = $masterData['cm_id'];
					
					$this->stockEffect($salesTransData);	// Stock Effect #PARAM => (Data Array, Transaction Type)
                endif;            
            endforeach;
			
            if(!empty($masterData['ref_id'])):
                $refIds = explode(",",$masterData['ref_id']);
                foreach($refIds as $key=>$value):
                    if($masterData['from_entry_type'] == 5):
                        $pendingItems = $this->challan->checkChallanPendingStatus($value);
                    elseif($masterData['from_entry_type'] == 4):
                        $pendingItems = $this->salesOrder->checkSalesOrderPendingStatus($value);
                    endif;
                    if(empty($pendingItems)):
                        $this->store($this->transMain,['id'=>$value,'trans_status'=>1]);
                    endif;
                endforeach;
            endif;
			
            /** Ledger Effect */
            $ledgerEff = $this->transModel->ledgerEffects($masterData,$expenseData);
            
            if($ledgerEff == false):
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : "];
            endif;
            
            // IF CASH MEMO THEN AUTO VOUCHER ENTRY
            if($masterData['memo_type'] == 'CASH'):
                /** Payment Voucher Effect */
                if(!empty($ledgerData))
                {
                    $ledgerData['ref_id']=$masterData['id'];
                    $voucherEff = $this->paymentVoucher->save($ledgerData);
                    if($voucherEff == false):
                        $this->db->trans_rollback();
                        return ['status'=>2,'message'=>"somthing is wrong. Error : "];
                    endif;
                }
            endif;

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getInvoice($id){ 
        $queryData = array();   
        $queryData['tableName'] = $this->transMain;
        $queryData['where']['id'] = $id;
        $invoiceData = $this->row($queryData);
        $invoiceData->itemData = $this->salesTransactions($id);

        $queryData = array();
        $queryData['tableName'] = "trans_expense";
        $queryData['where']['trans_main_id'] = $id;
        $invoiceData->expenseData = $this->row($queryData);
        return $invoiceData;
    }

    public function salesTransactions($id,$limit=""){
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = 'trans_child.*,item_master.price as mrp';
        if($this->CMID == 1):
            $queryData['select'] = 'trans_child.*,item_master.price1 as mrp';
        else:
            $queryData['select'] = 'trans_child.*,item_master.price2 as mrp';
        endif;
        $queryData['leftJoin']['item_master'] = "item_master.id = trans_child.item_id";
        $queryData['where']['trans_main_id'] = $id;
        
        if(!empty($limit)){$queryData['limit'] = $limit;}
        return $this->rows($queryData);
    }

    /**Updated By Mansee @ 22-03-2022
     * Payment Voucher Effect
     */
    public function deleteInv($id){
        try{
            $this->db->trans_begin();
            $transData = $this->getInvoice($id);
            foreach($transData->itemData as $row):
                if(!empty($row->ref_id)):
                    $setData = Array();
                    $setData['tableName'] = $this->transChild;
                    $setData['where']['id'] = $row->ref_id;
                    $setData['set']['dispatch_qty'] = 'dispatch_qty, - '.$row->qty;
                    $this->setValue($setData);

                    $queryData = array();
                    $queryData['tableName'] = $this->transChild;
                    $queryData['where']['id'] = $row->ref_id;
                    $transRow = $this->row($queryData);

                    if($transRow->qty != $transRow->dispatch_qty):
                        $this->store($this->transChild,['id'=>$row->ref_id,'trans_status'=>0]);
                        $this->store($this->transMain,['id'=>$transRow->trans_main_id,'trans_status'=>0]);
                    endif;
                endif;

                if($row->stock_eff == 1):
                    /** Remove Stock Transaction **/
                    $this->remove($this->stockTrans,['ref_id'=>$id,'trans_type'=>2,'ref_type'=>5]);
                endif;
                $this->trash($this->transChild,['id'=>$row->id]);
            endforeach;

            if(!empty($transData->ref_id)):
                $refIds = explode(",",$transData->ref_id);
                foreach($refIds as $key=>$value):
                    if($transData->from_entry_type == 5):
                        $pendingItems = $this->challan->checkChallanPendingStatus($value);
                    elseif($transData->from_entry_type == 4):
                        $pendingItems = $this->salesOrder->checkSalesOrderPendingStatus($value);
                    endif;
                    if(empty($pendingItems)):
                        $this->store($this->transMain,['id'=>$value,'trans_status'=>0]);
                    endif;
                endforeach;
            endif;
            $result = $this->trash($this->transMain,['id'=>$id],'Sales Invoice');

            $deleteLedgerTrans = $this->transModel->deleteLedgerTrans($id);
            if($deleteLedgerTrans == false):
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : "];
            endif;
            $deleteExpenseTrans = $this->transModel->deleteExpenseTrans($id);
            if($deleteExpenseTrans == false):
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : "];
            endif;

            /**Payment Voucher Effect */
            $voucherData=$this->paymentVoucher->getReceiveVoucherByRefId($id);
            if(!empty($voucherData->id)){
                $voucherEff=$this->paymentVoucher->delete($voucherData->id);
                if($voucherEff == false):
                    $this->db->trans_rollback();
                    return ['status'=>2,'message'=>"somthing is wrong. Error : "];
                endif;
            }
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function batchWiseItemStock($data){
		
        $i=1;$tbody="";
		$locationData = $this->store->getStoreLocationList();
		if(!empty($locationData)){
			foreach($locationData as $lData){                
				
				foreach($lData['location'] as $batch):
                    $queryData = array();
					$queryData['tableName'] = "stock_transaction";
					$queryData['select'] = "SUM(qty) as qty,batch_no";
					$queryData['where']['item_id'] = $data['item_id'];
					$queryData['where']['location_id'] = $batch->id;
					$queryData['order_by']['id'] = "asc";
					$queryData['group_by'][] = "batch_no";
					$result = $this->rows($queryData);
					if(!empty($result)){
                        $batch_no = array();
						foreach($result as $row){
                            $batch_no = (!empty($data['trans_id']))?explode(",",$data['batch_no']):$data['batch_no'];
                            $batch_qty = (!empty($data['trans_id']))?explode(",",$data['batch_qty']):$data['batch_qty'];
                            if($row->qty > 0 || !empty($batch_no) && in_array($row->batch_no,$batch_no)):
                                if(!empty($batch_no) && in_array($row->batch_no,$batch_no)):
                                    $arrayKey = array_search($row->batch_no,$batch_no);
                                    $qty = $batch_qty[$arrayKey];
                                    $cl_stock = (!empty($data['trans_id']))?floatVal($row->qty + $batch_qty[$arrayKey]):floatVal($row->qty);
                                else:
                                    $qty = "0";
                                    $cl_stock = floatVal($row->qty);
                                endif;                                
                                
                                $tbody .= '<tr>';
                                    $tbody .= '<td class="text-center">'.$i.'</td>';
                                    $tbody .= '<td>['.$lData['store_name'].'] '.$batch->location.'</td>';
                                    $tbody .= '<td>'.$row->batch_no.'</td>';
                                    $tbody .= '<td>'.floatVal($row->qty).'</td>';
                                    $tbody .= '<td>
                                        <input type="number" name="batch_quantity[]" class="form-control batchQty" data-rowid="'.$i.'" data-cl_stock="'.$cl_stock.'" min="0" value="'.$qty.'" />
                                        <input type="hidden" name="batch_number[]" id="batch_number'.$i.'" value="'.$row->batch_no.'" />
                                        <input type="hidden" name="location[]" id="location'.$i.'" value="'.$batch->id.'" />
                                        <div class="error batch_qty'.$i.'"></div>
                                    </td>';
                                $tbody .= '</tr>';
                                $i++;
                            endif;
						}
					}
				endforeach;
			}
		}else{
            $tbody = '<tr><td class="text-center" colspan="5">No Data Found.</td></tr>';
        }
        return ['status'=>1,'batchData'=>$tbody];
    }

    public function getItemList($id){        
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.item_name,trans_child.hsn_code,trans_child.igst_per,trans_child.qty,trans_child.unit_name,trans_child.price,trans_child.amount";
        $queryData['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $queryData['where']['trans_main.id'] = $id;
        //print_r($queryData);exit;
        $resultData = $this->rows($queryData);
        
        $html="";
        if(!empty($resultData)):
            $i=1;
            foreach($resultData as $row):              
                $html .= '<tr>
                            <td class="text-center">'.$i.'</td>
                            <td class="text-center">'.$row->item_name.'</td>
                            <td class="text-center">'.$row->hsn_code.'</td>
                            <td class="text-center">'.$row->igst_per.'</td>
                            <td class="text-center">'.$row->qty.'</td>
                            <td class="text-center">'.$row->unit_name.'</td>
                            <td class="text-center">'.$row->price.'</td>
                            <td class="text-center">'.$row->amount.'</td>
                          </tr>';
                $i++;
            endforeach;
        else:
            $html = '<tr><td class="text-center" colspan="5">No Data Found</td></tr>';
        endif;
        return ['status'=>1,'htmlData'=>$html,'result'=>$resultData];
    }

    public function getSalesInvoiceList($party_id){
        $data['tableName'] = $this->transMain;
        $data['where']['party_id'] = $party_id;
        $data['where_in']['entry_type'] = [6,7,8];
        return $this->rows($data);      
    }
	
    // Created By Meghavi 09/07/2022
    public function auditStatus($data) {
        $this->store($this->transMain, ['id'=> $data['id'], 'audit_status' => $data['val']]);
        return ['status' => 1, 'message' => 'Sales Invoice ' . $data['msg'] . ' successfully.'];
    }
	/*  Create By : Avruti @29-11-2021 4:00 PM
    update by : 
    note : 
*/
    //---------------- API Code Start ------//

    public function getCount($type=0){
		$data['tableName'] = $this->transMain;
		$data['where']['trans_main.trans_date >='] = $this->startYearDate;
        $data['where']['trans_main.trans_date <='] = $this->endYearDate;
        return $this->numRows($data);
    }

    public function getSalesInvoiceList_api($postData){
        $queryData = array();
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = 'trans_main.id,DATE_FORMAT(trans_main.trans_date, "%d %M %Y") as trans_date,trans_main.trans_number, trans_main.party_name, trans_main.net_amount';
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$this->startYearDate."' AND '".$this->endYearDate."'";
        $queryData['where_in']['trans_main.entry_type'] = [6,7,8];
        if(!in_array($this->userRole,[-1,1])){$queryData['where']['trans_main.created_by'] = $this->loginId;}
        $queryData['where']['trans_main.trans_date >='] = $this->startYearDate;
        $queryData['where']['trans_main.trans_date <='] = $this->endYearDate;
        $queryData['order_by']['trans_main.trans_date'] = 'DESC';
        $queryData['order_by']['trans_main.id'] = 'DESC';
        
        if(!empty($postData['search'])):
            $queryData['like_or']['trans_main.trans_number'] = $postData['search'];
            $queryData['like_or']['trans_main.party_name'] = $postData['search'];
            $queryData['like_or']['trans_main.trans_date'] = $postData['search'];
        endif;

        $queryData['length'] = $postData['limit'];
		$queryData['start'] = $postData['off_set'];
        $result = $this->rows($queryData);
        //print_r($this->printQuery());
        return $result;
    }

    //------ API Code End -------//
}
?>