<?php
class StoreModel extends MasterModel{
    private $locationMaster = "location_master";
    private $stockTrans = "stock_transaction";
    
    public function getDTRows($data){
        $data['tableName'] = $this->locationMaster;
        $data['order_by']['store_type'] = 'DESC';
        $data['searchCol'][] = "store_name";
        $data['searchCol'][] = "location";
        $data['searchCol'][] = "remark";

		$columns =array('','','store_name','location','remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }
    
    public function getStoreTranfDTRows($data, $type = 0){
        $data['tableName'] = "stock_transfer";
        $data['select'] = "stock_transfer.*";
        
        if(!empty($data['from_date']) AND !empty($data['to_date'])){$data['customWhere'][] = "stock_transfer.doc_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'"; }
        
        $data['searchCol'][] = "stock_transfer.trans_no";
        $data['searchCol'][] = "stock_transfer.trans_date";
        $data['searchCol'][] = "stock_transfer.doc_no";
        $data['searchCol'][] = "stock_transfer.doc_date";

        $columns = array('', '', 'stock_transfer.trans_no', 'stock_transfer.trans_date', 'stock_transfer.doc_no', 'stock_transfer.doc_date');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        return $this->pagingRows($data);
    }

    public function getStoreNames(){
        $data['tableName'] = $this->locationMaster;
        $data['select'] = "DISTINCT(store_name)";
        return $this->rows($data);
    }
    
    public function getStoreLocationList($customQry=""){
        $locationList = array();
        $squery['tableName'] = $this->locationMaster;
        $squery['select'] = "DISTINCT(store_name)";
        if(!empty($customQry)){$squery['customWhere'][] = $customQry;}
        $storeList = $this->rows($squery);
        
        if(!empty($storeList))
        {
            $i=0;
            foreach($storeList as $store)
            {
                $locationList[$i]['store_name'] = $store->store_name;
                $data['tableName'] = $this->locationMaster;
                $data['where']['store_name'] = $store->store_name;
                $locationList[$i++]['location'] =  $this->rows($data);
            }
        }
        return $locationList;
    }
    
    public function getLocationList(){
        $data['tableName'] = 'location_master';
        return $this->rows($data);
    }

    public function getStoreLocation($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->locationMaster;
        return $this->row($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
        $data['store_name'] = trim($data['store_name']);
        $data['location'] = trim($data['location']);
        if($this->checkDuplicate($data['store_name'],$data['location'],$data['id']) > 0):
            $errorMessage['location'] = "Location is duplicate.";
            $result = ['status' => 0, 'message' => "Some fields are duplicate.",'field_error'=>1,'field_error_message'=>$errorMessage];
        else:
            $result = $this->store($this->locationMaster,$data,'Store');
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

    public function checkDuplicate($storename,$location,$id=""){
        $data['tableName'] = $this->locationMaster;
        $data['where']['store_name'] = $storename;
        $data['where']['location'] = $location;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
        $result = $this->trash($this->locationMaster,['id'=>$id],'Store');
        if ($this->db->trans_status() !== FALSE):
			$this->db->trans_commit();
			return $result;
		endif;
	}catch(\Exception $e){
		$this->db->trans_rollback();
	   return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
	}	
    }

    public function getItemWiseStock($data){	
		
		$itmData = $this->item->getItem($data['item_id']);
		
		$thead = '<tr><th colspan="6">Product : ('.$itmData->item_code.') '.$itmData->item_name.'</th></tr>
					<tr>
                        <th style="width:5%;">Action</th>
						<th>#</th>
						<th style="text-align:left !important;">Store</th>
						<th>Location</th>
						<th>Batch</th>
						<th>Current Stock</th>
					</tr>';
		$tbody = '';
        $i=1;
		$locationData = $this->store->getStoreLocationList();
		if(!empty($locationData))
		{
			foreach($locationData as $lData)
			{
				// $tbody = '<tr><th colspan="5">'.$lData['store_name'].'</th></tr>';
				foreach($lData['location'] as $batch):
					$queryData['tableName'] = "stock_transaction";
					$queryData['select'] = "SUM(qty) as qty,batch_no";
					$queryData['where']['item_id'] = $data['item_id'];
					$queryData['where']['location_id'] = $batch->id;
					$queryData['order_by']['id'] = "asc";
					$queryData['group_by'][] = "batch_no";
					$result = $this->rows($queryData);
					if(!empty($result))
					{
						foreach($result as $row)
						{
                            if(floatVal($row->qty) > 0):
                                $stfParam = "{'location_id':".$batch->id.",'item_id':".$data['item_id'].",'stock_qty':".floatVal($row->qty).",'batch_no':'".$row->batch_no."','modal_id' : 'modal-md', 'form_id' : 'stockTransfer', 'title' : 'Stock Transfer','fnSave' : 'saveStockTransfer'}";
                                $stfBtn = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Stock Transfer" flow="down" onclick="stockTransfer('.$stfParam.');"><i class="ti-control-shuffle" ></i></a>';
                                //if($batch->id == $this->PROD_STORE->id){$stfBtn = '';}
                                $actionBtn = getActionButton($stfBtn);
                                $tbody .= '<tr>';
                                    $tbody .= '<td class="text-center">'.$actionBtn.'</td>';
                                    $tbody .= '<td class="text-center">'.$i++.'</td>';
                                    $tbody .= '<td>'.$lData['store_name'].'</td>';
                                    $tbody .= '<td>'.$batch->location.'</td>';
                                    $tbody .= '<td>'.$row->batch_no.'</td>';
                                    $tbody .= '<td>'.floatVal($row->qty).'</td>';
                                $tbody .= '</tr>';
                            endif;
						}
					}
				endforeach;
			}
		}
        return ['status'=>1, 'thead'=>$thead, 'tbody'=>$tbody];
    }

    public function checkBatchWiseStock($data){
        $queryData['tableName'] = "stock_transaction";
        $queryData['select'] = "SUM(qty) as qty";
        $queryData['where']['item_id'] = $data['item_id'];
        $queryData['where']['location_id'] = $data['from_location_id'];
        $queryData['where']['batch_no'] = $data['batch_no'];        
        if(!empty($data['ref_type']))
            $queryData['where']['ref_type'] = $data['ref_type'];
        $queryData['where']['is_delete'] = 0;
        return $this->row($queryData);
    }

    public function getItemStock($item_id){
        $queryData['tableName'] = "stock_transaction";
        $queryData['select'] = "SUM(qty) as qty";
        $queryData['where']['item_id'] = $item_id;
        return $this->row($queryData);
    }
    
    public function getLocationWiseItemStock($item_id,$location_id){
        $queryData['tableName'] = "stock_transaction";
        $queryData['select'] = "SUM(qty) as qty,item_id";
        $queryData['where']['item_id'] = $item_id;
        $queryData['where']['location_id'] = $location_id;
        return $this->row($queryData);
    }

    public function saveStockTransfer($data){
        try{
            $this->db->trans_begin();
            $fromTrans = [
                'id' => "",
                "location_id" => $data['from_location_id'],
                "batch_no" => $data['batch_no'],
                "trans_type" => 2,
                "item_id" => $data['item_id'],
                "qty" => "-".$data['transfer_qty'],
                "ref_type" => "9",
                "ref_id" => $data['from_location_id'],
                "ref_date" => date("Y-m-d"),
                "created_by" => $data['created_by']
            ];
            $this->store('stock_transaction',$fromTrans);
    
            $toTrans = [
                'id' => "",
                "location_id" => $data['to_location_id'],
                "batch_no" => $data['batch_no'],
                "trans_type" => 1,
                "item_id" => $data['item_id'],
                "qty" => $data['transfer_qty'],
                "ref_type" => "9",
                "ref_id" => $data['from_location_id'],
                "ref_date" => date("Y-m-d"),
                "created_by" => $data['created_by']
            ];
            $this->store('stock_transaction',$toTrans);
    
            $result = ['status'=>1,'message'=>"Stock Transfer successfully."];
            if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	   return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
    	}	
    }
    
    // Get Sngle Item Stock From Stock Transaction
    public function getItemCurrentStock($item_id,$location_id=""){
        $queryData['tableName'] = "stock_transaction";
        $queryData['select'] = "SUM(qty) as qty";
        $queryData['where']['item_id'] = $item_id;
		if(!empty($location_id)){ $queryData['where']['location_id'] = $location_id; }
        return $this->row($queryData);
    }
	
	//CREATED BY JP @ 23-04-2022
    public function getStockRegister($postData,$location_id=""){
		if(empty($postData['to_date'])){$postData['to_date'] = $this->endYearDate;}
		
		$data['tableName'] = 'item_master';
		if($this->CMID == 1):
			$data['select'] = 'item_master.id, item_master.item_name, item_master.item_code, item_master.item_type, item_master.price1 as price, item_master.prc_price1 as prc_price';
		else:
			$data['select'] = 'item_master.id, item_master.item_name, item_master.item_code, item_master.item_type, item_master.price2 as price, item_master.prc_price2 as prc_price';
		endif;
		$data['select'] .= ',SUM(CASE WHEN stock_transaction.trans_type = 1 THEN stock_transaction.qty ELSE 0 END) AS rqty';
		$data['select'] .= ',SUM(CASE WHEN stock_transaction.trans_type = 2 THEN stock_transaction.qty ELSE 0 END) AS iqty';
		$data['select'] .= ',SUM(stock_transaction.qty) AS stockQty';
		$data['leftJoin']['stock_transaction'] = 'stock_transaction.item_id=item_master.id';
		$data['where_in']['item_master.item_type'] = $postData['item_type'];
		$data['where']['stock_transaction.cm_id'] = $this->CMID;
		$data['where']['stock_transaction.is_delete'] = 0;
		//$data['where']['stock_transaction.ref_date >='] = $this->startYearDate;
		$data['where']['stock_transaction.ref_date <='] = $postData['to_date'];
		if(!empty($postData['category_id'])){$data['where']['category_id'] = $postData['category_id'];}
		if(!empty($location_id)){$data['where']['stock_transaction.location_id'] = $postData['location_id'];}
		if(!empty($postData['hsn_code'])){$data['where']['item_master.hsn_code'] = $postData['hsn_code'];}
		
		$data['group_by'][] = 'item_master.id';
		
		if($postData['stock_type'] == 2){ $data['having'][] = 'SUM(stock_transaction.qty) > 0'; }

		$data['order_by']['item_master.item_name'] = 'ASC';
		$result = $this->rows($data);
		return $result;
	}
	
	public function getItemHistory($item_id,$location_id=""){
        $queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = 'stock_transaction.*,item_master.item_code,item_master.item_name,location_master.location';
        $queryData['leftJoin']['item_master'] = "item_master.id = stock_transaction.item_id";
        $queryData['leftJoin']['location_master'] = "location_master.id = stock_transaction.location_id";
        $queryData['where']['stock_transaction.item_id'] = $item_id;
        if(!empty($location_id)){ $queryData['where']['stock_transaction.location_id'] = $location_id; }
        $queryData['order_by']['stock_transaction.ref_date'] = 'ASC';
        $queryData['order_by']['stock_transaction.trans_type'] = 'ASC';
		$result = $this->rows($queryData);
		return $result;
    }

    // Created By Meghavi @01/02/2023
    public function getTransMainData($trans_main_id){
        $queryData['tableName'] = 'trans_main';
        $queryData['select'] = 'trans_main.*,party_master.party_name';
        $queryData['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
        $queryData['where']['trans_main.id'] = $trans_main_id;
        $result = $this->row($queryData);
		return $result;
    }
	
	public function saveStockTransferMultiple($masterData, $itemData, $redirect_url = "stockTransfer")
    {
        try {
            $this->db->trans_begin();

            $result = $this->store("stock_transfer", $masterData);
            $id = !empty($masterData['id']) ? $masterData['id'] : $result['insert_id'];
            if (!empty($masterData['id'])) {
                $transDataResult = $this->getStockTransferLog($masterData['id'])->itemData;
                foreach ($transDataResult as $row) :
                    if (!in_array($row->id, $itemData['id'])) :
                        $this->remove($this->stockTrans, ['trans_ref_id' => $row->id, 'ref_id' => $masterData['id'], 'ref_type' => 9]);
                        $this->remove($this->stockTrans, ['id' => $row->id]);
                    endif;
                endforeach;
            }
            foreach ($itemData['item_id'] as $key => $value) {
                if (empty($itemData['id'][$key])) {
                    $fromTrans = [
                        'id' => "",
                        "location_id" => $masterData['from_location_id'],
                        "trans_type" => 2,
                        "item_id" => $value,
                        "qty" => "-" . $itemData['qty'][$key],
                        "ref_type" => "9",
                        "ref_id" => $id,
                        "ref_date" => $masterData['trans_date'],
                        "created_by" => $masterData['created_by']
                    ];
                    $quryRsult = $this->store('stock_transaction', $fromTrans);

                    $toTrans = [
                        'id' => "",
                        "location_id" => $masterData['to_location_id'],
                        "trans_type" => 1,
                        "item_id" => $value,
                        "qty" => $itemData['qty'][$key],
                        "ref_type" => "9",
                        "ref_id" => $id,
                        "trans_ref_id" => $quryRsult['insert_id'],
                        "ref_date" => $masterData['trans_date'],
                        "created_by" => $masterData['created_by']
                    ];
                    $this->store('stock_transaction', $toTrans);
                } else {
                    $this->edit('stock_transaction',['id'=> $itemData['id'][$key],'ref_type'=>9],['ref_date' => $masterData['trans_date']]); 
                    $this->edit('stock_transaction',['trans_ref_id'=> $itemData['id'][$key],'ref_type'=>9],['ref_date' => $masterData['trans_date']]); 
                }
            }


            $result = ['status' => 1, 'message' => "Stock Transfer successfully.", 'url' => base_url($redirect_url)];
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(), 'field_error' => 0, 'field_error_message' => null];
        }
    }

    public function getNextTransNo()
    {
        $data['select'] = "MAX(trans_no) as trans_no";
        $data['tableName'] = "stock_transfer";
        $trans_no = $this->specificRow($data)->trans_no;
        $nextTransNo = (!empty($trans_no)) ? ($trans_no + 1) : 1;
        return $nextTransNo;
    }

    public function getStockTransferLog($id)
    {
        $queryData['tableName'] = "stock_transfer";
        $queryData['select'] = 'stock_transfer.*';
        $queryData['where']['stock_transfer.id'] = $id;
        $result = $this->row($queryData);

        $stockQuery['tableName'] = "stock_transaction";
        $stockQuery['select'] = 'stock_transaction.*,item_master.item_name';
        $stockQuery['leftJoin']['item_master'] = 'stock_transaction.item_id=item_master.id';
        $stockQuery['where']['stock_transaction.ref_id'] = $id;
        $stockQuery['where']['stock_transaction.ref_type'] = 9;
        $stockQuery['where']['stock_transaction.trans_type'] = 2;
        $result->itemData = $this->rows($stockQuery);
        return $result;
    }

    public function deleteStockTransfer($id)
    {
        $this->remove("stock_transaction", ['ref_id' => $id, 'ref_type' => 9]);
        return $this->trash('stock_transfer', ['id' => $id]);
    }
	
	/*  Create By : Avruti @27-11-2021 2:00 PM
        update by : 
        note : 
    */
    //---------------- API Code Start ------//

    public function getCount(){
        $data['tableName'] = $this->locationMaster;
        return $this->numRows($data);
    }

    public function getStoreLocationList_api($limit, $start){
        $data['tableName'] = $this->locationMaster;
        $data['order_by']['store_type'] = 'DESC';
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>