<?php
class DeliveryChallanModel extends MasterModel{
    private $deliveryChallan = "delivery_challan";
    private $deliveryTrans = "delivery_transaction";
    private $productMaster = "item_master";
    private $itemMaster = "item_master";
    private $stockTrans = "stock_transaction";
    private $orderMaster = "sales_order";
    private $orderTrans = "sales_order_trans";
    private $transMain = "trans_main";
    private $transChild = "trans_child";

    public function getDTRows($data){
        $data['tableName'] = $this->transChild;
        $data['select'] = "trans_child.id,trans_child.trans_main_id,trans_child.item_name,trans_child.trans_status,trans_child.qty,trans_child.dispatch_qty, trans_child.cod_date,trans_child.item_remark,trans_main.trans_prefix,trans_main.trans_no,trans_main.trans_date,trans_main.party_id,trans_main.party_name,trans_main.remark";

        $data['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['where']['trans_child.entry_type'] = 5;
        if($data['status'] == 1) { 
            $data['where']['trans_child.trans_status'] = 1; 
            $data['where']['trans_main.trans_date >='] = $this->startYearDate;
            $data['where']['trans_main.trans_date <='] = $this->endYearDate;
        } 
        else { $data['where']['trans_child.trans_status != '] = 1; }
        $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";
		$data['group_by'][]='trans_child.trans_main_id';

		$data['searchCol'][] = "CONCAT(trans_main.trans_prefix,trans_main.trans_no)";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "trans_main.party_name";
       
        $columns =array('','','trans_main.trans_no','trans_main.trans_date','trans_main.party_name');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}

		return $this->pagingRows($data);
    }

    public function save($masterData, $itemData){
        try{
            $this->db->trans_begin();
        $dc_id = $masterData['id'];
		if(empty($dc_id)):
			/** Insert Delivery Challan Master Data **/
			$dcSaved = $this->store($this->transMain,$masterData);
			$dc_id = $dcSaved['insert_id'];		

			$result = ['status'=>1,'message'=>'Delivery Challan saved successfully.','url'=>base_url("deliveryChallan")];
		else:
			$queryData['tableName'] = $this->transChild;
            $queryData['where']['trans_main_id'] = $dc_id;
            $queryData['where']['entry_type'] = 5;
            $challanData = $this->rows($queryData);
            foreach($challanData as $row):

                if(!empty($row->grn_data)):
                    $grnData = json_decode($row->grn_data);
                    foreach($grnData as $grnRow):
                        $grnRow = json_decode($grnRow);
                        $setData = Array();
                        $setData['tableName'] = "grn_transaction";
                        $setData['where']['id'] = $grnRow->grn_trans_id;
                        $setData['set']['remaining_qty'] = 'remaining_qty, + '.$grnRow->grn_qty;
                        $this->setValue($setData);
                    endforeach;
                endif;

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
                    endif;
                endif;

                if($row->stock_eff == 1):
                    /** Update Item Stock **/
                    $setData = Array();
                    $setData['tableName'] = $this->itemMaster;
                    $setData['where']['id'] = $row->item_id;
                    $setData['set']['qty'] = 'qty, + '.$row->qty;
                    $setData['set']['packing_qty'] = 'packing_qty, + '.$row->qty;
                    $qryresult = $this->setValue($setData);

                    /** Remove Stock Transaction **/
                    $this->remove($this->stockTrans,['ref_id'=>$row->id,'trans_type'=>2,'ref_type'=>4]);
                endif;

                if(!in_array($row->id,$itemData['id'])):
                    $this->trash($this->transChild,['id'=>$row->id]);
                endif;

                
            endforeach;
			
			/** Update Delivery Challan Master Data **/
			$dcSaved = $this->store($this->transMain,$masterData);			
			
			$result = ['status'=>1,'message'=>'Delivery Challan updated successfully.','url'=>base_url("deliveryChallan"),'field_error'=>0,'field_error_message'=>null];
		endif;

        foreach($itemData['item_id'] as $key=>$value):
            
            $batch_qty = array(); $batch_no = array(); $location_id = array();
            $batch_qty[] = $itemData['qty'][$key];
			$batch_no[] = "General Batch";
			$location_id[] = $this->RTD_STORE->id;

            $transData = [
                'id' => $itemData['id'][$key],
                'entry_type' => $masterData['entry_type'],
                'trans_main_id' => $dc_id,
                'from_entry_type' => $itemData['from_entry_type'][$key],
                'ref_id' => $itemData['ref_id'][$key],
                'stock_eff' => $itemData['stock_eff'][$key],
                'item_id' => $value,
                'item_name' => $itemData['item_name'][$key],
                'item_type' => $itemData['item_type'][$key],
                'item_code' => $itemData['item_code'][$key],
                'item_desc' => $itemData['item_desc'][$key],
                'hsn_code' => $itemData['hsn_code'][$key],
                'gst_per' => $itemData['gst_per'][$key],
                'price' => $itemData['price'][$key],
                'unit_id' => $itemData['unit_id'][$key],
                'unit_name' => $itemData['unit_name'][$key],
                'qty' => $itemData['qty'][$key],
                'location_id' => implode(",",$location_id),
                'batch_no' => implode(",",$batch_no),
                'batch_qty' => implode(",",$batch_qty),
                'item_remark' => $itemData['item_remark'][$key],
                'grn_data' => $itemData['grn_data'][$key],
                'created_by' => $masterData['created_by']
            ];

            /** Insert Record in Delivery Transaction **/
            $saveDCTrans = $this->store($this->transChild,$transData);
            $refID = (empty($itemData['id'][$key]))?$saveDCTrans['insert_id']:$itemData['id'][$key];

            if(!empty($itemData['grn_data'][$key])):
                $grnData = json_decode($itemData['grn_data'][$key]);
                foreach($grnData as $row):
                    $row = json_decode($row);
                    $setData = Array();
                    $setData['tableName'] = "grn_transaction";
                    $setData['where']['id'] = $row->grn_trans_id;
                    $setData['set']['remaining_qty'] = 'remaining_qty, - '.$row->grn_qty;
                    $this->setValue($setData);
                endforeach;
            endif;

            if(!empty($itemData['ref_id'][$key])):
                $setData = Array();
                $setData['tableName'] = $this->transChild;
                $setData['where']['id'] = $itemData['ref_id'][$key];
                $setData['set']['dispatch_qty'] = 'dispatch_qty, + '.$itemData['qty'][$key];
                $this->setValue($setData);

                $queryData = array();
                $queryData['tableName'] = $this->transChild;
                $queryData['where']['id'] = $itemData['ref_id'][$key];
                $transRow = $this->row($queryData);

                if($transRow->qty == $transRow->dispatch_qty):
                    $this->store($this->transChild,['id'=>$itemData['ref_id'][$key],'trans_status'=>1]);
                endif;
            endif;

            // print_r($transData);
            if( $itemData['stock_eff'][$key] == 1):
                /** Update Item Stock **/
                $setData = Array();
                $setData['tableName'] = $this->itemMaster;
                $setData['where']['id'] = $itemData['item_id'][$key];
                $setData['set']['qty'] = 'qty, - '.$itemData['qty'][$key];
                $setData['set']['packing_qty'] = 'packing_qty, - '.$itemData['qty'][$key];
                $this->setValue($setData);

                /*** UPDATE STOCK TRANSACTION DATA ***/
                foreach($batch_qty as $bk=>$bv):
                    $stockQueryData['id']="";
                    $stockQueryData['location_id']=$location_id[$bk];
                    if(!empty($batch_no[$bk])){$stockQueryData['batch_no'] = $batch_no[$bk];}
                    $stockQueryData['trans_type']=2;
                    $stockQueryData['item_id']=$itemData['item_id'][$key];
                    $stockQueryData['qty'] = "-".$bv;
                    $stockQueryData['ref_type']=4;
                    $stockQueryData['ref_id']=$refID;
                    $stockQueryData['ref_no']=getPrefixNumber($masterData['trans_prefix'],$masterData['trans_no']);
                    $stockQueryData['ref_date']=$masterData['trans_date'];
                    $stockQueryData['created_by']=$this->loginID;
                    $this->store($this->stockTrans,$stockQueryData);
                endforeach;
            endif;
        endforeach;
        
        if(!empty($masterData['ref_id'])):
            $refIds = explode(",",$masterData['ref_id']);
            foreach($refIds as $key=>$value):
                $pendingItems = $this->salesOrder->checkSalesOrderPendingStatus($value);
                if(empty($pendingItems)):
                    $this->store($this->transMain,['id'=>$value,'trans_status'=>1]);
                endif;
            endforeach;
        endif;

        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
			return $result;

		endif;
	}catch(\Exception $e){
		$this->db->trans_rollback();
		return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];

	}
        
    }

    public function challanTransRow($id){
        $queryData['tableName'] = $this->transChild;
        $queryData['where']['id'] = $id;
        return $this->row($queryData);
    }

    public function getChallan($id){
        $queryData['tableName'] = $this->transMain;
        $queryData['where']['id'] = $id;
        $queryData['where']['entry_type'] = 5;
        $challanData = $this->row($queryData);
        $challanData->itemData = $this->getChallanTransactions($id);
        return $challanData;
    }

    public function getChallanTransactions($id){
        $queryData['tableName'] = $this->transChild;
        $queryData['where']['trans_main_id'] = $id;
        $queryData['where']['entry_type'] = 5;
        return $this->rows($queryData);
    }

    public function deleteChallan($id){
        try{
            $this->db->trans_begin();
        $transData = $this->getChallan($id);
        foreach($transData->itemData as $row):
            if(!empty($row->grn_data)):
                $grnData = json_decode($row->grn_data);
                foreach($grnData as $grnRow):
                    $grnRow = json_decode($grnRow);
                    $setData = Array();
                    $setData['tableName'] = "grn_transaction";
                    $setData['where']['id'] = $grnRow->grn_trans_id;
                    $setData['set']['remaining_qty'] = 'remaining_qty, + '.$grnRow->grn_qty;
                    $this->setValue($setData);
                endforeach;
            endif;

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
                endif;
            endif;

            if($row->stock_eff == 1):
                /** Update Item Stock **/
                $setData = Array();
                $setData['tableName'] = $this->itemMaster;
                $setData['where']['id'] = $row->item_id;
                $setData['set']['qty'] = 'qty, + '.$row->qty;
                $setData['set']['packing_qty'] = 'packing_qty, + '.$row->qty;
                $qryresult = $this->setValue($setData);

                /** Remove Stock Transaction **/
                $this->remove($this->stockTrans,['ref_id'=>$row->id,'trans_type'=>2,'ref_type'=>4]);
            endif;
            $this->trash($this->transChild,['id'=>$row->id]);
        endforeach;

        if(!empty($transData->ref_id)):
            $refIds = explode(",",$transData->ref_id);
            foreach($refIds as $key=>$value):
                $pendingItems = $this->salesOrder->checkSalesOrderPendingStatus($value);
                if(empty($pendingItems)):
                    $this->store($this->transMain,['id'=>$value,'trans_status'=>0]);
                endif;
            endforeach;
        endif;
        //return $this->trash($this->transMain,['id'=>$id],'Delivery Challan');
        $result = $this->trash($this->transMain,['id'=>$id],'Delivery Challan');
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
			return $result;

		endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];

	}
    }

    public function getPartyChallans($id){
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "id,trans_prefix,trans_no,trans_date";
        $queryData['where']['trans_status'] = 0;
        $queryData['where']['entry_type'] = 5;
        $queryData['where']['party_id'] = $id;
        $resultData = $this->rows($queryData);
        
        $html="";
        if(!empty($resultData)):
            $i=1;
            foreach($resultData as $row):
                $html .= '<tr>
                            <td class="text-center">
                                <input type="checkbox" id="md_checkbox_'.$i.'" name="ref_id[]" class="filled-in chk-col-success" value="'.$row->id.'"  ><label for="md_checkbox_'.$i.'" class="mr-3"></label>
                            </td>
                            <td class="text-center">'.getPrefixNumber($row->trans_prefix,$row->trans_no).'</td>
                            <td class="text-center">'.formatDate($row->trans_date).'</td>
                          </tr>';
                $i++;
            endforeach;
        else:
            $html = '<tr><td class="text-center" colspan="3">No Data Found</td></tr>';
        endif;
        return ['status'=>1,'htmlData'=>$html,'result'=>$resultData];
    }

    public function getChallanItems($transIds){
        $data['tableName'] = $this->transChild;        
        $data['where']['entry_type'] = 5;
        $data['where_in']['trans_main_id'] = $transIds;
        return $this->rows($data);
    }

    public function checkChallanPendingStatus($id){
        $data['select'] = "COUNT(trans_status) as orderStatus";
        $data['where']['trans_main_id'] = $id;
        $data['where']['trans_status'] = 0;
        $data['where']['entry_type'] = 5;
        $data['tableName'] = $this->transChild;
        return $this->specificRow($data)->orderStatus;
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
                            /* $batch_no = (!empty($data['trans_id']))?explode(",",$data['batch_no']):$data['batch_no'];
                            $batch_qty = (!empty($data['trans_id']))?explode(",",$data['batch_qty']):$data['batch_qty']; */
                            $batch_no = (!is_array($data['batch_no']))?explode(",",$data['batch_no']):$data['batch_no'];
                            $batch_qty = (!is_array($data['batch_qty']))?explode(",",$data['batch_qty']):$data['batch_qty'];
                            $location_id = (!is_array($data['location_id']))?explode(",",$data['location_id']):$data['location_id'];
                            if($row->qty > 0 || !empty($batch_no) && in_array($row->batch_no,$batch_no)):
                                if(!empty($batch_no) && in_array($row->batch_no,$batch_no) && in_array($batch->id,$location_id)):
                                    //$arrayKey = array_search($batch->id,$location_id);
                                    $qty = 0;
                                    foreach($batch_no as $key=>$value):
                                        if($key == array_search($batch->id,$location_id)):
                                            $qty = $batch_qty[$key];
                                            break;
                                        endif;
                                    endforeach;
                                    //$qty = (isset($batch_qty[$arrayKey]))?$batch_qty[$arrayKey]:0;
                                    $cl_stock = (!empty($data['trans_id']))?floatVal($row->qty + $qty):floatVal($row->qty);
                                else:
                                    $qty = "0";
                                    $cl_stock = floatVal($row->qty);
                                endif;                                
                                
                                if($batch->store_type == $this->RTD_STORE->store_type){
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
                                }
                                $i++;
                            endif;
						}
					}
				endforeach;
			}
		}else{
            $tbody = '<tr><td class="text-center" colspan="5">No Data Found.</td></tr>';
        }
        $itmData = $this->item->getItem($data['item_id']);
        $inrrate = 1;
		if(!empty($itmData->party_id))
		{
			$partyData = $this->party->getParty($itmData->party_id);
			$cqData['tableName'] = 'currency';
			$cqData['where']['currency'] =  substr($partyData->currency, -3);
			$currencyData = $this->row($cqData);
			$inrrate = !empty($currencyData->inrrate) ? $currencyData->inrrate : 1;
		}
		
		//print_r($this->printQuery());exit;
        return ['status'=>1,'batchData'=>$tbody, 'inrrate'=>$inrrate];
    }
    
    public function getPackedItemQty($item_id){
        $queryData['tableName'] = "stock_transaction";
        $queryData['select'] = "SUM(qty) as qty";
        $queryData['where']['item_id'] = $item_id;
        $queryData['where']['location_id'] = $this->RTD_STORE->id;
        return $this->row($queryData);
    }

    public function getItemList($id){        
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.item_name,trans_child.hsn_code,trans_child.igst_per,trans_child.qty,trans_child.unit_name";
        $queryData['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $queryData['where']['trans_main.id'] = $id;
        $queryData['where']['trans_child.entry_type'] = 5;
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
                            
                          </tr>';
                $i++;
            endforeach;
        else:
            $html = '<tr><td class="text-center" colspan="5">No Data Found</td></tr>';
        endif;
        return ['status'=>1,'htmlData'=>$html,'result'=>$resultData];
    }
	
	/*  Create By : Avruti @29-11-2021 01:00 PM
    update by : 
    note : 
*/
    //---------------- API Code Start ------//

    public function getCount(){
        $data['tableName'] = $this->transChild;
        $data['where']['trans_child.entry_type'] = 5;
        return $this->numRows($data);
    }

    public function getDeliveryChallanList_api($limit, $start,$status){
        $data['tableName'] = $this->transChild;
        $data['select'] = "trans_child.id,trans_child.trans_main_id,trans_child.item_name,trans_child.trans_status,trans_child.qty,trans_child.dispatch_qty, trans_child.cod_date,trans_child.item_remark,trans_main.trans_prefix,trans_main.trans_no,trans_main.trans_date,trans_main.party_id,trans_main.party_name,trans_main.remark";

        $data['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['where']['trans_child.entry_type'] = 5;
        if($status == 1) { $data['where']['trans_child.trans_status'] = 1; } 
        else { $data['where']['trans_child.trans_status != '] = 1; }
        $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";
		$data['group_by'][]='trans_child.trans_main_id';

        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//

    public function getSalesOrderTransactions($id){  
        $data['tableName'] = $this->transChild;    
        $data['select'] = 'trans_child.*,item_master.rev_no as itemRevNO , item_master.drawing_no as itemDrgNo, item_master.part_no as partNo';
        $data['join']['item_master'] = 'item_master.id = trans_child.item_id';
        $data['where']['entry_type'] = 5;
        $data['where']['trans_main_id'] = $id;
        return $this->rows($data);
    }
    public function getPartyOrders($id){
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "id,trans_prefix,trans_no,trans_date,doc_no";
        $queryData['where']['trans_status'] = 0;
        $queryData['where']['entry_type'] = 5;
        $queryData['where']['party_id'] = $id;
        $resultData = $this->rows($queryData);
        
        $html="";
        if(!empty($resultData)):
            $i=1;
            foreach($resultData as $row):
                
                $partCode = array(); $qty = array();
                $partData = $this->getSalesOrderTransactions($row->id);
                foreach($partData as $part):
                    $partCode[] = $part->item_code; 
                    $qty[] = $part->qty; 
                endforeach;
                $part_code = implode(",<br> ",$partCode); $part_qty = implode(",<br> ",$qty);
                
                $html .= '<tr>
                            <td class="text-center">
                                <input type="checkbox" id="md_checkbox_'.$i.'" name="ref_id[]" class="filled-in chk-col-success" value="'.$row->id.'"  ><label for="md_checkbox_'.$i.'" class="mr-3"></label>
                            </td>
                            <td class="text-center">'.getPrefixNumber($row->trans_prefix,$row->trans_no).'</td>
                            <td class="text-center">'.formatDate($row->trans_date).'</td>
                            <td class="text-center">'.$row->doc_no.'</td>
                            <td class="text-center">'.$part_code.'</td>
                            <td class="text-center">'.$part_qty.'</td>
                          </tr>';
                $i++;
            endforeach;
        endif;
        return ['status'=>1,'htmlData'=>$html,'result'=>$resultData];
    }
}
?>